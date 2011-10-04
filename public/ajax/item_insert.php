<?php
set_include_path('../../');

require 'lib/kirby/kirby.php';
require 'config/config.php';
require 'defines.php';

s::start();


require_once 'classes/utils/ajaxcall.php';
require_once 'classes/shopping/consts.php';
require_once 'classes/shopping/cart.php';
Cart::StaticConstruct();

class AjaxInsert extends AjaxCall
{
	// request handlers --------------------------------------------------------------------------------------
	protected function InsertProduct()
	{
		if( isset($this->attrib_size) )
			$options = array('attrib_has_size' => true, 'attrib_size_val' => intval($this->attrib_size));
		Cart::$gcart->InsertProduct($this->id, $options);
	}
	
	// overrides --------------------------------------------------------------------------------------
	protected function CommonHandler() // will always be executed for each ajax request
	{
		$this->SetReturnValue( 'cart_count', Cart::$gcart->ItemCount() );
	}
}

$exec = new AjaxInsert();
$exec->JSONreturn();
?>