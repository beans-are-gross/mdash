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
        <form method="post" action="delete.api.php">
            <h1>Delete an App</h1>

            <div class="center">
                <?php
                $accountIdEncrypted = encryptData($accountInfo[0]);
                $id = strip_tags($_GET["id"]);
                $idDecrypted = decryptData($id);

                //pull the app info
                $sql = "SELECT `name`, `icon`, `owner` FROM `apps` WHERE `id` = ?;";
                $stmt = mysqli_stmt_init($dbConn);
                mysqli_stmt_prepare($stmt, $sql);
                mysqli_stmt_bind_param($stmt, "s", $idDecrypted);
                $appExecute = mysqli_stmt_execute($stmt);

                if (!$appExecute) {
                    die("Failed to execute app query: " . mysqli_error($dbConn));
                }

                mysqli_stmt_bind_result($stmt, $name, $icon, $owner);
                mysqli_stmt_fetch($stmt);

                //check if the app exists
                if (is_null($owner)) {
                    echo "<p class='secondary'>This app does not exist.</p>";
                    exit;
                }

                //check if the user is the owner
                if (decryptData($owner) !== $accountInfo[0]) {
                    echo "<p class='secondary'>Only the owner can delete this app.</p>";
                    exit;
                }

                //display the app as it would be on the dashboard
                $name = decryptData($name);
                $icon = decryptData($icon);

                echo "<div class='app'>";

                echo "<img src='https://cdn.simpleicons.org/$icon'>";
                echo "<h2>$name</h2>";

                echo "</div>";
                ?>
            </div>
            <input type="hidden" name="id" value="<?php echo $id; ?>">

            <p>Are you sure you want to delete this app? This action cannot be undone.</p>
            <div class="center">
                <button type="submit">Yes</button>
                <button type="button" onclick="window.location.href = '../edit/?id=<?php echo $id; ?>';">No</button>
            </div>
        </form>
    </div>
</body>

</html>