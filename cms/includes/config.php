<?php
ini_set('display_errors', 'On');
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING & ~E_DEPRECATED);

$localsite	= $_SERVER['HTTP_HOST']=='server' || $_SERVER['HTTP_HOST']=='localhost' || $_SERVER['HTTP_HOST']=='gamba-cromo';
$buildmode	= $localsite || (defined('CFG_FORCE_DEBUG') && CFG_FORCE_DEBUG) ? 'DEBUG' : 'RELEASE';
$sitemode 	= $localsite ? 'desenvolvimento' : 'online';

define('G_LOCALSITE', $localsite);
define('G_SITEMODE', $localsite ? 'desenvolvimento' : 'online');
define('G_BUILDMODE', $buildmode);
define('G_BUILDDEBUG', $buildmode=='DEBUG');
		
switch($sitemode)
{
case 'desenvolvimento':
	$config=array();
	$config["title"]="Painel Administrativo - Rumocerto";
	$config["version"]="1.0";
	$config["charset"]="utf-8";
	
	$config["db_host"]="localhost";
	$config["db_user"]="root";
	$config["db_password"]="";
	$config["db_name"]="rumocerto";
	
	$config["mysql_backup_user"]="admin";
	$config["mysql_backup_password"]="admin";
	
	$config["user_profile"]["resultsPerPage"] = 10;
	
	$config["userfiles_path"] = "../userfiles";
	$config["siteurl"] = "http://www.rumocerto.com.br/";
	break;
	
case 'online':
	$config=array();
	$config["title"]="Painel Administrativo - Rumocerto";
	$config["version"]="1.0";
	$config["charset"]="utf-8";
	
	$config["db_host"]="localhost";
	$config["db_user"]="ezoomcp_lojamode";
	$config["db_password"]="y9e4x66q.ALI";
	$config["db_name"]="ezoomcp_loja_modelo";
	
	$config["mysql_backup_user"]="admin";
	$config["mysql_backup_password"]="admin";
	
	$config["user_profile"]["resultsPerPage"] = 10;
	
	$config["userfiles_path"] = "../userfiles";
	$config["siteurl"] = "http://www.rumocerto.com.br/";
	break;
}
?>
