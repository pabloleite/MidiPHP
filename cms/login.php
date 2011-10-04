<?
session_name('rumocerto');
session_start();

require_once('includes/requires.php');


$db = new MySQL(true, $config["db_name"], $config["db_host"], $config["db_user"], $config["db_password"], "", true);
$authUser = new AuthUser();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?=$config["charset"]?>" />
<title><?=$config["title"]. "- Login";?></title>
<script type="text/javascript" src="includes/js/jquery-1.5.js"></script>
<script type="text/javascript" src="includes/js/functions.js"></script>
<link rel="stylesheet" href="includes/css/login.css" media="all" />
</head>

<body>
   <div id="login">    
            <div id="loginLogo" style="margin-top:-80px;"><img src="gfx/logoCmsLogin.png" width="295" height="180" /></div>
           <p><img src="gfx/hrLogin.png" width="307" height="1" /></p>
            <p class="menor">Olá! Seja bem bem vindo ao <strong>Gerenciado de Conteúdo </strong>de seu site.<br />
            </p>
            <p class="menor">Entre com seus dados de login no formulário abaixo.</p>
			
	<p><img src="gfx/hrLogin.png" width="307" height="1" /></p>
			 <?
			 
			if ($_REQUEST["authError"]=="1") {
				echo "<p class=\"error\">Usuário ou senha inválidos.</p>";
			}
			if ($_REQUEST["recoverSucess"]=="1") {
				echo "<p class=\"sucess\">Sua senha foi enviada para seu email cadastrado.</p>";
			}
			if ($_REQUEST["errorSend"]=="1") {
				echo "<p class=\"error\">Falha no reenvio da senha. Favor entre em contato com a mosaico via telefone ou email.</p>";
			}
			if ($_REQUEST["invalidEmail"]=="1") {
				echo "<p class=\"error\">O email informado é inválido, favor verifique.</p>";
			}
   			?>
       <div id="validateFormError" style="display:none;">Preencha corretamente o campo.</div>
      <form name="frmLogin" id="frmLogin" method="post" onsubmit="return validateForm(this);">
      <table border="0" align="center" cellpadding="2" cellspacing="0">
      <tr>
                <td align="left">
                 	<label for="user" <?=$_REQUEST['recover']!='1'?"":"style=\"display:none;\"";?> >
                    	<img src="gfx/lblUsuario.gif" width="49" height="12" />
                    </label>
                   	<label for="email" <?=$_REQUEST['recover']!='1'?"style=\"display:none;\"":"";?> >
                    	<img src="gfx/lblEmail.gif" width="109" height="11" />
                    </label>
                    <br/>
                    
                   	<input type="text" name="user" id="user" value="" <?=$_REQUEST['recover']!='1'?"":"style=\"display:none;\"";?>  class="required" title="Informe o usuario." />
                   	<input type="text" name="email" id="email" value="" <?=$_REQUEST['recover']!='1'?"style=\"display:none;\"":"";?> class="required email" title="Por favor preencher com um email valido"/>
                </td>
        </tr>
              <tr>
                <td align="left">
                <div class="field" <?=$_REQUEST['recover']!='1'?"":"style=\"display:none;\"";?> >
                	<label for="login"><img src="gfx/lblSenha.gif" width="42" height="12" /></label><br />
                	<input type="password" name="pwd" id="pwd" value="" class="required" title="Informe a senha."/>
               	</div>
               </td>
            </tr>
              <tr>
                <td style="height:5px;"></td>
              </tr>
              <tr>
                <td align="center">
                <input type="image" <?=$_REQUEST['recover']!='1'?"src=\"gfx/btEntrar.gif\"":"src=\"gfx/btEnviarLogin.gif\""; ?> width="100" height="24" class="inputImage" style="width:100px; height:24px;" />
               	</td>
            </tr>
              <tr>
                <td align="center">
                	<!--<a href="?recover=1" id="recover" <?=$_REQUEST['recover']!='1'?"":"style=\"display:none;\"";?> >Esqueceu a senha?</a>-->
                	<a href="login.php" id="login" <?=$_REQUEST['recover']!='1'?"style=\"display:none;\"":"";?> >Retornar ao Login ?</a>
                </td>
              </tr>
      </table>
           <input type="hidden" <?=$_REQUEST['recover']!='1'?"name=\"login\" value=\"1\"":"name=\"recoverEmail\" value=\"1\"";?> />
      </form>
	</div>
	    
</body>
</body>
</html>
