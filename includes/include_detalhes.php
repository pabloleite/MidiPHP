<?php
if( !(int)$_GET['prod'] )
	Page::$gpage->MarkRedirect()->ThrowReturn();

$id_prod = intval($_GET['prod']);
$fetch_prod = db::row(ShopDBConsts::TABLE_PRODUCTS, '*', array('id_produto' => $id_prod));
if( !$fetch_prod )
	Page::$gpage->MarkRedirect()->ThrowReturn();
$fetch_subc = db::row('produtos_subcategorias', '*', array('id_subcategoria' => $fetch_prod['id_subcategoria']));
$fetch_cat = db::row('produtos_categorias', '*', array('id_categoria' => $fetch_subc['id_categoria']));
$id_subc = $fetch_subc['id_subcategoria'];
$id_cat = $fetch_cat['id_categoria'];

$list = Cart::GetOrderList();
$item = Cart::$gcart->GetItem($id_prod);
$view_stock = $item->GetAvailable();
$view_incart = isset($list[$id_prod]);
$view_isavail = $view_stock > 0;

$attrib_size = KudosFunctions::ProductAttribIsActive('tamanho') && !empty($fetch_prod['rel_atributo_tamanho']);
?>

<script type="text/javascript" src="public/js/cloud-zoom.1.0.2.js"></script>
<script type="text/javascript">
$(function() {

<?php if( $view_isavail && !$view_incart ) { ?>
	$('#mode-anchor').click(function() {
		<?php if($attrib_size) { ?>
		var attrib_size_val = $('#attrib-size option:selected').val();
		if( attrib_size_val=='' )
		{
			$('#msg-size').show();
			return false;
		} else {
			$('#attrib-size').hide();
			$('#msg-size').hide();
		}
		<?php } ?>
		
		var _anchor = $(this).hide();
		var _loader = _anchor.next().show();
		
		$.getJSON(
			'public/ajax/item_insert.php', 
			{
				<?php if($attrib_size) { ?>
				attrib_size: attrib_size_val, 
				<?php } ?>
				func: 'InsertProduct', 
				id: <?php echo $id_prod; ?>
			}, 
			function(data) {
				_loader.delay(600).queue(function() {
					_loader.dequeue();
					_loader.hide();
					_loader.next().show();
					
					$('#qty-box span').text( parseInt(data.cart_count) );
				});
			}
		);
		
		return false;
	});
<?php } ?>
	
	var onCompleteZoom = function(current) {
		$('.slides_container a').eq(current-1).CloudZoom({
			position: 'inside',
			showTitle: false
		});
	};
	
	$('#image-central').slides({
		//preload: true,
		//preloadImage: 'public/images/icon_loading_central.gif',
		effect: 'slide, fade',
		crossfade: true,
		slideSpeed: 350,
		fadeSpeed: 500,
		generatePagination: false,
		animationStart: function(index) {
			$('.slides_container a').eq(index-1).data('zoom').destroy();
		},
		animationComplete: onCompleteZoom
	});
	onCompleteZoom(1);
	
});
</script>

<span id="detalhes">


