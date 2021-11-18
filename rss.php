<?php

error_reporting(E_ALL);

require_once (dirname(__FILE__) . '/utils.php');

//----------------------------------------------------------------------------------------
// Parse RSS feed in RSS1, RSS2, or ATOM and return internal datastructure in JSON-LD
function rss_to_internal($xml)
{
	$dom = new DOMDocument;
	$dom->loadXML($xml, LIBXML_NOCDATA); // Elsevier wraps text in <![CDATA[ ... ]]>
	$xpath = new DOMXPath($dom);

	// namespaces we are likely to encounter
	$xpath->registerNamespace('atom',  				'http://www.w3.org/2005/Atom');
	$xpath->registerNamespace('cc',    				'http://web.resource.org/cc/');
	$xpath->registerNamespace('content',    		'http://purl.org/rss/1.0/modules/content/');
	$xpath->registerNamespace('creativeCommons',    'http://cyber.law.harvard.edu/rss/creativeCommonsRssModule.html');
	$xpath->registerNamespace('dc',    				'http://purl.org/dc/elements/1.1/');
	$xpath->registerNamespace('geo',    			'http://www.w3.org/2003/01/geo/wgs84_pos#');
	$xpath->registerNamespace('georss',    			'http://www.georss.org/georss');
	$xpath->registerNamespace('prism', 				'http://prismstandard.org/namespaces/1.2/basic/');
	//$xpath->registerNamespace('prism', 			'http://purl.org/rss/1.0/modules/prism/');
	$xpath->registerNamespace('rdf',   				'http://www.w3.org/1999/02/22-rdf-syntax-ns#');
	$xpath->registerNamespace('rss',   				'http://purl.org/rss/1.0/');
	$xpath->registerNamespace('woe',   				'http://where.yahooapis.com/v1/schema.rng');

	// Model RSS as schema.org DataFeed

	$dataFeed = new stdclass;
	$dataFeed->{'@context'} = 'http://schema.org/';
	$dataFeed->{'@type'} = 'DataFeed';

	//------------------------------------------------------------------------------------
	// feed title
	foreach ($xpath->query('/rdf:RDF/rss:channel/rss:title | /rss/channel/title | /atom:feed/atom:title') as $node)
	{
		$dataFeed->name = $node->firstChild->nodeValue;	
		$dataFeed->name = full_clean_text($dataFeed->name);
	}

	//------------------------------------------------------------------------------------
	// feed link

	// feed link RSS2/ATOM
	foreach ($xpath->query('//atom:link[@rel="self"]/@href') as $node)
	{
		$dataFeed->url = $node->firstChild->nodeValue;
	}

	// feed link RSS2
	foreach ($xpath->query('//channel/link') as $node)
	{
		$dataFeed->url = $node->firstChild->nodeValue;
	}

	// feed link RSS1
	foreach ($xpath->query('/rdf:RDF/rss:channel/rss:link') as $node)
	{
		$dataFeed->url = $node->firstChild->nodeValue;
	}

	//------------------------------------------------------------------------------------
	// feed image

	// ATOM
	foreach ($xpath->query('//atom:icon') as $node)
	{
		$dataFeed->image = $node->firstChild->nodeValue;
	}

	// RSS2
	foreach ($xpath->query('//channel/image/url') as $node)
	{
		$dataFeed->image = $node->firstChild->nodeValue;
	}

	// RSS1
	foreach ($xpath->query('/rdf:RDF/rss:channel/rss:image/@rdf:resource') as $node)
	{
		$dataFeed->image = $node->firstChild->nodeValue;
	}

	foreach ($xpath->query('/rdf:RDF/rss:image/@rdf:about') as $node)
	{
		$dataFeed->image = $node->firstChild->nodeValue;
	}

	//------------------------------------------------------------------------------------
	// feed language 
	
	// ATOM
	
	// RSS2
	foreach ($xpath->query('//channel/language') as $node)
	{
		$dataFeed->inLanguage = $node->firstChild->nodeValue;
	}

	// RSS1
	foreach ($xpath->query('//rss:channel/dc:language') as $node)
	{
		$dataFeed->inLanguage = $node->firstChild->nodeValue;
	}

	//------------------------------------------------------------------------------------
	// feed date

	// ATOM
	foreach ($xpath->query('//atom:updated') as $node)
	{
		$dataFeed->dateModified = date(DATE_ISO8601, strtotime($node->firstChild->nodeValue));
	}

	// RSS2
	foreach ($xpath->query('//channel/lastBuildDate') as $node)
	{
		$dataFeed->dateModified = date(DATE_ISO8601, strtotime($node->firstChild->nodeValue));
	}

	// RSS2
	foreach ($xpath->query('//channel/pubDate') as $node)
	{
		$date_string = $node->firstChild->nodeValue;
	
		if (isset($dataFeed->inLanguage))
		{
			$date_string = translate_date($date_string, $dataFeed->inLanguage);
		}

		$dataFeed->dateModified = date(DATE_ISO8601, strtotime($date_string));
	}

	// RSS1
	foreach ($xpath->query('/rdf:RDF/rss:channel/dc:date') as $node)
	{
		$dataFeed->dateModified = date(DATE_ISO8601, strtotime($node->firstChild->nodeValue));
	}

	//----------------------------------------------------------------------------------------
	// items
	$dataFeed->dataFeedElement = array();

	if (1) // set to 0 if we are debugging just feed details
	{
		$itemCollection = $xpath->query ('//rss:item | //item | //atom:entry');
		foreach ($itemCollection as $item)
		{
			$dataFeedElement = new stdclass;
			$dataFeedElement->{'@type'} = 'DataFeedItem';
	
			// link
			foreach ($xpath->query('rss:link | link | atom:link[@rel="alternate" and @type="text/html"]/@href', $item) as $node)
			{
				$dataFeedElement->{'@id'} = $node->firstChild->nodeValue;
				$dataFeedElement->url = $node->firstChild->nodeValue;
			}
						
			// link ATOM PDF
			foreach ($xpath->query('atom:link[@rel="alternate" and @type="application/pdf"]/@href', $item) as $node)
			{
				$dataFeedElement->pdf = $node->firstChild->nodeValue;
				
				if (!isset($dataFeedElement->{'@id'}))
				{
					$dataFeedElement->{'@id'} = $dataFeedElement->pdf ;
				}
			}	
			
			// id scholar.google.com		
			foreach ($xpath->query('atom:id', $item) as $node)
			{
				if (preg_match('/scholar.google.com/', $node->firstChild->nodeValue))
				{
					$dataFeedElement->{'@id'} = $node->firstChild->nodeValue;
				}
			}
	
			// name
			foreach ($xpath->query('rss:title | title | atom:title', $item) as $node)
			{
				$dataFeedElement->name = $node->firstChild->nodeValue;
		
				$dataFeedElement->name  = full_clean_text($dataFeedElement->name);
			}
	
			// description
			foreach ($xpath->query('description | rss:description', $item) as $node)
			{
				$dataFeedElement->description = $node->firstChild->nodeValue;
				
				// Pensoft may have useful details in the description, so extract those
				// and clean text
				if (preg_match_all('/<p>(?<p>.*)<\/p>/Uu', $dataFeedElement->description, $m))
				{
					foreach ($m['p'] as $paragraph)
					{
						if (preg_match('/Abstract:\s+(?<text>.*)/', $paragraph, $mm))
						{
							$dataFeedElement->description = $mm['text'];
						}
					}
				}
		
				$dataFeedElement->description  = full_clean_text($dataFeedElement->description);
				
				// Elsevier (sciencedirect.com) has publication dates for articles in description
				if (isset($dataFeed->url) && preg_match('/sciencedirect.com/', $dataFeed->url))
				{
					if (preg_match('/Available online\s+(?<date>\d+\s+[A-Z]\w+\s+[0-9]{4})/', $dataFeedElement->description, $m))
					{
						$dataFeedElement->datePublished = date(DATE_ISO8601, strtotime($m['date']));
					}
					else
					{
						// use feed date
						$dataFeedElement->datePublished = $dataFeed->dateModified;
					}
				}
			}

			// summary / content
			foreach ($xpath->query('atom:summary[@type="html"] | atom:content[@type="html"]', $item) as $node)
			{
				$dataFeedElement->description = $node->firstChild->nodeValue;
				
				$dataFeedElement->description  = full_clean_text($dataFeedElement->description);
			}
	
		
			//----------------------------------------------------------------------------
			// date
	
			// ATOM
			foreach ($xpath->query('atom:published', $item) as $node)
			{
				$dataFeedElement->datePublished = date(DATE_ISO8601, strtotime($node->firstChild->nodeValue));
			}
			foreach ($xpath->query('atom:updated', $item) as $node)
			{
				$dataFeedElement->dateModified = date(DATE_ISO8601, strtotime($node->firstChild->nodeValue));
			}
			
	
			// RSS2
			foreach ($xpath->query('pubDate', $item) as $node)
			{
				$date_string = $node->firstChild->nodeValue;
		
				if (isset($dataFeed->inLanguage))
				{
					$date_string = translate_date($date_string, $dataFeed->inLanguage);
				}
	
				$dataFeedElement->datePublished = date(DATE_ISO8601, strtotime($date_string));
			}
	
			// RSS 1
			foreach ($xpath->query('dc:date', $item) as $node)
			{
				$dataFeedElement->datePublished = date(DATE_ISO8601, strtotime($node->firstChild->nodeValue));
			}
			
			//----------------------------------------------------------------------------
			// tags
			
			// RSS2
			foreach ($xpath->query('category', $item) as $node)
			{
				$dataFeedElement->keywords[] = $node->firstChild->nodeValue;
			}
			
	
	
			//----------------------------------------------------------------------------
			// guid
			// Want some way to represent the actual thing that is the subject of this RSS item
			foreach ($xpath->query('guid', $item) as $node)
			{
				// $dataFeedElement->mainEntity = $node->firstChild->nodeValue;
			}
	
			//----------------------------------------------------------------------------
			// license

			// cc
			foreach ($xpath->query('cc:license/@rdf:resource', $item) as $node)
			{
				$dataFeedElement->license  = $node->firstChild->nodeValue;
			}

			foreach ($xpath->query('creativeCommons:license', $item) as $node)
			{
				$dataFeedElement->license  = $node->firstChild->nodeValue;
			}
	
			//----------------------------------------------------------------------------
			// geo
							
			// make list of points an array so that we can support multiple locations for an item
			foreach ($xpath->query('georss:point', $item) as $node)
			{
				$place = new stdclass;
				
				$place->{'@type'} = 'Place';
		
				// coordinates
				$place->geo = new stdclass;
		
				$parts = explode(' ', trim($node->firstChild->nodeValue));
				$place->geo->latitude 	= (float)$parts[0];
				$place->geo->longitude 	= (float)$parts[1];
		
				// flickr
				foreach ($xpath->query('woe:woeid', $item) as $node)
				{
					$place->{'@id'} = 'https://www.flickr.com/places/info/' . $node->firstChild->nodeValue;		
				}		
				
				$dataFeedElement->contentLocation[]  = $place;
			}
	
			//----------------------------------------------------------------------------
			// metadata about the thing...
	
			if (1) // 0 if we don't want these details
			{
	
				// doi
				foreach ($xpath->query('prism:doi', $item) as $node)
				{
					$dataFeedElement->doi = $node->firstChild->nodeValue;
				}

				foreach ($xpath->query('dc:identifier', $item) as $node)
				{
					if (preg_match('/^doi:(?<doi>.*)/', $node->firstChild->nodeValue, $m))
					{
						$dataFeedElement->doi = $m['doi'];
					}
					if (preg_match('/^pmid:(?<pmid>\d+)/', $node->firstChild->nodeValue, $m))
					{
						$dataFeedElement->pmid = $m['pmid'];
					}
				}
			
				// bibliographic metadata(?)
				foreach ($xpath->query('dc:creator', $item) as $node)
				{
					if (isset($node->firstChild->nodeValue))
					{
						// Some publishers (e.g., Wiley) store multiple values here
						$parts = preg_split('/,\s*\R/u', $node->firstChild->nodeValue);
						foreach ($parts as $part)
						{					
							$author = new stdclass;
							$author->name = trim($part);
		
							$dataFeedElement->author[] = $author;
						}
					}
				}

				foreach ($xpath->query('prism:volume', $item) as $node)
				{
					// some feeds have empty tags
					if (isset($node->firstChild->nodeValue))
					{
						$dataFeedElement->volumeNumber = $node->firstChild->nodeValue;
					}
				}
				
				foreach ($xpath->query('prism:number', $item) as $node)
				{
					// some feeds have empty tags
					if (isset($node->firstChild->nodeValue))
					{
						$dataFeedElement->issueNumber = $node->firstChild->nodeValue;
					}
				}
				
				foreach ($xpath->query('prism:startingPage', $item) as $node)
				{
					$dataFeedElement->pageStart = $node->firstChild->nodeValue;
				}
				
				foreach ($xpath->query('prism:endingPage', $item) as $node)
				{
					$dataFeedElement->pageEnd = $node->firstChild->nodeValue;
				}
	
			}
	
			// Add item to feed
			$dataFeed->dataFeedElement[] = $dataFeedElement;
		}
	}
	
	return $dataFeed;

}

