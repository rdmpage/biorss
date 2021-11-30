<?php

error_reporting(E_ALL);

require_once (dirname(__FILE__) . '/trie.php');

// load serialize object
$filename = dirname(__FILE__) . '/trie.dat';
$data = file_get_contents($filename);
$trie = unserialize($data);

$filename = 'trie.dot';
file_put_contents($filename, $trie->toDot());


?>

