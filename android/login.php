<?php
// 本机防火墙需要关闭, 否则局域网其他设备(手机)无法连接到本机数据库
ob_start();
if (!mysql_connect("sql312.cn.tn", "cntn_19669863", "ldc901017")) {
  echo "连接数据库失败<br>";
}
else {
    mysql_query("set names utf8;");
    echo "连接数据库成功<br>";
    if(!mysql_select_db("cntn_19669863_narutor")) {
        echo "进入数据库失败<br>";
    }
    else {
        echo "进入数据库成功<br>";
    }
}

header('Content-Type:text/html;charset=utf8');

$username = $password = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    echo "接收POST请求";
    if (verify_user($_POST["username"], $_POST["password"])){
      echo "用户登陆成功!";
    }
    else{
      $username = $_POST["username"];
      echo "游戏名或密码错误!";
    }
}

else {
    echo "暂无POST请求";
}

// 验证用户是否在数据库中 : param1 = username; param2 = usercode
function verify_user($param1, $param2) {
  $res = mysql_query("select username, usercode from users where username = '$param1' and usercode = '$param2'");
  return mysql_num_rows($res) > 0;
}

?>