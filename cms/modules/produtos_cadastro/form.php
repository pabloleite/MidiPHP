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

<script type="text/javascript">
$(function() {
	$('#descricao, #descricao_breve').wymeditor({
		lang: 'pt-br',
		logoHtml: '',
		boxHtml: "<div class='wym_box'>"
				  + "<div class='wym_area_top'>"
				  + WYMeditor.TOOLS
				  + "</div>"
				  + "<div class='wym_area_left'></div>"
				  + "<div class='wym_area_right'>"
				  //+ WYMeditor.CONTAINERS
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
	
	$('#tamanho-checklist, #relp-checklist').toChecklist({ preferIdOverName : false });
});
</script>

<div id="validateFormError" style="display:none;">Preencha corretamente o campo.</div>
<form name="oper" id="oper" enctype="multipart/form-data" method="post" onsubmit="return validateForm(this);">
	<div class="field">
		<label>Categoria</label><br />
		<select name="categoria" style="width: 150px;">
			<?php
			$db->SelectTable( 'produtos_categorias' );
			$fetch_categorias = $db->RecordsArray();
			foreach( $fetch_categorias as $rowc )
			{
				$db->SelectRows( 'produtos_subcategorias', array('id_categoria' => MySQL::SQLValue($rowc['id_categoria'])) );
				$fetch_subcategorias[] = $db->RecordsArray();
			}
			
			$ic = -1;
			foreach( $fetch_categorias as $rowc )
			{
				if( !$fetch_subcategorias[++$ic] )
					continue;
				
				$s = '';
				foreach( $fetch_subcategorias[$ic] as $rows )
					$s .= sprintf('<option value="%d" %s>%s</option>', $rows['id_subcategoria'], ($alt && $row['id_subcategoria']==$rows['id_subcategoria']) ? 'selected="selected"' : '', $rows['nome']);
				printf( '<optgroup label="%s">%s</optgroup>', $rowc['nome'], $s );
			}
			?>
		</select>
	</div>
    
    <div class="field">
		<label>Marca</label><br />
		<select name="marca" style="width: 150px;">
			<?php
			$db->SelectTable('marcas');
			foreach($db->RecordsArray() as $rowc) {
				printf('<option value="%d" %s>%s</option>', $rowc['id_marca'], ($alt && $row['id_marca'] == $rowc['id_marca']) ? 'selected="selected"' : '', $rowc['nome'] );
			}
			?>
		</select>
	</div>
    
	<div class="field"><label for="codigo">Código</label><br /><input name="codigo" type="text" id="codigo" value="<?=$row[codigo]?>" class="inputMedium" title="Código do produto" /></div>
	<div class="field"><label for="nome">Nome do Produto</label><br /><input name="nome" type="text" id="nome" value="<?=$row[nome]?>" class="inputBig required" title="Nome do produto" /></div>
	
	<div class="field">
		<?php
		$attrb_set = $db->QuerySingleValue("SELECT ativo FROM produtos_atributos WHERE identificacao = 'tamanho'");
		if( $attrb_set )
		{
		?>
		<div style="float: left; margin-right: 15px;">
			<label>Atributo - tamanhos</label><br />
			<select name="tamanho" id="tamanho-checklist" multiple="multiple" style="width: 190px; height: 220px;">
				<?php
				if( $alt )
					$arr_attrs = array_flip(explode(',', $row['rel_atributo_tamanho']));
				$db->SelectTable('produtos_atributo_tamanho');
				foreach( $db->RecordsArray() as $rowa )
					printf( '<option value="%d" %s>%s</option>', $rowa['id_atributo'], ($alt && isset($arr_attrs[ $rowa['id_atributo'] ])) ? 'selected="selected"' : '', $rowa['nome'] )
				?>
			</select>
		</div>
		<?php
		}
		?>
		
		
		<?php
		$attrb_set = $db->QuerySingleValue("SELECT ativo FROM produtos_atributos WHERE identificacao = 'rel_produtos'");
		if( $attrb_set )
		{
		?>
		<div style="float: left; margin-right: 15px;">
			<label>Produtos relacionados</label><br />
			<select name="rel_produtos" id="relp-checklist" multiple="multiple" style="width: 190px; height: 220px;">
				<?php
				if( $alt )
					$arr_rel = array_flip(explode(',', $row['rel_produtos']));
				$db->SelectTable( 'produtos_categorias' );
				foreach( $db->RecordsArray() as $rowc )
				{
					$db->SelectRows( 'produtos_subcategorias', array('id_categoria' => MySQL::SQLValue($rowc['id_categoria'])) );
					foreach( $db->RecordsArray() as $rows )
					{
						$categorias[] = $rowc['nome'].' - '.$rows['nome'];
						$fetch_produtos[] = $db->QueryArray("SELECT * FROM produtos WHERE id_subcategoria = {$rows['id_subcategoria']}" . ($alt ? " AND $primaryKey <> '{$_REQUEST['id']}'" : '') );
					}
				}
				
				$ic = -1;
				foreach( $categorias as $nomec )
				{
					if( !$fetch_produtos[++$ic] )
						continue;
					
					$s = '';
					foreach( $fetch_produtos[$ic] as $rowp )
						$s .= sprintf('<option value="%d" %s>%s</option>', $rowp[$primaryKey], ($alt && isset($arr_rel[ $rowp[$primaryKey] ])) ? 'selected="selected"' : '', $rowp['nome']);
					printf( '<optgroup label="%s">%s</optgroup>', $nomec, $s );
				}
				?>
			</select>
		</div>
		<?php
		}
		?>
		
		<br clear="all" />
	</div>
	
	<div class="field"><label>Imagem</label>
		<?=AdminKernel::getListDataImage($config["userfiles_path"]."/".$mainSession."/".$row["imagem"])?><br />
		<input type="file" name="imagem1" id="imagem" class="radio" title="Selecione um imagem" value="" />
		<?php if ($_REQUEST['action']=='alt') { ?>
			<br /><label class="normal"><input type="radio" name="manterArquivo1" id="manterArquivo1" value="true" checked="checked" title="Clique aqui para manter o arquivo"/>Manter arquivo</label><br />
			<label class="normal"><input type="radio" name="manterArquivo1" id="manterArquivo1" value="false" title="Clique aqui para apagar o arquivo"/>Apagar arquivo</label>
		<?php } ?>
	</div>
	
	<div class="field"><label>Descrição</label><br /><textarea id="descricao" name="descricao" rows="8" cols="80" style="width: 100%;"><?=$row[descricao]?></textarea></div>
	
	<?php
	$attrb_set = $db->QuerySingleValue("SELECT ativo FROM produtos_atributos WHERE identificacao = 'rel_produtos'");
	if( $attrb_set )
	{
	?>
		<div class="field"><label>Descrição curta</label><br /><textarea id="descricao_breve" name="descricao_breve" rows="3" cols="80" style="width: 100%;"><?=$row[descricao_breve]?></textarea></div>
	<?php
	}
	?>
	
	<hr /><br />
	<div class="field" style="float: left; margin-top: 0; margin-right: 7px;"><label for="peso">Peso (gramas)</label><br /><input name="peso" type="text" id="peso" value="<?=$row[peso]?>" class="inputSmall" title="Peso do produto" /></div>
	<div class="field" style="float: left; margin-top: 0; margin-right: 7px;"><label for="volume">Volume (litros)</label><br /><input name="volume" type="text" id="volume" value="<?=$row[volume]?>" class="inputSmall" title="Volume do produto" /></div>
	<br clear="all" />
	<div class="field"><label for="estoque">Quantidade em estoque</label><br /><input name="estoque" type="text" id="estoque" value="<?=(int) $row[estoque]?>" class="inputMedium required" title="Código do Produto" /></div>
	<div class="field" style="float: left; margin-top: 0; margin-right: 7px;"><label for="preco">Preço</label><br /><input name="preco" type="text" id="preco" value="<?=FormatLayer::FormatFloatStr($row[preco]); ?>" class="inputMedium required" title="Preço do Produto" /></div>
	<div class="field" style="float: left; margin-top: 0; margin-right: 7px;"><label for="preco_promocional">Preço promocional</label><br /><input name="preco_promocional" type="text" id="preco_promocional" value="<?=FormatLayer::FormatFloatStr($row[preco_promocional]); ?>" class="inputMedium" title="Preço promocional do Produto" /></div>
	<br clear="all" />
	<div class="field" style="margin-top: 0;"><label>Ativar promoção?</label><br /><input type="radio" name="flag_promocao" <?php if($row['flag_promocao']==0) echo 'checked="checked"'; ?> value="0" />Não <input type="radio" name="flag_promocao" <?php if($row['flag_promocao']==1) echo 'checked="checked"'; ?> value="1" />Sim</div>
	<div class="field" style="margin-top: 0;"><label>Em destaque? (aparece nas listagens de destaque da página inicial)</label><br /><input type="radio" name="flag_destaque" <?php if($row['flag_destaque']==0) echo 'checked="checked"'; ?> value="0" />Não <input type="radio" name="flag_destaque" <?php if($row['flag_destaque']==1) echo 'checked="checked"'; ?> value="1" />Sim</div>
	<br clear="all" />
	
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