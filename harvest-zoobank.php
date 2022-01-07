<?php

// ZooBank-specific code

// Rich says run http://zoobank.org/rssfeed.cfm to refesh...

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
		$dataFeedElement = zoobank_to_feed_item($uuid);
		
		if ($dataFeedElement)
		{
			$dataFeed->dataFeedElement[] = $dataFeedElement;
		}
	}
	
	print_r($dataFeed);
	
	// store
	
	$json_filename = $latest_dir . '/' . 'zoobank.json';
	 
	file_put_contents($json_filename, json_encode($dataFeed, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
	
	
	
	
}



?>

