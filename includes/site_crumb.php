<?php
/*
Exemplo de uso:

$crumb_label = $fetch_prod['nome'];
$crumb_sublabel = array($fetch_cat['nome'] => '?p=produtos&cat='.$fetch_cat['id_categoria'], $fetch_subc['nome'] => '?p=produtos&subc='.$fetch_subc['id_subcategoria']);
include 'site_crumb.php';
*/
$crumb_sublabel = (array) $crumb_sublabel;
foreach( $crumb_sublabel as $print => $mix )
{
	$bar_entire .= is_string($print) ? ('<a href="'.$mix.'" class="active">'.$print.'</a>') : ('<a class="active">'.$mix.'</a>');
	$bar_entire .= '<span> >> </span>';
}

$bar_entire = '<span>>></span> '.$bar_entire.'<a class="active">'.$crumb_label.'</a>';
?>
<div id="strip-crumb">
	<a href=".">Rumocerto</a> <?php echo $bar_entire; ?>
</div>