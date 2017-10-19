
<?php
require "phpmods/functions.php";
header('Content-Type:text/html;charset=utf8');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
	$mydb = mysqli_connect($_POST["server"], $_POST["username"], $_POST["password"], $_POST["database"]);
	if (!@$mydb) {
		echo "<script>alert('数据库连接失败！');</script>";
	}
    else {
		$dbconnect_file = file_get_contents("phpmods/dbconnect.php");
		$dbconnect_file = preg_replace("/mysql_connect\(\"(.+?)\", \"(.+?)\", \"(.+?)\"\)/is", "mysql_connect(\"".$_POST["server"]."\", \"".$_POST["username"]."\", \"".$_POST["password"]."\")", $dbconnect_file);
		$dbconnect_file = preg_replace("/mysql_select_db\(\"(.+?)\"\)/is", "mysql_select_db(\"".$_POST["database"]."\")", $dbconnect_file);
		$fp = @fopen("phpmods/dbconnect.php", "wb");
		if (@fwrite($fp, $dbconnect_file) && @fclose($fp)) {
			// 切分sql.txt中多个命令语句以便mysql_query逐一执行,因为mysql_query不支持执行多命令语句,可用mysqli_multi_query
			$sql_file = explode(";", file_get_contents("phpmods/sql.txt"), -1);
			mysqli_query($mydb, "drop table if exists scores;");
			mysqli_query($mydb, "drop table if exists users;");
			mysqli_query($mydb, "drop table if exists clubs;");
			$succes = true;
			foreach ($sql_file as $statement){
				if(!mysqli_query($mydb, $statement)){
					$succes = false;
					break;
				}
			}
			if ($succes) {
				echo "<script>alert('数据库安装成功！'); window.location.href='adminpanel.php';</script>";
			}
			else {
				echo "<script>alert('数据库安装失败！');</script>";
			}
		}
	}
}
?>

<!DOCTYPE HTML>
<html>
<head>
<title>安装应用</title>
<style>
.error {color: #FF0000;}
div.login {
	position: absolute;
	height: 260px;
	width: 550px;
	top: 30%;
	left: 50%;
	transform: translateX(-50%) translateY(-30%);
	border: 1px outset black;
	padding: 10px;
}
</style>
</head>

<body>
<?php
require "headinfo.php";
require "headbar.php";
?>
<div class=login>
<h2>安装应用</h2>
<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
<table>
	<tr><td>服务器：</td><td><input type="text" name="server" value="localhost"></td></tr>
	<tr><td>数据库：</td><td><input type="text" name="database" value=""></td></tr>
	<tr><td>DB账号：</td><td><input type="text" name="username" value=""></td></tr>
	<tr><td>DB密码：</td><td><input type="text" name="password" value=""></td></tr>
	<tr><td><br></td></tr>
	<tr><td colspan=2><input type="submit" name="submit" class="button button-glow button-rounded button-action" value="安装"></td></tr>
</table>
</form>
</div>
</body>

<?php
require "footinfo.php";
?>
</html>