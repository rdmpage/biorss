<?php


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

// get POST data and do something on the object, returning the object as the result

$debug = true;
$debug = false;

$status = 200;

$post = null;

if (!$debug)
{
	$doc = json_decode(file_get_contents('php://input'));
}
else
{


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


	$doc = json_decode($json);
}


if (json_last_error() != JSON_ERROR_NONE)
{
	$status = 500;
}
else
{

	if (isset($doc->name) || isset($doc->description))
	{
		$text_strings = array();
		if (isset($doc->name))
		{
			$text_strings[] = $doc->name;
		}	
		if (isset($doc->description))
		{
			$description = $doc->description;
			
			// clean
			$description = preg_replace('/^(.*Abstract:\s+)/', '', $description);
			
			
			$text_strings[] = $description;
		}	
	
		$text = join(' ', $text_strings);
		
		

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
			if (isset($result->features))
			{
				$have = array();
			
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
						
						$place->geo->addressCountry = $feature->properties->country_code;
						
						$place->{'@name'} 	= 'http://www.wikidata.org/entity/' . $feature->properties->wikidata_id;
						$place->name 		= $feature->properties->name;
						
						if (!in_array($feature->properties->wikidata_id, $have))
						{				
							$doc->contentLocation[] = $place;
							$have[] = $feature->properties->wikidata_id;
						}
					}
				}
			}
		}
	}
}

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
					
	default:
		header('HTTP/1.1 200 OK');
		break;
}

header("Content-type: text/plain");
echo json_encode($doc, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);



?>


