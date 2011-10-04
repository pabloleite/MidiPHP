<?php
/*************************************************************************
GLOBAL DEFINES SETTINGS THAT CAN BE CHANGED

-CFG_SCHEMA
	'ajax' or 'xml' or 'html' or 'css'

-CFG_FORCE_DEBUG
	true or false

**************************************************************************/


defined('CONFIG_GUARD') ? die('config.php - Once inclusion error') : define('CONFIG_GUARD', true);


class Config extends Base
{
	protected static function BuildOptions()
	{
		// Setup configuration based on the build mode
		switch(G_BUILDMODE)
		{
		case 'DEBUG':
			ini_set('display_errors', 'On');
			ini_set('xdebug.var_display_max_depth', 10);
			error_reporting(E_ALL);
			error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
			
			// javascript tracing proxy
			if(0)
			{
				require_once 'classes/debug.class.php';
				global $dbg;
				$dbg = new PHPDebug();
			}
			break;
			
		case 'RELEASE':
			ini_set('display_errors', 'Off');
			break;
		
		}
	}
	
	protected static function SchemaSetup()
	{
		// Configuration parameters based on the schema being requested
		if( G_IS_AJAX )
			define('CFG_SCHEMA', 'ajax');
		if( end(explode('.', $_SERVER['SCRIPT_NAME']))=='css' )
			define('CFG_SCHEMA', 'css');
		
		switch(@CFG_SCHEMA)
		{
		case 'ajax':
			self::$_asserts_die_output = true;
			break;
		case 'xml':
			self::$_asserts_die = true;
			break;
		case 'css':
			header('Content-Type: text/css');
			break;
		default:
			define('CFG_SCHEMA', 'html');
		case 'html':
			self::$_asserts_die_output = true;
			
			$expires = 60*60*24*14;
			header('Pragma: public');
			header('Cache-Control: maxage='.$expires);
			header('Expires: ' . gmdate('D, d M Y H:i:s', time()+$expires) . ' GMT');
			break;
		}
	}
	
	protected static function Includes()
	{
		// Common includes
		include 'config_db.php';
		include 'config_email.php';
		
		// Logging
		include 'log_db_onconnect.php';
	}
	
}



class Base
{
	protected static $_asserts_die = false;
	protected static $_asserts_die_output = false;
	
	
	public static function Starter()
	{
		$localsite	= $_SERVER['HTTP_HOST']=='server' || $_SERVER['HTTP_HOST']=='localhost' || $_SERVER['HTTP_HOST']=='gamba-cromo';
		$buildmode	= $localsite || (defined('CFG_FORCE_DEBUG') && CFG_FORCE_DEBUG) ? 'DEBUG' : 'RELEASE';
		
		define('G_LOCALSITE', $localsite);
		define('G_SITEMODE', $localsite ? 'desenvolvimento' : 'online'); //for db config
		define('G_BUILDMODE', $buildmode);
		define('G_BUILDDEBUG', $buildmode=='DEBUG');
		define('G_IS_AJAX', r::ajax());
		
		// Derived call
		Config::SchemaSetup();
		Config::BuildOptions();
		
		// Enable assertions
		assert_options(ASSERT_CALLBACK, array('Base', 'AssertionsCallback'));
		assert_options(ASSERT_ACTIVE, true);
		
		// General options setted, now it is safe to include files
		Config::Includes();
	}
	
	
	public static function AssertionsCallback($file, $line, $code)
	{
		if(G_BUILDDEBUG && Base::$_asserts_die_output)
		{
			error_reporting(E_ALL);
			echo "<hr>Assertion Failed:
					File '$file'<br />
					Line '$line'<br />
					Code '$code'<br />
				  <hr />
				  <br />Call stack:<br /><pre>";
			debug_print_backtrace();
			//trigger_error('Method [' . __FUNCTION__ . "] failed [Q: $code]", E_USER_ERROR);
		}
		
		if(Base::$_asserts_die || Base::$_asserts_die_output)
			die();
	}
	
}

Base::Starter();
?>