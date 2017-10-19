<?php
require "phpmods/functions.php";
require "phpmods/dbconnect.php";
require "phpmods/accessauth.php";

$res = mysqli_query($mydb, "select * from scores where clubid = '$clubid' and username = '$username' and bonusdate = '$bonusdate'");
$values = mysqli_fetch_array($res);

$restscore = $values['restscore'];
$weekdonation = $values['weekdonation'];
$warnumber = $values['warnumber'];
$totalscore = $values['totalscore'];
$bonus = $values['bonus'];
$reducescore = $values['reducescore'];
$endscore = $values['endscore'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if ($_POST['submit'] == "提交") {
        $weekdonation = $_POST['weekdonation'] === "" ? 0 : $_POST['weekdonation'];
        $warnumber = $_POST['warnumber'];

        $res = mysqli_query($mydb, "select * from scores where clubid = '$clubid' and username = '$username' and bonusdate = '$bonusdate'");
        $values = mysqli_fetch_array($res);
        $totalscore = $values['restscore'] + $weekdonation + $warnumber;
        $endscore = $totalscore - $values['reducescore'];

        mysqli_query($mydb, "update scores set 
        weekdonation = '$weekdonation', warnumber = '$warnumber', 
        totalscore = '$totalscore', endscore = '$endscore' 
        where clubid = '$clubid' and username = '$username' and bonusdate = '$bonusdate'");
    }
}


?>

<!DOCTYPE HTML>
<html>
<head>
<title>成员分包</title>
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
td > input[type='date'] {
    width:10em;
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
function submitAlert() {
    alert("提交成功!");
    //swal('Good job!', 'You clicked the button!', 'success');

}
</script>

</head>

<body>
<?php
require "headinfo.php";
require "headbar.php";
?>
<div align=center>
<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
    <table>
        <caption><b class="tab-cap"><?php echo $bonusdate;?>个人分包情况</b></caption>
        <tr>
            <th colspan="10">
                <font color=red>-></font> 累计积分 : 上周最后积分<br>
                <font color=red>-></font> 扣除积分 : 传说包 = <?php echo $bonusv1 ?>; 英雄包 = <?php echo $bonusv2 ?>; 精英包 = <?php echo $bonusv3 ?><br>
                <font color=red>-></font> 计算公式 : 总积分 = 累计积分 + 本周一至周六捐献/100 + 参战次数; 最后积分 = 总积分 - 扣除积分
            </th>
        </tr>
        <tr>
            <th>序号</th>
            <th>分包日期</th>
            <th>成员名称</th>
            <th>累计积分</th>
            <th><font color="blue">组织捐献/100</font></th>
            <th><font color="blue">参战次数</font></th>
            <th>总积分</th>
            <th>发放的包</th>
            <th>扣除积分</th>
            <th>最后积分</th>
        </tr>
        <tr>
            <td>1</td>
            <td><input type="date" name="bonusdate" readonly value="<?php echo $bonusdate;?>"></td>
            <td><input type="text" name="username" readonly value="<?php echo $username;?>" class='username-case'></td>
            <td><input type="number" name="restscore" readonly value="<?php echo $restscore;?>"></td>
            <td><input type="number" name="weekdonation" maxlength="4" value="<?php echo $weekdonation;?>"></td>
            <td>
                <select name='warnumber'>
                    <option value='0' <?php echo currwarnumber($warnumber, '0');?>>0</option>
                    <option value='1' <?php echo currwarnumber($warnumber, '1');?>>1</option>
                    <option value='2' <?php echo currwarnumber($warnumber, '2');?>>2</option>
                    <option value='3' <?php echo currwarnumber($warnumber, '3');?>>3</option>
                    <option value='4' <?php echo currwarnumber($warnumber, '4');?>>4</option>
                    <option value='5' <?php echo currwarnumber($warnumber, '5');?>>5</option>
                    <option value='6' <?php echo currwarnumber($warnumber, '6');?>>6</option>
                </select>
            </td>
            <td><input type="number" name="totalscore" readonly value="<?php echo $totalscore;?>"></td>
            <td><input type="text" name="bonus" readonly value="<?php echo $bonus;?>"></td>
            <td><input type="number" name="reducescore" readonly value="<?php echo $reducescore;?>"></td>
            <td><input type="number" name="endscore" readonly value="<?php echo $endscore;?>"></td>
        </tr>
        <?php
            function currwarnumber($param1, $param2){
                if ($param1 == $param2){
                    return 'selected';
                }
                return '';
            }
        ?>
    </table>
    <br>
    <input type="submit" name="submit" 
           onclick="submitAlert();" 
           class="button button-glow button-rounded button-action" value="提交">
</form>
<br><br>

<table>
    <caption><b class=tab-cap>个人分包历史</b></caption>
    <tr>
        <th>序号</th>
        <th>分包日期</th>
        <th>成员名称</th>
        <th>累计积分</th>
        <th>组织捐献/100</th>
        <th>参战次数</th>
        <th>总积分</th>
        <th>发放的包</th>
        <th>扣除积分</th>
        <th>最后积分</th>
    </tr>
<?php
$res = mysqli_query($mydb, "select * from scores where clubid = '$clubid' and username = '$username' order by bonusdate desc");
$num = 0;
while($values = mysqli_fetch_array($res)) {
    $num++;
?>
    <tr>
        <td><?php echo $num;?></td>
        <td><input type=date name=bonusdate readonly value=<?php echo $values['bonusdate'];?>></td>
        <td><input type=text name=username readonly value=<?php echo $values['username'];?> class='username-case'></td>
        <td><input type=number name=restscore readonly value=<?php echo $values['restscore'];?>></td>
        <td><input type=number name=weekdonation readonly maxlength=4 min=0 value=<?php echo $values['weekdonation'];?>></td>
        <td><input type=number name=warnumber readonly maxlength=1 min=0 value=<?php echo $values['warnumber'];?>></td>
        <td><input type=number name=totalscore readonly value=<?php echo $values['totalscore'];?>></td>
        <td><input type=text name=bonus readonly value=<?php echo $values['bonus'];?>></td>
        <td><input type=number name=reducescore readonly value=<?php echo $values['reducescore'];?>></td>
        <td><input type=number name=endscore readonly value=<?php echo $values['endscore'];?>></td>
    </tr>
<?php
}
?>
</table>

<div>

</body>

<?php
require "footinfo.php";
?>
</html>