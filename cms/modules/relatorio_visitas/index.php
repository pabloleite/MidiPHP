<?
	require("includes/requires.php");
	$db = new MySQL(true, $config["db_name"], $config["db_host"], $config["db_user"], $config["db_password"], '', true);
	
	AdminKernel::showModuleTitle("Relatório de visitas");
	
	$db->Query("SELECT *, DATE_FORMAT(publicationDate, '%Y%m%d') AS unslashedPublicationDate, DATE_FORMAT(publicationDate, '%d/%m/%Y') AS formatedPublicationDate FROM cms_config LIMIT 1");
	$config = $db->RowArray();
	
	$db->Query("SELECT * FROM cms_relatorio_acessos LIMIT 1");
	$relatorio = $db->RowArray();
?>
<h2>Relatório de visitas do site - <a href="<?=$config["siteUrl"]?>" target="_blank"><?=$config["enterpriseName"]?></a></h2>
<p>
	Data de publicação do site: <strong><?=$config["formatedPublicationDate"];?></strong><br />
<?php /*?>	Número de acessos: <strong><?=(int)$relatorio["acessos"];?></strong><br />
	Páginas visitadas: <strong><?=(int)$relatorio["pageviews"];?></strong><br />
    Média de <strong><?=(int)$relatorio["acessos"] > 0 ? number_format(((int)$relatorio["pageviews"]) / ((int)$relatorio["acessos"]),2,',','') : "0";?></strong> visualizações de página por visita.<br />
<?php */?>
</p>

<p><hr /></p>

Clique no link abaixo e acesse os relatórios detalhados de seu site através da ferramenta <em>Google Analytics.</em>
<p><a href="<?=$config["analyticsUrl"]?>&pdr=<?php echo date('Ym');?>01-<?=date('Ymd')?>" target="_blank"><img src="gfx/ico/ga.jpg" /></a></p>
