<?php

require_once (dirname(__FILE__) . '/augment.php');

$status = 200;

$doc = get_doc(false);
if ($doc)
{
	$status = add_meta($doc, $status);
}
else
{
	$status = 500;
}

send_doc($doc, $status);


?>


