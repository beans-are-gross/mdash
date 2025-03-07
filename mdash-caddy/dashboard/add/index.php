<?php
$dbConnRequired = true;
require_once "/var/www/mdash/header.php";
?>
<!DOCTYPE html>
<html lang="en">

<body>
    <div class="darken">
        <form method="post" id="add-form">
            <h1>Add an App</h1>

            <div class="form-field" id="name-field">
                <input type="text" id="name" placeholder="Name">
            </div>

            <p class="secondary">Leave internal URL blank if you only need a link.</p>

            <div class="form-field-double" id="int-url-field">
                <input type="text" id="int-url" placeholder="Internal URL">
                <div class="splitter"></div>
                <div class="center" style="height: 30px;">
                    <input type="checkbox" id="int-url-ssl">
                    <label for="int-url-ssl">Requires HTTPS</label>
                </div>
            </div>

            <div class="form-field" id="ext-url-field">
                <input type="text" id="ext-url" placeholder="External URL">
            </div>

            <div class="form-field" id="icon-field">
                <input type="text" class="input-small" id="icon" placeholder="Simple Icons name">
                <button type="button" class="input-small-icon"
                    onclick="window.open('https://simpleicons.org/', '_blank');">
                    <span class="material-symbols-rounded">
                        public
                    </span>
                </button>
            </div>

            <script src="./script.js"></script>

            <h2>Sharing</h2>

            <?php
            //insert the data into the database
            $sql = "SELECT `id`, `nickname` FROM `accounts` WHERE `id` != ?;";
            $stmt = mysqli_stmt_init($dbConn);
            mysqli_stmt_prepare($stmt, $sql);
            mysqli_stmt_bind_param($stmt, "i", $accountInfo[0]);
            $userQuery = mysqli_stmt_execute($stmt);

            if (!$userQuery) {
                die("Failed to get user information: " . mysqli_stmt_error($stmt));
            }

            mysqli_stmt_bind_result($stmt, $id, $nickname);

            while (mysqli_stmt_fetch($stmt)) {
                //decrypt info
                $nickname = decryptData($nickname);

                //turn it into a checkbox
                echo "<div class='center'>";
                echo "<p>$nickname</p>";
                echo "<select id='sharing-$id'>";
                echo "<option value='none'>None</option>";
                echo "<option value='view'>View</option>";
                echo "<option value='edit'>Edit</option>";
                echo "</select>";
                echo "<script>userIds.push($id);</script>";
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
                <button type="submit" id="add-form-submit">Add App</button>
                <button type="button" onclick="window.location.href = '/dashboard/';">Cancel</button>
            </div>
        </form>
    </div>
</body>

</html>