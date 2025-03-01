<?php
$dbConnRequired = true;
$noHtml = true;
require_once "/var/www/mdash/header.php";

if (isset($_POST["id"])) {
    $accountId = encryptData($accountInfo[0]);

    //decrypt the id
    $id = decryptData($_POST["id"]);

    //check if the user has edit access
    $sql = "SELECT `sharing` FROM `apps` WHERE `id` = ?;";
    $stmt = mysqli_stmt_init($dbConn);
    mysqli_stmt_prepare($stmt, $sql);
    mysqli_stmt_bind_param($stmt, "s", $id);
    $appExecute = mysqli_stmt_execute($stmt);

    if (!$appExecute) {
        die("Failed to execute app query: " . mysqli_error($dbConn));
    }

    mysqli_stmt_bind_result($stmt, $sharing);
    mysqli_stmt_fetch($stmt);

    //check if the app exists
    if (is_null($sharing)) {
        die("This app does not exist.");
    }

    //check if the user has edit access
    $sharing = json_decode(decryptData($sharing), true);
    if (decryptData($sharing[encryptData($accountInfo[0])]) !== "edit") {
        die("You don't have access to edit this app.");
    }

    //update the data in the database
    $sql = "DELETE FROM `apps` WHERE `id` = ?;";
    $stmt = mysqli_stmt_init($dbConn);
    mysqli_stmt_prepare($stmt, $sql);
    mysqli_stmt_bind_param($stmt, "s", $id);
    $appQuery = mysqli_stmt_execute($stmt);

    //close the connection
    mysqli_stmt_close($stmt);
    mysqli_close($dbConn);

    if (!$appQuery) {
        die("Failed to delete the app in the database: " . mysqli_stmt_error($stmt));
    } else {
        //call the build Caddyfile script
        $buildCaddyfile = json_decode(shell_exec("php /mdash/build-caddyfile.php"), true)["status"];
        if ($buildCaddyfile !== "ok") {
            die("Failed to build Caddyfile.");
        } else {
            header("Location: /dashboard/");
        }
        exit;
    }
} else {
    die("Required information is missing.");
}