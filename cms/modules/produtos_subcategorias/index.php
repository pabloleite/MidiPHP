<?
	require("includes/requires.php");
	require("oper.class.php");
	
	AdminKernel::showModuleTitle("Produtos / Sub-categorias");
	
	$mainSession = "produtos_subcategorias";
	$primaryKey = "id_subcategoria";
	$pageEngine = new Oper($mainSession, $primaryKey);
	
	if ($_REQUEST['action']=='inc' || ($_REQUEST['action']=='alt' && $_REQUEST['id'])) {
		require("form.php");
	} else {
		require("main.php");
	}
?>