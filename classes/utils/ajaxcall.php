<?php
class AjaxCall
{
	private $params;
	protected $json_return = array();
	
	function __construct()
	{
		$this->params = $_GET;
		
		assert( isset($this->params['func']) );
		assert( method_exists($this, $this->params['func']) );
		
		$func_call = $this->params['func'];
		unset($this->params['func']);
		$this->$func_call();
		$this->CommonHandler();
	}
	
	protected function __get($name)
	{
		return $this->params[$name];
	}
	
	protected function __isset($name)
	{
		return isset($this->params[$name]);
	}
	
	protected function SetReturnValue($name, $value=true)
	{
		$this->json_return[$name] = $value;
	}
	
	public function JSONreturn()
	{
		//echo json_encode( array_map(utf8_encode, $this->json_return) );
		echo json_encode( $this->json_return );
	}
}
?>