<?PHP
function calcula_frete($Dados) {
	$cep_origem = $Dados['cep_origem'];
	$cep_destino = $Dados['cep_destino'];
	$peso = $Dados['peso'];
	$tipofrete = $Dados['cod_frete'];

	$vinfo = Array();
	if ($peso != "0.3") $orPesoMin = " OR peso='0.3'";
	else $orPesoMin="";

	$res = mysql_query("SELECT peso,valor,prazo,regiao FROM frete WHERE cep_origem='".$cep_origem."' AND cep_destino_ini<='".$cep_destino."' AND cep_destino_fim >= '".$cep_destino."' AND (peso='$peso' ".$orPesoMin.") AND servico='".$tipofrete."'");
	while ( ($row=mysql_fetch_object($res)) ) {
		if ($peso == $row->peso) {
			$vinfo['valor'] = " ".str_replace(".",",",$row->valor);
			$vinfo['vtotal'] = $row->valor;
			$vinfo['prazo'] = $row->prazo;
			$vinfo['regiao'] = $row->regiao;
		}
		if ($row->peso == "0.3") {
			$vinfo['valor_minimo'] = $row->valor;
		}
	}

	return $vinfo;
}
?>
