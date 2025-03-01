<?php
$dbConnRequired = true;
$noHtml = true;
require_once "/var/www/mdash/header.php";

//check if the user is an admin
if (!verifyAdmin($accountInfo[0])) {
    die("You do not have permission to complete this task.");
}

if (isset($_POST["id"])) {
    $accountId = encryptData($accountInfo[0]);

    //decrypt the id
    $id = decryptData($_POST["id"]);

    //update the data in the database
    $sql = "DELETE FROM `accounts` WHERE `id` = ?;";
    $stmt = mysqli_stmt_init($dbConn);
    mysqli_stmt_prepare($stmt, $sql);
    mysqli_stmt_bind_param($stmt, "s", $id);
    $userQuery = mysqli_stmt_execute($stmt);

    //close the connection
    mysqli_stmt_close($stmt);

    if ($userQuery) {
        //delete all of the tokens in the database
        $sql = "DELETE FROM `tokens` WHERE `account_id` = ?;";
        $stmt = mysqli_stmt_init($dbConn);
        mysqli_stmt_prepare($stmt, $sql);
        mysqli_stmt_bind_param($stmt, "s", $_POST["id"]);
        $tokenQuery = mysqli_stmt_execute($stmt);

        if (!$tokenQuery) {
            die("Failed to delete tokens associated with this user.");
        } else {
            mysqli_stmt_close($stmt);
            mysqli_close($dbConn);
            header("Location: /settings/users/");
            exit;
        }
    } else {
        die("Failed to delete the user in the database: " . mysqli_stmt_error($stmt));
    }
} else {
    die("Required information is missing.");
}