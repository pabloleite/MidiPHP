<?php
set_include_path('../../');

require 'lib/kirby/kirby.php';
require 'config/config.php';
require 'defines.php';
require 'funcs.php';

s::start();


require_once 'classes/utils/ajaxcall.php';
require_once 'classes/shopping/consts.php';
require_once 'classes/shopping/cart.php';
Cart::StaticConstruct();


class AjaxCheckout extends AjaxCall
{
	// request handlers --------------------------------------------------------------------------------------
	protected function Remove()
	{
		$list = Cart::GetOrderList();
		Cart::$gcart->ItemRemove($this->id);
		
		if( $list->count()==0 )
			$this->SetReturnValue( 'empty', true );
		$this->SetReturnValue( 'item_count', Cart::$gcart->ItemCount() );
	}
	
	protected function UpdateQty()
	{
		$list = Cart::GetOrderList();
		$item = $list[$this->id];
		$setqty = Cart::$gcart->ItemSetQty($item, $this->newqty);
		
		$this->SetReturnValue( 'qty', $setqty );
		$this->SetReturnValue( 'item_total', FormatLayer::FormatCurrency($item->TotalPrice()) );
	}
	
	protected function CheckGift()
	{
		$list = Cart::GetOrderList();
		$item = $list[$this->id];
		Cart::$gcart->ItemSetGift($item, true);
		
		$this->SetReturnValue( 'item_unit', FormatLayer::FormatCurrency($item->UnitPrice()) );
		$this->SetReturnValue( 'item_total', FormatLayer::FormatCurrency($item->TotalPrice()) );
	}
	
	protected function UncheckGift()
	{
		$list = Cart::GetOrderList();
		$item = $list[$this->id];
		Cart::$gcart->ItemSetGift($item, false);
		
		$this->SetReturnValue( 'item_unit', FormatLayer::FormatCurrency($item->UnitPrice()) );
		$this->SetReturnValue( 'item_total', FormatLayer::FormatCurrency($item->TotalPrice()) );
	}
	
	// overrides --------------------------------------------------------------------------------------
	protected function CommonHandler() // will always be executed for each ajax request
	{
		$list = Cart::GetOrderList();
		$this->SetReturnValue( 'total', FormatLayer::FormatFloatStr($list->ListTotalPrice()) );
	}
}

$exec = new AjaxCheckout();
$exec->JSONreturn();
?>