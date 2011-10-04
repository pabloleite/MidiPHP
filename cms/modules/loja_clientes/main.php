<?
require("includes/pagination/pagination.class.php");
$db = new MySQL(true, $config["db_name"], $config["db_host"], $config["db_user"], $config["db_password"], '', true);

if($_REQUEST['action']=='filter')
{
	$fields = array("cpf", 
					"cnpj", 
					"inscricao_estadual", 
					"ident_nome", 
					"ident_apelido", 
					"cont_telresidencial", 
					"cont_telcelular", 
					"cont_telcomercial");
	$query = trim(mysql_real_escape_string(stripslashes($_REQUEST['filt_text'])));
	$sql_where = "WHERE " .implode(" LIKE '%" .$query. "%' OR ", $fields) . " LIKE '%$query%' ";
}

$db->Query("SELECT COUNT(*) FROM $mainSession LEFT JOIN loja_clientes USING(id_cliente) $sql_where");
$totalListRows = end($db->RowArray());
$pagination = new Pagination($_SESSION['user_profile']['resultsPerPage'], 10, $totalListRows);
?>

<?
if ($totalListRows > 0) {
	$db->Query("SELECT * 
				FROM $mainSession 
				LEFT JOIN loja_clientes USING(id_cliente) 
				$sql_where
				".$pagination->getSqlOrder($mainSession, $primaryKey, 'DESC')." ".$pagination->getPaginationSqlLimit());
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
		
		<?=AdminKernel::translateMsgInfo();?>
	
		<div id="listInfo"><span class="maior">Página <strong><?=$_REQUEST['pag']?></strong></span> (Mostrando registro <strong><?=$pagination->startRow+1?></strong> a <strong><?=$pagination->startRow + $db->RowCount()?></strong> de um total de <strong><?=$pagination->totalRows?></strong>)</div>
		<div id="alterResultsPerPage">
			Mostrar <?=$pagination->getPaginationSelectResults();?> registros por página
		</div>
		
		<table width="100%" border="0" cellpadding="5" cellspacing="0" id="listData">
		  <tr>
			<th width="21"><input type="checkbox" name="checkUncheck" id="checkUncheck" onchange="checkUncheckAll(this.form, 'multipleExclusion[]');" /></th>
			<th>Nome do cliente</th>
			<th width="90">Tipo de pessoa</th>
			<th width="70">&nbsp;</th>
		  </tr>
		<?
		$trId = 0;
		foreach( $db->RecordsArray() as $row )
		{
			$cf = new ClientFetch($row);
			$row = $cf->fetch;
			
			$personalidade = $cf->GetPersonTypeStr();
		?>
			<tr class="<?="listData_tr".($trId%2)?>" ondblclick="window.location='<?=AdminKernel::getAltHref($row[$primaryKey]);?>';" onmouseover="$(this).toggleClass('listData_highlight');" onmouseout="$(this).toggleClass('listData_highlight');">
				<td><input type="checkbox" name="multipleExclusion[]" id="multipleExclusion_<?=$trId?>" value="<?=$row[$primaryKey]?>" /></td>
				<td><?php echo $row['ident_nome']; ?></td>
				<td><?php echo $personalidade; ?></td>
				<td align="center"><?=AdminKernel::getAltLink($row[$primaryKey])?> <?=AdminKernel::getExcLink($row[$primaryKey])?></td>
			</tr>
		<?
			$trId++;
		}
		?>
		</table>
	  <input type="hidden" name="action" value="excmultiple" />
	</form>
	
	<div id="multipleExclusion"><?=AdminKernel::getMultipleExclusionLink()?></div>
	<div id="paginacao"><?=$pagination->getPagination();?></div>
<?
} else {
	AdminKernel::showMsgInfo("Não foram encontrados clientes cadastrados.");
}
?>

<? AdminKernel::showBtVoltarHome(); ?>