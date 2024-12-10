<?php

// get records for a view and post process them, e.g. PDFs

error_reporting(E_ALL);

require_once (dirname(__FILE__) . '/couchsimple.php');

$pdfs = array();

if (1)
{
	// DOIs for a specific agency

	$limit = 100;
	

	$url = '_design/key/_view/pdf?group_level=2';
	
	$resp = $couch->send("GET", "/" . $config['couchdb_options']['database'] . "/" . $url);

	$obj = json_decode($resp);
	
	//print_r($obj);

	
	foreach ($obj->rows as $row)
	{
		//echo $row->value . "\n";
		
		$pdfs[] = $row->key;
	}

	
}



echo "(\n'";

echo join ("',\n'", $pdfs);

echo "');\n";


?>
