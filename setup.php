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

$docker = isset($_GET["docker"]);

if (!$docker) {
    if (posix_geteuid() !== 0) {
        redResponse("This script must be run as root. Please use 'sudo' to run the script.");
    } else {
        greenResponse("You are running this script as root.");
    }
}

if ($docker) {
    blueResponse("Docker Setup");
    $dbHost = "172.220.0.5";
} else {
    if (isset($_GET["db_host"])) {
        $dbHost = $_GET["db_host"];
    } else {
        $dbHost = "127.0.0.1";
    }
}

if (!isset($_GET["db_pass"])) {
    if ($docker) {
        $env = shell_exec("env");
        $env = explode("\n", $env);
        foreach ($env as $envVar) {
            if (strpos($envVar, "DB_PASS=") === 0) {
                $dbPass = explode("=", $envVar)[1];
                break;
            }
        }

        if (!isset($dbPass)) {
            redResponse("Please set the root password for your MySQL server using '-e DB_PASS=<your-database-password>'.");
        }
    } else {
        redResponse("Please enter the root password for your MySQL server by adding 'db_pass=<your-database-password>'.");
    }
} else {
    $dbPass = $_GET["db_pass"];
}

system("clear");

echo "\033[94m +--------------+ \033[0m\n";
echo "\033[94m | mDash Script | \033[0m\n";
echo "\033[94m +--------------+ \033[0m\n";

if (!$docker) {
    echo "Please select what you would like to do:\n";
    echo "1. Install\n";
    echo "2. Update\n";
    $ask = readline(">");
    if ($ask == "1") {
        system("clear");
        greenResponse("Installing mDash");
        $update = false;
    } else if ($ask == 2) {
        system("clear");
        greenResponse("Updating mDash");
        $update = true;
    } else {
        redResponse("Answer '$ask' is invalid, please run the command again and respond with a valid answer.");
    }
} else {
    $update = false;
}

// +============+
// | File Setup |
// +============+

$pwd = str_replace("\n", "", shell_exec("pwd"));

if ($update) {
    blueResponse("Moving mDash config file to root directory.");
    shell_exec("mv /mdash/config.json /mdash-config-copy.json");
    greenResponse("Successfully moved mDash config file to root directory.");
}

blueResponse("Moving mDash root files to root directory.");
if ($update) {
    shell_exec("rm -r /mdash/");
}
shell_exec("mkdir /mdash/ && mv $pwd/mdash-root/* /mdash/");
greenResponse("Successfully moved mDash root files to root directory.");

blueResponse("Moving mDash webpage files to /var/www/mdash/.");
if ($update) {
    shell_exec("rm -r /var/www/mdash/");
} else {
    shell_exec("mkdir /var/www/");
}
shell_exec("mkdir /var/www/mdash && mv $pwd/mdash-caddy/* /var/www/mdash/");
greenResponse("Successfully moved mDash webpage files to /var/www/mdash/.");

blueResponse("Changing /mdash/ group to www-data.");
shell_exec("chgrp -R www-data /mdash/");
greenResponse("Successfully changed group.");

if (!$docker) {
    blueResponse("Changing /mdash/ group to www-data. (For modules)");
    shell_exec("chgrp -R www-data /mdash/");
    greenResponse("Successfully changed group.");

    blueResponse("Changing /mdash/ permission to 770. (For modules)");
    shell_exec("chmod -R 770 /mdash/");
    greenResponse("Successfully changed permissions.");

    blueResponse("Changing /var/www/ group to www-data. (For modules)");
    shell_exec("chgrp -R www-data /var/www/");
    greenResponse("Successfully changed group.");

    blueResponse("Changing /var/www/ permission to 770. (For modules)");
    shell_exec("chmod -R 770 /var/www/");
    greenResponse("Successfully changed permissions.");
}

// +==============================================+
// | Install and setup Caddy, xcaddy, go, and npm |
// +==============================================+

blueResponse("Installing Caddy. This might take a minute to complete. (Please answer yes to the questions asked.)");
shell_exec("apt-get install debian-keyring debian-archive-keyring apt-transport-https curl -y");
shell_exec("curl -1sLf 'https://dl.cloudsmith.io/public/caddy/stable/gpg.key' | gpg --dearmor -o /usr/share/keyrings/caddy-stable-archive-keyring.gpg");        //caddy
shell_exec("curl -1sLf 'https://dl.cloudsmith.io/public/caddy/stable/debian.deb.txt' | tee /etc/apt/sources.list.d/caddy-stable.list");                         //caddy
shell_exec("curl -1sLf 'https://dl.cloudsmith.io/public/caddy/xcaddy/gpg.key' | sudo gpg --dearmor -o /usr/share/keyrings/caddy-xcaddy-archive-keyring.gpg");   //xcaddy
shell_exec("curl -1sLf 'https://dl.cloudsmith.io/public/caddy/xcaddy/debian.deb.txt' | sudo tee /etc/apt/sources.list.d/caddy-xcaddy.list");                    //xcaddy
shell_exec("apt-get update && apt-get install caddy xcaddy golang-go -y");
greenResponse("Successfully installed Caddy.");

if (!$update) {
    blueResponse("Seting up Caddy configuration.");
    if ($docker) {
        $caddyfile = ":8080 {\n" .
            "   root * /var/www/mdash/\n" .
            "   file_server\n" .
            "   php_fastcgi 172.220.0.10:9000\n" .
            "}\n\n";
        file_put_contents("/etc/caddy/Caddyfile", $caddyfile);
    } else {
        $caddyfile = ":8080 {\n" .
            "   root * /var/www/mdash/\n" .
            "   file_server\n" .
            "   php_fastcgi unix//run/php/php-fpm.sock\n" .
            "}\n\n";
        file_put_contents("/etc/caddy/Caddyfile", $caddyfile);
    }
}

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

