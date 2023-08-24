<?php

error_reporting(E_ALL);

require_once (dirname(__FILE__) . '/couchsimple.php');
require_once (dirname(__FILE__) . '/rss.php');
require_once (dirname(__FILE__) . '/treemap.php');


//----------------------------------------------------------------------------------------
function default_display()
{
	echo "hi";
}

//----------------------------------------------------------------------------------------
// Get feed
function display_feed ($country, $path, $format= 'json', $callback = '')
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
		. '&limit=100'
		;
		
	if ($config['stale'])
	{
		$url .= '&stale=ok';
	}			
	
	$resp = $couch->send("GET", "/" . $config['couchdb_options']['database'] . "/" . $url);

	$obj = json_decode($resp);
	
	$dataFeed = new stdclass;
	$dataFeed->name = "BioRSS";
	$dataFeed->description = "BioRSS";
	
	$parameters = array(
		'country' 	=> $country,
		'path' 		=> $path
	);
	
	$dataFeed->url = $config['web_server'] . $config['web_root'] . 'feed/' . base64_encode(http_build_query($parameters));

	$dataFeed->url = $config['web_server'] . $config['web_root'] . 'api.php?feed=' . base64_encode(http_build_query($parameters));
	$dataFeed->parameters = base64_encode(http_build_query($parameters));
	
	$dataFeed->query = urldecode($url);

	$dataFeed->dataFeedElement = array();
	
	foreach ($obj->rows as $row)
	{
		// use id to avoid duplicates (why?)
		$dataFeed->dataFeedElement[$row->id] = $row->value;
	}
	
	switch ($format)
	{
		case 'rss':
			header("Content-type: application/xml");	
			$xml = internal_to_rss($dataFeed, 'rss2');
			
			// set a bunch of headers...
			echo $xml;
		
			break;
	
	
		case 'json':
		default:
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
			break;	
	}

}

//----------------------------------------------------------------------------------------
// Get treemap
function display_treemap ($path, $callback = '')
{
	global $config;
	global $couch;
	
	$key = json_decode($path);
	
	$level = count($key);

	// Get children of this node (and their children)
	$startkey = $key;
	//$startkey[] = "A";
	$endkey = $key;
	$endkey[] = "zzz";
		
	$url = '_design/key/_view/classification?startkey=' . urlencode(json_encode($startkey))
		. '&endkey=' .  urlencode(json_encode($endkey))
		. '&group_level=' . ($level + 2);
		
	if ($config['stale'])
	{
		$url .= '&stale=ok';
	}			
	
	$resp = $couch->send("GET", "/" . $config['couchdb_options']['database'] . "/" . $url);

	$obj = json_decode($resp);
	
	// pre_process
	$nodes = array();
	foreach ($obj->rows as $row)
	{
		// don't include key as an item
		if (count($row->key) > $level)
		{
	
			$node_array = array_slice($row->key, 0, $level + 1);
			$node_key = join("-", $node_array);
		
			if (!isset($nodes[$node_key]))
			{
				$nodes[$node_key] = new stdclass;
				$nodes[$node_key]->count = 0;
			
				if (count($row->key) == $level)
				{
					$nodes[$node_key]->label = $row->key[$level - 1];
				}
				else
				{
					$nodes[$node_key]->label = $row->key[$level];
				}
				$nodes[$node_key]->key = $node_array;
				$nodes[$node_key]->children = 1;
			}
		
			$nodes[$node_key]->count += $row->value;
		
			if (count($row->key) > $level + 1)
			{
				$nodes[$node_key]->children++;
			}

		}
	}	
	

	
	/*
	echo '<pre>';
	print_r($nodes);
	echo '</pre>';
	*/
	
	// get node and children
	$items = array();
	
	/*
	
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
	*/
	
	foreach ($nodes as $node)
	{
		
		$item = new Item(
			log10($node->count + 1), 
			$node->label, 
			json_encode($node->key),
			($node->children == 1)
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
		$country	 = '';
		$path 		= '';
		$format 	= 'json';
		
		if (isset($_GET['country']))
		{	
			$country = $_GET['country'];
		}
		
		if (isset($_GET['path']))
		{	
			$path = $_GET['path'];
		}
		
		if (isset($_GET['format']))
		{	
			$format = $_GET['format'];
		}
				
		if ($country != "" && $path != "")
		{
			display_feed($country, $path, $format, $callback);
			$handled = true;		
		}

	}
	
	if (!$handled)
	{
		$feed 		= '';
				
		if (isset($_GET['feed']))
		{	
			$feed = $_GET['feed'];
		}
		
		if ($feed != "")
		{
			$callback 	= ''; // don't use this as we serve XML
			$format 	= 'rss';
		
			$parameters = array();
			parse_str(base64_decode($feed), $parameters);
			
			display_feed($parameters['country'], $parameters['path'], $format, $callback);
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



