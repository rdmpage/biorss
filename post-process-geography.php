<?php

// Fix records that lack taxa (e.g., because GlobalNames killed GraphQL API)

error_reporting(E_ALL);

require_once (dirname(__FILE__) . '/couchsimple.php');
require_once (dirname(__FILE__) . '/datastore.php');
require_once (dirname(__FILE__) . '/utils.php');
require_once (dirname(__FILE__) . '/augment.php');


echo "Getting all records\n";


$limit = 100;

$url = '_design/queue/_view/modified'
	. '?descending=false'
	. '&include_docs=true'
	. '&limit=' . $limit
	;		
	
echo $url . "\n";

$resp = $couch->send("GET", "/" . $config['couchdb_options']['database'] . "/" . $url);

$obj = json_decode($resp);

foreach ($obj->rows as $row)
{
	$dataFeedElement = $row->doc;
	
	echo "\n" . $row->doc->_id . "\n";
	echo $dataFeedElement->message->item->name . "\n";
	
	
	echo "Geoparsing...\n";
	
	//print_r($dataFeedElement->message);
	
	$status = add_geo($dataFeedElement->message);
	
	echo $status . "\n";
	
	print_r($dataFeedElement->message->contentLocation);
	
	store($dataFeedElement->message);
}

?>
