<?php
//turn on error reporting
ini_set("display_errors", 1);
error_reporting(E_ALL);

//get the post data from javascript fetch
$payload = file_get_contents("php://input");
$data = json_decode($payload, true);

if (isset($data["nickname"]) && isset($data["username"]) && isset($data["password"])) {
    //get encryption data
    $config = json_decode(file_get_contents("/mdash/config.json"), true);
    $encryptionInfo = $config["encryption"];
    $dbInfo = $config["dbData"];

    //encrypt the nickname
    $nickname = $data["nickname"];
    $encryptedNickname = openssl_encrypt(
        $nickname,
        $encryptionInfo["cipher"],
        $encryptionInfo["key"],
        $encryptionInfo["options"],
        $encryptionInfo["iv"]
    );

    if (!$encryptedNickname) {
        die("Failed to encrypt your nickname: " . openssl_error_string());
    }

    //encrypt the username
    $username = $data["username"];
    $encryptedUsername = openssl_encrypt(
        $username,
        $encryptionInfo["cipher"],
        $encryptionInfo["key"],
        $encryptionInfo["options"],
        $encryptionInfo["iv"]
    );

    if (!$encryptedUsername) {
        die("Failed to encrypt your username: " . openssl_error_string());
    }

    //hash the password
    $password = $data["password"];
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

    if (!$hashedPassword) {
        die("Failed to hash your password.");
    }

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
        echo json_encode(["error" => "Failed to connect to the database: " . mysqli_connect_error()]);
    }

    $admin = openssl_encrypt(
        true,
        $encryptionInfo["cipher"],
        $encryptionInfo["key"],
        $encryptionInfo["options"],
        $encryptionInfo["iv"]
    );

    $sql = "INSERT INTO accounts (nickname, username, password, admin) VALUES (?, ?, ?, ?)";
    $stmt = mysqli_stmt_init($dbConn);
    mysqli_stmt_prepare($stmt, $sql);
    mysqli_stmt_bind_param($stmt, "ssss", $encryptedNickname, $encryptedUsername, $hashedPassword, $admin);
    $addUserExecute = mysqli_stmt_execute($stmt);

    if (!$addUserExecute) {
        echo json_encode(["error" => "Failed to add the user to the database: " . mysqli_stmt_error($stmt)]);
    } else {
        header("Content-Type: application/json");
        echo json_encode(["success" => true]);
        exit;
    }
}