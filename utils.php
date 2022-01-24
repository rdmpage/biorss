<?php

mb_internal_encoding("UTF-8");
setlocale(LC_ALL, 0);
date_default_timezone_set('UTC');

define ('WHITESPACE_CHARS', ' \f\n\r\t\x{00a0}\x{0020}\x{1680}\x{180e}\x{2028}\x{2029}\x{2000}\x{2001}\x{2002}\x{2003}\x{2004}\x{2005}\x{2006}\x{2007}\x{2008}\x{2009}\x{200a}\x{202f}\x{205f}\x{3000}');

//----------------------------------------------------------------------------------------
// Clean up text so that we have single spaces between text, 
// see https://github.com/readmill/API/wiki/Highlight-locators
function clean_text($text)
{	
	$text = preg_replace('/[' . WHITESPACE_CHARS . ']+/u', ' ', $text);
	
	return $text;
}

//----------------------------------------------------------------------------------------
// Completely clean text
function full_clean_text($text)
{
	$text = strip_tags($text);
	$text = clean_text($text);
	$text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
	$text = trim($text);

	return $text;
}

//----------------------------------------------------------------------------------------
// If RSS date is in another language then we need to translate days and
// months to English before converting to ISO date
// See https://stackoverflow.com/questions/6988536/strtotime-with-different-languages
function translate_date($date_string, $locale = "en_GB")
{
	$map = array();
	
	// clean up locale
	$locale = str_replace('-', '_', $locale);

	// months
	$month_numbers = range(1,12);

	foreach($month_numbers as $month)
	{
		setlocale(LC_TIME, "en_GB");
		$e = strftime('%b',mktime(0,0,0,$month,1,2011));
		setlocale(LC_TIME, $locale);
		$f = strftime('%b',mktime(0,0,0,$month,1,2011));

		$map[$f] = $e;

	}
	
	// Based on https://stackoverflow.com/q/34465351/9684
	$timestamp = strtotime('next Monday');
	for ($i = 0; $i < 7; $i++) 
	{
		setlocale(LC_TIME, "en_GB");
		$e = strftime('%a', $timestamp) . ',';
		setlocale(LC_TIME, $locale);
		$f = strftime('%a', $timestamp) . ',';
		$map[$f] = $e;
		$timestamp = strtotime('+1 day', $timestamp);
	}
	
	return strtr($date_string, $map);
}


//----------------------------------------------------------------------------------------
function url_pattern_to_doi($url)
{
	$doi = '';
	
	if ($doi == "")
	{
		if (preg_match('/(?<doi>10\.\d+\/(.*))\.pdf/', $url, $m))
		{
			$doi = $m['doi'];
		}
	}	
	
	return $doi;
}

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
function get_image($url)
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
// Encode image
function encode_image($doc)
{
	$url = '';
	
	if ($url == '')
	{
		if (isset($doc->thumbnailUrl) && !isset($doc->image))
		{
			$url = $doc->thumbnailUrl;
		}
	}

	if ($url == '')
	{
		if (isset($doc->image) && preg_match('/^http/', $doc->image))
		{
			$url = $doc->image;
		}
	}

	if ($url != '')
	{
		// get image URL		
		$image = get_image($url);
		
		if ($image != '')
		{
			// store image in a temporary file
			$image_file_name = tempnam("/tmp", "image");			
			file_put_contents($image_file_name, $image);
			
			// get MIME type
			$finfo = finfo_open(FILEINFO_MIME_TYPE); // return mime type ala mimetype extension
			$mime_type = finfo_file($finfo, $image_file_name);
			finfo_close($finfo);
			
			// resize
			$command = 'mogrify -resize 128 ' . $image_file_name;
			system($command);

			// encode 			
			$image = file_get_contents($image_file_name);
			$base64 = chunk_split(base64_encode($image));
			
			// save image URL then replace image
			if (!isset($doc->thumbnailUrl))
			{
				$doc->thumbnailUrl = $url;
			}			
			$doc->image = 'data:' . $mime_type . ';base64,' . $base64;	
		}
	}
	return $doc;
}

//----------------------------------------------------------------------------------------
// Clean a DOI
function clean_doi($doi)
{
	$doi = preg_replace('/https?:\/\/(dx\.)?doi.org\//i', '', $doi);
	
	$doi = strtolower($doi);
	
	return $doi;
}

?>
