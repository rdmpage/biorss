<?php

// Harvest data from journals where we can generate URLs and they have meta tags


require_once (dirname(__FILE__) . '/vendor/autoload.php');
require_once (dirname(__FILE__) . '/config.inc.php');
require_once (dirname(__FILE__) . '/item.php');
require_once (dirname(__FILE__) . '/utils.php');

if (1)
{
	require_once (dirname(__FILE__) . '/HtmlDomParser.php');
}
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

// need to do this more elegantly...


// list of journals to fetch
$periodicals = array();

// Zoological Systematics
$journal = new stdclass;
$journal->name 	= 'Zoological Systematics';
$journal->urls	= array();



// generate article URLs

$journal->date			= '2021-04-24' ; // 24 April 2021
$journal->thumbnailUrl	= 'http://www.zootax.com.cn/fileup/COVER/20210127143817.JPG';
$year 					= 2021;
$issue 					= 2;
$num_articles_per_issue = 10;

for ($i = 1; $i <= $num_articles_per_issue; $i++)
{
	$journal->urls[] = 'https://doi.org/10.11865/zs.' . $year . $issue . str_pad($i, 2, '0', STR_PAD_LEFT);
}

$periodicals[] = $journal;


print_r($periodicals);


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

foreach ($periodicals as $journal)
{
	// feed
	$dataFeed = new stdclass;
	$dataFeed->dataFeedElement = array();
	
	// fetch metadata
	foreach ($journal->urls as $url)
	{
		$html = get($url);
		
		if ($html == '')
		{
			$status = 404;
		}
		else
		{						
			$dom = HtmlDomParser::str_get_html($html);
			
			if ($dom)
			{	
				$dataFeedElement = new stdclass;
				$dataFeedElement->item = new stdclass;
			
			
				// meta
				foreach ($dom->find('meta') as $meta)
				{
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
				
				if (isset($dataFeedElement->id))
				{				
					$dataFeed->dataFeedElement[] = $dataFeedElement;	
				}
			}
		}
	}

	print_r($dataFeed);
	
	$filename = $latest_dir . '/' . $journal->name . '.json';
	file_put_contents($filename, json_encode($dataFeed, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));

}

?>
