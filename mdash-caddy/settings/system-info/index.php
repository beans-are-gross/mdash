<?php
require_once "/var/www/mdash/header.php";
?>

<body>
    <?php
    require_once "/var/www/mdash/settings/header.php";
    ?>
    <link rel="stylesheet" href="./style.css">
    <div class="darken">
        <div class="system-info-widget">
            <?php
            $bytesToGb = 1000000000;

            $totalSpace = round(disk_total_space("/") / $bytesToGb, 2);
            $freeSpace = round(disk_free_space("/") / $bytesToGb, 2);
            $usedSpace = round($totalSpace - $freeSpace, 2);
            ?>
            <span class="material-symbols-rounded right-special">hard_drive</span>
            <p class="secondary right-special">Storage</p>
            <h2 class="right"><?php echo $freeSpace ?> GB</h2>
            <p class="secondary right">Free</p>
            <progress id="storage" value="<?php echo $usedSpace ?>" max="<?php echo $totalSpace ?>"></progress>
            <div>
                <p class="secondary"><?php echo $usedSpace ?> GB </p>
                <p class="secondary right"><?php echo $totalSpace ?> GB</p>
            </div>
        </div>

        <div class="system-info-widget">
            <?php
            $memory = explode("\n", shell_exec("free -m"))[1];
            $memory = array_values(array_filter(explode(" ", $memory)));
            $totalMemory = $memory[1];
            $usedMemory = $memory[2];
            $freeMemory = $memory[3];
            $availableMemory = $memory[6];
            ?>
            <span class="material-symbols-rounded right-special">memory</span>
            <p class="secondary right-special">Memory</p>
            <h2 class="right"><?php echo $availableMemory ?> MB</h2>
            <p class="secondary right">Available</p>
            <progress id="storage" value="<?php echo $usedMemory ?>" max="<?php echo $totalMemory ?>"></progress>
            <div>
                <p class="secondary"><?php echo $usedMemory ?> MB </p>
                <p class="secondary right"><?php echo $totalMemory ?> MB</p>
            </div>
        </div>

        <div class="system-info-widget">
            <span class="material-symbols-rounded right-special">schedule</span>
            <p class="secondary right-special">Server Time</p>
            <p class="secondary"><?php echo date_default_timezone_get(); ?></p>
            <h2><?php echo date("G:i:s") ?></h2>
            <p class="secondary"><?php echo date("M d, o") ?></p>
        </div>

        <div class="system-info-widget">
            <?php
            $upSince = date("M d, o", strtotime(shell_exec("uptime -s")));
            $uptime = str_replace("up ", "", shell_exec("uptime -p"));
            ?>
            <span class="material-symbols-rounded right-special">power</span>
            <p class="secondary right-special">Up Since</p>
            <h2><?php echo $upSince ?></h2>
            <p class="secondary"><?php echo $uptime ?></p>
        </div>

        <div class="system-info-widget">
            <?php
            $ipAddr = shell_exec("hostname -i");
            $hostname = shell_exec("hostname");
            ?>
            <span class="material-symbols-rounded right-special">lan</span>
            <p class="secondary right-special">Network</p>
            <h2><?php echo $ipAddr ?></h2>
            <p class="secondary"><?php echo $hostname ?></p>
        </div>
    </div>
</body>