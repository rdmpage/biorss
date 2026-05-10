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
function orcid_to_country($orcid)
{
	global $config;
	global $couch;
	
	$country = array();

	$url = '_design/orchid/_view/address?key=' . urlencode('"' . $orcid . '"');
		
	if ($config['stale'])
	{
		$url .= '&stale=ok';
	}	
	
	$url = 'http://admin:peacrab@127.0.0.1:5984/ldf/' . $url;
		
	$resp = get($url);
	
	$obj = json_decode($resp);
	
	foreach ($obj->rows as $row)
	{
		$country[] = $row->value;
	}
	
	return $country;			
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
			
			// do we ORCIDs for this DOI?
			
			if (1)
			{
				// do we have ORCIDs?				
				$orcids = doi_to_orcid($obj->doi);
				
				//print_r($orcids);
				
				foreach ($orcids as $orcid)
				{
					$countries	= orcid_to_country($orcid);
					//print_r($countries);
					
					foreach ($countries as $country)
					{
						echo $obj->doi . "\t" . $orcid . "\t" . $country . "\n";					
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
