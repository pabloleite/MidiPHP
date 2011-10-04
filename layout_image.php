<?php
require 'lib/kirby/kirby.php';
require 'config/config.php';
require 'defines.php';
require 'funcs.php';

$file = pathinfo($_GET['image'], PATHINFO_BASENAME ); assert(!empty($file));
$res = db::row('layout_imagens', '*', "original = '$file' AND imagem IS NOT NULL");

$loc = $_GET['image'].'?skip_rewrite';
if( db::affected() )
{
	$new_path = 'userfiles/layout_imagens/'.$res['imagem'];
	if( is_file(dirname(__FILE__).DIRECTORY_SEPARATOR.$new_path) )
		$loc = 'http://'.$_SERVER['SERVER_NAME'].dirname($_SERVER['SCRIPT_NAME']).'/'.$new_path;
}

header('Location: '.$loc);
?>