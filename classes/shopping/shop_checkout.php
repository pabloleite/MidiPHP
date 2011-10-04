<?php
class ShopActiveCheckout
{
	private $session;
	public $frete_data;
	public $addr_data;
	
	public function __construct()
	{
		$this->session = new SessionProp('Checkout');
	}
	
	// Pagamento
	public function PgtoSetMode($newmode)
	{
		$this->session->pgto_mode = $newmode;
	}
	public function PgtoGetMode()
	{
		return ($r=$this->session->pgto_mode) ? $r : ShoppingConsts::PGTO_SERVICE_DEFAULT;
	}
	
	// Frete
	public function FreteSetMode($newmode)
	{
		$this->session->frete['mode'] = $newmode;
	}
	public function FreteGetMode()
	{
		$addr_fields = $this->AddrGetFields();
		
		if( db::value(ShopDBConsts::TABLE_CONFIG, 'frete_gratis_flag_min')==1 && db::value(ShopDBConsts::TABLE_CONFIG, 'frete_gratis_val_min') < $this->Total(false, false) )
			return ShoppingConsts::FRETE_SERVICE_GRATIS;
		if( db::value(ShopDBConsts::TABLE_CONFIG, 'frete_gratis_flag_cidade')==1 && is_int(stripos(db::value(ShopDBConsts::TABLE_CONFIG, 'frete_gratis_val_cidade'), $addr_fields['ender_cidade'])) )
			return ShoppingConsts::FRETE_SERVICE_GRATIS;
		return ($r=$this->session->frete['mode']) ? $r : ShoppingConsts::FRETE_SERVICE_DEFAULT;
	}
	
	public function FreteSetCEP($cep)
	{
		$this->session->frete['cep'] = str_replace('-', '', $cep);
		$res = $this->FreteCheckPrice();
		if( !$res )
			unset( $this->session->frete );
		$this->session->addr_adjusted = false;
		return $res;
	}
	public function FreteGetCEP()
	{
		if($this->session->frete['valid'])
			$cep = $this->session->frete['cep'];
		elseif( $fetch = $this->AddrQueryClient() )
			$cep = $fetch['ender_cep'];
		else
			return;
		return implode('-', str_split($cep, 5));
	}
	
	public function FretePrice($fretevars=false)
	{
		if( $this->FreteGetMode()==ShoppingConsts::FRETE_SERVICE_GRATIS )
			return 0.;
		else {
			$this->FreteCheckPrice();
			
			$arr_modes = array(ShoppingConsts::FRETE_SERVICE_SEDEX, ShoppingConsts::FRETE_SERVICE_PAC);
			foreach( $arr_modes as $mode )
				$arr_ret[$mode] = (float) $this->frete_data[$mode]['vtotal'];
		}
		return $fretevars ? $arr_ret : $arr_ret[$this->FreteGetMode()];
	}
	
	public function FretePrazo()
	{
		if( $this->FreteGetMode()==ShoppingConsts::FRETE_SERVICE_GRATIS )
			return 3;
		$this->FreteCheckPrice();
		return $this->frete_data[$this->FreteGetMode()]['prazo'] + 1;// +1 do dia da postagem, tanto PAC quanto SEDEX
	}
	
	public function FreteRegiao()
	{
		if( $this->FreteGetMode()==ShoppingConsts::FRETE_SERVICE_GRATIS )
			return 'RS - Interior';
		$this->FreteCheckPrice();
		return $this->frete_data[$this->FreteGetMode()]['regiao'];
	}
	
	public function FreteCheckPrice()
	{
		if( !$this->frete_data )
		{
			$peso_sum = 0;
			foreach( Cart::GetOrderList() as $item )
				$peso_sum += $item->fetch['peso'] * $item->qty;
			$peso_sum = $peso_sum / 1000;
			
			require 'frete/calculaFrete.php';
			$this->frete_data = frete( array('40010' /*Sedex*/, '41106' /*PAC*/), $peso_sum, $this->session->frete['cep'], ValueConsts::FRETE_CEP_ORIGEM );
		}
		$valid = $this->frete_data['40010']['valor'] && $this->frete_data['41106']['valor'];
		return $this->session->frete['valid'] = $valid;
	}
	
	// Endereço
	public function AddrQueryClient()
	{
		return Login::$logged ? db::row(ShopDBConsts::TABLE_ADDR, '*', array('id_usuario' => Login::$login_id, 'principal' => 1)) : false;
	}
	public function AddrNeedAdjust()
	{
		return !($this->session->addr_adjusted || (($fetch = $this->AddrQueryClient()) && $fetch['ender_cep']==$this->session->frete['cep']) );
	}
	
	public function AddrGetFields()
	{
		$data = array();
		if( $this->session->addr_adjusted )
			$data = $this->addr_data = $this->session->addr_fields;
		elseif( ($fetch = $this->AddrQueryClient()) && $fetch['ender_cep']==$this->session->frete['cep'] )
			$data = $this->addr_data = $fetch;
		elseif( $this->session->frete['valid'] )
		{
			if( $res_data = KudosFunctions::WebRequestAddr($this->session->frete['cep']) )
			{
				$this->addr_data['ender_estado'] = $res_data['uf'];
				$this->addr_data['ender_cidade'] = $res_data['cidade'];
				$this->addr_data['ender_bairro'] = $res_data['bairro'];
				$this->addr_data['ender_endereco'] = $res_data['tipo_logradouro'].' '.$res_data['logradouro'];
			}
			$this->addr_data['ender_cep'] = $this->session->frete['cep'];
			$data = $this->addr_data;
		}
		return $data;
	}
	public function AddrSetFields() //form is responsable to ensure post data
	{
		$this->session->addr_adjusted = true;
		$this->session->addr_fields['ender_cep'] = $_POST['cep'];
		$this->session->addr_fields['ender_estado'] = $_POST['uf'];
		$this->session->addr_fields['ender_cidade'] = $_POST['cidade'];
		$this->session->addr_fields['ender_bairro'] = $_POST['bairro'];
		$this->session->addr_fields['ender_endereco'] = $_POST['endereco'];
		$this->session->addr_fields['ender_numero'] = $_POST['numero'];
		$this->session->addr_fields['ender_complemento'] = $_POST['complemento'];
	}
	
	// Cálculo total de venda
	public function OrderTotal($fretevars=false, $addfrete=true)
	{
		if( !$this->session->frete['valid'] )
			return false;
		
		$total = 0.;
		$total += Cart::GetOrderList()->ListTotalPrice();
		if($fretevars)
		{
			$arr_fretes = $this->FretePrice(true);
			$arr_modes = array(ShoppingConsts::FRETE_SERVICE_SEDEX, ShoppingConsts::FRETE_SERVICE_PAC);
			foreach( $arr_modes as $mode )
				$ret_total[$mode] = $total + $arr_fretes[$mode];
		} elseif($addfrete)
			$total += $this->FretePrice();
		return $fretevars ? $ret_total : $total;
	}
}
?>