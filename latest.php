<?php

// Make "latest" folder
require_once(dirname(__FILE__) . '/config.inc.php');

// Where shall we store the feeds?
$today = date('Y-m-d', time());
$cache_dir = $config['cache'] . '/' . $today;
$latest_dir = $config['cache'] . '/latest';

if (!file_exists($cache_dir))
{
	echo "Creating \"$today\"\n";
	$oldumask = umask(0); 
	mkdir($cache_dir, 0777);
	umask($oldumask);
}	

if (file_exists($latest_dir))
{
	echo "Delete old \"latest\"\n";
	unlink($latest_dir);
}	

echo "Create \"latest\"\n";
symlink($cache_dir, $latest_dir);

?>
