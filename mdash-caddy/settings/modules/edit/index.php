<?php
$dbConnRequired = true;
require_once "/var/www/mdash/header.php";
?>

<body>
    <?php
    require_once "/var/www/mdash/settings/header.php";
    ?>
    <div id="info-container">
        <div>
            <div class="center">
                <span class='material-symbols-rounded' style="background-color: #ff964f;">restart_alt</span>
                <h3 style="padding-left: 10px;">Rebooting</h3>
            </div>
            <p class='secondary'>This will take a few minutes. <br>
                <span id='modules-log' class="log"></span>
                <br>
                <input type="checkbox" id="auto-scroll" checked>
                <label for="auto-scroll">Auto scroll</label>
            </p>
        </div>
    </div>
    <div class="darken">
        <div>
            <?php
            if (!verifyAdmin($accountInfo[0])) {
                echo "<h1>Error</h1>";
                echo "<p class='secondary'>You do not have access to this page.</p>";
                echo "<div class='center'><button type='button' onclick='window.history.back();'>Go back</p></div>";
                exit;
            }
            ?>
            <h1>Edit Modules</h1>
            <?php
            if ($docker) {
                echo "<p class='secondary'>Editing modules is not supported on Docker.</p>";
                exit;
            }
            ?>
            <p class="secondary">Do not put http:// or https:// in the link.</p>
            <form id="modules-form">
                <div id="module-fields">
                </div>
                <button type="button" id="add-link">Add Link</button>
                <script src="./script.js"></script>
                <?php
                //get the current modules
                $sql = "SELECT `url` FROM `modules`;";
                $stmt = mysqli_stmt_init($dbConn);
                mysqli_stmt_prepare($stmt, $sql);
                $moduleExecute = mysqli_stmt_execute($stmt);
                if (!$moduleExecute) {
                    die("Failed to execute module query: " . mysqli_error($dbConn));
                }
                mysqli_stmt_bind_result($stmt, $url);
                while (mysqli_stmt_fetch($stmt)) {
                    $url = decryptData($url);
                    echo "<script>addLink('$url')</script>";
                }
                ?>
                <div class="center">
                    <button type="submit" id="modules-form-submit">Update Modules</button>
                    <button type="button" onclick="window.location.href= '../';">Back </button>
                </div>
            </form>
        </div>
    </div>
</body>