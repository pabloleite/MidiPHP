<?php
switch( intval($_GET['order']) )
{
case 0 : $order = 'vendas DESC, nome'; break;
case 1 : $order = 'preco_regard DESC'; break;
case 2 : $order = 'preco_regard'; break;
case 3 : $order = 'nome'; break;
case 4 : $order = 'nome DESC'; break;
}

$text_query = trim($_GET['q']);
$html_query = htmlspecialchars(stripslashes($text_query));
$view_noresult = empty($text_query);

if( !$view_noresult )
{
	$sql_query1 = db::escape($text_query);
	$sql_query2 = db::escape(htmlentities($text_query));
	$table_name = ShopDBConsts::TABLE_PRODUCTS;
	
	// Enumera em uma array os campos FULLTEXT
	$target_fields = array();
	$sql = "SHOW INDEX FROM {$table_name}";
	foreach( db::query($sql) as $column_data )
	{
		if( strcasecmp($column_data['Index_type'], 'FULLTEXT')==0 )
			$target_fields[] = $table_name.'.'.$column_data['Column_name'];
	}
	
	// Search SQL query
	if( is_numeric($_GET['target']) && $_GET['target']!=0 )
	{
		$cat_join = 'INNER JOIN produtos_subcategorias USING(id_subcategoria) ';
		$cat_where = 'id_categoria = '.intval($_GET['target']).' AND';
	}
	
	$field_str = implode(',', $target_fields);
	$sql = "SELECT produtos.*, 
				ROUND(IF(flag_promocao=1, preco_promocional, preco), 2) AS preco_regard, 
				MATCH($field_str) AGAINST('$sql_query1 $sql_query2' IN BOOLEAN MODE) AS score 
			FROM {$table_name} 
				$cat_join 
			WHERE 
				$cat_where 
				(MATCH($field_str) AGAINST('$sql_query1 $sql_query2' IN BOOLEAN MODE) 
				OR {$target_fields[1]} LIKE '%{$sql_query1}%') 
			ORDER BY {$order}";
	$res = db::query($sql);
	$view_noresult = db::affected()==0;
}
?>

<span id="produtos">


<div class="content">
	<?php
	$crumb_label = $html_query;
	$crumb_sublabel = 'Busca';
	include 'site_crumb.php';
	?>
	
	<?php if( $view_noresult ) { ?>
		Sua busca não obteve resultados.
	<?php } else { ?>
	
		<div id="order-by">
			<?php
			$order_url = server::get('request_uri');
			$order_url = str_replace('&order='.$_GET['order'], '', $order_url);
			$order_url .= '&order=';
			?>
			<a <?php if($_GET['order']==0) echo 'class="active"'; ?> href="<?php echo $order_url; ?>0">MAIS VENDIDOS</a><img src="<?php echo DisplayLayer::LayoutImagePath('public/images/icon_dot.png'); ?>" />
			<a <?php if($_GET['order']==1) echo 'class="active"'; ?> href="<?php echo $order_url; ?>1">MAIOR PREÇO</a><img src="<?php echo DisplayLayer::LayoutImagePath('public/images/icon_dot.png'); ?>" />
			<a <?php if($_GET['order']==2) echo 'class="active"'; ?> href="<?php echo $order_url; ?>2">MENOR PREÇO</a><img src="<?php echo DisplayLayer::LayoutImagePath('public/images/icon_dot.png'); ?>" />
			<a <?php if($_GET['order']==3) echo 'class="active"'; ?> href="<?php echo $order_url; ?>3">A a Z</a><img src="<?php echo DisplayLayer::LayoutImagePath('public/images/icon_dot.png'); ?>" />
			<a <?php if($_GET['order']==4) echo 'class="active"'; ?> href="<?php echo $order_url; ?>4">Z a A</a>
		</div>
		
		Resultado da busca por <u><?php echo $html_query; ?></u>:<br />
		
		<ul class="for-product-list">
			<?php
			$in = 0;
			foreach( $res as $rowp )
			{
				$thumb_path = SiteConstants::DIR_USERFILES.'/produtos/'.$rowp['imagem'];
				$thumb_url = SiteConstants::PATH_PHPTHUMB.'?' . sprintf('src=%s&w=%d&h=%d', $thumb_path, 140, 115);
			?>
				<a href="?p=detalhes&prod=<?php echo $rowp['id_produto']; ?>">
					<div id="thumb" style="background-image: url(<?php echo $thumb_url; ?>);"></div>
					<div id="sub-height">
						<div id="label-name"><?php echo $rowp['nome']; ?></div>
						<?php
						// div Marca
						if( $f=db::field('marcas', 'nome', array('id_marca' => $rowp['id_marca'])) )
							echo '<div id="label-brand">'.$f.'</div>';
						// div Preço
						DisplayLayer::DivBoxPreco($rowp);
						?>
					</div>
					<img src="<?php echo DisplayLayer::LayoutImagePath('public/images/btn_comprar.png'); ?>" />
				</a>
			<?php
				if( ++$in%4==0 ) echo '<br clear="all" />';
			}
			?>
		</ul>
	<?php } ?>
</div>


</span>