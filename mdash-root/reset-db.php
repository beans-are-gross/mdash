<?php
echo "\033[94mmDash Database Reset Script\033[0m\n";

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

if (!isset($_GET["confirm"])) {
    redResponse("Please confirm you want to DELETE ALL OF YOUR MDASH DATA by adding 'confirm=true' to the end of the command.");
} elseif (isset($_GET["confirm"])) {
    if ($_GET["confirm"] !== "true") {
        redResponse("Invalid value. Please add 'confirm=true' to the end of the command.");
    } else if ($_GET["confirm"] === "true") {
        greenResponse("Deleting all of your mDash data.");
    }
}

if(!isset($_GET["db_pass"])) {
    redResponse("Please enter the root password for your MySQL server by adding 'db_pass=<your-database-password>'.");
}

blueResponse("Connecting to the mDash database.");

$dbConn = mysqli_connect("127.0.0.1", "root", $_GET["db_pass"]);

if (!$dbConn) {
    redResponse("Failed to connect to the mDash database.");
} else {
    greenResponse("Connected to the mDash database successfully.");
}

blueResponse("Deleting the mDash database.");

$deleteDbQuery = mysqli_query($dbConn, "DROP DATABASE mdash;");

if (!$deleteDbQuery) {
    redResponse("Failed to delete the mDash database. More info: " . mysqli_error($dbConn));
} else {
    greenResponse("Deleted the mDash database successfully.");
}

blueResponse("Deleting the mDash user.");

$deleteUserQuery = mysqli_query($dbConn, "DROP USER 'mdash_php'@'127.0.0.1';");

if (!$deleteUserQuery) {
    redResponse("Failed to delete the mDash user. More info: " . mysqli_error($dbConn));
} else {
    greenResponse("Deleted the mDash user successfully.");
}

echo "\033[94mmDash Database Reset Script Complete\nThank you for using mDash. Farewell!\033[0m\n";