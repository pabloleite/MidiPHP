<?php
define('FALLBACK_HOST', true);


//TODO: colocar como config porta e SSH, TSL ... talvez um switch
c::set('mail.admin_autority', 'Gosto do Brasil');
c::set('mail.admin_email', 'angela@gostodobrasil.com.br'); //c::set('mail.admin_email', 'midiway@terra.com.br');
c::set('mail.from_email', 'sender@gostodobrasil.com.br');

c::set('mail.host', 'smtp.gostodobrasil.com.br');
c::set('mail.port', 587);
c::set('mail.username', 'sender@gostodobrasil.com.br');
c::set('mail.password', 'q7kt5q');

if( FALLBACK_HOST ) // try to complement the default configuration
{
	c::set('mail.admin_autority', 'Rumo Certo - TESTE EZOOM');
	c::set('mail.from_email', 'carteiro@ezoom.com.br');
	c::set('mail.host', 'smtp.ezoom.com.br');
	c::set('mail.username', 'carteiro@ezoom.com.br');
	c::set('mail.password', 'car3434');
}
?>