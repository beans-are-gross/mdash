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

    $command = "cd /mdash/ && ./build-caddy.sh";

    $modules = $data["modules"];
    $modules = explode(",", $modules);
    foreach ($modules as $module) {
        if (empty($module))
            continue;

        $module = encryptData($module);

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

        $command .= " $module";
    }

    exec($command, $output, $code);

    if ($code != 0) {
        echo json_encode(["error" => "xcaddy failed to build, returned $code <br> Check /mdash/build-caddy.log for error info."]);
    }

    echo json_encode(["success" => true]);
} else {
    echo json_encode(["error" => "Required information is missing."]);
    exit;
}