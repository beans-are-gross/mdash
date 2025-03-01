<?php
$dbConnRequired = true;
require_once "/var/www/mdash/header.php";
$accountId = $accountInfo[0];

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

    //unset the token
    setcookie("mdash-token", "", time() - 3600, "/");

    header("Location: /");
    exit;
}