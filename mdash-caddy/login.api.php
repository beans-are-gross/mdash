<?php
ini_set("display_errors", 1);
error_reporting(E_ALL);

//get encryption data
$config = json_decode(file_get_contents("/mdash/config.json"), true);
$encryptionInfo = $config["encryption"];
$dbInfo = $config["dbData"];

//connect to the database
$dbHost = $dbInfo["dbHost"];
$dbUser = $dbInfo["dbUser"];
$dbPass = openssl_decrypt(
    $dbInfo["dbPass"],
    $encryptionInfo["cipher"],
    $encryptionInfo["key"],
    $encryptionInfo["options"],
    $encryptionInfo["iv"]
);
$dbDatabase = $dbInfo["dbDatabase"];

$dbConn = mysqli_connect($dbHost, $dbUser, $dbPass, $dbDatabase);

if (!$dbConn) {
    die("Failed to connect to the database: " . mysqli_connect_error());
}

//get the post data from javascript fetch
$payload = file_get_contents("php://input");
$data = json_decode($payload, true);

//check if all required data was provided
if (isset($data["username"]) && isset($data["password"])) {
    header("Content-Type: application/json");

    $username = $data["username"];
    $password = $data["password"];

    $encryptedUsername = openssl_encrypt(
        $username,
        $encryptionInfo["cipher"],
        $encryptionInfo["key"],
        $encryptionInfo["options"],
        $encryptionInfo["iv"]
    );
    $password = $data["password"];

    $sql = "SELECT id, password FROM accounts WHERE username = ?;";
    $stmt = mysqli_stmt_init($dbConn);
    mysqli_stmt_prepare($stmt, $sql);
    mysqli_stmt_bind_param($stmt, "s", $encryptedUsername);
    $userQuery = mysqli_stmt_execute($stmt);

    if (!$userQuery) {
        echo json_encode(["error" => "Failed to execute user query."]);
        exit;
    }

    mysqli_stmt_bind_result($stmt, $idSql, $passwordSql);

    if (mysqli_stmt_fetch($stmt)) {
        if (password_verify($password, $passwordSql)) {
            $tokenUnhashed = substr(str_shuffle(str_repeat($x = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil(32 / strlen($x)))), 1, 32);
            $token = openssl_encrypt(
                $tokenUnhashed,
                $encryptionInfo["cipher"],
                $encryptionInfo["key"],
                $encryptionInfo["options"],
                $encryptionInfo["iv"]
            );
            $id = openssl_encrypt(
                $idSql,
                $encryptionInfo["cipher"],
                $encryptionInfo["key"],
                $encryptionInfo["options"],
                $encryptionInfo["iv"]
            );

            if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
                $ip = $_SERVER['HTTP_CLIENT_IP'];
            } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
            } else {
                $ip = $_SERVER['REMOTE_ADDR'];
            }
            $ip = openssl_encrypt(
                $ip,
                $encryptionInfo["cipher"],
                $encryptionInfo["key"],
                $encryptionInfo["options"],
                $encryptionInfo["iv"]
            );

            $expiresUnhashed = time() + 2592000;
            $expires = openssl_encrypt(
                $expiresUnhashed,
                $encryptionInfo["cipher"],
                $encryptionInfo["key"],
                $encryptionInfo["options"],
                $encryptionInfo["iv"]
            );

            mysqli_stmt_close($stmt);

            $sql = "INSERT INTO tokens (`id`, `account_id`, `ip`, `expires`) VALUES(?, ?, ?, ?);";
            $stmt = mysqli_stmt_init($dbConn);
            mysqli_stmt_prepare($stmt, $sql);
            mysqli_stmt_bind_param($stmt, "ssss", $token, $id, $ip, $expires);
            $tokenQuery = mysqli_stmt_execute($stmt);

            if (!$tokenQuery) {
                echo json_encode(["error" => "Failed to create token query."]);
            } else {
                setcookie("mdash-token", $tokenUnhashed, $expiresUnhashed, "/");
                echo json_encode(["correct" => true]);
            }

            exit;
        } else {
            echo json_encode(["correct" => false]);
            exit;
        }
    } else {
        echo json_encode(["correct" => false]);
        exit;
    }
} else {
    //all required data was not provided
    echo json_encode(["error" => "Required information is missing."]);
    exit;
}