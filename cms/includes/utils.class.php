<?
class Utils {
	function checkCurrentPage($url) {
		global $_SERVER;
		return !strstr(strtolower($_SERVER['PHP_SELF']), $url);
	}
	function redirect($url){
		if (Utils::checkCurrentPage($url)) {
			if (!headers_sent()){ 
				header('Location: '.$url); exit;
			}else{                 
				echo '<script type="text/javascript">';
				echo 'window.location.href="'.$url.'";';
				echo '</script>';
				echo '<noscript>';
				echo '<meta http-equiv="refresh" content="0;url='.$url.'" />';
				echo '</noscript>'; exit;
			}
		}
	}
	function FULL_URL(){
		$tmp='http';
		if($_SERVER['HTTPS']=='on'){$tmp.='s';}
		$tmp.='://';
		if($_SERVER['SERVER_PORT']!='80')$tmp.=$_SERVER['HTTP_HOST'].':'.$_SERVER['SERVER_PORT'].$_SERVER['SCRIPT_NAME'];
		else $tmp.=$_SERVER['HTTP_HOST'].$_SERVER['SCRIPT_NAME'];
		if($_SERVER['QUERY_STRING']>' '){$tmp.='?'.$_SERVER['QUERY_STRING'];}
		$_SERVER['FULL_URL']=$tmp;
		return $_SERVER['FULL_URL'];
	}
	
	function generatePassword($length=9, $strength=0){
		$vowels='aeiouy';
		$consonants='bdghjmnpqrstvz';
		if($strength & 1){
			$consonants.='BDGHJLMNPQRSTVWXZ';
		}
		if($strength & 2){
			$vowels.="AEIOUY";
		}
		if($strength & 4){
			$consonants.='23456789';
		}
		if($strength & 8){
			$consonants.='@#$%';
		}
		
		$password='';
		$alt=time()%2;
		for($i=0;$i<$length;$i++){
			if($alt==1){
				$password.=$consonants[(rand()%strlen($consonants))];
				$alt=0;
			}else{
				$password.=$vowels[(rand()%strlen($vowels))];
				$alt=1;
			}
		}
		return $password;
	}
	function runJS($str){
		echo '<script language="javascript" type="text/javascript">'.$str.'</script>';
	}
	function trimTo($str,$maxChars){
		$str=trim(unhtmlentities(strip_tags($str)));
		if(strlen($str)>$maxChars){
			return substr($str,0,$maxChars).'...';
		}
		return $str;
	}
	function untag($string,$tag){
		preg_match_all("|<$tag>(.*?)</$tag>|s",$string,$tags);
		foreach($tags[1] as $tmpcont){$tmpval[]=$tmpcont;}
		return @implode('',$tmpval);
	}
	function unhtmlentities($string){
		$trans_tbl=get_html_translation_table(HTML_ENTITIES);
		$trans_tbl=array_flip($trans_tbl);
		return strtr($string,$trans_tbl);
	}
	function upload($file,$path){
		static $held = 0;
		
		if(!is_dir($path)){return false;}
		
		$filename=explode('.',$file['name']);
		$ext=end($filename);
		array_pop($filename);
		$filename=rand(0,9999)+$held.'_'.time().'.'.$ext;
		$uploadfile=$path.'/'.$filename;
		
		if(!move_uploaded_file($file['tmp_name'],$uploadfile))
			$result=false;
		else
			$result=$filename;
		
		$held++;
		return $result;
	}
	function sendMail($to,$subject,$corpo,$attachs){
		if($to==''){
			$sql="SELECT * FROM cms_config LIMIT 1";
			$resultado=@mysql_query($sql);
			$config=@mysql_fetch_array($resultado);
			$to=array($config['contato_nome']=>$f_config['contato_email']);
		}
		if(!is_array($to)){$to=array($to=>$to);}
		$to = array('RPP' => 'rpp@rppconstrutora.com.br');
		//$to = array('Ramon' => 'midiway@terra.com.br');
		
		flush();
		ob_start();
			$body="includes/emails/$corpo/".$corpo.".php";
			if(is_file($body)){require $body;}
			else{echo $corpo;}
		$Message=ob_get_clean();
		flush();
		
		
		require("emails/class.phpmailer.php");
		
		$mail = new PHPMailer();
		$mail->SMTPDebug = 0;
		$mail->IsSMTP();
		$mail->SMTPAuth = true;
		
		$mail->Host     = "smtp.csl.terra.com.br";
		$mail->Username = "mailsender@rppimoveis.com.br";
		$mail->Password = "yhgm+wni";
		$mail->From     = "mailsender@terra.com.br";
		$mail->FromName = "RPP";
		
		
		foreach($to as $name=>$email){
			$mail->AddAddress($email,$name);  //email,nome do destinatario
		}
		//$mail->AddReplyTo($fromMail,$fromName);
		
		$mail->WordWrap = 50;                              // set word wrap 
		$mail->IsHTML(true);                               // send as HTML
		$mail->Subject  =  $subject;
		$mail->Body     =  $Message;
		$mail->AltBody  =  strip_tags($Message);
		
		if(!is_array($attachs)){$attachs=array($attachs);}
		foreach($attachs as $key=>$file){  //atacha o(s) arquivo(s) informado(s), ou todos aqueles que estão dentro da(s) pasta(s) especificada(s)
			if(is_dir($file)) {
				$dir=$file;
				if($handle=opendir($dir)){
					while(($file=readdir($handle))!==false){
						if($file=='.' || $file=='..'){continue;}
						$mail->AddAttachment($file,$file);  //coloca um arquivo anexado normalmente
					}
				}
			}else{
				if(!is_file($file)){continue;}
				
				$file_exp = end(explode('/',$file));
				if( preg_match('/^image/i', $this->mime_content_type($file)) )
					$mail->AddEmbeddedImage($file, reset(explode('.', $file_exp)), $file );
				else
					$mail->AddAttachment($file, $file_exp);  //coloca um arquivo anexado normalmente
			}
		}
		
		$dir="includes/emails/$corpo/cids/";  //insere todos os arquivos que estão na pasta cids como arquivos anexos
		if(is_dir($dir)){
			if($handle=opendir($dir)){
				while(($file=readdir($handle))!==false){
					if($file=='.' || $file=='..'){continue;}
					$mail->AddEmbeddedImage("$dir$file",reset(explode('.',$file)),$file);  //define um cid
				}
			}
		}
		if(!$mail->Send()) {return false;}
		return true;
	}
	
