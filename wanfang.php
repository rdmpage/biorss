<?php

// Wangang

require_once (dirname(__FILE__) . '/rss.php');

/*
{
"detail": [
	{
		"periodical": {
			"Id": "zxxb202101001",
			"Title": [
				"中国延斑蛛属4新种(蜘蛛目:古筛蛛科)",
				"Four new species of the genus Ectatosticta(Araneae,Hypochilidae)from China"
			],
			"Creator": [
				"林业杰",
				"李枢强"
			],
			"FirstCreator": "林业杰",
			"ScholarIdAuthor": [],
			"ScholarId": [],
			"ForeignCreator": [
				"LIN Ye-Jie",
				"LI Shu-Qiang"
			],
			"CreatorForSearch": [],
			"OrganizationNorm": [
				"中国科学院动物研究所"
			],
			"OrganizationNew": [
				"中国科学院动物研究所"
			],
			"OriginalOrganization": [
				"中国科学院动物研究所,北京100101"
			],
			"OrganizationForSearch": [],
			"OriginalClassCode": [],
			"MachinedClassCode": [],
			"ClassCodeForSearch": [],
			"PeriodicalClassCode": [
				"NQ"
			],
			"ContentSearch": [],
			"Keywords": [
				"鉴别特征",
				"词源学",
				"分类",
				"模式标本",
				"蛛网"
			],
			"ForeignKeywords": [
				"diagnosis",
				"etymology",
				"taxonomy",
				"type",
				"webs"
			],
			"MachinedKeywords": [],
			"KeywordForSearch": [],
			"Abstract": [
				"本文报道了中国延斑蛛属(Ectatosticta Simon,1892)4新种:八戒延斑蛛,新种E.bajie sp.nov.(♂♀);大鹏延斑蛛,新种 E.dapeng sp.nov.(♂♀);如来延斑蛛,新种 E.rulai sp.nov.(♂♀);余锟延斑蛛,新种E.yukuni sp.nov.(♂♀).提供了这4种延斑蛛的描述、照片以及详细的鉴别特征.",
				"Four new species of the spider genus Ectatosticta Simon,1892 are reported,diagnosed,described and illustrated.The new species are E.bajie sp.nov.(♂♀),E.dapeng sp.nov.(♂♀),E.rulai sp.nov.(♂♀)and E.yukuni sp.nov.(♂♀)."
			],
			"CitedCount": 0,
			"PeriodicalId": "zxxb",
			"PeriodicalTitleForSearch": [],
			"PeriodicalTitle": [
				"蛛形学报",
				"Acta Arachnologica Sinica"
			],
			"SourceDB": [
				"WF"
			],
			"SingleSourceDB": "WF",
			"IsOA": false,
			"Fund": [],
			"PublishDate": "2021-01-01 00:00:00",
			"MetadataOnlineDate": "2021-07-20 00:00:00",
			"FulltextOnlineDate": "2021-07-20 00:00:00",
			"ServiceMode": 1,
			"HasFulltext": true,
			"PublishYear": 2021,
			"Issue": "1",
			"Volum": "30",
			"Page": "1-8",
			"PageNo": "8",
			"Column": [],
			"CorePeriodical": [],
			"FulltextPath": "zxxb/zxxb2021/2101pdf/210101.pdf",
			"DOI": "10.3969/j.issn.1005-9628.2021.01.001",
			"AuthorOrg": [
				"李枢强:中国科学院动物研究所",
				"林业杰:中国科学院动物研究所"
			],
			"ThirdPartyUrl": [],
			"Language": "chi",
			"ISSN": "1005-9628",
			"CN": "42-1376/Q",
			"SequenceInIssue": 1,
			"MetadataViewCount": 24,
			"ThirdpartyLinkClickCount": 0,
			"DownloadCount": 7,
			"ExportCount": 0,
			"PrePublishVersion": "",
			"PrePublishGroupId": "",
			"PublishStatus": "Regular",
			"Type": "Periodical",
			"ProjectId": [],
			"FundGroupName": [],
			"ProjectGrantNo": [],
			"history": [],
			"HighLight": {},
			"ResourceType": "",
			"Original": [],
			"ButtonStatus": {
				"Copyright": "true"
			}
		}
	}
],
"extraData": {
	"Status": "SUCCESS"
},
"total": 0
}
	*/


//----------------------------------------------------------------------------------------
// post
function post($url, $data = '', $content_type = '')
{
	
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data);  
	
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	
	if ($content_type != '')
	{
		curl_setopt($ch, CURLOPT_HTTPHEADER, 
			array(
				"Content-type: " . $content_type
				)
			);
	}	
	
	$response = curl_exec($ch);
	if($response == FALSE) 
	{
		$errorText = curl_error($ch);
		curl_close($ch);
		die($errorText);
	}
	
	$info = curl_getinfo($ch);
	$http_code = $info['http_code'];
		
	curl_close($ch);
	
	return $response;
}


$periodicals = array(
/*
	// Acta Arachnologica Sinica
	'zxxb' => array(
				'zxxb202101001',
				'zxxb202101002'
			),
			
	// Acta Entomologica Sinica
	'kcxb' => array(
				'kcxb201601004'
			),
			
	// Acta Zootaxonomica Sinica
	'dwfl' => array(
				'dwfl202102003'
			),
			*/
			
	// Acta Botanica Boreali-Occidentalia Sinica
	'xbzwxb' => array(
				'xbzwxb201601006'
			),			


);

/*

older journals

	'zzyjhk' => array(
				'zzyjhk201501003'
			),

*/

	


foreach ($periodicals as $journal => $ids)
{
	$dataFeed = new stdclass;
	$dataFeed->{'@context'} = 'http://schema.org/';
	$dataFeed->{'@type'} = 'DataFeed';
	
	
	$dataFeed->dataFeedElement = array();

	foreach ($ids as $id)
	{
		$data = new stdclass;
		$data->Id = $id;
	
		$url = 'https://d.wanfangdata.com.cn/Detail/Periodical/';
	
		$json = post($url, json_encode($data));
	
		$obj = json_decode($json);
	
		// print_r($obj);
	
		foreach ($obj->detail[0] as $item)
		{
			$dataFeedElement = new stdclass;
			$dataFeedElement->{'@type'} = 'DataFeedItem';
		
			$dataFeedElement->url = 'https://d.wanfangdata.com.cn/periodical/' . $id;
			$dataFeedElement->{'@id'} = $dataFeedElement->url;
		
			$dataFeedElement->name = join(' / ', $item->Title);
			$dataFeedElement->description = join(' / ', $item->Abstract);
				
			$dataFeedElement->datePublished = $item->PublishDate;
		
			// name whole feed based on the journal
			if (!isset($dataFeed->name))
			{			
				$dataFeed->name = join(' / ', $item->PeriodicalTitle);
			}

			// make up a feed url
			if (!isset($dataFeed->url))
			{			
				$dataFeed->url = 'https://www.wanfangdata.com.cn/perio/detail.do?perio_id='
					. $journal . '&perio_title=' . $item->PeriodicalTitle[0];
			}
				
			if (isset($item->DOI))
			{
				$dataFeedElement->doi = $item->DOI;
			}
			
		
			print_r($dataFeedElement);
		
			$dataFeed->dataFeedElement[] = $dataFeedElement;	
		}
	}

	print_r($dataFeed);

	$xml = internal_to_rss($dataFeed);

	echo $xml;
}

?>
