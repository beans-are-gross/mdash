<?php
$dbConnRequired = true;
require_once "/var/www/mdash/header.php";
$accountId = $accountInfo[0];
$nickname = decryptData($accountInfo[1]); ?>

<body>
    <?php
    require_once "/var/www/mdash/settings/header.php";
    ?>
    <div id="darken" class="darken">
        <form id="update-account-form">
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

            <h1>Your info</h1>

            <div class=" form-field" id="nickname-field">
                <p class="form-secondary">Nickname</p>
                <p style="width: 100%; text-align: left;"><?php echo $nickname; ?></p>
            </div>

            <div class="form-field" id="username-field">
                <p class="form-secondary">Username</p>
                <p style="width: 100%; text-align: left;"><?php echo $username; ?></p>
            </div>
        </form>

        <div id="settings-home">
            <button type="button" class="settings-home-button" id="users"
                onclick="window.location.href = '/settings/users/';">
                <div>
                    <span class="material-symbols-rounded">groups</span>
                    <h2>Users</h2>
                    <p>Add, edit, and remove users from mDash.</p>
                </div>
            </button>
            <button type="button" class="settings-home-button"
                onclick="window.location.href = '/settings/system-info/';">
                <div>
                    <span class="material-symbols-rounded">monitor</span>
                    <h2>System Info</h2>
                    <p>View the information about your server.</p>
                </div>
            </button>
            <button type="button" class="settings-home-button" id="modules"
                onclick="window.location.href = '/settings/modules/';">
                <div>
                    <span class="material-symbols-rounded">extension</span>
                    <h2>Modules</h2>
                    <p>View and add custom modules for Caddy.</p>
                </div>
            </button>
            <button type="button" class="settings-home-button" id="custom-config"
                onclick="window.location.href = '/settings/custom-config/';">
                <div>
                    <span class="material-symbols-rounded">manufacturing</span>
                    <h2>Edit Config</h2>
                    <p>Edit the config file for Caddy.</p>
                </div>
            </button>
            <script src="./script.js"></script>
        </div>
    </div>
</body>

</html>