<?php
if( !Login::$logged || !(int)$_GET['idp'] )
	Page::$gpage->MarkRedirect()->ThrowReturn();

$id_order = intval($_GET['idp']);
$fetch_order = db::row( ShopDBConsts::TABLE_ORDER, "*, DATE_FORMAT(data_ordered, '%d/%m/%Y') as view_data_ordered", array('id_pedido' => $id_order) );
$order = new ActiveOrder($fetch_order);

if( !$order->HasResult() || $order->fetch['id_usuario']!=Login::$login_id )
	Page::$gpage->MarkRedirect()->ThrowReturn();

$res = db::select( ShopDBConsts::TABLE_ORDER_PRODUCTS, '*', array('id_pedido' => $id_order) );
Site::$view_props->entire_flow = true;
?>

<span id="pedidos_detalhes">


<div class="content">

	<div id="identif-hint" style="background-image: url(<?php echo DisplayLayer::LayoutImagePath('public/images/model_label_pedidos.png'); ?>);"></div>
	<hr class="line">
	
	<table>
		<thead class="txt-bluish">
			<tr>
				<th class="tbg"><div></div>&nbsp;&nbsp;Produto</th>
				<th>Qnt</th>
				<th>Preço unitário</th>
				<th>Total</th>
			</tr>
		</thead>
		
		<tbody class="txt-hardgray">
			<?php
			foreach( $res as $rowp )
			{
				$fetch_prod = db::row( ShopDBConsts::TABLE_PRODUCTS, '*', array('id_produto' => $rowp['id_produto']));
			?>
			<tr>
				<td><?php echo $fetch_prod['nome']; ?></td>
				<td style="text-align: center;"><?php echo $rowp['quantidade']; ?></td>
				<td><?php echo FormatLayer::FormatItemPrice((float) $rowp['preco_item']); ?><?php if(1) echo ' (R$ 3,00 p/ presente)'; ?></td>
				<td><?php echo FormatLayer::FormatItemPrice((float) $rowp['preco_item'], (int) $rowp['quantidade']); ?></td>
			</tr>
			<?php
			}
			?>
		</tbody>
	</table>
	
	<div id="orderinfo">
		<div id="wraper">
			<strong class="txt-hardgray">Estado do pedido:</strong>
			<img src="<?php echo DisplayLayer::LayoutImagePath('public/images/order_states/'.$order->GetIconStateImage()); ?>" />
			
			<strong class="txt-hardgray">Código de rastreamento:</strong>
			<?php if( $order->state==OrderStateConsts::STATE_STEP3 ) { ?>
				<?php
				$data = $order->GetSubstateData();
				if( !empty($data['rastreamento']) )
					echo $data['rastreamento'];
				else
					echo 'Não disponível';
				?>
			<?php } else { ?>
				Pedido ainda não confirmado
			<?php } ?>
			
			<br /><br /><hr />
			
			<strong class="txt-hardgray">Detalhes do pedido:</strong>
			<ul>
				<li><span>.</span> <b>Número:</b> <?php echo FormatLayer::FormatOrderID($fetch_order['id_pedido']); ?></li>
				<li><span>.</span> <b>Data de requisição:</b> <?php echo $fetch_order['view_data_ordered']; ?></li>
				<li><span>.</span> <b>Forma de pagamento:</b> <?php echo $order->GetViewPayMode(); ?></li>
			</ul>
			
			<?php if( $fetch_order['order_pay_mode']==ShoppingConsts::PGTO_SERVICE_DEPOSITOBB ) { ?>
				<strong class="txt-hardgray">Dados para o depósito:</strong>
				<div class="textwall-normal">
					<?php echo db::field( ShopDBConsts::TABLE_CONFIG, 'pay_deposito_info' ); ?>
				</div>
			<?php } ?>
		</div>
		
	</div>
</div>


</span>