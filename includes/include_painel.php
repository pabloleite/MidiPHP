<?php
if( !Login::$logged )
	Page::$gpage->MarkRedirect()->ThrowReturn();

Site::$view_props->entire_flow = true;
?>
<span id="painel">


<div class="content">
	
	<div id="identif-hint" style="background-image: url(<?php echo DisplayLayer::LayoutImagePath('public/images/model_label_painel.png'); ?>);"></div>
	
	<div class="textwall-normal">
		<h1>Olá, seja bem vindo, <?php echo FormatLayer::ClientLoginName(Login::$user_data); ?></h1>
		<hr class="line"></hr>
		Faça suas consultas através do painel abaixo:
		
		<div id="panes-area">
			<div class="pane">
				<img src="<?php echo DisplayLayer::LayoutImagePath('public/images/painel_label_entrega.png'); ?>" />
				<ul>
					<li><a href="?p=pedidos">Consultar pedidos</a></li>
				</ul>
			</div>
			
			<div class="pane">
				<img src="<?php echo DisplayLayer::LayoutImagePath('public/images/painel_label_cadastro.png'); ?>" />
				<ul>
					<li><a href="?p=alterar_cadastro">Alterar cadastro</a></li>
					<li><a href="?p=alterar_senha">Alterar senha</a></li>
					<li><a href="?p=alterar_email">Alterar e-mail</a></li>
				</ul>
			</div>
			
			<div class="pane">
				<img src="<?php echo DisplayLayer::LayoutImagePath('public/images/painel_label_servicos.png'); ?>" />
				<ul>
					<li><a href="?p=atendimento">Central de atendimento</a></li>
				</ul>
			</div>
		</div>
	</div>
	
</div>


</span>