<?
global $db;
require("includes/pagination/pagination.class.php");

$db->Query("SELECT COUNT(*) FROM $mainSession");
$totalListRows = end($db->RowArray());
$pagination = new Pagination($_SESSION['user_profile']['resultsPerPage'], 10, $totalListRows);
?>

<?
if ($totalListRows > 0) {
	$db->Query("SELECT $mainSession.* 
					FROM $mainSession 
					".$pagination->getSqlOrder($mainSession, $primaryKey)." ".$pagination->getPaginationSqlLimit());
	?>
	
	<?=AdminKernel::translateMsgInfo();?>
	
	<div id="listInfo"><span class="maior">Página <strong><?=$_REQUEST['pag']?></strong></span> (Mostrando registro <strong><?=$pagination->startRow+1?></strong> a <strong><?=$pagination->startRow + $db->RowCount()?></strong> de um total de <strong><?=$pagination->totalRows?></strong>)</div>
	<div id="alterResultsPerPage">
		Mostrar <?=$pagination->getPaginationSelectResults();?> registros por página
	</div>
	
	<form name="multipleExclusionForm" id="multipleExclusionForm" method="post" enctype="multipart/form-data">
		<table width="100%" border="0" cellpadding="5" cellspacing="0" id="listData">
		  <tr>
			<th width="120">Imagem</th>
			<th><?=AdminKernel::getThOrderLabel("Identificação da página", "$mainSession.content_title");?></th>
			<th width="30">&nbsp;</th>
		  </tr>
		<?
		  $trId = 0;
		  foreach( $db->RecordsArray() as $row )
		  {
		?>
			 <tr class="<?="listData_tr".($trId%2)?>" ondblclick="window.location='<?=AdminKernel::getAltHref($row[$primaryKey]);?>';" onmouseover="$(this).toggleClass('listData_highlight');" onmouseout="$(this).toggleClass('listData_highlight');">
				<td><?=AdminKernel::getListDataImage($config["userfiles_path"]."/".$mainSession."/".$row['imagem_conteudo'],120,120)?></td>
                <td><strong><?=$row['identificacao']?></strong></td>
				<td align="center">
					<?=AdminKernel::getAltLink($row[$primaryKey])?>
				</td>
		  </tr>
		<?
			$trId++;
		  }
		?>
		</table>
	  <input type="hidden" name="action" value="excmultiple" />
	</form>
    
	<div id="paginacao">
		<?=$pagination->getPagination();?>
	</div>
<?
} else {
	AdminKernel::showMsgInfo("Não há páginas cadastradas.");
}
?>



<? AdminKernel::showBtVoltarHome(); ?>