<?php
class CK1_Page extends Page
{
	function PagePreInclude()
	{
		if( isset($_POST['form_sent']) )
		{
			$cep_pattern = '/^[0-9]{5}-[0-9]{3}$/';
			if( !preg_match($cep_pattern, $_POST['cep']) || !Cart::GetActiveCheckout()->FreteSetCEP($_POST['cep']) )
				$this->view_data->cep_invalid = true;
			else {
				if( !Login::$logged )
					$this->MarkRedirect('?p=login');
				else {
					$this->MarkRedirect('?p=checkout2');
					// TO-DO: setup login trigger
				}
			}
		}
	}
}

$page = Site::LinkIncludePage( new CK1_Page() );
$page->view_site->entire_flow = true;

$list = Cart::GetOrderList();
$view_empty = $list->count()==0;
//$view_empty = false;
$cep = $page->view_data->cep_invalid ? $_POST['cep'] : Cart::GetActiveCheckout()->FreteGetCEP();
?>

<?php if(!$view_empty) { ?>
<script type="text/javascript">
$(function() {
	$('tbody .overlay div').fadeTo(0, .75);
	
	
	// masks -------------------------------------------------------------------------------------
	$('input[name="cep"]').setMask();
	$('input.qty').setMask({ mask: '999', autoTab: false });
	
	
	// cep validate ------------------------------------------------------------------------------
	$('#main-form').validate({
		rules: {
			cep: {
				required: true,
				cep: true
			}
		},
		messages: {
			cep: {
				required: 'Informe um CEP'
			}
		},
		errorPlacement: function(error, element) {
			error.appendTo( element.parent().parent() );
		}
	});
	
	
	// ajax -------------------------------------------------------------------------------------
	function CheckoutRequestAjax(func, params, cbkfunc)
	{
		$('tfoot #loading').show();
		
		// setup required parameters
		var target = window.event ? window.event.srcElement : document.activeElement;
		var data = { func: func };
		if(target && $(target).closest('tr') )
		{
			target_item = $(target).closest('tr');
			target_item.prev().show();
			data.id = target_item.attr('pid');
		}
		$.extend(data, params);
		
		$.getJSON('public/ajax/item_checkout.php',
			data,
			function(data, textStatus, xhr) {
				$(window).delay(600).queue(function() {
					$(window).dequeue();
					
					$('tfoot #loading').hide();
					$('tfoot #order-total').text(data.total);
					
					if(target_item)
						target_item.prev().hide();
					if(cbkfunc)
						cbkfunc(data, target_item ? target_item : null);
				});
				
			}
		)
	}
	
	// attach events
	$('input.qty').keypress(function(e) {
		if( (e.which && e.which == 13) || (e.keyCode && e.keyCode == 13) )
		{
			$(this).parent().next().click();
			return false;
		}
	});
	
	$('input:checkbox').click(function() {
		var func;
		if( $(this).is(':checked') )
			func = 'CheckGift';
		else
			func = 'UncheckGift';
		
		CheckoutRequestAjax(func, {}, OnAfterCheck);
	});
	
	$('.anchor_refresh_qty').click(function() {
		var param = { newqty: $(this).prev().children().val() };
		CheckoutRequestAjax('UpdateQty', param, OnAfterRefresh);
		return false;
	});
	
	$('.anchor_remove_item').click(function() {
		CheckoutRequestAjax('Remove', {}, OnAfterRemove);
		return false;
	});
	
	// ajax return callbacks
	function OnAfterCheck(data, target_item)
	{
		target_item.find('#item-unit').text(data.item_unit);
		target_item.find('#item-total').text(data.item_total);
	}
	
	function OnAfterRefresh(data, target_item)
	{
		target_item.find('.qty').val(data.qty);
		target_item.find('#item-unit').text(data.item_unit);
		target_item.find('#item-total').text(data.item_total);
	}
	
	function OnAfterRemove(data, target_item)
	{
		$('#qty-box span').text( parseInt(data.item_count) );
		if(data.empty)
			location.reload(true);
		else
			target_item.remove().prev().remove();
	}
});
</script>
<?php } ?>

<span id="checkout" class="c1">


