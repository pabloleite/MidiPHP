<?
global $db;
require("includes/pagination/pagination.class.php");

$db->Query("SELECT * FROM $mainSession WHERE rel = '{$_REQUEST['rel']}' AND id_rel = '{$_REQUEST['id_rel']}'");
$totalListRows = $db->RowCount();
?>

<?=AdminKernel::getIncLink();?>	
<?=AdminKernel::translateMsgInfo();?>

<?
if( $totalListRows>0 ) {
?>
	<div id="listInfo"> Total de fotos nesta galeria: <strong><?=(int)$totalListRows?></strong></div>
    <br clear="all" />
	
	<form name="multipleExclusionForm" id="multipleExclusionForm" method="post" enctype="multipart/form-data">
		<?
		$tdId = 0;
		foreach( $db->RecordsArray() as $rowf )
		{
		?>
			
			<table width="140" border="0" cellspacing="0" cellpadding="3" style="float:left;">
				<tr>
					<td colspan="2" align="center"><?=AdminKernel::getListDataImage($config["userfiles_path"]."/".$rowf['rel']."/".$rowf["arquivo"],130,130)?></td>
				</tr>
				<tr>
					<td colspan="2" align="center"><?=$rowf['legenda']?></td>
				</tr>
				<tr>
					<td align="left"><input type="checkbox" name="multipleExclusion[]" id="multipleExclusion_<?=$tdId?>" value="<?=$rowf[$primaryKey]?>" /></td>
					<td align="right"><?=AdminKernel::getAltLink($rowf[$primaryKey])?> <?=AdminKernel::getExcLink($rowf[$primaryKey])?></td>
				</tr>
			</table>
			
		<?
			$tdId++;
			if ($tdId%6==0)
				echo '<br clear="all">';
		}
		?>
		
	  <input type="hidden" name="action" value="excmultiple" />
	</form>
	<br clear="all" /><br clear="all" />
	<div id="multipleExclusion"><?=AdminKernel::getMultipleExclusionLink()?></div>
<?
} else {
	AdminKernel::showMsgInfo("Não há fotos cadastradas nesta galeria.");
}
?>
<? AdminKernel::showBtVoltarHome(); ?>