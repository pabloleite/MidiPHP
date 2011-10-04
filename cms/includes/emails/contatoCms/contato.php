<?
require("includes/requires.php");
$db = new MySQL(true, $config["db_name"], $config["db_host"], $config["db_user"], $config["db_password"], '', true);

$db->Query("SELECT *, DATE_FORMAT(data_publicacao, '%d/%m/%Y') AS data_publicacao FROM cms_config LIMIT 1");
$config = $db->RowArray();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=urf-8" />
<style type="text/css">
<!--
* {
	color:#222222;
	font-family:Arial, Helvetica, sans-serif;
}
body {
	font-family: Arial, Helvetica, sans-serif;
	font-size: 11px;
	margin:0px;
	position:relative;
	background-repeat:no-repeat;
	margin:0px;
	padding:0px;
}
#content {
	margin-top:10px;
}
h1 {
	font-size:15px;
	line-height:20px;
	margin-top:0px;
	margin-bottom:5px;
}
h2 {
	font-size:12px;
	line-height:18px;
	margin-top:0px;
	margin-bottom:2px;
	color:#444444;
}
a {
	color:#333333;
}
.quotes {
	
}
.small {
	font-size:9px;
}
-->
</style>
</head>

<body>
<img src="cid:top" />
<div id="content">
  <h1>Mensagem enviada através do Mosaico CMS</h1>
  <h2>Domínio: <?=$config['siteurl']?></h2>
  <h2>Data de publicação: <?=$config['data_publicacao']?></h2>
 <br />
 <table width="700" border="0" cellpadding="3" style="width:680px;">
 	 <tr>
            <td width="87" align="left" valign="top"><span class="azul">Enviada por:</span></td>
            <td width="575" align="left"><?=$_REQUEST['nome']?></td>
      </tr>
        <tr>
            <td align="left" valign="top"><span class="azul">E-mail:</span></td>
            <td align="left"><a href="mailto:<?=$_REQUEST['email']?>"><?=$_REQUEST['email']?></a></td>
        </tr>
        <tr>
          <td align="left" valign="top"><span class="azul">Mensagem:</span></td>
          <td align="left"><?=nl2br($_REQUEST['mensagem'])?></td>
      </tr>
        <tr>
          <td align="left" valign="top"> </td>
          <td align="left"> </td>
      </tr>
        <tr>
            <td colspan="2" align="left" valign="top" class="small">
            Enviado em: <b><?=date('d/m/Y á\s h:i:s')?></b><br />
            Enviado pelo IP: <b><?=$_SERVER['REMOTE_ADDR']?></b></td>
        </tr>
    </table>
</div>
</body>
</html>
