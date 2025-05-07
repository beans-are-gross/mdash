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
                <span class='material-symbols-rounded' style="background-color: #ff6961;">settings_alert</span>
                <h3 style="padding-left: 10px;">Config Error</h3>
            </div>
            <p class='secondary'>Caddy reported an error with your custom config. <br>
                <span id='custom-config-log' class="log"></span>
            </p>
            <div class="center">
                <button type="button" onclick="closeInfoPopup();">Close</button>
            </div>
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

            <link rel="stylesheet" href="./style.css">

            <h1>Custom Config</h1>
            <p class="secondary">Please make sure to use the proper formatting. <br> <a
                    href="https://caddyserver.com/docs/caddyfile/concepts" target="_blank">How to use a Caddyfile</a>
            </p>

            <form id="config-form">
                <textarea id="custom-config-textarea" rows="5"><?php
                if (file_exists("/mdash/custom.caddyfile")) {
                    echo strip_tags(file_get_contents("/mdash/custom.caddyfile"));
                }
                ?></textarea>
                <button type="submit">Submit</button>
            </form>

            <script src="./script.js"></script>
        </div>
    </div>
</body>