if (!$docker) {
    $sudoPermissions = "www-data ALL=(ALL) NOPASSWD: /usr/bin/dpkg-divert --divert /usr/bin/caddy.default --rename /usr/bin/caddy, /usr/bin/mv ./caddy /usr/bin/caddy.custom, /usr/bin/update-alternatives --install /usr/bin/caddy caddy /usr/bin/caddy.default 10, /usr/bin/update-alternatives --install /usr/bin/caddy caddy /usr/bin/caddy.custom 50, /usr/bin/systemctl restart caddy";
    if (shell_exec("tail -n 1 /etc/sudoers") !== $sudoPermissions) {
        blueResponse("Updating sudoers file to give the caddy user moving permissions. (For modules)");

        shell_exec("usermod -aG sudo www-data");
        shell_exec("echo '$sudoPermissions' >> /etc/sudoers");

        greenResponse("Successfully updated permissions.");
    }
}

// +================+
// | Setup database |
// +================+

blueResponse("Connecting to database using the provided password.");
$dbConn = mysqli_connect($dbHost, "root", $dbPass);
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
$createTokenTableQuery = mysqli_query($dbConn, "CREATE TABLE IF NOT EXISTS `mdash`.`apps` ( `id` INT NOT NULL AUTO_INCREMENT , `name` VARCHAR(255) NOT NULL , `int_url` VARCHAR(255) NOT NULL , `int_url_ssl` VARCHAR(255) NOT NULL , `ext_url` VARCHAR(255) NOT NULL , `icon` VARCHAR(255) NOT NULL , `sharing` VARCHAR(1000) NOT NULL , `owner` VARCHAR(1000) NOT NULL , PRIMARY KEY (`id`));");
if (!$createTokenTableQuery) {
    redResponse("Failed to create the app table. More info: " . mysqli_error($dbConn));
} else {
    greenResponse("Created the app table successfully.");
}

if (!$docker) {
    blueResponse("Creating the module table.");
    $createModuleTableQuery = mysqli_query($dbConn, "CREATE TABLE IF NOT EXISTS `mdash`.`modules` ( `id` INT NOT NULL AUTO_INCREMENT , `url`  VARCHAR(1000) NOT NULL , PRIMARY KEY (`id`));");
    if (!$createModuleTableQuery) {
        redResponse("Failed to create the module table. More info: " . mysqli_error($dbConn));
    } else {
        greenResponse("Created the module table successfully.");
    }
}

if ($update) {
    if ($update) {
        blueResponse("Moving mDash config file to /mdash/ directory.");
        shell_exec("cp /mdash-config-copy.json /mdash/config.json");
        greenResponse("Successfully moved mDash config file to /mdash/ directory.");
    }

    $ip = str_replace("\n", "", shell_exec("hostname -i"));
    echo "\033[94mmDash Update Script Complete\nEnjoy at: {$ip}:8080\033[0m\n";
    exit;
}

blueResponse("Checking for an old mDash user.");
$dbUser = "mdash_php";
$checkIfUserExistsQuery = mysqli_query($dbConn, "SELECT EXISTS(SELECT 1 FROM mysql.user WHERE user = '$dbUser');");
if (!$checkIfUserExistsQuery) {
    redResponse("Failed to check if an old mDash user existed. More info: " . mysqli_error($dbConn));
} else {
    if (mysqli_fetch_row($checkIfUserExistsQuery)[0] === "0") {
        greenResponse("No old mDash user found.");
        blueResponse("Creating the mDash user.");

        $userIp = $docker ? "172.220.0.10" : "127.0.0.1";

        $createUserQuery = mysqli_query($dbConn, "CREATE USER IF NOT EXISTS `mdash_php`@`$userIp` IDENTIFIED WITH caching_sha2_password BY '$dbGeneratedPass';");

        if (!$createUserQuery) {
            redResponse("Failed to create mDash user. More info: " . mysqli_error($dbConn));
        } else {
            greenResponse("Created the mDash user successfully.");

            blueResponse("Granting the mDash user the necessary privileges.");
            $grantUserQuery = mysqli_query($dbConn, "GRANT SELECT, INSERT, UPDATE, DELETE ON  `mdash`.* TO `mdash_php`@`$userIp`;");
            if (!$grantUserQuery) {
                redResponse("Failed to grant mDash user privileges. More info: " . mysqli_error($dbConn));
            } else {
                greenResponse("Granted the mDash user the necessary privileges successfully.");
            }
        }
    } else {
        redResponse("An old mDash user was detected. The script has exited.");
    }

    mysqli_free_result($checkIfUserExistsQuery);
}

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

$config["docker"] = $docker;

blueResponse("Generating the JSON for the config file.");
$configJson = json_encode($configJson);
greenResponse("Generated the JSON for the config file successfully.");

blueResponse("Writing the JSON to the config file.");
file_put_contents("/mdash/config.json", $configJson);
greenResponse("Wrote the JSON to the config file successfully.");

if ($docker) {
    echo "\033[94mmDash Setup Script Complete\033[0m\n";
    exit(143); //Graceful termination (SIGTERM)
} else {
    $ip = str_replace("\n", "", shell_exec("hostname -i"));
    echo "\033[94mmDash Setup Script Complete\nEnjoy at: {$ip}:8080\033[0m\n";
}
