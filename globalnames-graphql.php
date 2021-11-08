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


// test
if (0)
{
	
}


?>