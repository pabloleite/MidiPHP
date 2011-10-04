<?	
class AuthUser {
	private $loginPage = "";
	private $mainPage = "";
	private $pwdCodification = "";
	
	public function __construct($loginPage="login.php", $mainPage="index.php", $pwdCodification="SHA1") {
		$this->loginPage = $loginPage;
		$this->mainPage = $mainPage;
		$this->pwdCodification = $pwdCodification;
		
		if ($_REQUEST['login']=='1') {
			$this->tryLogin();
		}
		if ($_REQUEST['logout']=='1') {
			$this->logout();
		}
		if ($_REQUEST['recoverEmail']=='1'){
			$this->recoverPassword();
		}
		$this->refreshAuthValues();
		$this->loginRedirects();
	}
	private function tryLogin() {
		global $db;
		
		$db->Query("SELECT * FROM cms_usuarios WHERE user = '".MySQL::SQLFix($_REQUEST[user])."' AND pwd = ".$this->pwdCodification."('".MySQL::SQLFix($_REQUEST[pwd])."') AND status = 'ativo'");
		//$db->Query("SELECT * FROM cms_usuarios WHERE user = '".MySQL::SQLFix($_REQUEST[user])."' AND pwd = '".base64_encode($_REQUEST[pwd])."' AND status = 'ativo'");
		if ($db->RowCount()>0) {
			$user = $db->RowArray();
			$this->login($user);
		} else {
			Utils::redirect($this->loginPage."?authError=1");
		}
	}
	public function refreshAuthValues() {
		global $db;
		
		$db->Query("SELECT * FROM cms_usuarios WHERE id_usuario = '".$_SESSION["usuario_dados"]["id_usuario"]."' AND status = 'ativo'");
		if ($db->RowCount()>0) {
			$user = $db->RowArray();
			$acessos = $this->getLastAccess($user);
			$user["acessos"] = $_SESSION["usuario_dados"]["acessos"];
			$user["ultimo_acesso"] = $_SESSION["usuario_dados"]["ultimo_acesso"];
			$_SESSION["usuario_dados"] = $user;
		}
	}
	private function login($user=null) {
		global $db;
		global $config;
		
		if ($user!=null) {
			
			$_SESSION["auth"] = true;
			
			$acessos = $this->getLastAccess($user);
			$user["ultimo_acesso"] = $acessos["ultimo_acesso"];
			
			$db->Query("INSERT INTO cms_usuarios_acessos(id_usuario, time) VALUES ('".$user["id_usuario"]."', NOW())");
			
			$acessos = $this->getLastAccess($user);
			$user["acessos"] = $acessos["acessos"];
			
			$_SESSION["usuario_dados"] = $user;
			$_SESSION["user_profile"] = $config['user_profile'];
			
			return true;
		}
		
		return false;
	}
	private function logout() {
		$_SESSION["auth"] = false;
		$_SESSION["usuario_dados"] = null;
		return true;
	}
	private function logged() {
		if ($_SESSION["auth"]==true) {
			return true;
		}
		return false;
	}
	private function loginRedirects() {
		if (!$this->logged()) {
			Utils::redirect($this->loginPage);
		} else {
			Utils::redirect($this->mainPage);
		}
	}
	private function recoverPassword(){
		global $db;
		$db->Query("SELECT * FROM cms_usuarios WHERE email = '".MySQL::SQLFix($_REQUEST[email])."'  AND status = 'ativo'");
		
		if ($db->RowCount()>0) {
			$user = $db->RowArray();
		}else{
			Utils::redirect($this->loginPage."?recover=1&invalidEmail=1");
		}
				
		$values['de'] = $config["title"]." ".$config["version"];
		$values['para'] =  $user['nome'];
		$values['emailde'] = "mailsender@mosaicoagencia.com";
		$values['emailpara'] = $user['email']; 
		
		$db->Query("SELECT * FROM cms_config LIMIT 1");
		$dbConfig = $db->RowArray();
		
		
		$mensagem = "<html>
					<head>
						<style type=\"text/css\">
							* {
								font-family:Arial, Helvetica, sans-serif;
								font-size:11px;
								color:#272727;
								line-height:18px;
							}
							h1 {
								font-size:16px;
								margin-bottom:6px;
							}
						</style>
					</head>
					<body>
						<h1>Pedido de reenvio de senha</h1>
						<p>Abaixo seguem seus dados para login no Painel Administrativo - Painel Administrativo - eZoom Agência Digital</p>
						<strong>Usuário:</strong> ".$user['user']."<br />
						<strong>Senha:</strong> ".base64_decode($user['pwd'])."<br /><br />
						Acesse: <a href='".$dbConfig["siteUrl "]."/cms'>http://".$dbConfig["siteUrl "]."/cms</a>
						<br /><br />
						Obrigado!<br /><br /><br /><br />
						---------------------------------------------------------------------------------------------------------------<br />
						Esta é uma mensagem gerada automaticamente, por favor não responda!<br />
						---------------------------------------------------------------------------------------------------------------<br />
					</body>
					</html>";
		
		  	$ErrorInfo = Utils::sendMail(array($values['para']=>$values['emailpara']), "Pedido de reenvio de senha (".$dbConfig["siteurl"].")", $mensagem, "", array($values['de']=>$values['emailde']));
		 	if($ErrorInfo!=" ")
		 	{
		  		  Utils::redirect($this->loginPage."?recover=1&errorSend=1");
		    } else {
			      Utils::redirect($this->loginPage."?recoverSucess=1");
		    }
	}
	public function getModules() {
		global $db;
		$db->Query("SELECT cms_modulos.id_modulo AS id_modulo, cms_modulos.label AS label, cms_modulos.nome AS nome, cms_modulos.target AS target, cms_modulos.ico AS ico, cms_modulos.container_of AS container_of, cms_modulos.visivel AS visivel FROM cms_usuarios_modulos 
						INNER JOIN cms_usuarios ON (cms_usuarios_modulos.id_usuario = cms_usuarios.id_usuario)
						INNER JOIN cms_modulos ON (cms_usuarios_modulos.id_modulo = cms_modulos.id_modulo)
					WHERE cms_usuarios.id_usuario = '".$_SESSION["usuario_dados"]["id_usuario"]."'
					AND cms_modulos.ativo = 's' AND cms_modulos.sub = 'n'
					ORDER BY cms_modulos.ordem ASC, cms_modulos.label ASC");

		$i=0;
		while ($modulo = $db->RowArray()) {
			$modulos[$i] = $modulo;
			
			$submenus = explode(',',$modulo['container_of']);
			
			if (count($submenus)>0 && $modulo['container_of']!='') {
				$whereSubmenusArr = array();
				foreach($submenus as $submenu) {
					$whereSubmenusArr[] =  "cms_modulos.id_modulo = '$submenu'";
				}
				$whereSubmenus = implode(' OR ', $whereSubmenusArr);
				$sqlSubmenus = mysql_query("SELECT cms_modulos.id_modulo AS id_modulo, cms_modulos.label AS label, cms_modulos.nome AS nome, cms_modulos.target AS target, cms_modulos.ico, cms_modulos.visivel AS visivel  FROM cms_usuarios_modulos 
						INNER JOIN cms_usuarios ON (cms_usuarios_modulos.id_usuario = cms_usuarios.id_usuario)
						INNER JOIN cms_modulos ON (cms_usuarios_modulos.id_modulo = cms_modulos.id_modulo)
					WHERE cms_usuarios.id_usuario = '".$_SESSION["usuario_dados"]["id_usuario"]."' AND cms_modulos.ativo = 's' AND sub = 's' AND (".$whereSubmenus.")");
				if (mysql_num_rows($sqlSubmenus)) {
					while ($submenu = mysql_fetch_array($sqlSubmenus)) {
						$modulos[$i]['submenus'][] = $submenu;
					}
				}
			}
			$i++;
		}
		return $modulos;
	}
	
	
	private function getLastAccess($user) {
		global $db;
		$db->Query("SELECT DATE_FORMAT(time, '%d/%m/%Y às %H:%i') AS ultimo_acesso FROM cms_usuarios_acessos WHERE id_usuario = '".$user["id_usuario"]."' ORDER BY id_acesso DESC");
		$acesso = $db->RowArray();
		$acesso["acessos"]=$db->RowCount();
		return array("acessos"=>$acesso["acessos"], "ultimo_acesso"=>$acesso["ultimo_acesso"]);
	}
	public function showLoginInfo() {
		global $db;
		$nameUser=current(explode(" ", $_SESSION["usuario_dados"]["nome"]));
		
		if ($this->logged()) {
			$loginInfo = "<p>Olá <span class=\"username\">".$nameUser."</span>, seja bem vindo!&nbsp;&nbsp;<span class=\"logout\">".$this->getLogoutLink()."</span></p>";
			if ($_SESSION["usuario_dados"]["ultimo_acesso"]!="") {
				$loginInfo .= "<p><span class=\"cinza\">Hoje é dia ".date('d/m/Y')." e este é seu <strong>".$_SESSION["usuario_dados"]["acessos"]."º</strong> acesso. <br />Último acesso em ".$_SESSION["usuario_dados"]["ultimo_acesso"]."</span></p>";
			}
			echo $loginInfo;
		}
	}
	public function getLogoutLink($label="sair") {
		return "<a href=\"index.php?logout=1\"><strong>".$label."</strong></a>";
	}
}

?>