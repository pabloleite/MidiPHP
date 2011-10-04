<?php
$height = 210;
$have_data = $fetch->fetch['order_pay_flag'];


// Recupera dados sobre o pagamento de acordo a forma dele (boleto, cartao, ...)
switch( $fetch->fetch['order_pay_mode'] )
{
case ShoppingConsts::PGTO_SERVICE_PDIGITAL:
	if( $have_data )
	{
		$dbdata = unserialize($fetch->fetch['order_pay_text']);
		$view_pgtdata = '
			Número da transação: ' .$dbdata['id_transacao'].'<br />
			Forma de pagamento: ' .$dbdata['tipo_pagamento'];
	}
	break;
case ShoppingConsts::PGTO_SERVICE_DEPOSITOBB:
	if( $have_data )
	{
		$view_pgtdata = nl2br($fetch->fetch['pgto_text']);
	}
	break;
case ShoppingConsts::PGTO_SERVICE_BOLETO:
	if( $have_data )
	{
		$dbdata = unserialize($fetch->fetch['order_pay_text']);
		$view_pgtdata = '
			Número do boleto: ' .$dbdata['nossonum']. '<br />
			Vencimento do boleto: ' .$dbdata['vcto'];
	}
	break;
case ShoppingConsts::PGTO_SERVICE_GATEWAY_REDECARD:
	$options = $fetch->GetPayOptions();
	if( $have_data )
	{
		$dbdata = unserialize($fetch->fetch['order_pay_text']);
		$view_pgtdata = 'TID: ' .$dbdata['tid'].
						'&nbsp;&nbsp;&nbsp;LR: ' .$dbdata['lr'].
						'<br />Código de autorização da compra: ' .$dbdata['arp'].
						'<br />Código do banco emissor: ' .$dbdata['bank'];
	}
	break;
}


// De acordo com a forma de pagamento, defini se o pagamento foi efetivado ou se se pode ser aprovado
// E compõe demais informações sobre o pagamento (TID, número da transação, número do boleto, ...)
switch( $fetch->fetch['order_pay_mode'] )
{
case ShoppingConsts::PGTO_SERVICE_PDIGITAL:
	if( $fetch->fetch['order_pay_flag'] )
	{
		switch( $dbdata['cod_status'] ) // TODO: substituir por contantes os valores dos case
		{
		case 0:
			$flag_aproval = true;
			$txt_paystats = 'Transação em andamento. Aguardando retorno do Pagamento Digital confirmando esta transação.';
			$txt_altaprove = 'Opcionalmente você pode aprovar o pagamento sem aguardar confirmação do Pagameto Digital clicanto no botão abaixo.';
			break;
		case 1:
			$flag_paydone = true;
			$txt_paystats = 'Transação concluída. Pagamento liberado pelo Pagamento Digital e creditado na sua conta. Confira no painel do Pagamento Digital através do número da transação se os valores e a situação estão corretas.';
			break;
		case 2:
			$txt_paystats = 'Transação cancelada. Esta transação foi cancelada no Pagamento Digital.';
			break;
		}
	} else {
		$txt_paystats = 'Sem número de transação. O pagamento foi iniciado pelo usuário mas o Pagamento Digital não retornou o status da transação. Verifique a situação deste pedido pelo painel do Pagamento Digital procurando pelo nome do cliente e valor da compra.';
		$txt_altaprove = 'Após aprove o pagamento clicando no botão abaixo.';
	}
	break;
case ShoppingConsts::PGTO_SERVICE_DEPOSITOBB:
	if( $fetch->fetch['order_pay_flag'] )
	{
		$flag_aproval = true;
		$txt_paystats = 'O usuário informou que efetivou o pagamento por depósito com os dados abaixo.';
		$txt_altaprove = 'Pressione o botão aprovar pagamento para prosseguir com o pedido.';
	} else {
		$flag_aproval = true;
		$txt_paystats = 'Aguardando usuário confirmar o depósito.';
		$txt_altaprove = 'Opcionalmente você pode confirmar o pagamento sem aguardar a confirmação do usuário.';
	}
	break;
case ShoppingConsts::PGTO_SERVICE_BOLETO:
	if( $fetch->fetch['order_pay_flag'] ) {
		if( $scol->substep_done )
			$txt_paystats = 'O pagamento deste boleto foi aprovado.';
		else {
			$txt_paystats = 'Boleto foi emitido. Verifique através do número do boleto abaixo se ele foi pago.';
			$txt_altaprove = 'Após aprove o pagamento clicando no botão abaixo.';
		}
	} else {
		$txt_paystats = 'Aguardando usuário requisitar a geração do boleto';
	}
	break;
case ShoppingConsts::PGTO_SERVICE_GATEWAY_REDECARD:
	if( $fetch->fetch['order_pay_flag'] )
	{
		if( $dbdata['status']==0 )
		{
			$flag_paydone = true;
			$txt_paystats = '';
		} else {
			
		}
	}
}


