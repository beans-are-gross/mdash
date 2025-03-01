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
            <h1>Delete a Token</h1>

            <div class="center">
                <?php
                if (isset($_POST["id"])) {
                    $accountIdEncrypted = encryptData($accountInfo[0]);
                    $id = $_POST["id"];
                    $idDecrypted = decryptData($id);

                    if (!verifyAdmin($accountInfo[0])) {
                        echo "<p class='secondary'>You do not have access to this page.</p>";
                        echo "<div class='center'><button type='button' onclick='window.history.back();'>Go back</p></div>";
                        exit;
                    }

                    //pull the token information
                    $sql = "SELECT `account_id`, `expires` FROM `tokens` WHERE `id` = ?;";
                    $stmt = mysqli_stmt_init($dbConn);
                    mysqli_stmt_prepare($stmt, $sql);
                    mysqli_stmt_bind_param($stmt, "s", $idDecrypted);
                    $tokenExecute = mysqli_stmt_execute($stmt);

                    if (!$tokenExecute) {
                        die("Failed to execute token query: " . mysqli_error($dbConn));
                    }

                    mysqli_stmt_bind_result($stmt, $tokenAccountId, $expires);
                    mysqli_stmt_fetch($stmt);

                    if (is_null($tokenAccountId)) {
                        echo "<div>";
                        echo "<p class='secondary'>This token does not exist.</p>";
                        echo "<div class='center'><button type='button' onclick='window.history.back();'>Go back</p></div>";
                        echo "</div>";
                        exit;
                    }

                    $tokenAccountId = decryptData($tokenAccountId);
                    $expires = date("M j, o g:i:s a", decryptData($expires));

                    mysqli_stmt_close($stmt);

                    //pull the user information
                    $sql = "SELECT `nickname` FROM `accounts` WHERE `id` = ?;";
                    $stmt = mysqli_stmt_init($dbConn);
                    mysqli_stmt_prepare($stmt, $sql);
                    mysqli_stmt_bind_param($stmt, "s", $tokenAccountId);
                    $tokenExecute = mysqli_stmt_execute($stmt);

                    if (!$tokenExecute) {
                        die("Failed to execute token query: " . mysqli_error($dbConn));
                    }

                    mysqli_stmt_bind_result($stmt, $nickname);
                    mysqli_stmt_fetch($stmt);

                    $nickname = !empty($nickname) ? decryptData($nickname) : "Deleted User";

                    echo "<div class='token-info'>";
                    echo "<h2>$nickname</h2>";
                    echo "<p class='secondary'>$expires</p>";
                    echo "</div>";

                    mysqli_stmt_close($stmt);
                    mysqli_close($dbConn);
                }
                ?>
            </div>
            <input type="hidden" name="id" value="<?php echo $id; ?>">

            <p>Are you sure you want to delete this token? This action cannot be undone, and will immediately log out
                the user.</p>
            <div class="center">
                <button type="submit">Yes</button>
                <button type="button" onclick="window.location.href = '../';">No</button>
            </div>
        </form>
    </div>
</body>

</html>