<?php
ini_set("display_errors", 1);
error_reporting(E_ALL);

function blueResponse($response)
{
    echo "\033[96m$response\033[0m \n";
}

function greenResponse($response)
{
    echo "\033[92m$response\033[0m \n";
}

function redResponse($response)
{
    die("\033[91m$response\033[0m \n");
}

parse_str(implode('&', array_slice($argv, 1)), $_GET);

// +============+
// | Root Check |
// +============+
if (!isset($_GET["docker"])) {
    if (posix_geteuid() !== 0) {
        redResponse("This script must be run as root. Please use 'sudo' to run the script.");
    } else {
        greenResponse("You are running this script as root.");
    }
}

$dbHost = "127.0.0.1";
if (isset($_GET["db_host"])) {
    $dbHost = $_GET["db_host"];
}

if (!isset($_GET["db_pass"])) {
    redResponse("Please enter the root password for your MySQL server by adding 'db_pass=<your-database-password>'.");
}

echo "\033[94mmDash Setup Script\033[0m\n";

// +============+
// | File Setup |
// +============+

blueResponse("Moving mDash root files to root directory.");
$pwd = str_replace("\n", "", shell_exec("pwd"));
shell_exec("mv $pwd/mdash-root/ /mdash/");
greenResponse("Successfully moved mdash root files to root directory.");

blueResponse("Moving mDash webpage files to /var/www/mdash/.");
shell_exec("mkdir /var/www/");
shell_exec("mv $pwd/mdash-caddy/ /var/www/mdash/");
greenResponse("Successfully moved mDash webpage files to /var/www/mdash/.");

// +=========================+
// | Install and setup Caddy |
// +=========================+

blueResponse("Installing Caddy. This might take a few seconds to complete.");
shell_exec("apt-get install -y debian-keyring debian-archive-keyring apt-transport-https curl");
shell_exec("curl -1sLf 'https://dl.cloudsmith.io/public/caddy/stable/gpg.key' | gpg --dearmor -o /usr/share/keyrings/caddy-stable-archive-keyring.gpg");
shell_exec("curl -1sLf 'https://dl.cloudsmith.io/public/caddy/stable/debian.deb.txt' | tee /etc/apt/sources.list.d/caddy-stable.list");
shell_exec("apt-get update && apt-get install caddy");
greenResponse("Successfully installed Caddy.");

blueResponse("Seting up Caddy configuration.");
$caddyfile = ":8080 {\n" .
    "   root * /var/www/mdash/\n" .
    "   file_server\n" .
    "   php_fastcgi unix//run/php/php-fpm.sock\n" .
    "}\n\n";
file_put_contents("/etc/caddy/Caddyfile", $caddyfile);

shell_exec("cd /etc/caddy/ && caddy fmt --overwrite");

blueResponse("Giving PHP access to Caddyfile.");

blueResponse("Changing Caddyfile owner to root and group to www-data.");
shell_exec("chown root:www-data /etc/caddy/Caddyfile");
greenResponse("Successfully changed Caddyfile owner and group.");

blueResponse("Changing Caddyfile access using chmod 660.");
shell_exec("chmod 660 /etc/caddy/Caddyfile");
greenResponse("Successfully changed Caddyfile access.");

shell_exec('systemctl reload caddy');
greenResponse("Set up Caddy configuration successfully.");

// +================+
// | Setup database |
// +================+

$configJson = [];

blueResponse("Generating the cipher information.");
$cipher = "aes-256-ctr";
$encryptionKey = substr(str_shuffle(str_repeat($x = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil(32 / strlen($x)))), 1, 32);
$options = 0;
$iv = rand(1000000000000000, 9999999999999999);
greenResponse("Generated the cipher information successfully.");

blueResponse("Adding the cipher information to the config file.");
$configJson["encryption"] = ["cipher" => $cipher, "key" => $encryptionKey, "options" => $options, "iv" => $iv];
greenResponse("Added the cipher information to the config file successfully.");

blueResponse("Generating the database password for mDash.");
$dbGeneratedPass = substr(str_shuffle(str_repeat($x = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil(32 / strlen($x)))), 1, 32);
greenResponse("Generated the database password for mDash successfully.");

blueResponse("Connecting to database using the provided password.");
$dbConn = mysqli_connect($dbHost, "root", $_GET["db_pass"]);
if (!$dbConn) {
    redResponse("Failed to connect to database. Please make sure you have MySQL installed. More info: " . mysqli_connect_error());
} else {
    greenResponse("Connected to the database successfully.");
}

blueResponse("Creating the mDash database.");
$createDb = mysqli_query($dbConn, "CREATE DATABASE IF NOT EXISTS `mdash`;");
if (!$createDb) {
    redResponse("Failed to create mDash database. More info: " . mysqli_error($dbConn));
} else {
    greenResponse("Created the mDash database successfully.");
}

blueResponse("Selecting the mDash database.");
mysqli_select_db($dbConn, "mdash");
greenResponse("Selected the mDash database successfully.");

