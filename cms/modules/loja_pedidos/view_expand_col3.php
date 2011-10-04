<?php
?>
<td valign="top" class="stepcol">
	<div id="suboverlay">
		<img src="gfxnew/expandtbl_indicator.gif" /><br />
		<img src="gfxnew/expandtbl_n3.gif" />
		<div id="sizewrap">
			<?php if( $fetch->fetch['order_frete_forma']!=ShoppingConsts::FRETE_SERVICE_GRATIS ) { ?>
				<p class="limarkup">Forma de envio selecionada: <strong><?php echo $fetch->fetch['order_frete_forma']==ShoppingConsts::FRETE_SERVICE_PAC ? 'PAC' : 'Sedex'; ?></strong></p><br />
			<?php } ?>
			
			<div class="separ"><div></div></div>
			<p class="limarkup">
				Situação do envio:
				<select id="send-state" name="state3_estado_envio">
					<option value="0" <?php if( $scol->data['estado_envio']==0 ) echo 'selected="selected"'; ?>>Pronto para envio</option>
					<option value="1" <?php if( $scol->data['estado_envio']==1 ) echo 'selected="selected"'; ?>>Enviado</option>
					<option value="2" <?php if( $scol->data['estado_envio']==2 ) echo 'selected="selected"'; ?>>Entregue</option>
				</select>
			</p><br />
			<p class="limarkup">Código de rastreamento: <input type="text" id="knowcod-input" name="state3_rastreamento" value="<?php echo $scol->data['rastreamento']; ?>" /></p><br />
			<p><label id="state3_knowcod_label"><input type="checkbox" id="knowcod-check" name="state3_knowcod_check" />Enviar e-mail com o número do rastreamento</label></p><br />
			<a href="" id="refresh"><img src="gfxnew/expandtbl_btn_enviorefresh.gif" style="margin-top: 5px;" /></a>
			<div class="separ"><div></div></div>
			
			<div id="zover-blank">
				<p class="limarkup">Dados para envio:</p>
				<p class="databox">
					<?php echo $fetch->client['ident_nome']; ?><br />
					<?php echo $fetch->addr['ender_endereco']; ?>, <?php echo $fetch->addr['ender_numero']; ?>, <?php echo $fetch->addr['ender_bairro']; ?><br />
					<?php echo $fetch->addr['ender_cidade']; ?> - <?php echo $fetch->addr['ender_estado']; ?><br />
					<?php
					if( !empty($fetch->addr['ender_complemento']) )
						echo $fetch->addr['ender_complemento'].'<br />';
					?>
					CEP: <?php echo $fetch->addr['ender_cep']; ?>
				</p>
			</div>
		</div>
		
		<?php if( $scol->shownext ) { ?>
			<a href="" id="actionnext"><img src="gfxnew/expandtbl_c3.gif" /></a>
		<?php } else {?>
			<img src="gfxnew/expandtbl_c3.gif" <?php if($scol->actual) echo 'id="btnfaded"';?> />
		<?php } ?>
	</div>
	
	<?php
	if( !$scol->actual )
		echo '<div id="overlay"></div>';
	?>
</td>