<?php
setcookie("admin", "", time()-3600, "/", "", FALSE, TRUE);
require "phpmods/functions.php";

$admin = parse_ini_file("phpmods/config.ini")["admin"];
$pw = parse_ini_file("phpmods/config.ini")["password"];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if ($_POST['submit'] == "登陆") {
        if ($_POST['admin'] == $admin && $_POST['password'] == $pw) {
            setcookie("admin", "1", time()+3600, "/", "", FALSE, TRUE);
            header("location:adminpanel.php");
        }
    }
}

?>

<!DOCTYPE HTML>
<html>
<head>
<title>管理员登陆</title>
<style>
.error {color: #FF0000;}
div.login {
	position: absolute;
	height: 240px;
	width: 550px;
	top: 30%;
	left: 50%;
	transform: translateX(-50%) translateY(-30%);
	
	border: 1px outset black;
	padding: 20px;
}
</style>
</head>

<body>
<?php
require "headinfo.php";
require "headbar.php";
?>
<div class=login>
<h2>管理员登陆</h2>
<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">

  <label for='message'>admin账号: </label><br>
  <input type="text" name="admin" maxlength="12" value="">
  <br><br>

  <label for='message'>admin密码：</label><br>
  <input type="password" name="password" maxlength="16" value="">
  <br><br>

  <input type="submit" name="submit" class="button button-glow button-rounded button-action" value="登陆">
</form>
</div>
</body>

<?php
require "footinfo.php";
?>
</html>