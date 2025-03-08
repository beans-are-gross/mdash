<?php
$dbConnRequired = true;
$noHtml = true;
require_once "/var/www/mdash/header.php";

//set output to json
header("Content-Type: application/json");

//get the post data from javascript fetch
$payload = file_get_contents("php://input");
$data = json_decode($payload, true);

if (isset($data["modules"])) {
    $sql = "DELETE FROM `modules`;";
    $stmt = mysqli_stmt_init($dbConn);
    mysqli_stmt_prepare($stmt, $sql);
    $moduleQuery = mysqli_stmt_execute($stmt);
    if (!$moduleQuery) {
        echo json_encode(["error" => "Failed to empty module database: " . mysqli_stmt_error($stmt)]);
        exit;
    }
    mysqli_stmt_close($stmt);

    $command = "cd /mdash/ && xcaddy build";

    $modules = $data["modules"];
    $modules = explode(",", $modules);
    foreach ($modules as $module) {
        if (empty($module))
            continue;

        $sql = "INSERT INTO `modules` (`url`) VALUES (?);";
        $stmt = mysqli_stmt_init($dbConn);
        mysqli_stmt_prepare($stmt, $sql);
        mysqli_stmt_bind_param($stmt, "s", $module);
        $moduleQuery = mysqli_stmt_execute($stmt);
        if (!$moduleQuery) {
            echo json_encode(["error" => "Failed to add module: " . mysqli_stmt_error($stmt)]);
            exit;
        }
        mysqli_stmt_close($stmt);

        $command .= " --with $module";
    }

    shell_exec($command);

    shell_exec("sudo dpkg-divert --divert /usr/bin/caddy.default --rename /usr/bin/caddy");
    shell_exec("sudo mv ./caddy /usr/bin/caddy.custom");
    shell_exec("sudo update-alternatives --install /usr/bin/caddy caddy /usr/bin/caddy.default 10");
    shell_exec("sudo update-alternatives --install /usr/bin/caddy caddy /usr/bin/caddy.custom 50");
    shell_exec("sudo systemctl restart caddy");
} else {
    echo json_encode(["error" => "Required information is missing."]);
    exit;
}