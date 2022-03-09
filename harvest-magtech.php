<?php

// Harvest data from web page


require_once (dirname(__FILE__) . '/vendor/autoload.php');
require_once (dirname(__FILE__) . '/config.inc.php');
require_once (dirname(__FILE__) . '/item.php');
require_once (dirname(__FILE__) . '/utils.php');


use Sunra\PhpSimple\HtmlDomParser;


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



// feed
$dataFeed = new stdclass;
$dataFeed->id = 'https://manu40.magtech.com.cn/Jwxb/EN/1672-6472/current.shtml';
$dataFeed->url = 'https://manu40.magtech.com.cn/Jwxb/EN/1672-6472/current.shtml';
$dataFeed->name = 'Mycostema';
$dataFeed->dataFeedElement = array();


$html = get($dataFeed->url);
//$html = file_get_contents('MYCOSYSTEMA.html');

if ($html == '')
{
	$status = 404;
}
else
{						
	$dom = HtmlDomParser::str_get_html($html);
	
	if ($dom)
	{	
	
		foreach ($dom->find('div[class=total-information] span[class=pull-left]') as $span)
		{
			if (preg_match('/Published:(?<date>.*)/', $span->plaintext, $m))
			{
				$dataFeed->datePublished = date(DATE_ISO8601, strtotime($m['date']));
			}
		}		
                    	
		foreach ($dom->find('ul[class=j-list]') as $ul)
		{
		
			$dataFeedElement = new stdclass;
			$dataFeedElement->item = new stdclass;
			
			if (isset($dataFeed->datePublished))
			{
				$dataFeedElement->datePublished = $dataFeed->datePublished;
			}			
			
			add_to_item($dataFeedElement->item, 'journal', 'Mycosystema');
			add_to_item($dataFeedElement->item, 'issn', '1672-6472');
				
			foreach ($ul->find('li[class=j-title]') as $li)
			{
				$dataFeedElement->name = html_entity_decode($li->plaintext, ENT_QUOTES | ENT_HTML5, 'UTF-8');			
				$dataFeedElement->item->name = $dataFeedElement->name;
				
				foreach ($li->find('a') as $a)
				{
					$dataFeedElement->id = $a->href;
				}
				
			}
			
			foreach ($ul->find('li[class=j-author]') as $li)
			{
				$author_string = html_entity_decode($li->plaintext, ENT_QUOTES | ENT_HTML5, 'UTF-8');
				
				$dataFeedElement->item->author = preg_split('/,\s*/', $author_string);
					
			}
			
			
			
			foreach ($ul->find('li[class=j-doi]') as $li)
			{
			
				foreach ($li->find('a') as $a)
				{
					$dataFeedElement->item->id = $a->href;
					$dataFeedElement->item->doi = clean_doi($dataFeedElement->item->id);
				}
				
				// <span class="njq">2021 Vol. 40 (12): 3061–3063 </span>
				foreach ($li->find('span[class=njq]') as $span)
				{
					// <span class="njq">2021 Vol. 40 (12): 3061–3063 </span>
					if (preg_match('/(?<year>[0-9]{4})\s+Vol\.\s+(?<volume>\d+)\s+\((?<issue>.*)\):\s+(?<spage>\d+)((&#x96;|–)(?<epage>.*))?/u', $span->plaintext, $m))
					{
						add_to_item($dataFeedElement->item, 'volume', $m['volume']);
						add_to_item($dataFeedElement->item, 'issue', $m['issue']);
						add_to_item($dataFeedElement->item, 'spage', $m['spage']);
						if ($m['epage'] != '')
						{
							add_to_item($dataFeedElement->item, 'epage', $m['epage']);
						}
						
						if (isset($dataFeedElement->datePublished))
						{
							$dataFeedElement->item->datePublished = $dataFeedElement->datePublished;
						}
						else
						{
							add_to_item($dataFeedElement->item, 'year', $m['year']);
						}
					}
				}
				
					
			}
			
			foreach ($ul->find('li a[class=figureClass]') as $a)
			{			
				$dataFeedElement->thumbnailUrl = $a->href;					
			}
			
							
			$dataFeed->dataFeedElement[] = $dataFeedElement;	

			
		}
		
		/*
			if (isset($meta->name) && ($meta->content != ''))
			{
				$meta->content = html_entity_decode($meta->content, ENT_QUOTES | ENT_HTML5, 'UTF-8');
			
				switch ($meta->name)
				{			
					case 'citation_doi':
						$dataFeedElement->item->doi = $meta->content;	
						$dataFeedElement->item->id = 'https://doi.org/' . $dataFeedElement->item->doi;								
						break;							
						
					case 'citation_title':	
						$dataFeedElement->item->name = $meta->content;
						$dataFeedElement->name = $meta->content;
						break;
						
					case 'citation_abstract_html_url':
						$dataFeedElement->id = $meta->content;	
						$dataFeedElement->url = $meta->content;								
						break;								
						
					case 'DC.Contributor':
					case 'citation_journal_title':
					case 'citation_volume':
					case 'citation_issue':
					case 'citation_firstpage':
					case 'citation_lastpage':
					case 'citation_issn':
					case 'citation_pdf_url':
						add_to_item($dataFeedElement->item, $meta->name, $meta->content);
						break;
						
					default:
						break;	
				}
			}			
		}
		
		
		
		if (!isset($dataFeedElement->datePublished) && isset($journal->date))
		{
			// hand code dates if we need them
			$dataFeedElement->item->datePublished = $journal->date;
			$dataFeedElement->datePublished = $dataFeedElement->item->datePublished;
		}
		
		if (!isset($dataFeedElement->datthumbnailUrlePublished) && isset($journal->thumbnailUrl))
		{
			// hand code thumbnail if needed
			$dataFeedElement->thumbnailUrl = $journal->thumbnailUrl;
		}
		
		*/
	
		
	}
}


print_r($dataFeed);

$filename = $latest_dir . '/' . $dataFeed->name . '.json';
file_put_contents($filename, json_encode($dataFeed, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));



?>