// Defini os status para a visualização do usuário
if( $scol->substep_done || $flag_paydone )
{
	$view_type = 2;
	$view_status = 'pagamento efetivado e aprovado';
} else {
	$view_type = $scol->actual && $flag_aproval? 1 : 0;
	if( $flag_aproval )
		$view_status = 'aguardado aprovação';
	else
		$view_status = 'pagamento não efetivado';
}

// Icone da forma de pagamento
$arr_icons = array(
	ShoppingConsts::PGTO_SERVICE_PDIGITAL => 'badge_pdigital.gif', 
	ShoppingConsts::PGTO_SERVICE_DEPOSITOBB => 'banco-brasil.gif',
	ShoppingConsts::PGTO_SERVICE_BOLETO => 'boleto.gif'
);
if( $fetch->fetch['order_pay_mode']==ShoppingConsts::PGTO_SERVICE_GATEWAY_REDECARD )
{
	$arr_flags = array(
		ShoppingConsts::REDECARD_BANDER_VISA => 'visa_novo.gif',
		ShoppingConsts::REDECARD_BANDER_MASTER => 'master.gif',
		ShoppingConsts::REDECARD_BANDER_DINERS => 'diners.gif'
	);
	$arr_icons[ShoppingConsts::PGTO_SERVICE_GATEWAY_REDECARD] = $arr_flags[ $options['bander'] ];
}
$view_icon = $arr_icons[ $fetch->fetch['order_pay_mode'] ];
?>
<td valign="top" class="stepcol">
	<div id="suboverlay">
		<img src="gfxnew/expandtbl_indicator.gif" /><br />
		<img src="gfxnew/expandtbl_n2.gif" />
		
		<div id="sizewrap">
			<div style="height: 210px;">
				<p class="limarkup">Forma de pagamento selecionada:&nbsp;</p>
				<img src="gfxnew/pgto_types/<?php echo $view_icon; ?>" />
				
				<!--<br /><p class="limarkup">Condição do pagamento: <strong><?php echo $msg_quote; ?></strong></p>-->
				<br /><p class="limarkup">Status: <strong><?php echo $view_status; ?></strong></p>
				<br /><br /><p class="linestatus"><em>
					<?php
					echo $txt_paystats;
					if( $view_type==1 )
						echo '<br />'.$txt_altaprove;
					?>
				</em></p>
				
				<?php if( $have_data ) { ?>
					<div class="separ"><div></div></div>
					<p class="databox">
						<?php echo $view_pgtdata; ?>
					</p>
					<div class="separ"><div></div></div>
				<?php } ?>
			</div>
			
			<?php if($view_type==1) { ?>
				<a href="" id="confirm"><img src="gfxnew/expandtbl_btn_pgtoaprove.gif" /></a>
			<?php }/* elseif($view_type==1) {?>
				<img src="gfxnew/expandtbl_btn_pgtoconfirm_done.gif" />
			<?php }*/ ?>
		</div>
		
		<?php if( $scol->shownext ) { ?>
			<a href="" id="actionnext"><img src="gfxnew/expandtbl_c2.gif" /></a>
		<?php } else {?>
			<img src="gfxnew/expandtbl_c2.gif" <?php if($scol->actual) echo 'id="btnfaded"';?> />
		<?php } ?>
	</div>
	
	<?php
	if( !$scol->actual )
		echo '<div id="overlay"></div>';
	?>
</td>