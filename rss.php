<?php

error_reporting(E_ALL);

require_once (dirname(__FILE__) . '/utils.php');

//----------------------------------------------------------------------------------------
// Parse RSS feed in RSS1, RSS2, or ATOM and return internal datastructure
function rss_to_internal($xml)
{
	$dom = new DOMDocument;
	$dom->loadXML($xml, LIBXML_NOCDATA); // Elsevier wraps text in <![CDATA[ ... ]]>
	$xpath = new DOMXPath($dom);

	// namespaces we are likely to encounter
	$xpath->registerNamespace('atom',  				'http://www.w3.org/2005/Atom');
	$xpath->registerNamespace('cc',    				'http://web.resource.org/cc/');
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
			foreach ($xpath->query('rss:link | link | atom:link[@rel="alternate"]/@href', $item) as $node)
			{
				$dataFeedElement->{'@id'} = $node->firstChild->nodeValue;
				$dataFeedElement->url = $node->firstChild->nodeValue;
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
		
				$dataFeedElement->description  = full_clean_text($dataFeedElement->description);
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
			foreach ($xpath->query('atom:updated', $item) as $node)
			{
				$dataFeedElement->datePublished = date(DATE_ISO8601, strtotime($node->firstChild->nodeValue));
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
			
			//----------------------------------------------------------------------------
			// tags
			
			// RSS2
			foreach ($xpath->query('category', $item) as $node)
			{
				$dataFeedElement->keywords[] = $node->firstChild->nodeValue;
			}
			
	
	
			//----------------------------------------------------------------------------
			// guid
			foreach ($xpath->query('guid', $item) as $node)
			{
				$dataFeedElement->mainEntity = $node->firstChild->nodeValue;
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
			
	
	
				/*
					   "@id": "https://species.wikimedia.org/wiki/Template:Ralph_et_al.,_2015",
						"@type": "WebPage",
						"mainEntity": {
							"@id": "https://doi.org/10.11646/zootaxa.4057.1.1",
							"@type": "CreativeWork",
							"author": [
								{
									"@id": "_:b3",
									"@type": "Person",
									"mainEntityOfPage": {
										"@id": "https://species.wikimedia.org/wiki/Taryn_M.C._Ralph"
									},
									"name": "Ralph, T.M.C."
								},
	
							   "identifier": {
								"@id": "_:b6",
								"@type": "PropertyValue",
								"propertyID": "doi",
								"value": "10.11646/zootaxa.4057.1.1"
							},

	
	
	
				*/
	
				// doi
				foreach ($xpath->query('prism:doi', $item) as $node)
				{
					$dataFeedElement->doi = $node->firstChild->nodeValue;
				}

			
				// bibliographic metadata(?)
				foreach ($xpath->query('dc:creator', $item) as $node)
				{
					$author = new stdclass;
					$author->name = $node->firstChild->nodeValue;
		
					$dataFeedElement->author[] = $author;
				}

				foreach ($xpath->query('prism:volume', $item) as $node)
				{
					$dataFeedElement->volumeNumber = $node->firstChild->nodeValue;
				}
				foreach ($xpath->query('prism:number', $item) as $node)
				{
					$dataFeedElement->issueNumber = $node->firstChild->nodeValue;
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
// test cases

if (1)
{
	


	$filename = 'examples/flickr.xml'; // atom
	$filename = 'examples/eol.xml'; // atom
	
	$filename = 'examples/oup.xml'; // rss2
	
	$filename = 'examples/aby.xml'; // rss 2 Chinese
	$filename = 'examples/ce.xml'; // rss 2 German
	
	//$filename = 'examples/elsevier.xml'; // rss 2 with <![CDATA[ ... ]]>
	
	//$filename = 'examples/tand.rdf'; // rss 1 (RDF)
	
	// taxa
	//$filename = 'examples/worms.xml'; // rss 2 
	
	
	//$filename = 'examples/oup.xml'; // rss2
	
	


	$xml = file_get_contents($filename);
	
	
	$dataFeed = rss_to_internal($xml);

	//echo json_encode($dataFeed, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

}

?>
