<?php
class CK2_Page extends Page
{
	function PagePreInclude()
	{
		if( !Login::$logged || Cart::GetOrderList()->count()==0 )
			$this->MarkRedirect()->ThrowReturn();
		
		if( isset($_POST['form_sent']) )
		{
			$checkout = Cart::GetActiveCheckout();
			$checkout->FreteSetMode($_POST['frete_mode']);
			$checkout->PgtoSetMode($_POST['pgto_mode']);
			
			if( $_POST['form_sent']=='addr' )
			{
				$cep_pattern = "/^[0-9]{5}-[0-9]{3}$/";
				if( !preg_match($cep_pattern, $_POST['cep']) || !$checkout->FreteSetCEP($_POST['cep']) )
					$this->view_chain->cep_invalid = true;
				else
					$checkout->AddrSetFields();
				$this->MarkRedirectChain();
			}
			
			if( $_POST['form_sent']=='checkout' )
			{
				$finalize = Cart::GetFinalizeCheckout();
				$finalize->FinalizeCheckout();
				
				switch( $checkout->PgtoGetMode() )
				{
				case ShoppingConsts::PGTO_SERVICE_PDIGITAL:
					$this->MarkRedirectChain('pad_redirect');
					break;
				case ShoppingConsts::PGTO_SERVICE_DEPOSITOBB:
					$this->MarkRedirectChain('checkout3');
					$finalize->OrderActiveState();
					break;
				}
				
				$finalize->InsertOrder();
			}
		}
	}
}

$page = Site::LinkIncludePage( new CK2_Page() );
$page->view_site->entire_flow = true;

$list = Cart::GetOrderList();
$checkout = Cart::GetActiveCheckout();

$view_addr_form = $page->view_chain->cep_invalid || $checkout->AddrNeedAdjust();
$view_addr_fields = $checkout->AddrGetFields();
$view_ship = $checkout->FreteGetMode()!=ShoppingConsts::FRETE_SERVICE_GRATIS;
$view_frete = $checkout->FretePrice($view_ship);
$view_total = $checkout->OrderTotal($view_ship);
?>

<script type="text/javascript">
var form_addr_shown = false;
var form_submit_source = false;

function InstallAddrValidate()
{
	form_addr_shown = true;
	
	// masks -------------------------------------------------------------------------------------
	$('input[name="cep"]').setMask();
	
	// validate ----------------------------------------------------------------------------------
	$('#main-form input[name="cep"]').rules('add', {
		required: true,
		cep: true
	});
	$('#main-form input[name="cidade"]').rules('add', {
		required: true
	});
	$('#main-form input[name="endereco"]').rules('add', {
		required: true
	});
}


$(function() {
	$('#submit-refresh').click(function() { form_submit_source = false; $('input[name="form_sent"]').val('addr'); });
	$('#submit-checkout').click(function() { form_submit_source = true; $('input[name="form_sent"]').val('checkout'); });
	
	$('#main-form').validate({
		messages: {
			cep: {
				required: 'Informe um CEP'
			},
			cidade: 'Informe a cidade',
			endereco: 'Informe o endereço'
		},
		
		wrapper: '',
		errorElement: 'li',
		errorLabelContainer: '#addr-error-bar',
		//errorPlacement: function(error, element) {}
		
		submitHandler: function(form) {
			if( form_submit_source && form_addr_shown )
				$('#msg-area .message-super-bar').show();
			else
				form.submit();
		}
	});
	
	<?php if( $view_addr_form ) { ?>
		InstallAddrValidate();
	<?php } else { ?>
		$('#anchor-change-trigger').click(function() {
			$(this).hide();
			$('#addr-actual').hide();
			$('#addr-form').show();
			InstallAddrValidate();
			return false;
		});
	<?php } ?>
	
	<?php if($view_ship) { ?>
		$('input[name="frete_mode"]').click(function() {
			var val = $(this).val();
			$('.total_value').hide();
			$('#total_mode_' + val).show();
			
			$('.frete_value').removeClass('selected');
			$('#frete_value_' + val).addClass('selected');
		});
		
		$('#frete_value_<?php echo $checkout->FreteGetMode(); ?>').addClass('selected');
		$('#total_mode_<?php echo $checkout->FreteGetMode(); ?>').show();
	<?php } ?>
});
</script>

