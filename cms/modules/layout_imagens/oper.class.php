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
		
	/*private function insert() {
		global $db, $config;
		
		$values['nome'] = MySQL::SQLValue($_REQUEST[nome]);
		
		$db->InsertRow($this->mainSession, $values);
	}*/
	
	private function edit() {
		global $db, $config;
		
		$imagem1 = Utils::upload($_FILES['imagem1'], $config['userfiles_path'].'/'.$this->mainSession.'/');
		if($_REQUEST['manterArquivo1']=='false') $values['imagem'] = MySQL::SQLValue('');
		if($imagem1) { $values['imagem'] = MySQL::SQLValue($imagem1); }
		if($imagem1 || $_REQUEST['manterArquivo1']=='false')
			unlink( $config['userfiles_path'].'/'.$this->mainSession.'/'.$db->QuerySingleValue("SELECT imagem FROM {$this->mainSession} WHERE {$this->primaryKey} = {$_REQUEST['id']} LIMIT 1") );
		
		$where[$this->primaryKey] = $_REQUEST['id'];
		$db->UpdateRows($this->mainSession, $values, $where);
	}
	
	/*private function delete() {
		global $db, $config;
		
		$where[$this->primaryKey] = $_REQUEST['id'];
		$db->DeleteRows($this->mainSession, $where);
	}
	private function deleteMultiple() {
		global $db, $config;
		
		foreach($_REQUEST['multipleExclusion'] as $value) {
			$where[$this->primaryKey] = $value;
			$db->DeleteRows($this->mainSession, $where);
		}
	}
	*/
}
?>