<?php
define('_PD_URL_RETORNO', SiteConstants::URL_ABSOLUTE.SiteConstants::GET_INC_PARAM_MARK.'gatepd');


interface PADGatewayConsts
{
	const PD_EMAIL				= 'financeiro@gostodobrasil.com.br';
	const PD_URL_RETORNO		= _PD_URL_RETORNO;
	
	const PD_STATUS_INVALIDO	= -1;
	const PD_STATUS_ANDAMENTO	= 0;
	const PD_STATUS_CONCLUIDO	= 1;
	const PD_STATUS_CANCELADO	= 2;
}

class GatewayPagamentoDigital implements InternalGatewayConstants
{
	static function InitService()
	{
	?>
		<style type="text/css">
			form { <?php if(!G_BUILDDEBUG) echo 'display: none;'; ?> }
			form input { display: block; }
		</style>
	<?php
	}
	
	
	// PROCEDURES
	static function Procedure1_SubmitTransaction(OrderList $list, $orderid, $total, $frete)
	{
		assert(is_float($total));
		assert(is_float($frete));
	?>
	
	<script type="text/javascript">
		window.onload=function() {
			document.forms['submit_form'].submit();
		}
	</script>
	
	<form name="pagamentodigital" action="https://www.pagamentodigital.com.br/checkout/pay/" method="post" id="submit_form">
		<?php foreach( $list as $item ) { $in++; ?>
			produto_codigo_<?php echo $in; ?><input name="produto_codigo_<?php echo $in; ?>" type="input" value="<?php echo $item->id; ?>"> 
			produto_descricao_<?php echo $in; ?><input name="produto_descricao_<?php echo $in; ?>" type="input" value="<?php echo $item->fetch['nome']; ?>">
			produto_qtde_<?php echo $in; ?><input name="produto_qtde_<?php echo $in; ?>" type="input" value="<?php echo $item->qty; ?>"> 
			produto_valor_<?php echo $in; ?><input name="produto_valor_<?php echo $in; ?>" type="input" value="<?php echo $item->GetItemPrice(); ?>" >
		<?php } ?>
		
		frete<input name="frete" type="input" value="<?php echo $frete; ?>">
		id_pedido<input name="id_pedido" type="input" value="<?php echo $orderid; ?>">
		email_loja<input name="email_loja" type="input" value="<?php echo InternalGatewayConstants::PD_EMAIL; ?>">
		tipo_integracao<input name="tipo_integracao" type="input" value="PAD">
		url_retorno<input name="url_retorno" type="input" value="<?php echo InternalGatewayConstants::PD_URL_RETORNO; ?>" />
		redirect<input name="redirect" type="input" value="true" />
	</form>
	
	<?php
	}
	
	static function Procedure2_ConfirmAuthenticity()
	{
		$token = '136f7e253e133b2d0ded80abecfb3b36';
		
		$id_transacao = $_POST['id_transacao'];
		$data_transacao = $_POST['data_transacao'];
		$data_credito = $_POST['data_credito'];
		$valor_original = $_POST['valor_original'];
		$valor_loja = $_POST['valor_loja'];
		$valor_total = $_POST['valor_total'];
		$desconto = $_POST['desconto'];
		$acrescimo = $_POST['acrescimo'];
		$tipo_pagamento = $_POST['tipo_pagamento'];
		$parcelas = $_POST['parcelas'];
		$cliente_nome = $_POST['cliente_nome'];
		$cliente_email = $_POST['cliente_email'];
		$cliente_rg = $_POST['cliente_rg'];
		$cliente_data_emissao_rg = $_POST['cliente_data_emissao_rg'];
		$cliente_orgao_emissor_rg = $_POST['cliente_orgao_emissor_rg'];
		$cliente_estado_emissor_rg = $_POST['cliente_estado_emissor_rg'];
		$cliente_cpf = $_POST['cliente_cpf'];
		$cliente_sexo = $_POST['cliente_sexo'];
		$cliente_data_nascimento = $_POST['cliente_data_nascimento'];
		$cliente_endereco = $_POST['cliente_endereco'];
		$cliente_complemento = $_POST['cliente_complemento'];
		$status = $_POST['status'];
		$cod_status = $_POST['cod_status'];
		$cliente_bairro = $_POST['cliente_bairro'];
		$cliente_cidade = $_POST['cliente_cidade'];
		$cliente_estado = $_POST['cliente_estado'];
		$cliente_cep = $_POST['cliente_cep'];
		$frete = $_POST['frete'];
		$tipo_frete = $_POST['tipo_frete'];
		$informacoes_loja = $_POST['informacoes_loja'];
		$id_pedido = $_POST['id_pedido'];
		$free = $_POST['free'];
		
		// TO-DO conseguindo pegar o retorno do PAD, caso resultado seja PD_STATUS_INVALIDO, alterar cod_status para PD_STATUS_INVALIDO para ser serializado com esse status
		
		/*$get = sprintf( "?transacao=%s" .
						"&status=%s" .
						"&cod_status=%s" .
						"&valor_original=%s" .
						"&valor_loja=%s" .
						"&token=%s", 
						urlencode($id_transacao), 
						urlencode($status), 
						urlencode($cod_status), 
						urlencode($valor_original), 
						urlencode($valor_loja), 
						urlencode($token) );
		$endereco = "https://www.pagamentodigital.com.br/checkout/verify/";
		ini_set("allow_url_fopen", 1);// habilita acesso a arquivos via URL
		$res = @file_get_contents($endereco.$get);
		
		
		// ************************ Armazena e retorna os dados ***************
		if( $res!='VERIFICADO' )
			$cod_status = self::PD_STATUS_INVALIDO;*/
		$ret_data = compact('cod_status', 'id_transacao', 'id_pedido', 'tipo_pagamento');
		return $ret_data;
	}
}
?>