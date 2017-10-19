
<?php
?>

<div align="center" style="border-style:none; height:200px; background:linear-gradient(to bottom, #0c644b, #ffffff);">
    <br>
    <?php
    if (!in_array(basename($_SERVER["PHP_SELF"]), array("adminlogin.php", "adminpanel.php", "register.php", "login.php", "install.php"))) {
    ?>
    <div>
        <b style="color:grey;font-size:6em;font-family:楷体;"><?php echo $clubname ?></b>
    </div>

    <div class="button-group">
        <button type="button" class="button button-royal button-rounded button-raised" onclick="window.location.href='index.php'">
            <font color=white><b>组织分包表</b></font>
        </button>
        <button type="button" class="button button-royal button-rounded button-raised" onclick="window.location.href='userhome.php'">
            <font color=white><b>个人分包表</b></font>
        </button>
    </div>
    <?php
    }
    ?>
</div>
