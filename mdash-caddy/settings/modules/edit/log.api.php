<?php
require_once "/var/www/mdash/header.php";

if (!verifyAdmin($accountInfo[0])) {
    return shell_exec("tail -n 1 /mdash/build-caddy.log");
} else {
    return "Access denied, you are not an admin.";
}
