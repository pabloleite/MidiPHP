<?
global $db;
require("includes/pagination/pagination.class.php");

if($_REQUEST['action']=='filter')
{
	$fields = array("$mainSession.codigo", 
					"$mainSession.nome", 
					"$mainSession.descricao_breve", 
					"$mainSession.descricao" );
	$query = trim(mysql_real_escape_string(stripslashes($_REQUEST['filt_text'])));
	$sql_where = "WHERE " .implode(" LIKE '%" .$query. "%' OR ", $fields) . " LIKE '%$query%' ";
}

$db->Query("SELECT COUNT(*) FROM $mainSession $sql_where");
$totalListRows = end($db->RowArray());
$pagination = new Pagination($_SESSION['user_profile']['resultsPerPage'], 10, $totalListRows);
?>

<?=AdminKernel::getIncLink();?>

<?
if ($totalListRows > 0) {
	$db->Query("SELECT $mainSession.*, produtos_subcategorias.nome AS nome_subcategoria, produtos_categorias.nome AS nome_categoria
				FROM $mainSession 
					LEFT JOIN produtos_subcategorias ON($mainSession.id_subcategoria = produtos_subcategorias.id_subcategoria) 
					LEFT JOIN produtos_categorias ON(produtos_subcategorias.id_categoria = produtos_categorias.id_categoria) 
				$sql_where 
				".$pagination->getSqlOrder($mainSession, $primaryKey)." ".$pagination->getPaginationSqlLimit());
	?>
	
	<form name="multipleExclusionForm" id="multipleExclusionForm" method="post" enctype="multipart/form-data">
		<input type="hidden" name="ir" value="<?php echo $_GET['ir']; ?>" />
		<style type="text/css">
			#filter_wraper { margin: 10px 0; }
			#filter_wraper input[type="text"] { border: solid 1px #c3c3c3; height: 24px; line-height: 24px; vertical-align: top; }
		</style>
		
		<div id="filter_wraper">
			<label><strong>&nbsp;Busca textual</strong></label><br />
			<input name="filt_text" type="text" value="<?php echo $_REQUEST['filt_text']; ?>" />
			<input type="image" src="gfx/search.png" style="margin-top: -2px; margin-left: 2px;" onclick="$('[name=\'action\']', form).val('filter');" />
		</div>
		
		<hr />
		<?=AdminKernel::translateMsgInfo();?>
		
		<div id="listInfo"><span class="maior">Página <strong><?=$_REQUEST['pag']?></strong></span> (Mostrando registro <strong><?=$pagination->startRow+1?></strong> a <strong><?=$pagination->startRow + $db->RowCount()?></strong> de um total de <strong><?=$pagination->totalRows?></strong>)</div>
		<div id="alterResultsPerPage">
			Mostrar <?=$pagination->getPaginationSelectResults();?> registros por página
		</div>
		<table width="100%" border="0" cellpadding="5" cellspacing="0" id="listData">
		  <tr>
			<th width="21"><input type="checkbox" name="checkUncheck" id="checkUncheck" onchange="checkUncheckAll(this.form, 'multipleExclusion[]');" /></th>
			<th width="70"><?=AdminKernel::getThOrderLabel("Código", "$mainSession.codigo");?></th>
			<th><?=AdminKernel::getThOrderLabel("Nome", "$mainSession.nome");?></th>
			<th><?=AdminKernel::getThOrderLabel("Categoria / Sub-categoria", "produtos_subcategorias.id_categoria");?></th>
			<th width="110"> </th>
		  </tr>
			<?
			$trId = 0;
			foreach( $db->RecordsArray() as $row )
			{
			?>
				<tr class="<?="listData_tr".($trId%2)?>" ondblclick="window.location='<?=AdminKernel::getAltHref($row[$primaryKey]);?>';" onmouseover="$(this).toggleClass('listData_highlight');" onmouseout="$(this).toggleClass('listData_highlight');">
					<td><input type="checkbox" name="multipleExclusion[]" id="multipleExclusion_<?=$trId?>" value="<?=$row[$primaryKey]?>" /></td>
					<td><strong><?=$row['codigo']?></strong></td>
					<td><strong><?=$row['nome']?></strong></td>
					<td><strong><?=$row['nome_categoria']?>, <?=$row['nome_subcategoria']?></strong></td>
					<td align="center">
						<?php
						$link = '../?p=detalhes&prod=' . $row[$primaryKey];
						?>
						<a href="<?php echo $link; ?>" target="_blank" title="Visualizar produto no site"><img src="gfxnew/eye.png" /></a>
						<a href="?ir=fotos&rel=produtos&id_rel=<?=$row[$primaryKey]?>" title="Galeria de Fotos"><img src="gfx/ico/images.gif" width="20" height="20" /></a>
						<?=AdminKernel::getAltLink($row[$primaryKey])?>
						<?=AdminKernel::getExcLink($row[$primaryKey])?>
					</td>
				</tr>
			<?
				$trId++;
			}
			?>
		</table>
	  <input type="hidden" name="action" value="excmultiple" />
	</form>
	
	<div id="multipleExclusion"><?=AdminKernel::getMultipleExclusionLink()?></div>
	
	<div id="paginacao">
		<?=$pagination->getPagination();?>
	</div>
<?
} else {
	AdminKernel::showMsgInfo("Não há produtos cadastrados. Para cadastrar, clique no botão acima.");
}
?>

<? AdminKernel::showBtVoltarHome(); ?>