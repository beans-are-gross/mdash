<?php
$dbConnRequired = true;
$noHtml = true;
require_once "/var/www/mdash/header.php";

//set output to json
header("Content-Type: application/json");

//check if the user is an admin
if (!verifyAdmin($accountInfo[0])) {
    echo json_encode(["error" => "You do not have permission to complete this task."]);
    exit;
}

//get the post data from javascript fetch
$payload = file_get_contents("php://input");
$data = json_decode($payload, true);

if (isset($data["nickname"]) && isset($data["username"]) && isset($data["password"]) && isset($data["admin"]) && isset($data["id"])) {
    $accountId = encryptData($accountInfo[0]);

    //encrypt the other data
    $nickname = encryptData($data["nickname"]);
    $username = encryptData($data["username"]);
    $admin = encryptData($data["admin"]);

    //decrypt the id
    $id = decryptData($data["id"]);

    //check if the app already exists
    $sql = "SELECT `id` FROM `accounts` WHERE `username` = ?;";
    $stmt = mysqli_stmt_init($dbConn);
    mysqli_stmt_prepare($stmt, $sql);
    mysqli_stmt_bind_param($stmt, "s", $username);
    $userCheck = mysqli_stmt_execute($stmt);

    if (!$userCheck) {
        echo json_encode(["error" => "Failed to verify that the user is not a duplicate."]);
    }

    mysqli_stmt_bind_result($stmt, $userId);

    $userExists = false;
    while (mysqli_stmt_fetch($stmt)) {
        if ($userExists)
            break;
        $userExists = $userId == $id ? false : true;
    }

    if ($userExists) {
        echo json_encode(["error" => "A user with the same username already exists."]);
        exit;
    }

    if (!empty(trim($data["password"], " "))) {
        $password = password_hash($data["password"], PASSWORD_BCRYPT);

        //update the data in the database
        $sql = "UPDATE `accounts` SET `nickname` = ?, `username` = ?, `password` = ?, `admin` = ? WHERE `id` = ?;";
        $stmt = mysqli_stmt_init($dbConn);
        mysqli_stmt_prepare($stmt, $sql);
        mysqli_stmt_bind_param($stmt, "sssss", $nickname, $username, $password, $admin, $id);
        $updateUserExecute = mysqli_stmt_execute($stmt);
    } else {
        //update the data in the database
        $sql = "UPDATE `accounts` SET `nickname` = ?, `username` = ?, `admin` = ? WHERE `id` = ?;";
        $stmt = mysqli_stmt_init($dbConn);
        mysqli_stmt_prepare($stmt, $sql);
        mysqli_stmt_bind_param($stmt, "ssss", $nickname, $username, $admin, $id);
        $updateUserExecute = mysqli_stmt_execute($stmt);
    }

    //close the connection
    mysqli_stmt_close($stmt);
    mysqli_close($dbConn);

    if (!$updateUserExecute) {
        echo json_encode(["error" => "Failed to edit the user to the database: " . mysqli_stmt_error($stmt)]);
    } else {
        echo json_encode(["success" => true]);
        exit;
    }
} else {
    echo json_encode(["error" => "Required information is missing."]);
    exit;
}