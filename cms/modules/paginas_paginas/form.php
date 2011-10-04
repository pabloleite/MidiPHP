<?
global $db;

if ($_REQUEST['action']=='alt' && $_REQUEST[id]) {
	$db->Query("SELECT $mainSession.* 
					FROM $mainSession 
					WHERE $primaryKey = '$_REQUEST[id]'");
	
	if ($db->RowCount()>0) {
		$row = $db->RowArray();
	}
}
?>

<script type="text/javascript">
$(function() {
	$('#texto').wymeditor({
		lang: 'pt-br',
		logoHtml: '',
		boxHtml: "<div class='wym_box'>"
				  + "<div class='wym_area_top'>"
				  + WYMeditor.TOOLS
				  + "</div>"
				  + "<div class='wym_area_left'></div>"
				  + "<div class='wym_area_right'>"
				  + WYMeditor.CONTAINERS
				  + "</div>"
				  + "<div class='wym_area_main'>"
				  + WYMeditor.HTML
				  + WYMeditor.IFRAME
				  + WYMeditor.STATUS
				  + "</div>"
				  + "<div class='wym_area_bottom'>"
				  + "</div>"
				  + "</div>"
	});
});
</script>

<div id="validateFormError" style="display:none;">Preencha corretamente o campo.</div>
<form name="oper" id="oper" enctype="multipart/form-data" method="post" onsubmit="return validateForm(this);">
	<h1><?php echo $row['identificacao']; ?></h1>
	
	<div class="field"><label for="titulo">Título</label><br /><input name="titulo" type="text" id="titulo" value="<?=$row[titulo]?>" class="inputBig" title="Informe o título"/></div>
    <div class="field"><label>Texto</label><br /><textarea id="texto" name="texto" rows="15" cols="80" style="width: 100%;"><?=$row[texto]?></textarea></div>
	
	<div class="field"><label>Imagem</label>
		<?=AdminKernel::getListDataImage($config["userfiles_path"]."/".$mainSession."/".$row["imagem"])?><br />
		<input type="file" name="imagem" id="imagem" class="radio" title="Selecione um imagem" value="" />
		<?php if ($_REQUEST['action']=='alt') { ?>
			<br /><label class="normal"><input type="radio" name="manterArquivo" id="manterArquivo" value="true" checked="checked" title="Clique aqui para manter o arquivo"/>Manter arquivo</label><br />
			<label class="normal"><input type="radio" name="manterArquivo" id="manterArquivo" value="false" title="Clique aqui para apagar o arquivo"/>Apagar arquivo</label>
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