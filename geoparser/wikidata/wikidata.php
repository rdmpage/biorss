<?php

// SPARQL queries to extract localitles form Wikidata

error_reporting(E_ALL);

mb_internal_encoding("UTF-8");

//----------------------------------------------------------------------------------------
function get($url, $user_agent='', $content_type = '')
{	
	$data = null;

	$opts = array(
	  CURLOPT_URL =>$url,
	  CURLOPT_FOLLOWLOCATION => TRUE,
	  CURLOPT_RETURNTRANSFER => TRUE
	);

	if ($content_type != '')
	{
		
		$opts[CURLOPT_HTTPHEADER] = array(
			"Accept: " . $content_type, 
			"User-agent: Mozilla/5.0 (iPad; U; CPU OS 3_2_1 like Mac OS X; en-us) AppleWebKit/531.21.10 (KHTML, like Gecko) Mobile/7B405" 
		);
		
	}
	
	$ch = curl_init();
	curl_setopt_array($ch, $opts);
	$data = curl_exec($ch);
	$info = curl_getinfo($ch); 
	curl_close($ch);
	
	return $data;
}

//----------------------------------------------------------------------------------------


$queries = array(
'countries' => 'SELECT ?wikidata_id ?name ?enwiki_title (GROUP_CONCAT(?other;SEPARATOR="|") AS ?alternate_names) ?country_code ?latitude  ?longitude ?geonames_id ?osm_id WHERE {
  VALUES ?type { wd:Q6256 }
  
  # id
  ?item wdt:P31 ?type .
  BIND( REPLACE( STR(?item),"http://www.wikidata.org/entity/","" ) AS ?wikidata_id). 
   
  # name
  ?item rdfs:label ?name .
  
  {
    ?item rdfs:label ?other .
     FILTER((LANG(?other)) IN ("fr","zh","es","jp","de"))
  }
  
  OPTIONAL { 
    ?sitelink schema:about ?item;
    schema:isPartOf <https://en.wikipedia.org/>;
    schema:name ?enwiki_title.
           }
  
  # ISO code
  ?item wdt:P297 ?country_code.
  
  # coordinates
  ?item p:P625 ?statement .
  ?statement psv:P625 ?coordinate_node .
  ?coordinate_node wikibase:geoLatitude ?lat . 
  BIND(xsd:float(?lat) AS ?latitude) .
  ?coordinate_node wikibase:geoLongitude ?lon .    
  BIND(xsd:float(?lon) AS ?longitude) .
  
  OPTIONAL { ?item wdt:P1566 ?geonames_id. }
  OPTIONAL { ?item wdt:P402 ?osm_id. }
  
  FILTER((LANG(?name)) = "en")
}
GROUP BY ?wikidata_id ?name ?enwiki_title ?country_code ?latitude  ?longitude ?geonames_id ?osm_id
',
'adm1' => 'SELECT ?wikidata_id ?name ?enwiki_title (GROUP_CONCAT(?other;SEPARATOR="|") AS ?alternate_names) ?country_code ?latitude  ?longitude ?geonames_id ?osm_id WHERE {
 
   ?country wdt:P31 wd:Q6256.
   ?country wdt:P150 ?item . #adm1
 
  # id
   BIND( REPLACE( STR(?item),"http://www.wikidata.org/entity/","" ) AS ?wikidata_id). 
   
     {
    ?item rdfs:label ?other .
     FILTER((LANG(?other)) IN ("fr","zh","es","jp","de"))
  }
  
  OPTIONAL { 
    ?sitelink schema:about ?item;
    schema:isPartOf <https://en.wikipedia.org/>;
    schema:name ?enwiki_title.
           }
  
  # ISO code
  ?country wdt:P297 ?country_code.
  
  # coordinates
  ?item p:P625 ?statement .
  ?statement psv:P625 ?coordinate_node .
  ?coordinate_node wikibase:geoLatitude ?lat . 
  BIND(xsd:float(?lat) AS ?latitude) .
  ?coordinate_node wikibase:geoLongitude ?lon .    
  BIND(xsd:float(?lon) AS ?longitude) .
  
  OPTIONAL { ?item wdt:P1566 ?geonames_id. }
  OPTIONAL { ?item wdt:P402 ?osm_id. }
  
  # name
  ?item rdfs:label ?name .
 
  FILTER((LANG(?name)) = "en")
}
GROUP BY ?wikidata_id ?name ?enwiki_title ?country_code ?latitude  ?longitude ?geonames_id ?osm_id',

);

$force = false;
$force = true;

foreach ($queries as $name => $sparql)
{
	echo "Query: $name\n";
	$filename = $name . '.csv';
	
	if (!file_exists($filename) || $force)
	{
		echo "  -- querying Wikidata...\n";
		$url = 'https://query.wikidata.org/bigdata/namespace/wdq/sparql?query=' . urlencode($sparql);
		$output = get($url, '', 'text/csv');
		file_put_contents($filename, $output);
		echo "  -- done.\n";
	}
	else
	{
		echo "  -- have already.\n";
	}
}

?>
