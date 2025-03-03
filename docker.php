<?php
//install packages
echo "Installing packages.\n";
shell_exec("apt-get update");
shell_exec("apt-get install git php-fpm php-mysql systemd systemctl -y");

//install mdash
echo "Downloading mDash.\n";
shell_exec("mkdir /mdash-installer/");

echo "Get environment varibales.\n";
$env = shell_exec("env");
$env = explode("\n", $env);

foreach($env as $envVar){
    $envVar = explode("=", $envVar);
    if($envVar[0] !== "DB_PASS") continue;
    else $dbPass = $envVar[1];
}

if(!isset($dbPass)){
    die("DB_PASS not found.");
}

echo "Running mDash install script.\n";
shell_exec("cd /mdash-installer/ && git clone https://github.com/beans-are-gross/mdash");
shell_exec("cd /mdash-installer/mdash/ && php setup.php db_host=172.220.0.5 db_pass=$dbPass docker=true");
