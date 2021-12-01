<?php

// Do something on a feed element

error_reporting(E_ALL);

require_once (dirname(__FILE__) . '/vendor/autoload.php');
require_once (dirname(__FILE__) . '/globalnames-graphql.php');
require_once (dirname(__FILE__) . '/utils.php');


use Sunra\PhpSimple\HtmlDomParser;

//----------------------------------------------------------------------------------------
function get($url, $accept = "text/html")
{
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	
	// Cookies 
	curl_setopt($ch, CURLOPT_COOKIEJAR, sys_get_temp_dir() . '/cookies.txt');
	curl_setopt($ch, CURLOPT_COOKIEFILE, sys_get_temp_dir() . '/cookies.txt');	
	
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
		"Accept: " . $accept,
		"Accept-Language: en-gb",
		"User-agent: Mozilla/5.0 (iPad; U; CPU OS 3_2_1 like Mac OS X; en-us) AppleWebKit/531.21.10 (KHTML, like Gecko) Mobile/7B405" 	
		));
	
	$response = curl_exec($ch);
	
	
	if($response == FALSE) 
	{
		$errorText = curl_error($ch);
		curl_close($ch);
		//die($errorText);
		return "";
	}
	
	$info = curl_getinfo($ch);
	$http_code = $info['http_code'];
	
	//print_r($info);
		
	curl_close($ch);
	
	return $response;
}


//----------------------------------------------------------------------------------------
// post
function post($url, $data = '', $content_type = '')
{
	
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data);  
	
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	
	if ($content_type != '')
	{
		curl_setopt($ch, CURLOPT_HTTPHEADER, 
			array(
				"Content-type: " . $content_type
				)
			);
	}	
	
	$response = curl_exec($ch);
	if($response == FALSE) 
	{
		$errorText = curl_error($ch);
		curl_close($ch);
		die($errorText);
	}
	
	$info = curl_getinfo($ch);
	$http_code = $info['http_code'];
		
	curl_close($ch);
	
	return $response;
}

//----------------------------------------------------------------------------------------
// Get doc from POST
function get_doc($debug = false)
{

	$doc = null;

	if (!$debug)
	{
		// Get FROM POST
		$doc = json_decode(file_get_contents('php://input'));
	}
	else
	{
		// Create an example document
		$json = '{
			"@type": "DataFeedItem",
			"@id": "https://mapress.com/zt/article/view/zootaxa.5061.2.11",
			"url": "https://mapress.com/zt/article/view/zootaxa.5061.2.11",
			"name": "Catalogue of the genus Cereopsius Pascoe 1857 (Coleoptera: Cerambycidae: Lamiinae) in the Philippines with description of a new species from Mindanao",
			"description": "The catalogue of the genus Cereopsius Pascoe 1857 fauna of the Philippines is provided, with description of a new species, C. erasmus sp. nov. from Mindanao Island. Additional taxonomic and faunistic notes on the other Philippine species are added. &nbsp;",
			"doi": "10.11646/zootaxa.5061.2.11",
			"author": [
				{
					"name": "MILTON NORMAN MEDINA"
				},
				{
					"name": "LESLAE KAY MANTILLA"
				},
				{
					"name": "ANALYN CABRAS"
				},
				{
					"name": "FRANCESCO VITALI"
				}
			],
			"volumeNumber": "5061",
			"issueNumber": "2",
			"pageStart": "383",
			"pageEnd": "391"
		}';
		
		$json = '{
			"@type": "DataFeedItem",
			"@id": "https://mapress.com/zt/article/view/zootaxa.5061.2.11",
			"url": "https://mapress.com/zt/article/view/zootaxa.5061.2.11",
			"name": "Catalogue of the genus Cereopsius Pascoe 1857 (Coleoptera: Cerambycidae: Lamiinae) in the Philippines with description of a new species from Mindanao",
			"description": "The catalogue of the genus Cereopsius Pascoe 1857 fauna of the Philippines is provided, with description of a new species, C. erasmus sp. nov. from Mindanao Island. Additional taxonomic and faunistic notes on the other Philippine species are added. &nbsp;",
			"author": [
				{
					"name": "MILTON NORMAN MEDINA"
				},
				{
					"name": "LESLAE KAY MANTILLA"
				},
				{
					"name": "ANALYN CABRAS"
				},
				{
					"name": "FRANCESCO VITALI"
				}
			],
			"volumeNumber": "5061",
			"issueNumber": "2",
			"pageStart": "383",
			"pageEnd": "391"
		}';		
		
		$json = '{"@type":"DataFeedItem","@id":"https:\/\/www.ingentaconnect.com\/content\/aspt\/sb\/2021\/00000046\/00000003\/art00028","url":"https:\/\/www.ingentaconnect.com\/content\/aspt\/sb\/2021\/00000046\/00000003\/art00028","name":"Taxonomic Reevaluation of Endemic Hawaiian Planchonella (Sapotaceae)","author":[{"name":"Havran, J. Christopher"},{"name":"Nylinder, Stephan"},{"name":"Swenson, Ulf"}],"volumeNumber":"46","issueNumber":"3","pageStart":"875","pageEnd":"888","doi":"10.1600\/036364421X16312067913480","image":"https:\/\/www.ingentaconnect.com\/images\/journal-logos\/aspt\/sb.gif","contentLocation":[],"about":[]}';


		$doc = json_decode($json);
	}
	
	return $doc;

}