blueResponse("Checking for an old mDash user.");
$dbUser = "mdash_php";
$checkIfUserExistsQuery = mysqli_query($dbConn, "SELECT EXISTS(SELECT 1 FROM mysql.user WHERE user = '$dbUser');");
if (!$checkIfUserExistsQuery) {
    redResponse("Failed to check if an old mDash user existed. More info: " . mysqli_error($dbConn));
} else {
    if (mysqli_fetch_row($checkIfUserExistsQuery)[0] === "0") {
        greenResponse("No old mDash user found.");
        blueResponse("Creating the mDash user.");

        $createUserQuery = mysqli_query($dbConn, "CREATE USER IF NOT EXISTS `mdash_php`@`127.0.0.1` IDENTIFIED WITH caching_sha2_password BY '$dbGeneratedPass';");

        if (!$createUserQuery) {
            redResponse("Failed to create mDash user. More info: " . mysqli_error($dbConn));
        } else {
            greenResponse("Created the mDash user successfully.");
        }
    } else {
        redResponse("An old mDash user was detected. The script has exited to prevent complete data loss.");
    }

    mysqli_free_result($checkIfUserExistsQuery);
}

blueResponse("Granting the mDash user the necessary privileges.");
$grantUserQuery = mysqli_query($dbConn, "GRANT SELECT, INSERT, UPDATE, DELETE ON  `mdash`.* TO `mdash_php`@`127.0.0.1`;");
if (!$grantUserQuery) {
    redResponse("Failed to grant mDash user privileges. More info: " . mysqli_error($dbConn));
} else {
    greenResponse("Granted the mDash user the necessary privileges successfully.");
}

blueResponse("Creating the user account table.");
$createUserAccountTableQuery = mysqli_query($dbConn, "CREATE TABLE IF NOT EXISTS `mdash`.`accounts` ( `id` INT NOT NULL AUTO_INCREMENT , `nickname` VARCHAR(255) NOT NULL , `username` VARCHAR(255) NOT NULL , `password` VARCHAR(255) NOT NULL , `admin` VARCHAR(255) NOT NULL , PRIMARY KEY (`id`))");
if (!$createUserAccountTableQuery) {
    redResponse("Failed to create the user account table. More info: " . mysqli_error($dbConn));
} else {
    greenResponse("Created the user account table successfully.");
}

blueResponse("Creating the token table.");
$createTokenTableQuery = mysqli_query($dbConn, "CREATE TABLE IF NOT EXISTS `mdash`.`tokens` ( `id` VARCHAR(255) NOT NULL , `account_id`  VARCHAR(255) NOT NULL , `ip`  VARCHAR(255) NOT NULL , `expires`  VARCHAR(255) NOT NULL , PRIMARY KEY (`id`));");
if (!$createTokenTableQuery) {
    redResponse("Failed to create the token table. More info: " . mysqli_error($dbConn));
} else {
    greenResponse("Created the token table successfully.");
}

blueResponse("Creating the app table.");
$createTokenTableQuery = mysqli_query($dbConn, "CREATE TABLE `mdash`.`apps` ( `id` INT NOT NULL AUTO_INCREMENT , `name` VARCHAR(255) NOT NULL , `int_url` VARCHAR(255) NOT NULL , `int_url_ssl` VARCHAR(255) NOT NULL , `ext_url` VARCHAR(255) NOT NULL , `icon` VARCHAR(255) NOT NULL , `sharing` VARCHAR(1000) NOT NULL , `owner` VARCHAR(1000) NOT NULL , PRIMARY KEY (`id`));");
if (!$createTokenTableQuery) {
    redResponse("Failed to create the app table. More info: " . mysqli_error($dbConn));
} else {
    greenResponse("Created the app table successfully.");
}

blueResponse("Encrypting the mDash database password for the config file.");
$encryptedDbPass = openssl_encrypt(
    $dbGeneratedPass,
    $cipher,
    $encryptionKey,
    $options,
    $iv
);
if (!$encryptedDbPass) {
    redResponse("Failed to encrypt the mDash database password. More info: " . openssl_error_string());
} else {
    greenResponse("Encrypted the mDash database password for the config file successfully.");
}

blueResponse("Adding the encrypted mDash database password for the config file.");
$configJson["dbData"] = ["dbHost" => $dbHost, "dbUser" => $dbUser, "dbPass" => $encryptedDbPass, "dbDatabase" => "mdash"];
greenResponse("Added the encrypted mDash database password for the config file successfully.");

blueResponse("Generating the JSON for the config file.");
$configJson = json_encode($configJson);
greenResponse("Generated the JSON for the config file successfully.");

blueResponse("Writing the JSON to the config file.");
file_put_contents("/mdash/config.json", $configJson);
greenResponse("Wrote the JSON to the config file successfully.");

$ip = str_replace("\n", "", shell_exec("hostname -i"));

echo "\033[94mmDash Setup Script Complete\nEnjoy at: {$ip}:8080\033[0m\n";
