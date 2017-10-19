<?php
header('Content-Type:text/html;charset=utf8');
$ini_path = php_ini_loaded_file();

function GetIP() {
    global $ip;

    if (getenv("HTTP_CLIENT_IP"))
        $ip = getenv("HTTP_CLIENT_IP");
    else if (getenv("HTTP_X_FORWARDED_FOR"))
        $ip = getenv("HTTP_X_FORWARDED_FOR");
    else if (getenv("REMOTE_ADDR"))
        $ip = getenv("REMOTE_ADDR");
    else
        $ip = "Unknown";

    return $ip;
}

?>