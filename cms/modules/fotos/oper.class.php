<?
class Oper {
	private $mainSession = "";
	private $primaryKey = "";
	private $currentActions = array('alt', 'exc', 'excmultiple');
	private $msgInfoId = "";
	
	public function __construct($mainSession="", $primaryKey="") {
		global $_REQUEST;
		
		$this->mainSession = $mainSession;
		$this->primaryKey = $primaryKey;
		
		$_REQUEST['action'] = strtolower($_REQUEST['action']);
		
		if (in_array($_REQUEST['action'],$this->currentActions)) {
			if ($_REQUEST['enviado']=='1' && $_REQUEST['action']=='alt') {
				if ($_REQUEST['action']=='alt') {
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
		
		$values['legenda'] = MySQL::SQLValue($_REQUEST['legenda']);
		$imagem1 = Utils::upload($_FILES['imagem1'], $config['userfiles_path'].'/'.$_REQUEST['rel'].'/');
		if($_REQUEST['manterArquivo1']=='false') $values['arquivo'] = $values['original'] = MySQL::SQLValue('');
		if($imagem1) { $values['arquivo'] = MySQL::SQLValue($imagem1); $values['original'] = MySQL::SQLValue($_FILES['imagem1']['name']); }
		if($imagem1 || $_REQUEST['manterArquivo1']=='false')
			unlink( $config['userfiles_path'].'/'.$_REQUEST['rel'].'/'.$db->QuerySingleValue("SELECT arquivo FROM {$this->mainSession} WHERE id = {$_REQUEST['id']} LIMIT 1") );
		
		$where[$this->primaryKey] = $_REQUEST['id'];
		$db->UpdateRows($this->mainSession, $values, $where);
	}
	private function delete() {
		global $db, $config;
		
		unlink( $config['userfiles_path'].'/'.$_REQUEST['rel'].'/'.$db->QuerySingleValue("SELECT arquivo FROM {$this->mainSession} WHERE id = {$_REQUEST['id']} LIMIT 1") );
		$where[$this->primaryKey] = $_REQUEST['id'];
		$db->DeleteRows($this->mainSession, $where);
	}
	private function deleteMultiple() {
		global $db, $config;
		
		foreach($_REQUEST['multipleExclusion'] as $key=>$value) {
			unlink( $config['userfiles_path'].'/'.$_REQUEST['rel'].'/'.$db->QuerySingleValue("SELECT arquivo FROM {$this->mainSession} WHERE id = $value LIMIT 1") );
			$where[$this->primaryKey] = $value;
			$db->DeleteRows($this->mainSession, $where);
		}
	}
	
}
?>