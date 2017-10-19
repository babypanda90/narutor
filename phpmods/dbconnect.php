<?php
ob_start();
header('Content-Type:text/html;charset=utf8');
$mysql_hostname = parse_ini_file("config.ini")["mysql_hostname"];
$mysql_username = parse_ini_file("config.ini")["mysql_username"];
$mysql_password = parse_ini_file("config.ini")["mysql_password"];
$mysql_dataname = parse_ini_file("config.ini")["mysql_dataname"];
$mydb = mysqli_connect($mysql_hostname, $mysql_username, $mysql_password, $mysql_dataname);
mysqli_query($mydb, "SET NAMES utf8;");
if(!$mydb) {
	header("location:install.php");
	exit;
}

?>