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


?>
