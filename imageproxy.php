<?php

// Image proxy

$url = '';
if (isset($_GET['url']))
{
	$url = $_GET['url'];
}

if ($url != '')
{
	// Does image exist?
	
	$url = str_replace(' ', '%20', $url);
	
	
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0); 
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); 
	
	  	
	$response = curl_exec($ch);
	if($response == FALSE) 
	{
		$errorText = curl_error($ch);
		curl_close($ch);
		die($errorText);
	}
	
	$info = curl_getinfo($ch);
	
	if (0)
	{
		echo '<pre>';
		print_r($info);
		echo '</pre>';
		exit();
	}


	
	$http_code = $info['http_code'];
	
	curl_close($ch);
		
	if ($http_code == 200)
	{
		// redirect to image URL
		header("Location: $url\n");
		exit();
	}
	else
	{
		// local blank image
		header("Location: images/no-icon.svg\n");
		exit();
	
	}

}


?>

