<?php

error_reporting(E_ALL);

require_once (dirname(__FILE__) . '/config.inc.php');
require_once (dirname(__FILE__) . '/couchsimple.php');


//----------------------------------------------------------------------------------------
function fetch($id)
{
	global $config;
	global $couch;

	$resp = $couch->send("GET", "/" . $config['couchdb_options']['database'] . "/" . urlencode($id));
	$response_obj = json_decode($resp);
	
	return $response_obj;
}

//----------------------------------------------------------------------------------------
function store($dataFeedElement, $force = false)
{
	global $config;
	global $couch;
	
	$id = $dataFeedElement->id;
	
	$go = true;
	
	$exists = $couch->exists($id);

	if ($exists)
	{
		$resp 			= $couch->send("GET", "/" . $config['couchdb_options']['database'] . "/" . urlencode($id));	
		$doc 			= json_decode($resp);
		$revision 		= $doc->_rev;
	
		if ($force)
		{
			// force so delete and start again
			$resp = $couch->send("DELETE", "/" . $config['couchdb_options']['database'] . "/" . urlencode($id). '?rev=' . $revision);	
			$go = true;
		}
		else
		{
			// exists so update it
			$doc->{'message-modified'}  = date("c", time()); // now
			$doc->{'message'} 		  	= $dataFeedElement;
			$resp = $couch->send("PUT", "/" . $config['couchdb_options']['database'] . "/" . urlencode($id), json_encode($doc));	
			
			$go = false;
		}		
	}
	
	// New, or starting from scratch
	if ($go)
	{
		// so we can have an internal queue if we need one
		$doc = new stdclass;
		$doc->_id 					= $dataFeedElement->id;
		$doc->{'message-timestamp'} = date("c", time());			// now
		$doc->{'message-modified'}  = $doc->{'message-timestamp'};	// now
		//$doc->{'message-format'} 	= 'unknown';					// what type of object is this?		
	
		// data
		$doc->{'message'} 		  	= $dataFeedElement;
		
		//print_r($doc);
				
		$resp = $couch->send("PUT", "/" . $config['couchdb_options']['database'] . "/" . urlencode($doc->_id), json_encode($doc));
	}

}

//----------------------------------------------------------------------------------------

// test
if (0)
{
	require_once (dirname(__FILE__) . '/rss.php');

	$force 	= true;

	$filename = 'examples/phytokeys.xml'; // rss2

	$xml = file_get_contents($filename);

	$dataFeed = rss_to_internal($xml);

	$n = count($dataFeed->dataFeedElement);

	for ($i = 0; $i < $n; $i++)
	{
		print_r($dataFeed->dataFeedElement[$i]);
		
		store($dataFeed->dataFeedElement[$i], $force);
	
	}
}

?>
