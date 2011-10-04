<?php
include 'classes/gateway/pagamentodigital.gateway.php';


abstract class GatewayPD_ReturnPage extends IncludePage
{
	protected $gateway_return;
	
	public function IncludeOper()
	{
		// Gateway transaction
		GatewayPagamentoDigital::InitService();
		$this->gateway_return = GatewayPagamentoDigital::Procedure2_ConfirmAuthenticity();
	}
}
?>