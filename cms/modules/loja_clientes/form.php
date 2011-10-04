<?
global $db;

$alt = $_REQUEST['action']=='alt';
if( $alt && $_REQUEST[id] ) {
	$cf = new ClientFetch( $_REQUEST['id'] );
	$cf->FetchAddr();
	
	$row = $cf->fetch;
	$row_addr = $cf->addr;
}
?>
<form name="oper" id="oper" enctype="multipart/form-data" method="post" onsubmit="return validateForm(this);">
	<div class="field">
		<?php $tipo = (int) $row['cliente_tipo']; ?>
		<label>Tipo de pessoa</label><br />
		<input type="radio" name="tipo" <?php if($tipo==ValueConsts::PESSOA_FISICA) echo 'checked="checked"'; ?> value="0" />Pessoa física 
		<input type="radio" name="tipo" <?php if($tipo==ValueConsts::PESSOA_JURIDICA) echo 'checked="checked"'; ?> value="1" />Pessoa jurídica 
	</div>
	
	<script type="text/javascript">
		$(document).ready(function() {
			$("input:radio[name='tipo']").click(function() {
				if( $(this).val()==<?php echo ValueConsts::PESSOA_FISICA; ?> ) {
					$("#form-fisica").show();
					$("#form-juridica").hide();
				} else {
					$("#form-fisica").hide();
					$("#form-juridica").show();
				}
			});
		});
	</script>
	
	<hr />
	<div id="form-fisica" <?php if($tipo!=ValueConsts::PESSOA_FISICA) echo 'style="display: none;"'; ?>>
		<div class="field"><label for="cpf">CPF</label><br /><input name="cpf" type="text" id="cpf" value="<?=$row[cpf]?>" class="inputMedium required" title="CPF" /></div>
	</div>
	
	<div id="form-juridica" <?php if($tipo!=ValueConsts::PESSOA_JURIDICA) echo 'style="display: none;"'; ?>>
		<div class="field"><label for="cnpj">CNPJ</label><br /><input name="cnpj" type="text" id="cnpj" value="<?=$row[cnpj]?>" class="inputMedium required" title="CNPJ" /></div>
		<div class="field"><label for="ie">Inscrição estadual</label><br /><input name="ie" type="text" id="ie" value="<?=$row[inscricao_estadual]?>" class="inputMedium required" title="Inscrição estadual" /></div>
	</div>
	<hr />
	
	<div class="field"><label for="nome">E-mail:</label><br /><?=$row[login_email]; ?></div>
	<div class="field"><label for="nome">Nome</label><br /><input name="nome" type="text" id="nome" value="<?=$row[ident_nome]?>" class="inputMedium required" title="Informe o nome" /></div>
	<div class="field"><label for="apelido">Apelido</label><br /><input name="apelido" type="text" id="apelido" value="<?=$row[ident_apelido]?>" class="inputMedium required" title="Informe o apelido" /></div>
	<div class="field"><label for="nascimento">Data de nascimento</label><br /><input name="nascimento" id="nascimento" value="<?=$row[view_nascimento]?>" readonly="readonly" type="text" class="inputSmall" style="float:left;"/><img src="gfx/ico/calendar.gif" style="cursor:pointer; float:left;" align="absmiddle" id="btCalendarData" name="btCalendarData" /></div><br clear="all" />
	<div class="field">
		<?php $tipo = $row['ident_sexo']=='f' ? 1 : 0; ?>
		<label>Sexo</label><br />
		<input type="radio" name="sexo" <?php if($tipo==0) echo 'checked="checked"'; ?> value="0" />Masculino 
		<input type="radio" name="sexo" <?php if($tipo==1) echo 'checked="checked"'; ?> value="1" />Feminino
	</div>
	<div class="field"><label for="tel_residencial">Telefone residencial</label><br /><input name="tel_residencial" type="text" id="tel_residencial" value="<?=$row[cont_telresidencial]?>" class="inputMedium required" title="Informe o telefone residencial" /></div>
	<div class="field"><label for="tel_celular">Telefone celular</label><br /><input name="tel_celular" type="text" id="tel_celular" value="<?=$row[cont_telcelular]?>" class="inputMedium required" title="Informe o telefone celular" /></div>
	<div class="field"><label for="tel_comercial">Telefone comercial</label><br /><input name="tel_comercial" type="text" id="tel_comercial" value="<?=$row[cont_telcomercial]?>" class="inputMedium required" title="Informe o telefone comercial" /></div>
	<div class="field">
		<?php $tipo = (int) $row['opt_news']; ?>
		<label>Receber newsletter</label><br />
		<input type="radio" name="newsletter" <?php if($tipo==0) echo 'checked="checked"'; ?> value="0" />Não 
		<input type="radio" name="newsletter" <?php if($tipo==1) echo 'checked="checked"'; ?> value="1" />Sim
	</div>
	
	<hr />
	
	<div class="field"><label for="cep">CEP</label><br /><input name="cep" type="text" id="cep" value="<?=$row_addr[ender_cep]?>" class="inputMedium required" title="Informe o CEP" /></div>
	<div class="field"><label for="estado">Estado</label><br />
    	<?
		$db->Query('SELECT * FROM cms_estados ORDER BY nome ASC');
		?>
        <select name="estado" title="Selecione o estado correspondente">
        	<?
				while ($estado = $db->RowArray()) {
					$selected = $row_addr[ender_estado]==$estado[uf]||($row_addr[ender_estado]==''&&$estado[uf]=='RS')?"selected=\"selected\"":"";
					echo "<option value=\"$estado[uf]\" $selected>$estado[nome]</option>";
				}
			?>
        </select>
    </div>
	
    <div class="field"><label for="cidade">Cidade</label><br /><input name="cidade" type="text" id="cidade" value="<?=$row_addr[ender_cidade]?>" class="inputMedium" title="Informe a cidade"/></div>
	<div class="field"><label for="bairro">Bairro</label><br /><input name="bairro" type="text" id="bairro" value="<?=$row_addr[ender_bairro]?>" class="inputMedium" title="Informe o bairro"/></div>
    <div class="field"><label for="endereco">Endereço</label><br /><input name="endereco" type="text" id="endereco" value="<?=$row_addr[ender_endereco]?>" class="inputBig" title="Informe o endereço completo"/></div>
    <div class="field"><label for="numero">Número</label><br /><input name="numero" type="text" id="numero" value="<?=$row_addr[ender_numero]?>" class="inputSmall" title="Informe o numero" style="text-align:left;" /></div>
    <div class="field"><label for="complemento">Complemento</label><br /><input name="complemento" type="text" id="complemento" value="<?=$row_addr[ender_complemento]?>" class="inputMedium" title="Informe o complemento"/></div>
	<!--<div class="field"><label for="referencia">Referência</label><br /><input name="referencia" type="text" id="referencia" value="<?=$row_addr[ender_referencia]?>" class="inputMedium" title="Informe a referência" /></div>-->
	
	
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

<script type="text/javascript">
	var cal = Calendar.setup({
	  onSelect: function(cal) { cal.hide() }
	});
	cal.manageFields("btCalendarData", "nascimento", "%d/%m/%Y");
</script>