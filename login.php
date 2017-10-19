<?php
setcookie("username", "", time()-3600, "/", "", FALSE, TRUE);
setcookie("clubid", "", time()-3600, "/", "", FALSE, TRUE);
require "phpmods/functions.php";
require "phpmods/dbconnect.php";
require "phpmods/accessauth.php";

$Err = "";
$clubid = $username = $password = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 验证用户是否在数据库中
    $res = mysqli_query($mydb, "select clubid from clubs where clubserver = '$_POST[clubserver]' and clubname = '$_POST[clubname]'");
    $row = mysqli_fetch_row($res);
    $clubid = $row[0];

    $res = mysqli_query($mydb, "select username, usercode from users where username = '$_POST[username]' and usercode = '$_POST[password]' and clubid = '$clubid'");
    if (mysqli_num_rows($res) > 0){
      setcookie("username", $_POST["username"], time()+7200, "/", "", FALSE, TRUE);
      setcookie("clubid", $clubid, time()+7200, "/", "", FALSE, TRUE);
      header("location:userhome.php");
    }
    else{
      $username = $_POST["username"];
      $Err = "游戏名或密码错误!";
    }
}

?>

<!DOCTYPE HTML>
<html>
<head>
<title>组织成员登陆</title>
<style>
.clubserver-case {
    width:13em;
}
.clubname-case {
    width:13em;
}

.error {color: #FF0000;}

div.login{
  position: absolute;
  height: 360px;
  width: 550px;
  left: 25%;
  transform: translateX(-25%);
  
  border: 1px outset black;
  padding: 20px;
  
}
</style>
<script>
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
</script>
</head>

<body>
<?php
require "headinfo.php";
require "headbar.php";
?>
<div class=login>
<h2>组织成员登陆</h2>
<p></p>
<form style="display: inline;" method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']);?>">
  <label for='message'>组织服务器 :</label><br>
  <select name="clubserver" onchange="chooseClubServer(this.value)" class="clubserver-case">
      <option value=''>选择区服</option>
      <?php 
      $res = mysqli_query($mydb, "select distinct(clubserver) from clubs");
      while($values = mysqli_fetch_array($res)) {
        if (isset($_COOKIE["clubserver"])) {
      ?>
        <option value=<?php echo $values['clubserver']?> <?php echo currClubServer($values['clubserver'], $_COOKIE["clubserver"])?>><?php echo $values['clubserver']?></option>
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
  <span class="error">*</span>
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
  <span class="error">*</span>
  <br><br>

  <label for='message'>游戏名 :</label><br>
  <input type="text" name="username" maxlength="16" value="<?php echo $username;?>">
  <span class="error">* <?php echo $Err;?></span>
  <br><br>

  <label for='message'>密码 :</label><br>
  <input type="password" name="password" maxlength="16" value="<?php echo $password;?>">
  <span class="error">* <?php echo $Err;?></span>
  <br><br>

  <input type="submit" name="submit" class="button button-glow button-rounded button-action" value="登陆">
</form>

<a href="register.php" class="button button-glow button-rounded button-action">注册</a>
<br><br>

</div>

</body>

<?php
require "footinfo.php";
?>

</html>