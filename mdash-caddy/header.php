<?php
//turn on error reporting
ini_set("display_errors", 1);
error_reporting(E_ALL);

require_once "/var/www/mdash/functions.php";

$accountInfo = verifyLogin($_COOKIE["mdash-token"]);

if (!is_array($accountInfo)) {
    //the verification failed, so delete the cookie and redirect
    setcookie("mdash-token", "", time() - 3600, "/");
    header("Location: /?$accountInfo");
} else {
    $config = json_decode(file_get_contents("/mdash/config.json"), true);
    $docker = $config["docker"];

    //check if the script wants a database connection
    if (isset($dbConnRequired)) {
        //connect to the database
        $dbInfo = $config["dbData"];

        $dbHost = $dbInfo["dbHost"];
        $dbUser = $dbInfo["dbUser"];
        $dbPass = decryptData($dbInfo["dbPass"]);
        $dbDatabase = $dbInfo["dbDatabase"];

        $dbConn = mysqli_connect($dbHost, $dbUser, $dbPass, $dbDatabase);

        if (!$dbConn) {
            die("Failed to connect to the database: " . mysqli_connect_error());
        }
    }
    
    unset($config);
}

//check if the script wants html
if (!isset($noHtml)) {
    echo "
    <!DOCTYPE html>
    <html lang='en'>

    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>mDash</title>
        <link rel='stylesheet'
            href='https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,400,0,0' />
        <link rel='stylesheet' href='/style.css'>
        <script src='/functions.js'></script>
    </head>

    <body>
        <div id='error-container'>
            <span class='material-symbols-rounded'>error</span>
            <div>
                <h3 id='error-header'>Error</h3>
                <p class='secondary' id='error-message'>Message</p>
            </div>
        </div>
    </body>
";
}
?>