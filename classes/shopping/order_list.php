<?php
class OrderList extends ArrayObject
{
	static function __CartCreateInstance($session_array)
	{
		$order_list = new self();
		foreach( $session_array as $id => $arr_item )
		{
			$item = new OrderItem($id, $arr_item);
			$order_list[$id] = $item;
		}
		return $order_list;
	}
	
	static function __OrderCreateInstance($order_id)
	{
	}
	
// INTERFACE
	public $total_changed = false;
	public $total = false;
	
	public function ListTotalPrice()
	{
		if( !$this->total || $this->total_changed )
		{
			$this->total_changed = false;
			$this->total = 0;
			foreach( $this as $item )
				$this->total += $item->TotalPrice();
		}
		return $this->total;
	}
}


class OrderItem extends FetchRegister
{
	public $id;
	public $qty;
	public $gift;
	public $options;
	
	public function TotalPrice()
	{
		return $this->UnitPrice() * $this->qty;
	}
	
	public function UnitPrice()
	{
		if( $this->fetch['flag_promocao'] )
			$price = (float) $this->fetch['preco_promocional'];
		else
			$price = (float) $this->fetch['preco'];
		
		if( $this->gift )
			$price += ValueConsts::ITEM_PRICE_GIFT;
		return $price;
	}
	
	public function GetAvailable()
	{
		$avail = (int) $this->fetch['estoque'] - $this->qty;
		return max(0, $avail);
	}
	
	public function GetStoreArray()
	{
		$arr['id'] = $this->id;
		$arr['qty'] = $this->qty;
		$arr['gift'] = $this->gift;
		$arr['options'] = $this->options;
		return $arr;
	}
	
	function __construct($mix, $store_array)
	{
		parent::__construct($mix, ShopDBConsts::TABLE_PRODUCTS, 'id_produto');
		$this->id = (int) $this->fetch['id_produto'];
		$this->qty = (int) $store_array['qty'];
		$this->gift = (bool) $store_array['gift'];
		$this->options = (array) $store_array['options'];
	}
	
	
}
?>