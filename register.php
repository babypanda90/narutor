<?php
session_start();
require "phpmods/functions.php";
require "phpmods/dbconnect.php";
require "phpmods/accessauth.php";

$bonusdate = "0000-00-00";
$clubErr = $nameErr = $codeErr = $confCodeErr = $captchaErr = "";
$clubid = $name = $code = $confCode = "";
$validName = "/^.{1,}$/"; //"/^[\x{4e00}-\x{9fa5}a-zA-Z0-9]{1,12}$/u"; // 游戏名必须为1到12位汉字或字母数字
$validCode = "/^.{1,}$/"; //"/^[a-zA-Z0-9]{6,16}$/"; // 密码必须为6到16位字母数字

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  if ($_POST["submit"] == "提交") {
    if (empty($_POST["clubserver"]) || empty($_POST["clubname"])) {
      $clubErr = "必选";
    }
    else {
      $res = mysqli_query($mydb, "select clubid, bonusdate from clubs where clubserver = '$_POST[clubserver]' and clubname = '$_POST[clubname]'");
      $row = mysqli_fetch_row($res);
      $clubid = $row[0];
      $bonusdate = $row[1];
    }

    if (empty($_POST["name"])) {
      $nameErr = "必填!";
    }
    else {
      $name = test_input($_POST["name"]);
      if (!preg_match($validName,$name)) {
        $nameErr = "游戏名格式错误!";
      }
      else {
        $res = mysqli_query($mydb, "select username from users where username = '$name' and clubid = '$clubid'");
        if(mysqli_num_rows($res) == 1) {
          $nameErr = "游戏名已存在!";
        }
      }
    }

    if (empty($_POST["code"])) {
      $codeErr = "必填!";
    }
    else {
      $code = test_input($_POST["code"]);
      if (!preg_match($validCode,$code)) {
        $codeErr = "密码格式错误!";
      }
    }

    if (empty($_POST["confCode"])) {
      $confCodeErr = "必填!";
    }
    else {
      $confCode = test_input($_POST["confCode"]);
      if (!preg_match($validCode,$confCode)) {
        $confCodeErr = "密码格式错误!";
      }
      else {
        if ($code !== $confCode) {
          $confCodeErr = "两次输入密码不一致!";
        }
      }
    }

    // code for check server side validation
    if(empty($_SESSION['captcha_code'] ) || strcasecmp($_SESSION['captcha_code'], $_POST['captcha_code']) != 0){
      $captchaErr = "验证码输入错误!"; // Captcha verification is incorrect.
    }

    // 用户点击提交后如果信息符合要求则存入数据库并且跳转至登陆页面
    if ($clubErr == "" && $nameErr == "" && $codeErr == "" && $confCodeErr == "" && $captchaErr == "") {
      mysqli_query($mydb, "insert into users (username, usercode, clubid) values ('$name', '$code', '$clubid')");

      $res = mysqli_query($mydb, "select userid from users where username = '$name' and clubid = '$clubid'");
      $row = mysqli_fetch_row($res);
      $userid = $row[0];

      mysqli_query($mydb, "insert into 
      scores (clubid, userid, username, bonusdate, restscore, weekdonation, warnumber, totalscore, bonus, reducescore, endscore) 
      values ('$clubid', '$userid', '$name', '$bonusdate', '0', '0', '0', '0', '', '0', '0')");

      header("location:login.php");
    }

  }

}

// 调整用户输入信息的格式
function test_input($data) {
  $data = trim($data);
  $data = stripslashes($data);
  $data = htmlspecialchars($data);
  return $data;
}
?>

<!DOCTYPE HTML>
<html>
<head>
<title>组织成员注册</title>
<style>

.clubserver-case {
    width:13em;
}
.clubname-case {
    width:13em;
}

