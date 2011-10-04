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
		
		$values['id_subcategoria'] = MySQL::SQLValue($_REQUEST[categoria]);
		$values['id_marca'] = MySQL::SQLValue($_REQUEST[marca]);
		$values['estoque'] = MySQL::SQLValue($_REQUEST[estoque]);
		$values['preco'] = MySQL::SQLValue(str_replace(',', '.', $_REQUEST[preco]));
		$values['preco_promocional'] = MySQL::SQLValue(str_replace(',', '.', $_REQUEST[preco_promocional]));
		$values['peso'] = MySQL::SQLValue($_REQUEST[peso]);
		$values['volume'] = MySQL::SQLValue($_REQUEST[volume]);
		$values['codigo'] = MySQL::SQLValue($_REQUEST[codigo]);
		$values['nome'] = MySQL::SQLValue($_REQUEST[nome]);
		$values['descricao_breve'] = MySQL::SQLValue($_REQUEST[descricao_breve]);
		$values['descricao'] = MySQL::SQLValue($_REQUEST[descricao]);
		$values['rel_produtos'] = MySQL::SQLValue(implode(',', $_REQUEST[rel_produtos]));
		$values['rel_atributo_tamanho'] = MySQL::SQLValue(implode(',', $_REQUEST[tamanho]));
		$values['flag_promocao'] = MySQL::SQLValue($_REQUEST[flag_promocao]);
		$values['flag_destaque'] = MySQL::SQLValue($_REQUEST[flag_destaque]);
		
		$imagem1 = Utils::upload($_FILES['imagem1'], $config['userfiles_path'].'/'.$this->mainSession."/");
		$values['imagem'] = MySQL::SQLValue($imagem1);
		
		$db->InsertRow($this->mainSession, $values);
	}
	
	private function edit() {
		global $db, $config;
		
		$values['id_subcategoria'] = MySQL::SQLValue($_REQUEST[categoria]);
		$values['id_marca'] = MySQL::SQLValue($_REQUEST[marca]);
		$values['estoque'] = MySQL::SQLValue($_REQUEST[estoque]);
		$values['preco'] = MySQL::SQLValue(str_replace(',', '.', $_REQUEST[preco]));
		$values['preco_promocional'] = MySQL::SQLValue(str_replace(',', '.', $_REQUEST[preco_promocional]));
		$values['peso'] = MySQL::SQLValue($_REQUEST[peso]);
		$values['volume'] = MySQL::SQLValue($_REQUEST[volume]);
		$values['codigo'] = MySQL::SQLValue($_REQUEST[codigo]);
		$values['nome'] = MySQL::SQLValue($_REQUEST[nome]);
		$values['descricao_breve'] = MySQL::SQLValue($_REQUEST[descricao_breve]);
		$values['descricao'] = MySQL::SQLValue($_REQUEST[descricao]);
		$values['rel_produtos'] = MySQL::SQLValue(implode(',', $_REQUEST[rel_produtos]));
		$values['rel_atributo_tamanho'] = MySQL::SQLValue(implode(',', $_REQUEST[tamanho]));
		$values['flag_promocao'] = MySQL::SQLValue($_REQUEST[flag_promocao]);
		$values['flag_destaque'] = MySQL::SQLValue($_REQUEST[flag_destaque]);
		
		$imagem1 = Utils::upload($_FILES['imagem1'], $config['userfiles_path'].'/'.$this->mainSession."/");
		
		$imagem1 = Utils::upload($_FILES['imagem1'], $config['userfiles_path'].'/'.$this->mainSession.'/');
		if($_REQUEST['manterArquivo1']=='false') $values['imagem'] = MySQL::SQLValue('');
		if($imagem1) { $values['imagem'] = MySQL::SQLValue($imagem1); }
		if($imagem1 || $_REQUEST['manterArquivo1']=='false')
			unlink( $config['userfiles_path'].'/'.$this->mainSession.'/'.$db->QuerySingleValue("SELECT imagem FROM {$this->mainSession} WHERE {$this->primaryKey} = {$_REQUEST['id']} LIMIT 1") );
		
		$where[$this->primaryKey] = $_REQUEST['id'];
		$db->UpdateRows($this->mainSession, $values, $where);
	}
	
	private function delgallery($id)
	{
		global $db, $config;
		
		$db->Query("SELECT * FROM fotos WHERE rel = '{$this->mainSession}' AND id_rel = '$id'");
		foreach( $db->RecordsArray() as $rowf )
			unlink( $config['userfiles_path'].'/'.$this->mainSession.'/'.$rowf['arquivo'] );
		$where['rel'] = MySQL::SQLValue($this->mainSession);
		$where['id_rel'] = MySQL::SQLValue($id);
		$db->DeleteRows('fotos', $where);
	}
	private function delete() {
		global $db, $config;
		
		unlink( $config['userfiles_path'].'/'.$this->mainSession.'/'.$db->QuerySingleValue("SELECT imagem FROM {$this->mainSession} WHERE {$this->primaryKey} = {$_REQUEST['id']} LIMIT 1") );
		$this->delgallery($_REQUEST['id']);
		$where[$this->primaryKey] = $_REQUEST['id'];
		$db->DeleteRows($this->mainSession, $where);
	}
	private function deleteMultiple() {
		global $db, $config;
		
		foreach($_REQUEST['multipleExclusion'] as $value) {
			unlink( $config['userfiles_path'].'/'.$this->mainSession.'/'.$db->QuerySingleValue("SELECT imagem FROM {$this->mainSession} WHERE {$this->primaryKey} = $value LIMIT 1") );
			$this->delgallery($value);
			$where[$this->primaryKey] = $value;
			$db->DeleteRows($this->mainSession, $where);
		}
	}
	
}
?>