<?php

// get records for a view and post process them, e.g. DOI classify

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
function doi_to_agency($prefix, $doi)
{
	global $prefix_to_agency;
	
	$agency = '';
			
	if (isset($prefix_to_agency[$prefix]))
	{
		$agency = $prefix_to_agency[$prefix];
	}
	else
	{
		$url = 'https://doi.org/ra/' . $doi;
	
		$json = get($url);
	
		//echo $json;
	
		$obj = json_decode($json);
	
		if ($obj)
		{
			if (isset($obj[0]->RA))
			{
				$agency = $obj[0]->RA;
		
				$prefix_to_agency[$prefix] = $agency;
			}
	
		}
	}
	
	return $agency;
}

echo "Getting prefixes...\n";


$prefix_filename = 'prefix.json';
$json = file_get_contents($prefix_filename);
$prefix_to_agency = json_decode($json, true);

echo "Getting queue...\n";

$limit = 1000;

$url = '_design/queue/_view/doi_no_agency'
	. '?descending=true'
	. '&include_docs=true'
	. '&limit=' . $limit
	;

$resp = $couch->send("GET", "/" . $config['couchdb_options']['database'] . "/" . $url);

$obj = json_decode($resp);


foreach ($obj->rows as $row)
{
	$dataFeedElement = $row->doc;

	// print_r($dataFeedElement->message->item);
	
	$doi = $dataFeedElement->message->item->doi;
	
	echo $doi . "\n";
	echo $dataFeedElement->_id . "\n";
	
	$parts = explode('/', $doi);
	$prefix = $parts[0];
		
	$agency = doi_to_agency($prefix, $doi);
	
	echo "agency=$agency\n";

	$dataFeedElement->message->item->doi_agency = $agency;

	store($dataFeedElement->message);
	


}




?>





