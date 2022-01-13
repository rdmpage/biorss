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
	
		case 'DC.Contributor':
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
			
		case 'citation_doi':
		case 'doi':
			$item->{$key} = strtolower($value);
			break;
			
		case 'lastPage':
		case 'citation_lastpage':
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
			
		case 'citation_issue':
		case 'issue':
		case 'Issue':
		case 'number':
			$item->issueNumber = $value;
			break;			
			
		case 'citation_issn':
		case 'issn':
		case 'ISSN':
		case 'name':
		case 'pmid':
			$item->{$key} = $value;
			break;
			
		case 'Page':
		case 'page':
		case 'pages':
			$item->pagination = $value;
			break;
			
		case 'citation_pdf_url':
			$item->pdf = $value;
			break;
						
		case 'citation_date':
		case 'publicationDate':
		case 'Publication date':
		case 'PublishDate':
			$item->datePublished = date("Y-m-H", strtotime($value));
			break;

		case 'citation_journal_title':
		case 'container-title':
		case 'journal':
		case 'parentreference':
		case 'publicationTitle':		
			$item->container = $value;
			break;
			
		case 'firstPage':
		case 'citation_firstpage':
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
						
		case 'citation_volume':
		case 'volume':
		case 'Volum':
			$item->volumeNumber = $value;
			break;
			
		case 'publicationYear':
		case 'year':
			$item->datePublished = $value . "-00-00";
			break;

		
		
		default:
			break;
	
	
	
	}


	return $item;

}


?>