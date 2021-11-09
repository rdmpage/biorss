<?php

require_once (dirname(__FILE__) . '/couchsimple.php');

//----------------------------------------------------------------------------------------

function get_facets()
{
	// Get URL parameters so we know what to display and what happens when 
	// visitor changes view. Provide defaults if none supplied. Interface depends on
	// always knowing geographic and taxonomic facets

	$url_parameters = $_GET;

	// What country are we viewing?

	$country = 'CN'; // Provide a default

	if (isset($url_parameters['country']))
	{
		$country = $url_parameters['country'];
	}
	else
	{
		$url_parameters['country'] = $country;
	}

	// For country determine enclosing "region" (sensu Google) to help map display

	$region = '';

	if (isset($url_parameters['region']))
	{
		$region = $url_parameters['region'];
	}

	// Taxonomic classification

	$path = '["BIOTA","Animalia","Chordata"]'; 
	$path = '["BIOTA"]'; // default

	if (isset($url_parameters['path']))
	{
		$path = $url_parameters['path'];
	}
	else
	{
		$url_parameters['path'] = $path;
	}
	
	return $url_parameters;

}

//----------------------------------------------------------------------------------------
// This should be an API call
function do_query($url_parameters)
{
	global $config;
	global $couch;

	$key = array();

	$key[] = $url_parameters['country'];
	$key[] = join("-", json_decode($url_parameters['path']));

	$startkey = $key;
	$endkey = $key;
	$startkey[] = new stdclass;
		
	$url = '_design/key/_view/query?startkey=' . urlencode(json_encode($startkey))
		. '&endkey=' .  urlencode(json_encode($endkey))
		. '&descending=true'
		;
		
	echo urldecode($url) . "\n";
	
	$resp = $couch->send("GET", "/" . $config['couchdb_options']['database'] . "/" . $url);

	$response_obj = json_decode($resp);
	
	return $response_obj;
}

$url_parameters = get_facets();
$obj = do_query($url_parameters);

?>
<html>
<head>
	<head>
		<meta charset="utf-8" /> 
		
		<title>
			BioRSS
		</title>		
		
		<!--Import Google Icon Font-->
		<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
	
		  <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.4/jquery.js"></script>
		  <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.css">
		  <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.js"></script>
	
	   <!--Let browser know website is optimized for mobile-->
		<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
	

		
		<style></style>		
</head>
<body>
<?php

/*
echo '<pre>';
print_r($obj);
echo '</pre>';
*/


foreach ($obj->rows as $row)
{
	echo '<div class="row">
	<div class="col s12 m8">
        <div class="card">
          <div class="row">';
          echo '<div class="card-content">';
          
    // image
    echo '<div class="col s3">';
	if (isset($row->value->image))
	{
		echo '<img class="responsive-img circle" src="'. $row->value->image . '">';
	}
    echo ' </div>  <!-- image -->';
    
    // content
    echo '<div class="col s9">';
    
	echo '	<div class="card-title">' . $row->value->name . '</div>';
	
	$host = parse_url($row->value->url, PHP_URL_HOST);
	
	echo  '	<div><a href="' . $row->value->url . '">' . $host . '</a></div>';
	//' . distanceOfTimeInWords(strtotime($row->doc->message->isoDate) ,time()) . '</span><br/>';
	
	
	if (isset($row->value->description))
	{
		echo '<div>' . $row->value->description . '</div>';
	}
	echo '</div>';
	
	echo '</div>   <!-- col s10 -->
          </div> <!-- row -->
        </div> <!-- card -->
      </div> <!-- col -->
</div> <!-- row -->';
}



?>
</body>
</html>