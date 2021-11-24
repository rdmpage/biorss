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
//$filename = 'examples/googlescholar.xml'; // rss2
//$filename = 'examples/native-pubmed.xml'; // rss2
//$filename = 'examples/ingenta.rdf';
$filename = 'examples/ajh.xml';
//$filename = 'examples/zookeys.xml';
//$filename = 'examples/canent.xml';

$filename = 'examples/mpe.xml';
$filename = 'examples/cnki.xml'; // rss2

$filename = 'examples/phytotaxa.xml'; // rss2
$filename = 'examples/sb.xml'; // 
$filename = 'examples/googlescholar.xml';
//$filename = 'examples/taxon.xml';

$filename = 'cache/2021-11-23/3bd4ee2e7045ead791e322c2f0fc7c76.xml';

$filename = 'cache/2021-11-23/068db33d20974d850dfe786514d3f70a.xml';
//$filename = 'cache/2021-11-23/7e2b51d9446382fee6cfa66b88388f73.xml';
$filename = 'cache/2021-11-23/b7a6def63bf0dad1ab0f285c614b43c2.xml'; // Pubmed
$filename = 'cache/2021-11-23/9d8468d30357f8a07dd790d4ded09d40.xml'; // ZooKeys
$filename = 'cache/2021-11-23/d08acaa8dc9b5e6f5bd8e6631cfd9e73.xml'; // zootaxa
$filename = 'cache/2021-11-23/3578d5934537d5474573eef7d1bf9de5.xml'; // mycotaxon

$filename = 'cache/2021-11-24/807b0d5897e9d8cd6fe528273687a5d5.xml'; // Rivista with DataCite DOI
$filename = 'cache/2021-11-24/0abe4bd4e1c41f034b91fd79cc81fda4.xml';
$filename = 'cache/2021-11-24/30e6d505389f0453d808ad9b438775f8.xml';
$filename = 'cache/2021-11-24/2150d69c452159c003d91353ddfb76a0.xml';

// Zoological Journal Linn Soc - blocks access if hit too hard

$filename = 'cache/2021-11-24/e379dc5d4d4c416ee999e4b32339d5e8.xml'; 

$filename = 'cache/2021-11-24/c072d1fa2ead34b56705e1d18037069f.xml'; // JoTT
$filename = 'cache/2021-11-24/6feb2bca6f7e9afe74928a2903a1a484.xml'; // IJSEM
$filename = 'cache/2021-11-24/3f855f4ae8332f01f6377fa341c3e93d.xml';


$xml = file_get_contents($filename);


$dataFeed = rss_to_internal($xml);

// file_put_contents("1.json", json_encode($dataFeed, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));

$n = count($dataFeed->dataFeedElement);

$force = false;
//$force = true;

for ($i = 0; $i < $n; $i++)
{

	
	// do we have this already?
	if ($couch->exists($dataFeed->dataFeedElement[$i]->{'@id'}) && !$force)
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
	
	// print_r($dataFeedElement);

	//echo json_encode($dataFeedElement);
	
	$url = 'http://localhost/~rpage/biorss/meta.php';
	$url = 'http://localhost/~rpage/biorss/geoparser.php';
	$url = 'http://localhost/~rpage/biorss/taxa.php';
	
	$code = post_job($url, $dataFeedElement);
		
	print_r($dataFeedElement);
	
	store($dataFeedElement);
}

// print_r($dataFeed);

// file_put_contents("2.json", json_encode($dataFeed, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));


//echo internal_to_rss($dataFeed);

?>
