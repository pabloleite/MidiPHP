<?
global $db;

if ($_REQUEST['action']=='alt' && $_REQUEST[id]) {
	$db->Query("SELECT $mainSession.* 
				FROM $mainSession 
				WHERE $primaryKey = '$_REQUEST[id]'");
	
	if( $db->RowCount()>0 )
		$row = $db->RowArray();
}
?>

<div id="validateFormError" style="display:none;">Preencha corretamente o campo.</div>
<form name="oper" id="oper" enctype="multipart/form-data" method="post" onsubmit="return validateForm(this);">
	<div class="field"><label for="legenda">Legenda</label><br /><input name="legenda" type="text" id="legenda" value="<?=$row[legenda]?>" class="inputBig" title="Informe a legenda" /></div>
	<div class="field"><label for="link">Link</label><br /><input name="link" type="text" id="link" value="<?=$row[youtube_url]?>" class="inputBig" title="Informe a link deste vídeo no Youtube" /></div>
	
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