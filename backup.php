<?php

error_reporting(E_ALL);

require_once (dirname(__FILE__) . '/config.inc.php');
require_once (dirname(__FILE__) . '/couchsimple.php');

$url = $config['couchdb_options']['prefix'] 
	. $config['couchdb_options']['host']
	. ':'
	. $config['couchdb_options']['port']
	. '/'
	. $config['couchdb_options']['database']
	. '/_design/housekeeping/_list/text/backup';
	
$command = 'curl ' . $url;

echo $command . "\n";

system($command);


?>
