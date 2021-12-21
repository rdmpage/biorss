<?php

// $Id: //

/**
 * @file config.php
 *
 * Global configuration variables (may be added to by other modules).
 *
 */

global $config;

// Date timezone
date_default_timezone_set('UTC');

$config['cache'] = dirname(__FILE__) . '/cache';

// Environment----------------------------------------------------------------------------
// In development this is a PHP file that is in .gitignore, when deployed these parameters
// will be set on the server
if (file_exists(dirname(__FILE__) . '/env.php'))
{
	include 'env.php';
}

$config['platform'] = 'local';
$config['platform'] = 'cloud';

$config['site']		= 'local';
$config['site']		= 'heroku';

switch ($config['site'])
{
	case 'heroku':
		$config['web_server']	= 'https://biorss.herokuapp.com'; 
		$config['web_root']		= '/';
		$config['site_name'] 	= 'BioRSS';
		break;	

	case 'local':
	default:
		$config['web_server']	= 'http://localhost'; 
		$config['web_root']		= '/~rpage/biorss/';
		$config['site_name'] 	= 'BioRSS';
		break;
}

// CouchDB--------------------------------------------------------------------------------
		
if ($config['platform'] == 'local')
{
	$config['couchdb_options'] = array(
		'database' 	=> 'biorss',
		'host' 		=> '127.0.0.1',
		'port' 		=> 5984,
		'prefix' 	=> 'http://'
		);	

}

if ($config['platform'] == 'cloud')
{
	$config['couchdb_options'] = array(
		'database' 	=> 'biorss',
		'host' 		=> getenv('COUCHDB_HOST'),
		'port' 		=> getenv('COUCHDB_PORT'),
		'prefix' 	=> getenv('COUCHDB_PROTOCOL')
		);	
}


$config['stale'] = false;

// Twitter--------------------------------------------------------------------------------
$config['twitter_api_key'] = getenv('TWITTER_BEARER_TOKEN');

	
?>