//----------------------------------------------------------------------------------------
function send_doc($doc, $status)
{
	switch ($status)
	{
		case 303:
			header('HTTP/1.1 303 See Other');
			break;

		case 404:
			header('HTTP/1.1 404 Not Found');
			break;
		
		case 410:
			header('HTTP/1.1 410 Gone');
			break;
		
		case 500:
			header('HTTP/1.1 500 Internal Server Error');
			break;
				
		case 200:
		default:
			header('HTTP/1.1 200 OK');
			header("Content-type: text/plain");
			echo json_encode($doc, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
			break;
	}

}


//----------------------------------------------------------------------------------------
// Extract geographic localities from text
function add_geo(&$doc)
{
	$status = 200;
	
	// add empty array to signal that we have processed this, even if we find no names
	$doc->contentLocation = array();

	if (isset($doc->description) || isset($doc->name))
	{
		// get what we need from doc
		$text_elements = array();
		if (isset($doc->name))
		{
			$text_elements[] = $doc->name;
		}
		if (isset($doc->description))
		{
			$text_elements[] = $doc->description;
		}
		
		$text = join(' ', $text_elements);
		
		//echo $text . "\n";

		$parameters = array(
			"text" => $text
		);

		$url = 'http://localhost/~rpage/biorss/geoparser/';

		$json = post($url, http_build_query($parameters));

		$result = json_decode($json);

		if (json_last_error() != JSON_ERROR_NONE)
		{
			$status = 500;
		}
		else
		{
			// update doc
			if (isset($result->features))
			{
				$doc->contentLocation = array();
			
				// make sure we have only one representative of each location
				$wikidata = array();
				
				foreach ($result->features as $feature)
				{
					if ($feature->geometry->type == 'Point')
					{
						if (!in_array($feature->properties->wikidata_id, $wikidata))
						{
							$place = new stdclass;				
							$place->type = 'Place';
		
							// coordinates
							$place->geo = new stdclass;
							$place->geo->type = 'GeoCoordinates';
		
							$place->geo->latitude 	= (float)$feature->geometry->coordinates[1];
							$place->geo->longitude 	= (float)$feature->geometry->coordinates[0];
							
							if (isset($feature->properties->country_code))
							{
								$place->geo->addressCountry = $feature->properties->country_code;
							}
						
							$place->id 	= 'http://www.wikidata.org/entity/' . $feature->properties->wikidata_id;
							$place->name = $feature->properties->name;
				
							$doc->contentLocation[] = $place;
							
							$wikidata[] = $feature->properties->wikidata_id;
							
						}
					}
				}
				
				if (!isset($doc->about))
				{
					$doc->about = array();
				}
				foreach ($wikidata as $id)
				{
					$about_uri = 'http://www.wikidata.org/entity/' . $id;
					if (!in_array($about_uri, $doc->about))
					{				
						$doc->about[] = $about_uri;
					}
				}
			}
		}
	}
	
	return $status;
	
}


//----------------------------------------------------------------------------------------
//
function add_meta(&$doc)
{
	$status = 200;
	
	// add empty array to signal that we have processed this, even if we find no names
	$doc->meta = array();
		
	if (isset($doc->url) && (!isset($doc->item->doi) || !isset($doc->image) || !isset($doc->datePublished)  || !isset($doc->description)))
	{
		$go = true;
		
		$url = $doc->url;
		
		// Source shouldn't be an aggregator
		if (preg_match('/zoobank.org/', $url))
		{
			if (isset($doc->item->doi))
			{
				$url = 'https://doi.org/' . $doc->item->doi;
			}
		}
		
		if (!$go)
		{
			return $status;
		}
	
	
		$html = get($url);	
		
		if ($html == '')
		{
			$status = 404;
		}
		else
		{						
			$dom = HtmlDomParser::str_get_html($html);
			
			if ($dom)
			{	
				// meta
				foreach ($dom->find('meta') as $meta)
				{
					if (isset($meta->name))
					{
						$doc->meta[] = $meta->name;
					}
					if (isset($meta->property))
					{
						$doc->meta[] = $meta->property;
					}
				
					// DOI
					if (!isset($doc->item->doi) && isset($meta->name) && ($meta->content != ''))
					{
						switch ($meta->name)
						{				
							case 'citation_doi':
								$doi = $meta->content;
								$doc->item->doi = $doi;
								break;					

							case 'DC.identifier':
								$doi = $meta->content;
								$doi = str_replace('info:doi/', '', $doi);
								$doc->item->doi = $doi;
								break;	
								
							// https://cdnsciencepub.com/doi/abs/10.1139/cjes-2020-0190
							case 'dc.Identifier':
								if (isset($meta->scheme) && ($meta->scheme == 'doi'))
								{
									$doi = $meta->content;
									$doc->item->doi = $doi;	
								}								
								break;					
												

							default:
								break;
						}
					}
					
					// Date if we don't have one
					// <meta name="DCTERMS.issued" content="October 2021">	
					if (!isset($doc->datePublished) && isset($meta->name) && ($meta->content != ''))
					{
						switch ($meta->name)
						{				
							case 'DCTERMS.issued':
								$doc->datePublished = date(DATE_ISO8601, strtotime($meta->content));
								break;	
								
							default:
								break;
						}
					}				
					
					
					// Image
					if (!isset($doc->image) && isset($meta->property) && ($meta->content != ''))
					{
						switch ($meta->property)
						{				
							case 'og:image':
								$go = true;
								
								// check for bad images, e.g. Pensoft
								if (preg_match('/dx200x_\.jpg/', $meta->content))
								{
									$go = false;
								}
								
								if ($go)
								{
									$doc->image = $meta->content;
								}
								break;					

							default:
								break;
						}
					}
					
					// Description
					if (!isset($doc->description) && isset($meta->property) && ($meta->content != ''))
					{
						switch ($meta->property)
						{				
							case 'og:description':
								$doc->description = $meta->content;
								break;					

							default:
								break;
						}
					}
					
				}	
				
				// DOIs in various places
				if (!isset($doc->item->doi))
				{
					// CNKI
					foreach ($dom->find('li span[class=rowtit]') as $span)	
					{
						if (preg_match('/DOI：/u', $span->plaintext)	)
						{
							$p = $span->next_sibling();
							if ($p)
							{
								$doc->item->doi = $p->plaintext;
							}
						}
					}
				}

				// image
				
				// If we don't have an image in <META> go looking elsewhere
				if (!isset($doc->image))
				{
					// Magnolia Press 
					foreach ($dom->find('div[class=item cover_image] div img') as $img)	
					{
						$doc->image = $img->src;				
					}
				}
				
				if (!isset($doc->image))
				{
					// Ingenta
					foreach ($dom->find('div[id=article-journal-logo] img') as $img)	
					{
						$doc->image = 'https://www.ingentaconnect.com' . $img->src;				
					}
				}	
				
				// description
				if (!isset($doc->description))
				{
					// Ingenta
					foreach ($dom->find('div[class=tab-content] div[id=Abst]') as $div)	
					{
						$doc->description = $div->plaintext;	
						$doc->description = str_replace('Abstract&#8212;', '', $doc->description);
						$doc->description = full_clean_text	($doc->description);
					}
				}
				
						
						
			}
			
			$doc->meta = array_unique($doc->meta);
		}
		
		if (!isset($doc->image))
		{
			// try and find an image
			if (isset($doc->url))
			{
				// wanfangdata.com.cn/periodical/dwfl202102003
				if (preg_match('/.cn\/periodical\/(?<code>[a-z]{4})\d+/', $doc->url, $m))
				{
					$doc->image = 'https://www.wanfangdata.com.cn/images/PeriodicalImages/' . $m['code'] . '/' . $m['code'] . '.jpg';
				}
			
			}
		
		}
		
		// DOI as identifier
		if (isset($doc->item->doi) && !isset($doc->item->id))
		{
			$doc->item->id = 'https://doi.org/' . $doc->item->doi;
		}
		
			
	}
	
	return $status;
	
}

//----------------------------------------------------------------------------------------
// Extract taxonomic names
// We use taxonfinder to get the names, then use globalnames to match to GBIF

function add_taxa(&$doc)
{
	$status = 200;
	
	// add empty array to signal that we have processed this, even if we find no names
	$doc->classification = array();
	
	if (isset($doc->description) || isset($doc->name))
	{
		// get what we need from doc
		$text_elements = array();
		if (isset($doc->name))
		{
			$text_elements[] = $doc->name;
		}
		if (isset($doc->description))
		{
			$text_elements[] = $doc->description;
		}
		
		$text = join(' ', $text_elements);		
		
		$url = 'https://right-frill.glitch.me/api/find?text=' . urlencode($text);
		
		//echo "\n" . $url . "\n";
	
		$result = get($url);
	
		// Names returned by taxonfinder
		$result = json_decode($result, true);

		if (json_last_error() != JSON_ERROR_NONE)
		{
			$status = 500;
		}
		else
		{
			// print_r($result);
			
			$taxon_names = array();
			
			foreach ($result as $name)
			{
				$taxon_names[] = $name['name'];
			}
			
			$taxon_names = array_unique($taxon_names);
			
			// print_r($taxon_names);
			
			// names as keywords
			if (!isset($doc->keywords))
			{
				$doc->keywords = array();
			}
			$doc->keywords = array_merge($doc->keywords, $taxon_names);
			$doc->keywords = array_unique($doc->keywords);
			
			//----------------------------------------------------------------------------
			// Get identifiers and classification for names
			$query = $taxon_names;
		
			$response = global_names_index($query);
					
			// print_r($response);
			
			// we want to add the names, the GBIF ids, and represent the main subject somehow
			
			$paths = array();
			
			$taxon_ids = array();
		
			if (isset($response->data->nameResolver->responses))
			{
				foreach ($response->data->nameResolver->responses as $r)
				{
					if (isset($r->results[0]))
					{
						$paths[] = explode("|", $r->results[0]->classification->path);
						
						$taxon_ids[] = $r->results[0]->taxonId;
					}
				}
			}
			
			//print_r($path);
			
			//echo json_encode($path);
			
			//----------------------------------------------------------------------------
			// GBIF taxon ids are schema:about
			if (count($taxon_ids) > 0)
			{
				if (!isset($doc->about))
				{
					$doc->about = array();
				}
				foreach ($taxon_ids as $id)
				{
					$about_uri = 'https://www.gbif.org/species/' . $id;
					if (!in_array($about_uri, $doc->about))
					{				
						$doc->about[] = $about_uri;
					}
				}
			}
			
			//----------------------------------------------------------------------------
			// Get majority rule path so we can index the 
			// How many paths do we have?
			$num_paths = count($paths);

			// Store counts of each taxon
			$taxon_count = array();

			// Majority-rule
			$threshold = round($num_paths / 2);
			if ($num_paths % 2 == 0)
			{
				$threshold++;
			}

			// Count each taxon
			foreach ($paths as $path)
			{
				$path_length = count($path);
				for ($level = 0; $level < $path_length; $level++)
				{
					if (!isset($taxon_count[$level]))
					{
						$taxon_count[$level] = array();
					}
		
					if (!isset( $taxon_count[$level][$path[$level]] ))
					{
						$taxon_count[$level][$path[$level]] = 0;
					}
					$taxon_count[$level][$path[$level]]++;
				}
			}

			// Get majority-rule path
			$majority = array();

			foreach ($taxon_count as $level => $values)
			{
				foreach ($values as $name => $count)
				{
					if ($count >= $threshold)
					{
						$majority[] = $name;
					}
				}
			}
			
			if (count($majority) > 0)
			{
				$doc->classification = $majority;
			}

		}
	}
	
	return $status;
	
}



//----------------------------------------------------------------------------------------


if (0)
{
	// geoparsing
	$status = 200;

	$doc = get_doc(true);
	if ($doc)
	{
		$status = add_geo($doc);
	}
	else
	{
		$status = 500;
	}

	send_doc($doc, $status);
}

if (0)
{
	// DOI and thumbnail
	$status = 200;

	$doc = get_doc(true);
	if ($doc)
	{
		$status = add_meta($doc);
	}
	else
	{
		$status = 500;
	}

	send_doc($doc, $status);
}

if (0)
{
	// Taxonomic names
	$status = 200;

	$doc = get_doc(true);
	if ($doc)
	{
		$status = add_taxa($doc);
	}
	else
	{
		$status = 500;
	}

	send_doc($doc, $status);
}

?>
