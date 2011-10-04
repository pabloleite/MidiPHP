<?php
/*
>>>TO-DO
-padronizar o HTML de cada step, $subview_step estaria portanto identificando a exibição da coluna
$subview_step = 1;
$subview_fieldstate = substate_pagamento;
$subview_substate = 10;
*/
session_start();
header('Content-Type: text/html; charset=utf-8', true);
set_include_path('../../../');

require 'cms/includes/config.php';
require 'cms/includes/mysql.class.php';
require_once 'funcs.php';
require_once 'classes/shopping/order_fetch.php';

$db = new MySQL(true, $config["db_name"], $config["db_host"], $config["db_user"], $config["db_password"], 'utf8', $_SERVER['HTTP_HOST']=='server');

$orderid = $_GET['id'];
$css_space = 'orderspace'.$orderid;


$sql = "SELECT *,
			DATE_FORMAT(data_ordered, '%%d/%%m/%%Y às %%H:%%i') as view_data, 
			DATE_FORMAT(data_lastevent, '%%d/%%m/%%Y') as view_lastevent 
		FROM %s 
		WHERE id_pedido = %d 
		LIMIT 1";
$sql = sprintf($sql, ShopDBConsts::TABLE_ORDER, $orderid);

$fetch = $db->QuerySingleRowArray($sql);
$fetch = new ActiveOrder($fetch);
$fetch->FetchClient()->FetchAddr()->FetchClientAddr();
$state = intval($fetch->fetch['state']);
$substate = intval($fetch->fetch['substate']);

class StepCol
{
	public $data;
	public $substate;
	public $substep_done;
	public $shownext;
}

class ExpandView
{
	static function BuildStateCols()
	{
		global $css_space, $fetch, $state, $substate;
		
		$substate_data = unserialize($fetch->fetch['substate_data']);
		foreach( OrderStateConsts::$arr_state_steps as $step => $sname )
		{
			$scol = new StepCol();
			$scol->actual = $step==$state;
			$scol->data = $substate_data[$sname];
			$scol->substate = $scol->actual ? $fetch->fetch['substate'] : $scol->data['substate'];
			$scol->substep_done = $scol->substate >= OrderStateConsts::SUBSTATE_DONEMARK;
			$scol->shownext = $step==OrderStateConsts::STATE_STEP3 || ($scol->substep_done && $step==$state);
			
			include sprintf('view_expand_col%d.php', $step);
		}
	}
}
?>




