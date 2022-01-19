<?php

// Harvest from DataCite by querying by publisher


// See https://support.datacite.org/docs/api-queries
// Also https://api.datacite.org/dois/10.5281/zenodo.1133361


require_once(dirname(__FILE__) . '/config.inc.php');
require_once(dirname(__FILE__) . '/rss.php');
require_once(dirname(__FILE__) . '/utils.php');

require_once(dirname(__FILE__) . '/datacite.php');

//----------------------------------------------------------------------------------------
function get($url, $accept = "text/html")
{
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	
	// Cookies 
	curl_setopt($ch, CURLOPT_COOKIEJAR, sys_get_temp_dir() . '/cookies.txt');
	curl_setopt($ch, CURLOPT_COOKIEFILE, sys_get_temp_dir() . '/cookies.txt');	
	
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
		"Accept: " . $accept,
		"Accept-Language: en-gb",
		"User-agent: Mozilla/5.0 (iPad; U; CPU OS 3_2_1 like Mac OS X; en-us) AppleWebKit/531.21.10 (KHTML, like Gecko) Mobile/7B405" 	
		));
	
	$response = curl_exec($ch);
	
	
	if($response == FALSE) 
	{
		$errorText = curl_error($ch);
		curl_close($ch);
		//die($errorText);
		return "";
	}
	
	$info = curl_getinfo($ch);
	$http_code = $info['http_code'];
	
	//print_r($info);
		
	curl_close($ch);
	
	return $response;
}


//----------------------------------------------------------------------------------------

// Where shall we store the feeds?
$today = date('Y-m-d', time());
$cache_dir = $config['cache'] . '/' . $today;
$latest_dir = $config['cache'] . '/latest';

if (!file_exists($cache_dir))
{
	$oldumask = umask(0); 
	mkdir($cache_dir, 0777);
	umask($oldumask);
}	

if (file_exists($latest_dir))
{
	unlink($latest_dir);
}	
symlink($cache_dir, $latest_dir);



//----------------------------------------------------------------------------------------

$year = 2021;

$publishers = array(
	'Naturhistorisches Museum Bern',
	//'Société Française d\'Ichtyologie',

);

foreach ($publishers as $publisher)
{
	$parameters = array(
		'query' 		=> 'publisher:' . $publisher,
		'registered' 	=> $year,
		'page[size]' 	=> 50		
		);

	$url = 'https://api.datacite.org/dois?' . http_build_query($parameters);

	echo $url . "\n";


	// call API
	$json = get($url, 'application/json');

	

	$obj = json_decode($json);
	
	$dataFeed = process_datacite($obj, $publisher, $url);	
	
	print_r($dataFeed);
	
	$json_filename = $latest_dir . '/' . $publisher . '.json';
 
	file_put_contents($json_filename, json_encode($dataFeed, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
}


?>
