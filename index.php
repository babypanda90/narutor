<?php
require "phpmods/functions.php";
require "phpmods/dbconnect.php";
require "phpmods/accessauth.php";

if ($_SERVER["REQUEST_METHOD"] == "POST"){
    if ($_POST['submit'] == "提交"){
        $res = mysqli_query($mydb, "select * from scores where bonusdate = '$bonusdate' and clubid = '$clubid'");
        while($values = mysqli_fetch_array($res)){
            $bonus = $_POST[$values['username'] . '_bonus'];
            $reducescore = $_POST[$values['username'] . '_reducescore'];
            $weekdonation = $_POST[$values['username'] . '_weekdonation'] === "" ? 0 : $_POST[$values['username'] . '_weekdonation'];
            $warnumber = $_POST[$values['username'] . '_warnumber'];

            $totalscore = $_POST[$values['username'] . '_restscore'] + $weekdonation + $warnumber;
            $endscore = $totalscore - $reducescore;

            mysqli_query($mydb, "update scores set 
            totalscore = '$totalscore', bonus = '$bonus', reducescore = '$reducescore', 
            endscore = '$endscore', weekdonation = '$weekdonation', warnumber = '$warnumber' 
            where bonusdate = '$bonusdate' and username = '$values[username]' and clubid = '$clubid'");
        }
    }
    
    if ($_POST['submit'] == "更新") {
        $res = mysqli_query($mydb, "select bonusdate from clubs where clubid = '$clubid'");
        $row = mysqli_fetch_row($res);
        $currdate = strtotime($row[0]);
        $bonusdate = date("Y-m-d", strtotime("next Saturday", $currdate));
        mysqli_query($mydb, "update clubs set bonusdate = '$bonusdate' where clubid = '$clubid'");
        
        $res = mysqli_query($mydb, "select * from users where clubid = '$clubid'");
        while ($values = mysqli_fetch_array($res)) {
            $res1 = mysqli_query($mydb, "select * from scores where username = '$values[username]' and clubid = '$clubid' order by bonusdate desc");

            $values1 = mysqli_fetch_array($res1);
            $restscore = $totalscore = $endscore = $values1['endscore'];

            mysqli_query($mydb, "insert into scores (clubid, userid, bonusdate, username, restscore, weekdonation, warnumber, totalscore, bonus, reducescore, endscore)
            values ('$clubid', '$values[userid]', '$bonusdate', '$values[username]', '$restscore', '0', '0', '$totalscore', '', '0', '$endscore')");
        }
        
    }

    if ($_POST['submit'] == "查看") {
        $bonusdate = $_POST["bonusdate_check"];
    }
}

?>



<!DOCTYPE html>
<html>
<head>
<title>组织分包</title>
<style>
th, td {
    background: linear-gradient(to bottom right, #d2b28a, #8f7657); /* http://www.color-hex.com/ */
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
    width:9em;
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
.bonus-history {
    background: white;
    border-radius:5px;
    border:groove;
    position: absolute;
    z-index: 10;
    overflow: hidden;
    display: none;
}
.username-case:hover + .bonus-history {
    display: block;
}
</style>
<script type="text/javascript">
function distribute_bonus(sel) {
    var curr_td = sel.parentNode;
    var curr_tr = curr_td.parentNode;

    var totalscore_td = curr_tr.getElementsByTagName("td")[6];
    var totalscore_input = totalscore_td.getElementsByTagName("input")[0];

    var reducescore_td = curr_tr.getElementsByTagName("td")[8];
    var reducescore_input = reducescore_td.getElementsByTagName("input")[0];

    var endscore_td = curr_tr.getElementsByTagName("td")[9];
    var endscore_input = endscore_td.getElementsByTagName("input")[0];

    if (sel.value == "传说") {
        reducescore_input.value = <?php echo $bonusv1;?>;
    }
    else if (sel.value == "英雄") {
        reducescore_input.value = <?php echo $bonusv2;?>;
    }
    else if (sel.value == "精英") {
        reducescore_input.value = <?php echo $bonusv3;?>;
    }
    else {
        reducescore_input.value = 0;
    }

    endscore_input.value = totalscore_input.value - reducescore_input.value;
}

function sortTable(n) {
    var table, rows, switching, i, x, y, shouldSwitch, dir, switchcount = 0;
    table = document.getElementById("scoreTable");
    switching = true;
    //Set the sorting direction to ascending:
    dir = "asc";
    /*Make a loop that will continue until
    no switching has been done:*/
    while (switching) {
        //start by saying: no switching is done:
        switching = false;
        rows = table.getElementsByTagName("tr");
        /*Loop through all table rows (except the
        first, which contains table headers):*/
        for (i = 2; i < (rows.length - 1); i++) {
            //start by saying there should be no switching:
            shouldSwitch = false;
            /*Get the two elements you want to compare,
            one from current row and one from the next:*/
            x = rows[i].getElementsByTagName("td")[n].getElementsByTagName("input")[0].value;
            y = rows[i + 1].getElementsByTagName("td")[n].getElementsByTagName("input")[0].value;
            x = parseInt(x);
            y = parseInt(y);
            /*check if the two rows should switch place,
            based on the direction, asc or desc:*/
            if (dir == "asc") {
                if (x > y) {
                    //if so, mark as a switch and break the loop:
                    shouldSwitch= true;
                    break;
                }
            }
            else if (dir == "desc") {
                if (x < y) {
                    //if so, mark as a switch and break the loop:
                    shouldSwitch= true;
                    break;
                }
            }
        }
    if (shouldSwitch) {
        /*If a switch has been marked, make the switch
        and mark that a switch has been done:*/
        rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);
        switching = true;
        //Each time a switch is done, increase this count by 1:
        switchcount++; 
    }
    else {
            /*If no switching has been done AND the direction is "asc",
            set the direction to "desc" and run the while loop again.*/
            if (switchcount == 0 && dir == "asc") {
                dir = "desc";
                switching = true;
            }
        }
    }

    // 还原序号
    rows = table.getElementsByTagName("tr");
    for (i = 2; i < rows.length; i++) {
        rows[i].getElementsByTagName("td")[0].innerHTML = i - 1;
    }
}

function submitAlert() {
    alert("提交成功!");
    //swal('Good job!', 'You clicked the button!', 'success');

}
function updateAlert() {
    return confirm("确定更新到下周吗?");
}

</script>
</head>

<body>

<?php
require "headinfo.php";
require "headbar.php";
?>



<div align=center>

    <table id="scoreTable">
        <caption>
            <form style="display: inline;" method="post" align="right" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
                <input type="date" name="bonusdate_check" value=<?php echo $bonusdate?>><b class="tab-cap">组织分包情况</b>
                <input type="submit" name="submit" value="查看">
            </form>
        </caption>
        <tr>
            <th colspan="11">
                <font color=red>-></font> 累计积分 : 上周最后积分<br>
                <font color=red>-></font> 扣除积分 : 传说包 = <?php echo $bonusv1 ?>; 英雄包 = <?php echo $bonusv2 ?>; 精英包 = <?php echo $bonusv3 ?><br>
                <font color=red>-></font> 计算公式 : 总积分 = 累计积分 + 本周一至周六捐献/100 + 参战次数; 最后积分 = 总积分 - 扣除积分
            </th>
        </tr>
        <tr>
            <th>序号</th>
            <th>分包日期</th>
            <th>成员名称</th>
            <th onclick="sortTable(3)">累计积分</th>
            <th onclick="sortTable(4)"><font color="blue">组织捐献/100</font></th>
            <th><font color="blue">参战次数</font></th>
            <th onclick="sortTable(6)">总积分</th>
            <th>发放的包</th>
            <th>扣除积分</th>
            <th onclick="sortTable(9)">最后积分</th>
        </tr>
<form style="display: inline;" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
<?php
$res = mysqli_query($mydb, "select * from scores where bonusdate = '$bonusdate' and clubid = '$clubid'");
$num = 0;
while($values = mysqli_fetch_array($res)) {
    $num++;
?>
        <tr>
            <td><?php echo $num;?></td>
            <td><input type=date name=<?php echo $values['username'] . '_bonusdate';?> readonly value=<?php echo $values['bonusdate'];?>></td>
            <td><input type=text name=<?php echo $values['username'] . '_username';?> readonly value=<?php echo $values['username'];?> class=username-case>
                <div class=bonus-history><?php echo viewBonusHistory($mydb, $values['username'], $clubid);?></div>
            </td>
            <td><input type=number name=<?php echo $values['username'] . '_restscore';?> readonly value=<?php echo $values['restscore'];?>></td>
            <td><input type=number name=<?php echo $values['username'] . '_weekdonation';?> <?php echo readonlyinput($isChecker);?> maxlength=4 min=-500 value=<?php echo $values['weekdonation'];?>></td>
    <?php
    if ($isChecker && $bonusdate === $last_bonusdate) {
    ?>
            <td>
                <select name=<?php echo $values['username'] . '_warnumber';?>>
                    <option value='0' <?php echo currwarnumber($values['warnumber'], '0');?>>0</option>
                    <option value='1' <?php echo currwarnumber($values['warnumber'], '1');?>>1</option>
                    <option value='2' <?php echo currwarnumber($values['warnumber'], '2');?>>2</option>
                    <option value='3' <?php echo currwarnumber($values['warnumber'], '3');?>>3</option>
                    <option value='4' <?php echo currwarnumber($values['warnumber'], '4');?>>4</option>
                    <option value='5' <?php echo currwarnumber($values['warnumber'], '5');?>>5</option>
                    <option value='6' <?php echo currwarnumber($values['warnumber'], '6');?>>6</option>
                </select>
            </td>
    <?php
    }
    else {
    ?>
            <td><input type=text name=<?php echo $values['username'] . '_warnumber';?> readonly value=<?php echo $values['warnumber'];?>></td>
    <?php
    }
    ?>
            <td><input type=number name=<?php echo $values['username'] . '_totalscore';?> readonly value=<?php echo $values['totalscore'];?>></td>
    <?php
    if ($isChecker && $bonusdate === $last_bonusdate) {
    ?>
            <td>
                <select name=<?php echo $values['username'] . '_bonus';?> onchange='distribute_bonus(this);'>
                    <option value='' <?php echo currbonus($values['bonus'], '');?>></option>
                    <option value=传说 <?php echo currbonus($values['bonus'], '传说');?>>传说</option>
                    <option value=英雄 <?php echo currbonus($values['bonus'], '英雄');?>>英雄</option>
                    <option value=精英 <?php echo currbonus($values['bonus'], '精英');?>>精英</option>
                </select>
            </td>
    <?php
    }
    else {
    ?>
            <td><input type=text name=<?php echo $values['username'] . '_bonus';?> readonly value=<?php echo $values['bonus'];?>></td>
    <?php
    }
    ?>
            <td><input type=number name=<?php echo $values['username'] . '_reducescore';?> readonly value=<?php echo $values['reducescore'];?>></td>
            <td><input type=number name=<?php echo $values['username'] . '_endscore';?> readonly value=<?php echo $values['endscore'];?>></td>
        </tr>
<?php
}

function viewBonusHistory($mydb, $param1, $param2) {
    $bonusHistory = mysqli_query($mydb, "select * from scores where username = '$param1' and clubid = '$param2' order by bonusdate desc");
    $res = "";
    while($values = mysqli_fetch_array($bonusHistory)) {
        $res .= $values['bonusdate'] . " : 参战" . $values['warnumber'] . "次 " . $values['bonus'] . "<br>";
    }
    return $res;
}

function currbonus($param1, $param2){
    if ($param1 == $param2){
        return 'selected';
    }
    return '';
}

function currwarnumber($param1, $param2){
    if ($param1 == $param2){
        return 'selected';
    }
    return '';
}

function readonlyinput($param){
    if ($param){
        return '';
    }
    return 'readonly';
}
?>

    </table>
    <br>
<?php
if ($isChecker && $bonusdate === $last_bonusdate) {
?>
    <input type=submit name=submit onclick='submitAlert();' class='button button-glow button-rounded button-action' value=提交>
<?php
}
?>
</form>

<?php
if ($isChecker && $bonusdate === $last_bonusdate) {
?>
<form style="display: inline;" method=post action=<?php echo htmlspecialchars($_SERVER['PHP_SELF']);?> onsubmit='return updateAlert();'>
    <input type=submit name=submit class='button button-glow button-rounded button-caution' value=更新>
</form>
<?php
}
?>
</div>
<br><br>
</body>

<?php
require "footinfo.php";
?>


</html>