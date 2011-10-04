<?php
class FormatLayer
{
	static function FormatOrderID($id)
	{
		assert($id);
		return str_pad( $id, 5, '0', STR_PAD_LEFT);
	}
	static function FormatCurrency($val)
	{
		return 'R$ '.number_format( $val, 2 , ',', '' );
	}
	static function FormatItemPrice($val, $n=1)
	{
		assert(is_float($val));
		return self::FormatCurrency($n * (float) $val);
	}
	static function FormatFloatStr($val)
	{
		return number_format((float) $val, 2, ',', '');
	}
	
	static function ClientFirstName($name)
	{
		return empty($name) ? '' : reset( explode(' ', $name) );
	}
	static function ClientLoginName($fetch)
	{
		assert( !empty($fetch) );
		if( empty($fetch['ident_apelido']) )
			return self::ClientFirstName($fetch['ident_nome']);
		return $fetch['ident_apelido'];
	}
	
	static function TitleCase($string, $delimiters = array(" ", "-", ".", "'", "O'", "Mc"), $exceptions = array("a" , "e", "ou", "I", "II", "III", "IV", "V", "VI", "VII", "VIII", "IX", "X", "XI", "XII", "XIII", "XIV", "XV", "XVI", "XVII", "XVIII", "XIX", "XX", "XXI", "XXII", "XXIII", "XXIV", "XXV", "XXVI", "XXVII", "XXVIII", "XXIX", "XXX" )) {
       /*
        * Exceptions in lower case are words you don't want converted
        * Exceptions all in upper case are any words you don't want converted to title case
        *   but should be converted to upper case, e.g.:
        *   king henry viii or king henry Viii should be King Henry VIII
        */
		$string = mb_convert_case($string, MB_CASE_TITLE);

		foreach ($delimiters as $dlnr => $delimiter){
			$words = explode($delimiter, $string);
			$newwords = array();
			foreach ($words as $wordnr => $word) {
				
				if (in_array(mb_strtoupper($word), $exceptions)){
					// check exceptions list for any words that should be in upper case
					$word = mb_strtoupper($word);
				}
				elseif (in_array(mb_strtolower($word), $exceptions)){
					// check exceptions list for any words that should be in upper case
					$word = mb_strtolower($word);
				}
				
				elseif (!in_array($word, $exceptions) ){
					// convert to uppercase (non-utf8 only)
					
					$word = ucfirst($word);
					
				}
				array_push($newwords, $word);
			}
			$string = join($delimiter, $newwords);
		}
		$string = str_replace('Ml', 'ML', $string);
		return $string;
	}
}

class DisplayLayer
{
	static function DivBoxPreco($rowp)
	{
		if( $rowp['flag_promocao']==1 )
		{
		?>
			<div id="label-wrap">
				<div id="label-price-promo">De: <?php echo FormatLayer::FormatCurrency($rowp['preco']); ?></div>
				<div id="label-price-base">Por: <?php echo FormatLayer::FormatCurrency($rowp['preco_promocional']); ?></div>
			</div>
		<?php
		} else {
		?>
			<div id="label-wrap" class="alone">
				<div id="label-price-base">Por: <?php echo FormatLayer::FormatCurrency($rowp['preco']); ?></div>
			</div>
		<?php
		}
	}
	
	/*
	<?php echo DisplayLayer::LayoutImagePath(''); ?>
	*/
	static function LayoutImagePath($file_path)
	{
		assert( CFG_SCHEMA=='html' );
		$file = pathinfo($file_path, PATHINFO_BASENAME ); assert(!empty($file));
		$res = db::row('layout_imagens', '*', "original = '$file' AND imagem IS NOT NULL");
		
		if( db::affected() )
		{
			$new_path = 'userfiles/layout_imagens/'.$res['imagem'];
			if( is_file(dirname(__FILE__).DIRECTORY_SEPARATOR.$new_path) )
				return $new_path;
		}
		return $file_path.'?skip_rewrite';
	}
}

