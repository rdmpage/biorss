<?php

// Process feeds that are in native JSON format

require_once (dirname(__FILE__) . '/rss.php');
require_once (dirname(__FILE__) . '/datastore.php');

//----------------------------------------------------------------------------------------
// post
function post_job($url, &$doc)
{
	$http_code = 500;
	
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
	curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($doc));  
	
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	
	$response = curl_exec($ch);
	if($response == FALSE) 
	{
		$errorText = curl_error($ch);
		curl_close($ch);
		die($errorText);
	}
	
	$info = curl_getinfo($ch);
	$http_code = $info['http_code'];
	
	//echo $http_code . "\n";
	//echo $response;
	
	if ($http_code == 200)
	{
		$doc = json_decode($response );
	}
		
	curl_close($ch);
	
	return $http_code;
}

//----------------------------------------------------------------------------------------

$filename = 'cache/zoobank.json';
$filename = 'cache/google.json';

$json = file_get_contents($filename);
$dataFeed = json_decode($json);
$n = count($dataFeed->dataFeedElement);

$force = false;
//$force = true;

for ($i = 0; $i < $n; $i++)
{
	// do we have this already?
	if ($couch->exists($dataFeed->dataFeedElement[$i]->id) && !$force)
	{
		$doc = fetch($dataFeed->dataFeedElement[$i]->id);
		$dataFeedElement = $doc->message;
		
		echo "HAVE IT\n";
	}
	else
	{
		$dataFeedElement = $dataFeed->dataFeedElement[$i];
		
		echo "DON'T HAVE IT\n";
	}
	
	print_r($dataFeedElement);

	//echo json_encode($dataFeedElement);
	
	$url = '';
	$url = 'http://localhost/~rpage/biorss/meta.php';
	$url = 'http://localhost/~rpage/biorss/geoparser.php';
	$url = 'http://localhost/~rpage/biorss/taxa.php';
	
	if ($url != '')
	{
		$code = post_job($url, $dataFeedElement);
	}
		
	print_r($dataFeedElement);
	
	store($dataFeedElement);
}


?>
