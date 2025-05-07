<?php
require_once "/var/www/mdash/header.php";
?>

<body>
    <?php
    require_once "/var/www/mdash/settings/header.php";
    ?>
    <link rel="stylesheet" href="./style.css">
    <div class="darken" id="darken">
        <div id="system-info-grid">
            <div class="system-info-widget">
                <span class="material-symbols-rounded right-special">hard_drive</span>
                <p class="secondary right-special">Storage</p>
                <h2><span id="free-space"></span> GB</h2>
                <p class="secondary">Free</p>
                <progress id="storage"></progress>
                <div>
                    <p class="secondary"><span class="secondary" id="used-space"></span> GB</p>
                    <p class="secondary right"><span class="secondary" id="total-space"></span> GB</p>
                </div>
            </div>

            <div class="system-info-widget">
                <span class="material-symbols-rounded right-special">memory</span>
                <p class="secondary right-special">Memory</p>
                <h2><span id="available-memory"></span> MB</h2>
                <p class="secondary">Available</p>
                <progress id="memory"></progress>
                <div>
                    <p class="secondary"><span class="secondary" id="used-memory"></span> MB</p>
                    <p class="secondary right"><span class="secondary" id="total-memory"></span> MB</p>
                </div>
            </div>

            <div class="system-info-widget">
                <span class="material-symbols-rounded right-special">schedule</span>
                <p class="secondary right-special">Server Time</p>
                <p class="secondary" id="timezone"></p>
                <h2 id="server-time"></h2>
                <p class="secondary" id="server-date"></p>
            </div>

            <div class="system-info-widget">
                <span class="material-symbols-rounded right-special">power</span>
                <p class="secondary right-special">Up Since</p>
                <h2 id="up-since"></h2>
                <p class="secondary" id="uptime"></p>
            </div>

            <div class="system-info-widget">
                <span class="material-symbols-rounded right-special">lan</span>
                <p class="secondary right-special">Network</p>
                <h2 id="ip-addr"></h2>
                <p class="secondary" id="hostname"></p>
            </div>
        </div>
        <script src="./script.js"></script>
    </div>
</body>