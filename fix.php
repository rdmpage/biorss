<?php

error_reporting(E_ALL);

require_once (dirname(__FILE__) . '/couchsimple.php');
require_once (dirname(__FILE__) . '/base64.php');

// get records for a journal/source and fix/edit/etc.

$domain = 'xbkcflxb.alljournal.net';
$domain = 'pubmed.ncbi.nlm.nih.gov';
$domain = 'europeanjournaloftaxonomy.eu';
$domain = 'ojs.mtak.hu';

$url = '_design/key/_view/domain?key=' . urlencode('"' . $domain . '"');

$url .= '&reduce=false';
$url .= '&include_docs=true';

$resp = $couch->send("GET", "/" . $config['couchdb_options']['database'] . "/" . $url);
$obj = json_decode($resp);

// print_r($obj);

foreach ($obj->rows as $row)
{
	$doc = $row->doc;
	
	print_r($doc);
	
	$modified = false;
	
	
	// fix an image
	if (!isset($doc->message->thumbnailUrl) || !isset($doc->message->image))
	{
		//echo $doc->message->thumbnailUrl . "\n";
		
		//$image = encode_from_file(dirname(__FILE__) . '/images/未命名1.jpg');
		
		// pubmed.ncbi.nlm.nih.gov'
		//$doc->message->thumbnailUrl = 'https://cdn.ncbi.nlm.nih.gov/pubmed/persistent/pubmed-meta-image.png';
		//$image = encode_from_file(dirname(__FILE__) . '/images/pubmed-meta-image.png');

		// europeanjournaloftaxonomy.eu
		//$doc->message->thumbnailUrl = 'https://pbs.twimg.com/profile_images/1233042952236257281/3cZ7IjEE_400x400.jpg';
		//$image = encode_from_file(dirname(__FILE__) . '/images/3cZ7IjEE_400x400.jpg');		

		// ojs.mtak.hu  
		$doc->message->thumbnailUrl = 'https://scontent-lcy1-1.xx.fbcdn.net/v/t31.18172-8/23593534_1199898190140077_1648694872519145048_o.jpg?_nc_cat=104&ccb=1-5&_nc_sid=09cbfe&_nc_ohc=iKwkuSZJksoAX9vHvhM&tn=fI5WyTjQc7u8lJzM&_nc_ht=scontent-lcy1-1.xx&oh=00_AT-M7NtARPjMfmqeOzrnH_cdQ_mpj2H0-J2NtNnz9A_6zA&oe=623B1AF9';
		$image = encode_from_file(dirname(__FILE__) . '/images/23593534_1199898190140077_1648694872519145048_o.jpg');		
			
		$doc->message->image = $image;
		
		$modified = true;		
	}
	
	
	// update if modified
	if ($modified)
	{
		//print_r($doc);
				
		// update
		$doc->{'message-modified'}  = date("c", time()); // now
		$resp = $couch->send("PUT", "/" . $config['couchdb_options']['database'] . "/" . urlencode($doc->_id), json_encode($doc));	
		
		print_r($resp);	
		
	}


}

	



?>



