<?php

// Process internal feeds (e.g., native JSON format)
require_once (dirname(__FILE__) . '/process-feed.php');

$force = false;
//$force = true;

$latest_dir = $config['cache'] . '/latest';

$files = scandir($latest_dir);

// debugging
//$files = array('doaj.json');

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
