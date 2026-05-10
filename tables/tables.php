<?php

// Data sets

error_reporting(E_ALL);

require_once (dirname(dirname(__FILE__)) . '/couchsimple.php');


//----------------------------------------------------------------------------------------
function write_table($table)
{
	echo join("\t", $table->header) . "\n";
	
	foreach ($table->rows as $row)
	{
		echo join("\t", $row) . "\n";
	}


}

//----------------------------------------------------------------------------------------
// 
function data_country($year = 2021)
{
	global $config;
	global $couch;

	$startkey 	= array((String)$year);
	$endkey		= array((String)($year+1));
	
	$url = '_design/tables/_view/country?startkey=' . urlencode(json_encode($startkey))
		. '&endkey=' .  urlencode(json_encode($endkey))
		. '&group_level=2'
		;
		
	if ($config['stale'])
	{
		$url .= '&stale=ok';
	}			
	
	$resp = $couch->send("GET", "/" . $config['couchdb_options']['database'] . "/" . $url);
	

	$obj = json_decode($resp);
	
	//print_r($obj);
	
	$table = new stdclass;
	$table->header = array();
	$table->rows = array();
	
	$table->header[] = 'Country code';
	$table->header[] = 'Count';
	
	foreach ($obj->rows as $row)
	{
		$values = array();
		$values[] = $row->key[1];
		$values[] = $row->value;
		$table->rows[] = $values;
	}
	
	//print_r($table);
	
	write_table($table);
	
}




//----------------------------------------------------------------------------------------
// 
function data_taxa($year = 2022, $level = 1)
{
	global $config;
	global $couch;

	$startkey 	= array((String)$year);
	$endkey		= array((String)($year+1));
	
	$level += 2;
	
	$url = '_design/tables/_view/taxa?startkey=' . urlencode(json_encode($startkey))
		. '&endkey=' .  urlencode(json_encode($endkey))
		. '&group_level=' . $level
		;
		
	if ($config['stale'])
	{
		$url .= '&stale=ok';
	}			
	
	$resp = $couch->send("GET", "/" . $config['couchdb_options']['database'] . "/" . $url);

	$obj = json_decode($resp);
	
	//print_r($obj);
	
	$table = new stdclass;
	$table->header = array();
	$table->rows = array();
	
	for ($i = 2; $i <= $level; $i++)
	{
		$table->header[] = 'Level '. ($i-1);
	}
	$table->header[] = 'Count';
	
	foreach ($obj->rows as $row)
	{
		$values = array();
		
		for ($i = 2; $i <= $level; $i++)
		{
			if (isset($row->key[$i]))
			{
				$values[] = $row->key[$i];
			}
			else
			{
				$values[] = "";
			}
		}
		$values[] = $row->value;
		$table->rows[] = $values;
	}
	
	//print_r($table);
	
	write_table($table);
	
}

//----------------------------------------------------------------------------------------
// dump list of DOIs for a year
function data_doi($year = 2022)
{
	global $config;
	global $couch;

	$startkey 	= array((String)$year);
	$endkey		= array((String)($year+1));
	
	$url = '_design/tables/_view/doi?startkey=' . urlencode(json_encode($startkey))
		. '&endkey=' .  urlencode(json_encode($endkey))
		. '&group_level=2'
		;
		
	if ($config['stale'])
	{
		$url .= '&stale=ok';
	}			
	
	$resp = $couch->send("GET", "/" . $config['couchdb_options']['database'] . "/" . $url);
	

	$obj = json_decode($resp);
		
	$table = new stdclass;
	$table->header = array();
	$table->rows = array();
	
	$table->header[] = 'doi';
	
	foreach ($obj->rows as $row)
	{
		$values = array();
		$values[] = $row->key[1];
		$table->rows[] = $values;
	}
	
	//print_r($table);
	
	write_table($table);
}

//----------------------------------------------------------------------------------------
// 
function doi_country($year = 2021)
{
	global $config;
	global $couch;

	$startkey 	= array((String)$year);
	$endkey		= array((String)($year+1));
	
	$url = '_design/tables/_view/year-doi-country?startkey=' . urlencode(json_encode($startkey))
		. '&endkey=' .  urlencode(json_encode($endkey))
		. '&group_level=3'
		;
		
	if ($config['stale'])
	{
		$url .= '&stale=ok';
	}			
	
	$resp = $couch->send("GET", "/" . $config['couchdb_options']['database'] . "/" . $url);
	

	$obj = json_decode($resp);
	
	// print_r($obj);
	
	
	$table = new stdclass;
	$table->header = array();
	$table->rows = array();
	
	$table->header[] = 'DOI';
	$table->header[] = 'Country code';
	
	foreach ($obj->rows as $row)
	{
		$values = array();
		$values[] = $row->key[1];
		$values[] = $row->key[2];
		$table->rows[] = $values;
	}
	
	//print_r($table);
	
	write_table($table);
	
	
}


//data_country(2022);

// data_taxa(2022, 4);

// data_doi(2022);

doi_country($year = 2022);



?>
