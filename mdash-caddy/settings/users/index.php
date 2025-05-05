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
</head>

<body>
    <?php
    require_once "/var/www/mdash/settings/header.php";
    ?>
    <div class="darken">
        <div>
            <?php
            if (!verifyAdmin($accountId)) {
                echo "<h1>Error</h1>";
                echo "<p class='secondary'>You do not have access to this page.</p>";
                echo "<div class='center'><button type='button' onclick='window.history.back();'>Go back</p></div>";
                exit;
            }
            ?>
            <h1 style="margin: 0;">Users</h1>
            <div class="center" style="margin-bottom: 20px;">
                <button type="button" onclick="window.location.href = './add';">Add a User</button>
            </div>

            <table>
                <thead>
                    <tr>
                        <th>Nickname</th>
                        <th>Username</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    //pull the user information
                    $sql = "SELECT `id`, `nickname`, `username` FROM `accounts`;";
                    $stmt = mysqli_stmt_init($dbConn);
                    mysqli_stmt_prepare($stmt, $sql);
                    $userExecute = mysqli_stmt_execute($stmt);

                    if (!$userExecute) {
                        die("Failed to execute user query: " . mysqli_error($dbConn));
                    }

                    mysqli_stmt_bind_result($stmt, $id, $nickname, $username);

                    while (mysqli_stmt_fetch($stmt)) {
                        $id = encryptData($id);
                        $nickname = decryptData($nickname);
                        $username = decryptData($username);

                        //display the user information in a table format
                        echo "<tr>";
                        echo "<td>$nickname</td>";
                        echo "<td>$username</td>";
                        echo "<td class='center'>";
                        echo "<button type='button' onclick='window.location.href = `./edit/?id=$id`;'>Edit</button>";
                        echo "<button type='button' onclick='window.location.href = `./delete/?id=$id`;'>Delete</button>";
                        echo "</td>";
                        echo "</tr>";
                    }

                    mysqli_stmt_close($stmt);
                    mysqli_close($dbConn);
                    ?>

                </tbody>
            </table>
        </div>
    </div>
</body>

</html>