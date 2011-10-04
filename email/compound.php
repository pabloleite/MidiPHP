<?php
// Class for e-mail assembling - midi
// define('MAILTEST', true);


class MailCompound
{
	private $img_cidattachs = array();
	private $message;
	
	public function __construct()
	{
		ob_start();
	}
	
	public function GetOutputUpdateCID( $cids )
	{
		$i = 0;
		foreach( $this->img_cidattachs as $hash => $attach )
		{
			//assert( !empty($cids[$i]) );
			if( !empty($cids[$i]) )
				$this->message = str_replace($hash, $cids[$i], $this->message);
			$i++;
		}
		return $this->message;
	}
	
	public function ListImgAttachs( $subpath )
	{
		$return_array = $this->img_cidattachs;
		foreach( $return_array as &$attach )
			$attach = $subpath.$attach;
		return $return_array;
	}
	
	
	// ---------------------------
	// Mail include interface
	// ---------------------------
	
	public function PrintImageAddr( $file )
	{
		if( defined('MAILTEST') && MAILTEST )
			echo $file;
		else {
			$hash = md5($file);
			echo $hash;
			$this->img_cidattachs[$hash] = $file;
		}
	}
	
	public function EndMessage()
	{
		$this->message = ob_get_clean();
	}
}
?>