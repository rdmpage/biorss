<?php

// get records for a view and post process them, e.g. DOI classify

error_reporting(E_ALL);

require_once (dirname(__FILE__) . '/couchsimple.php');


if (1)
{
	// DOIs for a specific agency

	$limit = 100;
	
	$dois = array();
	
	$key = 'CNKI';
	$key = 'Airiti';
	//$key = 'Crossref';
	//$key = 'DataCite';
	//$key = 'mEDRA';
	//$key = 'ISTIC';
	//$key = 'JALC';

	$url = '_design/key/_view/doi_agency'
		. '?key=' . urlencode('"' . $key . '"')
		;
	
	$resp = $couch->send("GET", "/" . $config['couchdb_options']['database'] . "/" . $url);

	$obj = json_decode($resp);


	foreach ($obj->rows as $row)
	{
		//echo $row->value . "\n";
		
		$dois[] = $row->value;
	}
	
	$dois = array_unique($dois);
	
	echo "\n" . '$dois=array(' . "\n";
	foreach ($dois as $doi)
	{
		echo '"' . $doi . '",' . "\n";
	}
	echo ");\n";

	
}


?>
