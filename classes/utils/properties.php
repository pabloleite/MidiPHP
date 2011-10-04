<?php

// Classe representando um registro de tabela e a respectiva array de dados
class FetchRegister extends FetchProp
{
	// $mix: FetchProp ou derivada, array, id do registro
	public function __construct($mix, $table_name, $table_key)
	{
		parent::__construct($mix);
		
		if( !$this->HasResult() && $mix )
		{
			assert(!empty($mix));
			$this->fetch = db::row( $table_name, '*', array($table_key => intval($mix)) );
		}
	}
	
	public function FromArray()
	{
	}
	
	public function Insert()
	{
	}

}

// Classe representando um array de dados
// Pode ser contruìda apartir de outro array ou de uma instancia da mesma classe
class FetchProp
{
	public $fetch;
	private $serial_cache = array();
	
	// $mix: FetchProp ou derivada, array
	public function __construct($mix)
	{
		if( is_array($mix) )
			$this->fetch = $mix;
		elseif( is_object($mix) && (get_class($mix)==get_class($this) || assert(false)) )
			$this->fetch = $mix->fetch;
	}
	
	public function HasResult()
	{
		return !empty($this->fetch);
	}
	
	protected function GetSerializedData($field)
	{
		if( $cache=$this->serial_cache[$field] )
			return $cache;
		$this->serial_cache[$field] = ($cache=unserialize($this->fetch[$field])) ? $cache : array();
		return $this->serial_cache[$field];
	}
	protected function ModifySerializedData($field, $merge_data)
	{
		return $this->fetch[$field] = serialize( array_merge($this->GetSerializedData($field), $merge_data) );
	}
}


class SessionProp //usar na ActiveOrder
{
	private $name;
	private $session;
	private $inherited = false;
	
	
	public function __construct($name, $parent=null)
	{
		assert( !empty($name) );
		
		$this->name = $name;
		if( $parent )
		{
			$this->session = &$_SESSION['SessionProp_'.$parent->name]['Inherits_'.$name];
			$this->inherited = $parent;
		} else {
			$this->session = &$_SESSION['SessionProp_'.$name];
		}
	}
	
	public function __destruct()
	{
		if( empty($this->session['values']) && !$this->inherited )
		{
			unset($_SESSION['SessionProp_'.$this->name]);
			//echo 'wtf - '.'SessionProp_'.$this->name);
		}
	}
	
	public function InheritNewProp($name)
	{
		assert( !empty($name) );
		assert( $this->inherited==false );
		
		$sp = new self($name, $this);
		return $sp;
	}
	
	public function Reset()
	{
		unset( $this->session['values'] );
	}
	public function Serialize()
	{
		//verificar se a array está vazia, e retornar '' pois serialize retorna "N;"
	}
	
	public function GetArray()
	{
		return $this->session['values'];
	}
	
	public function __set($name, $value)
	{
		$this->session['values'][$name] = $value;
	}
	public function &__get($name)
	{
		return $this->session['values'][$name];
	}
	public function __unset($name)
	{
		unset($this->session['values'][$name]);
	}
}

class SimpleProp
{
	private $props;
	
	public function __construct()
	{
		$this->props = array();
	}
	
	public function __set($name, $value)
	{
		$this->props[$name] = $value;
	}
	
	public function &__get($name)
	{
		return $this->props[$name];
	}
	
	public function __unset($name)
	{
		unset($this->props[$name]);
	}
}
?>