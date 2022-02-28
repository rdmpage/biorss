<?php

// Process RSS feeds (i.e., XML)

require_once (dirname(__FILE__) . '/rss.php');
require_once (dirname(__FILE__) . '/process-feed.php');

$force = false;
//$force = true;

$latest_dir = $config['cache'] . '/latest';

$files = scandir($latest_dir);

/*
$files = array('publish.csiro.au-RSS_Feed-CSIRO_Publishing_Recent_IS.xml');
$files = array('rss.sciencedirect.com-publication-science-13835769.xml');
$files = array('africaninvertebrates.pensoft.net-rss.php.xml');
$files = array('onlinelibrary.wiley.com-feed-1522239xb-most-recent.xml');
$files = array('vertebrate-zoology.arphahub.com-rss.xml');
*/

//$files=array('jstage.jst.go.jp-AF05S010NewRssDld-btnaction=JT0041-sryCd=asjaa-rssLang=en.xml');

//$files = array('thebhs.org-publications-the-herpetological-journal.xml');

//$files = array('rostaniha.areeo.ac.ir-ju.rss.xml');

//$files = array('li01.tci-thaijo.org-index.php-ThaiForestBulletin-gateway-plugin-WebFeedGatewayPlugin-rss.xml');

foreach ($files as $filename)
{
	// process RSS (XML) files
	if (preg_match('/\.xml$/', $filename))
	{	
		$xml = file_get_contents($latest_dir . '/' . $filename);

		$dataFeed = rss_to_internal($xml);	
		
		print_r($dataFeed);
		
		//exit();
		
		process_feed($dataFeed, $force);

	}
}

?>
