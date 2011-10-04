<?
	set_include_path('../');
	require_once 'classes/shopping/order_fetch.php';
	
	require("includes/requires.php");
	
	AdminKernel::showModuleTitle("Loja / Pedidos");
	
	global $mainSession, $primaryKey, $db;
	$mainSession = "loja_pedidos";
	$primaryKey = "id_pedido";

	require("main.php");
?>