<?php

// Parse journal(s) from DOAJ

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


$issns = array(
	'2063-1588' => 'Opuscula Zoologica Instituti Zoosystematici et Oecologici Universitatis Budapestinensis',
);

$dataFeed = new stdclass;
$dataFeed->name = "Directory of Open Access Journals";
$dataFeed->url = "https://doaj.org";
$dataFeed->dataFeedElement = array();


foreach ($issns as $issn => $journal)
{
	$url = 'https://doaj.org/api/search/articles/' . urlencode('issn:' . $issn) . '?sort=' . urlencode('last_updated:desc');
	
	$json = get($url, 'application/json');
	
	
	$obj = json_decode($json);
	
	// print_r($obj);
	
	// convert to internal RSS format
	
	foreach ($obj->results as $result)
	{
		$dataFeedElement = new stdclass;
		$dataFeedElement->id = 'https://doaj.org/article/' . $result->id;
		$dataFeedElement->url = $dataFeedElement->id;
		
		$dataFeedElement->datePublished = date(DATE_ISO8601, strtotime($result->created_date));
			
		$dataFeedElement->name = full_clean_text($result->bibjson->title);
		
		// item
		$dataFeedElement->item = new stdclass;
		
		
		foreach ($result->bibjson as $k => $v)
		{
			switch ($k)
			{
				case 'abstract':
					$dataFeedElement->description = full_clean_text($v);
					break;
			
				case 'title':
					add_to_item($dataFeedElement->item, 'name', full_clean_text($v));
					break;
										
				case 'start_page':
				case 'end_page':
				case 'year':
					add_to_item($dataFeedElement->item, $k, $v);
					break;
					
					
				case 'identifier':
					foreach ($v as $identifier)
					{
						switch ($identifier->type)
						{
							case 'doi':
								add_to_item($dataFeedElement->item, 'doi', $identifier->id);
								break;

							case 'issn':
							case 'pissn':
							case 'eissn':
								add_to_item($dataFeedElement->item, 'issn', $identifier->id);
								break;
						
							default:
								break;
						}
					}
					break;

				case 'link':
					foreach ($v as $link)
					{
						if ($link->type == 'fulltext' && preg_match('/\.pdf$/', $link->url))
						{
							$dataFeedElement->item->pdf = $link->url;
						}
					}
					break;
			
				case 'author':
					foreach ($v as $author)
					{
						add_to_item($dataFeedElement->item, 'author', $author->name);			
					}
					break;

				case 'journal':
					if (isset($v->volume))
					{
						add_to_item($dataFeedElement->item, 'volume', $v->volume);
					}
					if (isset($v->number))
					{
						add_to_item($dataFeedElement->item, 'number', $v->number);
					}
					if (isset($v->title))
					{
						add_to_item($dataFeedElement->item, 'journal', $v->title);
					}
				break;
			
			
				default:
					break;
			}
		
		}
		
		if (isset($result->bibjson->year) && isset($result->bibjson->month))
		{
			$date = $result->bibjson->year . '-' . str_pad($result->bibjson->month, 2, '0', STR_PAD_LEFT) . '-00';
			$dataFeedElement->item->datePublished = $date;
		}
		
		$dataFeed->dataFeedElement[] = $dataFeedElement;
	
	}


}

$json_filename = $latest_dir . '/' . 'doaj.json';
 
file_put_contents($json_filename, json_encode($dataFeed, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));


//print_r($dataFeed);

?>

