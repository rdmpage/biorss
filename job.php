<?php

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

$filename = 'examples/zootaxa.rdf';
$filename = 'examples/phytokeys.xml'; // rss2
//$filename = 'examples/cnki.xml'; // rss2
//$filename = 'examples/aby.xml'; // rss2
$filename = 'examples/googlescholar.xml'; // rss2
//$filename = 'examples/native-pubmed.xml'; // rss2
//$filename = 'examples/ingenta.rdf';


$xml = file_get_contents($filename);

$dataFeed = rss_to_internal($xml);

// file_put_contents("1.json", json_encode($dataFeed, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));

$n = count($dataFeed->dataFeedElement);

for ($i = 0; $i < $n; $i++)
{

	// do we have this already?
	if ($couch->exists($dataFeed->dataFeedElement[$i]->{'@id'}))
	{
		$doc = fetch($dataFeed->dataFeedElement[$i]->{'@id'});
		$dataFeedElement = $doc->message;
		
		echo "HAVE IT\n";
	}
	else
	{
		$dataFeedElement = $dataFeed->dataFeedElement[$i];
		
		echo "DON'T HAVE IT\n";
	}
	
	//print_r($dataFeedElement);
	
	$url = 'http://localhost/~rpage/biorss/meta.php';
	//$url = 'http://localhost/~rpage/biorss/geoparser.php';
	//$url = 'http://localhost/~rpage/biorss/meta.php';
	//$url = 'http://localhost/~rpage/biorss/taxa.php';
	
	$code = post_job($url, $dataFeedElement);
		
	print_r($dataFeedElement);
	
	store($dataFeedElement);
}

// print_r($dataFeed);

// file_put_contents("2.json", json_encode($dataFeed, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));


//echo internal_to_rss($dataFeed);

?>
