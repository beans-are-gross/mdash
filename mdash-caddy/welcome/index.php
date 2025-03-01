<?php
//turn on error reporting
ini_set("display_errors", 1);
error_reporting(E_ALL);

$config = json_decode(file_get_contents("/mdash/config.json"), true);
$encryptionInfo = $config["encryption"];
$dbInfo = $config["dbData"];

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

$sql = "SELECT COUNT(*) FROM `accounts`;";
$stmt = mysqli_stmt_init($dbConn);
mysqli_stmt_prepare($stmt, $sql);
$checkExecute = mysqli_stmt_execute($stmt);

if (!$checkExecute) {
    die("Failed to execute the SQL statement: " . mysqli_stmt_error($stmt));
}

mysqli_stmt_bind_result($stmt, $count);
mysqli_stmt_fetch($stmt);

if ($count !== 0) {
    die("The welcome page is locked because a user already exists.");
}

mysqli_stmt_close($stmt);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>mDash</title>
    <link rel="stylesheet" href="../style.css">
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
            <form id="welcome-form">
                <h1>Welcome to mDash</h1>
                <p class="secondary">Just one step to begin.</p>
                <div class="form-field" id="nickname-field">
                    <input type="text" id="nickname" placeholder="Nickname">
                </div>

                <div class="form-field" id="username-field">
                    <input type="text" id="username" placeholder="Username">
                </div>

                <div class="form-field" id="password-field">
                    <input type="password" class="input-small" id="password" placeholder="Password">
                    <button type="button" class="input-small-icon" id="password-reveal">
                        <span class="material-symbols-rounded">
                            visibility
                        </span>
                    </button>
                </div>

                <div class="form-field" id="password-verify-field">
                    <input type="password" class="input-small" id="password-verify" placeholder="Verify password">
                    <button type="button" class="input-small-icon" id="password-verify-reveal">
                        <span class="material-symbols-rounded">
                            visibility
                        </span>
                    </button>
                </div>

                <button type="submit">Sign up</button>
            </form>
        </div>
    </div>
    <script src="/functions.js"></script>
    <script src="./script.js"></script>
</body>

</html>