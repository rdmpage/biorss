<?php

// Cross links between publications, sequences, and identifiers in GenBank

error_reporting(E_ALL);

$api_key = '56b7d0c702af1e3fa9e863c48a53cfc89b09';

$MedAbbr = array(
'0044-586X' => 'Acarologia',
'0567-7920' => 'Acta Palaeontol Pol',
'1230-2821' => 'Acta Parasitol',
'0065-1583' => 'Acta Protozool',
'0001-7280' => 'Acta Zool Pathol Antverp',
'0002-9122' => 'Am J Bot',
'0096-5294' => 'Am J Hyg',
'0002-9483' => 'Am J Phys Anthropol',
'0275-2565' => 'Am J Primatol',
'0002-9599' => 'Am J Sci',
'0740-2783' => 'Am Malacol Bull',
'0001-3765' => 'An Acad Bras Cienc',
'0003-3162' => 'Angew Parasitol',
'1570-7555' => 'Anim Biol Leiden Neth',
'1976-8354' => 'Animal Cells Syst (Seoul)',
'0003-4746' => 'Ann Appl Biol',
'0013-8746' => 'Ann Entomol Soc Am',
'0077-8923' => 'Ann N Y Acad Sci',
'0003-4150' => 'Ann Parasitol Hum Comp',
'0003-4983' => 'Ann Trop Med Parasitol',
'0003-5092' => 'Annot Zool Jpn',
'0954-1020' => 'Antarct Sci',
'0065-9452' => 'Anthropol Pap Am Mus Nat Hist',
'0044-8486' => 'Aquaculture',
'1467-8039' => 'Arthropod Struct Dev',
'0004-8038' => 'Auk',
'0004-959X' => 'Aust J Zool',
'1030-1887' => 'Aust Syst Bot',
'0005-2086' => 'Avian Dis',
'0005-7959' => 'Behaviour',
'0305-1978' => 'Biochem Syst Ecol',
'0960-3115' => 'Biodivers Conserv',
'0006-3185' => 'Biol Bull',
'0024-4066' => 'Biol J Linn Soc Lond',
'1744-9561' => 'Biol Lett',
'1464-7931' => 'Biol Rev Camb Philos Soc',
'0006-3304' => 'Biol Zent Bl',
'0006-3088' => 'Biologia (Bratisl)',
'0006-3568' => 'Bioscience',
'0006-3606' => 'Biotropica',
'0037-850X' => 'Bol Soc Biol Concepc',
'0024-4074' => 'Bot J Linn Soc',
'1519-6984' => 'Braz J Biol',
'0007-4187' => 'Bull Biol Fr Belg',
'0007-4853' => 'Bull Entomol Res',
'0007-5167' => 'Bull Zool Nomencl',
'0567-655X' => 'C R Acad Sci Hebd Seances Acad Sci D',
'0764-4469' => 'C R Acad Sci III',
'1631-0691' => 'C R Biol',
'0001-4036' => 'C R Hebd Seances Acad Sci',
'0567-655X' => 'C R Seances Acad Sci D',
'0037-9026' => 'C R Seances Soc Biol Fil',
'0008-347X' => 'Can Entomol',
'0008-4026' => 'Can J Bot',
'0008-4077' => 'Can J Earth Sci',
'0008-4301' => 'Can J Zool',
'1001-6538' => 'Chin Sci Bull',
'0009-5915' => 'Chromosoma',
'0748-3007' => 'Cladistics',
'0010-406X' => 'Comp Biochem Physiol',
'0010-5422' => 'Condor',
'1566-0621' => 'Conserv Genet',
'0045-8511' => 'Copeia',
'0960-9822' => 'Curr Biol',
'0011-3891' => 'Curr Sci',
'0967-0637' => 'Deep Sea Res Part 1 Oceanogr Res Pap',
'0967-0645' => 'Deep Sea Res Part 2 Top Stud Oceanogr',
'1608-8700' => 'Denisia',
'0177-5103' => 'Dis Aquat Organ',
'0001-7302' => 'Dong Wu Xue Bao',
'0254-5853' => 'Dongwuxue Yanjiu',
'0012-9658' => 'Ecology',
'0013-8703' => 'Entomol Exp Appl',
'0013-872X' => 'Entomol News',
'0967-0262' => 'Eur J Phycol',
'0932-4739' => 'Eur J Protistol',
'1520-541X' => 'Evol Dev',
'0014-3820' => 'Evolution',
'0168-8162' => 'Exp Appl Acarol',
'0014-4827' => 'Exp Cell Res',
'0014-4894' => 'Exp Parasitol',
'0014-4754' => 'Experientia',
'0388-788X' => 'Fish Pathol',
'0015-5683' => 'Folia Parasitol (Praha)',
'0015-5713' => 'Folia Primatol (Basel)',
'0046-5070' => 'Freshw Biol',
'0016-6707' => 'Genetica',
'0016-7568' => 'Geol Mag',
'0016-7835' => 'Geol Rundsch',
'1568-9883' => 'Harmful Algae',
'0018-067X' => 'Heredity (Edinb)',
'0268-0130' => 'Herpetol J',
'2634-1379' => 'Herpetol J',
'0018-084X' => 'Herpetol Rev',
'0891-2963' => 'Hist Biol',
'0018-8158' => 'Hydrobiologia',
'0019-1019' => 'Ibis (Lond 1859)',
'0818-9641' => 'Immunol Cell Biol',
'0971-5916' => 'Indian J Med Res',
'1672-9609' => 'Insect Sci',
'1399-560X' => 'Insect Syst Evol',
'0020-1812' => 'Insectes Soc',
'0020-7519' => 'Int J Parasitol',
'0164-0291' => 'Int J Primatol',
'1466-5026' => 'Int J Syst Evol Microbiol',
'1742-7584' => 'Int J Trop Insect Sci',
'1540-7063' => 'Integr Comp Biol',
'1077-8306' => 'Invertebr Biol',
'1445-5226' =>' Invertebr Syst',
'1447-2600' => 'Invertebr Syst',
'0021-2210' => 'Isr J Zool',
'0095-9758' => 'J Agric Res',
'8756-971X' => 'J Am Mosq Control Assoc',
'0021-8782' => 'J Anat',
'0899-7659' => 'J Aquat Anim Health',
'1226-8615' => 'J Asia Pac Entomol',
'0305-0270' => 'J Biogeogr',
'0021-9533' => 'J Cell Sci',
'0095-1137' => 'J Clin Microbiol',
'0278-0372' => 'J Crustacean Biol',
'0253-5890' => 'J Egypt Soc Parasitol',
'0013-8789' => 'J Entomol Soc South Afr',
'1066-5234' => 'J Eukaryot Microbiol',
'0022-0981' => 'J Exp Mar Bio Ecol',
'0022-1007' => 'J Exp Med',
'0022-1112' => 'J Fish Biol',
'0140-7775' => 'J Fish Dis',
'0022-149X' => 'J Helminthol',
'0022-1511' => 'J Herpetol',
'0047-2484' => 'J Hum Evol',
'0022-1899' => 'J Infect Dis',
'0022-1910' => 'J Insect Physiol',
'0022-2011' => 'J Invertebr Pathol',
'1064-7554' => 'J Mamm Evol',
'0022-2372' => 'J Mammal',
'0025-3154' => 'J Mar Biol Assoc U.K.',
'0924-7963' => 'J Mar Syst',
'0022-2585' => 'J Med Entomol',
'0022-2844' => 'J Mol Evol',
'0260-1230' => 'J Molluscan Stud',
'0362-2525' => 'J Morphol',
'0022-300X' => 'J Nematol',
'0021-8375' => 'J Ornithol',
'0022-3360' => 'J Paleontol',
'0022-3395' => 'J Parasitol',
'1612-4758' => 'J Pest Sci (2004)',
'0022-3646' => 'J Phycol',
'0142-7873' => 'J Plankton Res',
'0918-9440' => 'J Plant Res',
'0368-3974' => 'J R Microsc Soc',
'0035-922X' => 'J R Soc West Aust',
'0743-9547' => 'J Southeast Asian Earth Sci',
'0022-474X' => 'J Stored Prod Res',
'0974-7893' => 'J Threat Taxa',
'0022-5320' => 'J Ultrastruct Res',
'0043-0439' => 'J Wash Acad Sci',
'0952-8369' => 'J Zool (1987)',
'0021-5171' => 'Kisechugaku Zasshi',
'0023-4001' => 'Korean J Parasitol',
'0024-1164' => 'Lethaia',
'0024-5461' => 'Lloydia',
'0076-2997' => 'Malacologia',
'1616-5047' => 'Mamm Biol',
'0025-1461' => 'Mammalia',
'1874-7787' => 'Mar Genomics',
'0269-283X' => 'Med Vet Entomol',
'0073-9901' => 'Mem Inst Butantan',
'0074-0276' => 'Mem Inst Oswaldo Cruz',
'0737-4038' => 'Mol Biol Evol',
'0962-1083' => 'Mol Ecol',
'1755-098X' => 'Mol Ecol Resour',
'1055-7903' => 'Mol Phylogenet Evol',
'0953-7562' => 'Mycol Res',
'0027-5514' => 'Mycologia',
'0028-0836' => 'Nature',
'0028-1042' => 'Naturwissenschaften',
'0028-1344' => 'Nautilus (Philadelphia)',
'1519-566X' => 'Neotrop Entomol',
'0077-7749' => 'Neues Jahrb Geol Palaontol Abh',
'0149-175X' => 'Occas Pap Tex Tech Univ Mus',
'0030-0950' => 'Ohio J Sci',
'0030-2465' => 'Onderstepoort J Vet Res',
'0030-9923' => 'Pak J Zool',
'0031-0182' => 'Palaeogeogr Palaeoclimatol Palaeoecol',
'0031-0239' => 'Palaeontology',
'0883-1351' => 'Palaios',
'0094-8373' => 'Paleobiology',
'0031-0603' => 'Pan-Pac Entomol',
'1252-607X' => 'Parasite',
'1383-5769' => 'Parasitol Int',
'0031-1820' => 'Parasitology',
'0031-1847' => 'Parazitologiia',
'0962-8436' => 'Philos Trans R Soc Lond B Biol Sci',
'0031-8884' => 'Phycologia',
'0378-2697' => 'Plant Syst Evol',
'0722-4060' => 'Polar Biol',
'0800-0395' => 'Polar Res',
'0301-9268' => 'Precambrian Res',
'0032-8332' => 'Primates',
'0003-049X' => 'Proc Am Philos Soc',
'0962-8452' => 'Proc Biol Sci',
'0068-547X' => 'Proc Calif Acad Sci',
'0018-0130' => 'Proc Helminthol Soc Wash',
'0023-3374' => 'Proc K Ned Akad Wet C',
'0027-8424' => 'Proc Natl Acad Sci U S A',
'1434-4610' => 'Protist',
'0033-2615' => 'Psyche (Camb Mass)',
'0370-2952' => 'Q J Microsc Sci',
'1040-6182' => 'Quat Int',
'0277-3791' => 'Quat Sci Rev',
'0034-7744' => 'Rev Biol Trop',
'0034-7108' => 'Rev Bras Biol',
'0034-9623' => 'Rev Iber Parasitol',
'0034-6667' => 'Rev Palaeobot Palynol',
'0034-8910' => 'Rev Saude Publica',
'0035-418X' => 'Rev Suisse Zool',
'0038-2353' => 'S Afr J Sci',
'0036-5343' => 'Sb Nar Muz Praze Rada B',
'0036-8075' => 'Science',
'0037-2102' => 'Senckenb Biol',
'0038-0717' => 'Soil Biol Biochem',
'0038-4909' => 'Southwest Nat',
'1063-5157' => 'Syst Biol',
'0165-5752' => 'Syst Parasitol',
'0039-7989' => 'Syst Zool',
'0002-8487' => 'Trans Am Fish Soc',
'0003-0023' => 'Trans Am Microsc Soc',
'0019-2252' => 'Trans Ill State Acad Sci',
'0022-8443' => 'Trans Kans Acad Sci',
'0035-9203' => 'Trans R Soc Trop Med Hyg',
'1348-8945' => 'Trop Med Health',
'0084-5647' => 'Verh Zool Bot Ges Wien',
'0304-4017' => 'Vet Parasitol',
'0043-5163' => 'Wiad Parazytol',
'0044-3255' => 'Z Parasitenkd',
'1000-7423' => 'Zhongguo Ji Sheng Chong Xue Yu Ji Sheng Chong Bing Za Zhi',
'1313-2989' => 'Zookeys',
'0044-5231' => 'Zool Anz',
'0024-4082' => 'Zool J Linn Soc',
'0044-5177' => 'Zool Jahrb Abt Anat Ontogenie Tiere',
'0300-3256' => 'Zool Scr',
'0044-5134' => 'Zool Zhurnal',
'0289-0003' => 'Zoolog Sci',
'1175-5326' => 'Zootaxa'
);

