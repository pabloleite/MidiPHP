<?php
class ValueConsts
{
	const ITEM_PRICE_GIFT = 5.00;
	const FRETE_CEP_ORIGEM = '95082200';
	const PGTO_PARCELAS = 10;
	
	const PESSOA_FISICA = 0;
	const PESSOA_JURIDICA = 1;
}

class ShopDBConsts
{
	const TABLE_PRODUCTS = 'produtos';
	const TABLE_PRODUCTS_ATTRIBS = 'produtos_atributos';
	const TABLE_ORDER = 'loja_pedidos';
	const TABLE_ORDER_PRODUCTS = 'loja_pedidos_produtos';
	const TABLE_USER = 'loja_usuarios';
	const TABLE_CLIENT = 'loja_clientes';
	const TABLE_ADDR = 'loja_clientes_enderecos';
	const TABLE_CONFIG = 'loja_configuracoes';
}


class ShoppingConsts
{
	const FRETE_SERVICE_GRATIS = 'gratis';
	const FRETE_SERVICE_SEDEX = '40010';
	const FRETE_SERVICE_PAC = '41106';
	const FRETE_SERVICE_DEFAULT = self::FRETE_SERVICE_SEDEX;
	
	const PGTO_SERVICE_PDIGITAL = 0;
	const PGTO_SERVICE_DEPOSITOBB = 1;
	const PGTO_SERVICE_BOLETO = 2;
	const PGTO_SERVICE_GATEWAY_REDECARD = 3;
	const PGTO_SERVICE_DEFAULT = self::PGTO_SERVICE_PDIGITAL;
	
	const REDECARD_BANDER_VISA = 'visa';
	const REDECARD_BANDER_MASTER = 'master';
	const REDECARD_BANDER_DINERS = 'diners';
	
	const CUPOM_TYPE_PORCENT = 0;
	const CUPOM_TYPE_DISCONT = 1;
}

class OrderStateConsts
{
	const STATE_INACTIVE	= 0;
	
	const STATE_STEP1		= 1;
	const STATE_STEP2		= 2;
	const STATE_STEP3		= 3;
	
	const STATE_COMPLETE	= 4;
	const STATE_CANCELED	= 5;
	const STATE_REMOVED		= 6;
	
	const SUBSTATE_DONEMARK = 10;
	static $arr_state_steps = array( 1 => 'pedido', 2 => 'pagamento', 3 => 'envio' );
}
?>