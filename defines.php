<?php
define('_URL_ABSOLUTE', @G_LOCALSITE ? 'server/rumocerto/index/' : 'http://www.rumocerto.com.br/');

class SiteConstants
{
	const URL_ABSOLUTE = _URL_ABSOLUTE;
	const URL_SUBSITE = '/'; //NYU
	
	const GET_INC_PARAM = 'p';
	const GET_INC_PARAM_MARK = '?p=';
	
	const DIR_USERFILES = 'userfiles';
	const PATH_PHPTHUMB = 'thumb/';
	
	static $site_begin_date;
}

class PageConstants //saída em texto
{
}

class LogicConstants //lógica de negócio
{
}

SiteConstants::$site_begin_date = strtotime('28 February 2011'); //pre-emptive
?>