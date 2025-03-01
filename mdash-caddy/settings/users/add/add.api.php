<?php
$dbConnRequired = true;
$noHtml = true;
require_once "/var/www/mdash/header.php";

//check if the user is an admin
if (!verifyAdmin($accountInfo[0])) {
    echo json_encode(["error" => "You do not have permission to complete this task."]);
    exit;
}

header("Content-Type: application/json");

//get the post data from javascript fetch
$payload = file_get_contents("php://input");
$data = json_decode($payload, true);

if (isset($data["nickname"]) && isset($data["username"]) && isset($data["password"]) && isset($data["admin"])) {
    //encrypt the data
    $nickname = encryptData($data["nickname"]);
    $username = encryptData($data["username"]);
    $password = password_hash($data["password"], PASSWORD_BCRYPT);
    $admin = encryptData($data["admin"]);

    //check if the app already exists
    $sql = "SELECT COUNT(*) FROM `accounts` WHERE `username` = ?;";
    $stmt = mysqli_stmt_init($dbConn);
    mysqli_stmt_prepare($stmt, $sql);
    mysqli_stmt_bind_param($stmt, "s", $username);
    $appCheck = mysqli_stmt_execute($stmt);

    if (!$appCheck) {
        echo json_encode(["error" => "Failed to verify that the user is not a duplicate."]);
    }

    mysqli_stmt_bind_result($stmt, $appCount);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);

    if ($appCount !== 0) {
        echo json_encode(["error" => "A user with the same username already exists."]);
        exit;
    }

    //insert the data into the database
    $sql = "INSERT INTO accounts (nickname, username, password, admin) VALUES (?, ?, ?, ?)";
    $stmt = mysqli_stmt_init($dbConn);
    mysqli_stmt_prepare($stmt, $sql);
    mysqli_stmt_bind_param($stmt, "ssss", $nickname, $username, $password, $admin);
    $addUserExecute = mysqli_stmt_execute($stmt);

    if (!$addUserExecute) {
        echo json_encode(["error" => "Failed to add the user to the database: " . mysqli_stmt_error($stmt)]);
    } else {
        echo json_encode(["success" => true]);
    }
}