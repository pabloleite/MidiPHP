<?
global $db;

$db->Query("SELECT $mainSession.* 
			FROM $mainSession");
$row = $db->RowArray();
?>

<script type="text/javascript">
$(function() {
	$('#deposito_info').wymeditor({
		lang: 'pt-br',
		logoHtml: ''
	});
});
</script>

<?=AdminKernel::translateMsgInfo();?>

<div id="validateFormError" style="display:none;">Preencha corretamente o campo.</div>
<form name="oper" id="oper" enctype="multipart/form-data" method="post" onsubmit="return validateForm(this);">
	<h1 style="margin: 0;">Pagamento Digital</h1>
	Configuração do e-mail correspondente a conta do Pagamento Digital que será utilizada no site.
	<div class="field"><label for="nome">E-mail</label><br /><input name="pad_email" type="text" id="pad_email" value="<?=$row[pay_pad_email]?>" class="inputMedium" /></div>
	
	<hr />
	<br />
	
	<h1 style="margin: 0;">Depósito bancário</h1>
	Instruções para efetuar o depósito (bancos/contas disponíveis).
	<div class="field"><label>Descrição</label><br /><textarea id="deposito_info" name="deposito_info"><?=$row[pay_deposito_info]?></textarea></div>
	
	<hr />
	<br />

	<h1 style="margin: 0;">Frete grátis</h1>
	Ative clicando no checkbox as situações em que o frete do pedido será grátis.
	<div class="field">
		<label><input type="checkbox" name="gratis_flag_cidade" <?php if($row['frete_gratis_flag_cidade']==1) echo 'checked="checked"'; ?> /> Quando a cidade de envio do pedido for: &nbsp;</label>
		<input type="text" name="gratis_val_cidade" value="<?=$row[frete_gratis_val_cidade]?>" class="inputSmall" />
	</div>
	
	<div class="field">
		<label><input type="checkbox" name="gratis_flag_min" <?php if($row['frete_gratis_flag_min']==1) echo 'checked="checked"'; ?> /> Quando o valor do pedido for maior ou igual a: &nbsp;</label>
		<input type="text" name="gratis_val_min" value="<?php if(!empty($row['frete_gratis_val_min'])) echo FormatLayer::FormatFloatStr($row['frete_gratis_val_min']); ?>" class="inputSmall" />
	</div>
	
	<hr />
	<br />
	
	<h1 style="margin: 0;">Produtos - atributos</h1>
	Campos adicionais para os produtos
	
	
		<?php
		$db->SelectTable('produtos_atributos');
		foreach( $db->RecordsArray() as $rowa )
		{
		?>
			<div class="field">
				<label>
					<input type="checkbox" name="atributo_<?php echo $rowa['identificacao']; ?>" <?php if($rowa['ativo']==1) echo 'checked="checked"'; ?> /> 
					<?php echo $rowa['nome']; ?>
				</label>
			</div>
		<?php
		}
		?>
	
	
	<input type="hidden" name="action" value="alt" />
	<input type="hidden" name="enviado" value="1" />
    <table width="100%" border="0" cellspacing="0" cellpadding="3">
      <tr>
        <td></td>
        <td width="61" valign="bottom"><a href="#" onclick="history.go(-1);"><img src="gfx/btCancelar.gif" width="61" height="18" border="0"/></a></td>
        <td width="83" valign="bottom"><input type="image" src="gfx/btEnviar.gif" class="inputImage wymupdate" title="Clique aqui para salvar as configurações"/></td>
      </tr>
    </table>
</form>


<? AdminKernel::showBtVoltar(); ?>