<span id="checkout" class="c2">


<div class="content">

	<div id="identif-hint" style="background-image: url(<?php echo DisplayLayer::LayoutImagePath('public/images/model_label_checkout.png'); ?>);"></div>
	<hr class="line"></hr>
	
	<div class="textwall-normal">
		<h2>TÍTULO TÍTULO TÍTULO</h2>
		<p>
			COLOCAR AQUI INSTRUÇÕES PARA O USUÁRIO SOBRE ESTA ÚLTIMA ETAPA DO CHECKOUT
		</p>
	</div>
	
	<hr class="line"></hr>
	
	<form method="post" id="main-form" class="model-widgets">
		<table>
			<thead class="txt-hardgray">
				<tr>
					<th width="770">Produto</th>
					<th class="col-hcenter">Quantidade</th>
					<th class="col-hright">Subtotal</th>
				</tr>
			</thead>
			
			<tbody>
				<?php
				$in = 1;
				foreach( $list as $item )
				{
					$rowp = $item->fetch;
				?>
				<tr>
					<td>
						<?php
						echo $rowp['nome'];
						if( KudosFunctions::ProductAttribIsActive('tamanho') && $item->options['attrib_has_size'] )
							printf( ' (<em>Tamanho: %d</em>)', db::field('produtos_atributo_tamanho', 'nome', array('id_atributo' => $item->options['attrib_size_val'])) );
						?>
					</td>
					<td style="text-align: center;"><b><?php echo $item->qty; ?></b></td>
					<td class="col-hright"><b><?php echo FormatLayer::FormatItemPrice($item->UnitPrice()); ?></b></td>
				</tr>
				<?php
				}
				?>
			</tbody>
			
			<tfoot>
				<tr class="sep">
					<td></td>
					<th colspan="2"><hr class="line"></hr></th>
				</tr>
				
				<?php if( $checkout->FreteGetMode()==ShoppingConsts::FRETE_SERVICE_GRATIS ) { ?>
					<tr>
						<td>&nbsp;</td>
						<td class="col-hcenter txt-hardgray">Frete</td>
						<td>Frete grátis</td>
					</tr>
					<tr>
						<td>&nbsp;</td>
						<td class="col-hcenter txt-hardgray">Total</td>
						<td>
							<div class="total_value" style="display: block;"><?php echo FormatLayer::FormatCurrency($view_total); ?></div>
						</td>
					</tr>
				<?php } else {?>
					<tr>
						<td>&nbsp;</td>
						<td class="col-hcenter txt-hardgray">Frete</td>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<td>&nbsp;</td>
						<td class="col-hcenter txt-hardgray"><div class="frete"><input type="radio" name="frete_mode" value="<?php echo ShoppingConsts::FRETE_SERVICE_PAC; ?>" <?php if( $checkout->FreteGetMode()==ShoppingConsts::FRETE_SERVICE_PAC ) echo 'checked="checked"'; ?> />PAC</div></td>
						<td class="col-hright"><div class="frete_value" id="frete_value_<?php echo ShoppingConsts::FRETE_SERVICE_PAC; ?>"><?php echo FormatLayer::FormatCurrency($view_frete[ShoppingConsts::FRETE_SERVICE_PAC]); ?></div></td>
					</tr>
					<tr>
						<td>&nbsp;</td>
						<td class="col-hcenter txt-hardgray"><div class="frete"><input type="radio" name="frete_mode" value="<?php echo ShoppingConsts::FRETE_SERVICE_SEDEX; ?>" <?php if( $checkout->FreteGetMode()==ShoppingConsts::FRETE_SERVICE_SEDEX ) echo 'checked="checked"'; ?> />Sedex</div></td>
						<td class="col-hright"><div class="frete_value" id="frete_value_<?php echo ShoppingConsts::FRETE_SERVICE_SEDEX; ?>"><?php echo FormatLayer::FormatCurrency($view_frete[ShoppingConsts::FRETE_SERVICE_SEDEX]); ?></div></td>
					</tr>
					<tr>
						<td>&nbsp;</td>
						<td class="col-hcenter txt-hardgray">Total</td>
						<td class="col-hright">
							<div class="total_value" id="total_mode_<?php echo ShoppingConsts::FRETE_SERVICE_PAC; ?>"><?php echo FormatLayer::FormatCurrency($view_total[ShoppingConsts::FRETE_SERVICE_PAC]); ?></div>
							<div class="total_value" id="total_mode_<?php echo ShoppingConsts::FRETE_SERVICE_SEDEX; ?>"><?php echo FormatLayer::FormatCurrency($view_total[ShoppingConsts::FRETE_SERVICE_SEDEX]); ?></div>
						</td>
					</tr>
				<?php } ?>
			</tfoot>
		</table>
		
		<hr class="line"></hr>
		
		<div id="box-dados-envio" class="header-pattern clearfix">
			<div id="col-float-pay">
				<h1 class="h1">Forma de Pagamento</h1>
				
				<label>
					<input type="radio" name="pgto_mode" value="<?php echo ShoppingConsts::PGTO_SERVICE_PDIGITAL; ?>" <?php if( $checkout->PgtoGetMode()==ShoppingConsts::PGTO_SERVICE_PDIGITAL ) echo 'checked="checked"'; ?> />
					<span class="h2">Pagar com</span>
					<img src="<?php echo DisplayLayer::LayoutImagePath('public/images/icon_pay_pagamento_digital.png'); ?>" />
					
					<div>
						<p><img src="<?php echo DisplayLayer::LayoutImagePath('public/images/icon_padlock.png'); ?>" id="thumb" />Colocar um texto mais inteligente sobre o PAD. Colocar um texto mais inteligente sobre o PAD. Colocar um texto mais inteligente sobre o PAD. Colocar um texto mais inteligente sobre o PAD. Colocar um texto mais inteligente sobre o PAD. Colocar um texto mais inteligente sobre o PAD.</p>
					</div>
				</label>
				
				<label>
					<input type="radio" name="pgto_mode" value="<?php echo ShoppingConsts::PGTO_SERVICE_DEPOSITOBB; ?>" <?php if( $checkout->PgtoGetMode()==ShoppingConsts::PGTO_SERVICE_DEPOSITOBB ) echo 'checked="checked"'; ?> />
					<span class="h2">Pagar com</span>
					<img src="<?php echo DisplayLayer::LayoutImagePath('public/images/icon_pay_deposito_bancario.png'); ?>" style="margin-left: 15px;" />
					
					<div>
						<p>Depósito bancário. Depósito bancário. Depósito bancário. Depósito bancário. Depósito bancário. Depósito bancário. Depósito bancário. Depósito bancário. Depósito bancário. </p>
					</div>
				</label>
			</div>
			
			<div id="col-float-send">
				<h1 class="h1">Dados para Envio</h1>
				
				<?php if( !$view_addr_form ) { ?>
					<a href="" id="anchor-change-trigger">(Alterar endereço de envio)</a>
					
					<div id="addr-actual" class="textwall-normal">
						<p>
							<?php
							echo $nx=(bool)($s=$view_addr_fields['ender_endereco']) ? $s : '';
							echo ($nx?', ':'').$nx=(bool)($s=$view_addr_fields['ender_numero']) ? $s : '';
							echo ($nx?', ':'').$nx=(bool)($s=$view_addr_fields['ender_bairro']) ? $s : '';
							?>
						</p>
						<p>
							<?php
							echo $nx=(bool)($s=$view_addr_fields['ender_cidade']) ? $s : '';
							echo ($nx?' - ':'').$nx=(bool)($s=$view_addr_fields['ender_estado']) ? $s : '';
							?>
						</p>
						<p>
							<?php
							echo $nx=(bool)($s=$view_addr_fields['ender_cep']) ? 'CEP: '.$s : '';
							?>
						</p>
						<p>
							<?php
							echo $nx=(bool)($s=$view_addr_fields['ender_complemento']) ? 'Complemento: '.$s : '';
							?>
						</p>
					</div>
				<?php } ?>
				
				<div id="addr-form" <?php if( !$view_addr_form ) echo 'class="nodisplay"'; ?>>
					<div class="join-line">
						<div class="label">CEP:</div>
						<div class="input-wrap-round <?php echo browser::css(); ?>"><input type="text" name="cep" alt="cep" value="<?php echo $view_addr_fields['ender_cep']; ?>" /></div>
						<?php if( $page->view_chain->cep_invalid ) { ?>
							<label class="error" style="color: red;">O CEP digitado é inválido</label>
						<?php } ?>
					</div>
					
					<div class="join-line">
						<div class="label">Cidade:</div>
						<div class="input-wrap-round <?php echo browser::css(); ?>"><input type="text" name="cidade" value="<?php echo $view_addr_fields['ender_cidade']; ?>" /></div>
						<div class="label" style="width: 30px; margin-left: 10px;">UF:</div>
						<div class="input-wrap-round" id="select-wrap">
							<select name="uf">
								<?php
								foreach( db::select('cms_estados', '*', null, 'nome') as $row_uf )
									printf('<option value="%s" %s>%s</option>', $row_uf['uf'], $view_addr_fields['ender_estado']==$row_uf['uf'] ? 'selected="selected"' : '', $row_uf['uf']);
								?>
							</select>
						</div>
					</div>
					
					<div class="join-line">
						<div class="label">Endereço:</div>
						<div class="input-wrap-round <?php echo browser::css(); ?>"><input type="text" name="endereco" value="<?php echo $view_addr_fields['ender_endereco']; ?>" style="width: 246px;" /></div>
					</div>
					
					<div class="join-line">
						<div class="label">Número:</div>
						<div class="input-wrap-round <?php echo browser::css(); ?>"><input type="text" name="numero" value="<?php echo $view_addr_fields['ender_numero']; ?>" style="width: 56px;" /></div>
						<div class="label" style="width: 50px; margin-left: 10px;">Bairro:</div>
						<div class="input-wrap-round <?php echo browser::css(); ?>"><input type="text" name="bairro" value="<?php echo $view_addr_fields['ender_bairro']; ?>" style="width: 118px;" /></div>
					</div>
					
					<div class="join-line">
						<div class="label">Complemento:</div>
						<div class="input-wrap-round <?php echo browser::css(); ?>"><input type="text" name="complemento" value="<?php echo $view_addr_fields['ender_complemento']; ?>" style="width: 246px;" /></div>
					</div>
					
					<br clear="all" />
					<div id="addr-error-bar" class="error-bar">Alguns campos não foram preenchidos corretamente.</div>
					<input type="image" src="<?php echo DisplayLayer::LayoutImagePath('public/images/btn_text_atualizar.png'); ?>" id="submit-refresh" />
				</div>
			</div>
		</div>
		
		<hr class="line"></hr>
		
		<div id="action-bar">
			<div id="obs-area">
				<strong>Observações do pedido:</strong>
                <textarea name="pedido_obs"></textarea>
			</div>
			
			<div id="msg-area">
				<div id="wrap-msg">
					<div class="message-super-bar nodisplay">
						Você deve terminar de atualizar seu endereço para poder finalizar a compra.
					</div>
				</div>
				<input type="image" src="<?php echo DisplayLayer::LayoutImagePath('public/images/btn_text_fechar_pedido.png'); ?>" id="submit-checkout" />
			</div>
		</div>
		
		<input type="hidden" name="form_sent" />
	</form>
</div>


</span>