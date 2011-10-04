<?
	require("includes/requires.php");
	require("oper.class.php");
	
	AdminKernel::showModuleTitle("Produtos / Marcas");
	
	$mainSession = "marcas";
	$primaryKey = "id_marca";
	
	$pageEngine = new Oper($mainSession, $primaryKey);
	
	if ($_REQUEST['action']=='inc' || ($_REQUEST['action']=='alt' && $_REQUEST['id'])) {
		require("form.php");
	} else {
		require("main.php");
	}
?>