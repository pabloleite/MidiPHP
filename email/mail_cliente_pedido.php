<?php
require_once 'compound.php';
$local_mc = new MailCompound();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<?php include 'mailstyles.php'; ?>
</head>


<body class="txt-default">

<div id="wraper" class="mail-sized">

	<!-- CONTEUDO BEGIN -->
	<div id="main" class="cliente_pedido">
		<div id="top" class="bline-mark">
			<img src="<?php $local_mc->PrintImageAddr('logo.png'); ?>" />
			<h1>Recebemos o seu pedido</h1>
		</div>
		
		<div id="subject">
			<h2><?php echo $mail_data['nome']; ?>,</h2>
			Obrigado por comprar na Rumo Certo.<br />Segue abaixo o sumário da sua compra em nosso site.
		</div>
		
		<div id="text" class="bline-mark">
			<h3>Identificação do pedido</h3>
			<ul>
				<li><div class="bullet"></div>Número do pedido: <strong><?php echo $mail_data['orderid']; ?></strong></li>
				<li><div class="bullet"></div>Data do pedido: <strong><?php echo $mail_data['data']; ?></strong></li>
				<li><div class="bullet"></div>Situação: <strong class="txt-redbold"><?php echo $mail_data['state']; ?></strong></li>
				<li><div class="bullet"></div>Código de rastreamento: <strong class="txt-redbold"><?php echo $mail_data['rastreamento']; ?></strong></li>
			</ul>
			
			<br />
			<h3>Lista de produtos e total do pedido</h3>
			<table width="100%" cellspacing="0" cellpadding="0">
				<tr class="header">
					<td>&nbsp;</td>
					<td class="tcenter">Qnt</td>
					<td class="tcenter" width="90">Preço unitário</td>
					<td style="padding-right: 0;">Total</td>
				</tr>
				
				<tr valign="top" class="item">
					<td>Notebook Acer Aspire c/ Intel® Pentium Dual Core P6000</td>
					<td class="tcenter">10x</td>
					<td class="tcenter">10,00<!--<br />(+ 3,00 p/ presente)--></td>
					<td class="tright">10,00</td>
				</tr>
				
				<tr class="total">
					<td class="sheet" colspan="3">Subtotal:</td>
					<td class="tright">100,00</td>
				</tr>
				
				<tr class="total">
					<td class="sheet" colspan="3">Frete:</td>
					<td class="tright">+100,00</td>
				</tr>
				
				<tr class="total">
					<td class="sheet" colspan="3">Total:</td>
					<td class="tright"><u>100,00</u></td>
				</tr>
			</table>

		</div>
		
		<div id="fot">
			<a href="<?php echo SiteConstants::URL_ABSOLUTE; ?>">www.rumocerto.com.br</a>
			<div id="side-info">E-mail enviado em <?php echo date('j/m/Y'); ?> às <?php echo date('G:i'); ?></div>
		</div>
	</div>
	<!-- CONTEUDO END -->
	
</div>

</body>

</html>
<?php
$local_mc->EndMessage();
?>