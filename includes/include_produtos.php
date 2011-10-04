<?php
if( !(int)$_GET['cat'] && !(int)$_GET['subc'] )
	Page::$gpage->MarkRedirect()->ThrowReturn();


global $id_cat, $id_subc;

if( isset($_GET['cat']) )
{
	$id_cat = intval($_GET['cat']);
	$fetch_cat = db::row('produtos_categorias', '*', array('id_categoria' => $id_cat));
	$fetch_subc = db::row('produtos_subcategorias', '*', array('id_categoria' => $id_cat), 'nome');
	
	if( !empty($fetch_subc) ) {
		$id_subc = $fetch_subc['id_subcategoria'];
	} else {
		$view_noresult = true;
		$view_nosubcat = true;
		//Page::$gpage->MarkRedirect()->ThrowReturn();
	}
} else {
	$id_subc = intval($_GET['subc']);
	$fetch_subc = db::row('produtos_subcategorias', '*', array('id_subcategoria' => $id_subc));
	if( empty($fetch_subc) )
		Page::$gpage->MarkRedirect()->ThrowReturn();
	$id_cat = $fetch_subc['id_categoria'];
	$fetch_cat = db::row('produtos_categorias', '*', array('id_categoria' => $id_cat));
}

if( !$view_noresult )
{
	switch( intval($_GET['order']) )
	{
	case 0 : $order = 'vendas DESC, nome'; break;
	case 1 : $order = 'preco_regard DESC'; break;
	case 2 : $order = 'preco_regard'; break;
	case 3 : $order = 'nome'; break;
	case 4 : $order = 'nome DESC'; break;
	}
	$sql = "SELECT *, 
				ROUND(IF(flag_promocao=1, preco_promocional, preco), 2) AS preco_regard 
			FROM produtos 
			WHERE 
				id_subcategoria = {$id_subc} 
			ORDER BY $order";
	
	$res = db::query($sql);
	$view_noresult = db::affected()==0;
}
?>

<span id="produtos">


<div class="content">
	<?php
	if( $view_nosubcat )
		$crumb_label = $fetch_cat['nome'];
	else {
		$crumb_label = $fetch_subc['nome'];
		$crumb_sublabel = array($fetch_cat['nome'] => '?p=produtos&cat='.$fetch_cat['id_categoria']);
	}
	include 'site_crumb.php';
	?>
	
	<?php if( $view_noresult ) { ?>
		Não possuímos nenhum produto nesta categoria.
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