<?php
ini_set("display_errors", 1);
error_reporting(E_ALL);

header("Content-Type: application/json");

$config = json_decode(file_get_contents("/mdash/config.json"), true);
$encryptionInfo = $config["encryption"];
$dbInfo = $config["dbData"];

$dbHost = $dbInfo["dbHost"];
$dbUser = $dbInfo["dbUser"];
$dbPass = openssl_decrypt(
    $dbInfo["dbPass"],
    $encryptionInfo["cipher"],
    $encryptionInfo["key"],
    $encryptionInfo["options"],
    $encryptionInfo["iv"]
);
$dbDatabase = $dbInfo["dbDatabase"];

$dbConn = mysqli_connect($dbHost, $dbUser, $dbPass, $dbDatabase);

if (!$dbConn) {
    die("Failed to connect to the database: " . mysqli_connect_error());
}

$sql = "SELECT `int_url`, `int_url_ssl`, `ext_url` FROM apps;";
$stmt = mysqli_stmt_init($dbConn);
mysqli_stmt_prepare($stmt, $sql);
$appQuery = mysqli_stmt_execute($stmt);

if (!$appQuery) {
    die("Failed to execute app query.");
}

mysqli_stmt_bind_result($stmt, $intUrl, $intUrlSsl, $extUrl);

$caddyfile = ":8080 {\n" .
    "   root * /var/www/mdash/\n" .
    "   file_server\n" .
    "   php_fastcgi unix//run/php/php-fpm.sock\n" .
    "}\n\n";

while (mysqli_stmt_fetch($stmt)) {
    $intUrl = openssl_decrypt(
        $intUrl,
        $encryptionInfo["cipher"],
        $encryptionInfo["key"],
        $encryptionInfo["options"],
        $encryptionInfo["iv"]
    );

    $intUrlSsl = openssl_decrypt(
        $intUrlSsl,
        $encryptionInfo["cipher"],
        $encryptionInfo["key"],
        $encryptionInfo["options"],
        $encryptionInfo["iv"]
    );

    $extUrl = openssl_decrypt(
        $extUrl,
        $encryptionInfo["cipher"],
        $encryptionInfo["key"],
        $encryptionInfo["options"],
        $encryptionInfo["iv"]
    );

    if ($intUrlSsl) {
        $caddyfile .= "$extUrl {\n" .
            "   reverse_proxy $intUrl{\n" .
            "       transport http {\n".
            "           tls\n" .
            "           tls_insecure_skip_verify\n" .
            "       }\n".
            "   }\n" .
            "}\n\n";
    } else {
        $caddyfile .= "$extUrl {\n" .
            "   reverse_proxy $intUrl\n" .
            "}\n\n";
    }
}

file_put_contents("/etc/caddy/Caddyfile", $caddyfile);
shell_exec("cd /etc/caddy/ && caddy fmt --overwrite");
shell_exec('cd /etc/caddy/ && curl "http://localhost:2019/load" -H "Content-Type: text/caddyfile" --data-binary @Caddyfile');

mysqli_stmt_close($stmt);
mysqli_close($dbConn);

echo json_encode(["status" => "ok"]);