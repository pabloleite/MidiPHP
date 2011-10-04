<?
	set_include_path('../');
	require_once 'classes/shopping/order_fetch.php';

	require("includes/requires.php");
	require("oper.class.php");
	
	AdminKernel::showModuleTitle("Loja / Clientes");
	
	$mainSession = "loja_usuarios";
	$primaryKey = "id_usuario";
	
	$pageEngine = new Oper($mainSession, $primaryKey);
	
	if ($_REQUEST['action']=='inc' || ($_REQUEST['action']=='alt' && $_REQUEST['id'])) {
		require("form.php");
	} else {
		require("main.php");
	}
?>