<?php
$dbConnRequired = true;
require_once "/var/www/mdash/header.php";

$accountId = $accountInfo[0];
$nickname = decryptData($accountInfo[1]);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./style.css">
</head>

<body>
    <!-- <div id="loader-container">
        <div class="loader"></div>
    </div> -->
    <div class="darken">
        <div>
            <div class="center">
                <h1 style="margin: 0;">Welcome <?php echo $nickname; ?></h1>
            </div>
            <div class="center" style="margin-bottom: 20px;">
                <button onclick="window.location.href = './add/';">Add App</button>
                <button onclick="window.location.href = '../settings/';">Settings</button>
            </div>
            <div id="app-grid" class="app-grid">
                <!-- Include the script here because the script needs 'app-grid' and the apps need the functions -->
                <script src="./script.js"></script>

                <?php
                //pull all of the apps
                $sql = "SELECT `id`, `name`, `ext_url`, `icon`, `sharing` FROM `apps`;";
                $stmt = mysqli_stmt_init($dbConn);
                mysqli_stmt_prepare($stmt, $sql);
                $appExecute = mysqli_stmt_execute($stmt);

                if (!$appExecute) {
                    die("Failed to execute app query: " . mysqli_error($dbConn));
                }

                mysqli_stmt_bind_result($stmt, $id, $name, $extUrl, $icon, $sharing);

                $userHasOneApp = false;

                //encrypt account id to search within sharing
                $accountIdEncrypted = encryptData($accountId);

                //display the apps in a grid layout
                while (mysqli_stmt_fetch($stmt)) {
                    $sharing = json_decode(decryptData($sharing), true);
                    if (!isset($sharing[$accountIdEncrypted]) || decryptData($sharing[$accountIdEncrypted]) === "none")
                        continue;
                    else
                        $userHasOneApp = true;

                    $name = decryptData($name);
                    $extUrl = decryptData($extUrl);
                    $icon = decryptData($icon);

                    echo "<div class='app' id='app-$id'>";

                    $idEncrypted = encryptData($id);

                    echo "<span class='material-symbols-rounded app-edit' id='app-edit-$id' onclick='window.location.href = `./edit/?id=$idEncrypted`;'>edit</span>";
                    echo "<img src='https://cdn.simpleicons.org/$icon'>";
                    echo "<h2>$name</h2>";

                    echo "<script>addAppFeatures($id, '$extUrl');</script>";

                    echo "</div>";
                }

                //check if they dont have any apps, if so, show a message
                if (!$userHasOneApp) {
                    //the </div> closes the app grid div
                    echo "</div><div class='center'><p class='secondary'>You don't have any apps.</p></div>";
                }

                //close the connection
                mysqli_stmt_close($stmt);
                mysqli_close($dbConn);
                ?>
            </div>
        </div>
    </div>
</body>

</html>