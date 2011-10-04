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
		
		$values['pay_pad_email'] = MySQL::SQLValue($_REQUEST[pad_email]);
		$values['pay_deposito_info'] = MySQL::SQLValue($_REQUEST[deposito_info]);
		$values['frete_gratis_flag_cidade'] = MySQL::SQLValue((bool) $_REQUEST[gratis_flag_cidade]);
		$values['frete_gratis_flag_min'] = MySQL::SQLValue((bool) $_REQUEST[gratis_flag_min]);
		
		if($values['frete_gratis_flag_cidade'])
			$values['frete_gratis_val_cidade'] = MySQL::SQLValue($_REQUEST[gratis_val_cidade]);
		if($values['frete_gratis_flag_min'])
			$values['frete_gratis_val_min'] = MySQL::SQLValue(str_replace(',', '.', $_REQUEST[gratis_val_min]));
		$db->UpdateRows($this->mainSession, $values);
		
		$db->SelectTable('produtos_atributos');
		foreach( $db->RecordsArray() as $rowa )
		{
			$sel = (int) isset($_REQUEST[ 'atributo_'.$rowa['identificacao'] ]);
			$db->UpdateRows( 'produtos_atributos', array('ativo' => $sel), array('id_atributo' => $rowa['id_atributo']) );
		}
	}
	
}
?>