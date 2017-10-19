<?php
require "phpmods/functions.php";
require "phpmods/dbconnect.php";
require "phpmods/accessauth.php";

$validName = "/^.{1,}$/"; //"/^[\x{4e00}-\x{9fa5}a-zA-Z0-9]{1,12}$/u"; // 组织名必须为1到12位汉字或字母数字

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if ($_POST['submit'] == "加入") {
        $isValidName = preg_match($validName,$_POST['addclubserver']) && preg_match($validName,$_POST['addclubname']);
        if ($isValidName) {
            $currdate = strtotime(date("Y-m-d"));
            $bonusdate = date("Y-m-d", strtotime("next Saturday", $currdate));
            if (!$_POST['addbonusvalue']) { $_POST['addbonusvalue'] = '0|0|0'; }

            $isaddusername = mysqli_query($mydb, "insert into clubs (clubserver, clubname, clubqq, clubchecker, bonusvalue, bonusdate) 
            values ('$_POST[addclubserver]', '$_POST[addclubname]', '$_POST[addclubqq]', '$_POST[addclubchecker]', '$_POST[addbonusvalue]', '$bonusdate')");

            if (!$isaddusername) {
                echo "<script>alert('组织名称已存在!'); window.location.href='adminpanel.php';</script>";
            }
        }
    }

    if ($_POST['submit'] == "提交") {
        $isValidName = preg_match($validName,$_POST['clubserver']) && preg_match($validName,$_POST['clubname']);
        if ($isValidName) {
            if (!$_POST['bonusvalue']) { $_POST['bonusvalue'] = '0|0|0'; }
            mysqli_query($mydb, "update clubs set clubserver = '$_POST[clubserver]' where clubid = '$_POST[clubid]'");
            mysqli_query($mydb, "update clubs set clubname = '$_POST[clubname]' where clubid = '$_POST[clubid]'");
            mysqli_query($mydb, "update clubs set clubqq = '$_POST[clubqq]' where clubid = '$_POST[clubid]'");
            mysqli_query($mydb, "update clubs set clubchecker = '$_POST[clubchecker]' where clubid = '$_POST[clubid]'");
            mysqli_query($mydb, "update clubs set bonusvalue = '$_POST[bonusvalue]' where clubid = '$_POST[clubid]'");
        }
    }

    if ($_POST['submit'] == "初始化") {
        mysqli_query($mydb, "delete from users where clubid = '$_POST[clubid]'");
        mysqli_query($mydb, "delete from scores where clubid = '$_POST[clubid]'");
        mysqli_query($mydb, "update clubs set clubchecker = '' where clubid = '$_POST[clubid]'");
    }

    if ($_POST['submit'] == "删除") {
        mysqli_query($mydb, "delete from clubs where clubid = '$_POST[clubid]'");
        mysqli_query($mydb, "delete from users where clubid = '$_POST[clubid]'");
        mysqli_query($mydb, "delete from scores where clubid = '$_POST[clubid]'");
    }
}

?>

<!DOCTYPE HTML>
<html>
<head>
<title>组织管理</title>
<style>

th, td {
    background: linear-gradient(to bottom right, #d7bb97, #8f7657);
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
.clubserver-case {
    width:8em;
}
.clubname-case {
    width:8em;
}
.clubqq-case {
    width:8em;
}
.clubchecker-case {
    width:8em;
}
.bonusvalue-case {
    width:6em;
}
.bonusdate-case {
    width:6em;
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
function initClubAlert() {
    return confirm("确定初始化该组织所有数据吗?");
}

function removeClubAlert() {
    return confirm("确定删除该组织及其所有数据吗?");
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
        <caption><b class=tab-cap>组织管理界面</b></caption>
        <tr>
            <th>序号</th>
            <th>组织服务器</th>
            <th>组织名称</th>
            <th>组织QQ</th>
            <th>组织统计员</th>
            <th>忍战包分值</th>
            <th>分包日期</th>
            <th>修改</th>
            <th>清空数据</th>
            <th>彻底删除</th>
        </tr>
<?php
$res = mysqli_query($mydb, "select * from clubs");
$num = 0;
while($values = mysqli_fetch_array($res)) {
    $num++;
?>
        <tr>
            <td><?php echo $num;?></td>
            <form method='post' action=<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>>
            <td><input type='text' name='clubserver' value=<?php echo $values['clubserver'];?> class='clubserver-case'></td>
            <td><input type='text' name='clubname' value=<?php echo $values['clubname'];?> class='clubname-case'></td>
            <td><input type='text' name='clubqq' value='<?php echo $values['clubqq'];?>' class='clubqq-case'></td>
            <td>
                <select name='clubchecker' class='clubchecker-case'>
                    <option value=''></option>
                    <?php
                    $res1 = mysqli_query($mydb, "select * from users where clubid = '$values[clubid]'");
                    while($values1 = mysqli_fetch_array($res1)) {
                    ?>
                    <option value=<?php echo $values1['username'];?> <?php echo currClubChecker($values1['username'], $values["clubchecker"]);?>><?php echo $values1['username'];?></option>
                    <?php
                    }
                    ?>
                </select>
            </td>
            <td><input type='text' name='bonusvalue' value='<?php echo $values['bonusvalue'];?>' class='bonusvalue-case'></td>
            <td><input type='text' name='bonusdate' readonly value='<?php echo $values['bonusdate'];?>' class='bonusdate-case'></td>
            <td>
                <input type='hidden' name='clubid' readonly value=<?php echo $values['clubid'];?>>
                <input type='submit' name='submit' value='提交'>
            </td>
            </form>
            <td>
                <form method='post' action=<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?> onsubmit='return initClubAlert();'>
                    <input type='hidden' name='clubid' readonly value=<?php echo $values['clubid'];?>>
                    <input type='submit' name='submit' value='初始化'>
                </form>
            </td>
            <td>
                <form method='post' action=<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?> onsubmit='return removeClubAlert();'>
                    <input type='hidden' name='clubid' readonly value=<?php echo $values['clubid'];?>>
                    <input type='submit' name='submit' value='删除'>
                </form>
            </td>
        </tr>
<?php
}
function currClubChecker($param1, $param2){
    if ($param1 == $param2){
        return 'selected';
    }
    return '';
}
?>
            <tr>
                <td></td>
            <form method='post' action=<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>>
                <td><input type='text' name='addclubserver' value='' class='clubserver-case'></td>
                <td><input type='text' name='addclubname' value='' class='clubname-case'></td>
                <td><input type='text' name='addclubqq' value='' class='clubqq-case'></td>
                <td><input type='text' name='addclubchecker' value='' class='clubchecker-case'></td>
                <td><input type='text' name='addbonusvalue' value='' class='bonusvalue-case'></td>
                <td><input type='text' readonly name='addbonusdate' value='' class='bonusdate-case'></td>
                <td><input type='submit' name='submit' value='加入'></td>
            </form>
                <td></td>
                <td></td>
            </tr>
    </table>
    <br>

    <button class='button button-glow button-rounded button-caution' onclick="window.location.href='install.php'">重新安装</button>
    <button class='button button-glow button-rounded button-action' onclick="window.location.href='adminlogin.php';">退出界面</button>
<div>
<br><br>
</body>

<?php
require "footinfo.php";
?>
</html>