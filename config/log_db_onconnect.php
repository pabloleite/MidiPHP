<?php
if( G_BUILDDEBUG )
{
	$sql = "CREATE TABLE IF NOT EXISTS `_log_db` (
				`id_log` int(10) unsigned NOT NULL auto_increment,
				`accessed` date NOT NULL,
				`tablename` varchar(500) NOT NULL,
				`sitemode` varchar(500) NOT NULL,
				PRIMARY KEY  (`id_log`)
			)";
	db::query($sql);
	
	/*select update_time from information_schema.tables where
	table_name='tablename'*/
}
?>