<?php

// Force add RSS feed (so we start from scratch)

require_once (dirname(__FILE__) . '/rss.php');
require_once (dirname(__FILE__) . '/datastore.php');

$force = true;

$filename = 'examples/ajh.xml';
$filename = 'examples/mpe.xml';
$filename = 'examples/taxon.xml';

$filename = 'cache/2021-11-23/d08acaa8dc9b5e6f5bd8e6631cfd9e73.xml';

$xml = file_get_contents($filename);

$dataFeed = rss_to_internal($xml);

$n = count($dataFeed->dataFeedElement);

for ($i = 0; $i < $n; $i++)
{
	print_r($dataFeed->dataFeedElement[$i]);	
	
	store($dataFeed->dataFeedElement[$i], $force);
}


?>
