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
		
	private function insert() {
		global $db, $config;
		
		$values['legenda'] = MySQL::SQLValue($_REQUEST[legenda]);
		$values['youtube_url'] = MySQL::SQLValue($_REQUEST[link]);
		$values['rel'] = MySQL::SQLValue($_REQUEST[rel]);
		$values['id_rel'] = MySQL::SQLValue($_REQUEST[id_rel]);
		
		$db->InsertRow($this->mainSession, $values);
	}
	
	private function edit() {
		global $db, $config;
		
		$values['legenda'] = MySQL::SQLValue($_REQUEST[legenda]);
		$values['youtube_url'] = MySQL::SQLValue($_REQUEST[link]);
		
		$where[$this->primaryKey] = $_REQUEST['id'];
		$db->UpdateRows($this->mainSession, $values, $where);
	}
	private function delete() {
		global $db, $config;
		
		$where[$this->primaryKey] = $_REQUEST['id'];
		$db->DeleteRows($this->mainSession, $where);
	}
	private function deleteMultiple() {
		global $db, $config;
		
		if (is_array($_REQUEST['multipleExclusion'])) {
			foreach($_REQUEST['multipleExclusion'] as $key=>$value) {
				$where[$this->primaryKey] = $value;
				$db->DeleteRows($this->mainSession, $where);
			}
		}
	}
	
}
?>