.error {color: #FF0000;}

div.register{
  position: absolute;
  height: 540px;
  width: 550px;
  left: 25%;
  transform: translateX(-25%);
  
  border: 1px outset black;
  padding: 20px;
  
}
</style>
<script type='text/javascript'>
function chooseClubServer(clubserver) {
  document.cookie = "clubserver=" + document.getElementsByName("clubserver")[0].value.toString();
  if (window.XMLHttpRequest) {
      // code for IE7+, Firefox, Chrome, Opera, Safari
      xmlhttp = new XMLHttpRequest();
  }
  else {
      // code for IE6, IE5
      xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
  }
  xmlhttp.onreadystatechange = function() {
      if (this.readyState == 4 && this.status == 200) {
          document.getElementsByName("clubname")[0].innerHTML = this.responseText;
      }
  };
  xmlhttp.open("GET","phpajax/getclubnamelist.php?cs="+clubserver, true);
  xmlhttp.send();
}

function chooseClubName(clubname) {
  document.cookie = "clubname=" + document.getElementsByName("clubname")[0].value.toString();
}

function refreshCaptcha() {
	var img = document.images['captchaimg'];
	img.src = img.src.substring(0,img.src.lastIndexOf("?"))+"?rand="+Math.random()*1000;
}
</script>
</head>

<body>
<?php
require "headinfo.php";
require "headbar.php";
?>
<div class=register>
<h2>组织成员注册</h2>
<form style="display: inline;" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">

  <label for='message'>组织服务器 :</label><br>
  <select name="clubserver" onchange="chooseClubServer(this.value)" class="clubserver-case">
      <option value=''>选择区服</option>
      <?php 
      $res = mysqli_query($mydb, "select distinct(clubserver) from clubs");
      while($values = mysqli_fetch_array($res)) {
          if (isset($_COOKIE["clubserver"])) {
      ?>
      <option value=<?php echo $values['clubserver']?> <?php echo currClubServer($values['clubserver'], $_COOKIE["clubserver"]);?>><?php echo $values['clubserver'];?></option>
      <?php
          }
          else {
      ?>
      <option value=<?php echo $values['clubserver']?>><?php echo $values['clubserver']?></option>
      <?php
          }
      }

      function currClubServer($param1, $param2){
        if ($param1 == $param2){
            return 'selected';
        }
        return '';
      }
      ?>
  </select>
  <span class="error">* <?php echo $clubErr;?></span>
  <br><br>

  <label for='message'>组织名称 :</label><br>
  <select name="clubname" onchange='chooseClubName(this.value)' class="clubname-case">
      <option value=''>选择组织</option>
      <?php 
      if (isset($_COOKIE["clubserver"])) {
        $res = mysqli_query($mydb, "select * from clubs where clubserver = '$_COOKIE[clubserver]'");
        while($values = mysqli_fetch_array($res)) {
          if (isset($_COOKIE["clubname"])) {
        ?>
          <option value=<?php echo $values['clubname']?> <?php echo currClubName($values['clubname'], $_COOKIE["clubname"])?>><?php echo $values['clubname']?></option>
        <?php
          }
          else {
        ?>
          <option value=<?php echo $values['clubname']?>><?php echo $values['clubname']?></option>
        <?php
          }
        }
      }

      function currClubName($param1, $param2){
        if ($param1 == $param2){
            return 'selected';
        }
        return '';
      }
      ?>
  </select>
  <span class="error">* <?php echo $clubErr;?></span>
  <br><br>

  <label for='message'>游戏名 :</label><br>
  <input type="text" name="name" maxlength="12" value="<?php echo $name;?>">
  <span class="error">* <?php echo $nameErr;?></span>
  <br><br>

  <label for='message'>密码 :</label><br>
  <input type="password" name="code" maxlength="16" value="<?php echo $code;?>">
  <span class="error">* <?php echo $codeErr;?></span>
  <br><br>

  <label for='message'>重复密码 :</label><br>
  <input type="password" name="confCode" maxlength="16" value="<?php echo $confCode;?>">
  <span class="error">* <?php echo $confCodeErr;?></span>
  <br><br>

  <label for='message'>验证码 :</label><br>
  <input id="captcha_code" name="captcha_code" type="text">
  <span class="error">* <?php echo $captchaErr;?></span><br>
  <img src="phpcaptcha/captcha.php?rand=<?php echo rand();?>" id='captchaimg'>
  <a href='javascript: refreshCaptcha();'>刷新</a>
  <br><br>

  <input type="submit" name="submit" class="button button-glow button-rounded button-action" value="提交">
</form>

  <a href="login.php" class="button button-glow button-rounded button-action">返回</a>

</div>

</body>

<?php
require "footinfo.php";
?>
</html>