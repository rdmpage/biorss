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

// three months ago
$feed_timestamp = time() - (60 * 60 * 24 * 30 * 3);

// one month ago
//$feed_timestamp = time() - (60 * 60 * 24 * 30);

// one week ago
//$feed_timestamp = time() - (60 * 60 * 24 * 7);

// today
//$feed_timestamp = time();


$issns = array(
	'2107-7207' => 'Acarologia', 
	//'0003-3847' => 'Annales Botanici Fennici',  // BioOne
	//'1572-9699' => 'Antonie van Leeuwenhoek', // Springer
	//'0010-065X' => 'The Coleopterists Bulletin', // BioOne
	//'1875-9866' => 'Contributions to Zoology', // Brill
	//'1803-6465' => 'Folia Parasitologica',
	//'1878-9129' => 'Fungal Diversity', // Springer
	
	//'0022-1511' => 'Journal of Herpetology', // BioOne
	
	//'1874-933X' => 'Kew Bulletin', // Springer
	//'1861-8952' => 'Mycological Progress', // Springer
	//'2657-5000' => 'Plant and Fungal Systematics',
	//'0035-418X' => 'Revue suisse de Zoologie', // BioOne
	//'1573-5192' => 'Systematic Parasitology', // Springer
	//'0002-8320' => 'Transactions of the American Entomological Society',  // BioOne
);

foreach ($issns as $issn => $journal)
{
	// query CrossRef for records within this time period
	$parameters = array(
		'filter' 		=> 'issn:' . $issn . ',from-pub-date:' . date("Y-m-H", $feed_timestamp)
		);
	
	$url = 'https://api.crossref.org/works?' . http_build_query($parameters);
	
	echo $url . "\n";
	
	// feed
	$dataFeed = new stdclass;
	$dataFeed->name = 'CrossRef ' . $issn;
	$dataFeed->url = $url;
	$dataFeed->dataFeedElement = array();	

	// call API
	$json = get($url, 'application/json');
	
	echo $json;

	$obj = json_decode($json);
	
	foreach ($obj->message->items as $item)
	{
	
		$dataFeedElement = new stdclass;
		$dataFeedElement->id = 'https://doi.org/' . $item->DOI;
		$dataFeedElement->url = $dataFeedElement->id;
		
		$dataFeedElement->item = new stdclass;			
		$dataFeedElement->item->doi = $item->DOI;
		
		if (isset($item->abstract))
		{
			$dataFeedElement->description = full_clean_text($item->abstract);
		}
					
		if (isset($item->title))
		{
			if (is_array($item->title))
			{
				$dataFeedElement->name = full_clean_text($item->title[0]);
			}
			else
			{
				$dataFeedElement->name = full_clean_text($item->title);
			}
			
			$dataFeedElement->item->name = $dataFeedElement->name;
		}
		
		if (isset($item->author))
		{
			foreach ($item->author as $author)
			{
				$parts = array();
				
				if (isset($author->given))
				{
					$parts[] = $author->given;
				}
				if (isset($author->family))
				{
					$parts[] = $author->family;
				}
				
				add_to_item($dataFeedElement->item, 'author', join(' ', $parts));	
			
			}
		}			
		
		if (isset($item->{'container-title'}))
		{
			if (is_array($item->{'container-title'}))
			{
				add_to_item($dataFeedElement->item, 'container-title', $item->{'container-title'}[0]);	
			}
			else
			{
				add_to_item($dataFeedElement->item, 'container-title', $item->{'container-title'});	
			}
		}
		
		if (isset($item->URL))
		{
			$dataFeedElement->url = $item->URL;			
		}

		if (isset($item->ISSN))
		{
			add_to_item($dataFeedElement->item, 'issn', $item->ISSN);		
		}

		if (isset($item->volume))
		{
			add_to_item($dataFeedElement->item, 'volume', $item->volume);		
		}

		if (isset($item->issue))
		{
			add_to_item($dataFeedElement->item, 'issue', $item->issue);		
		}

		if (isset($item->page))
		{
			add_to_item($dataFeedElement->item, 'page', $item->page);		
		}

		if (isset($item->issued))
		{			
			$d = $item->issued->{'date-parts'}[0];
			if ( count($d) > 0 ) $year = $d[0] ;
			if ( count($d) > 1 ) $month = preg_replace ( '/^0+(..)$/' , '$1' , '00'.$d[1] ) ;
			if ( count($d) > 2 ) $day = preg_replace ( '/^0+(..)$/' , '$1' , '00'.$d[2] ) ;
		
			if ( isset($month) and isset($day) ) $date 	= "$year-$month-$day";
			else if ( isset($month) ) $date 			= "$year-$month-00";
			else if ( isset($year) ) $date 				= "$year-00-00";
			
			$dataFeedElement->item->datePublished = $date;
			
			// Set date for feed element
			$dataFeedElement->datePublished = $dataFeedElement->item->datePublished;
		}
		
		$dataFeed->dataFeedElement[] = $dataFeedElement;
	
	}
	
	print_r($dataFeed);
	
	$json_filename = $latest_dir . '/' . $issn . '.json';
 
	file_put_contents($json_filename, json_encode($dataFeed, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
}


?>
