<?php

// get records for a view and post process them, e.g. DOI classify

error_reporting(E_ALL);

require_once (dirname(__FILE__) . '/couchsimple.php');


if (1)
{
	// Things with no DOI sorted by recency of being added

	$limit = 100;

	$url = '_design/queue/_view/no_doi'
		. '?descending=true'
		. '&include_docs=true'
		. '&limit=' . $limit
		;
	
	$resp = $couch->send("GET", "/" . $config['couchdb_options']['database'] . "/" . $url);

	$obj = json_decode($resp);


	foreach ($obj->rows as $row)
	{
		$dataFeedElement = $row->doc;
	
		// echo $dataFeedElement->message->url . "\n";
		print_r($dataFeedElement->message->item);

		$url = $dataFeedElement->_id;
	
		if (isset($dataFeedElement->message->item->url))
		{
			$url = $dataFeedElement->message->item->url;
		}
	
		$urls[] = $url;
	
	
		// try and get DOI
	
		// add DOI
	
		// update metadata
	

	}

	print_r($urls);
}


?>





