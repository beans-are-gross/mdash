<?php
$dbConnRequired = true;
require "/var/www/mdash/header.php";

$accountId = $accountInfo[0];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <link rel="stylesheet" href="../style.css">
</head>

<body>
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

                $id = strip_tags($_GET["id"]);
                $idDecrypted = decryptData($id);

                //pull the user information
                $sql = "SELECT `nickname`, `username`, `admin` FROM `accounts` WHERE `id`=?;";
                $stmt = mysqli_stmt_init($dbConn);
                mysqli_stmt_prepare($stmt, $sql);
                mysqli_stmt_bind_param($stmt, "s", $idDecrypted);
                $userExecute = mysqli_stmt_execute($stmt);

                if (!$userExecute) {
                    die("Failed to execute user query: " . mysqli_error($dbConn));
                }

                mysqli_stmt_bind_result($stmt, $nickname, $username, $admin);
                mysqli_stmt_fetch($stmt);
                mysqli_stmt_close($stmt);

                $nickname = decryptData($nickname);
                $username = decryptData($username);
                $admin = decryptData($admin) ? "checked" : "";
                ?>
                <h1>Edit a User</h1>

                <input type="hidden" id="id" value="<?php echo $id; ?>">

                <div class="form-field" id="nickname-field">
                    <p class="form-secondary">Nickname</p>
                    <input type="text" id="nickname" placeholder="Nickname" value="<?php echo $nickname; ?>">
                </div>

                <div class="form-field-double" id="username-field">
                    <p class="form-secondary">Username</p>
                    <input type="text" id="username" placeholder="Username" value="<?php echo $username; ?>">
                    <div class="splitter" style="width: 80%;"></div>
                    <div class="center" style="height: 30px;">
                        <input type="checkbox" id="admin" <?php echo $admin; ?>>
                        <label for="admin">Admin</label>
                    </div>
                </div>

                <p class="secondary">You do not need to fill the password fields out if you do not want to give the user
                    a new one.</p>

                <div class="form-field" id="password-field">
                    <input type="password" class="input-small" id="password" placeholder="New password">
                    <button type="button" class="input-small-icon" id="password-reveal">
                        <span class="material-symbols-rounded">
                            visibility
                        </span>
                    </button>
                </div>

                <div class="form-field" id="password-verify-field">
                    <input type="password" class="input-small" id="password-verify" placeholder="Verify new password">
                    <button type="button" class="input-small-icon" id="password-verify-reveal">
                        <span class="material-symbols-rounded">
                            visibility
                        </span>
                    </button>
                </div>

                <div class="center">
                    <button type="submit">Edit User</button>
                    <button type="button" onclick="window.location.href = '../';">Cancel</button>
                </div>
            </form>
        </div>
    </div>
    <script src="./script.js"></script>
</body>

</html>