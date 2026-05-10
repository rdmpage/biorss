<?php

error_reporting(E_ALL);

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
function doi_to_pdf($doi)
{
	$pdfs = array();

	$url = 'https://api.oadoi.org/v2/' . urlencode('"' . strtolower($doi) . '"') . '?email=unpaywall@impactstory.org';
	
	$json = get($url);
	
	$obj = json_decode($json);
	
	print_r($obj);
	
	if ($obj)
	{
		if (isset($obj->is_oa))
		{
			if ($obj->is_oa)
			{
				foreach ($obj->oa_locations as $location)
				{
					if (isset($location->url_for_pdf) && $location->url_for_pdf != "")
					{
						$pdfs[] = $location->url_for_pdf;
					}
				}
		
			}		
		}
	}
	
	return $pdfs;			
}

//----------------------------------------------------------------------------------------
function doi_to_oa($doi)
{
	$is_oa = false;

	$url = 'https://api.oadoi.org/v2/' . urlencode('"' . strtolower($doi) . '"') . '?email=unpaywall@impactstory.org';
	
	$json = get($url);
	
	$obj = json_decode($json);
	
	//print_r($obj);
	
	if ($obj)
	{
		if (isset($obj->is_oa))
		{
			$is_oa = $obj->is_oa;
		}
	}
	
	return $is_oa;			
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
			
			if (1)
			{
				//$obj->doi = '10.7717/peerj.12913';
				
				// pdfs
				if (0)
				{
					$pdfs = doi_to_pdf($obj->doi);
								
					echo $obj->doi . "\t";
					if (count($pdfs) > 0)
					{
						 echo $pdfs[0];
					}
					echo  "\n";
				}

				// OA flag
				if (1)
				{
					$is_oa = doi_to_oa($obj->doi);
								
					echo $obj->doi . "\t";
					if ($is_oa)
					{
						 echo "1";
					}
					else
					{
						 echo "0";					
					}
					echo  "\n";
				}
				
			}
			
		}
	}	
	
	$row_count++;
	
	// Give server a break every 10 items
	if (($row_count % 5) == 0)
	{
		$rand = rand(1000000, 3000000);
		//echo "\n-- ...sleeping for " . round(($rand / 1000000),2) . ' seconds' . "\n\n";
		usleep($rand);
	}
	

}


?>
