<?php
$dbConnRequired = true;
require "/var/www/mdash/header.php";
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <link rel="stylesheet" href="./style.css">
</head>

<body>
    <div class="darken">
        <form method="post" action="delete.api.php">
            <h1>Delete a User</h1>

            <div class="center">
                <?php
                $accountIdEncrypted = encryptData($accountInfo[0]);
                $id = strip_tags($_GET["id"]);
                $idDecrypted = decryptData($id);

                if(!verifyAdmin($accountInfo[0])){
                    echo "<p class='secondary'>You do not have access to this page.</p>";
                    echo "<div class='center'><button type='button' onclick='window.history.back();'>Go back</p></div>";
                    exit;
                }

                //pull the app info
                $sql = "SELECT `nickname`, `username` FROM `accounts` WHERE `id` = ?;";
                $stmt = mysqli_stmt_init($dbConn);
                mysqli_stmt_prepare($stmt, $sql);
                mysqli_stmt_bind_param($stmt, "s", $idDecrypted);
                $userExecute = mysqli_stmt_execute($stmt);

                if (!$userExecute) {
                    die("Failed to execute app query: " . mysqli_error($dbConn));
                }

                mysqli_stmt_bind_result($stmt, $nickname, $username);
                mysqli_stmt_fetch($stmt);

                //check to see if the app exists, or if the user has access
                if (is_null($nickname)) {
                    echo "<p class='secondary'>This user does not exist.</p>";
                    exit;
                }

                //display the app as it would be on the dashboard
                $nickname = decryptData($nickname);
                $username = decryptData($username);

                echo "<div class='user-info'>";
                echo "<h2>$nickname</h2>";
                echo "<p class='secondary'>$username</p>";
                echo "</div>";
                ?>
            </div>
            <input type="hidden" name="id" value="<?php echo $id; ?>">

            <p>Are you sure you want to delete this user? This action cannot be undone.</p>
            <div class="center">
                <button type="submit">Yes</button>
                <button type="button" onclick="window.location.href = '../';">No</button>
            </div>
        </form>
    </div>
</body>

</html>