<?php
$dbConnRequired = true;
$noHtml = true;
require_once "/var/www/mdash/header.php";

//set output to json
header("Content-Type: application/json");

//get the post data from javascript fetch
$payload = file_get_contents("php://input");
$data = json_decode($payload, true);

if (isset($data["name"]) && isset($data["intUrl"]) && isset($data["intUrlSsl"]) && isset($data["extUrl"]) && isset($data["icon"]) && isset($data["id"]) && isset($data["sharing"])) {
    $accountId = encryptData($accountInfo[0]);

    //decrypt the id
    $id = decryptData($data["id"]);

    //check for a link only app
    $intUrl = empty($data["intUrl"]) ? "---" : $data["intUrl"]; //--- means ignore for build-caddyfile.php

    //encrypt the other data
    $name = encryptData($data["name"]);
    $intUrl = encryptData($intUrl);
    $intUrlSsl = encryptData($data["intUrlSsl"]);
    $extUrl = encryptData($data["extUrl"]);
    $icon = encryptData($data["icon"]);

    //check if the app already exists
    $sql = "SELECT `id` FROM `apps` WHERE `name` = ? OR `ext_url` = ?;";
    $stmt = mysqli_stmt_init($dbConn);
    mysqli_stmt_prepare($stmt, $sql);
    mysqli_stmt_bind_param($stmt, "ss", $name, $extUrl);
    $appCheck = mysqli_stmt_execute($stmt);

    if (!$appCheck) {
        echo json_encode(["error" => "Failed to verify that the app is not a duplicate."]);
    }

    mysqli_stmt_bind_result($stmt, $appId);

    $appExists = false;
    while (mysqli_stmt_fetch($stmt)) {
        if ($appExists)
            break;
        $appExists = $appId == $id ? false : true;
    }

    if ($appExists) {
        echo json_encode(["error" => "An app with the same name or external URL already exists."]);
        exit;
    }

    mysqli_stmt_close($stmt);

    $sharing = $data["sharing"];
    $sharingEncrypted = [encryptData($accountInfo[0]) => encryptData("edit")];
    $sharing = explode(",", $sharing);
    foreach ($sharing as $user) {
        if (empty($user))
            continue;

        $user = explode("=", $user);
        $sharingEncrypted[encryptData($user[0])] = encryptData($user[1]);
    }
    $sharingEncrypted = encryptData(json_encode($sharingEncrypted));

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

    $sharingCheck = json_decode(decryptData($sharing), true);
    if (decryptData($sharingCheck[$accountId]) !== "edit") {
        echo json_encode(["error" => "You dot have access to edit this file."]);
        exit;
    }

    //update the data in the database
    $sql = "UPDATE `apps` SET `name` = ?, `int_url` = ?, `int_url_ssl` = ?, `ext_url` = ?, `icon` = ?, `sharing` = ? WHERE `id` = ?;";
    $stmt = mysqli_stmt_init($dbConn);
    mysqli_stmt_prepare($stmt, $sql);
    mysqli_stmt_bind_param($stmt, "sssssss", $name, $intUrl, $intUrlSsl, $extUrl, $icon, $sharingEncrypted, $id);
    $appUpdate = mysqli_stmt_execute($stmt);

    //close the connection
    mysqli_stmt_close($stmt);
    mysqli_close($dbConn);

    if (!$appUpdate) {
        echo json_encode(["error" => "Failed to edit the app in the database: " . mysqli_stmt_error($stmt)]);
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