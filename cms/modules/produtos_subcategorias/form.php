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
	<div class="field"><label for="nome">Nome da Sub-categoria</label><br /><input name="nome" type="text" id="nome" value="<?=$row[nome]?>" class="inputBig required" title="Nome da Sub-categoria" /></div>
	<div class="field">
		<label>Categoria</label><br />
		<select id="list" name="categoria" style="width: 200px;" title="Selecione uma única categoria">
			<?php
			$first = $_REQUEST['action']=='inc';
			$db->SelectTable('produtos_categorias');
			foreach( $db->RecordsArray() as $subrow )
			{
				$sel = ($_REQUEST['action']=='alt' && $subrow['id_categoria']==$row['id_categoria']) || $first;
				$first = false;
				echo '<option ' .($sel ? 'selected="selected"' : ''). ' value="' .$subrow['id_categoria']. '">' .$subrow['nome']. '</option>';
			}
			?>
		</select>
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