<div class="content">
	<?php
	$crumb_label = $fetch_prod['nome'];
	$crumb_sublabel = array($fetch_cat['nome'] => '?p=produtos&cat='.$fetch_cat['id_categoria'], $fetch_subc['nome'] => '?p=produtos&subc='.$fetch_subc['id_subcategoria']);
	include 'site_crumb.php';
	?>
	
	<div id="images-floatcol">
		<div id="image-central">
			<div class="slides_container">
				<?php
				$res = db::select('fotos', 'arquivo', array('rel' => 'produtos', 'id_rel' => $id_prod));
				array_unshift( $res, array('arquivo' => $fetch_prod['imagem']) );
				
				foreach( $res as $rowf )
				{
					$thumb_path = SiteConstants::DIR_USERFILES.'/produtos/'.$rowf['arquivo'];
					$thumb_url = SiteConstants::PATH_PHPTHUMB.'?' . sprintf('src=%s&w=%d&h=%d', $thumb_path, 257, 241);
					printf( '<div class="item"><a href="%s" class="cloud-zoom"><img src="%s" /></a></div>', $thumb_path, $thumb_url );
				}
				?>
			</div>
			
			<div id="slider-info">
				<img src="<?php echo DisplayLayer::LayoutImagePath('public/images/icon_zoom.png'); ?>" /> Passe o mouse para ver detalhes
			</div>
			
			<div id="pagination-bglayer">
				<ul class="pagination">
					<?php
					foreach( $res as $rowf )
					{
						$thumb_path = SiteConstants::DIR_USERFILES.'/produtos/'.$rowf['arquivo'];
						$thumb_url = SiteConstants::PATH_PHPTHUMB.'?' . sprintf('src=%s&w=%d&h=%d', $thumb_path, 61, 41);
						printf( '<li style="background-image: url(%s);"><a href=""></a></li>', $thumb_url );
					}
					?>
				</ul>
			</div>
		</div>
	</div>
	
	<div id="details-floatcol">
		<div id="info-wrap">
			<h1><?php echo $fetch_prod['nome']; ?></h1>
			<?php if( !empty($fetch_prod['codigo']) ) { ?>
				<h2>Produto: <?php echo $fetch_prod['codigo']; ?></h2>
			<?php } ?>
			
			<div id="infobox-sup">
				<?php echo DisplayLayer::DivBoxPreco($fetch_prod); ?>
			</div>
			
			<div id="infobox-inf">
				<div id="sub-wrap">
					<?php if($view_isavail) { ?>
						<?php if(!$view_incart) { ?>
						<a href="" id="mode-anchor">
							<img src="<?php echo DisplayLayer::LayoutImagePath('public/images/btn_additem.png'); ?>" />
							<div id="float-wrap">
								<div id="label-add">Adicionar ao Carrinho</div>
								<div id="label-stock"><?php echo $view_stock; ?> disponíveis</div>
							</div>
						</a>
						
						<div id="mode-loader"></div>
						<?php } ?>
						
						<div id="mode-added" <?php if($view_incart) echo 'style="display: block;"'; ?>>
							<img src="<?php echo DisplayLayer::LayoutImagePath('public/images/btn_additem.png'); ?>" />
							<div id="float-wrap">
								<div id="label">Produto adicionado!</div>
								<a href="?p=checkout1">Visualizar carrinho</a>
							</div>
						</div>
					<?php } else { ?>
						<div id="mode-nostock">
							Produto indisponível
						</div>
					<?php } ?>
				</div>
			</div>
			
			<?php if( $attrib_size && !$view_incart ) { ?>
				<div id="attrib-size">
					Escolha o tamanho: 
					<select id="attrib-size">
						<option></option>
						<?php
						foreach( explode(',', $fetch_prod['rel_atributo_tamanho']) as $id_size )
							printf( '<option value="%s">%s</option>', $id_size, db::field('produtos_atributo_tamanho', 'nome', array('id_atributo' => $id_size)) );
						?>
					</select>
				</div>
				
				<div class="model-widgets">
					<div id="msg-size" class="message-super-bar nodisplay">Escolha qual o tamanho do produto</div>
				</div>
			<?php } ?>
		</div>
		
		<div id="desc" class="textwall-normal">
			<?php if( !empty($fetch_prod['descricao']) ) { ?>
				<h3>Descrição do Produto</h3>
				<?php echo $fetch_prod['descricao']; ?>
			<?php } ?>
			
			<a href="javascript: history.back();"><img src="<?php echo DisplayLayer::LayoutImagePath('public/images/btn_label_voltar.png'); ?>" id="back" /></a>
		</div>
		
	</div>
	
	<div id="model-bot-related">
		<img src="<?php echo DisplayLayer::LayoutImagePath('public/images/model_inf_separator.png'); ?>" id="bar" />
		<img src="<?php echo DisplayLayer::LayoutImagePath('public/images/label_mprodutos.png'); ?>" />
		
		<ul class="for-product-list">
			<?php
			$sql = "SELECT * 
					FROM (
						SELECT *, IF(id_subcategoria = $id_subc, 1, 0) AS prior_scat 
						FROM ".ShopDBConsts::TABLE_PRODUCTS." 
						WHERE imagem <> '' AND imagem IS NOT NULL AND id_produto <> $id_prod 
						ORDER BY prior_scat DESC, vendas DESC, visitas DESC 
						LIMIT 4
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
</div>


</span>