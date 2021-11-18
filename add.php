<?php

// Force add RSS feed (so we start from scratch)

require_once (dirname(__FILE__) . '/rss.php');
require_once (dirname(__FILE__) . '/datastore.php');

$force = true;

$filename = 'examples/ajh.xml';
$filename = 'examples/mpe.xml';
$filename = 'examples/taxon.xml';

$xml = file_get_contents($filename);

$dataFeed = rss_to_internal($xml);

$n = count($dataFeed->dataFeedElement);

for ($i = 0; $i < $n; $i++)
{
	print_r($dataFeed->dataFeedElement[$i]);	
	
	store($dataFeed->dataFeedElement[$i], $force);
}


?>
