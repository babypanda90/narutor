<?php
if (in_array(basename($_SERVER["PHP_SELF"]), array("adminpanel.php"))) {
	if(!isset($_COOKIE["admin"]) || $_COOKIE["admin"] != "1") {
		echo "<script>alert('请登陆后再操作!');window.location.href='adminlogin.php';</script>";
		exit;
	}
}

else if(isset($_COOKIE["username"]) && isset($_COOKIE["clubid"])) {
	$username = $_COOKIE["username"];
	$clubid = $_COOKIE["clubid"];

	$res = mysqli_query($mydb, "select * from clubs where clubid = '$clubid'");
	$club = mysqli_fetch_array($res);
	$clubserver = $club['clubserver'];
	$clubname = $club['clubname'];
	$clubqq = $club['clubqq'];
	$clubchecker = $club['clubchecker'];

	$res = mysqli_query($mydb, "select userid from users where username = '$clubchecker' and clubid = '$clubid'");
	$row = mysqli_fetch_row($res);
	$checkerid = $row[0];
	$isChecker = $username == $clubchecker;

	$bonusvalue = $club['bonusvalue'];
	$bonusv1 = $bonusv2 = $bonusv3 = '';
	if ($bonusvalue ) {
		$bonusvaluearray = explode("|", $bonusvalue);
		$bonusv1 = $bonusvaluearray[0];
		$bonusv2 = $bonusvaluearray[1];
		$bonusv3 = $bonusvaluearray[2];
	}

	$bonusdate = $club['bonusdate'];
	$last_bonusdate = $bonusdate;

	// pages access : 统计员才可进入管理页面
	if (!$isChecker && in_array(basename($_SERVER["PHP_SELF"]), array("checkerpanel.php"))) {
		echo "<script>alert('请登陆后再操作!'); window.location.href='login.php';</script>";
		exit;
	}
}

else {
	if (!in_array(basename($_SERVER["PHP_SELF"]), array("login.php", "register.php"))) {
		echo "<script>alert('请登陆后再操作!'); window.location.href='login.php';</script>";
		exit;
	}
}
?>
