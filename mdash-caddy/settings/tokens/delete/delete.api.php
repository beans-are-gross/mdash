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

    $id = decryptData($_POST["id"]);

    //delete the data from the database
    $sql = "DELETE FROM `tokens` WHERE `id` = ?;";
    $stmt = mysqli_stmt_init($dbConn);
    mysqli_stmt_prepare($stmt, $sql);
    mysqli_stmt_bind_param($stmt, "s", $id);
    $tokenQuery = mysqli_stmt_execute($stmt);

    //close the connection
    mysqli_stmt_close($stmt);

    if (!$tokenQuery) {
        die("Failed to delete the token in the database: " . mysqli_stmt_error($stmt));
    } else {
        header("Location: /settings/tokens/");
    }
} else {
    die("Required information is missing.");
}