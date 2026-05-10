<?php

// Fix records that lack taxa (e.g., because GlobalNames killed GraphQL API)

error_reporting(E_ALL);

require_once (dirname(__FILE__) . '/couchsimple.php');
require_once (dirname(__FILE__) . '/datastore.php');
require_once (dirname(__FILE__) . '/utils.php');
require_once (dirname(__FILE__) . '/augment.php');



echo "Getting records with no taxa...\n";


$limit = 1000;


$url = '_design/debugging/_view/no_classification'
	. '?startkey=' . urlencode('"' . 'https://www.mapress.com/' . '"')
	. '&endkey=' . urlencode('"' . 'https://www.mb' . '"')
	. '&include_docs=true'
	. '&limit=' . $limit
	;
	
// https://www.ingentaconnect.com/content/wfbi/fuse/2025/00000016/00000001/art00003
	
//_design/debugging/_view/no_classification?startkey=%22%22https%3A%2F%2Fdx.doi.org%2F10.1017%2Fjpa.2021.126%3Frft_dat%3Dsource%253Ddrss%22%22&endkey=%22https%3A%2F%2Fdz%22&inclu
//_design/debugging/_view/no_classification?startkey=%22https%3A%2F%2Fdx.doi.org%2F10.1017%2Fjpa.2021.126%3Frft_dat%3Dsource%253Ddrss%22&endkey=%22https%3A%2F%2Fe%22


$url = '_design/housekeeping/_view/no_taxa'
	. '?startkey=' . urlencode('"' . 'https://www.science' . '"')
	. '&endkey=' . urlencode('"' . 'https://www.sciencf' . '"')
	. '&include_docs=true'
	. '&reduce=false'
	. '&limit=' . $limit
	;

	
echo $url . "\n";

$resp = $couch->send("GET", "/" . $config['couchdb_options']['database'] . "/" . $url);

echo $resp;

$obj = json_decode($resp);

if ($obj)
{
	
	foreach ($obj->rows as $row)
	{
		$dataFeedElement = $row->doc;
		
		echo "\n" . $row->doc->_id . "\n";
		
		if (isset($dataFeedElement->message->item->name))
		{	
			if (is_object($dataFeedElement->message->item->name))
			{
				print_r($dataFeedElement->message->item->name);
			}
			else
			{
				echo $dataFeedElement->message->item->name . "\n";
			}
		}
		
		//echo "keywords\n";
		//print_r($dataFeedElement->message->keywords);
		
		echo "Getting taxa...\n";
		
		//print_r($dataFeedElement->message);
		
		$status = add_taxa($dataFeedElement->message);
		
		echo $status . "\n";
		
		//print_r($dataFeedElement->message);
		
		print_r($dataFeedElement->message->classification);
		
		store($dataFeedElement->message);
	
	}
}

?>
