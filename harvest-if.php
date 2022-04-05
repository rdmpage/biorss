<?php

// Harvest from source database since a date

require_once(dirname(__FILE__) . '/config.inc.php');
require_once(dirname(__FILE__) . '/rss.php');
require_once(dirname(__FILE__) . '/utils.php');

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

// one month ago
$feed_timestamp = time() - (60 * 60 * 24 * 30);

// one week ago
$feed_timestamp = time() - (60 * 60 * 24 * 7);

// today
//$feed_timestamp = time();


// query IF for records modified within this time period
$parameters = array(
	'rank' 		=> 'sp.',
	'startDate' => date('Ymd', $feed_timestamp) // IF requires YYYYMMDD
	);
	
	

$url = 'http://www.indexfungorum.org/IXFWebService/Fungus.asmx/UpdatedNames?' . http_build_query($parameters);

// feed
$dataFeed = new stdclass;
$dataFeed->datePublished = date(DATE_ISO8601, $feed_timestamp);
$dataFeed->name = 'Index Fungorum ' . $dataFeed->datePublished;
$dataFeed->url = $url;
$dataFeed->dataFeedElement = array();

// we want to filter out anything not modified in this time period
// IF doesn't have any date information more precise than year :(
// so we rely on publication dates, but retrospective adding of records means
// we need to be lenient otherwise we could miss relevant records

$filter_dates = array('2021-00-00', '2022-00-00');


// call API
$xml = get($url);


$lsids = array();
				
$dom = new DOMDocument;
$dom->loadXML($xml, LIBXML_NOCDATA); 
$xpath = new DOMXPath($dom);

foreach ($xpath->query('//FungusNameLSID') as $node)
{			
	$lsids[] = $node->firstChild->nodeValue;
}

print_r($lsids);


$count = 1;

foreach ($lsids as $lsid)
{
	$url = 'http://www.indexfungorum.org/IXFWebService/Fungus.asmx/NameByKeyRDF?NameLsid=' . $lsid;
	$xml = get($url);
	
	// echo $xml;
	
	$dom = new DOMDocument;
	$dom->loadXML($xml, LIBXML_NOCDATA); 
	$xpath = new DOMXPath($dom);
	
	$xpath->registerNamespace('PublicationCitation', 'http://rs.tdwg.org/ontology/voc/PublicationCitation#');
	$xpath->registerNamespace('TaxonName', 'http://rs.tdwg.org/ontology/voc/TaxonName#');
	$xpath->registerNamespace('ns', 'http://purl.org/dc/elements/1.1/');
	$xpath->registerNamespace('rdf', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#');

	// element is the LSID record for a name
	$dataFeedElement = new stdclass;
	$dataFeedElement->id = $lsid;
	$dataFeedElement->url = 'http://www.indexfungorum.org/Names/NamesRecord.asp?RecordID=' . str_replace('urn:lsid:indexfungorum.org:names:', '', $lsid);
	
	// set date to be when we made the query (IF has no metadata indicating modification time)
	$dataFeedElement->datePublished = $dataFeed->datePublished;
	
	foreach ($xpath->query('//TaxonName:TaxonName[@rdf:about="' . $lsid . '"]') as $taxon_name)
	{			
		foreach ($xpath->query('ns:Title', $taxon_name) as $node)
		{
			$dataFeedElement->name = $node->firstChild->nodeValue;
		}
	
	}
	
	// we have only a partial bibliographic record
	$dataFeedElement->item = new stdclass;
	
	$terms = array();
	
	foreach ($xpath->query('//PublicationCitation:PublicationCitation') as $publication)
	{			
		foreach ($xpath->query('PublicationCitation:title', $publication) as $node)
		{
			add_to_item($dataFeedElement->item, 'journal', $node->firstChild->nodeValue);
			$terms[] = $node->firstChild->nodeValue;
		}

		foreach ($xpath->query('PublicationCitation:volume', $publication) as $node)
		{
			add_to_item($dataFeedElement->item, 'volume', $node->firstChild->nodeValue);
			$terms[] = $node->firstChild->nodeValue;
		}

		foreach ($xpath->query('PublicationCitation:number', $publication) as $node)
		{
			add_to_item($dataFeedElement->item, 'number', $node->firstChild->nodeValue);
			$terms[] = $node->firstChild->nodeValue;
		}

		foreach ($xpath->query('PublicationCitation:pages', $publication) as $node)
		{
			add_to_item($dataFeedElement->item, 'pages', $node->firstChild->nodeValue);
			$terms[] = $node->firstChild->nodeValue;
		}

		foreach ($xpath->query('PublicationCitation:year', $publication) as $node)
		{
			add_to_item($dataFeedElement->item, 'year', $node->firstChild->nodeValue);
			$terms[] = $node->firstChild->nodeValue;
		}
	
	}	
	
	// create a description
	if (count($terms) > 0)
	{
		$dataFeedElement->description = join(' ', $terms);
	}
	
	if (isset($dataFeedElement->item->datePublished) && (in_array($dataFeedElement->item->datePublished, $filter_dates)))
	{
		$dataFeed->dataFeedElement[] = $dataFeedElement;
	}
	
	// Give server a break every 10 items
	if (($count++ % 10) == 0)
	{
		$rand = rand(1000000, 3000000);
		echo "\n-- ...sleeping for " . round(($rand / 1000000),2) . ' seconds' . "\n\n";
		usleep($rand);
	}

}


print_r($dataFeed);



$json_filename = $latest_dir . '/' . 'indexfungorum.json';
 
file_put_contents($json_filename, json_encode($dataFeed, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));


?>

