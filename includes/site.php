<?php
$view_entire = Site::$view_props->entire_flow;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link href="data:image/x-icon;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQEAYAAABPYyMiAAAABmJLR0T///////8JWPfcAAAACXBIWXMAAABIAAAASABGyWs+AAAAF0lEQVRIx2NgGAWjYBSMglEwCkbBSAcACBAAAeaR9cIAAAAASUVORK5CYII=" rel="icon" type="image/x-icon" />
	<title><?php if(G_BUILDDEBUG) echo '{{ DEBUG }} '; ?>Rumocerto</title>
	
	<style type="text/css">
		@import "public/css/main.css";
		@import "public/css/model.css";
		@import "public/css/text.css";
		@import "public/fancybox/jquery.fancybox-1.3.4.css";
	</style>
	
	<script type="text/javascript">var C_JS_DEBUG = <?php echo G_BUILDDEBUG ? 'true' : 'false'; ?>;</script>
	<script type="text/javascript" src="public/js/jquery-1.5.min.js"></script>
	<script type="text/javascript" src="public/js/jquery.inputfocus.midi.js"></script>
	<script type="text/javascript" src="public/js/jquery.tools.min.js"></script>
	<script type="text/javascript" src="public/js/slides.min.jquery.js"></script>
	<script type="text/javascript" src="public/js/jquery.validate.min.js"></script>
	<script type="text/javascript" src="public/js/util.validate.js"></script>
	<script type="text/javascript" src="public/js/jquery.meio.mask.min.js"></script>
	<script type="text/javascript" src="public/fancybox/jquery.fancybox-1.3.4.js"></script>
	
	<script type="text/javascript">
	$(function() {
		$('#search #query').inputfocus({ idle_value: 'BUSCAR:', idle_color: false });
		
		// Top slider
		$("#slider").scrollable({ circular: true }).autoscroll(); // initialize scrollable
		$("#slider").append('<div id="corner-layer"></div>'); // slider corners - necessary to add them after scrollable initialize
	});
	</script>
</head>


<body class="txt-default">


