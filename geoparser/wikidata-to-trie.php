<?php

require_once(dirname(__FILE__) . '/trie.php');

//----------------------------------------------------------------------------------------
// http://stackoverflow.com/a/5996888/9684
function translate_quoted($string) {
  $search  = array("\\t", "\\n", "\\r");
  $replace = array( "\t",  "\n",  "\r");
  return str_replace($search, $replace, $string);
}

//----------------------------------------------------------------------------------------

$trie = new Trie();

// parse all CSV files in folder

$basedir = dirname(__FILE__) . '/wikidata';

$files = scandir($basedir);

foreach ($files as $filename)
{
	if (preg_match('/\.csv$/', $filename))
	{	
		echo $filename . "\n";
	
		$filename = $basedir . '/' . $filename;
	
		$headings = array();

		$row_count = 0;

		$file = @fopen($filename, "r") or die("couldn't open $filename");
		
		$file_handle = fopen($filename, "r");
		while (!feof($file_handle)) 
		{
			$row = fgetcsv(
				$file_handle, 
				0, 
				translate_quoted(','),
				translate_quoted('"') 
				);
		
			$go = is_array($row) && count($row) > 1;
	
			if ($go)
			{
				if ($row_count == 0)
				{
					$headings = $row;		
				}
				else
				{
					$obj = new stdclass;
		
					foreach ($row as $k => $v)
					{
						if ($v != '')
						{
							switch ($headings[$k])
							{
								// ensure coordinates are treated as numbers in JSON
								case 'longitude':
								case 'latitude':
									$obj->{$headings[$k]} = floatval($v);
									break;
					
								default:
									$obj->{$headings[$k]} = $v;
									break;							
							}
					
						}
					}
					
					// print_r($obj);
					
					$trie->add($obj, false);
				}
			}	
			$row_count++;
		}
		
		fclose($file_handle);
		
	}
}

// store data
$filename = 'trie.dat';
file_put_contents($filename, serialize($trie));

$filename = 'trie.dot';
file_put_contents($filename, $trie->toDot());



?>
