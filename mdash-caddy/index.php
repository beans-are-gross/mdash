<?php
//turn on error reporting
ini_set("display_errors", 1);
error_reporting(E_ALL);

//if the mdash-token cookis is found, redirect to the dashboard
if (isset($_COOKIE["mdash-token"])) {
    header("Location: /dashboard/");
}

//get encryption data
$config = json_decode(file_get_contents("/mdash/config.json"), true);
$encryptionInfo = $config["encryption"];
$dbInfo = $config["dbData"];

//connect to the database
$dbHost = $dbInfo["dbHost"];
$dbUser = $dbInfo["dbUser"];
$dbPass = openssl_decrypt(
    $dbInfo["dbPass"],
    $encryptionInfo["cipher"],
    $encryptionInfo["key"],
    $encryptionInfo["options"],
    $encryptionInfo["iv"]
);
$dbDatabase = $dbInfo["dbDatabase"];

$dbConn = mysqli_connect($dbHost, $dbUser, $dbPass, $dbDatabase);

if (!$dbConn) {
    die("Failed to connect to the database: " . mysqli_connect_error());
}

//check to see if this is a first time setup
$sql = "SELECT COUNT(*) FROM `accounts`;";
$stmt = mysqli_stmt_init($dbConn);
mysqli_stmt_prepare($stmt, $sql);
$checkExecute = mysqli_stmt_execute($stmt);

if (!$checkExecute) {
    die("Failed to execute the SQL statement: " . mysqli_stmt_error($stmt));
}

mysqli_stmt_bind_result($stmt, $count);
mysqli_stmt_fetch($stmt);

//if there are no accounts, redirect to the welcome screen
if ($count === 0) {
    header("Location: /welcome/");
}

mysqli_stmt_close($stmt);
mysqli_close($dbConn);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./style.css">
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,400,0,0" />
</head>

<body>
    <div id='error-container'>
        <span class='material-symbols-rounded'>error</span>
        <div>
            <h3 id='error-header'>Error</h3>
            <p class='secondary' id='error-message'>Message</p>
        </div>
    </div>
    <div class="darken">
        <div id="container">
            <form id="login-form">
                <h1>Hi there</h1>
                <p class="secondary">Enter your info to login</p>
                <div class="form-field" id="login-form-username-field">
                    <input type="text" id="login-username" placeholder="Username">
                </div>
                <p class="error" id="login-username-error"></p>

                <div class="form-field" id="login-form-password-field">
                    <input type="password" class="input-small" id="login-password" placeholder="Password">
                    <button type="button" class="input-small-icon" id="login-password-reveal">
                        <span class="material-symbols-rounded">
                            visibility
                        </span>
                    </button>
                </div>
                <p class="error" id="login-password-error"></p>

                <button type="submit" style="width: 100px;">Login</button>
            </form>
        </div>
    </div>
    <script src="./functions.js"></script>
    <script src="./script.js"></script>
</body>

</html>