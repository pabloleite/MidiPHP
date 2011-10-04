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
	margin:20px;
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
	margin-top:5px;
	margin-bottom:5px;
	color:#444444;
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
  <h1>Mensagem enviada pelo site</h1>
    <?
    if ($_REQUEST[assunto]!='') {
    ?>
        <h2><span class="quotes">"</span><?=$_REQUEST['assunto']?><span class="quotes">"</span></h2>
    <?
    }
    ?>
    <table width="700" border="0" cellpadding="3" style="width:680px;">
 	 <tr>
            <td width="87" align="left" valign="top"><span class="azul">Enviada por:</span></td>
            <td width="575" align="left"><?=$_REQUEST['usuario']?></td>
      </tr>
        <tr>
            <td align="left" valign="top"><span class="azul">Telefone:</span></td>
            <td align="left"><?=$_REQUEST['telefone']?></td>
        </tr>
        <tr>
            <td align="left" valign="top"><span class="azul">Endereço:</span></td>
            <td align="left"><?=$_REQUEST['endereco']?></td>
        </tr>
        <tr>
            <td align="left" valign="top"><span class="azul">E-mail:</span></td>
            <td align="left"><a href="mailto:<?=$_REQUEST['para']?>"><?=$_REQUEST['para']?></a></td>
        </tr>
        <tr>
          <td align="left" valign="top"><span class="azul">Mensagem:</span></td>
          <td align="left"><?=$_REQUEST['mensagem']?></td>
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
