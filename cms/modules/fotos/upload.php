<?
ini_set('display_errors', 'On');
error_reporting(E_ALL);

session_start();
set_include_path('../../');
require_once 'includes/config.php';
require_once 'includes/mysql.class.php';
require_once '/../lib/GdThumb/ThumbLib.inc.php';
require_once '/../lib/GdThumb/thumb_plugins/gd_watermark.inc.php';

$db = new MySQL(true, $config["db_name"], $config["db_host"], $config["db_user"], $config["db_password"], '', true);

$upload_dir = '../../../userfiles/'.$_REQUEST['rel'].'/';
$file_param_name = 'file';

try
{
	$tmpfile	= $_FILES[$file_param_name]['tmp_name'];
	$filename	= $_FILES[$file_param_name]['name'];
	$savename	= rand(0,9999).'_'.time().'.'.end(explode('.', $filename));
	
	// thumb reduced version
	if(0)
	{
		$thumb = PhpThumbFactory::create($tmpfile);
		$thumb->adaptiveResize(234, 156);
		$thumb->save($upload_dir . 'thumb_' . $savename);
	}
	
	// normal image version
	$thumb = PhpThumbFactory::create($tmpfile);
	//$thumb->resize(800, 600);
	//$thumb->createWatermark('watermark.png', 'rb', 8); //apply watermark
	$thumb->save($upload_dir . $savename);
}
catch(Exception $e)
{
	var_dump($e->getMessage());
	die();
}


// Insert into table fotos ------------------------------------------------------------------------------------------

$values['rel'] = MySQL::SQLValue($_REQUEST['rel']);
$values['id_rel'] = MySQL::SQLValue($_REQUEST['id_rel']);
$values['arquivo'] = MySQL::SQLValue($savename);
$values['original'] = MySQL::SQLValue($filename);
$db->InsertRow('fotos', $values);
?>