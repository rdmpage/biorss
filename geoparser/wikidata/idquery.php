<?php



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
function query($ids, $first = false)
{
	// Query template

	$sparql = 'SELECT ?wikidata_id ?name ?enwiki_title (GROUP_CONCAT(?other;SEPARATOR="|") AS ?alternate_names) ?country_code ?latitude  ?longitude ?geonames_id ?osm_id WHERE {
	  VALUES ?item { <QID> }
	  ?item wdt:P17 ?country
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
	GROUP BY ?wikidata_id ?name ?enwiki_title ?country_code ?latitude  ?longitude ?geonames_id ?osm_id';


	$values = join(" ", $ids);	
	$values = str_replace('Q', 'wd:Q', $values);
	
	$sparql = str_replace('<QID>', $values, $sparql);
	
	$url = 'https://query.wikidata.org/bigdata/namespace/wdq/sparql?query=' . urlencode($sparql);
	$output = get($url, '', 'text/csv');
	
	// need to ensure we only output header once
	$rows = explode("\n", $output);
	
	if ($first)
	{
		$first = false;
	}
	else
	{
		array_shift($rows);
	}
	
	echo join("\n", $rows);

}


//----------------------------------------------------------------------------------------


/*
SELECT ?id 
WHERE {
  # islands
  ?item wdt:P31 wd:Q23442.
  
   BIND( REPLACE( STR(?item),"http://www.wikidata.org/entity/","" ) AS ?id). 
  
  # bigger than 50000 km^2
  ?item wdt:P2046 ?area .
  FILTER(?area > 50000) .
}
*/


// List of ids that match a query

// islands
$ids = array(Q36117,
Q40285,
Q48335,
Q59771,
Q3492,
Q3757,
Q3812,
Q7792,
Q13987,
Q13989,
Q13991,
Q21162,
Q22502,
Q22890,
Q23666,
Q25277,
Q81178,
Q83067,
Q118863,
Q120755,
Q124873,
Q125384,
Q134116,
Q143246,
Q146841,
Q148440,
Q158129,
Q170479,
Q178124,
Q185532,
Q186367,
Q186841,
Q188428,
Q188489,
Q193264,
Q200223,
Q200226,
Q201398,
Q206368,
Q207374,
Q207666,
Q207751,
Q207925,
Q208198,
Q211800,
Q1061269,
Q1081204,
Q1145547,
Q1147435,
Q841059,
Q849318,
Q849318,
Q852433,
Q212219,
Q212777,
Q212806,
Q213146,
Q215001,
Q217362,
Q217369,
Q217372,
Q219737,
Q242176,
Q256293,
Q282059,
Q329452,
Q332359,
Q583990,
Q586657,
Q645180,
Q648391,
Q650720,
Q662158,
Q1362330,
Q1432983,
Q1674067,
Q1861129,
Q3593036,
Q3593416,
Q3740828,
Q3760293,
Q3802876,
Q2905619,
Q3010627,
Q4148644,
Q4206742,
Q4526612,
Q6081502,
Q4821459,
Q4951156,
Q7463928,
Q11261050,
Q12608568,
Q21894028,
Q22431567,
Q22431682,
Q22431813,
Q22431870,
Q22486559,
Q22518024,
Q22518040,
Q22560390,
Q22560400,
Q22561725,
Q22627494,
Q22656324,
Q25193815,
Q27508031,
Q30740800,
Q32510077,
Q66213874);



// missed/interesting
$ids=array(
Q3323094, // Nakanai Mountains
Q143014, // Cordillera Oriental
);



$first = true;

$chunk_size = 20;

$chunks = array_chunk($ids, $chunk_size);

foreach ($chunks as $chunk)
{
	// print_r($chunk);
	
	query($chunk, $first);
	
	$first = false;
}

?>
