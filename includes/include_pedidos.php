<?php
if( !Login::$logged )
	Page::$gpage->MarkRedirect()->ThrowReturn();

Site::$view_props->entire_flow = true;

$sql = "SELECT *, 
			DATE_FORMAT(data_ordered, '%d/%m/%Y') as view_data_ordered 
		FROM ".ShopDBConsts::TABLE_ORDER." 
		WHERE 
			id_usuario = ".Login::$login_id." 
			AND state > 0 
			AND state != 6";
$res = db::query($sql);
$view_empty = db::affected()==0;
?>

<span id="pedidos">


<div class="content">

	<div id="identif-hint" style="background-image: url(<?php echo DisplayLayer::LayoutImagePath('public/images/model_label_pedidos.png'); ?>);"></div>
	<hr class="line">
	
	<?php if($view_empty) { ?>
		<div id="empty">
			Você não possui nenhum pedido.
		</div>
	<?php } else { ?>
		<table>
			<thead class="txt-bluish">
				<tr>
					<th width="100" class="tbg"><div></div><!--<img src="public/images/icon_order.png" />-->&nbsp;&nbsp;Número</th>
					<th width="135">Data do pedido</th>
					<th width="100">Total</th>
					<th>Estado</th>
					<th style="text-align: right;">Ações</th>
				</tr>
			</thead>
			
			<tbody class="txt-hardgray">
				<?php
				foreach( $res as $rowp )
				{
					$order = new ActiveOrder($rowp);
				?>
				<tr>
					<td><?php echo FormatLayer::FormatOrderID($rowp['id_pedido']); ?></td>
					<td><?php echo $rowp['view_data_ordered']; ?></td>
					<td>R$ <?php echo FormatLayer::FormatFloatStr($rowp['order_total']); ?></td>
					<td><img src="<?php echo DisplayLayer::LayoutImagePath('public/images/order_states/'.$order->GetIconStateImage()); ?>" /></td>
					<td style="text-align: right;"><a href="?p=pedidos_detalhes&idp=<?php echo $rowp['id_pedido']; ?>"><img src="public/images/btn_text_detalhes.png" /></a></td>
				</tr>
				<?php
				}
				?>
			</tbody>
			
		</table>
	<?php } ?>
</div>


</span>