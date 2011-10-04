<?
	require("includes/requires.php");
	require("oper.class.php");
	
	AdminKernel::showModuleTitle("Configurações");
	
	$mainSession =  "cms_usuarios";
	$primaryKey = "id_usuario";

	$pageEngine = new Oper($mainSession, $primaryKey);
	
	require("form.php");
?>