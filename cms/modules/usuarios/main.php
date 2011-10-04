<?
global $db;
require("includes/pagination/pagination.class.php");

$db->Query("SELECT COUNT(*) FROM $mainSession");
$totalListRows = end($db->RowArray());
$pagination = new Pagination($_SESSION['user_profile']['resultsPerPage'], 10, $totalListRows);
?>

<?=AdminKernel::getIncLink();?>
<?=AdminKernel::translateMsgInfo();?>

<?
if ($totalListRows > 0) {
	$db->Query("SELECT $mainSession.*
					FROM $mainSession 
					".$pagination->getSqlOrder($mainSession, $primaryKey)." ".$pagination->getPaginationSqlLimit());
	?>
	<div id="listInfo"><span class="maior">Página <strong><?=$_REQUEST['pag']?></strong></span> (Mostrando registro <strong><?=$pagination->startRow+1?></strong> a <strong><?=$pagination->startRow + $db->RowCount()?></strong> de um total de <strong><?=$pagination->totalRows?></strong>)</div>
	<div id="alterResultsPerPage">
		Mostrar <?=$pagination->getPaginationSelectResults();?> registros por página
	</div>
	
	<form name="multipleExclusionForm" id="multipleExclusionForm" method="post" enctype="multipart/form-data">
		<table width="100%" border="0" cellpadding="5" cellspacing="0" id="listData">
		  <tr>
			<th width="21"><input type="checkbox" name="checkUncheck" id="checkUncheck" onchange="checkUncheckAll(this.form, 'multipleExclusion[]');" /></td>
			<th width="120"><?=AdminKernel::getThOrderLabel("Nome", "$mainSession.nome");?></td>
			<th><?=AdminKernel::getThOrderLabel("Contato", "1");?></td>
			<th width="70"> </th>	  
		  </tr>
		<?
		  $trId = 0;
		  while ($row = $db->RowArray()) {
		?>
			 <tr class="<?="listData_tr".($trId%2)?>" ondblclick="window.location='<?=AdminKernel::getAltHref($row[$primaryKey]);?>';" onmouseover="$(this).toggleClass('listData_highlight');" onmouseout="$(this).toggleClass('listData_highlight');">
				<td><input type="checkbox" name="multipleExclusion[]" id="multipleExclusion_<?=$trId?>" value="<?=$row[$primaryKey]?>" /></td>
				<td><?=$row['nome']?></td>
				<td>
					<?
                    if ($row['telefone1']!='') {echo $row['telefone1']."<br />";}
					if ($row['email']!='') {echo "<a href=\"mailto:$row[email]\">".$row[email]."</a>";}
					?>
                </td>
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
	AdminKernel::showMsgInfo("Não há usuários cadastrados.");
}
?>

<? AdminKernel::showBtVoltarHome(); ?>