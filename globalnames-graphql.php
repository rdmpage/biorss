<?php

// Query global names for matches


error_reporting(E_ALL);

mb_internal_encoding("UTF-8");


//----------------------------------------------------------------------------------------
function global_names_index($query)
{
	// construct a GraphQL object
	
	$data = new stdclass;
	$data->operationName = 'NameResolver';
	$data->variables = new stdclass;
	$data->variables->names = array();

	foreach ($query as $q)
	{
		$name = new stdclass;
		$name->value = $q;

		$data->variables->names[] = $name;
	}

	$data->variables->bestMatchOnly = true;
	$data->variables->dataSourceIds = array();

	$data->variables->dataSourceIds[] = 11; // GBIF

	$data->query = 'query NameResolver($names: [name!]!, $dataSourceIds: [Int!], $bestMatchOnly: Boolean) {
	  nameResolver(names: $names, bestMatchOnly: $bestMatchOnly, dataSourceIds: $dataSourceIds) {
		responses {
		  suppliedInput
		  total
		  results {
			name {
			  id
			  value
			}
		   canonicalName {
			  id
			  value
			  valueRanked
			}        
			dataSource {
			  id
			  title
			}
			synonym
			taxonId        
			classification {
			  path
			}
			acceptedName {
			  name {
				value
			  }
			}
			matchType {
			  kind
			  score
			}
		  }
		}
	  }
	}
	';

	$url = 'https://index.globalnames.org/api/graphql';
	
	// echo json_encode($data);

	$json = post($url, json_encode($data), 'application/json');

	$response = json_decode($json);
	
	if (json_last_error() == JSON_ERROR_NONE)
	{
		return $response;
	}
	else
	{
		return null;
	}

}

//----------------------------------------------------------------------------------------
function global_names_verifier($query)
{
	// construct a POST object
	
	$data = new stdclass;
	$data->nameStrings = array();
	
	foreach ($query as $q)
	{
		$data->nameStrings[] = $q;
	}	
	
	$data->dataSources = array(11); // GBIF

	$url = 'https://verifier.globalnames.org/api/v1/verifications';
	
	//echo json_encode($data);

	$json = post($url, json_encode($data), 'application/json');

	$response = json_decode($json);
	
	if (json_last_error() == JSON_ERROR_NONE)
	{
		return $response;
	}
	else
	{
		return null;
	}

}


// test
if (0)
{

// post
function post($url, $data = '', $content_type = '')
{
	
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data);  
	
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	
	if ($content_type != '')
	{
		curl_setopt($ch, CURLOPT_HTTPHEADER, 
			array(
				"Content-type: " . $content_type
				)
			);
	}	
	
	$response = curl_exec($ch);
	if($response == FALSE) 
	{
		$errorText = curl_error($ch);
		curl_close($ch);
		die($errorText);
	}
	
	$info = curl_getinfo($ch);
	$http_code = $info['http_code'];
		
	curl_close($ch);
	
	return $response;
}

	$result = global_names_verifier(['Pinnotheres']);
	
	print_r($result);
}


?>