<?php
$noHtml = true;
require_once "/var/www/mdash/header.php";

if (verifyAdmin($accountInfo[0])) {
    echo shell_exec("tail -n 1 /mdash/build-caddy.log");
} else {
    echo "You do not have access.";
}