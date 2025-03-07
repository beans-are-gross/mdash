<?php
$dbConnRequired = true;
require "/var/www/mdash/header.php";
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <link rel="stylesheet" href="../style.css">
</head>

<body>
    <div class="darken">
        <form method="post" id="edit-form">
            <h1>Edit an App</h1>

            <?php
            $accountIdEncrypted = encryptData($accountInfo[0]);
            $id = strip_tags($_GET["id"]);
            $idDecrypted = decryptData($id);

            //pull the app info
            $sql = "SELECT `name`, `int_url`, `int_url_ssl`, `ext_url`, `icon`, `sharing`, `owner` FROM `apps` WHERE `id` = ?;";
            $stmt = mysqli_stmt_init($dbConn);
            mysqli_stmt_prepare($stmt, $sql);
            mysqli_stmt_bind_param($stmt, "s", $idDecrypted);
            $appExecute = mysqli_stmt_execute($stmt);

            if (!$appExecute) {
                die("Failed to execute app query: " . mysqli_error($dbConn));
            }

            mysqli_stmt_bind_result($stmt, $name, $intUrl, $intUrlSsl, $extUrl, $icon, $sharing, $owner);
            mysqli_stmt_fetch($stmt);

            //check if the app exists
            if (is_null($sharing)) {
                echo "<p class='secondary'>This app does not exist.</p>";
                exit;
            }

            //check if the user has edit access
            $sharing = json_decode(decryptData($sharing), true);
            if (decryptData($sharing[$accountIdEncrypted]) !== "edit") {
                echo "<p class='secondary'>You don't have access to edit this app.</p>";
                exit;
            }

            //display the app as it would be on the dashboard
            $name = decryptData($name);
            $intUrl = decryptData($intUrl);
            $intUrl = $intUrl == "---" ? "" : $intUrl;
            $intUrlSsl = decryptData($intUrlSsl) ? "checked" : "";
            $extUrl = decryptData($extUrl);
            $icon = decryptData($icon);

            echo "<div class='app'>";

            echo "<img src='https://cdn.simpleicons.org/$icon'>";
            echo "<h2>$name</h2>";

            echo "</div>";

            mysqli_stmt_close($stmt);
            ?>

            <div class="form-field" id="name-field">
                <p class="form-secondary">Name</p>
                <input type="text" id="name" placeholder="Name" value="<?php echo $name; ?>">
            </div>

            <p class="secondary">Leave internal URL blank if you only need a link.</p>

            <div class="form-field-double" id="int-url-field">
                <p class="form-secondary">Internal URL</p>
                <input type="text" id="int-url" placeholder="Internal URL" value="<?php echo $intUrl; ?>">
                <div class="splitter" style="width: 75%;"></div>
                <div class="center" style="height: 30px;">
                    <input type="checkbox" id="int-url-ssl" <?php echo $intUrlSsl; ?>>
                    <label for="int-url-ssl">Requires HTTPS</label>
                </div>
            </div>

            <div class="form-field" id="ext-url-field">
                <p class="form-secondary">External URL</p>
                <input type="text" id="ext-url" placeholder="External URL" value="<?php echo $extUrl; ?>">
            </div>

            <div class="form-field" id="icon-field">
                <input type="text" class="input-small" id="icon" placeholder="Simple Icons name"
                    value="<?php echo $icon; ?>">
                <button type="button" class="input-small-icon"
                    onclick="window.open('https://simpleicons.org/', '_blank');">
                    <span class="material-symbols-rounded">
                        public
                    </span>
                </button>
            </div>

            <input type="hidden" id="id" value="<?php echo $id; ?>">

            <script src="./script.js"></script>

            <h2>Sharing</h2>

            <?php
            $ownerDecrypted = decryptData($owner);

            //select all users that are not the current one, or the owner
            $sql = "SELECT `id`, `nickname` FROM `accounts` WHERE `id` != ? AND `id` != ?;";
            $stmt = mysqli_stmt_init($dbConn);
            mysqli_stmt_prepare($stmt, $sql);
            mysqli_stmt_bind_param($stmt, "ss", $accountInfo[0], $ownerDecrypted);
            $userQuery = mysqli_stmt_execute($stmt);

            if (!$userQuery) {
                die("Failed to get user information: " . mysqli_stmt_error($stmt));
            }

            mysqli_stmt_bind_result($stmt, $userId, $nickname);

            while (mysqli_stmt_fetch($stmt)) {
                //decrypt info
                $nickname = decryptData($nickname);

                //turn it into a checkbox
                echo "<div class='center'>";
                echo "<p>$nickname</p>";
                echo "<select id='sharing-$userId'>";

                if (isset($sharing[encryptData($userId)])) {
                    $selected = decryptData($sharing[encryptData($userId)]);
                    echo "<option value='$selected' selected>" . ucwords($selected) . "</option>";
                    echo "<option disabled></option>";
                }

                echo "<option value='none'>None</option>";
                echo "<option value='view'>View</option>";
                echo "<option value='edit'>Edit</option>";
                echo "</select>";
                echo "<script>userIds.push($userId);</script>";
                echo "</div>";
            }

            if (mysqli_stmt_num_rows($stmt) == 0) {
                echo "<p class='secondary'>There are no other users to share this app with.</p>";
            }

            //close the connection
            mysqli_stmt_close($stmt);
            mysqli_close($dbConn);
            ?>

            <div class="center">
                <button type="submit" id="edit-form-submit">Update App</button>
                <button type="button" onclick="window.location.href = '/dashboard/';">Cancel</button>
            </div>

            <button onclick="window.location.href = '../delete/?id=<?php echo $id; ?>';">Delete App</button>

        </form>
    </div>
</body>

</html>