<div class="content">

	<div id="identif-hint" style="background-image: url(<?php echo DisplayLayer::LayoutImagePath('public/images/model_label_checkout.png'); ?>);"></div>
	<hr class="line">
	
	<?php if($view_empty) { ?>
		<div id="empty-cart">
			Seu carrinho de compras está vazio.
		</div>
	<?php } else { ?>
		<form method="post" id="main-form" class="model-widgets">
			<table>
				<thead>
					<tr>
						<th class="tbg"><div></div></th>
						<th align="left" width="100%">Nome do Produto</th>
						<th class="col-hcenter">Quantidade</th>
						<th class="col-hcenter">Presente?</th>
						<th class="col-hcenter" style="padding: 0 8px;">Preço Unitário</th>
						<th colspan="2" style="padding-left: 25px; padding-right: 70px">Total</th>
					</tr>
				</thead>
				
				<tbody>
					<?php
					$in = 1;
					foreach( $list as $item )
					{
						$rowp = $item->fetch;
						
						$thumb_path = SiteConstants::DIR_USERFILES.'/produtos/'.$rowp['imagem'];
						$thumb_url = SiteConstants::PATH_PHPTHUMB.'?' . sprintf('src=%s&w=%d&h=%d', $thumb_path, 76, 76);
					?>
					<tr class="overlay">
						<th colspan="7"><div></div></th>
					</tr>
					
					<tr <?php if(++$n%2==0) echo 'class="odd"'; ?> pid="<?php echo $item->id; ?>">
						<td><img src="<?php echo $thumb_url; ?>" /></td>
						<td>
							<?php
							echo $rowp['nome'];
							if( KudosFunctions::ProductAttribIsActive('tamanho') && $item->options['attrib_has_size'] )
								printf( ' (<em>Tamanho: %d</em>)', db::field('produtos_atributo_tamanho', 'nome', array('id_atributo' => $item->options['attrib_size_val'])) );
							?>
						</td>
						<td>
							<div class="input-wrap-round <?php echo browser::css(); ?>"><input maxlength="4" type="text" class="qty" value="<?php echo $item->qty; ?>" /></div>
							<a href="" class="anchor_refresh_qty"><img src="<?php echo DisplayLayer::LayoutImagePath('public/images/btn_ok.png'); ?>" /></a>
						</td>
						<td class="col-strong"><input type="checkbox" <?php if( $item->gift ) echo 'checked="checked"'; ?> /></td>
						<td class="col-strong"><span id="item-unit"><?php echo FormatLayer::FormatItemPrice($item->UnitPrice()); ?></span></td>
						<td class="col-strong"><span id="item-total"><?php echo FormatLayer::FormatItemPrice($item->TotalPrice()); ?></span></td>
						<td width="30"><a href="" class="anchor_remove_item"><img src="<?php echo DisplayLayer::LayoutImagePath('public/images/checkout_delete.png'); ?>" /></a></td>
					</tr>
					<?php
					}
					?>
				</tbody>
				
				<tfoot>
					<tr>
						<th class="tbg"><div align="left">&nbsp;Total dos produtos</div></th>
						<th colspan="6" class="colfot">
							<span id="loading"><img src="<?php echo DisplayLayer::LayoutImagePath('public/images/icon_busy.gif'); ?>" /> Atualizando total&nbsp;&nbsp; - &nbsp;&nbsp;</span><span>R$ <span id="order-total"><?php echo FormatLayer::FormatFloatStr($list->ListTotalPrice()); ?></span></span>
						</th>
					</tr>
				</tfoot>
			</table>
			
			<div id="calc-frete">
				<span>Insira o seu CEP para o cálculo do frete:</span>
				<div class="input-wrap-round <?php echo browser::css(); ?>"><input type="text" name="cep" alt="cep" <?php if($cep) echo 'value="'.$cep.'"'; ?> /></div>
				
				<?php if( $page->view_data->cep_invalid ) { ?>
					<div class="message-super-bar cep-warn">
						O CEP digitado é inválido
					</div>
					
					<script type="text/javascript">
						$('html, body').animate({scrollTop: $('.cep-warn').offset().top}, 'fast');
						$('input[name="cep"]').focus().one('keydown', function() {
							$('.cep-warn').hide();
						});
					</script>
				<?php } ?>
			</div>
			
			<div id="action-bar">
				<a href="javascript: history.back()"><img src="<?php echo DisplayLayer::LayoutImagePath('public/images/btn_text_continuar_comprando.png'); ?>" /></a>
				<input type="image" src="<?php echo DisplayLayer::LayoutImagePath('public/images/btn_text_avancar.png'); ?>" id="action-right" />
			</div>
			
			<input type="hidden" name="form_sent" />
		</form>
	<?php } ?>
</div>


</span>