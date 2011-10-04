<?
	require("includes/requires.php");
	require("oper.class.php");
	
	AdminKernel::showModuleTitle("Galeria de Fotos");
	
	$mainSession = "fotos";
	$primaryKey = "id";
	
	$pageEngine = new Oper($mainSession, $primaryKey);
	
	if ($_REQUEST['action']=='inc' || ($_REQUEST['action']=='alt' && $_REQUEST['id'])) {
		require("form.php");
	} else {
		require("main.php");
	}
?>