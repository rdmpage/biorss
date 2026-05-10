<?php

error_reporting(E_ALL);

require_once (dirname(__FILE__) . '/ncbi.php');



//----------------------------------------------------------------------------------------

$filename = 'dois.tsv';
$filename = 'dois-test.tsv';

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
				//$obj->doi = '10.11646/zootaxa.4881.3.5';
			
				// do we have sequences?				
				$result = new stdclass;	
				
				// go via PMID			
				$pmid = doi_to_pmid($obj->doi);
				if ($pmid != 0)
				{					
					$result->pmid = $pmid;
					$result->gis = pmid_to_sequences($pmid);
				}
				
				// go via metadata
				if ($pmid == 0)
				{
					$result->gis = doi_to_genbank($obj->doi);
				}
				
				
				// print_r($result);
				
				$records[$obj->doi] = $result;
				
			
				
				//exit();
			}
			
		}
	}	
	$row_count++;
	
	
	if ($row_count > 100)
	{
		print_r($records);
		$json = json_encode($records, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

		file_put_contents("genbank.json", $json);

		exit();
	}
	
}

print_r($records);

$json = json_encode($records, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

file_put_contents("genbank.json", $json);


?>
