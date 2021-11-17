<?php

error_reporting(E_ALL);

require_once (dirname(__FILE__) . '/couchsimple.php');
require_once (dirname(__FILE__) . '/treemap.php');


//----------------------------------------------------------------------------------------
function default_display()
{
	echo "hi";
}

//----------------------------------------------------------------------------------------
// Get feed
function display_feed ($country, $path, $callback = '')
{
	global $config;
	global $couch;

	$key = array();

	$key[] = $country;
	$key[] = join("-", json_decode($path));

	$startkey = $key;
	$endkey = $key;
	$startkey[] = new stdclass;
	
	
		
	$url = '_design/key/_view/query?startkey=' . urlencode(json_encode($startkey))
		. '&endkey=' .  urlencode(json_encode($endkey))
		. '&descending=true'
		;
	
	$resp = $couch->send("GET", "/" . $config['couchdb_options']['database'] . "/" . $url);

	$obj = json_decode($resp);
	
	$dataFeed = new stdclass;
	$dataFeed->dataFeedElement = array();
	
	foreach ($obj->rows as $row)
	{
		// use id to avoid duplicates (why?)
		$dataFeed->dataFeedElement[$row->id] = $row->value;
	}
	
	// convert to format we want
	
	
	// output
	header("Content-type: text/plain");	
	if ($callback != '')
	{
		echo $callback . '(';
	}

	echo json_encode($dataFeed, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
	
	if ($callback != '')
	{
		echo ')';
	}	

}

//----------------------------------------------------------------------------------------
// Get treemap
function display_treemap ($path, $callback = '')
{
	global $config;
	global $couch;
	
	$key = json_decode($path);

	// Get children of this node
	$startkey = $key;
	$startkey[] = "A";
	$endkey = $key;
	$endkey[] = "zzz";
		
	$url = '_design/key/_view/classification?startkey=' . urlencode(json_encode($startkey))
		. '&endkey=' .  urlencode(json_encode($endkey))
		. '&group_level=' . (count($key) + 1);
	
	$resp = $couch->send("GET", "/" . $config['couchdb_options']['database'] . "/" . $url);

	$obj = json_decode($resp);
		
	// get node and children
	$items = array();
	
	foreach ($obj->rows as $row)
	{
		$depth = count($row->key);
		
		$item = new Item(
			log10($row->value + 1), 
			$row->key[$depth-1], 
			json_encode($row->key),
			($depth == 5)
			);
	
		array_push($items, $item);
	}
	
	$r = new Rectangle(0,0,280,280);

	// Compute the layout
	splitLayout($items, $r);	
	
	
	// output
	header("Content-type: text/plain");	
	if ($callback != '')
	{
		echo $callback . '(';
	}

	echo json_encode($items, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
	
	if ($callback != '')
	{
		echo ')';
	}	

}


//----------------------------------------------------------------------------------------
function main()
{

	$callback = '';
	$handled = false;
	
	
	// If no query parameters 
	if (count($_GET) == 0)
	{
		default_display();
		exit(0);
	}
	
	if (isset($_GET['callback']))
	{	
		$callback = $_GET['callback'];
	}
	
	// Get feed indexed by country and path, ordered by date in reverse order
	if (!$handled)
	{
		$country = '';
		$path = '';
		
		if (isset($_GET['country']))
		{	
			$country = $_GET['country'];
		}
		
		if (isset($_GET['path']))
		{	
			$path = $_GET['path'];
		}
		
		if ($country != "" && $path != "")
		{
			display_feed($country, $path, $callback);
			$handled = true;		
		}

	}

	if (!$handled)
	{
		$path = '';
		
		if (isset($_GET['path']))
		{	
			$path = $_GET['path'];
		}
		
		if ($path != "")
		{
			display_treemap($path, $callback);
			$handled = true;		
		}
		
	}	
	
	if (!$handled)
	{
		default_display();
	}	

}


main();


?>



