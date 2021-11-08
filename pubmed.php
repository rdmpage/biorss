<?php

// Pubmed search from scratch

error_reporting(E_ALL);

require_once (dirname(__FILE__) . '/config.inc.php');

use Sunra\PhpSimple\HtmlDomParser;

//----------------------------------------------------------------------------------------
function get($url, $accept = "text/html")
{
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	
	// Cookies 
	//curl_setopt($ch, CURLOPT_COOKIEJAR, sys_get_temp_dir() . '/cookies.txt');
	//curl_setopt($ch, CURLOPT_COOKIEFILE, sys_get_temp_dir() . '/cookies.txt');	
	
	/*
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
		"Accept: " . $accept,
		"Accept-Language: en-gb",
		"User-agent: Mozilla/5.0 (iPad; U; CPU OS 3_2_1 like Mac OS X; en-us) AppleWebKit/531.21.10 (KHTML, like Gecko) Mobile/7B405" 	
		));
	*/
	
	$response = curl_exec($ch);
	
	
	if($response == FALSE) 
	{
		$errorText = curl_error($ch);
		curl_close($ch);
		//die($errorText);
		return "";
	}
	
	$info = curl_getinfo($ch);
	$http_code = $info['http_code'];
	
	//print_r($info);
		
	curl_close($ch);
	
	return $response;
}

$dataFeed = new stdclass;
$dataFeed->{'@context'} = 'http://schema.org/';
$dataFeed->{'@type'} = 'DataFeed';

$dataFeed->name = "PubMed";
$dataFeed->url = 'https://pubmed.ncbi.nlm.nih.gov';

$dataFeed->dataFeedElement = array();

$query = '("new species") OR ("n. sp.") OR ("sp. nov.") OR ("n. gen.") OR ("gen. nov.") OR ("n. comb.") OR ("comb. nov.")';

$dataFeed->name = "PubMed " . $query;

// 1. Get pmids
$parameters = array(
	'api_key' 	=> getenv('NCBI_API_KEY'),
	'datetype' 	=> 'pdat',
	'reldate' 	=> '7',
	'retmode' 	=> 'json',
	'term' 		=> $query,
);

$url = 'https://eutils.ncbi.nlm.nih.gov/entrez/eutils/esearch.fcgi';

$url .= '?' . http_build_query($parameters);

$json = get($url);

$obj = json_decode($json);

if(json_last_error() == JSON_ERROR_NONE)
{
	// print_r($obj);
	
	// 2. get docs
	
	foreach ($obj->esearchresult->idlist as $id)
	{
		// Get article in XML as we want the abstract 		
		$parameters = array(
			'api_key' 	=> getenv('NCBI_API_KEY'),
			'db' 		=> 'pubmed',
			'retmode' 	=> 'xml',
			'id' 		=> $id,
		);
		
		$url = 'https://eutils.ncbi.nlm.nih.gov/entrez/eutils/efetch.fcgi';

		$url .= '?' . http_build_query($parameters);

		$xml = get($url);

		// extract data from PubMed XML
		
				
		$dom = new DOMDocument;
		$dom->loadXML($xml, LIBXML_NOCDATA); // Elsevier wraps text in <![CDATA[ ... ]]>
		$xpath = new DOMXPath($dom);

		$dataFeedElement = new stdclass;
		$dataFeedElement->{'@type'} = 'DataFeedItem';
		
		foreach ($xpath->query('//Article') as $article)
		{			
			foreach ($xpath->query('ArticleTitle', $article) as $node)
			{
				$dataFeedElement->name = $node->firstChild->nodeValue;
			}

			foreach ($xpath->query('Abstract/AbstractText', $article) as $node)
			{
				$dataFeedElement->description = $node->firstChild->nodeValue;
			}
			
		}		

		foreach ($xpath->query('//PubmedData') as $pubmeddata)
		{
					
			// DOI
			foreach ($xpath->query('ArticleIdList/ArticleId[@IdType="doi"]', $pubmeddata) as $node)
			{
				$dataFeedElement->doi = $node->firstChild->nodeValue;
			}
			
			// PMID
			foreach ($xpath->query('ArticleIdList/ArticleId[@IdType="pubmed"]', $pubmeddata) as $node)
			{
				$dataFeedElement->url = 'https://pubmed.ncbi.nlm.nih.gov/' . $node->firstChild->nodeValue;
				$dataFeedElement->{'@id'} = $dataFeedElement->url;
			}
			
			// Date
			foreach ($xpath->query('History/PubMedPubDate[@PubStatus="pubmed"]', $pubmeddata) as $history)
			{
				$date = array();
				
				foreach ($xpath->query('Year', $history) as $node)
				{
					$date[] = $node->firstChild->nodeValue;
				}
				foreach ($xpath->query('Month', $history) as $node)
				{
					$date[] = str_pad($node->firstChild->nodeValue, 2, '0', STR_PAD_LEFT);
				}
				foreach ($xpath->query('Day', $history) as $node)
				{
					$date[] = str_pad($node->firstChild->nodeValue, 2, '0', STR_PAD_LEFT);
				}
				
				$dataFeedElement->datePublished = join('-', $date);
			}
			
		}	
		
		$dataFeed->dataFeedElement[] = $dataFeedElement;	
		
		
	}
}

// print_r($dataFeed);

$xml = internal_to_rss($dataFeed);

echo $xml;



?>
