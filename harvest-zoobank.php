<?php

// ZooBank-specific code

require_once(dirname(__FILE__) . '/vendor/autoload.php');
require_once(dirname(__FILE__) . '/config.inc.php');
require_once(dirname(__FILE__) . '/utils.php');
require_once(dirname(__FILE__) . '/rss.php');
require_once(dirname(__FILE__) . '/zoobank.php');

use Sunra\PhpSimple\HtmlDomParser;


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


$feed_history_filename = 'feedstatus.json';
$json = file_get_contents($feed_history_filename);
$data = json_decode($json);

$url = 'http://zoobank.org/rss/rss.xml';

$force = false;

$clean_filename = $url;
$clean_filename = preg_replace('/https?:\/\/(www\.)?/', '', $clean_filename);
$clean_filename = preg_replace('/\//', '-', $clean_filename);
$clean_filename = preg_replace('/\?(.*)$/', '', $clean_filename);
$clean_filename = preg_replace('/\.xml$/', '', $clean_filename);

//$data = null;
$rss = conditional_get($url, $data);

// update feed status
file_put_contents($feed_history_filename, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));

// Process RSS feed
if ($rss != '')
{	
	echo $rss;
	
	// Get the links to ZooBank pages
	$links = rss_to_links($rss);
	
	print_r($links);
	
	// Filter out eveything that isn't a publication
	$uuids = array();
	foreach ($links as $link)
	{
		if (preg_match('/urn:lsid:zoobank.org:pub:(?<uuid>.*)/', $link, $m))
		{
			$uuids[]  = $m['uuid'];
		}
	}
	
	print_r($uuids);
		
	//$uuids = array($uuids[0]);
	
	// Build feed from UUIDs
	
	$dataFeed = new stdclass;
	$dataFeed->name = "ZooBank";
	$dataFeed->url = "http://zoobank.org";
	$dataFeed->dataFeedElement = array();


	foreach ($uuids as $uuid)
	{
		$files = zoobank_retrieve($uuid);

		//print_r($files);

		if (count($files) == 2)
		{
			$obj = json_decode($files['json']);
	
			//print_r($obj);

			$dataFeedElement = new stdclass;
			$dataFeedElement->id = 'http://zoobank.org/References/' . $obj->referenceuuid;
			$dataFeedElement->url = $dataFeedElement->id;
	
			$dataFeedElement->name = full_clean_text($obj->title);
		
			// $dataFeedElement->datePublished = $item->PublishDate;

			// item
			$dataFeedElement->item = new stdclass;
	
			foreach ($obj as $k => $v)
			{
				switch ($k)
				{
					case 'title':
						add_to_item($dataFeedElement->item, 'name', full_clean_text($v));
						break;
				
					case 'endpage':
					case 'lsid':
					case 'number':
					case 'parentreference':
					case 'referenceuuid':
					case 'startpage':			
					case 'volume':
					case 'year':
						add_to_item($dataFeedElement->item, $k, $v);
						break;
				
					case 'authors':
						foreach ($v as $element)
						{
							$parts = array();
					
							if (isset($element[0]->givenname) && ($element[0]->givenname != ''))
							{
								$parts[] = $element[0]->givenname;
							}

							if (isset($element[0]->familyname) && ($element[0]->familyname != ''))
							{
								$parts[] = $element[0]->familyname;
							}
					
							add_to_item($dataFeedElement->item, 'author', join(' ', $parts));				
						}
						break;
				
					default:
						break;
				}
	
			}
	
			// HTML has some additional stuff such as DOI and a more precise date
			$dom = HtmlDomParser::str_get_html($files['html']);
	
			if ($dom)
			{	
				foreach ($dom->find('tr th[class=entry_label]') as $th)
				{
					switch (trim($th->plaintext))
					{
						case 'Date Published:':
							add_to_item($dataFeedElement->item, 'publicationDate', trim($th->next_sibling()->plaintext));	
							break;

						case 'DOI:':
							add_to_item($dataFeedElement->item, 'doi', trim($th->next_sibling()->plaintext));	
							break;
			
						default:
							break;
					}
	
				}
			}
	
			// set date for DataFeedElement
			if (isset($dataFeedElement->item->datePublished))
			{
				$dataFeedElement->datePublished = $dataFeedElement->item->datePublished;
			}
	
			$dataFeed->dataFeedElement[] = $dataFeedElement;
	
	

		}
	}
	
	print_r($dataFeed);
	
	// store
	
	$json_filename = $latest_dir . '/' . 'zoobank.json';
	 
	file_put_contents($json_filename, json_encode($dataFeed, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
	
	
	
	
}



?>

