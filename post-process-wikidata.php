<?php

// get records for a view and post process them, e.g. and Wikidata id

error_reporting(E_ALL);

require_once (dirname(__FILE__) . '/couchsimple.php');
require_once (dirname(__FILE__) . '/datastore.php');

//----------------------------------------------------------------------------------------
function get($url, $user_agent='', $content_type = '')
{	
	$data = null;

	$opts = array(
	  CURLOPT_URL =>$url,
	  CURLOPT_FOLLOWLOCATION => TRUE,
	  CURLOPT_RETURNTRANSFER => TRUE,
	  
		CURLOPT_SSL_VERIFYHOST=> FALSE,
		CURLOPT_SSL_VERIFYPEER=> FALSE,
	  
	);

	if ($content_type != '')
	{
		
		$opts[CURLOPT_HTTPHEADER] = array(
			"Accept: " . $content_type, 
			"User-agent: Mozilla/5.0 (iPad; U; CPU OS 3_2_1 like Mac OS X; en-us) AppleWebKit/531.21.10 (KHTML, like Gecko) Mobile/7B405" 
		);
		
	}
	
	$ch = curl_init();
	curl_setopt_array($ch, $opts);
	$data = curl_exec($ch);
	$info = curl_getinfo($ch); 
		
	curl_close($ch);
	
	return $data;
}

//----------------------------------------------------------------------------------------
// Does wikidata have this DOI?
function wikidata_item_from_doi($doi)
{
	$item = '';
	
	$sparql = 'SELECT * WHERE { ?work wdt:P356 "' . mb_strtoupper($doi) . '" }';
	
	//echo $sparql . "\n";
	
	$url = 'https://query.wikidata.org/bigdata/namespace/wdq/sparql?query=' . urlencode($sparql);
	$json = get($url, '', 'application/json');
	
	//echo $json;
		
	if ($json != '')
	{
		$obj = json_decode($json);
		if (isset($obj->results->bindings))
		{
			if (count($obj->results->bindings) != 0)	
			{
				$item = $obj->results->bindings[0]->work->value;
				$item = preg_replace('/https?:\/\/www.wikidata.org\/entity\//', '', $item);
			}
		}
	}
	
	return $item;
}

echo "Getting queue...\n";

$limit = 1000;

$url = '_design/queue/_view/doi_no_wikidata'
	. '?descending=true'
	. '&include_docs=true'
	. '&limit=' . $limit
	;

$resp = $couch->send("GET", "/" . $config['couchdb_options']['database'] . "/" . $url);

$obj = json_decode($resp);

$dois = array();

foreach ($obj->rows as $row)
{
	$dataFeedElement = $row->doc;

	// print_r($dataFeedElement->message->item);
	
	$doi = $dataFeedElement->message->item->doi;

	echo $doi . "\n";
	
	$qid = wikidata_item_from_doi($doi);
	
	if ($qid != '')
	{	
		echo "QID=$qid\n";
		$dataFeedElement->message->item->wikidata = $qid;
		store($dataFeedElement->message);
	}
	else
	{
		$dois[] = $doi;
	}

}

$dois = array_unique($dois);

// echo "DOIs not in Wikidata\n";
echo "\n" . '$dois=array(' . "\n";
foreach ($dois as $doi)
{
	echo '"' . $doi . '",' . "\n";
}
echo ");\n";





?>