//----------------------------------------------------------------------------------------
function rss_content($source, $feed, $target, $tagname = 'content')
{
	if (isset($source->description))
	{
		$description_content = '';
		
		if (isset($source->image))
		{
			$description_content = '<p>' . '<img src="' . $source->image . '" width="240"></p>';
			$description_content .= '<p>' . $source->description . '</p>';
		}
		else
		{
			$description_content = '<p>' . $source->description . '</p>';
		}				
		
		if (isset($source->url))
		{
			$host = parse_url($source->url, PHP_URL_HOST);
			$description_content .= '<p><a href="' .$source->url . '">' . $host . '</a></p>';
		}
	
		$description = $target->appendChild($feed->createElement($tagname));
		
		if ($tagname == 'content')
		{
			$description->setAttribute('type', 'html');	
		}		
		$description->appendChild($feed->createTextNode($description_content));
	}
}

//----------------------------------------------------------------------------------------
function rss_geo($dataFeedElement, $feed, $item)
{
	if (isset($dataFeedElement->contentLocation))
	{
		foreach ($dataFeedElement->contentLocation as $place)
		{
			$georss = $item->appendChild($feed->createElement('georss:point'));
			$georss->appendChild($feed->createTextNode($place->geo->latitude . ' ' .  $place->geo->longitude)); 
		}
	
	}
}

