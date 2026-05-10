<?php

error_reporting(E_ALL);

require_once (dirname(dirname(__FILE__)) . '/couchsimple.php');



//----------------------------------------------------------------------------------------
function get($url)
{
	$opts = array(
	  CURLOPT_URL =>$url,
	  CURLOPT_FOLLOWLOCATION => TRUE,
	  CURLOPT_RETURNTRANSFER => TRUE
	);
	
	$ch = curl_init();
	curl_setopt_array($ch, $opts);
	$data = curl_exec($ch);
	$info = curl_getinfo($ch); 
	curl_close($ch);
	
	
	return $data;
			
}
	
//----------------------------------------------------------------------------------------
function doi_to_orcid($doi)
{
	global $config;
	global $couch;
	
	$orcids = array();

	$url = '_design/author/_view/doi-orcid?key=' . urlencode('"' . strtolower($doi) . '"');
		
	if ($config['stale'])
	{
		$url .= '&stale=ok';
	}	
	
	$url = 'http://admin:peacrab@127.0.0.1:5984/crossref-cache/' . $url;
		
	$resp = get($url);
	
	$obj = json_decode($resp);
	
	foreach ($obj->rows as $row)
	{
		$orcids[] = $row->value;
	}
	
	return $orcids;			
}

//----------------------------------------------------------------------------------------
function doi_to_author_count($doi)
{
	global $config;
	global $couch;
	
	$num_authors = 0;

	$url = '_design/author/_view/doi-author-count?key=' . urlencode('"' . strtolower($doi) . '"')
		. '&group_level=2';
		
	if ($config['stale'])
	{
		$url .= '&stale=ok';
	}	
	
	$url = 'http://admin:peacrab@127.0.0.1:5984/crossref-cache/' . $url;
		
	$resp = get($url);
	
	$obj = json_decode($resp);
	if (count($obj->rows) == 1)
	{
		$num_authors = $obj->rows[0]->value;
	}
	
	return $num_authors;			
}


//----------------------------------------------------------------------------------------

$filename = 'dois.tsv';

$headings = array();

$row_count = 0;

$file = @fopen($filename, "r") or die("couldn't open $filename");


$records = array();
		
$file_handle = fopen($filename, "r");
while (!feof($file_handle)) 
{
	$row = fgetcsv(
		$file_handle, 
		0, 
		"\t" 
		);
		
	$go = is_array($row);
	
	if ($go)
	{
		if ($row_count == 0)
		{
			$headings = $row;		
		}
		else
		{
			$obj = new stdclass;
			
			$keys = array();
			$values = array();
		
			foreach ($row as $k => $v)
			{
				if ($v != '')
				{
					$obj->{$headings[$k]} = $v;
					
					$keys[] = $headings[$k];
					$values[] = '"' . str_replace('"', '""', $v) . '"';
				}
			}
		
			//print_r($obj);	
			
			// do we have metadata for this DOI?
			
			if (1)
			{
				// do we have ORCIDs?				
				$result = new stdclass;				
				$result->orcids = doi_to_orcid($obj->doi);
				
				$result->num_authors = doi_to_author_count($obj->doi);
				$records[$obj->doi] = $result;
			}
			
		}
	}	
	$row_count++;
	
	/*
	if ($row_count > 10)
	{
		print_r($records);
		$json = json_encode($records, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

		file_put_contents("authors.json", $json);

		exit();
	}
	*/
}

//print_r($records);

$json = json_encode($records, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

file_put_contents("authors.json", $json);


?>
