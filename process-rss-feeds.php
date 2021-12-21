<?php

// Process RSS feeds (i.e., XML)

require_once (dirname(__FILE__) . '/rss.php');
require_once (dirname(__FILE__) . '/process-feed.php');

$force = false;

$latest_dir = $config['cache'] . '/latest';

$files = scandir($latest_dir);

foreach ($files as $filename)
{
	// process RSS (XML) files
	if (preg_match('/\.xml$/', $filename))
	{	
		$xml = file_get_contents($latest_dir . '/' . $filename);

		$dataFeed = rss_to_internal($xml);
		
		process_feed($dataFeed);

	}
}

?>
