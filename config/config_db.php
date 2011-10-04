<?php
switch( G_SITEMODE )
{
case 'desenvolvimento':
	c::set('db.host', 'localhost');
	c::set('db.user', 'root');
	c::set('db.password', '');
	c::set('db.name', 'rumocerto');
	c::set('db.debugging', true); //!
	//c::set('db.charset', '');//
	break;
	
case 'online':
	c::set('db.host', 'localhost');
	c::set('db.user', 'ezoomcp_lojamode');
	c::set('db.password', 'y9e4x66q.ALI');
	c::set('db.name', 'ezoomcp_loja_modelo');
	c::set('db.charset', 'utf8');
	break;
}
$res = db::connect(); assert( !core::error($res) );
$res = db::query("SET lc_time_names = 'pt_BR'", false); assert($res);



// CMS

?>