<?php

//----------------------------------------------------------------------------------------
function process_datacite($obj, $name = "DataCite", $url = "https://datacite.org", $issns = [])
{
	// feed
	$dataFeed = new stdclass;
	$dataFeed->name = $name;
	$dataFeed->url = $url;
	$dataFeed->dataFeedElement = array();	

	foreach ($obj->data as $item)
	{
		$go = true;
		
		// check whether this is an older version and, if so, ignore it
		if (isset($item->attributes->relatedIdentifiers))
		{
			foreach ($item->attributes->relatedIdentifiers as $relatedIdentifier)
			{			
				if ($relatedIdentifier->relationType == "IsVersionOf")
				{
					echo $item->attributes->doi . " is an older version\n";
					$go = false;
				}
			}
		}		
		
		if ($go)
		{
		
			$dataFeedElement = new stdclass;
			$dataFeedElement->id = 'https://doi.org/' . $item->attributes->doi;
			$dataFeedElement->url = $dataFeedElement->id;
		
			$dataFeedElement->datePublished = date(DATE_ISO8601, strtotime($item->attributes->created));		
		
			$dataFeedElement->item = new stdclass;			
			$dataFeedElement->item->doi = $item->attributes->doi;
		
			if (isset($item->attributes->identifiers))
			{
				foreach ($item->attributes->identifiers as $identifier)
				{			
					if ($identifier->identifierType == "LSID")
					{
						add_to_item($dataFeedElement->item, 'lsid', $identifier->identifier);	
					}
				}
			}
								
			if (isset($item->attributes->titles))
			{
				$dataFeedElement->name = full_clean_text($item->attributes->titles[0]->title);
				$dataFeedElement->item->name = $dataFeedElement->name;
			}

			if (isset($item->attributes->descriptions))
			{
				if (count($item->attributes->descriptions) > 0)
				{
					$dataFeedElement->description = full_clean_text($item->attributes->descriptions[0]->description);
				}
			}
		
			if (isset($item->attributes->creators))
			{
				foreach ($item->attributes->creators as $creator)
				{
					if (isset($creator->name))
					{
						add_to_item($dataFeedElement->item, 'author', $creator->name);	
					}
				}
			}
		
			if (isset($item->attributes->url))
			{
				$dataFeedElement->url = $item->attributes->url;	
				if (preg_match('/\.pdf$/', $item->attributes->url))
				{
					$dataFeedElement->item->pdf = $item->attributes->url;
				}	
			}
		
			if (isset($item->attributes->publicationYear))
			{
				add_to_item($dataFeedElement->item, 'publicationYear', $item->attributes->publicationYear);		
			}
				
			if (isset($item->attributes->container))
			{
		
				if (isset($item->attributes->container->title))
				{
					add_to_item($dataFeedElement->item, 'container-title', $item->attributes->container->title);		
				}
					
				if (isset($item->attributes->container->identifier))
				{
					if ($item->attributes->container->identifierType == "ISSN")
					{
						add_to_item($dataFeedElement->item, 'issn', $item->attributes->container->identifier);		
					}
					if ($item->attributes->container->identifierType == "EISSN")
					{
						add_to_item($dataFeedElement->item, 'issn', $item->attributes->container->identifier);		
					}
				}
			
				if (isset($item->attributes->container->volume))
				{
					add_to_item($dataFeedElement->item, 'volume', $item->attributes->container->volume);		
				}

				/*
				if (isset($item->issue))
				{
					add_to_item($dataFeedElement->item, 'issue', $item->issue);		
				}
				*/

				if (isset($item->attributes->container->firstPage))
				{
					add_to_item($dataFeedElement->item, 'firstPage', $item->attributes->container->firstPage);		
				}

				if (isset($item->attributes->container->lastPage))
				{
					add_to_item($dataFeedElement->item, 'lastPage', $item->attributes->container->lastPage);		
				}
			
			
				// Zenodo/DataCite often don't know about journal
				
				if (!isset($dataFeedElement->item->container) && isset($dataFeedElement->item->issn))
				{
					if (isset($issns[$dataFeedElement->item->issn]))
					{
						$dataFeedElement->item->container = $issns[$dataFeedElement->item->issn];
					}
				}
						
			}

		
			$dataFeed->dataFeedElement[] = $dataFeedElement;
		}
	
	}


	return $dataFeed;
}


?>
