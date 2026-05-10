<?php

// For list of DOIs get funders via CrossRef cache

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
function doi_to_funders($doi)
{
	global $config;
	global $couch;
	
	$funders = array();

	$url = '_design/funder/_view/funder?key=' . urlencode('"' . strtolower($doi) . '"');
		
	if ($config['stale'])
	{
		$url .= '&stale=ok';
	}	
	
	$url = 'http://admin:peacrab@127.0.0.1:5984/crossref-cache/' . $url;
		
	$resp = get($url);
	
	$obj = json_decode($resp);
	
	foreach ($obj->rows as $row)
	{
		$funders[] = $row->value;
	}
	
	return $funders;			
}

//----------------------------------------------------------------------------------------

$filename = 'dois.tsv';

$headings = array();

$row_count = 0;

$file = @fopen($filename, "r") or die("couldn't open $filename");
		
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
			
			// do we funders for this DOI?
			
			if (1)
			{
				// do we have funders?				
				$funders = doi_to_funders($obj->doi);
				
				//print_r($orcids);
				
				foreach ($funders as $funder)
				{
					if (isset($funder->DOI))
					{
						echo $obj->doi . "\t" . $funder->DOI . "\n";		
					}			
					
				}
				
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


?>
