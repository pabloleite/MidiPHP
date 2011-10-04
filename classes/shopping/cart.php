<?php
require_once 'classes/utils/properties.php';
require_once 'order_list.php';
require_once 'shop_checkout.php';
require_once 'shop_finalize.php';


class Cart
{
// STATIC
	static $gcart;
	
	static function StaticConstruct()
	{
		self::$gcart = new self;
	}
	
	static function GetOrderList()
	{
		return self::$gcart->instance_list;
	}
	
	static function GetActiveCheckout()
	{
		if( !self::$gcart->instance_active )
			self::$gcart->instance_active = new ShopActiveCheckout();
		return self::$gcart->instance_active;
	}
	
	static function GetFinalizeCheckout()
	{
		if( !self::$gcart->instance_finalize )
			self::$gcart->instance_finalize = new ShopFinalize();
		return self::$gcart->instance_finalize;
	}
	
// INTERFACE
	public function InsertProduct($id, $options)
	{
		$item = new OrderItem($id, array());
		$item->qty = 1;
		$item->options = $options;
		$this->session->cart_itens[$id] = $item->GetStoreArray();
	}
	
	public function ClearCartItens()
	{
		$this->instance_list->exchangeArray(array());
		unset($this->session->cart_itens);
	}
	
	public function ItemCount()
	{
		return count($this->session->cart_itens);
	}
	
	public function ItemRemove( $id )
	{
		unset($this->instance_list[$id]);
		unset($this->session->cart_itens[$id]);
	}
	
	public function ItemSetQty( OrderItem $item, $qty )
	{
		$qty = intval($qty);
		$item->qty = min($item->fetch['estoque'], max(1, $qty));
		$this->session->cart_itens[$item->id] = $item->GetStoreArray();
		return $item->qty;
	}
	
	public function ItemSetGift( OrderItem $item, $b )
	{
		$item->gift = $b;
		$this->session->cart_itens[$item->id] = $item->GetStoreArray();
	}
	
	public function ItemSetOptions( OrderItem $item, $options )
	{
		$item->options = $options;
		$this->session->cart_itens[$item->id] = $item->GetStoreArray();
	}
	
	public function GetItem($id)
	{
		if( isset($this->session->cart_itens[$id]) )
			return $this->instance_list[$id];
		return new OrderItem($id, array());
	}
	
	
// PRIVATE
	private $session;
	private $instance_list;
	private $instance_active;
	private $instance_finalize;
	
	private function __construct()
	{
		$this->session = new SessionProp('Cart');
		$this->instance_list = OrderList::__CartCreateInstance( $this->session->cart_itens );
	}
}
?>