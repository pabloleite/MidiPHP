<?
class Oper {
	private $mainSession = "";
	private $primaryKey = "";
	private $currentActions = array('inc', 'alt', 'exc', 'excmultiple');
	private $msgInfoId = "";
	
	public function __construct($mainSession="", $primaryKey="") {
		$this->mainSession = $mainSession;
		$this->primaryKey = $primaryKey;
		
		$_REQUEST['action'] = strtolower($_REQUEST['action']);
		
		if (in_array($_REQUEST['action'],$this->currentActions)) {
			if ($_REQUEST['enviado']=='1' && ($_REQUEST['action']=='inc' || $_REQUEST['action']=='alt')) {
				if ($_REQUEST['action']=='inc') {
					$this->insert();
					$this->msgInfoId = "1";
				} else if ($_REQUEST['action']=='alt') {
					$this->edit();
					$this->msgInfoId = "2";
				}
				Utils::Redirect("?".Utils::queryString('id,action,msgInfoId')."&msgInfoId=$this->msgInfoId");
			} elseif ($_REQUEST['action']=='exc' || $_REQUEST['action']=='excmultiple') {
				if ($_REQUEST['action']=='exc') {
					$this->delete();
					$this->msgInfoId = "3";
				} else if ($_REQUEST['action']=='excmultiple') {
					$this->deleteMultiple();
					$this->msgInfoId = "4";
				}
				Utils::Redirect("?".Utils::queryString('id,action,msgInfoId')."&msgInfoId=$this->msgInfoId");
			}
		}
	}
	
	private function edit() {
		global $db, $config;
		
		$values['titulo'] = MySQL::SQLValue($_REQUEST[titulo]);
		$values['texto'] = MySQL::SQLValue($_REQUEST[texto]);
		
		$imagem1 = Utils::upload($_FILES['imagem'], $config['userfiles_path'].'/'.$this->mainSession.'/');
		if($_REQUEST['manterArquivo']=='false') $values['imagem_conteudo'] = MySQL::SQLValue('');
		if($imagem1) { $values['imagem_conteudo'] = MySQL::SQLValue($imagem1); }
		if($imagem1 || $_REQUEST['manterArquivo']=='false')
			unlink( $config['userfiles_path'].'/'.$this->mainSession.'/'.$db->QuerySingleValue("SELECT imagem_conteudo FROM {$this->mainSession} WHERE id = {$_REQUEST['id']} LIMIT 1") );
		
		$where[$this->primaryKey] = $_REQUEST['id'];
		$db->UpdateRows($this->mainSession, $values, $where);
	}
	
}
?>