<tr id="<?php echo $css_space;?>">
	<td colspan="6">
		
		<script type="text/javascript">
		$(function() {

			function SpaceUpdate(data, form)
			{
				data.action = 'update';
				RequestSubmit(<?php echo $orderid; ?>, data, form);
				return false;
			}
			var _space = $('#<?php echo $css_space;?>');
			$('#btnfaded', _space).fadeTo(0, 0.18); //botao da etapa ativa mostrado como desativado (com fade)
			$('#actionnext', _space).click( function() { return SpaceUpdate({subaction: 'next'}); }); //ação do botao que termina a etapa
			
			
			// register expanded actions click
			function ConfirmCancelAction()
			{
				return confirm('Deseja realmente cancelar este pedido?');
			}
			<?php if( $state==OrderStateConsts::STATE_STEP1 ) { ?>
				$('#update_aprove', _space).click(function() { return SpaceUpdate({subaction: 'done'}); });
				$('#update_desaprove', _space).click(function() { return ConfirmCancelAction() ? SpaceUpdate({subaction: 'cancel'}) : false; });
			<?php } elseif( $state==OrderStateConsts::STATE_STEP2 ) { ?>
				$('#confirm', _space).click(function() { return SpaceUpdate({subaction: 'done'}); });
			<?php } elseif( $state==OrderStateConsts::STATE_STEP3 ) { ?>
				$('#refresh', _space).click(function() {
					return SpaceUpdate({subaction: 'refresh'}, $('#send-state, #knowcod-input, #knowcod-check', _space));
				});
				
				// código rastreamento input
				function ReviewKnowcod(e)
				{
					if( $('#knowcod-input', _space).val()=='' )
						$('#knowcod-check', _space).attr('disabled', 'true').attr('checked', false).parent().addClass('disabled');
					else
						$('#knowcod-check', _space).removeAttr('disabled').parent().removeClass('disabled');
				}
				ReviewKnowcod();
				$('#knowcod-input', _space).keyup(ReviewKnowcod).keydown(ReviewKnowcod);
			<?php } ?>
			
		});
		</script>
		
		<table border="0" cellspacing="0" cellpadding="0" id="expandtbl">
			<tr class="tritem">
				<td id="col1" valign="top">
					<div style="padding: 10px; padding-top: 7px">
						<div id="txt-intro">Pedido <span><?php echo FormatLayer::FormatOrderID($orderid); ?></span> feito em <?php echo $fetch->fetch['view_data']; ?></div>
						<table border="0" cellspacing="0" cellpadding="0" id="itensheader">
							<tr>
								<th>COD.</th>
								<!--<th width="135" align="left">DESCRIÇÃO</th>-->
								<th width="26" style="text-align: center;">QTD</th>
								<th width="60" style="text-align: center;">PRESENTE</th>
								<th width="60" style="text-align: right;">TOTAL</th>
							</tr>
						</table>
						
						<div id="itenswraper">
							<table border="0" cellspacing="0" cellpadding="0">
								<?php
								$sql = "SELECT * FROM ".ShopDBConsts::TABLE_ORDER_PRODUCTS." WHERE id_pedido = $orderid";
								foreach( $db->QueryArray($sql) as $rowi )
								{
									$fetch_produto = $db->QuerySingleRowArray("SELECT * FROM ".ShopDBConsts::TABLE_PRODUCTS." WHERE id_produto = {$rowi['id_produto']}");
								?>
								<tr valign="top">
									<td><a href="?ir=produtos_cadastro&action=alt&id=<?php echo $rowi['id_produto']; ?>" target="_blank"><?php echo $fetch_produto['codigo']; ?></a></td>
									<!--<td width="135"><?php echo strip_tags($fetch_produto['descricao']); ?></td>-->
									<td width="25" style="text-align: center;"><?php echo $rowi['quantidade']; ?></td>
									<td width="60" style="text-align: center;"><?php echo $rowi['gift'] ? 'Sim' : 'Não'; ?></td>
									<td width="60" style="text-align: right;"><?php echo FormatLayer::FormatItemPrice((float) $rowi['preco_item'], $rowi['quantidade']); ?></td>
								</tr>
								<?php
								}
								?>
							</table>
						</div>
					</div>
					
					<table border="0" cellspacing="0" cellpadding="0" id="totaltbl">
						<tr>
							<td colspan="2" class="separ"><div></div></td>
						</tr>
						<tr>
							<td>Sub-total</td>
							<td width="60"><?php echo FormatLayer::FormatCurrency($fetch->fetch['order_subtotal']); ?></td>
						</tr>
						<tr>
							<td>Frete</td>
							<td>
								<?php if($fetch->fetch['order_frete_forma']==ShoppingConsts::FRETE_SERVICE_GRATIS) { ?>
									Grátis
								<?php
								} else
									echo FormatLayer::FormatCurrency($fetch->fetch['order_frete_total']);
								?>
							</td>
						</tr>
						<?php if( !empty($fetch->fetch['order_cupom_code']) ) { ?>
						<tr>
							<td>Cupom de desconto<br />(<?php echo $fetch->fetch['order_cupom_code']; ?>)</td>
							<td>-<?php echo FormatLayer::FormatCurrency($fetch->fetch['order_cupom_discount']); ?></td>
						</tr>
						<?php } ?>
						<tr>
							<td colspan="2" class="separ"><div></div></td>
						</tr>
						<tr class="total">
							<td>Total Geral</td>
							<td><?php echo FormatLayer::FormatCurrency($fetch->fetch['order_total']); ?></td>
						</tr>
					</table>
					
					<?php
					if( !empty($fetch->fetch['order_obs']) )
					{
					?>
					<div id="obsarea">
						&nbsp;<strong>Observações do cliente:</strong>
						<div id="wrap"><textarea disabled="disabled" style="color: #474747 !important;"><?php echo $fetch->fetch['order_obs']; ?></textarea></div>
					</div>
					<?php
					}
					?>
				</td>
				
				<?php
				ExpandView::BuildStateCols();
				?>
				
			</tr>
		</table>
		
	</td>
</tr>