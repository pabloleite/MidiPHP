<?
global $db;

$alt = $_REQUEST['action']=='alt';
if( $alt && $_REQUEST[id] ) {	$db->Query("SELECT $mainSession.*, DATE_FORMAT($mainSession.data_nascimento, '%d/%m/%Y') as data_nascimento
					FROM $mainSession 
					WHERE $primaryKey = '$_REQUEST[id]'");
	
	if ($db->RowCount()>0)
		$row = $db->RowArray();
}
?>

<script type="text/javascript">
	$(function() {
		$('#telefone').mask("(99) 99999999");
		$('#celular').mask("(99) 99999999");
	});
</script>

<div id="validateFormError" style="display:none;">Preencha corretamente o campo.</div>
<form name="oper" id="oper" enctype="multipart/form-data" method="post" onsubmit="return validateForm(this);">
	<div class="field"><label for="nome">Nome</label><br /><input name="nome" type="text" id="nome" value="<?=$row[nome]?>" class="inputMedium required" title="Informe o nome completo."/></div>
    <div class="field"><label>Foto</label>
	<?=AdminKernel::getListDataImage($config["userfiles_path"]."/".$mainSession."/".$row["imagem"])?><br />
	<input type="file" name="imagem" id="imagem" class="radio" title="Selecione uma imagem para o usuario"/>
	<? if( $alt ) { ?>
		<br /><label class="normal"><input type="radio" name="manterArquivo" id="manterArquivo" value="true" checked="checked" title="Clique aqui para manter o arquivo"/>Manter arquivo</label><br />
		<label class="normal"><input type="radio" name="manterArquivo" id="manterArquivo" value="false" title="Clique aqui para apagar o arquivo"/>Apagar arquivo</label>
	<? } ?>
	</div>
    
    <div class="field"><label for="endereco">Endereço</label><br /><input name="endereco" type="text" id="endereco" value="<?=$row[endereco]?>" class="inputBig" title="Informe o endereço completo"/></div>
    <div class="field"><label for="numero">Número</label><br /><input name="numero" type="text" id="numero" value="<?=$row[numero]?>" class="inputSmall" title="Informe o numero" style="text-align:left;" /></div>
    <div class="field"><label for="bairro">Bairro</label><br /><input name="bairro" type="text" id="bairro" value="<?=$row[bairro]?>" class="inputMedium" title="Informe o bairro"/></div>
    <div class="field"><label for="cidade">Cidade</label><br /><input name="cidade" type="text" id="cidade" value="<?=$row[cidade]?>" class="inputMedium" title="Informe a cidade"/></div>
    <div class="field"><label for="estado">Estado</label><br />
    	<?
		$db->Query('SELECT * FROM cms_estados ORDER BY nome ASC');
		?>
        <select name="estado" title="Selecione o estado correspondente">
        	<?
				while ($estado = $db->RowArray()) {
					$selected = $row[estado]==$estado[id_estado]||($row[estado]==''&&$estado[uf]=='RS')?"selected=\"selected\"":"";
					echo "<option value=\"$estado[id_estado]\" $selected>$estado[nome]</option>";
				}
			?>
        </select>
    </div>
    
    <div class="field"><label for="telefone">Telefone</label><br /><input name="telefone" type="text" id="telefone" value="<?=$row[telefone]?>" class="inputMedium required" title="Informe o telefone. <br/> Digite somente números"/></div>
    <div class="field"><label for="celular">Celular</label><br /><input name="celular" type="text" id="celular" value="<?=$row[celular]?>" class="inputMedium" title="Informe o celular. <br/> Digite somente números."/></div>
    <div class="field"><label for="email">Email</label><br /><input name="email" type="text" id="email" value="<?=$row[email]?>" class="inputMedium required email" title="Informe o email do usuário"/></div>
    <div class="field"><label for="data_nascimento">Data de nascimento</label><br /><input name="data_nascimento" id="data_nascimento" value="<?=$row[data_nascimento]?>" readonly="readonly" type="text" class="inputSmall" style="float:left;"/><img src="gfx/ico/calendar.gif" style="cursor:pointer; float:left;" align="absmiddle" id="btCalendarData" name="btCalendarData" /></div><br clear="all" />
    
	<div class="field">
		<label>Acesso aos módulos</label><br />
		<?
		$db->Query("SELECT id_modulo FROM cms_usuarios_modulos WHERE id_usuario = '$_REQUEST[id]'");
		while ($moduloSelecionado = $db->RowArray()) {
			$modulosSelecionados[] = $moduloSelecionado['id_modulo'];
		}
		
		$db->Query("SELECT * FROM cms_modulos WHERE ativo = 's' AND sub = 'n' ORDER BY ordem ASC");
		?>
		<?
		$nTd=0;
		$nCollumns=3;
		while ($modulo = $db->RowArray()) {
			$checked = @in_array($modulo['id_modulo'],$modulosSelecionados)?"checked=\"checked\"":"";
			echo "<div style=\"float:left; width:200px;\"><label class=\"normal\"><input type=\"checkbox\" name=\"id_modulo[]\" title=\"Selecione os cms_modulos para este usuário\" $checked value=\"$modulo[id_modulo]\"> $modulo[label]</label></div>";
			$nTd++;
			if ($nTd%$nCollumns==0) {
				echo "<br clear=\"all\">";
			}
		}
		echo "<br clear=\"all\">";
		echo "<br clear=\"all\">";
		?>
	</div>
     
     
	<div class="field"><label>Usuário</label><br /><input name="user" type="text" id="user" value="<?=$row[user]?>" class="inputMedium required"  title="Digite o usuario para acesso" /></div>
	
	<div class="field" <?=$_REQUEST[action]=="alt"?"":"style=\"display:none;\"";?>><label>Alterar senha</label><br /><input name="alterar_senha" type="checkbox" id="alterar_senha" value="s" onchange="$('#alteracao_senha').toggle();" title="Clique aqui se deseja alterar a senha deste usuário"/></div>
	<div id="alteracao_senha" <?=$_REQUEST[action]=="alt"?"style=\"display:none;\"":"";?>>
		<div class="field"><label>Senha</label><br /><input name="pwd1" type="password" id="pwd1" value="" class="inputMedium required senha" title="Digite a senha" /></div>
		<div class="field"><label>Confirme a senha</label><br /><input name="pwd2" type="password" id="pwd2" value="" class="inputMedium required senha" title="Digite a senha novamente para confirmação" /></div>
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

<script type="text/javascript">
	var cal = Calendar.setup({
	  onSelect: function(cal) { cal.hide() }
	});
	cal.manageFields("btCalendarData", "data_nascimento", "%d/%m/%Y");
</script>


<? AdminKernel::showBtVoltar(); ?>