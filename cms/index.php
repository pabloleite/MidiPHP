<?php
session_name('rumocerto');
session_start();

require_once '../defines.php';
require_once '../funcs.php';
require_once 'includes/requires.php';


$db = new MySQL(true, $config["db_name"], $config["db_host"], $config["db_user"], $config["db_password"], 'utf8', true);
$authUser = new AuthUser();
$kernel = new AdminKernel();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $config["charset"]; ?>" />
	<title><?=$config["title"]." ".$config["version"]?></title>
	
	<link rel="stylesheet" href="includes/css/html.css" media="all" />
	<link rel="stylesheet" href="includes/css/arq.css" media="all" />
	<link rel="stylesheet" href="includes/js/toChecklist-1.4.3/jquery.toChecklist.css" media="all" />
	
	<script type="text/javascript" src="includes/js/jquery-1.5.js"></script>
	<script type="text/javascript" src="includes/js/toChecklist-1.4.3/jquery.toChecklist.js"></script>
	<!--<script type="text/javascript" src="includes/js/jquery.tools.min.js"></script> -->
	<script type="text/javascript" src="includes/js/listData.js"></script>
	<script type="text/javascript" src="includes/js/functions.js"></script>
	<script type="text/javascript" src="includes/js/jquery.maskedinput-1.2.2.min.js"></script>
	<script type="text/javascript" src="includes/js/wymeditor/jquery.wymeditor.pack.js"></script>
	<? include("includes/calendar.inc.php"); ?>
</head>

<body>
<div id="container">
  <div id="top">
    <div id="top-logo"><a href="index.php"><img src="gfx/logoCms.gif" width="650" height="70" /></a></div>
  <div id="top-loginInfo"><? $authUser->showLoginInfo(); ?></div>
  </div>
    <div id="content">
        <div id="adminMenu">
			<img src="gfx/titMenu.png" width="203" height="43" style="margin-bottom:10px;" /><br />
			<? AdminKernel::showAdminMenu($authUser->getModules()); ?>
            
            <br clear="all" />
			<br />

            <div id="auxMenu">
              <img src="gfx/titOutrasOpcoes.png" width="84" height="14" style="margin-bottom:10px;" /><br />
                <? AdminKernel::showAuxMenu(); ?>
            </div>
            
        </div>
        
        <div id="separadorVMenu"><img src="gfx/separadorVMenu.png" width="52" height="607" /></div>
        
		<div id="conteudo">
            <?
                $pageRequest = new PageRequest("ir", true, $_SESSION["auth"]);
                $pageRequest->init($authUser->getModules());
            ?>
        </div>
	</div>
</div>
<br clear="all" />
<div id="bottom">
  <div id="bottomContent">
    <div id="rodape">
      <p><strong>Ezoom Soluções Interativas Ltda</strong> -   Rua Os 18 do Forte 1256, sala 21, Caxias do Sul - RS, Fone: +55 54   30282220 - <strong><a href="http://www.ezoom.com.br" target="_blank">www.ezoom.com.br</a></strong></p>
    </div>
  </div>
</div>
</body>
</html>
