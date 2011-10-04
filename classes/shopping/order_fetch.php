<?php
require_once 'lib/kirby/kirby.php';
require_once 'config/config.php';
require_once 'classes/utils/properties.php';
require_once 'classes/shopping/consts.php';


class OrderFetch extends FetchRegister
{
	public $client;
	public $addr;
	public $client_addr;
	
	// Constructor
	// $mix: OrderFetch, row or id of the order
	function __construct($mix, $fetch_client=false)
	{
		parent::__construct($mix, ShopDBConsts::TABLE_ORDER, 'id_pedido');
		
		if($fetch_client)
			$this->FetchClient();
	}
	
	function FetchClient($mix)
	{
		assert(empty($this->client));
		
		$this->client = new ClientFetch($mix ? $mix : $this->fetch['id_usuario']);
		$this->client = $this->client->fetch;
		
		return $this;
	}
	
	function FetchAddr($mix)
	{
		assert(empty($this->addr));
		
		$this->addr = new FetchRegister($mix ? $mix : $this->fetch['id_endereco'], ShopDBConsts::TABLE_ADDR, 'id_endereco');
		$this->addr = $this->addr->fetch;
		
		return $this;
	}
	
	function FetchClientAddr($mix)
	{
		assert(empty($this->client_addr));
		
		$this->client_addr = new FetchProp($mix);
		if( !$this->client_addr->HasResult() )
			$this->client_addr->fetch = db::row(ShopDBConsts::TABLE_ADDR, '*', array('id_usuario' => $this->fetch['id_usuario'], 'principal' => 1));
		$this->client_addr = $this->client_addr->fetch;
		
		return $this;
	}
	
	function GetViewPayMode()
	{
		switch( $this->fetch['order_pay_mode'] )
		{
		case ShoppingConsts::PGTO_SERVICE_PDIGITAL:
			$view_paymode = 'Pagamento Digital';
			break;
		case ShoppingConsts::PGTO_SERVICE_DEPOSITOBB:
			$view_paymode = 'Depósito bancário';
			break;
		}
		return $view_paymode;
	}
}

class ActiveOrder extends OrderFetch
{
	public $state;
	
	function __construct($mix)
	{
		parent::__construct($mix);
		$this->state = $this->fetch['state'];
	}
	
	function GetSubstateData($state)
	{
		if( is_null($state) )
			$state = $this->state;
		else
			assert(is_int($state));
		$serial = $this->GetSerializedData('substate_data');
		$data = $serial[ OrderStateConsts::$arr_state_steps[$state] ];
		return (array) $data;
	}
	
	function GetSubstateValue($state)
	{
		if( is_null($state) )
			$state = $this->state;
		else
			assert(is_int($state));
		
		if( $this->state==$state )
			return (int) $this->fetch['substate'];
		$data = GetSubstateData($state);
		return $data['substate'];
	}
	
	function ModifySubstateData($data)
	{
		$data = array_merge($this->GetSubstateData(), $data);
		$data = array( OrderStateConsts::$arr_state_steps[$this->state] => $data );
		return $this->ModifySerializedData('substate_data', $data);
	}
	
	function GetIconStateImage()
	{
		switch( $this->state )
		{
		case OrderStateConsts::STATE_STEP1:
			$img_path = 'status_state1.gif';
			break;
		case OrderStateConsts::STATE_STEP2: 
			if( $this->GetSubstateValue() )
				$img_path = 'status_state2_sub2.gif';
			else
				$img_path = 'status_state2_sub1.gif';
			break;
		case OrderStateConsts::STATE_STEP3: 
			$data = $this->GetSubstateData();
			switch( $data['estado_envio'] )
			{
			case 0 : $img_path = 'status_state3_sub1.gif'; break;
			case 1 : $img_path = 'status_state3_sub2.gif'; break;
			case 2 : $img_path = 'status_state3_sub3.gif'; break;
			}
		case OrderStateConsts::STATE_COMPLETE: 
			$img_status = 'status_state3_sub3.gif';
			break;
		case OrderStateConsts::STATE_CANCELED: 
			$img_status = 'status_state5.gif';
			break;
		default:
			assert(false);
			break;
		}
		return $img_path;
	}
}

class ClientFetch extends FetchProp
{
	public $addr;
	
	function __construct($mix)
	{
		parent::__construct($mix);
		
		if( !$this->HasResult() )
		{
			assert(!empty($mix));
			$res = db::query("SELECT *, DATE_FORMAT(login_joined, '%d/%m/%Y') AS view_joined FROM ".ShopDBConsts::TABLE_USER." WHERE id_usuario = $mix LIMIT 1"); 
			$this->fetch = $res[0];
			
			$res = db::query("SELECT *, DATE_FORMAT(ident_nascimento, '%d/%m/%Y') AS view_nascimento FROM ".ShopDBConsts::TABLE_CLIENT." WHERE id_cliente = {$this->fetch['id_cliente']} LIMIT 1"); assert($res);
			$this->fetch += $res[0];
		}
	}
	
	function FetchAddr($mix)
	{
		$this->addr = new FetchProp($mix);
		if( !$this->addr->HasResult() )
			$this->addr->fetch = db::row(ShopDBConsts::TABLE_ADDR, '*', array('id_usuario' => $this->fetch['id_usuario'], 'principal' => 1));
		$this->addr = $this->addr->fetch;
		
		return $this;
	}
	
	function GetPersonTypeStr()
	{
		return $this->fetch['cliente_tipo']==ValueConsts::PESSOA_FISICA ? 'Pessoa física' : 'Pessoa jurídica';
	}
}
?>