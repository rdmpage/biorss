<?php

// Do something on a feed element

error_reporting(E_ALL);

require_once (dirname(__FILE__) . '/vendor/autoload.php');
require_once (dirname(__FILE__) . '/globalnames-graphql.php');
require_once (dirname(__FILE__) . '/config.inc.php');
require_once (dirname(__FILE__) . '/utils.php');

if (1)
{
	require_once (dirname(__FILE__) . '/HtmlDomParser.php');
}
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
		
		$json = '{
            "id": "urn:lsid:indexfungorum.org:names:842068",
            "url": "http://www.indexfungorum.org/Names/NamesRecord.asp?RecordID=842068",
            "datePublished": "2022-01-03T18:19:54+0000",
            "name": "Xylodon laeratus C.L. Zhao2021",
            "item": {
                "container": "Journal of Fungi",
                "volumeNumber": "8",
                "issueNumber": "1, no. 35",
                "pagination": "8",
                "datePublished": "2021-00-00",
                "description": "Journal of Fungi 8 1, no. 35 8 2021"
            }
        }';	
        
        $json = '{
            "id": "urn:lsid:indexfungorum.org:names:842172",
            "url": "http://www.indexfungorum.org/Names/NamesRecord.asp?RecordID=842172",
            "datePublished": "2022-01-03T18:29:09+0000",
            "name": "Mariorajchenbergia australiae (Y.C. Dai, Yuan Yuan & Ya.R. Wang) Gibertoni2021",
            "item": {
                "container": "Mycosphere",
                "volumeNumber": "12",
                "issueNumber": "1",
                "pagination": "1169",
                "datePublished": "2021-00-00"
            },
            "description": "Mycosphere 12 1 1169 2021"
        }';
		
		//$json = '{"@type":"DataFeedItem","@id":"https:\/\/www.ingentaconnect.com\/content\/aspt\/sb\/2021\/00000046\/00000003\/art00028","url":"https:\/\/www.ingentaconnect.com\/content\/aspt\/sb\/2021\/00000046\/00000003\/art00028","name":"Taxonomic Reevaluation of Endemic Hawaiian Planchonella (Sapotaceae)","author":[{"name":"Havran, J. Christopher"},{"name":"Nylinder, Stephan"},{"name":"Swenson, Ulf"}],"volumeNumber":"46","issueNumber":"3","pageStart":"875","pageEnd":"888","doi":"10.1600\/036364421X16312067913480","image":"https:\/\/www.ingentaconnect.com\/images\/journal-logos\/aspt\/sb.gif","contentLocation":[],"about":[]}';

		$json = '{"id":"https:\/\/doi.org\/10.1163\/18759866-bja10025","url":"http:\/\/dx.doi.org\/10.1163\/18759866-bja10025","item":{"doi":"10.1163\/18759866-bja10025","name":"The world\u2019s tiniest land snails from Laos and Vietnam (Gastropoda, Pulmonata, Hypselostomatidae)","author":["Barna P\u00e1ll-Gergely","Adrienne Jochum","Jaap J. Vermeulen","Katja Anker","Andr\u00e1s Hunyadi","Aydin \u00d6rstan","\u00c1bel Szab\u00f3","L\u00e1szl\u00f3 D\u00e1nyi","Menno Schilthuizen"],"container":"Contributions to Zoology","issn":["1383-4517","1875-9866"],"pagination":"1-17","datePublished":"2022-01-05","id":"https:\/\/doi.org\/10.1163\/18759866-bja10025"},"description":"Abstract Two new, extremely small land snail species, Angustopila coprologos P\u00e1ll-Gergely, Jochum & Hunyadi n. sp. and Angustopila psammion P\u00e1ll-Gergely, Vermeulen & Anker n. sp. are described from northern Vietnam and northern Laos, respectively. The former is characterized by a rough surface sculpture and bears tiny mud granules arranged in a pattern of radial lines on its shell surface. The latter species is the new global record-holder of the tiniest land snail title, with a shell width of 0.6\u20130.68 mm and a shell height of 0.46\u20130.57 mm. These measurements surpass the former records of Angustopila pallgergelyi and Acmella nana.","name":"The world\u2019s tiniest land snails from Laos and Vietnam (Gastropoda, Pulmonata, Hypselostomatidae)","datePublished":"2022-01-05","meta":[]}';

        
        $json = '{
    "id": "http://zoobank.org/References/5b105f82-ef77-4c18-b9dc-df4b51123a54",
    "url": "http://zoobank.org/References/5b105f82-ef77-4c18-b9dc-df4b51123a54",
    "name": "Hydrodroma angelieri (Acari, Hydrachnidia: Hydrodromidae) a new water mite species from Corsica based on morphological and DNA barcode evidence",
    "item": {
      "identifier": [
        "5b105f82-ef77-4c18-b9dc-df4b51123a54",
        "urn:lsid:zoobank.org:pub:5B105F82-EF77-4C18-B9DC-DF4B51123A54"
      ],
      "datePublished": "2022-01-00",
      "name": "Hydrodroma angelieri (Acari, Hydrachnidia: Hydrodromidae) a new water mite species from Corsica based on morphological and DNA barcode evidence",
      "volumeNumber": "62",
      "issueNumber": "1",
      "pageStart": "3",
      "pageEnd": "11",
      "container": "Acarologia",
      "author": [
        "Vladimir Pešić",
        "Harry Smit"
      ],
      "doi": "https://doi.org/10.24349/l06c-j0qm",
      "id": "https://doi.org/https://doi.org/10.24349/l06c-j0qm"
    },
    "datePublished": "2022-01-00",
    "description": "In the present study we used morphological data and DNA barcodes to describe a new species, Hydrodroma angelieri sp. nov. from Corsica, France. A high genetic distance of 17.3±0.017% K2P from its molecularly most closely related European congener, H. despiciens (Müller, 1776), supports H. angelieri sp. nov. as a distinct species. Morphologically the new species can be identified on the basis of relatively small leg claws, the presence of only one swimming seta on II-L-5 and 4-6 swimming setae on the anterior surface of IV-L-5. An updated key for the European species of Hydrodroma is provided. "
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
	global $config;
	
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

		$url = $config['web_server'] . $config['web_root'] . 'geoparser/';
		//$url = 'http://localhost/~rpage/biorss/geoparser/';

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
						if (isset($feature->properties->wikidata_id))
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
		
	if (isset($doc->url) && (!isset($doc->item->doi) || !isset($doc->thumbnailUrl) || !isset($doc->datePublished)  || !isset($doc->description)))
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
		
		if (preg_match('/ipni.org/', $url))
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
											
							// https://www.thebhs.org/publications/the-herpetological-journal/volume-32-number-1-january-2022/3430-05-i-acanthosaura-meridiona-i-sp-nov-squamata-agamidae-a-new-short-horned-lizard-from-southern-thailand	
							case 'description':
								if (preg_match('/(DOI:\s+)?https:\/\/doi.org\/(?<doi>[^\s]+)/', $meta->content, $m))
								{
									$doc->item->doi = $m['doi'];
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

							case 'citation_date':
								$doc->datePublished = date(DATE_ISO8601, strtotime($meta->content));
								break;	
								
							default:
								break;
						}
					}		
					
					// Twitter card	image	
					if (!isset($doc->thumbnailUrl) && isset($meta->name) && ($meta->content != ''))
					{
						switch ($meta->name)
						{				
							case 'twitter:image':
								$go = true;
								
								if ($go)
								{
									$doc->thumbnailUrl = $meta->content;
								}
								break;					

							default:
								break;
						}
					}
					
					// Image
					if (!isset($doc->thumbnailUrl) && isset($meta->property) && ($meta->content != ''))
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

								// check for poor choices of image, e.g. Acaralogia
								if (preg_match('/vignettetwitter.png/', $meta->content))
								{
									$go = false;
								}
								
								if ($go)
								{
									$doc->thumbnailUrl = $meta->content;
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
				if (!isset($doc->thumbnailUrl))
				{
					// Magnolia Press 
					foreach ($dom->find('div[class=item cover_image] div img') as $img)	
					{
						$doc->thumbnailUrl = $img->src;				
					}
										
				}
				
				// threatenedtaxa.org
				if (!isset($doc->thumbnailUrl))
				{
					// Magnolia Press 
					foreach ($dom->find('div[class=cover_image] img') as $img)	
					{
						$doc->thumbnailUrl = $img->src;				
					}
				}				

				// oaj.fupress.net
				if (!isset($doc->thumbnailUrl))
				{
					// Magnolia Press 
					foreach ($dom->find('div[class=article_cover_wrapper] img') as $img)	
					{
						$doc->thumbnailUrl = $img->src;				
					}
				}
				
				if (!isset($doc->thumbnailUrl))
				{
					// Ingenta
					foreach ($dom->find('div[id=article-journal-logo] img') as $img)	
					{
						$doc->thumbnailUrl = 'https://www.ingentaconnect.com' . $img->src;				
					}
				}	
				
				if (!isset($doc->thumbnailUrl))
				{					
					// Cahiers de Biologie Marine
					foreach ($dom->find('div[id=logobar] img') as $img)	
					{
						$doc->thumbnailUrl = 'http://application.sb-roscoff.fr/cbm/' . $img->src;				
					}
				}	
				
				// Brill
				if (!isset($doc->thumbnailUrl))
				{
					// Ingenta
					foreach ($dom->find('img[alt=Cover Tijdschrift voor Entomologie]') as $img)	
					{
						$doc->thumbnailUrl = 'https://brill.com' . $img->src;				
					}
				}	
				
				// AEMNP
				// http://dx.doi.org/10.37520/aemnp.2021.020
				if (!isset($doc->thumbnailUrl))
				{
					// AEMNP
					foreach ($dom->find('div[class=articleRightSideTop] a img') as $img)	
					{
						$doc->thumbnailUrl = $img->src;				
					}
				}	

				/*
				if (!isset($doc->thumbnailUrl))
				{
					// Acarologia
					if (isset($doc->item->doi) && preg_match('/10.24349/', $doc->item->doi))
					{
						foreach ($dom->find('figure img[class=img-fluid]') as $img)	
						{
							$doc->thumbnailUrl = 'https://www1.montpellier.inrae.fr/CBGP/acarologia/' . $img->src;		
							break;		
						}
					}
				}
				*/	
				
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
			else
			{
				// echo "oops\n";
				
				// Couldn't parse HTML so try regular expressions
				
				// <meta content="https://brill.com/cover/covers/13834517.jpg" property="og:image">
				if (preg_match('/<meta content="(.*)" property="og:image"\/>/', $html, $m))
				{
					$doc->thumbnailUrl = $m[1];
					$doc->meta[] = 'og:image';
				}
				
				
			}
			
			$doc->meta = array_unique($doc->meta);
		}
		
		if (!isset($doc->thumbnailUrl))
		{
			// try and find an image
			if (isset($doc->url))
			{
				// wanfangdata.com.cn/periodical/dwfl202102003
				if (preg_match('/.cn\/periodical\/(?<code>[a-z]{4})\d+/', $doc->url, $m))
				{
					$doc->thumbnailUrl = 'https://www.wanfangdata.com.cn/images/PeriodicalImages/' . $m['code'] . '/' . $m['code'] . '.jpg';
				}
				
				if (preg_match('/xbkcflxb/', $doc->url))
				{
					$doc->thumbnailUrl = 'http://xbkcflxb.cnjournals.com/xbkcflxb/ch/ext_images/未命名1.jpg';
				}
				// https://europeanjournaloftaxonomy.eu/index.php/ejt/article/view/1653
				if (preg_match('/europeanjournaloftaxonomy/', $doc->url))
				{
					$doc->thumbnailUrl = 'https://pbs.twimg.com/profile_images/1233042952236257281/3cZ7IjEE_400x400.jpg';
				}
								
				// https://pubmed.ncbi.nlm.nih.gov/34982242/?utm_source=Mobile%20Safari%20UI/WKWebView&utm_medium=rss&utm_campaign=pubmed-2&utm_content=1rE397IRBYU0-ogsyRnEw9o91K808u0evolcHK9IDZ0PVH5cqD&fc=20211108074834&ff=20220106081823&v=2.17.5
				if (preg_match('/pubmed.ncbi.nlm.nih.gov/', $doc->url))
				{
					$doc->thumbnailUrl = 'https://cdn.ncbi.nlm.nih.gov/pubmed/persistent/pubmed-meta-image.png';
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

if (0)
{
	// Taxonomic names
	$status = 200;

	$doc = get_doc(true);
	$status = add_taxa($doc);

	print_r($doc);
}



?>
