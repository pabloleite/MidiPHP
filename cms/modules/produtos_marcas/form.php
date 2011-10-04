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
	<div class="field"><label for="nome">Nome da marca</label><br /><input name="nome" type="text" id="nome" value="<?=$row[nome]?>" class="inputBig required" title="Informe o nome"/></div>
	<div class="field"><label>Descrição</label><br /><textarea id="descricao" name="descricao" rows="7" cols="80" style="width: 100%;"><?=$row[descricao]?></textarea></div>
	
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
        <td width="83" valign="bottom"><input type="image" src="gfx/btEnviar.gif" class="inputImage wymupdate" width="83" height="25" title="Clique aqui para salvar as configurações"/></td>
      </tr>
    </table>
</form>

<? AdminKernel::showBtVoltar(); ?>