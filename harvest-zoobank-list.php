<?php

// Create ZooBank feed from list of UUIDs (e.g., if ZooBank RSS is down, or we are 
// uploading a lot of data.


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

$uuids = array(
'5F93E4EB-0C09-4F4E-8C08-12887C0D49BD',

'894abe3b-f665-4f71-9328-4855fb8fc90f',
'68e194f7-5e74-4748-93eb-ff84a5cf675d',
'ee44e9a6-5fc5-4623-a3de-8649bd7ffb0d',
'81cc9416-c366-4525-a1ee-9c5cad063d9e',
'8ad7d7b8-8384-4bdf-8243-31f1c210eb48',
'242a12cc-a793-47ac-ad11-665123ad6edd',
'dc1edfc7-901a-402e-828c-43db7b2e5203',
'6a4424d0-defd-4783-9b75-24ac2c7829f7',
'422da9c2-cc2c-4ab6-bf90-1d2bc0fb324d',
'ec231a77-580d-46dc-8328-3c41507e0527',
'5f3940bb-d1a8-4fa5-9609-ce8b880ddecf',
'54fa15fd-f281-4c27-91a1-2370cf9b488e',
'ad9a368b-11d2-49d9-a1c0-3451d239361d',
'e0269f20-6634-4bf9-a9ac-716929c0c180',
'97c72bd6-a9bd-4281-a6fe-dea5e33b359e',
'41ff30c8-8e27-45a5-a250-13937d6206f3',
'7968e7cd-2c3a-402a-82f5-acd44e8322c2',
'eaf5bd14-e52a-46d0-a7a0-b238b3dc727d',
'f3151f86-5f4e-4269-bc3e-cb0166a895c3',
'acfa7036-c10e-4370-bf43-d22288168f33',
'947d88e4-c53d-4631-b6ee-94a4df9f540a',
'e8f59cf5-73e3-461c-9191-04e560eeac40',
'ca75c8db-013e-42d3-920f-654890cafcac',
'09e84aef-4422-4e55-9959-a728620dc465',
'86daaf2d-c098-452b-b3ea-51d84eb5855e',
);
	
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

$json_filename = $latest_dir . '/' . 'zoobank-list.json';
 
file_put_contents($json_filename, json_encode($dataFeed, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
	



?>

