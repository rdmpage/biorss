<?php

require_once(dirname(__FILE__) . '/vendor/autoload.php');
require_once(dirname(__FILE__) . '/config.inc.php');
require_once(dirname(__FILE__) . '/item.php');
require_once(dirname(__FILE__) . '/utils.php');
require_once(dirname(__FILE__) . '/zoobank.php');

use Sunra\PhpSimple\HtmlDomParser;


$dataFeed = new stdclass;
$dataFeed->name = "ZooBank";
$dataFeed->url = "http://zoobank.org";
$dataFeed->dataFeedElement = array();


$uuids = array(
'd00dc6e3-80b6-4fe8-8d0d-e2dee2670130',
'dcdfc3e7-5821-4839-80f5-f5ff7b180cad',
'1bd6220b-7540-4a12-b3d2-5edc1a3b7dd8',
'e0baa274-37cb-407b-849a-fd284bbe954b',
'344ca83a-6c54-4425-8a1f-1ba540611214',
'0e077f1c-c4fe-492e-8b87-3bc66e53561d',
'2fdf3098-684a-4503-85d8-e0c05233d67f',
'73f7417b-1e34-48e4-8a91-7aaad4ec5fa7',
'c3373c04-b4e8-4146-8dd1-0185c3823f2e',
'390fea39-deba-4406-b99f-bc6625821960',
'0670ac22-7b43-4eda-ba29-7fac9951b48a',
'0362ddf4-d56e-4431-9639-3dbf772e221c',
'd0be7479-6c9c-417b-bc69-fdad60d38bbd',
);


foreach ($uuids as $uuid)
{
	$files = zoobank_retrieve($uuid);

	//print_r($files);

	if (count($files) == 2)
	{
		$obj = json_decode($files['json']);
	
		//print_r($obj);

		$dataFeedElement = new stdclass;
		$dataFeedElement->id = 'http://zoobank.org/References/' . $obj->referenceuuid;
		$dataFeedElement->url = $dataFeedElement->id;
	
		$dataFeedElement->name = full_clean_text($obj->title);
		
		// $dataFeedElement->datePublished = $item->PublishDate;

		// item
		$dataFeedElement->item = new stdclass;
	
		foreach ($obj as $k => $v)
		{
			switch ($k)
			{
				case 'title':
					add_to_item($dataFeedElement->item, 'name', full_clean_text($v));
					break;
				
				case 'endpage':
				case 'lsid':
				case 'number':
				case 'parentreference':
				case 'referenceuuid':
				case 'startpage':			
				case 'volume':
				case 'year':
					add_to_item($dataFeedElement->item, $k, $v);
					break;
				
				case 'authors':
					foreach ($v as $element)
					{
						$parts = array();
					
						if (isset($element[0]->givenname) && ($element[0]->givenname != ''))
						{
							$parts[] = $element[0]->givenname;
						}

						if (isset($element[0]->familyname) && ($element[0]->familyname != ''))
						{
							$parts[] = $element[0]->familyname;
						}
					
						add_to_item($dataFeedElement->item, 'author', join(' ', $parts));				
					}
					break;
				
				default:
					break;
			}
	
		}
	
		// HTML has some additional stuff such as DOI and a more precise date
		$html = 
		$dom = HtmlDomParser::str_get_html($files['html']);
	
		if ($dom)
		{	
			foreach ($dom->find('tr th[class=entry_label]') as $th)
			{
				switch (trim($th->plaintext))
				{
					case 'Date Published:':
						add_to_item($dataFeedElement->item, 'publicationDate', trim($th->next_sibling()->plaintext));	
						break;

					case 'DOI:':
						add_to_item($dataFeedElement->item, 'doi', trim($th->next_sibling()->plaintext));	
						break;
			
					default:
						break;
				}
	
			}
		}
	
		// set date for DataFeedElement
		if (isset($dataFeedElement->item->datePublished))
		{
			$dataFeedElement->datePublished = $dataFeedElement->item->datePublished;
		}
	
		$dataFeed->dataFeedElement[] = $dataFeedElement;
	
	

	}
}

print_r($dataFeed);


// Where shall we store the feeds?
if (0)
{
	$today = date('Y-m-d', time());

	$cache_dir = $config['cache'] . '/' . $today;

	if (!file_exists($cache_dir))
	{
		$oldumask = umask(0); 
		mkdir($cache_dir, 0777);
		umask($oldumask);
	}	
}
else
{
	$cache_dir = $config['cache'];
}

$filename = $cache_dir . '/zoobank.json';
file_put_contents($filename, json_encode($dataFeed, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));

?>

