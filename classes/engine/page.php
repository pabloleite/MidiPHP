<?php
class Page
{
// STATIC
	static $gpage;
	
	static function StaticConstruct()
	{
		self::$chain_prop = new SessionProp('Chains');
	}
	
// PAGE OVERRIDABLE METHODS
	public function PagePreInclude() {}
	public function PagePostInclude() {}

// PAGE INTERFACE
	public $view_site;
	public $view_data;
	public $view_chain;
	
	public function ChainRetain() //chain data will be maintained for the next same Page
	{
		$this->context->_chain_retain = true;
	}
	
	public function MarkFlush()
	{
		$this->context->_flush = true;
	}
	public function MarkRedirect($urlto)
	{
		if( empty($urlto) )
			$urlto = '.';
		$this->context->_redirect = $urlto;
		return $this;
	}
	public function MarkRedirectChain($includeto)
	{
		if( empty($includeto) )
			$includeto = EngineCore::$include_name;
		$this->context->_redirect = SiteConstants::GET_INC_PARAM_MARK.$includeto;
		$this->context->_chain_next = $includeto;
		return $this;
	}
	public function MarkAlternate($includeto)
	{
		// NYI
	}
	
	public function FlagPostCall()
	{
		$this->context->_postcall = true;
		return $this;
	}
	
	public function ThrowReturn()
	{
		throw new ReturnException();
	}
	
// PRIVATE / CORE INTERFACE
	private $context;
	private static $chain_prop;
	
	static function InternalPageSetup(Page $page, $context)
	{	
		if( self::$gpage )
		{
			$page->context = $context;
			$page->view_site = EngineCore::$view_props;
			$page->view_data = self::$gpage->view_data;
			$page->view_chain = self::$gpage->view_chain;
		} else {
			$page->context = $context;
			$page->view_site = EngineCore::$view_props;
			$page->view_data = new SessionProp( uniqid() );
			$page->view_chain = self::$chain_prop->InheritNewProp('Props');
			
			if( self::$chain_prop->page!=EngineCore::$include_name )
				$page->view_chain->Reset();
			unset(self::$chain_prop->page);
		}
		self::$gpage = $page;
	}
	
	static function InternalPageRelease()
	{
		$page = self::$gpage;
		$page->view_data->Reset();
		
		// Setup chaining props
		//var_dump($page->context);
		//die();
		
		if( $page->context->_chain_retain )
			$page->context->_chain_next = EngineCore::$include_name;
		
		if( $page->context->_chain_next )
			self::$chain_prop->page = $page->context->_chain_next;
		else
			self::$chain_prop->Reset();
	}
}
?>