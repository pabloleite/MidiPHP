<?php
require_once 'classes/engine/page.php';
require_once 'classes/utils/properties.php';

define('_DEFAULT_PATH',	'includes/');
define('_PREFIX_DECORATE', 'interna,include,return');


abstract class EngineCore
{
// Force Extending class to define this methoda
    abstract protected function StaticConstructCalls();
    abstract protected function AlternateInclude(_RequestContext $context);
	
// STATIC
	static $gsite;	//antes eu usava variaveis globais mesmo, ficava mais simples, mas dava muita confusão quando precisava acessar essas variaveis dentro do escopo de funçoes ja que elas nao estão acessiveis,
					//precisando primeiro acessar o escopo global usando a keyword 'global'. Com essa maneira estatica, nao precisa usar 'global' e fica mais claro a origem dessas variaveis: Page::$gpage
	static $view_props;
	static $include_name;
	
// PUBLIC
	function __construct()
	{
		self::$gsite = $this;
		self::$view_props = new SimpleProp();
		
		// Site utilitary classes construct
		Page::StaticConstruct();
		$this->StaticConstructCalls();
		
		
		/*************************************************************************/
		// Set the file being included
		// Calls routing alternative to select what to include
		$context = new _RequestContext();
		$context->_path 		= _DEFAULT_PATH;
		$context->_get 			= $_GET[SiteConstants::GET_INC_PARAM];
		$context->_default		= 'home';
		$this->AlternateInclude($context);
		
		// Define path where to search the include file
		$default_include = $context->_default;
		$prefix_search = $context->_get!=$default_include && !empty($context->_get);
		if( $prefix_search )
		{
			foreach( explode(',', _PREFIX_DECORATE) as $prefix )
			{
				$file_path = $context->_path . $prefix . '_' . $context->_get . '.php';
				if( $prefixed_found = is_file($file_path) )
					break;
			}
		}
		
		// Define the file being included and whether it should be the default include
		if( $prefixed_found )
			self::$include_name = $context->_get;
		else {
			self::$include_name = $default_include;
			$file_path = _DEFAULT_PATH . self::$include_name . '.php';
		}
		/*************************************************************************/
		
		
		/*************************************************************************/
		// Instances the page class
		$page_context = $this->context = new _PageContext();
		Page::InternalPageSetup( new Page(), $page_context );
		
		// Buffering method that assembles the entire site output in 2 parts
		// 1-First buffers and process the include output
		// 2-Second is where the site.php and the included portion are joined
		
		// First part - buffer/process the include portion of the site
		ob_start();
		try
		{
			require $file_path;
			assert(ob_get_level()==1);
		}
		catch(ReturnException $e)
		{
		}
		$this->include_cache = ob_get_clean();
		
		// Releases the page
		Page::InternalPageRelease();
		
		// Handles Page context flags
		if( $page_context->_redirect )
		{
			assert(!headers_sent());
			header('Location: '.$page_context->_redirect);
			echo $this->include_cache;
			die();
		}
		
		// Second part - buffer site.php which should call PrintIncludeOutput() in order to join the include buffer
		ob_start( array($this, 'ObSiteHandler') );
		require _DEFAULT_PATH.'site.php';
		assert(ob_get_level()==1);
		/*************************************************************************/
		
		
		// Force the client to not wait in case we have a post processing flag - http://stackoverflow.com/questions/138374/php-close-a-connection-early
		if( $page_context->_postcall )
		{
			ignore_user_abort(true);
			session_write_close();
			header('Content-Length: '.ob_get_length());
			header('Connection: close');
		}
		
		// Buffer output
		assert(!headers_sent());
		ob_end_flush();
		flush();
		
		// Post processing; site already sent to the client
		if( $page_context->_postcall )
			Page::$gpage->PagePostInclude();
	}
	
	public static function LinkIncludePage(Page $page)
	{
		$context = self::$gsite->context;
		$context->_linked = true;
		
		Page::InternalPageSetup($page, $context);
		Page::$gpage->PagePreInclude();
		return $page;
	}
	
	public static function PrintIncludeOutput()
	{
		echo self::$gsite->include_cache;
	}
	
	
// PRIVATE
	private $context;
	private $include_cache;
	
	private function ObSiteHandler( $buffer, $mode )
	{
		return $buffer;
		//return ($gzbuffer = ob_gzhandler($buffer, $mode))!==FALSE ? $gzbuffer : $buffer;
	}
	
}


// INTERNAL CLASSES
class ReturnException extends Exception
{
}

class _RequestContext
{
	public $_path;
	public $_get;
	public $_default;
}

class _PageContext
{
	public $_linked			= false;
	public $_flush			= false;
	public $_redirect		= false;
	public $_chain_next		= false;
	public $_chain_retain	= false;
	public $_postcall		= false;
}
?>