//----------------------------------------------------------------------------------------
function internal_to_rss($dataFeed, $format = 'atom')
{
	$feed = new DomDocument('1.0', 'UTF-8');
	$feed->formatOutput = true;

	switch ($format)
	{
		case 'atom':
			$rss = $feed->createElement('feed');
			$rss->setAttribute('xmlns', 'http://www.w3.org/2005/Atom');
			$rss->setAttribute('xmlns:geo', 'http://www.w3.org/2003/01/geo/wgs84_pos#');
			$rss->setAttribute('xmlns:georss', 'http://www.georss.org/georss');
			$rss = $feed->appendChild($rss);
		
			// feed
		
			// title
			$title = $feed->createElement('title');
			$title = $rss->appendChild($title);
			$value = $feed->createTextNode($dataFeed->name);
			$value = $title->appendChild($value);
		
			// link
			$link = $feed->createElement('link');
			$link->setAttribute('href', $dataFeed->url);
			$link = $rss->appendChild($link);
		
			$link = $feed->createElement('link');
			$link->setAttribute('rel', 'self');
			$link->setAttribute('type', 'application/atom+xml');
			$link->setAttribute('href', $dataFeed->url);
			$link = $rss->appendChild($link);
				
			// updated
			$updated = $feed->createElement('updated');
			$updated = $rss->appendChild($updated);
			$value = $feed->createTextNode(date(DATE_ATOM));
			$value = $updated->appendChild($value);
		
			// id
			$id = $feed->createElement('id');
			$id = $rss->appendChild($id);
			$id->appendChild($feed->createTextNode($dataFeed->url));
		
			// items
			foreach ($dataFeed->dataFeedElement as $dataFeedElement)
			{
				$item = $rss->appendChild($feed->createElement('entry'));
				
				// title
				if (isset($dataFeedElement->name))
				{
					$title = $item->appendChild($feed->createElement('title'));
					$title->appendChild($feed->createTextNode($dataFeedElement->name));
				}
				
				rss_content($dataFeedElement, $feed, $item);
				
				// id
				if (isset($dataFeedElement->{'@id'}))
				{
					$id = $item->appendChild($feed->createElement('id'));
					$id->appendChild($feed->createTextNode($dataFeedElement->{'@id'}));
				}
											
				// link
				if (isset($dataFeedElement->url))
				{
					$link = $item->appendChild($feed->createElement('link'));
					$link->setAttribute('rel', 'alternate');
					$link->setAttribute('type', 'text/html');
					$link->setAttribute('href', $dataFeedElement->url);					
				}
				
				if (isset($dataFeedElement->pdf))
				{
					$link = $item->appendChild($feed->createElement('link'));
					$link->setAttribute('rel', 'alternate');
					$link->setAttribute('type', 'application/pdf');
					$link->setAttribute('href', $dataFeedElement->pdf);					
				}
								
				// published
				if (isset($dataFeedElement->datePublished))
				{
					$published = $item->appendChild($feed->createElement('published'));
					$published->appendChild($feed->createTextNode(date(DATE_ATOM, strtotime($dataFeedElement->datePublished))));
				}
				
				// updated
				if (isset($dataFeedElement->dateModified))
				{
					$updated = $item->appendChild($feed->createElement('updated'));
					$updated->appendChild($feed->createTextNode(date(DATE_ATOM, strtotime($dataFeedElement->dateModified))));
				}
				else
				{
					// ATOM expects updated so use datePublished
					if (isset($dataFeedElement->datePublished))
					{
						$updated = $item->appendChild($feed->createElement('updated'));
						$updated->appendChild($feed->createTextNode(date(DATE_ATOM, strtotime($dataFeedElement->datePublished))));
					}
					
				}
				
				
				// geo
				rss_geo($dataFeedElement, $feed, $item);
			
			}
		
			break;
			
		case 'rss2':
			$rss = $feed->createElement('rss');
			$rss->setAttribute('version', '2.0');
			$rss->setAttribute('xmlns:atom', 'http://www.w3.org/2005/Atom');
			$rss->setAttribute('xmlns:georss', 'http://www.georss.org/georss');
			$rss = $feed->appendChild($rss);

			// channel
			$channel = $feed->createElement('channel');
			$channel = $rss->appendChild($channel);
		
			// title
			$title = $channel->appendChild($feed->createElement('title'));
			$title->appendChild($feed->createTextNode($dataFeed->name));
			
			// description
			rss_content($dataFeed, $feed, $channel, 'description');
			
			// link
			$link = $channel->appendChild($feed->createElement('link'));
			$link->appendChild($feed->createTextNode($dataFeed->url));
			
			$link = $feed->createElement('atom:link');
			$link->setAttribute('rel', 'self');
			$link->setAttribute('type', 'application/atom+xml');
			$link->setAttribute('href', $dataFeed->url);
			$link = $channel->appendChild($link);
			
			foreach ($dataFeed->dataFeedElement as $dataFeedElement)
			{
				$item = $channel->appendChild($feed->createElement('item'));
				
				// title
				if (isset($dataFeedElement->name))
				{
					$title = $item->appendChild($feed->createElement('title'));
					$title->appendChild($feed->createTextNode($dataFeedElement->name));
				}
				
				// description
				if (isset($dataFeedElement->description))
				{
					$description_content = '';
					
					if (isset($dataFeedElement->image))
					{
						$description_content = '<p>' . '<img src="' . $dataFeedElement->image . '" width="240"></p>';
						$description_content .= '<p>' . $dataFeedElement->description . '</p>';
						$description_content .= '<p>' . $dataFeedElement->url . '</p>';
						
					}
					else
					{
						$description_content = $dataFeedElement->description;
					}				
				
					$description = $item->appendChild($feed->createElement('description'));
					$description->appendChild($feed->createTextNode($description_content));
				}
				
				// link
				if (isset($dataFeedElement->url))
				{
					$link = $item->appendChild($feed->createElement('link'));
					$link->appendChild($feed->createTextNode($dataFeedElement->url));
				}
				
				// pubDate
				if (isset($dataFeedElement->datePublished))
				{
					$pubDate = $item->appendChild($feed->createElement('pubDate'));
					$pubDate->appendChild($feed->createTextNode(date(DATE_RSS, strtotime($dataFeedElement->datePublished))));
				}
				
				// guid
				if (isset($dataFeedElement->doi))
				{
					$guid = $item->appendChild($feed->createElement('guid'));
					$guid->setAttribute('isPermaLink', 'true');
					$guid->appendChild($feed->createTextNode('https://doi.org/' . strtolower($dataFeedElement->doi)));
				}
				else
				{
					if (isset($dataFeedElement->url))
					{
						$guid = $item->appendChild($feed->createElement('guid'));
						$guid->setAttribute('href', $dataFeedElement->url);					
					}
				}
				
				// geo
				rss_geo($dataFeedElement, $feed, $item);
				
			}
						
			break;
			
		
		case 'rss1':
			$rss = $feed->createElement('rdf:RDF');
			$rss->setAttribute('xmlns', 'http://purl.org/rss/1.0/');
			$rss->setAttribute('xmlns:rdf', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#');
			$rss = $feed->appendChild($rss);

			// channel
			$channel = $feed->createElement('channel');
			$channel->setAttribute('rdf:about', $dataFeed->url);
			$channel = $rss->appendChild($channel);
		
			// title
			$title = $channel->appendChild($feed->createElement('title'));
			$title->appendChild($feed->createTextNode($dataFeed->name));

			// link
			$link = $channel->appendChild($feed->createElement('link'));
			$link->appendChild($feed->createTextNode($dataFeed->url));

			// description
			$description = $channel->appendChild($feed->createElement('description'));
			$description->appendChild($feed->createTextNode($dataFeed->name));

			// items

			$items = $channel->appendChild($feed->createElement('items'));
			$seq = $items->appendChild($feed->createElement('rdf:Seq'));
			
			foreach ($dataFeed->dataFeedElement as $dataFeedElement)
			{
				$li = $seq->appendChild($feed->createElement('rdf:li'));
				$li->setAttribute('rdf:resource', $dataFeedElement->url);
			}			
			
			foreach ($dataFeed->dataFeedElement as $dataFeedElement)
			{
				$item = $rss->appendChild($feed->createElement('item'));
				$item->setAttribute('rdf:about', $dataFeedElement->url);
				
				// title
				if (isset($dataFeedElement->name))
				{
					$title = $item->appendChild($feed->createElement('title'));
					$title->appendChild($feed->createTextNode($dataFeedElement->name));
				}
				
				// link
				if (isset($dataFeedElement->url))
				{
					$link = $item->appendChild($feed->createElement('link'));
					$link->appendChild($feed->createTextNode($dataFeedElement->url));
				}
				
				// could add more RDF here so we could feed a triple store
				
			}
			
	
			break;
		
		default:
			break;
	}

	return $feed->saveXML();

}



//----------------------------------------------------------------------------------------
// test cases

if (0)
{
	


	$filename = 'examples/flickr.xml'; // atom
	$filename = 'examples/eol.xml'; // atom
	
	$filename = 'examples/oup.xml'; // rss2
	
	$filename = 'examples/aby.xml'; // rss 2 Chinese
	//$filename = 'examples/ce.xml'; // rss 2 German
	
	//$filename = 'examples/elsevier.xml'; // rss 2 with <![CDATA[ ... ]]>
	
	//$filename = 'examples/tand.rdf'; // rss 1 (RDF)
	
	// taxa
	//$filename = 'examples/worms.xml'; // rss 2 
	
	
	//$filename = 'examples/oup.xml'; // rss2
	//$filename = 'examples/phytokeys.xml'; // rss2
	
	$filename = 'examples/googlescholar.xml'; // rss 2 

	//$filename = 'examples/native-pubmed.xml'; // rss 2 
	//$filename = 'examples/zoobank.xml'; // rss 2 
	
	$filename = 'examples/zookeys.xml'; 


	$xml = file_get_contents($filename);
	
	
	$dataFeed = rss_to_internal($xml);

	echo json_encode($dataFeed, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
	
	//$rss = internal_to_rss($dataFeed);
	
	//echo $rss;

}

?>
