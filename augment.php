<?php

// Do something on a feed element

error_reporting(E_ALL);

require_once (dirname(__FILE__) . '/vendor/autoload.php');

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
function post($url, $data = '')
{
	
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data);  
	
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	
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
	
	if (isset($doc->description))
	{
		// get what we need from doc
		$text = $doc->description;

		$parameters = array(
			"text" => $text
		);

		$url = 'http://localhost/~rpage/glasgow-geoparser/';

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
				foreach ($result->features as $feature)
				{
					if ($feature->geometry->type == 'Point')
					{
						$place = new stdclass;				
						$place->{'@type'} = 'Place';
		
						// coordinates
						$place->geo = new stdclass;
		
						$place->geo->latitude 	= (float)$feature->geometry->coordinates[1];
						$place->geo->longitude 	= (float)$feature->geometry->coordinates[0];
						
						$place->{'@name'} 	= 'http://www.wikidata.org/entity/' . $feature->properties->wikidata_id;
						$place->name 		= $feature->properties->name;
				
						$doc->contentLocation[] = $place;
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
	
	if (isset($doc->url) && (!isset($doc->doi) || !isset($doc->image)))
	{
		$html = get($doc->url);	
		
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
					// DOI
					if (!isset($doc->doi) && isset($meta->name) && ($meta->content != ''))
					{
						switch ($meta->name)
						{
				
							case 'citation_doi':
								$doi = $meta->content;
								$doc->doi = $doi;
								break;					

							case 'DC.identifier':
								$doi = $meta->content;
								$doi = str_replace('info:doi/', '', $doi);
								$doc->doi = $doi;
								break;	
								
							// https://cdnsciencepub.com/doi/abs/10.1139/cjes-2020-0190
							case 'dc.Identifier':
								if (isset($meta->scheme) && ($meta->scheme == 'doi'))
								{
									$doi = $meta->content;
									$doc->doi = $doi;										
								}								
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
								$doc->image = $meta->content;
								break;					

							default:
								break;
						}
					}
				}				
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
		$status = add_geo($doc, $status);
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

?>
