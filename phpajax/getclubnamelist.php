<?php
require "../phpmods/dbconnect.php";
$cs = $_GET['cs'];

$res = mysqli_query($mydb, "select * from clubs where clubserver = '$cs'");
echo "<option value=''>选择组织</option>";
while($values = mysqli_fetch_array($res)) {
    echo "<option value=".$values['clubname'].">".$values['clubname']."</option>";
}
?>