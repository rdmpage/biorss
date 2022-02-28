<?php

// CSL (e.g., from CrossRef) to dataFeedElement

require_once(dirname(__FILE__) . '/item.php');
require_once(dirname(__FILE__) . '/utils.php');


//----------------------------------------------------------------------------------------
function csl_to_dataFeedElement($csl)
{
	$dataFeedElement = new stdclass;
	$dataFeedElement->id = 'https://doi.org/' . $csl->DOI;
	$dataFeedElement->url = $dataFeedElement->id;
	
	$dataFeedElement->item = new stdclass;			
	$dataFeedElement->item->doi = $csl->DOI;
	
	if (isset($csl->abstract))
	{
		$dataFeedElement->description = full_clean_text($csl->abstract);
	}
				
	if (isset($csl->title))
	{
		if (is_array($csl->title))
		{
			$dataFeedElement->name = full_clean_text($csl->title[0]);
		}
		else
		{
			$dataFeedElement->name = full_clean_text($csl->title);
		}
		
		$dataFeedElement->item->name = $dataFeedElement->name;
	}
	
	if (isset($csl->author))
	{
		foreach ($csl->author as $author)
		{
			$parts = array();
			
			if (isset($author->given))
			{
				$parts[] = $author->given;
			}
			if (isset($author->family))
			{
				$parts[] = $author->family;
			}
			
			add_to_item($dataFeedElement->item, 'author', join(' ', $parts));	
		
		}
	}			
	
	if (isset($csl->{'container-title'}))
	{
		if (is_array($csl->{'container-title'}))
		{
			add_to_item($dataFeedElement->item, 'container-title', $csl->{'container-title'}[0]);	
		}
		else
		{
			add_to_item($dataFeedElement->item, 'container-title', $csl->{'container-title'});	
		}
	}
	
	if (isset($csl->URL))
	{
		$dataFeedElement->url = $csl->URL;			
	}

	if (isset($csl->ISSN))
	{
		add_to_item($dataFeedElement->item, 'issn', $csl->ISSN);		
	}

	if (isset($csl->volume))
	{
		add_to_item($dataFeedElement->item, 'volume', $csl->volume);		
	}

	if (isset($csl->issue))
	{
		add_to_item($dataFeedElement->item, 'issue', $csl->issue);		
	}

	if (isset($csl->page))
	{
		add_to_item($dataFeedElement->item, 'page', $csl->page);		
	}

	if (isset($csl->issued))
	{			
		$d = $csl->issued->{'date-parts'}[0];
		if ( count($d) > 0 ) $year = $d[0] ;
		if ( count($d) > 1 ) $month = preg_replace ( '/^0+(..)$/' , '$1' , '00'.$d[1] ) ;
		if ( count($d) > 2 ) $day = preg_replace ( '/^0+(..)$/' , '$1' , '00'.$d[2] ) ;
	
		if ( isset($month) and isset($day) ) $date 	= "$year-$month-$day";
		else if ( isset($month) ) $date 			= "$year-$month-00";
		else if ( isset($year) ) $date 				= "$year-00-00";
		
		$dataFeedElement->item->datePublished = $date;
		
		// Set date for feed element
		$dataFeedElement->datePublished = $dataFeedElement->item->datePublished;
	}
		
	return $dataFeedElement;
}


?>