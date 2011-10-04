<?php
// - possui como membro o id do pedido associado
// - ap�s a requisi��o do pagamento, � duplicada a sess�o atual com a lista de itens, op��es de frete, prazo, etc
// - assim q � retornado o resultado do pagamento, uma flag � marcada como pedido processado, assim a ultima tela n�o ira limpar o carrinho 2 vezes
class ShopFinalize
{
	private $session;
	private $order_id;
	private $values;
	
	public function __construct()
	{
		$this->session = new SessionProp('Finalize');
	}
	
	public function FinalizeCheckout()
	{
		// Copy checkout state
		$checkout = Cart::GetActiveCheckout();
		$checkout_session = new SessionProp('Checkout');
		$this->session->checkout = $checkout_session->GetArray();
		
		// Endere�o
		$main_addr = $checkout->AddrQueryClient();
		if( $checkout_session->addr_adjusted || $main_addr['ender_cep']!=$checkout_session->frete['cep'] )
			$addr_id = db::insert(ShopDBConsts::TABLE_ADDR, $checkout_session->addr_fields);
		else
			$addr_id = $main_addr['id_endereco'];
		
		// Pedido
		$values['id_usuario'] = Login::$login_id;
		$values['id_endereco'] = $addr_id;
		$values['data_ordered'] = 'NOW()';
		$values['state'] = 0;
	}
	
	public function OrderInsert()
	{
		
	}
	
	public function OrderActiveState($values)
	{
		
	}
}
?>