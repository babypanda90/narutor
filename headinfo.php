
<!-- <script type="text/javascript" src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>
<script type="text/javascript">
function googleTranslateElementInit() {
  new google.translate.TranslateElement({pageLanguage: 'zh-CN', 
  includedLanguages: 'en,fr', 
  layout: google.translate.TranslateElement.InlineLayout.SIMPLE, 
  autoDisplay: false},
  'google_translate_element');
}
</script> -->

<link rel="stylesheet" type="text/css" href="css/buttons.css">

<link rel="stylesheet" type="text/css" href="css/main.css">

<div id="headinfo" style="text-align:right; line-height:30px;">
    <!-- <div style="float:left;" id="google_translate_element"></div> -->
    <?php
    echo "IP地址 = " . GetIP() . " | ";
    if(!in_array(basename($_SERVER["PHP_SELF"]), array("adminlogin.php", "adminpanel.php", "register.php", "login.php", "install.php"))) {
        
        if($username == $clubchecker) {
            echo "<a href=checkerpanel.php style=color:white; text-decoration: none;>成员管理</a> | ";
        }

        echo $clubname . "组织QQ群 : " . $clubqq . " | ";

        echo "欢迎 <a href=userhome.php style=color:white; text-decoration: none;>" . $username . "</a>
            <a href=register.php style=color:white; text-decoration: none;>注册</a>
            <a href=login.php style=color:white; text-decoration: none;>退出</a>";

    }
    else {
            echo "<a href=register.php style=color:white; text-decoration: none;>注册</a>
                  <a href=login.php style=color:white; text-decoration: none;>登陆</a>";
        }
    ?>
</div>
