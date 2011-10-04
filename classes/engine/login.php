<?php
define('_LOGIN_PWD_HASHING', 'SHA1');
define('_LOGIN_TABLE_NAME', 'loja_usuarios');
define('_LOGIN_TABLE_KEY', 'id_usuario');

require_once 'classes/utils/properties.php';
require_once 'classes/shopping/order_fetch.php';


class Login
{
// STATIC
	static $glogin;
	
	static $logged = false;
	static $login_id;
	static $login_fetch;
	static $user_data;
	
	static function StaticConstruct()
	{
		self::$glogin = new self;
	}
	
// PUBLIC
	function ManualLogin()
	{
		$ret = $this->do_login($_POST['email'], $_POST['pwd'], true);
		$this->session_status();
		return $ret;
	}
	
	function HadFailAttempt()
	{
		return $this->session->fail;
	}
	
	function RenewLoginFetch()
	{
		$this->session->fetch = db::row(_LOGIN_TABLE_NAME, '*', array(_LOGIN_TABLE_KEY => self::$login_id));
	}
	
	
// PRIVATE
	private $session;
	
	private function __construct($request_param = 'login_action')
	{
		$this->session = new SessionProp('login');
		
		switch( $_REQUEST[$request_param] )
		{
		case 'login' 	: $this->do_login($_POST['email'], $_POST['pwd']); break;
		case 'logout' 	: $this->do_logout(); break;
		}
		$this->session_status();
	}
	
	private function session_status()
	{
		self::$logged = $this->session->access;
		if( self::$logged )
		{
			self::$login_fetch = $this->session->fetch;
			self::$login_id = self::$login_fetch[_LOGIN_TABLE_KEY]; //0, needs special fetching
			unset( $this->session->fail );
			
			// User data
			self::$user_data = new ClientFetch(self::$login_id);
			self::$user_data = self::$user_data->fetch;
		}
	}
	
	private function do_login($user, $password, $manual = false)
	{
		$user = db::escape(trim($user));
		$password = db::escape(trim($password));
		
		$bAccessOK = !empty($user) && !empty($password);
		if($bAccessOK)
		{
			$sql = "SELECT * 
					FROM "._LOGIN_TABLE_NAME." 
					WHERE 
						login_ativo = '1' 
						AND login_email = '$user'
						AND login_senha = " ._LOGIN_PWD_HASHING. "('$password')";
			$res = db::query($sql, false);
		}
		
		$bAccessOK = $bAccessOK && db::affected()==1;
		if($bAccessOK)
		{
			$this->session->access = true;
			$this->session->fetch = db::fetch($res);
			
			if( $manual )
				$this->session_status();
		} else {
			$this->session->fail = true;
		}
		
		return $bAccessOK;
	}
	
	private function do_logout()
	{
		assert( !headers_sent() );
		session_destroy();
		header('Location: .');
		die();
	}
}
?>