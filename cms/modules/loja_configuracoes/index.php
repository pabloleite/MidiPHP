<?
	require("includes/requires.php");
	require("oper.class.php");
	
	AdminKernel::showModuleTitle("Loja / Configurações");
	
	$mainSession = "loja_configuracoes";
	$primaryKey = "";
	
	$pageEngine = new Oper($mainSession, $primaryKey);
	
	require("form.php");
?>