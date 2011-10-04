<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Ampliar imagem 100%</title>
<style type="text/css">
	body {
		margin:0px;
		padding:0px;
	}
	img {
		border:5px solid #999999;
	}
</style>
</head>

<body>
<?
	$_REQUEST['imgSrc'] = '../'.$_REQUEST['imgSrc'];
	if (is_file($_REQUEST['imgSrc'])) {
		echo "<a href=\"javascript:void(0);\" onClick=\"window.close();\"><img src=\"".$_REQUEST['imgSrc']."\"></a>";
	} else {
		echo "<script type=\"text/javascript\">window.close();</script>";
	}
?>
</body>
</html>