	function queryString($remove=''){
		$remove=array_flip(explode(',',$remove));
		parse_str($_SERVER['QUERY_STRING'],$queryStr);
		$queryStr=array_diff_key($queryStr,$remove);
		foreach($queryStr as $key=>$value){
			if(is_array($value)){
				unset($temp);
				foreach($value as $value2){$temp[]=$key."[]=".$value2;}
				$result[]=implode('&',$temp);
			}else{$result[]="$key=".$value;}
		}
		return implode('&',$result);
	}
	function queryStringHiddenInputs($remove='') {
		$remove=array_flip(explode(',',$remove));
		parse_str($_SERVER['QUERY_STRING'],$queryStr);
		$queryStr=array_diff_key($queryStr,$remove);
		foreach($queryStr as $key=>$value){
			if(is_array($value)){
				unset($temp);
				foreach($value as $value2){
					$temp[]="<input type=\"hidden\" name=\"$key\" value=\"".$value2."\">\n";
				}
				$result[]=implode('',$temp);
			}else{$result[]="<input type=\"hidden\" name=\"$key\" value=\"".$value."\">\n";}
		}
		return implode("",$result);
	}
	function tamanhoImg($src,$w,$h){
		// Pega o tamanho da imagem e proporção de resize
		if(!is_file($src)){return array($w,$h);}
		list($width, $height, $type, $attr) = getimagesize($src);
		$scale = min($w/$width, $h/$height);
		// Se a imagem é maior que o permitido, encolhe ela!
		$new_width = min($width,floor($scale * $width));
		$new_height = min($height,floor($scale * $height));
		return array($new_width,$new_height);
	}
	function replaceBreakLines($str){
		$str=str_replace(array(chr(10),chr(13),chr(10).chr(13),chr(13).chr(10)),'',$str);
		return $str;
	}
	function htmlentitiesRecursive($arr){
		if(is_string($arr)){$arr=htmlentities($arr);}
		elseif(is_array($arr)){
			foreach($arr as $key=>$val){$arr[$key]=htmlentitiesRecursive($val);}
		}
		return $arr;
	}
	function numberConvert($val){
		$val=str_replace(',','.',$val);
		$teste=explode('.',$val);
		$t=array_pop($teste);
		if(count($teste)>0){
			$val=implode('',$teste).'.'.$t;
		}
		return floatval($val);
	}
	function printMoney($value){
		$value=sprintf('%01.2f',$value);
		$value=str_replace(',','.',$value);
		return number_format($value,2,',','.');
	}
	function removeLineBreaks($str){  //remove line breaks to use with javascript
		return str_replace(chr(13).chr(10),'',str_replace(chr(10).chr(13),'',str_replace(chr(13),'',str_replace(chr(10),'',$str))));
	}
	function encodeText($str){
		if(strpos($_SERVER['HTTP_USER_AGENT'],"MSIE")==0){
			$newstr=urlencode(utf8_encode($str));
		}else{
			$newstr=$str;
		}
		return($newstr);
	}

