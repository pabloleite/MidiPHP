<?php
// - possui como membro o id do pedido associado
// - após a requisição do pagamento, é duplicada a sessão atual com a lista de itens, opções de frete, prazo, etc
// - assim q é retornado o resultado do pagamento, uma flag é marcada como pedido processado, assim a ultima tela não ira limpar o carrinho 2 vezes
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
		
		// Endereço
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