<span id="home">


<div class="content">

	<div id="identif-hint" style="background-image: url(<?php echo DisplayLayer::LayoutImagePath('public/images/model_label_destaques.png'); ?>);"></div>
	
	<ul class="for-product-list">
		<?php
		$sql = "SELECT * 
				FROM (
					SELECT * 
					FROM ".ShopDBConsts::TABLE_PRODUCTS." 
					WHERE imagem <> '' AND imagem IS NOT NULL 
					ORDER BY flag_destaque DESC, vendas DESC, visitas DESC
					LIMIT 12
					) AS derived 
				ORDER BY id_marca, nome";
		
		$in = 0;
		foreach( db::query($sql) as $rowp )
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

</div>


</span>
