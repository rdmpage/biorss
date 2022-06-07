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
//$files = array('1804-6487.json');

//$files = array('2107-7207.json');

//$files = array('zoobank-list.json');
//$files = array('zoobank.json');

//$files = array('zs.json');

//$files = array('extras.json');

// Google Scholar 
//$files = array('"n. sp." OR "new species" OR "n. gen." OR "sp. nov ... - new results.json');
//$files = array('new  intitle_"new species" - new results.json');

//$files = array('zxxb.json');

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