///----------------------------------------------------------------------------------------
function get($url, $content_type = '')
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
			"Accept: " . $content_type 
		);		
	}
	
	$ch = curl_init();
	curl_setopt_array($ch, $opts);
	$data = curl_exec($ch);
	$info = curl_getinfo($ch); 
	curl_close($ch);
	
	return $data;
}

//----------------------------------------------------------------------------------------
// Given a DOI get the corresponding PMID (if any)
function doi_to_pmid($doi)
{
	global $api_key;
	
	$pmid = 0;
	
	$parameters = array(
		'db' 		=> 'pubmed',
		'retmode' 	=> 'json',
		'term' 		=> $doi . '[DOI]',
		'api_key'	=> $api_key	
	);
	
	$url = 'https://eutils.ncbi.nlm.nih.gov/entrez/eutils/esearch.fcgi?' . http_build_query($parameters);
	
	// echo $url . "\n";
		
	$json = get($url);
	
	$obj = json_decode($json);
	
	if ($obj)
	{
		if ($obj->esearchresult->count == 1)
		{
			$pmid = $obj->esearchresult->idlist[0];
		}
	}
	
	return $pmid;
}

//----------------------------------------------------------------------------------------
// Get list of GIs for PMID
function pmid_to_sequences($pmid)
{
	global $api_key;

	$gis = array();
	
	$parameters = array(
		'db' 		=> 'nucleotide',
		'dbfrom' 	=> 'pubmed',
		'retmode' 	=> 'json',
		'id'		=> $pmid,
		'retmax' 	=> 10000,
		'api_key'	=> $api_key	
	);
	
	$url = 'https://eutils.ncbi.nlm.nih.gov/entrez/eutils/elink.fcgi?' . http_build_query($parameters);
	
		
	$json = get($url);
	
	$obj = json_decode($json);
	
	if ($obj)
	{
		// print_r($obj);
		
		if (isset($obj->linksets))
		{
			foreach ($obj->linksets as $linkset)
			{
				if ($linkset->dbfrom == 'pubmed')
				{
					if (isset($linkset->linksetdbs))
					{
						foreach ($linkset->linksetdbs as $linksetdb)
						{
							if ($linksetdb->dbto == 'nuccore')
							{
								$gis = array_merge($gis, $linksetdb->links);
							}
						}
					}
				}
			}		
		}
		
	}
	
	return $gis;
}

