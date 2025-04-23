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
if($docker) {
    redResponse("Docker cannot be updated using the script, please download and reinstall the beansaregross/mdash installer.");
}

if (posix_geteuid() !== 0) {
    redResponse("This script must be run as root. Please use 'sudo' to run the script.");
} else {
    greenResponse("You are running this script as root.");
}

$branch = isset($_GET["testing"]) ? "-b testing" : "";

echo "\033[94mmDash Update Script\033[0m\n";

blueResponse("Clearing update folder if it exists.");
shell_exec("[[ -d '/mdash-updater/' ]] && rm -r /mdash-updater/");
shell_exec("mkdir /mdash-updater/");
greenResponse("Successfully cleared update folder.");

blueResponse("Downloading update");
shell_exec("git clone https://github.com/beans-are-gross/mdash $branch /mdash-updater/");
greenResponse("Successfully downloaded update.");

blueResponse("Moving mDash root files to root directory.");
$pwd = "/mdash-updater";
shell_exec("mv $pwd/mdash-root/* /mdash/");
greenResponse("Successfully moved mdash root files to root directory.");

blueResponse("Moving mDash webpage files to /var/www/mdash/.");
shell_exec("mv $pwd/mdash-caddy/ /var/www/mdash/");
greenResponse("Successfully moved mDash webpage files to /var/www/mdash/.");