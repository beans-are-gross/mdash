<?php
$noHtml = true;
require_once "/var/www/mdash/header.php";

//set output to json
header("Content-Type: application/json");

//check if the user is an admin
if (!verifyAdmin($accountInfo[0])) {
    echo json_encode(["error" => "You do not have permission to complete this task."]);
    exit;
}


// storage
$bytesToGb = 1000000000;

$totalSpace = round(disk_total_space("/") / $bytesToGb, 2);
$freeSpace = round(disk_free_space("/") / $bytesToGb, 2);
$usedSpace = round($totalSpace - $freeSpace, 2);

// memory
$memory = explode("\n", shell_exec("free -m"))[1];
$memory = array_values(array_filter(explode(" ", $memory)));
$totalMemory = $memory[1];
$usedMemory = $memory[2];
$availableMemory = $memory[6];

// server time
$timezone = date_default_timezone_get();
$serverTime = date("G:i:s");
$serverDate = date("M d, o");

// uptime
$upSince = date("M d, o", strtotime(shell_exec("uptime -s")));
$uptime = str_replace("up ", "", shell_exec("uptime -p"));

// network
$ipAddr = shell_exec("hostname -i");
$hostname = shell_exec("hostname");

$returnArray = [
  "total-space" => $totalSpace,
  "free-space" => $freeSpace,
  "used-space" => $usedSpace,
  "total-memory" => $totalMemory,
  "used-memory" => $usedMemory,
  "available-memory" => $availableMemory,
  "timezone" => $timezone,
  "server-time" => $serverTime,
  "server-date" => $serverDate,
  "up-since" => $upSince,
  "uptime" => $uptime,
  "ip-addr" => $ipAddr,
  "hostname" => $hostname,
];

echo json_encode($returnArray);