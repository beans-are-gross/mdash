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

if (isset($data["customConfig"])) {
    $customConfig = strip_tags($data["customConfig"]);
    $customConfigLog = "/mdash/custom-config.log";
    $customCaddyfile = "/mdash/custom.caddyfile";

    if (file_exists($customCaddyfile)) {
        shell_exec("rm $customCaddyfile");
    }

    if (file_exists($customConfigLog)) {
        shell_exec("> $customConfigLog");
    }

    shell_exec("touch $customCaddyfile");
    shell_exec("echo '$customConfig' >> $customCaddyfile");
    exec("cd /mdash/ && caddy validate --config $customCaddyfile >> custom-config.log 2>&1 ", $output, $code);

    if ($code !== 0) {
        shell_exec("rm $customCaddyfile");
        $customConfigErrors = nl2br(file_get_contents($customConfigLog));
        echo json_encode(["error" => $customConfigErrors]);
        exit;
    } else {
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