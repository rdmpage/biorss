<?php

// Process feed
require_once (dirname(__FILE__) . '/config.inc.php');
require_once (dirname(__FILE__) . '/datastore.php');
require_once (dirname(__FILE__) . '/utils.php');

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

function process_feed($dataFeed, $force = false)
{
	global $couch;
	
	$n = count($dataFeed->dataFeedElement);

	for ($i = 0; $i < $n; $i++)
	{
		$modified = false;

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
			$modified = true;
		
			echo "DON'T HAVE IT\n";
		}
		
		
	
		if (!isset($dataFeedElement->meta) || $force)
		{
			echo "Adding metadata\n";
			$url = 'http://localhost/~rpage/biorss/meta.php';
			$code = post_job($url, $dataFeedElement);
		
			$modified = true;
		}
		else
		{
			echo "Have already added metadata\n";
		}
		
		//print_r($dataFeedElement);
		
		//exit();
		
		// image
		if (!isset($dataFeedElement->thumbnailUrl))
		{
			if (isset($dataFeedElement->item->container))
			{
				switch ($dataFeedElement->item->container)
				{
					case 'Annales Botanici Fennici':
						$dataFeedElement->thumbnailUrl = 'https://bioone.org/ContentImages/journals/anbf/58/4-6/4-6/WebImages/085.058.0400.cover.jpg';
						break;
				
					default:
						break;
				}
			}
		}		
		
		if (isset($dataFeedElement->thumbnailUrl))
		{
			$dataFeedElement = encode_image($dataFeedElement);
			$modified = true;
		}
		
		//print_r($dataFeedElement);
			
		
		if (!isset($dataFeedElement->contentLocation) || $force)
		{
			echo "Geoparsing\n";
			$url = 'http://localhost/~rpage/biorss/geoparser.php';
			$code = post_job($url, $dataFeedElement);
		
			$modified = true;
		}
		else
		{
			echo "Have already geoparsed\n";
		}
		
				
		if (!isset($dataFeedElement->classification) || $force)
		{
			echo "Classifying\n";
			$url = 'http://localhost/~rpage/biorss/taxa.php';
			$code = post_job($url, $dataFeedElement);
		
			$modified = true;
		}
		else
		{
			echo "Have already added taxa\n";
		}

		if (!$dataFeedElement)
		{
			echo "Line " . __LINE__  . " badness problem\n";
			exit();
		}

		
		// exit();

		if ($modified)
		{
			store($dataFeedElement);
		}
	}
}

?>
