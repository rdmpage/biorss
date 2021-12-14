<?php

error_reporting(E_ALL);

require_once (dirname(__FILE__) . '/couchsimple.php');

//----------------------------------------------------------------------------------------
// Search
function display_search ($query, $callback = '')
{
	
	
	global $config;
	global $couch;
	
	$stopWords = ["about", "after", "all", "also", "am", "an", "and", "another", "any", "are", "as", "at", "be", "because", "been", "before", "being", "between", "both", "but", "by", "came", "can", "come", "could", "did", "do", "each", "for", "from", "get", "got", "has", "had", "he", "have", "her", "here", "him", "himself", "his", "how", "if", "in", "into", "is", "it", "like", "make", "many", "me", "might", "more", "most", "much", "must", "my", "never", "now", "of", "on", "only", "or", "other", "our", "out", "over", "said", "same", "see", "should", "since", "some", "still", "such", "take", "than", "that", "the", "their", "them", "then", "there", "these", "they", "this", "those", "through", "to", "too", "under", "up", "very", "was", "way", "we", "well", "were", "what", "where", "which", "while", "who", "with", "would", "you", "your", "a", "i"];

	
	$query = strtolower($query);
	$query = preg_replace('/[&\—\–\-\‐“”…@!"\',.:;?\(\)\[\]]/u', ' ', $query);
	
	$terms = preg_split('/\s+/', $query);
	
	$terms = array_diff($terms, $stopWords);
	
	// print_r($terms);
	
	$t = array();
	
	$docs = array();
	$names = array();
	
	foreach ($terms as $term)
	{
		$key = '"' . $term . '"';
	
		$url = '_design/key/_view/search?key=' . urlencode($key)
			. '&reduce=false'
			. '&include_docs=true'
			;
	
		$resp = $couch->send("GET", "/" . $config['couchdb_options']['database'] . "/" . $url);
		
		$obj = json_decode($resp);
		
		//print_r($obj);
		
		// Get total score for this term
		$t[$term] = 0;		
		foreach ($obj->rows as $row)
		{
			$t[$term] += $row->value;
		}
		
		$t[$term] = $t[$term] / $obj->total_rows;
		$t[$term] = 1.5 - $t[$term];
		
		foreach ($obj->rows as $row)
		{
			if (!isset($docs[$row->id]))
			{
				$docs[$row->id] = new stdclass;
				$docs[$row->id]->score = 0;
				$docs[$row->id]->terms = array();
				$docs[$row->id]->name = $row->doc->message->name;
			}
			$docs[$row->id]->score += $t[$term];	
			$docs[$row->id]->terms[] = $term;		
		}
	}
	
	//print_r($t);
	
	$scores = array();
	foreach ($docs as $k => $v)
	{
		$scores[$k] = $v->score;
	}
	
	arsort($scores, SORT_NUMERIC); 
	
	//print_r($docs);
	
	//print_r($names);
	
	$k = array_keys($scores);
	
	$limit = min(count($k), 10);
	
	for ($i = 0; $i < $limit; $i++)
	{
		//echo $docs[$k[$i]] . ' ' .  $names[$k[$i]] . "\n";
		
		//print_r($docs[$k[$i]]);
		
		echo $docs[$k[$i]]->score . ' ' . $docs[$k[$i]]->name . "\n";
	}
	//print_r($t);
	
	
	/*
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
	$dataFeed->name = "BioRSS";
	$dataFeed->description = "BioRSS";
	
	$parameters = array(
		'country' 	=> $country,
		'path' 		=> $path
	);
	
	$dataFeed->url = $config['web_server'] . $config['web_root'] . 'feed/' . base64_encode(http_build_query($parameters));

	$dataFeed->url = $config['web_server'] . $config['web_root'] . 'api.php?feed=' . base64_encode(http_build_query($parameters));
	$dataFeed->parameters = base64_encode(http_build_query($parameters));

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

	*/
}


//display_search('Gesneriaceae new species from limestone areas of northern Vietnam');
//display_search('Enderlein, 1905 (Neuroptera');
//display_search('dna barcoding barcodes sequencing');

//display_search('Lamb, 1914');

//display_search('novel species coi');
display_search('Chrysomelidae');

?>

