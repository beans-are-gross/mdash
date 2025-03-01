<?php
function decryptData($data)
{
    //to help reduce the total number of lines
    $config = json_decode(file_get_contents("/mdash/config.json"), true);
    $encryptionInfo = $config["encryption"];

    return openssl_decrypt(
        $data,
        $encryptionInfo["cipher"],
        $encryptionInfo["key"],
        $encryptionInfo["options"],
        $encryptionInfo["iv"]
    );
}

function encryptData($data)
{
    //to help reduce the total number of lines
    $config = json_decode(file_get_contents("/mdash/config.json"), true);
    $encryptionInfo = $config["encryption"];

    return openssl_encrypt(
        $data,
        $encryptionInfo["cipher"],
        $encryptionInfo["key"],
        $encryptionInfo["options"],
        $encryptionInfo["iv"]
    );
}

$accountInfo = [];

// login security
function verifyLogin($token)
{
    if (!empty($token)) {
        //connect to the database
        $config = json_decode(file_get_contents("/mdash/config.json"), true);
        $dbInfo = $config["dbData"];

        $dbHost = $dbInfo["dbHost"];
        $dbUser = $dbInfo["dbUser"];
        $dbPass = decryptData($dbInfo["dbPass"]);
        $dbDatabase = $dbInfo["dbDatabase"];

        $dbConn = mysqli_connect($dbHost, $dbUser, $dbPass, $dbDatabase);

        if (!$dbConn) {
            die("Failed to connect to the database: " . mysqli_connect_error());
        }
        unset($config);

        //check the token cookie
        $tokenHashed = encryptData($token);
        $sql = "SELECT * FROM tokens WHERE id = ?;";
        $stmt = mysqli_stmt_init($dbConn);
        mysqli_stmt_prepare($stmt, $sql);
        mysqli_stmt_bind_param($stmt, "s", $tokenHashed);
        $tokenQuery = mysqli_stmt_execute($stmt);

        if (!$tokenQuery) {
            die("Failed to verify your token.");
        }

        mysqli_stmt_bind_result($stmt, $idSql, $accountIdSql, $ipSql, $expiresSql);

        if (mysqli_stmt_fetch($stmt)) {
            //check if the token is expired
            $expiresSql = decryptData($expiresSql);

            if (!(time() > $expiresSql)) {
                //check if the token equals the database (double check)
                $tokenSql = decryptData($idSql);

                if ($token === $tokenSql) {
                    //check if the ip matches
                    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
                        $ip = $_SERVER['HTTP_CLIENT_IP'];
                    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
                    } else {
                        $ip = $_SERVER['REMOTE_ADDR'];
                    }

                    $ipSql = decryptData($ipSql);

                    if ($ip === $ipSql) {
                        //if all pass, then pull the users nickname
                        $accountId = decryptData($accountIdSql);

                        mysqli_stmt_close($stmt);
                        $sql = "SELECT nickname FROM accounts WHERE id = ?;";
                        $stmt = mysqli_stmt_init($dbConn);
                        mysqli_stmt_prepare($stmt, $sql);
                        mysqli_stmt_bind_param($stmt, "s", $accountId);
                        $userQuery = mysqli_stmt_execute($stmt);

                        if (!$userQuery) {
                            die("Failed to execute user query.");
                        }

                        mysqli_stmt_bind_result($stmt, $accountNickname);

                        if (!mysqli_stmt_fetch($stmt)) {
                            //the user was not found
                            mysqli_stmt_close($stmt);
                            mysqli_close($dbConn);

                            return "no-user";
                        }

                        mysqli_stmt_close($stmt);
                        mysqli_close($dbConn);

                        return [$accountId, $accountNickname];
                    } else {
                        //the ip was bad
                        return "bad-ip";
                    }

                } else {
                    //the token was bad
                    return "bad-token";
                }
            } else {
                //the token was expired
                return "token-expired";
            }
        } else {
            //the token was not found in the database
            return "token-404";
        }
    } else {
        //the token cookie was not set
        return "token-400";
    }
}

function verifyAdmin($id)
{
    //connect to the database
    $config = json_decode(file_get_contents("/mdash/config.json"), true);
    $dbInfo = $config["dbData"];

    $dbHost = $dbInfo["dbHost"];
    $dbUser = $dbInfo["dbUser"];
    $dbPass = decryptData($dbInfo["dbPass"]);
    $dbDatabase = $dbInfo["dbDatabase"];

    $dbConn = mysqli_connect($dbHost, $dbUser, $dbPass, $dbDatabase);

    if (!$dbConn) {
        die("Failed to connect to the database: " . mysqli_connect_error());
    }
    unset($config);

    //check if the user is an admin
    $sql = "SELECT `admin` FROM `accounts` WHERE `id` = ?;";
    $stmt = mysqli_stmt_init($dbConn);
    mysqli_stmt_prepare($stmt, $sql);
    mysqli_stmt_bind_param($stmt, "s", $id);
    $checkExecute = mysqli_stmt_execute($stmt);

    if (!$checkExecute) {
        die("Failed to execute the SQL statement: " . mysqli_stmt_error($stmt));
    }

    mysqli_stmt_bind_result($stmt, $admin);
    mysqli_stmt_fetch($stmt);

    $admin = decryptData($admin);

    mysqli_stmt_close($stmt);
    mysqli_close($dbConn);

    return $admin;
}