	function mime_content_type($filename) {
		
		$mime_types = array(

			'txt' => 'text/plain',
			'htm' => 'text/html',
			'html' => 'text/html',
			'php' => 'text/html',
			'css' => 'text/css',
			'js' => 'application/javascript',
			'json' => 'application/json',
			'xml' => 'application/xml',
			'swf' => 'application/x-shockwave-flash',
			'flv' => 'video/x-flv',

			// images
			'png' => 'image/png',
			'jpe' => 'image/jpeg',
			'jpeg' => 'image/jpeg',
			'jpg' => 'image/jpeg',
			'gif' => 'image/gif',
			'bmp' => 'image/bmp',
			'ico' => 'image/vnd.microsoft.icon',
			'tiff' => 'image/tiff',
			'tif' => 'image/tiff',
			'svg' => 'image/svg+xml',
			'svgz' => 'image/svg+xml',

			// archives
			'zip' => 'application/zip',
			'rar' => 'application/x-rar-compressed',
			'exe' => 'application/x-msdownload',
			'msi' => 'application/x-msdownload',
			'cab' => 'application/vnd.ms-cab-compressed',

			// audio/video
			'mp3' => 'audio/mpeg',
			'qt' => 'video/quicktime',
			'mov' => 'video/quicktime',

			// adobe
			'pdf' => 'application/pdf',
			'psd' => 'image/vnd.adobe.photoshop',
			'ai' => 'application/postscript',
			'eps' => 'application/postscript',
			'ps' => 'application/postscript',

			// ms office
			'doc' => 'application/msword',
			'rtf' => 'application/rtf',
			'xls' => 'application/vnd.ms-excel',
			'ppt' => 'application/vnd.ms-powerpoint',

			// open office
			'odt' => 'application/vnd.oasis.opendocument.text',
			'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
		);

		$ext = strtolower(array_pop(explode('.',$filename)));
		if (array_key_exists($ext, $mime_types)) {
			return $mime_types[$ext];
		}
		elseif (function_exists('finfo_open')) {
			$finfo = finfo_open(FILEINFO_MIME);
			$mimetype = finfo_file($finfo, $filename);
			finfo_close($finfo);
			return $mimetype;
		}
		else {
			return 'application/octet-stream';
		}
	}

	function isEmail($email) {
		return preg_match('|^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]{2,})+$|i', $email);
	}




}
?>