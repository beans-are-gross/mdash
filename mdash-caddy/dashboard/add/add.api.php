<?php
$dbConnRequired = true;
$noHtml = true;
require_once "/var/www/mdash/header.php";

//set output to json
header("Content-Type: application/json");

//get the post data from javascript fetch
$payload = file_get_contents("php://input");
$data = json_decode($payload, true);

if (isset($data["name"]) && isset($data["intUrl"]) && isset($data["intUrlSsl"]) && isset($data["extUrl"]) && isset($data["icon"]) && isset($data["sharing"])) {
    //encrypt the account id
    $accountIdEncrypted = encryptData($accountInfo[0]);

    //check for a link only app
    $intUrl = empty($data["intUrl"]) ? "---" : $data["intUrl"]; //--- means ignore for build-caddyfile.php

    //encrypt the other data
    $name = encryptData($data["name"]);
    $intUrl = encryptData($intUrl);
    $intUrlSsl = encryptData($data["intUrlSsl"]);
    $extUrl = encryptData($data["extUrl"]);
    $icon = encryptData($data["icon"]);

    //check if the app already exists
    $sql = "SELECT COUNT(*) FROM `apps` WHERE `name` = ? OR `ext_url` = ?;";
    $stmt = mysqli_stmt_init($dbConn);
    mysqli_stmt_prepare($stmt, $sql);
    mysqli_stmt_bind_param($stmt, "ss", $name, $extUrl);
    $appCheck = mysqli_stmt_execute($stmt);

    if (!$appCheck) {
        echo json_encode(["error" => "Failed to verify that the app is not a duplicate."]);
    }

    mysqli_stmt_bind_result($stmt, $appCount);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);

    if ($appCount !== 0) {
        echo json_encode(["error" => "An app with the same name or external URL already exists."]);
        exit;
    }

    $sharing = $data["sharing"];
    $sharingEncrypted = [$accountIdEncrypted => encryptData("edit")];
    $sharing = explode(",", $sharing);
    foreach ($sharing as $user) {
        if (empty($user))
            continue;

        $user = explode("=", $user);
        $sharingEncrypted[encryptData($user[0])] = encryptData($user[1]);
    }
    $sharingEncrypted = encryptData(json_encode($sharingEncrypted));

    //insert the data into the database
    $sql = "INSERT INTO `apps` (`name`, `int_url`, `int_url_ssl`, `ext_url`, `icon`, `sharing`, `owner`) VALUES (?, ?, ?, ?, ?, ?, ?);";
    $stmt = mysqli_stmt_init($dbConn);
    mysqli_stmt_prepare($stmt, $sql);
    mysqli_stmt_bind_param($stmt, "sssssss", $name, $intUrl, $intUrlSsl, $extUrl, $icon, $sharingEncrypted, $accountIdEncrypted);
    $appQuery = mysqli_stmt_execute($stmt);

    //close the connection
    mysqli_stmt_close($stmt);
    mysqli_close($dbConn);

    if (!$appQuery) {
        echo json_encode(["error" => "Failed to add the app to the database: " . mysqli_stmt_error($stmt)]);
    } else {
        //call the build Caddyfile script
        $buildCaddyfile = json_decode(shell_exec("php /mdash/build-caddyfile.php"), true);
        if (!isset($buildCaddyfile["status"])) {
            echo json_encode(["error" => "Failed to build Caddyfile."]);
        } else {
            echo json_encode(["success" => true]);
        }
        exit;
    }
} else {
    echo json_encode(["error" => "Required information is missing."]);
    exit;
}