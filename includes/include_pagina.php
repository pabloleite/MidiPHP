<?php
$page = $_GET['n'];
$rowp = db::row( 'paginas', '*', array('page' => db::escape($page), 'ativo' => 1) );
?>

<span id="paginas">


<div class="content">
	<?php
	$crumb_label = $rowp['identificacao'];
	if( $page!='institucional' && $page!='politica' && $page!='lojas' )
		$crumb_sublabel = array('Atendimento' => '?p=atendimento');
	include 'site_crumb.php';
	?>
	
	<div id="text" class="textwall-normal">
		
		<?php
		if( !empty($rowp['imagem_conteudo']) )
			printf( '<img src="%s" id="img-block" />', SiteConstants::DIR_USERFILES.'/paginas/'.$rowp['imagem_conteudo'] );
		
		if( !empty($rowp['titulo']) )
			echo '<div id="title"><h1>'.$rowp['titulo'].'</h1></div>';
		
		echo $rowp['texto'];
		?>
	
	</div>
	
</div>


</span>