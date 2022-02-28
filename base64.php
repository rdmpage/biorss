<?php

// Encode an image in base64, e.g. 'images/未命名1.jpg';

function encode_from_file($image_file_name)
{
	// get MIME type
	$finfo = finfo_open(FILEINFO_MIME_TYPE); // return mime type ala mimetype extension
	$mime_type = finfo_file($finfo, $image_file_name);
	finfo_close($finfo);

	// resize
	$command = 'mogrify -resize 128 ' . $image_file_name;
	system($command);

	// encode 			
	$image = file_get_contents($image_file_name);
	$base64 = chunk_split(base64_encode($image));

	return 'data:' . $mime_type . ';base64,' . $base64;	
}

?>


