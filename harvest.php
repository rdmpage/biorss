<?php

// Parse OPML list of feeds, fetch (conditional?) and save to disk

require_once(dirname(__FILE__) . '/config.inc.php');

//----------------------------------------------------------------------------------------
/*
 Checking whether a HTTP source has been modified.

 We use HTTP conditional GET to check whether source has been updated, see 
 http://fishbowl.pastiche.org/2002/10/21/http_conditional_get_for_rss_hackers .
 ETag is a double-quoted string sent by the HTTP server, e.g. "2f4511-8b92-44717fa6"
 (note the string includes the enclosing double quotes). Last Modified is date,
 written in the form Mon, 22 May 2006 09:08:54 GMT.
*/
function conditional_get($url, &$data)
{
	$content = '';
	
	$ETag = '';
	$LastModified = '';
	
	if (isset($data->{$url}))
	{
		if (isset($data->{$url}->etag))
		{
			$ETag = $data->{$url}->etag;
		}
		if (isset($data->{$url}->modified))
		{
			$LastModified = $data->{$url}->modified;
		}
	}
	
	// Construct conditional GET header
	$if_header = array(
		"Accept: */*",
		"Accept-Language: en-gb",
		"User-agent: Mozilla/5.0 (iPad; U; CPU OS 3_2_1 like Mac OS X; en-us) AppleWebKit/531.21.10 (KHTML, like Gecko) Mobile/7B405"
	);
	
	if ($LastModified != '')
	{
		array_push ($if_header, 'If-Modified-Since: ' . $LastModified);
	}
	
	// Only add this header if server returned an ETag value
	if ($ETag != '')
	{
		array_push ($if_header, 'If-None-Match: ' . $ETag);
	}
	
	if (0)
	{
		print_r($if_header);
	}
	 
	$ch = curl_init(); 
	
	curl_setopt($ch, CURLOPT_URL, $url);
	
	curl_setopt($ch, CURLOPT_HEADER, 		 1);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	
	// Cookies 
	curl_setopt($ch, CURLOPT_COOKIEJAR, sys_get_temp_dir() . '/cookies.txt');
	curl_setopt($ch, CURLOPT_COOKIEFILE, sys_get_temp_dir() . '/cookies.txt');	
	
	curl_setopt ($ch, CURLOPT_HTTPHEADER, $if_header); 	
			
	$curl_result = curl_exec ($ch); 
		
	if(curl_errno ($ch) != 0 )
	{
		// Problems with CURL
		//$result = curl_errno ($ch);
	}
	else
	{
		$info = curl_getinfo($ch);
				
		$header = substr($curl_result, 0, $info['header_size']);
		
		$http_code = $info['http_code'];
		
		if (0)
		{
			echo $header;
		}

		if ($http_code == 200)
		{
			// HTTP 200 means the feed exists and has been modified since we 
			// last visited (or this is the first time we've looked at it)
			// so we grab it, remembering to trim off the header. We store
			// details of the feed in our database.
			$content = substr ($curl_result, $info['header_size']);
			
			// Retrieve ETag and LastModified			
			$ETag 			= '';
			$LastModified 	= '';
			
			$rows = explode ("\n", $header);
			foreach ($rows as $row)
			{
				$parts = explode (":", $row, 2);
				if (count($parts) == 2)
				{
					if (preg_match("/ETag/", $parts[0]))
					{
						$ETag = trim($parts[1]);
					}
					
					if (preg_match("/Last-Modified/", $parts[0]))
					{
						$LastModified = trim($parts[1]);
					}
					
				}
			}
			
			// store header details
			if (data)
			{
				if (!isset($data->{$url}))
				{
					$data->{$url} = new stdclass;
				}
					
				if ($ETag != '')
				{
					$data->{$url}->etag = $ETag;
				}
				
				if ($LastModified != '')
				{
					$data->{$url}->modified = $LastModified;
				}					
				
			}
			
			// get feed itself
		}
	}
	return $content;
}

//----------------------------------------------------------------------------------------


// Where shall we store the feeds?
$today = date('Y-m-d', time());

$cache_dir = $config['cache'] . '/' . $today;

if (!file_exists($cache_dir))
{
	$oldumask = umask(0); 
	mkdir($cache_dir, 0777);
	umask($oldumask);
}	

$opml_filename = 'test.opml';

$feed_history_filename = 'feedstatus.json';

$json = file_get_contents($feed_history_filename);

$data = json_decode($json);

$xml = file_get_contents($opml_filename);

$dom = new DOMDocument;
$dom->loadXML($xml, LIBXML_NOCDATA); // Elsevier wraps text in <![CDATA[ ... ]]>
$xpath = new DOMXPath($dom);

foreach ($xpath->query('//outline/@xmlUrl') as $node)
{
	$url = $node->firstChild->nodeValue;
	
	echo $url . "\n";
	
	$rss = conditional_get($url, $data);
	
	if ($rss != '')
	{	
		$rss_filename = $cache_dir . '/' . md5($url) . '.xml';
		
		file_put_contents($rss_filename, $rss);
	}
}

file_put_contents($feed_history_filename, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));


//$url = 'https://api.ingentaconnect.com/content/aspt/sb/latest?format=rss';

?>