<div id="main-site" class="site-width-extent">

	<!-- TOPO -->
	<div id="topo" class="clearfix">
		<div class="clearfix">
			<a href="."><img src="<?php echo DisplayLayer::LayoutImagePath('public/images/main_logo.png'); ?>" id="logo" /></a>
			<div id="leading-area">
				<table>
					<tr>
						<td width="100%" id="link-user-access">
						<?php if(Login::$logged) { ?>
							<span id="nick">Olá <strong><?php echo FormatLayer::ClientLoginName(Login::$user_data); ?>!</strong></span> Não é seu usuário? (<a href="?login_action=logout" id="logout"><strong class="txt-marine">Sair</strong></a>)<br />
							<a href="?p=painel" id="painel">Painel de controle</a>
						<?php } else { ?>
							<span id="nick">Olá <strong>Visitante!</strong></span> (<a href="?p=login" id="logout"><strong class="txt-marine">Fazer login</strong></a>)<br />
							Não tem conta em nosso site? <a href="?p=login&cadastrar" id="painel">Cadastre-se</a>
						<?php }?>
						</td>
						
						<td><img src="<?php echo DisplayLayer::LayoutImagePath('public/images/main_top_centraltel.png'); ?>" id="link-central" /></td>
						<td><a href="?p=checkout1" id="qty-box"><div><span><?php echo Cart::$gcart->ItemCount(); ?></span> produtos</div></a></td>
					</tr>
				</table>
				
				<div id="short-links" class="txt-marine">
					<div class="p">
						<a href="?p=pagina&n=institucional">institucional</a><span class="d">:</span>
						<a href="?p=pagina&n=politica">política do site</a><span class="d">:</span>
						<a href="?p=atendimento">atendimento</a>
					</div>
					
					<div id="auxlayer">
						<div class="p">
						<?php if(Login::$logged) { ?>
							<a href="?p=pedidos" id="altcolor">meus pedidos</a><span class="d">:</span>
							<a href="?p=painel">meu cadastro</a>
						<?php } else { ?>
							<a href="?p=atendimento" id="altcolor">contato</a><span class="d">:</span>
							<a href="?p=checkout1">carrinho de compras</a>
						<?php }?>
						</div>
					</div>
				</div>
			</div>
		</div>
		
		<div id="trailing-band">
			<div id="strip">
				<form id="search">
					<input type="hidden" name="p" value="busca" />
					
					<div id="input-wrap">
						<input type="text" name="q" id="query" class="<?php echo browser::css(); ?>" />
						<input type="submit" id="submit" value="" />
					</div>
					
					<div id="select-wrap">
						<select name="target" class="txt-marine <?php echo browser::css(); ?>">
							<option value="0">em todo o site</option>
							<?php
							foreach( db::select('produtos_categorias', '*') as $rowc )
								printf( '<option value="%d">%s</option>', $rowc['id_categoria'], $rowc['nome'] );
							?>
						</select>
					</div>
				</form>
				
				<div id="links">
					<?php
					$res = db::select('produtos_categorias', '*', null, 'nome');
					foreach( $res as $rowc )
						printf('<div>%s<a href="%s">%s</a></div>', (!$dot && $dot=true) ? '' : '<span class="d">:</span>', '?p=produtos&cat='.$rowc['id_categoria'], $rowc['nome']);
					?>
				</div>
			</div>
		</div>
	</div>
	
	
	<!-- SLIDER -->
	<div id="slider" <?php if($view_entire) echo 'class="short"'; ?>>
		<div id="horz-scroll">
			<?php
			$res = db::select('banners_slider', '*', array('ativo' => 1), 'posicao');
			foreach( $res as $rowb )
				printf( '<div class="slide-item" style="background-image: url(%s);">%s</div>', SiteConstants::DIR_USERFILES.'/banners_slider/'.$rowb['imagem'], empty($rowb['link']) ? '' : '<a href="'.$rowb['link'].'"></a>' );
			?>
		</div>
	</div>
	
	
	<!-- INCLUDE -->
	<?php if($view_entire) { ?>
	
		<div id="include" class="clearfix entire-flow">
			<?php Site::PrintIncludeOutput(); ?>
		</div>
	
	<?php } else { ?>
	
		<div id="include" class="clearfix normal-flow">

			<div class="side-cols" id="menu-left">
				<div id="head-size-box">
					<img src="<?php echo DisplayLayer::LayoutImagePath('public/images/home_label_departamentos.png'); ?>" />
				</div>
				
				<div class="wrap-col">
					<?php
					// for Categorias
					$res = db::select('produtos_categorias', '*', null, 'nome');
					foreach( $res as $rowc )
					{
						$res = db::select('produtos_subcategorias', '*', array('id_categoria' => $rowc['id_categoria']), 'nome');
						if( $res )
						{
					?>
					<div class="section">
						<div id="name-box"><a href="?p=produtos&cat=<?php echo $rowc['id_categoria']; ?>"><?php echo $rowc['nome']; ?></a></div>
						<ul>
							<?php
							// for Subcategorias
							foreach( $res as $rows )
								printf( '<li%s><span>.</span><a href="%s">%s</a></li>', ' class="noborder"', '?p=produtos&subc='.$rows['id_subcategoria'], $rows['nome'] );
							?>
						</ul>
					</div>
					<?php
						}
					}
					?>
					<a href=""><img src="<?php echo DisplayLayer::LayoutImagePath('public/images/main_icon_mored.png'); ?>" id="last-link" /></a>
				</div>
				
				<div id="fot-size-box"></div>
			</div>
			
			
			<div id="include-content">
				<?php Site::PrintIncludeOutput(); ?>
			</div>
			
			
			<div class="side-cols" id="menu-right">
				<div id="head-size-box">
					<img src="<?php echo DisplayLayer::LayoutImagePath('public/images/home_label_ofertas.png'); ?>" />
				</div>
				
				<div class="wrap-col">
					<?php
					$res = db::select('banners_laterais', '*', array('ativo' => 1), 'RAND()', 0, 2);
					foreach( $res as $rowb )
					{
						$banner_img = SiteConstants::DIR_USERFILES.'/banners_laterais/'.$rowb['imagem'];
						if( empty($rowb['link']) )
							printf( '<img src="%s" class="side-banner" />', $banner_img );
						else
							printf( '<a href="%s"><img src="%s" class="side-banner" /></a>', $rowb['link'], $banner_img );
					}
					?>
					
					<div id="pages">
						<ul>
							<li><span>.</span><a href="?p=pagina&n=atendimento_duvidas">Dúvidas</a></li>
							<li><span>.</span><a href="?p=pagina&n=atendimento_entregas">Entregas</a></li>
							<li><span>.</span><a href="?p=pagina&n=atendimento_garantia">Trocas e devoluções</a></li>
							<li><span>.</span><a href="?p=pagina&n=atendimento_garantia">Garantia</a></li>
						</ul>
					</div>
				</div>
				
				<div id="fot-size-box"></div>
			</div>
			
		</div>
		
	<?php } ?>
	
</div>

<!-- RODAPÉ -->
<div id="basic-footer">
	<div class="site-width-extent">
		<div id="wraper">
			<img src="<?php echo DisplayLayer::LayoutImagePath('public/images/main_fot_logo.png'); ?>" id="logo" />
			
			<div id="subarea">
				<ul>
					<li><a href=".">HOME</a></li>
					<li><a href="?p=pagina&n=institucional">INSTITUCIONAL</a></li>
					<li><a href="?p=pagina&n=lojas">LOJAS</a></li>
					<li><a href="">LISTA DE PRESENTES</a></li>
					<li><a href="?p=atendimento">ATENDIMENTO</a></li>
					<li><a href="?p=pedidos">MEUS PEDIDOS</a></li>
					<li><a href="?p=painel">MEU CADASTRO</a></li>
					<li><a href="?p=pagina&n=politica">POLÍTICA DE PRIVACIDADE</a></li>
				</ul>
				
				<p>Os preços, prazos de pagamento e promoções são exclusivos para compras efetuadas através do site.<br />Os mesmos não se aplicam em nossas lojas físicas.</p>
				<img src="<?php echo DisplayLayer::LayoutImagePath('public/images/icon_paymodes.png'); ?>" id="icon-pay" />
			</div>
		</div>
	</div>
</div>


</body>

</html>