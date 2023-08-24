<?php

// Fix any broken OU images

error_reporting(E_ALL);

require_once (dirname(__FILE__) . '/couchsimple.php');
require_once (dirname(__FILE__) . '/datastore.php');
require_once (dirname(__FILE__) . '/utils.php');
require_once (dirname(__FILE__) . '/base64.php');


//----------------------------------------------------------------------------------------
function get($url, $user_agent='', $content_type = '')
{	
	$data = null;

	$opts = array(
	  CURLOPT_URL =>$url,
	  CURLOPT_FOLLOWLOCATION => TRUE,
	  CURLOPT_RETURNTRANSFER => TRUE,
	  
		CURLOPT_SSL_VERIFYHOST=> FALSE,
		CURLOPT_SSL_VERIFYPEER=> FALSE,
	  
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


echo "Getting broken images...\n";


$limit = 1000;

$url = '_design/queue/_view/oup_image'
	. '?descending=true'
	. '&include_docs=true'
	. '&limit=' . $limit
	;

$resp = $couch->send("GET", "/" . $config['couchdb_options']['database'] . "/" . $url);

$obj = json_decode($resp);


foreach ($obj->rows as $row)
{
	$dataFeedElement = $row->doc;
	
	echo "Fixing image...\n";
	
	$dataFeedElement->message->thumbnailUrl ='https://twitter.com/OUPAcademic/photo';
	$image = encode_from_file(dirname(__FILE__) . '/images/twitter_UeOSnJ6X_400x400.png');		
		
	$dataFeedElement->message->image = $image;

	store($dataFeedElement->message);

}




?>





