<?
class Oper {
	private $mainSession = "";
	private $primaryKey = "";
	private $currentActions = array('inc', 'alt', 'exc', 'excmultiple');
	private $msgInfoId = "";
	
	public function __construct($mainSession="", $primaryKey="") {
		global $_REQUEST;
		
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
		global $db;
		global $_REQUEST;
		global $_FILES;
		global $_SESSION;
		
		$imagem = Utils::upload($_FILES['imagem'], "../userfiles/".$this->mainSession."/");
		
		$values['nome'] = MySQL::SQLValue($_REQUEST[nome]);
		$values['imagem'] = MySQL::SQLValue($imagem);
		$values['endereco'] = MySQL::SQLValue($_REQUEST[endereco]);
		$values['numero'] = MySQL::SQLValue($_REQUEST[numero]);
		$values['bairro'] = MySQL::SQLValue($_REQUEST[bairro]);
		$values['cidade'] = MySQL::SQLValue($_REQUEST[cidade]);
		$values['estado'] = MySQL::SQLValue($_REQUEST[estado]);
		$values['telefone'] = MySQL::SQLValue($_REQUEST[telefone]);
		$values['celular'] = MySQL::SQLValue($_REQUEST[celular]);
		$values['email'] = MySQL::SQLValue($_REQUEST[email]);
		
		$tmpData = explode('-',$_REQUEST[data_nascimento]);
		$values['data_nascimento'] = MySQL::SQLValue($tmpData[2]."-".$tmpData[1]."-".$tmpData[0]);
		
		$values['user'] = MySQL::SQLValue($_REQUEST[user]);
		$values['pwd'] = 'SHA1('.MySQL::SQLValue($_REQUEST[pwd2]).')';
		
		$values['inclusao'] = "NOW()";
		$values['tipo'] = MySQL::SQLValue("U");
		$values['status'] = MySQL::SQLValue("ativo");
		
		$db->InsertRow($this->mainSession, $values);
		
		$id_usuario = $db->GetLastInsertID();
		
		foreach($_REQUEST['id_modulo'] as $modulo) {
			$values = array();
			$values['id_usuario'] = $id_usuario;
			$values['id_modulo'] = $modulo;
			$db->InsertRow( 'cms_usuarios_modulos', $values);
			
			$db->Query("SELECT * FROM cms_modulos WHERE id_modulo = '$modulo'");
			$rowModulo = $db->RowArray();
			
			if ($rowModulo['container_of']!='') {
				$submodules = explode(',',$rowModulo['container_of']);
				if (count($submodules)>0) {
					foreach($submodules as $submodule) {
						$values = array();
						$values['id_usuario'] = $id_usuario;
						$values['id_modulo'] = $submodule;
						$db->InsertRow( 'cms_usuarios_modulos', $values);
					}
				}
			}
		}
	}
	private function edit() {
		global $db;
		global $_REQUEST;
		global $_FILES;
		
		$imagem = Utils::upload($_FILES['imagem'], "../userfiles/".$this->mainSession."/");
		
		$values['nome'] = MySQL::SQLValue($_REQUEST[nome]);
		$values['endereco'] = MySQL::SQLValue($_REQUEST[endereco]);
		$values['numero'] = MySQL::SQLValue($_REQUEST[numero]);
		$values['bairro'] = MySQL::SQLValue($_REQUEST[bairro]);
		$values['cidade'] = MySQL::SQLValue($_REQUEST[cidade]);
		$values['estado'] = MySQL::SQLValue($_REQUEST[estado]);
		$values['telefone'] = MySQL::SQLValue($_REQUEST[telefone]);
		$values['celular'] = MySQL::SQLValue($_REQUEST[celular]);
		$values['email'] = MySQL::SQLValue($_REQUEST[email]);
		
		if ($_REQUEST['manterArquivo']=='false') {
			$values['imagem'] = MySQL::SQLValue('');
		}
		if ($imagem) {
			$values['imagem'] = MySQL::SQLValue($imagem);
		}
		
		
		$tmpData = explode('-',$_REQUEST[data_nascimento]);
		$values['data_nascimento'] = MySQL::SQLValue($tmpData[2]."-".$tmpData[1]."-".$tmpData[0]);
		
		$values['user'] = MySQL::SQLValue($_REQUEST[user]);
		if ($_REQUEST[alterar_senha]=='s') {
			$values['pwd'] = 'SHA1('.MySQL::SQLValue($_REQUEST[pwd2]).')';
		}

		$values['status'] = MySQL::SQLValue("ativo");
		
		$where[$this->primaryKey] = $_REQUEST['id'];
		$db->UpdateRows($this->mainSession, $values, $where);
		
		
		$id_usuario = $_REQUEST['id'];
		$where[$this->primaryKey] = $id_usuario;
		$db->DeleteRows( 'cms_usuarios_modulos', $where);
		
		foreach($_REQUEST['id_modulo'] as $modulo) {
			$values = array();
			$values['id_usuario'] = $id_usuario;
			$values['id_modulo'] = $modulo;
			$db->InsertRow( 'cms_usuarios_modulos', $values);
			
			$db->Query("SELECT * FROM cms_modulos WHERE id_modulo = '$modulo'");
			$rowModulo = $db->RowArray();
			
			if ($rowModulo['container_of']!='') {
				$submodules = explode(',',$rowModulo['container_of']);
				if (count($submodules)>0) {
					foreach($submodules as $submodule) {
						$values = array();
						$values['id_usuario'] = $id_usuario;
						$values['id_modulo'] = $submodule;
						$db->InsertRow( 'cms_usuarios_modulos', $values);
					}
				}
			}
		}
		
	}
	
	private function delete() {
		global $db;
		global $_REQUEST;
		
		$where[$this->primaryKey] = $_REQUEST['id'];
		$db->DeleteRows($this->mainSession, $where);
		$db->DeleteRows( 'cms_usuarios_modulos', $where);
	}
	private function deleteMultiple() {
		global $db;
		global $_REQUEST;
		
		if (is_array($_REQUEST['multipleExclusion'])) {
			foreach($_REQUEST['multipleExclusion'] as $key=>$value) {
				$where[$this->primaryKey] = $value;
				$db->DeleteRows($this->mainSession, $where);
				$db->DeleteRows( 'cms_usuarios_modulos', $where);
			}
		}
	}
	
}
?>