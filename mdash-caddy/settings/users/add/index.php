<?php
$dbConnRequired = true;
require_once "/var/www/mdash/header.php";

$accountId = $accountInfo[0];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,400,0,0" />
</head>

<body>
    <?php
    require_once "/var/www/mdash/settings/header.php";
    ?>
    <div class="darken">
        <div id="container">
            <form id="add-form">
                <?php
                if (!verifyAdmin($accountId)) {
                    echo "<h1>Error</h1>";
                    echo "<p class='secondary'>You do not have access to this page.</p>";
                    echo "<div class='center'><button type='button' onclick='window.history.back();'>Go back</p></div>";
                    exit;
                }
                ?>
                <h1>Add a User</h1>
                <div class="form-field" id="nickname-field">
                    <input type="text" id="nickname" placeholder="Nickname">
                </div>

                <div class="form-field-double" id="username-field">
                    <input type="text" id="username" placeholder="Username">
                    <div class="splitter"></div>
                    <div class="center" style="height: 30px;">
                        <input type="checkbox" id="admin">
                        <label for="admin">Admin</label>
                    </div>
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

                <div class="center">
                    <button type="submit">Add User</button>
                    <button type="button" onclick="window.location.href = '../';">Cancel</button>
                </div>
            </form>
        </div>
    </div>
    <script src="./script.js"></script>
</body>

</html>