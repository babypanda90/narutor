<?php
require "phpmods/functions.php";
require "phpmods/dbconnect.php";
require "phpmods/accessauth.php";

$validName = "/^.{1,}$/"; //"/^[\x{4e00}-\x{9fa5}a-zA-Z0-9]{1,12}$/u"; // 游戏名必须为1到12位汉字或字母数字

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if ($_POST['submit'] == "改名") {
        if (preg_match($validName,$_POST['username'])) {
            mysqli_query($mydb, "update users set username = '$_POST[username]' where userid = '$_POST[userid]'");
            mysqli_query($mydb, "update scores set username = '$_POST[username]' where userid = '$_POST[userid]'");
            if ($_POST['userid'] == $checkerid) {
                mysqli_query($mydb, "update clubs set clubchecker = '$_POST[username]' where clubid = '$clubid'");
                $username = $clubchecker = $_POST['username'];
                setcookie("username", "", time()-3600, "/", "", FALSE, TRUE); // 由于使用 httponly, 先删除cookie username
                setcookie("username", $username, time()+7200, "/", "", FALSE, TRUE); //再建立cookie username, 
            }
        }
    }

    if ($_POST['submit'] == "加入") {
        if (preg_match($validName,$_POST['addusername'])) {
            $isaddusername = mysqli_query($mydb, "insert into users (username, usercode, clubid) values ('$_POST[addusername]', '0000', '$clubid')");
            if (!$isaddusername) {
                echo "<script>alert('成员名称已存在!');window.location.href='$_SERVER[PHP_SELF]';</script>";
            }
            else {
                $res = mysqli_query($mydb, "select userid from users where username = '$_POST[addusername]' and clubid = '$clubid'");
                $row = mysqli_fetch_row($res);

                mysqli_query($mydb, "insert into 
                scores (clubid, userid, bonusdate, username, restscore, weekdonation, warnumber, totalscore, bonus, reducescore, endscore) 
                values ('$clubid', '$row[0]', '$bonusdate', '$_POST[addusername]', '0', '0', '0', '0', '', '0', '0')");
            }
        }
    }

    if ($_POST['submit'] == "清空") {
        mysqli_query($mydb, "delete from scores where userid = '$_POST[userid]'");
        mysqli_query($mydb, "insert into 
        scores (clubid, userid, bonusdate, username, restscore, weekdonation, warnumber, totalscore, bonus, reducescore, endscore) 
        values ('$clubid', '$_POST[userid]', '$bonusdate', '$_POST[username]', '0', '0', '0', '0', '', '0', '0')");
    }

    if ($_POST['submit'] == "删除") {
        mysqli_query($mydb, "delete from users where userid = '$_POST[userid]'");
        mysqli_query($mydb, "delete from scores where userid = '$_POST[userid]'");
        if ($_POST['userid'] == $checkerid) {
                mysqli_query($mydb, "update clubs set clubchecker = '' where clubid = '$clubid'");
                header("location:login.php");
        }
    }
}


?>

<!DOCTYPE HTML>
<html>
<head>
<title>成员管理</title>
<style>

th, td {
    background: linear-gradient(to bottom right, #d7bb97, #8f7657); /* http://www.color-hex.com/ */
    border: 1px solid black;
    border-collapse: collapse;
    padding: 5px;
    box-shadow: 5px 5px 5px #888888;
}
th {
    text-align: left;
    font-size: 15px;
}
td > input {
    width:5em;
}
td > input[type='button'] {
    width:5em;
}
.username-case {
    width:12em;
}
input:hover {
   background:lightskyblue;
}
.tab-cap {
    height:20px;
    width:auto;
    background: linear-gradient(to bottom right, #cb99ff, #8559b2);
    border-radius:5px;
    box-shadow: 5px 5px 5px #888888;
}

</style>

<script type="text/javascript">
function emptyUserAlert() {
    return confirm("确定清空该成员所有数据吗?");
}

function removeUserAlert() {
    return confirm("确定删除该成员及其所有数据吗?");
}

</script>

</head>

<body>
<?php
require "headinfo.php";
require "headbar.php";
?>

<div align=center>
    <table>
        <caption><b class=tab-cap>成员管理界面</b></caption>
        <tr>
            <th>序号</th>
            <th>成员名称</th>
            <th>修改</th>
            <th>清空数据</th>
            <th>彻底删除</th>
        </tr>
<?php
$res = mysqli_query($mydb, "select * from users where clubid = '$clubid'");
$num = 0;
while($values = mysqli_fetch_array($res)) {
    $num++;
?>
        <tr>
            <td><?php echo $num;?></td>
            <form method='post' action=<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>>
            <input type='hidden' name='userid' readonly value=<?php echo $values['userid'];?>>
            <td><input type='text' name='username' value=<?php echo $values['username'];?> class='username-case'></td>
            <td><input type='submit' name='submit' value='改名'></td>
            </form>
            <td>
                <form method='post' action=<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?> onsubmit='return emptyUserAlert();'>
                    <input type='hidden' name='userid' readonly value=<?php echo $values['userid'];?>>
                    <input type='hidden' name='username' readonly value=<?php echo $values['username'];?>>
                    <input type='submit' name='submit' value='清空'>
                </form>
            </td>
            <td>
                <form method='post' action=<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?> onsubmit='return removeUserAlert();'>
                    <input type='hidden' name='userid' readonly value=<?php echo $values['userid'];?>>
                    <input type='hidden' name='username' readonly value=<?php echo $values['username'];?>>
                    <input type='submit' name='submit' value='删除'>
                </form>
            </td>
        </tr>
<?php
}
?>
        <tr>
            <td></td>
            <form method='post' action=<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>>
            <td><input type='text' name='addusername' value='' class='username-case'></td>
            <td><input type='submit' name='submit' value='加入'></td>
            </form>
            <td></td>
            <td></td>
        </tr>
    </table>
    <br>

<div>
<br><br>
</body>

<?php
require "footinfo.php";
?>
</html>