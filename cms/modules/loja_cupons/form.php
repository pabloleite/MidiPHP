<?
set_include_path('../');
require_once 'classes/shopping/consts.php';
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
	
	<div class="field"><label for="nome">Identificação</label><br /><input name="nome" type="text" id="nome" value="<?=$row[nome]?>" class="inputBig required" title="Informe uma identificação para este cupom" /></div>
	<div class="field"><label for="code">Código</label><br /><input name="code" type="text" id="code" value="<?=$row[code]?>" class="inputMedium required" title="Informe o código de ativação deste cupom" /></div>
	
	<div class="field">
		<label>Forma de desconto</label><br />
		<input type="radio" name="tipo" <?php if($row['tipo']==ShoppingConsts::CUPOM_TYPE_PORCENT) echo 'checked="checked"'; ?> value="<?php echo ShoppingConsts::CUPOM_TYPE_PORCENT; ?>" />Porcentual de desconto sobre subtotal dos produtos (em %)<br />
		<input type="radio" name="tipo" <?php if($row['tipo']==ShoppingConsts::CUPOM_TYPE_DISCONT) echo 'checked="checked"'; ?> value="<?php echo ShoppingConsts::CUPOM_TYPE_DISCONT; ?>" />Abatimento do total (em R$)<br />
		<label>Valor <input type="text" name="valor" id="code" value="<?php if( $alt ) echo $row['valor']; ?>" class="inputSmall" /></label>
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