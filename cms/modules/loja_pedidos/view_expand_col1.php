<?php
$height = 210;
if( $state > OrderStateConsts::STATE_COMPLETE )
{
	$view_msg = 'pedido cancelado';
	$view_txt = 'Este pedido foi cancelado em ' . $fetch->fetch['view_lastevent'];
	$view_type = 0;
} elseif( $state > OrderStateConsts::STATE_STEP1 ) {
	$view_msg = 'aprovado'; //$view_txt = 'Pedido aprovado';
	$view_type = 1;
} elseif( $scol->substep_done ) {
	$view_msg = 'aprovado';
	$view_txt = 'Para finalizer essa etapa e alterar a visualização do cliente para pedido autorizado, clique no botão de concluir.';
	$view_type = 1;
} else {
	$view_msg = 'pré-aprovado';
	$view_txt = 'Os itens deste pedido já foram descontados do estoque. Clique no botão \'aprovar pedido\' para aceitá-lo.';
	$view_type = 2;
	$height = 171;
}
?>
<td valign="top" class="stepcol">
	<div id="suboverlay">
		<img src="gfxnew/expandtbl_indicator.gif" /><br />
		<img src="gfxnew/expandtbl_n1.gif" />
		
		<div id="sizewrap">
			<div style="height: <?php echo $height; ?>px;">
				<p class="limarkup">Dados de identificação do cliente:</p>
				<table border="0" cellspacing="0" cellpadding="0" align="center" id="identtbl">
					<tr>
						<td>Apelido no site:</td>
						<td align="left"><?php echo $fetch->client['ident_apelido']; ?></td>
					</tr>
					<tr>
						<td>Cadastrado desde:</td>
						<td align="left"><?php echo $fetch->client['view_joined']; ?></td>
					</tr>
					<tr>
						<td>Nome:</td>
						<td align="left"><?php echo $fetch->client['ident_nome']; ?></td>
					</tr>
					<tr>
						<td>Cidade/UF:</td>
						<td align="left"><?php echo $fetch->client_addr['ender_cidade']; ?> - <?php echo $fetch->client_addr['ender_estado']; ?></td>
					</tr>
				</table>
				
				<br /><p class="limarkup">Status da aprovação: <strong><?php echo $view_msg; ?></strong></p>
				<p class="linestatus"><em><?php echo $view_txt; ?></em></p>
			</div>
			
			<?php if($view_type==2) { ?>
				<a href="" id="update_aprove"><img src="gfxnew/expandtbl_btn_aprove.gif" style="margin-bottom: 5px;" /></a><br />
				<a href="" id="update_desaprove"><img src="gfxnew/expandtbl_btn_desaprove.gif" /></a><br />
			<?php } elseif($view_type==1) { ?>
				<img src="gfxnew/expandtbl_btn_aprove_done.gif" />
			<?php } elseif($view_type==0) { ?>
				<img src="gfxnew/expandtbl_btn_desaprove_done.gif" />
			<?php } ?>
		</div>
		
		<?php if( $scol->shownext ) { ?>
			<a href="" id="actionnext"><img src="gfxnew/expandtbl_c1.gif" /></a>
		<?php } else {?>
			<img src="gfxnew/expandtbl_c1.gif" <?php if($scol->actual) echo 'id="btnfaded"';?> />
		<?php } ?>
	</div>
	
	<?php
	if( !$scol->actual )
		echo '<div id="overlay"></div>';
	?>
</td>