<?php

// Process Google Scholar email(s)

error_reporting(E_ALL);
mb_internal_encoding("UTF-8");

require_once (dirname(__FILE__) . '/vendor/autoload.php');
require_once (dirname(__FILE__) . '/config.inc.php');
require_once (dirname(__FILE__) . '/utils.php');

if (1)
{
	require_once (dirname(__FILE__) . '/HtmlDomParser.php');
}
use Sunra\PhpSimple\HtmlDomParser;

$latest_dir = $config['cache'] . '/latest';
$files = scandir($latest_dir);

foreach ($files as $filename)
{
	// Only process email files (.eml) files
	if (preg_match('/\.eml$/', $filename))
	{
		
		$message = new stdclass;
		$message->body = array();

		$state = 0;

		$file_handle = fopen($latest_dir . '/' . $filename, "r");
		while (!feof($file_handle)) 
		{
			$line = fgets($file_handle);
	
			if ($state == 1)
			{
				$line = preg_replace('/=$/', '', $line);
				$message->body[] = $line;
			}
	
			if ($state == 0)
			{
				if (preg_match('/^Date:\s+(?<date>.*)/', $line, $m))
				{
					$message->date = $m['date'];
				}
				if (preg_match('/^Subject:\s+(?<subject>.*)/', $line, $m))
				{
					$message->subject = $m['subject'];
					$message->subject = preg_replace('/^new\s+/', '', $message->subject);
					$message->subject = preg_replace('/\s+-\s+new\s+results/', '', $message->subject);
				}
		
				$line = preg_replace('/=$/', '', $line);
				$message->body[] = $line;
			}
	
	
			if (preg_match('/^Content-Transfer-Encoding/', $line))
			{
				$state = 1;
			}
	

		}	

		print_r($message);
		
		$html = quoted_printable_decode(join("", $message->body));

		$dataFeed = new stdclass;
		$dataFeed->name = 'Google Scholar ' . $message->subject;
		$dataFeed->url = 'https://scholar.google.com/scholar?hl=en&as_sdt=0%2C5&q=' . urlencode($message->subject);
		$dataFeed->dataFeedElement = array();

		if ($html != '')
		{						
			$dom = HtmlDomParser::str_get_html($html);
	
			foreach ($dom->find('h3') as $h3)
			{
				$obj = new stdclass;
				$obj->mimetype = 'text/html'; // assume that link is to a web page by default
		
				$obj->datePublished = date(DATE_ISO8601, strtotime($message->date));
		
				$obj->item = new stdclass;
		
				// Convert date from RFC112 to ISO8601
				//$dateTime = date_create_from_format(DATE_RFC1123, $datestring);
				//$obj->isoDate = date_format($dateTime, DATE_ISO8601);
				
				// get title				
				$obj->name = html_entity_decode($h3->plaintext, ENT_QUOTES | ENT_HTML5, 'UTF-8');
				$obj->name = preg_replace('/\s\s+/u', ' ', $obj->name);
		
				// clean HTML/PDF
				if (preg_match('/^\[PDF\]\s+/', $obj->name))
				{
					$obj->name = preg_replace('/^\[PDF\]\s+/u', '', $obj->name);
					$obj->mimetype = 'application/pdf';
				}

				if (preg_match('/^\[HTML\]\s+/', $obj->name))
				{
					$obj->name = preg_replace('/^\[HTML\]\s+/u', '', $obj->name);
					$obj->mimetype = 'text/html';
				}	
		
				$obj->item->name = $obj->name;
				$obj->item->datePublished = $obj->datePublished;
		
				// get link to Google Scholar	
				foreach ($h3->find('a') as $a)
				{
					// echo $a->href . "\n";
		
					$parts = parse_url($a->href);
		
					// print_r($parts);
		
					if (isset($parts['query']))
					{
						parse_str(html_entity_decode($parts['query']), $query);
			
						// link
						$obj->url = $query['url'];
						
						// remove any stray encoded spaces in URL
						$obj->url = str_replace('%20', '', $obj->url);
						
						$obj->item->url = $obj->url;
				
						$doi = url_pattern_to_doi($obj->item->url);
						if ($doi != '')
						{
							$obj->item->doi = $doi;
						}
				
						if ($obj->mimetype == 'application/pdf')
						{
							$obj->item->pdf = $query['url'];
						}
				
						// Google Scholar cluster id
						$obj->id = 'https://scholar.google.com/scholar?cluster=' . $query['d'];
					}
				}
		
				unset($obj->mimetype);
		
				// get snippet of text 
				$sib = $h3->next_sibling();
				if ($sib)
				{
					$sib = $sib->next_sibling();
					if (isset($sib->class) && ($sib->class == "gse_alrt_sni"))
					{
						$obj->description = html_entity_decode($sib->plaintext, ENT_QUOTES | ENT_HTML5, 'UTF-8');
						$obj->description = preg_replace('/\R/u', ' ', $obj->description);
						$obj->description = preg_replace('/\s\s+/u', ' ', $obj->description);
					}
				}
				
				// clean up
				//unset($obj->mimetype);
				
				if (isset($obj->id) && isset($obj->name) && ($obj->name != ''))
				{		
					$dataFeed->dataFeedElement[] = $obj;	
				}
		
		
			}
		}

		print_r($dataFeed);		
		
		// store
		
		$json_filename = $latest_dir . '/' . basename($filename, '.eml') . '.json';
		 
		file_put_contents($json_filename, json_encode($dataFeed, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));

	}
}


?>
