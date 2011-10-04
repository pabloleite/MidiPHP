<?php
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
		global $db;
		
		// NYI
		$db->InsertRow($this->mainSession, $values);
	}
	private function edit() {
		global $db;
		
		// Cliente
		if( $_REQUEST['tipo']==ValueConsts::PESSOA_FISICA ) {
			$values['cpf'] = MySQL::SQLValue($_REQUEST[cpf]);
		} else if( $_REQUEST['tipo']==ValueConsts::PESSOA_JURIDICA ) {
			$values['cnpj'] = MySQL::SQLValue($_REQUEST[cnpj]);
			$values['inscricao_estadual'] = MySQL::SQLValue($_REQUEST[ie]);
		}
		
		$values['ident_nome'] = MySQL::SQLValue($_REQUEST[nome]);
		$values['ident_apelido'] = MySQL::SQLValue($_REQUEST[apelido]);
		$tmpData = explode('/',$_REQUEST[nascimento]);
		$values['ident_nascimento'] = MySQL::SQLValue($tmpData[2]."-".$tmpData[1]."-".$tmpData[0]);
		$values['ident_sexo'] = MySQL::SQLValue($_REQUEST[sexo]==0 ? 'm' : 'f');
		$values['cont_telresidencial'] = MySQL::SQLValue($_REQUEST[tel_residencial]);
		$values['cont_telcelular'] = MySQL::SQLValue($_REQUEST[tel_celular]);
		$values['cont_telcomercial'] = MySQL::SQLValue($_REQUEST[tel_comercial]);
		$values['opt_news'] = MySQL::SQLValue($_REQUEST[newsletter]);
		$client_id = $db->InsertRow(ShopDBConsts::TABLE_CLIENT, $values);
		
		// Endereço
		$where[$this->primaryKey] = $_REQUEST['id'];
		$where['principal'] = 1;
		$db->UpdateRows(ShopDBConsts::TABLE_ADDR, array('principal' => 0), $where);
		
		$values = $where = array();
		$values[$this->primaryKey] = MySQL::SQLValue($_REQUEST[id]);
		$values['principal'] = MySQL::SQLValue(1);
		$values['ender_cep'] = MySQL::SQLValue($_REQUEST[cep]);
		$values['ender_estado'] = MySQL::SQLValue($_REQUEST[estado]);
		$values['ender_cidade'] = MySQL::SQLValue($_REQUEST[cidade]);
		$values['ender_bairro'] = MySQL::SQLValue($_REQUEST[bairro]);
		$values['ender_endereco'] = MySQL::SQLValue($_REQUEST[endereco]);
		$values['ender_numero'] = MySQL::SQLValue($_REQUEST[numero]);
		$values['ender_complemento'] = MySQL::SQLValue($_REQUEST[complemento]);
		$values['ender_referencia'] = MySQL::SQLValue($_REQUEST[referencia]);
		$db->InsertRow(ShopDBConsts::TABLE_ADDR, $values);
		
		// Usuário
		$values = $where = array();
		$values['cliente_tipo'] = MySQL::SQLValue($_REQUEST['tipo']);
		$values['id_cliente'] = MySQL::SQLValue($client_id);
		$where[$this->primaryKey] = $_REQUEST['id'];
		$db->UpdateRows($this->mainSession, $values, $where);
	}
	private function delete() {
		global $db;
		
		$where[$this->primaryKey] = $_REQUEST['id'];
		$db->DeleteRows($this->mainSession, $where);
	}
	private function deleteMultiple() {
		global $db;
		
		foreach($_REQUEST['multipleExclusion'] as $key=>$value) {
			$where[$this->primaryKey] = $value;
			$db->DeleteRows($this->mainSession, $where);
		}
	}
	
}
?>