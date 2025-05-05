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
            <h1 style="margin: 0;">Tokens</h1>

            <table>
                <thead>
                    <tr>
                        <th>Nickname</th>
                        <th>Expires</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    //pull the user information
                    $sql = "SELECT `id`, `nickname` FROM `accounts`;";
                    $stmt = mysqli_stmt_init($dbConn);
                    mysqli_stmt_prepare($stmt, $sql);
                    $userExecute = mysqli_stmt_execute($stmt);

                    if (!$userExecute) {
                        die("Failed to execute user query: " . mysqli_error($dbConn));
                    }

                    mysqli_stmt_bind_result($stmt, $userId, $nickname);

                    $usersArray = [];
                    while (mysqli_stmt_fetch($stmt))
                        $usersArray[$userId] = $nickname;

                    //pull the token information
                    $sql = "SELECT `id`, `account_id`, `expires` FROM `tokens`;";
                    $stmt = mysqli_stmt_init($dbConn);
                    mysqli_stmt_prepare($stmt, $sql);
                    $tokenExecute = mysqli_stmt_execute($stmt);

                    if (!$tokenExecute) {
                        die("Failed to execute token query: " . mysqli_error($dbConn));
                    }

                    mysqli_stmt_bind_result($stmt, $tokenId, $tokenAccountId, $expires);

                    while (mysqli_stmt_fetch($stmt)) {
                        $currentToken = "";
                        if ($_COOKIE["mdash-token"] == decryptData($tokenId))
                            $currentToken = "(Current)";

                        $tokenAccountId = decryptData($tokenAccountId);
                        $nickname = isset($usersArray[$tokenAccountId]) ? decryptData($usersArray[$tokenAccountId]) : "Deleted User";
                        $expires = date("M j, o g:i:s a", decryptData($expires));

                        $tokenId = encryptData($tokenId);

                        //display the user information in a table format
                        echo "<tr>";
                        echo "<td>$nickname $currentToken</td>";
                        echo "<td>$expires</td>";
                        echo "<td>";
                        echo "<form style='width: max-content;' method='post' action='./delete/'>";
                        echo "<input type='hidden' name='id' value='$tokenId'>";
                        echo "<button type='submit' name='id' value='$tokenId'>Delete</button>";
                        echo "</form>";
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