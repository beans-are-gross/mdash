<?php
$noHtml = true;
require_once "/var/www/mdash/header.php";

if (verifyAdmin($accountInfo[0])) {
    echo nl2br(shell_exec("cat /mdash/build-caddy.log"));
} else {
    echo "You do not have access.";
}