class GenerateLayer
{
	static function GetOrdinal($number)
	{
		// get first digit
		$digit = abs($number) % 10;
		$ext = 'th';
		$ext = ((abs($number) %100 < 21 && abs($number) %100 > 4) ? 'th' : (($digit < 4) ? ($digit < 3) ? ($digit < 2) ? ($digit < 1) ? 'th' : 'st' : 'nd' : 'rd' : 'th'));
		return $number.$ext;
	}
}


class KudosFunctions //kudos for you
{
	static function converter_data($strData)
	{
		// Recebemos a data no formato: dd/mm/aaaa
		// Convertemos a data para o formato: aaaa-mm-dd
		if ( preg_match("#/#",$strData) == 1 )
			$strDataFinal .= implode('-', array_reverse(explode('/',$strData)));
		return $strDataFinal;
	}
	
	static function WebRequestAddr($cep)
	{
		// consulta o endereço remotamente através do CEP
		assert($cep);
		$res = ini_set("allow_url_fopen", 1); // habilita acesso a arquivos via URL
		$ctx = stream_context_create( array( 
				'http' => array('timeout' => 4)
			)
		);
		
		$cep = str_replace('-', '', $cep);
		$res = @file_get_contents('http://republicavirtual.com.br/web_cep.php?cep='.urlencode($cep).'&formato=query_string', 0, $ctx);
		if( $res )
			parse_str($res, $res);
		return $res ? $res : false;
	}
	
	static function SendEmail(MailCompound $mc, $subject, $email_to=null, $email_from=null)
	{
		assert(is_object($mc));
		
		if( !$email_to )
			$email_to = array(c::get('mail.admin_email') => c::get('mail.admin_autority'));
		if( !$email_from )
			$email_from = array(c::get('mail.from_email') => c::get('mail.admin_autority'));
		//$message_body = $mc->GetMessageOutput();
		//assert( !empty($message_body) );
		
		
		require_once dirname( __FILE__ ) . '/lib/Swift-4.0.6/lib/swift_required.php';
		
		try
		{
			//Create the Transport
			$transport = Swift_SmtpTransport::newInstance(c::get('mail.host'), 587)
				->setUsername(c::get('mail.username'))
				->setPassword(c::get('mail.password'))
			;
			
			//Create the Mailer using your created Transport
			$mailer = Swift_Mailer::newInstance($transport);
			
			//Use the Echo Logger
			if(1)
			{
				$logger = new Swift_Plugins_Loggers_EchoLogger();
				$mailer->registerPlugin(new Swift_Plugins_LoggerPlugin($logger));
			}
			
			//Create the message
			$message = Swift_Message::newInstance();
			
			// If placing the embed() code inline becomes cumbersome
			// it's easy to do this in two steps
			$cids = array();
			foreach( $mc->ListImgAttachs('email/') as $path )
				$cids[] = $message->embed(Swift_Image::fromPath($path));
			$message_body = $mc->GetOutputUpdateCID( $cids );
			
			$message
				//Give the message a subject
				->setSubject($subject)
				//Set the From address with an associative array
				->setFrom($email_from)
				//Set the To addresses with an associative array
				->setTo($email_to)
				//Give it a body
				->setBody($message_body, 'text/html', 'utf-8')// iso-8859-1 // iso-8859-2 nao tem crase
				//And optionally an alternative body
				//->addPart('test', 'text/html')
				//Optionally add any attachments
				//->attach(Swift_Attachment::fromPath('my-document.pdf'))
				
			;
			
			//$cid = $message->embed(Swift_Image::fromPath('image.png'));
			//var_dump($cid);
			
			//Send the message
			set_time_limit(15);
			$result = $mailer->send($message);//, $failures);//var_dump($failures);
		}
		
		catch(Exception $e)
		{
			die($e->getMessage());
		}
		
		return $result;
	}
	
	static function ProductAttribIsActive($ident)
	{
		$check = db::field(ShopDBConsts::TABLE_PRODUCTS_ATTRIBS, 'ativo', array('identificacao' => $ident));
		return (bool) $check;
	}
}
?>