//----------------------------------------------------------------------------------------
// Given simple article metadata find GIs 
// "terms" is an array of NCBI compatible terms we can use to search GenBank
// 
function metdata_to_genbank($terms = array())
{
	global $api_key;
	global $MedAbbr;
	
	$gis = array();
	
	if (count($terms) >= 3)
	{	
		$parameters = array(
			'db' 		=> 'nucleotide',
			'retmode' 	=> 'json',
			'term' 		=> join(' AND ', $terms),
			'api_key'	=> $api_key	
		);

		$url = 'https://eutils.ncbi.nlm.nih.gov/entrez/eutils/esearch.fcgi?' . http_build_query($parameters);

		// echo $url . "\n";

		$json = get($url);
		
		// echo $json;

		$obj = json_decode($json);
		
		if ($obj)
		{
			if ($obj->esearchresult->count > 0)
			{
				$gis = $obj->esearchresult->idlist;
			}
		}
	}
	
	return $gis;
}


//----------------------------------------------------------------------------------------
// Given a DOI get metadata for article so we can search GenBank
function doi_to_metadata($doi)
{
	global $MedAbbr;
	
	$terms = array();
	
	// DOI to CSL
	$url = 'https://doi.org/' . $doi;	
	$json = get($url, 'application/vnd.citationstyles.csl+json');
	$csl = json_decode($json);
	
	if ($csl)
	{
		//print_r($csl);
	
		$terms = array();
	
		if (isset($csl->ISSN))
		{
			$journal = '';
		
			foreach ($csl->ISSN as $issn)
			{
				// echo $issn . "\n";
				
				if (isset($MedAbbr[$issn]))
				{
					$journal = $MedAbbr[$issn];
				}
			}
		
			if ($journal != '')
			{
				$terms[] = $journal . '[Journal]';
			}
			else
			{
				$terms[] = $csl->{'container-title'} . '[Journal]';
			}
		
		}	
	
		if (isset($csl->volume))
		{
			$terms[] = $csl->volume . '[Volume]';
		}
	
		if (isset($csl->page))
		{
			$parts = explode('-', $csl->page);
			$terms[] = $parts[0] . '[Page number]';
		}

		// this doesn't seem to work
		/*
		if (isset($csl->issued))
		{
			$terms[] = $csl->issued->{'date-parts'}[0][0] . '[pdat]';
		}
		*/
		
		
	}
	return $terms;
}		

//----------------------------------------------------------------------------------------
function doi_to_genbank($doi)
{
	$terms = doi_to_metadata($doi);
	$gis = metdata_to_genbank($terms);
	return $gis;
}

//----------------------------------------------------------------------------------------

// to do
// function accession_to_doi($accession)
{

}

//----------------------------------------------------------------------------------------

// examples
if (0)
{
	$doi = '10.11646/zootaxa.4881.3.5';
	$doi = '10.11609/jott.6899.14.2.20637-20642';
	//$doi = '10.1071/IS21041';

	if (0)
	{
		$terms = doi_to_metadata($doi);

		print_r($terms);

		$gis = metdata_to_genbank($terms);

		print_r($gis);
	}

	if (1)
	{
		$gis = doi_to_genbank($doi);
		print_r($gis);
	}
}


?>
