<?php
session_start();
ini_set('xdebug.var_display_max_depth', 10);

require_once('classes/utils/properties.php');

function Foo()
{
	static $fuu = array();
	
	var_dump($fuu);
}

Foo();
Foo();


die();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>midi - Testing Sandbox</title>
	
	<script type="text/javascript" src="public/js/jquery-1.5.min.js"></script>
	<script type="text/javascript" src="public/js/jquery.inputfocus.midi.js"></script>
	<script type="text/javascript" src="public/js/jQuery.Console.js"></script>
	
	<script type="text/javascript">
		//window.C_JS_DEBUG
		
		$(function() {
			$('#iname').inputfocus({ idle_value: 'USUÁRIO', idle_color: '#333' });
			$('#ipwd').inputfocus({ idle_value: 'SENHA' });
		});
	</script>
</head>


<body>

<div id="foo">
	<style type="text/css">
		input.idle { border: solid 3px; }
		input.focus { font-weight: bold; }
		
		#outer { position: relative; width: 250px; height: 180px; background: red; }
		#outer #inner { position: absolute; top: 0; bottom: 0; left: 0; right: 0; background: blue; margin: 10px; }
		#outer #inner #fit { width: 100%; height: 100%; background: green; margin: 15px; }
	</style>
	
	<input type="text" id="iname" name="name" /><br />
	<input type="text" id="ipwd" name="pwd" /><br />
	
	<br /><img src="thumb/?src=temp/tanque.jpg&w=200" />
	
	<div id="outer">
		<div id="inner">
			<div id="fit"></div>
		</div>
	</div>
</div>

<br />
{{ TESTING DOCUMENT END }}

</body>

</html>