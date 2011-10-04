<?
require("includes/requires.php");
$db = new MySQL(true, $config["db_name"], $config["db_host"], $config["db_user"], $config["db_password"], '', true);
	
if ($_REQUEST[enviarMensagem]=='1') {
	$db->Query("SELECT *, DATE_FORMAT(data_publicacao, '%d/%m/%Y') AS data_publicacao FROM cms_config LIMIT 1");
	$config = $db->RowArray();
	
	Utils::sendMail(array("Mosaico Agência Digital"=>"contato@mosaicoagencia.com"), "Mosaico CMS - $_REQUEST[motivoContato] ($config[siteurl])", "contatoCms", "", array("Rodrigo"=>"rodrigo@mosaicoagencia.com"));
	Utils::redirect("index.php?mensagemEnviada=1");
}
?>		

<div id="homeLeft">
    <img src="gfx/titComoFunciona.png" width="152" height="17" />
<p>Utilize o menu ao lado para acessar as sessões disponíveis. Ao clicar em algum item, será exibida a página de visualização de conteúdo respectivo a sessão selecionada. Nesta página você pode realizar a inclusão de novas informações, fazer a edição dos itens disponíveis e até mesmo excluír algo que você não queira mais. </p>
    <p>O Sitema de gerenciamento de conteúdo - <strong>eZoom Agência Digital</strong> foi desenvolvido para que seja uma ferramenta útil e de fácil utilização por ter uma interface simples e intuítiva.</p>
  <p>Qualquer dúvida, sugestão ou problema que você possa ter, utilize o formulário ao lado para enviar-nos uma mensagem ou através do email <a href="mailto:suporte@mosaicoagencia.com">suporte@ezoom.com.br.</a> Em breve estaremos respondendo sua mensagem.</p>
  <p class="bemVindo">Seja bem vindo!</p>
</div>

<div id="homeRight">
<img src="gfx/titDuvidas.png" width="328" height="22" />

<?
if ($_REQUEST[mensagemEnviada]!='1') {
?>
<p>Ajude-nos a melhorar o Painel Administrativo da eZoom! Envie sua sugestão ou dúvida! </p>
<form name="formContact" method="post" enctype="multipart/form-data" id="formContact" onsubmit="return validateForm(this);">
    	<table width="100%" border="0" cellspacing="0" cellpadding="3">
          <tr>
            <td><label><strong>Seu nome</strong><br />
                <input type="text" name="nome" id="nome" class="inputMedium required" title="Informe seu nome completo." />
            </label></td>
          </tr>
          <tr>
            <td><label><strong>E-mail</strong><br />
                <input type="text" name="email" id="email" class="inputMedium required email" title="Informe um email válido!" />
            </label></td>
          </tr>
          <tr>
            <td><strong>Motivo do contato</strong><br />
               <p>
                   <label><input type="radio" name="motivoContato" value="Sugestão" /> Sugestão</label>    
                   <label><input type="radio" name="motivoContato" value="Dúvida" checked="checked" />Dúvida</label>    
                   <label><input type="radio" name="motivoContato" value="Reclamação/Bug" /> Reclamação</label>
               </p>
                </td>
          </tr>
          <tr>
            <td><label><strong>Mensagem</strong><br />
                <textarea name="mensagem" class="textareaMedium required" title="Digite uma mensagem para enviar."></textarea>
            </label></td>
          </tr>
          <tr>
          	<td align="right"><input type="image" src="gfx/btEnviar.gif" class="inputImage" style="margin-right:65px;" /></td>
          </tr>
        </table>
	<input type="hidden" name="enviarMensagem" value="1" />
    </form>
<?
} else {
?>
	<p><strong>Obrigado!</strong></p>
    <p>Sua mensagem foi enviada. Em breve estaremos lhe respondendo.</p>
<?
}
?>
</div>