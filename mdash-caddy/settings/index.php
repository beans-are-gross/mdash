<?php
$dbConnRequired = true;
require_once "/var/www/mdash/header.php";
$accountId = $accountInfo[0];
$nickname = decryptData($accountInfo[1]); ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>

<body>
    <div class="darken">
        <form id="update-account-form">
            <h1 style="margin: 0;">Settings</h1>
            <p class="secondary">Version 1.0.1</p>
            <div class="center" style="margin-bottom: 20px;">
                <button type="button" onclick="window.location.href = '../dashboard/';">Back</button>
                <button type="button" onclick="window.location.href = './users/';">Users</button>
                <button type="button" onclick="window.location.href = './tokens/';">Tokens</button>
                <button type="button" onclick="window.location.href = './logout.php';">Log Out</button>
            </div>

            <?php
            //pull the user information
            $sql = "SELECT `username` FROM `accounts` WHERE `id` = ?;";
            $stmt = mysqli_stmt_init($dbConn);
            mysqli_stmt_prepare($stmt, $sql);
            mysqli_stmt_bind_param($stmt, "s", $accountId);
            $accountExecute = mysqli_stmt_execute($stmt);

            if (!$accountExecute) {
                die("Failed to execute account query: " . mysqli_error($dbConn));
            }

            mysqli_stmt_bind_result($stmt, $username);
            mysqli_stmt_fetch($stmt);

            $username = decryptData($username);
            ?>

            <div class=" form-field" id="nickname-field">
                <p class="form-secondary">Nickname</p>
                <p style="width: 100%; text-align: left;"><?php echo $nickname; ?></p>
            </div>

            <div class="form-field" id="username-field">
                <p class="form-secondary">Username</p>
                <p style="width: 100%; text-align: left;"><?php echo $username; ?></p>
            </div>
        </form>
    </div>
</body>

</html>
