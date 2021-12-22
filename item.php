<?php

error_reporting(E_ALL);

// An "item" or work

//----------------------------------------------------------------------------------------
function add_multingual_key_value(&$item, $key, $values)
{
	$item->{$key} = array();
	foreach ($values as $value)
	{
		$language = 'en';
		if (preg_match('/\p{Han}+/u', $value))
		{
			$language = 'zh';
		}
		$item->{$key}[$language] = $value;
	}
}

//----------------------------------------------------------------------------------------
function add_to_item(&$item, $key, $value)
{
	// ignore empty values
	if ($value == "")
	{
		return $item;
	}

	switch ($key)
	{
		case 'Abstract':
			add_multingual_key_value($item, 'description', $value);
			break;			
	
		case 'author':
			if (!isset($item->author))
			{
				$item->author = array();
			}
			$item->author[] = $value;
			break;
			
		case 'Creator'	:		
			$item->author = $value;
			break;
			
		case 'doi':
			$item->{$key} = strtolower($value);
			break;
			
		case 'endpage':
		case 'endingPage':
		case 'end_page':
			$item->pageEnd = $value;
			$item->pageEnd = preg_replace('/^0/', '', $item->pageEnd);
			break;	
			
		case 'Id':
		case 'lsid':
		case 'referenceuuid':
			$item->identifier[] = $value;
			break;		
			
		case 'issue':
		case 'Issue':
		case 'number':
			$item->issueNumber = $value;
			break;			
			
		case 'issn':
		case 'ISSN':
		case 'name':
		case 'pmid':
			$item->{$key} = $value;
			break;
			
		case 'Page':
			$item->pagination = $value;
			break;
						
		case 'publicationDate':
		case 'PublishDate':
			$item->datePublished = date("Y-m-H", strtotime($value));
			break;

		case 'journal':
		case 'parentreference':
		case 'publicationTitle':		
			$item->container = $value;
			break;
			
		case 'startpage':
		case 'startingPage':
		case 'start_page':
			$item->pageStart = $value;
			$item->pageStart = preg_replace('/^0/', '', $item->pageStart);
			break;
			
		case 'PeriodicalTitle':
			add_multingual_key_value($item, 'container', $value);
			break;			
			
		case 'Title':
			add_multingual_key_value($item, 'name', $value);
			break;			
						
		case 'volume':
		case 'Volum':
			$item->volumeNumber = $value;
			break;
			
		case 'year':
			$item->datePublished = $value . "-00-00";
			break;

		
		
		default:
			break;
	
	
	
	}


	return $item;

}


?>