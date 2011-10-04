<?
class Oper {
	private $mainSession = "";
	private $primaryKey = "";
	private $currentActions = array('alt');
	private $msgInfoId = "";
	
	public function __construct($mainSession="", $primaryKey="") {
		$this->mainSession = $mainSession;
		$this->primaryKey = $primaryKey;
		
		$_REQUEST['action'] = strtolower($_REQUEST['action']);
		
		if (in_array($_REQUEST['action'],$this->currentActions)) {
			if ($_REQUEST['enviado']=='1' && ($_REQUEST['action']=='inc' || $_REQUEST['action']=='alt')) {
				if ($_REQUEST['action']=='alt') {
					$this->edit();
					$this->msgInfoId = "5";
				}
				Utils::Redirect("?".Utils::queryString('id,action,msgInfoId')."&msgInfoId=$this->msgInfoId");
			}
		}
	}
	
	
	private function edit() {
		global $db;
		
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
			$values['pwd'] = MySQL::SQLValue($_REQUEST[pwd2]);
		}
		$values['tipo'] = MySQL::SQLValue("U");
		$values['status'] = MySQL::SQLValue("ativo");
		
		$where[$this->primaryKey] = $_SESSION['usuario_dados']['id_usuario'];
		$db->UpdateRows($this->mainSession, $values, $where);
	}
	
}
?>