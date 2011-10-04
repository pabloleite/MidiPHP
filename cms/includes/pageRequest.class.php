<?
class PageRequest {
	private $requestPageVar = "ir";	
	private $restritAccess = false;
	private $userLogged = false;	
	private $avaliableModules = array();
	
	public function __construct($requestPageVar="ir", $restritAccess=false, $userLogged=false) {
		$this->requestPageVar = $requestPageVar;
		$this->restritAccess = $restritAccess;
		$this->userLogged = $userLogged;
	}
	public function hasAccess() {
		global $_REQUEST;
		
		foreach($this->avaliableModules as $module) {
			if ($module['nome'] == $_REQUEST[$this->requestPageVar]) {
				return true;
			}
			if (is_array($module['submenus'])) {
				foreach($module['submenus'] as $submenu) {
					if ($submenu['nome'] == $_REQUEST[$this->requestPageVar]) {
						return true;
					}
				}
			}
		}
		
		return false;
	}
	public function init($modules=array()) {
		global $_REQUEST;
		$this->avaliableModules = $modules;
		
		$requestFile = "modules/".$_REQUEST[$this->requestPageVar]."/index.php";
		if (is_file($requestFile)) {
			
			if (!$this->restritAccess) {
				require($requestFile);
			} else {
				
				if ($this->userLogged && $this->hasAccess()) {
					require($requestFile);
				} else {
					Utils::redirect("index.php");
				}
			}
		} else {
			@require("home.php");
		}
	}
	
}
?>