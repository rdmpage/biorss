<?php

// Process internal feeds (e.g., native JSON format)
require_once (dirname(__FILE__) . '/process-feed.php');

$force = false;
//$force = true;

$latest_dir = $config['cache'] . '/latest';

$files = scandir($latest_dir);

// debugging
//$files = array('ipni.json');
//$files = array('0003-3847.json');
//$files = array('2657-5000.json');
//$files = array('0035-418X.json');

$files = array('1803-6465.json');

//$files = array('zoobank-list.json');

foreach ($files as $filename)
{
	// Process JSON files
	if (preg_match('/\.json$/', $filename))
	{	
		$json = file_get_contents($latest_dir . '/' . $filename);

		$dataFeed = json_decode($json);

		process_feed($dataFeed, $force);

	}
}

?>
