<?
global $db;

$alt = $_REQUEST['action']=='alt';
if( $alt && $_REQUEST[id] ) {
	$db->Query("SELECT $mainSession.* 
				FROM $mainSession 
				WHERE $primaryKey = '$_REQUEST[id]'");
	
	if( $db->RowCount()>0 )
		$row = $db->RowArray();
}
?>

<div id="validateFormError" style="display:none;">Preencha corretamente o campo.</div>
<form name="oper" id="oper" enctype="multipart/form-data" method="post" onsubmit="return validateForm(this);">

	<div class="field">
		<?php $state = $row['ativo']=='1' || $_REQUEST['action']=='inc'; ?>
		<label for="ativo">Ativo</label><br />
		<input type="radio" name="ativo" <?php if($state==true) echo 'checked="checked"'; ?> value="1" />Sim 
		<input type="radio" name="ativo" <?php if($state==false) echo 'checked="checked"'; ?> value="0" />Não
	</div>
	
	<div class="field"><label for="nome">Nome de identificação</label><br /><input name="nome" type="text" id="nome" value="<?=$row[nome]?>" class="inputBig" title="Informe o nome"/></div>
	<div class="field"><label for="link">Link</label><br /><input name="link" type="text" id="link" value="<?=$row[link]?>" class="inputBig" title="Link deste banner" /></div>
	
	<div class="field"><label>Imagem</label>
		<?=AdminKernel::getListDataImage($config["userfiles_path"]."/".$mainSession."/".$row["imagem"])?><br />
		<input type="file" name="imagem1" id="imagem" class="radio" title="Selecione um imagem" value="" />
		<?php if ($_REQUEST['action']=='alt') { ?>
			<br /><label class="normal"><input type="radio" name="manterArquivo1" id="manterArquivo1" value="true" checked="checked" title="Clique aqui para manter o arquivo"/>Manter arquivo</label><br />
			<label class="normal"><input type="radio" name="manterArquivo1" id="manterArquivo1" value="false" title="Clique aqui para apagar o arquivo"/>Apagar arquivo</label>
		<?php } ?>
	</div>
	
	<input type="hidden" name="enviado" value="1" />
    <table width="100%" border="0" cellspacing="0" cellpadding="3">
      <tr>
        <td></td>
        <td width="61" valign="bottom"><a href="#" onclick="history.go(-1);"><img src="gfx/btCancelar.gif" width="61" height="18" border="0"/></a></td>
        <td width="83" valign="bottom"><input type="image" src="gfx/btEnviar.gif" class="inputImage" width="83" height="25" title="Clique aqui para salvar as configurações"/></td>
      </tr>
    </table>
</form>


<? AdminKernel::showBtVoltar(); ?>