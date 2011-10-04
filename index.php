<?php
define('CFG_FORCE_DEBUG', false);

require 'lib/kirby/kirby.php';
require 'config/config.php';
require 'defines.php';
require 'funcs.php';

require_once 'classes/engine/core.php';
require_once 'classes/shopping/consts.php';

s::start(); //s::destroy();
$res = setlocale(LC_TIME, 'pt_BR', 'pt_BR.utf-8', 'portuguese', 'portuguese-brazil', 'ptb', 'bra', 'brazil'); assert((bool) $res);


// EngineCore defini todo a criação da pagina; primeiro contrói as demais classes utilitárias da engine, e depois faz todo o processo de buffer/include do que esta sendo requisitado
// Site é a classe especializada que altera o comportamento de EngineCore definindo as opções principais do site - encaminhamento de includes e funcionamento das classes utilitárias
class Site extends EngineCore
{
	function StaticConstructCalls() //Construção das classes utilitarias relacionados com EngineCore
	{
		require 'classes/engine/login.php';
		Login::StaticConstruct();
		
		require_once 'classes/shopping/cart.php';
		Cart::StaticConstruct();
	}
	
	function AlternateInclude(_RequestContext $context)
	{
	}
}
Site::$gsite = new Site();
?>