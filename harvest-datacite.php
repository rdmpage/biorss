<?php

// Harvest from DataCite


// See https://support.datacite.org/docs/api-queries
// Also https://api.datacite.org/dois/10.5281/zenodo.1133361

// some journals have ISSNs and so can be retrieved that way, others 
// will need to be searched fro by publisher, or other ways

require_once(dirname(__FILE__) . '/config.inc.php');
require_once(dirname(__FILE__) . '/rss.php');
require_once(dirname(__FILE__) . '/utils.php');

require_once(dirname(__FILE__) . '/datacite.php');

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
	curl_setopt($ch, CURLOPT_COOKIEJAR, sys_get_temp_dir() . '/cookies.txt');
	curl_setopt($ch, CURLOPT_COOKIEFILE, sys_get_temp_dir() . '/cookies.txt');	
	
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
		"Accept: " . $accept,
		"Accept-Language: en-gb",
		"User-agent: Mozilla/5.0 (iPad; U; CPU OS 3_2_1 like Mac OS X; en-us) AppleWebKit/531.21.10 (KHTML, like Gecko) Mobile/7B405" 	
		));
	
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


//----------------------------------------------------------------------------------------

// Where shall we store the feeds?
$today = date('Y-m-d', time());
$cache_dir = $config['cache'] . '/' . $today;
$latest_dir = $config['cache'] . '/latest';

if (!file_exists($cache_dir))
{
	$oldumask = umask(0); 
	mkdir($cache_dir, 0777);
	umask($oldumask);
}	

if (file_exists($latest_dir))
{
	unlink($latest_dir);
}	
symlink($cache_dir, $latest_dir);

//----------------------------------------------------------------------------------------


// for other things we need to search by publisher, etc.
// 1660-9972	10.5169/seals-787048			"publisher": "Naturhistorisches Museum Bern", 
// 0399-0974	10.26028/cybium/2017-411-005	"publisher": "Société Française d'Ichtyologie"

$issns = array(
	'0217-2445' => 'Raffles Bulletin of Zoology', 
	//'2224-6304' => 'Israel Journal of Entomology', // eISSN
	'2262-3094'	=> 'Cahiers de Biologie Marine',
	
	//'2100-0840' => 'Ascomycete.org',

);

$year = 2022;

foreach ($issns as $issn => $journal)
{
	// query datacite

	if (1)
	{
		$parameters = array(
			'query' 		=> 'relatedIdentifiers.relatedIdentifier:' . $issn,
			'registered' 	=> $year,
			'page[size]' 	=> 50		
			);
	
		$url = 'https://api.datacite.org/dois?' . http_build_query($parameters);
	
		echo $url . "\n";

		// call API
		$json = get($url, 'application/json');
	}
	else
	{
		$json = '{
    "data": [
        {
            "id": "10.26107/rbz-2022-0003",
            "type": "dois",
            "attributes": {
                "doi": "10.26107/rbz-2022-0003",
                "identifiers": [],
                "creators": [
                    {
                        "name": "Nguyen, Thanh V.",
                        "nameType": "Personal",
                        "givenName": "Thanh V.",
                        "familyName": "Nguyen",
                        "affiliation": [],
                        "nameIdentifiers": []
                    },
                    {
                        "name": "Man, Huy",
                        "nameType": "Personal",
                        "givenName": "Huy",
                        "familyName": "Man",
                        "affiliation": [],
                        "nameIdentifiers": []
                    },
                    {
                        "name": "Nguyen, Anh",
                        "nameType": "Personal",
                        "givenName": "Anh",
                        "familyName": "Nguyen",
                        "affiliation": [],
                        "nameIdentifiers": []
                    }
                ],
                "titles": [
                    {
                        "title": "An assessment of potential distribution and climate change impacts on a critically endangered primate, the Delacour’s langur"
                    }
                ],
                "publisher": "Lee Kong Chian Natural History Museum",
                "container": {
                    "type": "Series",
                    "title": "Raffles Bulletin of Zoology",
                    "volume": "70",
                    "lastPage": "38",
                    "firstPage": "30",
                    "identifier": "0217-2445",
                    "identifierType": "ISSN"
                },
                "publicationYear": 2022,
                "subjects": [
                    {
                        "subject": "Trachypithecus delacouri"
                    },
                    {
                        "subject": "Maxent"
                    },
                    {
                        "subject": "endemic"
                    },
                    {
                        "subject": "Vietnam"
                    }
                ],
                "contributors": [],
                "dates": [
                    {
                        "date": "2022",
                        "dateType": "Issued"
                    }
                ],
                "language": null,
                "types": {
                    "ris": "JOUR",
                    "bibtex": "article",
                    "citeproc": "article-journal",
                    "schemaOrg": "ScholarlyArticle",
                    "resourceType": "Journal article",
                    "resourceTypeGeneral": "Text"
                },
                "relatedIdentifiers": [
                    {
                        "relationType": "IsPartOf",
                        "relatedIdentifier": "0217-2445",
                        "relatedIdentifierType": "ISSN"
                    }
                ],
                "sizes": [],
                "formats": [],
                "version": null,
                "rightsList": [],
                "descriptions": [
                    {
                        "description": "Raffles Bulletin of Zoology, 70, 30-38",
                        "descriptionType": "SeriesInformation"
                    }
                ],
                "geoLocations": [],
                "fundingReferences": [],
                "url": "https://lkcnhm.nus.edu.sg/wp-content/uploads/sites/10/2022/01/RBZ-2022-0003.pdf",
                "contentUrl": null,
                "metadataVersion": 0,
                "schemaVersion": "http://datacite.org/schema/kernel-4",
                "source": "mds",
                "isActive": true,
                "state": "findable",
                "reason": null,
                "viewCount": 0,
                "downloadCount": 0,
                "referenceCount": 0,
                "citationCount": 0,
                "partCount": 0,
                "partOfCount": 0,
                "versionCount": 0,
                "versionOfCount": 0,
                "created": "2022-01-12T05:15:40Z",
                "registered": "2022-01-12T05:15:41Z",
                "published": null,
                "updated": "2022-01-12T05:15:41Z"
            },
            "relationships": {
                "client": {
                    "data": {
                        "id": "nus.sb",
                        "type": "clients"
                    }
                }
            }
        },
        {
            "id": "10.26107/rbz-2022-0002",
            "type": "dois",
            "attributes": {
                "doi": "10.26107/rbz-2022-0002",
                "identifiers": [],
                "creators": [
                    {
                        "name": "Ban, Teruaki",
                        "nameType": "Personal",
                        "givenName": "Teruaki",
                        "familyName": "Ban",
                        "affiliation": [],
                        "nameIdentifiers": []
                    }
                ],
                "titles": [
                    {
                        "title": "The genus Kanigara Distant (Heteroptera: Lygaeoidea: Rhyparochromidae) from Malay Peninsula and Thailand, with description of a new species"
                    }
                ],
                "publisher": "Lee Kong Chian Natural History Museum",
                "container": {
                    "type": "Series",
                    "title": "Raffles Bulletin of Zoology",
                    "volume": "70",
                    "lastPage": "29",
                    "firstPage": "22",
                    "identifier": "0217-2445",
                    "identifierType": "ISSN"
                },
                "publicationYear": 2022,
                "subjects": [
                    {
                        "subject": "seed bug"
                    },
                    {
                        "subject": "taxonomy"
                    },
                    {
                        "subject": "key to species"
                    },
                    {
                        "subject": "Malaysia"
                    },
                    {
                        "subject": "Oriental Region"
                    }
                ],
                "contributors": [],
                "dates": [
                    {
                        "date": "2022",
                        "dateType": "Issued"
                    }
                ],
                "language": null,
                "types": {
                    "ris": "JOUR",
                    "bibtex": "article",
                    "citeproc": "article-journal",
                    "schemaOrg": "ScholarlyArticle",
                    "resourceType": "Journal article",
                    "resourceTypeGeneral": "Text"
                },
                "relatedIdentifiers": [
                    {
                        "relationType": "IsPartOf",
                        "relatedIdentifier": "0217-2445",
                        "relatedIdentifierType": "ISSN"
                    }
                ],
                "sizes": [],
                "formats": [],
                "version": null,
                "rightsList": [],
                "descriptions": [
                    {
                        "description": "Raffles Bulletin of Zoology, 70, 22-29",
                        "descriptionType": "SeriesInformation"
                    }
                ],
                "geoLocations": [],
                "fundingReferences": [],
                "url": "https://lkcnhm.nus.edu.sg/wp-content/uploads/sites/10/2022/01/RBZ-2022-0002.pdf",
                "contentUrl": null,
                "metadataVersion": 0,
                "schemaVersion": "http://datacite.org/schema/kernel-4",
                "source": "mds",
                "isActive": true,
                "state": "findable",
                "reason": null,
                "viewCount": 0,
                "downloadCount": 0,
                "referenceCount": 0,
                "citationCount": 0,
                "partCount": 0,
                "partOfCount": 0,
                "versionCount": 0,
                "versionOfCount": 0,
                "created": "2022-01-12T05:15:36Z",
                "registered": "2022-01-12T05:15:38Z",
                "published": null,
                "updated": "2022-01-12T05:15:38Z"
            },
            "relationships": {
                "client": {
                    "data": {
                        "id": "nus.sb",
                        "type": "clients"
                    }
                }
            }
        },
        {
            "id": "10.26107/rbz-2022-0001",
            "type": "dois",
            "attributes": {
                "doi": "10.26107/rbz-2022-0001",
                "identifiers": [],
                "creators": [
                    {
                        "name": "Cumberlidge, Neil",
                        "nameType": "Personal",
                        "givenName": "Neil",
                        "familyName": "Cumberlidge",
                        "affiliation": [],
                        "nameIdentifiers": []
                    },
                    {
                        "name": "Caro, Tim",
                        "nameType": "Personal",
                        "givenName": "Tim",
                        "familyName": "Caro",
                        "affiliation": [],
                        "nameIdentifiers": []
                    },
                    {
                        "name": "Watson-Zink, Victoria M.",
                        "nameType": "Personal",
                        "givenName": "Victoria M.",
                        "familyName": "Watson-Zink",
                        "affiliation": [],
                        "nameIdentifiers": []
                    },
                    {
                        "name": "Naruse, Tohru",
                        "nameType": "Personal",
                        "givenName": "Tohru",
                        "familyName": "Naruse",
                        "affiliation": [],
                        "nameIdentifiers": []
                    },
                    {
                        "name": "Ng, Peter K. L.",
                        "nameType": "Personal",
                        "givenName": "Peter K. L.",
                        "familyName": "Ng",
                        "affiliation": [],
                        "nameIdentifiers": []
                    },
                    {
                        "name": "Orchard, Max",
                        "nameType": "Personal",
                        "givenName": "Max",
                        "familyName": "Orchard",
                        "affiliation": [],
                        "nameIdentifiers": []
                    },
                    {
                        "name": "Rahayu, Dwi L.",
                        "nameType": "Personal",
                        "givenName": "Dwi L.",
                        "familyName": "Rahayu",
                        "affiliation": [],
                        "nameIdentifiers": []
                    },
                    {
                        "name": "Worwor, Daisy",
                        "nameType": "Personal",
                        "givenName": "Daisy",
                        "familyName": "Worwor",
                        "affiliation": [],
                        "nameIdentifiers": []
                    },
                    {
                        "name": "Yeo, Darren C. J.",
                        "nameType": "Personal",
                        "givenName": "Darren C. J.",
                        "familyName": "Yeo",
                        "affiliation": [],
                        "nameIdentifiers": []
                    },
                    {
                        "name": "White, Tim",
                        "nameType": "Personal",
                        "givenName": "Tim",
                        "familyName": "White",
                        "affiliation": [],
                        "nameIdentifiers": []
                    }
                ],
                "titles": [
                    {
                        "title": "Troubled giants: The updated conservation status of the coconut crab (Birgus latro)"
                    }
                ],
                "publisher": "Lee Kong Chian Natural History Museum",
                "container": {
                    "type": "Series",
                    "title": "Raffles Bulletin of Zoology",
                    "volume": "70",
                    "lastPage": "21",
                    "firstPage": "1",
                    "identifier": "0217-2445",
                    "identifierType": "ISSN"
                },
                "publicationYear": 2022,
                "subjects": [
                    {
                        "subject": "conservation"
                    },
                    {
                        "subject": "exploitation"
                    },
                    {
                        "subject": "extinction threat"
                    },
                    {
                        "subject": "land-use change"
                    },
                    {
                        "subject": "terrestrial hermit crab"
                    }
                ],
                "contributors": [],
                "dates": [
                    {
                        "date": "2022",
                        "dateType": "Issued"
                    }
                ],
                "language": null,
                "types": {
                    "ris": "JOUR",
                    "bibtex": "article",
                    "citeproc": "article-journal",
                    "schemaOrg": "ScholarlyArticle",
                    "resourceType": "Journal article",
                    "resourceTypeGeneral": "Text"
                },
                "relatedIdentifiers": [
                    {
                        "relationType": "IsPartOf",
                        "relatedIdentifier": "0217-2445",
                        "relatedIdentifierType": "ISSN"
                    }
                ],
                "sizes": [],
                "formats": [],
                "version": null,
                "rightsList": [],
                "descriptions": [
                    {
                        "description": "Raffles Bulletin of Zoology, 70, 1-21",
                        "descriptionType": "SeriesInformation"
                    }
                ],
                "geoLocations": [],
                "fundingReferences": [],
                "url": "https://lkcnhm.nus.edu.sg/wp-content/uploads/sites/10/2022/01/RBZ-2022-0001.pdf",
                "contentUrl": null,
                "metadataVersion": 0,
                "schemaVersion": "http://datacite.org/schema/kernel-4",
                "source": "mds",
                "isActive": true,
                "state": "findable",
                "reason": null,
                "viewCount": 0,
                "downloadCount": 0,
                "referenceCount": 0,
                "citationCount": 0,
                "partCount": 0,
                "partOfCount": 0,
                "versionCount": 0,
                "versionOfCount": 0,
                "created": "2022-01-12T05:15:34Z",
                "registered": "2022-01-12T05:15:35Z",
                "published": null,
                "updated": "2022-01-12T05:15:35Z"
            },
            "relationships": {
                "client": {
                    "data": {
                        "id": "nus.sb",
                        "type": "clients"
                    }
                }
            }
        },
        {
            "id": "10.26107/rbz-2021-0073",
            "type": "dois",
            "attributes": {
                "doi": "10.26107/rbz-2021-0073",
                "identifiers": [],
                "creators": [
                    {
                        "name": "Kan, Adrian",
                        "nameType": "Personal",
                        "givenName": "Adrian",
                        "familyName": "Kan",
                        "affiliation": [],
                        "nameIdentifiers": []
                    },
                    {
                        "name": "Ratnayeke, Shyamala",
                        "nameType": "Personal",
                        "givenName": "Shyamala",
                        "familyName": "Ratnayeke",
                        "affiliation": [],
                        "nameIdentifiers": []
                    },
                    {
                        "name": "Yow, Yoon-Yen",
                        "nameType": "Personal",
                        "givenName": "Yoon-Yen",
                        "familyName": "Yow",
                        "affiliation": [],
                        "nameIdentifiers": []
                    }
                ],
                "titles": [
                    {
                        "title": "Molecular evidence of hybridisation in two invasive species of Pomacea (Gastropoda: Ampullariidae) in Peninsular Malaysia"
                    }
                ],
                "publisher": "Lee Kong Chian Natural History Museum",
                "container": {
                    "type": "Series",
                    "title": "Raffles Bulletin of Zoology",
                    "volume": "69",
                    "lastPage": "585",
                    "firstPage": "570",
                    "identifier": "0217-2445",
                    "identifierType": "ISSN"
                },
                "publicationYear": 2021,
                "subjects": [
                    {
                        "subject": "apple snails"
                    },
                    {
                        "subject": "nuclear elongation factor 1-alpha"
                    },
                    {
                        "subject": "mitochondrial cytochrome c oxidase subunit I"
                    },
                    {
                        "subject": "genetic exchange"
                    },
                    {
                        "subject": "phylogeny"
                    }
                ],
                "contributors": [],
                "dates": [
                    {
                        "date": "2021",
                        "dateType": "Issued"
                    }
                ],
                "language": null,
                "types": {
                    "ris": "JOUR",
                    "bibtex": "article",
                    "citeproc": "article-journal",
                    "schemaOrg": "ScholarlyArticle",
                    "resourceType": "Journal article",
                    "resourceTypeGeneral": "Text"
                },
                "relatedIdentifiers": [
                    {
                        "relationType": "IsPartOf",
                        "relatedIdentifier": "0217-2445",
                        "relatedIdentifierType": "ISSN"
                    }
                ],
                "sizes": [],
                "formats": [],
                "version": null,
                "rightsList": [],
                "descriptions": [
                    {
                        "description": "Raffles Bulletin of Zoology, 69, 570-585",
                        "descriptionType": "SeriesInformation"
                    }
                ],
                "geoLocations": [],
                "fundingReferences": [],
                "url": "https://lkcnhm.nus.edu.sg/wp-content/uploads/sites/10/2021/12/RBZ-2021-0073.pdf",
                "contentUrl": null,
                "metadataVersion": 0,
                "schemaVersion": "http://datacite.org/schema/kernel-4",
                "source": "mds",
                "isActive": true,
                "state": "findable",
                "reason": null,
                "viewCount": 0,
                "downloadCount": 0,
                "referenceCount": 0,
                "citationCount": 0,
                "partCount": 0,
                "partOfCount": 0,
                "versionCount": 0,
                "versionOfCount": 0,
                "created": "2022-01-12T05:15:30Z",
                "registered": "2022-01-12T05:15:32Z",
                "published": null,
                "updated": "2022-01-12T05:15:32Z"
            },
            "relationships": {
                "client": {
                    "data": {
                        "id": "nus.sb",
                        "type": "clients"
                    }
                }
            }
        },
        {
            "id": "10.26107/rbz-2021-0072",
            "type": "dois",
            "attributes": {
                "doi": "10.26107/rbz-2021-0072",
                "identifiers": [],
                "creators": [
                    {
                        "name": "Nahok, Benchawan",
                        "nameType": "Personal",
                        "givenName": "Benchawan",
                        "familyName": "Nahok",
                        "affiliation": [],
                        "nameIdentifiers": []
                    },
                    {
                        "name": "Tumpeesuwan, Chanidaporn",
                        "nameType": "Personal",
                        "givenName": "Chanidaporn",
                        "familyName": "Tumpeesuwan",
                        "affiliation": [],
                        "nameIdentifiers": []
                    },
                    {
                        "name": "Tumpeesuwan, Sakboworn",
                        "nameType": "Personal",
                        "givenName": "Sakboworn",
                        "familyName": "Tumpeesuwan",
                        "affiliation": [],
                        "nameIdentifiers": []
                    }
                ],
                "titles": [
                    {
                        "title": "Two new species of genus Anceyoconcha S. Tumpeesuwan &amp; C. Tumpeesuwan, in Nahok et al., 2020 (Gastropoda: Pulmonata: Camaenidae), from northeastern Thailand"
                    }
                ],
                "publisher": "Lee Kong Chian Natural History Museum",
                "container": {
                    "type": "Series",
                    "title": "Raffles Bulletin of Zoology",
                    "volume": "69",
                    "lastPage": "569",
                    "firstPage": "555",
                    "identifier": "0217-2445",
                    "identifierType": "ISSN"
                },
                "publicationYear": 2021,
                "subjects": [
                    {
                        "subject": "tree snails"
                    },
                    {
                        "subject": "genital system"
                    },
                    {
                        "subject": "systematics"
                    },
                    {
                        "subject": "phylogeny"
                    }
                ],
                "contributors": [],
                "dates": [
                    {
                        "date": "2021",
                        "dateType": "Issued"
                    }
                ],
                "language": null,
                "types": {
                    "ris": "JOUR",
                    "bibtex": "article",
                    "citeproc": "article-journal",
                    "schemaOrg": "ScholarlyArticle",
                    "resourceType": "Journal article",
                    "resourceTypeGeneral": "Text"
                },
                "relatedIdentifiers": [
                    {
                        "relationType": "IsPartOf",
                        "relatedIdentifier": "0217-2445",
                        "relatedIdentifierType": "ISSN"
                    }
                ],
                "sizes": [],
                "formats": [],
                "version": null,
                "rightsList": [],
                "descriptions": [
                    {
                        "description": "Raffles Bulletin of Zoology, 69, 555-569",
                        "descriptionType": "SeriesInformation"
                    }
                ],
                "geoLocations": [],
                "fundingReferences": [],
                "url": "https://lkcnhm.nus.edu.sg/wp-content/uploads/sites/10/2021/12/RBZ-2021-0072.pdf",
                "contentUrl": null,
                "metadataVersion": 0,
                "schemaVersion": "http://datacite.org/schema/kernel-4",
                "source": "mds",
                "isActive": true,
                "state": "findable",
                "reason": null,
                "viewCount": 0,
                "downloadCount": 0,
                "referenceCount": 0,
                "citationCount": 0,
                "partCount": 0,
                "partOfCount": 0,
                "versionCount": 0,
                "versionOfCount": 0,
                "created": "2022-01-12T05:15:25Z",
                "registered": "2022-01-12T05:15:27Z",
                "published": null,
                "updated": "2022-01-12T05:15:27Z"
            },
            "relationships": {
                "client": {
                    "data": {
                        "id": "nus.sb",
                        "type": "clients"
                    }
                }
            }
        },
        {
            "id": "10.26107/rbz-2021-0071",
            "type": "dois",
            "attributes": {
                "doi": "10.26107/rbz-2021-0071",
                "identifiers": [],
                "creators": [
                    {
                        "name": "Rogers, D. Christopher",
                        "nameType": "Personal",
                        "givenName": "D. Christopher",
                        "familyName": "Rogers",
                        "affiliation": [],
                        "nameIdentifiers": []
                    },
                    {
                        "name": "Sanoamuang, Laorsri",
                        "nameType": "Personal",
                        "givenName": "Laorsri",
                        "familyName": "Sanoamuang",
                        "affiliation": [],
                        "nameIdentifiers": []
                    },
                    {
                        "name": "Sanoamuang, Niwat",
                        "nameType": "Personal",
                        "givenName": "Niwat",
                        "familyName": "Sanoamuang",
                        "affiliation": [],
                        "nameIdentifiers": []
                    }
                ],
                "titles": [
                    {
                        "title": "A new semiterrestrial freshwater crab in the genus Badistemon and a new record of Indochinamon from northern Thailand (Brachyura: Potamidae)"
                    }
                ],
                "publisher": "Lee Kong Chian Natural History Museum",
                "container": {
                    "type": "Series",
                    "title": "Raffles Bulletin of Zoology",
                    "volume": "69",
                    "lastPage": "554",
                    "firstPage": "548",
                    "identifier": "0217-2445",
                    "identifierType": "ISSN"
                },
                "publicationYear": 2021,
                "subjects": [
                    {
                        "subject": "freshwater crabs"
                    },
                    {
                        "subject": "Southeast Asia"
                    },
                    {
                        "subject": "new species"
                    }
                ],
                "contributors": [],
                "dates": [
                    {
                        "date": "2021",
                        "dateType": "Issued"
                    }
                ],
                "language": null,
                "types": {
                    "ris": "JOUR",
                    "bibtex": "article",
                    "citeproc": "article-journal",
                    "schemaOrg": "ScholarlyArticle",
                    "resourceType": "Journal article",
                    "resourceTypeGeneral": "Text"
                },
                "relatedIdentifiers": [
                    {
                        "relationType": "IsPartOf",
                        "relatedIdentifier": "0217-2445",
                        "relatedIdentifierType": "ISSN"
                    }
                ],
                "sizes": [],
                "formats": [],
                "version": null,
                "rightsList": [],
                "descriptions": [
                    {
                        "description": "Raffles Bulletin of Zoology, 69, 548-554",
                        "descriptionType": "SeriesInformation"
                    }
                ],
                "geoLocations": [],
                "fundingReferences": [],
                "url": "https://lkcnhm.nus.edu.sg/wp-content/uploads/sites/10/2021/12/RBZ-2021-0071.pdf",
                "contentUrl": null,
                "metadataVersion": 0,
                "schemaVersion": "http://datacite.org/schema/kernel-4",
                "source": "mds",
                "isActive": true,
                "state": "findable",
                "reason": null,
                "viewCount": 0,
                "downloadCount": 0,
                "referenceCount": 0,
                "citationCount": 0,
                "partCount": 0,
                "partOfCount": 0,
                "versionCount": 0,
                "versionOfCount": 0,
                "created": "2022-01-12T05:15:21Z",
                "registered": "2022-01-12T05:15:23Z",
                "published": null,
                "updated": "2022-01-12T05:15:23Z"
            },
            "relationships": {
                "client": {
                    "data": {
                        "id": "nus.sb",
                        "type": "clients"
                    }
                }
            }
        },
        {
            "id": "10.26107/rbz-2021-0070",
            "type": "dois",
            "attributes": {
                "doi": "10.26107/rbz-2021-0070",
                "identifiers": [],
                "creators": [
                    {
                        "name": "Fujiwara, Kyoji",
                        "nameType": "Personal",
                        "givenName": "Kyoji",
                        "familyName": "Fujiwara",
                        "affiliation": [],
                        "nameIdentifiers": []
                    },
                    {
                        "name": "Psomadakis, Peter N.",
                        "nameType": "Personal",
                        "givenName": "Peter N.",
                        "familyName": "Psomadakis",
                        "affiliation": [],
                        "nameIdentifiers": []
                    },
                    {
                        "name": "Swe, Thet Yu Yu",
                        "nameType": "Personal",
                        "givenName": "Thet Yu Yu",
                        "familyName": "Swe",
                        "affiliation": [],
                        "nameIdentifiers": []
                    },
                    {
                        "name": "Motomura, Hiroyuki",
                        "nameType": "Personal",
                        "givenName": "Hiroyuki",
                        "familyName": "Motomura",
                        "affiliation": [],
                        "nameIdentifiers": []
                    }
                ],
                "titles": [
                    {
                        "title": "Description of a new species of Obliquogobius (Teleostei: Gobiidae) from the Andaman Sea (northeastern Indian Ocean)"
                    }
                ],
                "publisher": "Lee Kong Chian Natural History Museum",
                "container": {
                    "type": "Series",
                    "title": "Raffles Bulletin of Zoology",
                    "volume": "69",
                    "lastPage": "547",
                    "firstPage": "541",
                    "identifier": "0217-2445",
                    "identifierType": "ISSN"
                },
                "publicationYear": 2021,
                "subjects": [
                    {
                        "subject": "Obliquogobius eptactis"
                    },
                    {
                        "subject": "Obliquogobius yamadai"
                    },
                    {
                        "subject": "deepwater goby"
                    },
                    {
                        "subject": "R/V Dr. Fridtjof Nansen"
                    },
                    {
                        "subject": "trawl surveys"
                    },
                    {
                        "subject": "Myanmar"
                    },
                    {
                        "subject": "Indian Ocean"
                    }
                ],
                "contributors": [],
                "dates": [
                    {
                        "date": "2021",
                        "dateType": "Issued"
                    }
                ],
                "language": null,
                "types": {
                    "ris": "JOUR",
                    "bibtex": "article",
                    "citeproc": "article-journal",
                    "schemaOrg": "ScholarlyArticle",
                    "resourceType": "Journal article",
                    "resourceTypeGeneral": "Text"
                },
                "relatedIdentifiers": [
                    {
                        "relationType": "IsPartOf",
                        "relatedIdentifier": "0217-2445",
                        "relatedIdentifierType": "ISSN"
                    }
                ],
                "sizes": [],
                "formats": [],
                "version": null,
                "rightsList": [],
                "descriptions": [
                    {
                        "description": "Raffles Bulletin of Zoology, 69, 541-547",
                        "descriptionType": "SeriesInformation"
                    }
                ],
                "geoLocations": [],
                "fundingReferences": [],
                "url": "https://lkcnhm.nus.edu.sg/wp-content/uploads/sites/10/2021/12/RBZ-2021-0070.pdf",
                "contentUrl": null,
                "metadataVersion": 0,
                "schemaVersion": "http://datacite.org/schema/kernel-4",
                "source": "mds",
                "isActive": true,
                "state": "findable",
                "reason": null,
                "viewCount": 0,
                "downloadCount": 0,
                "referenceCount": 0,
                "citationCount": 0,
                "partCount": 0,
                "partOfCount": 0,
                "versionCount": 0,
                "versionOfCount": 0,
                "created": "2022-01-12T05:15:18Z",
                "registered": "2022-01-12T05:15:20Z",
                "published": null,
                "updated": "2022-01-12T05:15:20Z"
            },
            "relationships": {
                "client": {
                    "data": {
                        "id": "nus.sb",
                        "type": "clients"
                    }
                }
            }
        },
        {
            "id": "10.26107/rbz-2021-0069",
            "type": "dois",
            "attributes": {
                "doi": "10.26107/rbz-2021-0069",
                "identifiers": [],
                "creators": [
                    {
                        "name": "Kottelat, Maurice",
                        "nameType": "Personal",
                        "givenName": "Maurice",
                        "familyName": "Kottelat",
                        "affiliation": [],
                        "nameIdentifiers": []
                    },
                    {
                        "name": "Lim, Kelvin K. P.",
                        "nameType": "Personal",
                        "givenName": "Kelvin K. P.",
                        "familyName": "Lim",
                        "affiliation": [],
                        "nameIdentifiers": []
                    }
                ],
                "titles": [
                    {
                        "title": "Two new species of Barbodes from the Malay Peninsula and comments on ‘cryptic species’ in the B. binotatus group (Teleostei: Cyprinidae)"
                    }
                ],
                "publisher": "Lee Kong Chian Natural History Museum",
                "container": {
                    "type": "Series",
                    "title": "Raffles Bulletin of Zoology",
                    "volume": "69",
                    "lastPage": "540",
                    "firstPage": "522",
                    "identifier": "0217-2445",
                    "identifierType": "ISSN"
                },
                "publicationYear": 2021,
                "subjects": [
                    {
                        "subject": "Barbodes"
                    },
                    {
                        "subject": "Singapore"
                    },
                    {
                        "subject": "Malaysia"
                    },
                    {
                        "subject": "cryptic species"
                    }
                ],
                "contributors": [],
                "dates": [
                    {
                        "date": "2021",
                        "dateType": "Issued"
                    }
                ],
                "language": null,
                "types": {
                    "ris": "JOUR",
                    "bibtex": "article",
                    "citeproc": "article-journal",
                    "schemaOrg": "ScholarlyArticle",
                    "resourceType": "Journal article",
                    "resourceTypeGeneral": "Text"
                },
                "relatedIdentifiers": [
                    {
                        "relationType": "IsPartOf",
                        "relatedIdentifier": "0217-2445",
                        "relatedIdentifierType": "ISSN"
                    }
                ],
                "sizes": [],
                "formats": [],
                "version": null,
                "rightsList": [],
                "descriptions": [
                    {
                        "description": "Raffles Bulletin of Zoology, 69, 522-540",
                        "descriptionType": "SeriesInformation"
                    }
                ],
                "geoLocations": [],
                "fundingReferences": [],
                "url": "https://lkcnhm.nus.edu.sg/wp-content/uploads/sites/10/2021/12/RBZ-2021-0069.pdf",
                "contentUrl": null,
                "metadataVersion": 0,
                "schemaVersion": "http://datacite.org/schema/kernel-4",
                "source": "mds",
                "isActive": true,
                "state": "findable",
                "reason": null,
                "viewCount": 0,
                "downloadCount": 0,
                "referenceCount": 0,
                "citationCount": 0,
                "partCount": 0,
                "partOfCount": 0,
                "versionCount": 0,
                "versionOfCount": 0,
                "created": "2022-01-12T05:15:15Z",
                "registered": "2022-01-12T05:15:17Z",
                "published": null,
                "updated": "2022-01-12T05:15:17Z"
            },
            "relationships": {
                "client": {
                    "data": {
                        "id": "nus.sb",
                        "type": "clients"
                    }
                }
            }
        },
        {
            "id": "10.26107/rbz-2021-0068",
            "type": "dois",
            "attributes": {
                "doi": "10.26107/rbz-2021-0068",
                "identifiers": [],
                "creators": [
                    {
                        "name": "Tran, A. D.",
                        "nameType": "Personal",
                        "givenName": "A. D.",
                        "familyName": "Tran",
                        "affiliation": [],
                        "nameIdentifiers": []
                    },
                    {
                        "name": "Zettel, H.",
                        "nameType": "Personal",
                        "givenName": "H.",
                        "familyName": "Zettel",
                        "affiliation": [],
                        "nameIdentifiers": []
                    }
                ],
                "titles": [
                    {
                        "title": "Taxonomy of the Ranatra biroi group sensu Lansbury, 1972 (Nepomorpha: Nepidae), with descriptions of two new species"
                    }
                ],
                "publisher": "Lee Kong Chian Natural History Museum",
                "container": {
                    "type": "Series",
                    "title": "Raffles Bulletin of Zoology",
                    "volume": "69",
                    "lastPage": "521",
                    "firstPage": "507",
                    "identifier": "0217-2445",
                    "identifierType": "ISSN"
                },
                "publicationYear": 2021,
                "subjects": [
                    {
                        "subject": "Nepidae"
                    },
                    {
                        "subject": "Ranatra biroi group"
                    },
                    {
                        "subject": "new species"
                    },
                    {
                        "subject": "taxonomy"
                    },
                    {
                        "subject": "Philippines"
                    }
                ],
                "contributors": [],
                "dates": [
                    {
                        "date": "2021",
                        "dateType": "Issued"
                    }
                ],
                "language": null,
                "types": {
                    "ris": "JOUR",
                    "bibtex": "article",
                    "citeproc": "article-journal",
                    "schemaOrg": "ScholarlyArticle",
                    "resourceType": "Journal article",
                    "resourceTypeGeneral": "Text"
                },
                "relatedIdentifiers": [
                    {
                        "relationType": "IsPartOf",
                        "relatedIdentifier": "0217-2445",
                        "relatedIdentifierType": "ISSN"
                    }
                ],
                "sizes": [],
                "formats": [],
                "version": null,
                "rightsList": [],
                "descriptions": [
                    {
                        "description": "Raffles Bulletin of Zoology, 69, 507-521",
                        "descriptionType": "SeriesInformation"
                    }
                ],
                "geoLocations": [],
                "fundingReferences": [],
                "url": "https://lkcnhm.nus.edu.sg/wp-content/uploads/sites/10/2021/12/RBZ-2021-0068.pdf",
                "contentUrl": null,
                "metadataVersion": 0,
                "schemaVersion": "http://datacite.org/schema/kernel-4",
                "source": "mds",
                "isActive": true,
                "state": "findable",
                "reason": null,
                "viewCount": 0,
                "downloadCount": 0,
                "referenceCount": 0,
                "citationCount": 0,
                "partCount": 0,
                "partOfCount": 0,
                "versionCount": 0,
                "versionOfCount": 0,
                "created": "2022-01-12T05:15:11Z",
                "registered": "2022-01-12T05:15:13Z",
                "published": null,
                "updated": "2022-01-12T05:15:13Z"
            },
            "relationships": {
                "client": {
                    "data": {
                        "id": "nus.sb",
                        "type": "clients"
                    }
                }
            }
        },
        {
            "id": "10.26107/rbz-2021-0067",
            "type": "dois",
            "attributes": {
                "doi": "10.26107/rbz-2021-0067",
                "identifiers": [],
                "creators": [
                    {
                        "name": "Marshall, Andrew J.",
                        "nameType": "Personal",
                        "givenName": "Andrew J.",
                        "familyName": "Marshall",
                        "affiliation": [],
                        "nameIdentifiers": []
                    },
                    {
                        "name": "Meijaard, Erik",
                        "nameType": "Personal",
                        "givenName": "Erik",
                        "familyName": "Meijaard",
                        "affiliation": [],
                        "nameIdentifiers": []
                    },
                    {
                        "name": "Leighton, Mark",
                        "nameType": "Personal",
                        "givenName": "Mark",
                        "familyName": "Leighton",
                        "affiliation": [],
                        "nameIdentifiers": []
                    }
                ],
                "titles": [
                    {
                        "title": "Extreme ecological specialisation in a rainforest mammal, the Bornean tufted ground squirrel, Rheithrosciurus macrotis Gray, 1857"
                    }
                ],
                "publisher": "Lee Kong Chian Natural History Museum",
                "container": {
                    "type": "Series",
                    "title": "Raffles Bulletin of Zoology",
                    "volume": "69",
                    "lastPage": "506",
                    "firstPage": "497",
                    "identifier": "0217-2445",
                    "identifierType": "ISSN"
                },
                "publicationYear": 2021,
                "subjects": [
                    {
                        "subject": "Borneo"
                    },
                    {
                        "subject": "Canarium decumanum"
                    },
                    {
                        "subject": "diet breadth"
                    },
                    {
                        "subject": "Indonesia"
                    },
                    {
                        "subject": "keystone species"
                    },
                    {
                        "subject": "seed predation"
                    }
                ],
                "contributors": [],
                "dates": [
                    {
                        "date": "2021",
                        "dateType": "Issued"
                    }
                ],
                "language": null,
                "types": {
                    "ris": "JOUR",
                    "bibtex": "article",
                    "citeproc": "article-journal",
                    "schemaOrg": "ScholarlyArticle",
                    "resourceType": "Journal article",
                    "resourceTypeGeneral": "Text"
                },
                "relatedIdentifiers": [
                    {
                        "relationType": "IsPartOf",
                        "relatedIdentifier": "0217-2445",
                        "relatedIdentifierType": "ISSN"
                    }
                ],
                "sizes": [],
                "formats": [],
                "version": null,
                "rightsList": [],
                "descriptions": [
                    {
                        "description": "Raffles Bulletin of Zoology, 69, 497-506",
                        "descriptionType": "SeriesInformation"
                    }
                ],
                "geoLocations": [],
                "fundingReferences": [],
                "url": "https://lkcnhm.nus.edu.sg/wp-content/uploads/sites/10/2021/11/RBZ-2021-0067.pdf",
                "contentUrl": null,
                "metadataVersion": 0,
                "schemaVersion": "http://datacite.org/schema/kernel-4",
                "source": "mds",
                "isActive": true,
                "state": "findable",
                "reason": null,
                "viewCount": 0,
                "downloadCount": 0,
                "referenceCount": 0,
                "citationCount": 0,
                "partCount": 0,
                "partOfCount": 0,
                "versionCount": 0,
                "versionOfCount": 0,
                "created": "2022-01-12T05:15:07Z",
                "registered": "2022-01-12T05:15:09Z",
                "published": null,
                "updated": "2022-01-12T05:15:09Z"
            },
            "relationships": {
                "client": {
                    "data": {
                        "id": "nus.sb",
                        "type": "clients"
                    }
                }
            }
        },
        {
            "id": "10.26107/rbz-2021-0066",
            "type": "dois",
            "attributes": {
                "doi": "10.26107/rbz-2021-0066",
                "identifiers": [],
                "creators": [
                    {
                        "name": "McGowen, Michael R.",
                        "nameType": "Personal",
                        "givenName": "Michael R.",
                        "familyName": "McGowen",
                        "affiliation": [],
                        "nameIdentifiers": []
                    },
                    {
                        "name": "Vu, Long",
                        "nameType": "Personal",
                        "givenName": "Long",
                        "familyName": "Vu",
                        "affiliation": [],
                        "nameIdentifiers": []
                    },
                    {
                        "name": "Potter, Charles W.",
                        "nameType": "Personal",
                        "givenName": "Charles W.",
                        "familyName": "Potter",
                        "affiliation": [],
                        "nameIdentifiers": []
                    },
                    {
                        "name": "Tho, Truong Anh",
                        "nameType": "Personal",
                        "givenName": "Truong Anh",
                        "familyName": "Tho",
                        "affiliation": [],
                        "nameIdentifiers": []
                    },
                    {
                        "name": "Jefferson, Thomas A.",
                        "nameType": "Personal",
                        "givenName": "Thomas A.",
                        "familyName": "Jefferson",
                        "affiliation": [],
                        "nameIdentifiers": []
                    },
                    {
                        "name": "Kuit, Sui Hyang",
                        "nameType": "Personal",
                        "givenName": "Sui Hyang",
                        "familyName": "Kuit",
                        "affiliation": [],
                        "nameIdentifiers": []
                    },
                    {
                        "name": "Abdel-Raheem, Salma T.",
                        "nameType": "Personal",
                        "givenName": "Salma T.",
                        "familyName": "Abdel-Raheem",
                        "affiliation": [],
                        "nameIdentifiers": []
                    },
                    {
                        "name": "Hines, Ellen",
                        "nameType": "Personal",
                        "givenName": "Ellen",
                        "familyName": "Hines",
                        "affiliation": [],
                        "nameIdentifiers": []
                    }
                ],
                "titles": [
                    {
                        "title": "Whale temples are unique repositories for understanding marine mammal diversity in Central Vietnam"
                    }
                ],
                "publisher": "Lee Kong Chian Natural History Museum",
                "container": {
                    "type": "Series",
                    "title": "Raffles Bulletin of Zoology",
                    "volume": "69",
                    "lastPage": "496",
                    "firstPage": "481",
                    "identifier": "0217-2445",
                    "identifierType": "ISSN"
                },
                "publicationYear": 2021,
                "subjects": [
                    {
                        "subject": "whales"
                    },
                    {
                        "subject": "dolphins"
                    },
                    {
                        "subject": "Cetacea"
                    },
                    {
                        "subject": "natural history"
                    },
                    {
                        "subject": "dugongs"
                    },
                    {
                        "subject": "South China Sea"
                    }
                ],
                "contributors": [],
                "dates": [
                    {
                        "date": "2021",
                        "dateType": "Issued"
                    }
                ],
                "language": null,
                "types": {
                    "ris": "JOUR",
                    "bibtex": "article",
                    "citeproc": "article-journal",
                    "schemaOrg": "ScholarlyArticle",
                    "resourceType": "Journal article",
                    "resourceTypeGeneral": "Text"
                },
                "relatedIdentifiers": [
                    {
                        "relationType": "IsPartOf",
                        "relatedIdentifier": "0217-2445",
                        "relatedIdentifierType": "ISSN"
                    }
                ],
                "sizes": [],
                "formats": [],
                "version": null,
                "rightsList": [],
                "descriptions": [
                    {
                        "description": "Raffles Bulletin of Zoology, 69, 481-496",
                        "descriptionType": "SeriesInformation"
                    }
                ],
                "geoLocations": [],
                "fundingReferences": [],
                "url": "https://lkcnhm.nus.edu.sg/wp-content/uploads/sites/10/2021/11/RBZ-2021-0066.pdf",
                "contentUrl": null,
                "metadataVersion": 0,
                "schemaVersion": "http://datacite.org/schema/kernel-4",
                "source": "mds",
                "isActive": true,
                "state": "findable",
                "reason": null,
                "viewCount": 0,
                "downloadCount": 0,
                "referenceCount": 0,
                "citationCount": 0,
                "partCount": 0,
                "partOfCount": 0,
                "versionCount": 0,
                "versionOfCount": 0,
                "created": "2022-01-12T05:15:04Z",
                "registered": "2022-01-12T05:15:05Z",
                "published": null,
                "updated": "2022-01-12T05:15:05Z"
            },
            "relationships": {
                "client": {
                    "data": {
                        "id": "nus.sb",
                        "type": "clients"
                    }
                }
            }
        },
        {
            "id": "10.26107/rbz-2021-0065",
            "type": "dois",
            "attributes": {
                "doi": "10.26107/rbz-2021-0065",
                "identifiers": [],
                "creators": [
                    {
                        "name": "Mendoza, Jose Christopher E.",
                        "nameType": "Personal",
                        "givenName": "Jose Christopher E.",
                        "familyName": "Mendoza",
                        "affiliation": [],
                        "nameIdentifiers": []
                    }
                ],
                "titles": [
                    {
                        "title": "Marine crabs new to Singapore, with a description of a new species of intertidal xanthid crab of the genus Macromedaeus Ward, 1942 (Crustacea: Decapoda: Brachyura)"
                    }
                ],
                "publisher": "Lee Kong Chian Natural History Museum",
                "container": {
                    "type": "Series",
                    "title": "Raffles Bulletin of Zoology",
                    "volume": "69",
                    "lastPage": "480",
                    "firstPage": "463",
                    "identifier": "0217-2445",
                    "identifierType": "ISSN"
                },
                "publicationYear": 2021,
                "subjects": [
                    {
                        "subject": "Xanthidae"
                    },
                    {
                        "subject": "Camptandriidae"
                    },
                    {
                        "subject": "taxonomy"
                    },
                    {
                        "subject": "biodiversity"
                    },
                    {
                        "subject": "Macromedaeus adelus"
                    },
                    {
                        "subject": "Hepatoporus"
                    },
                    {
                        "subject": "Exagorium"
                    }
                ],
                "contributors": [],
                "dates": [
                    {
                        "date": "2021",
                        "dateType": "Issued"
                    }
                ],
                "language": null,
                "types": {
                    "ris": "JOUR",
                    "bibtex": "article",
                    "citeproc": "article-journal",
                    "schemaOrg": "ScholarlyArticle",
                    "resourceType": "Journal article",
                    "resourceTypeGeneral": "Text"
                },
                "relatedIdentifiers": [
                    {
                        "relationType": "IsPartOf",
                        "relatedIdentifier": "0217-2445",
                        "relatedIdentifierType": "ISSN"
                    }
                ],
                "sizes": [],
                "formats": [],
                "version": null,
                "rightsList": [],
                "descriptions": [
                    {
                        "description": "Raffles Bulletin of Zoology, 69, 463-480",
                        "descriptionType": "SeriesInformation"
                    }
                ],
                "geoLocations": [],
                "fundingReferences": [],
                "url": "https://lkcnhm.nus.edu.sg/wp-content/uploads/sites/10/2021/11/RBZ-2021-0065.pdf",
                "contentUrl": null,
                "metadataVersion": 0,
                "schemaVersion": "http://datacite.org/schema/kernel-4",
                "source": "mds",
                "isActive": true,
                "state": "findable",
                "reason": null,
                "viewCount": 0,
                "downloadCount": 0,
                "referenceCount": 0,
                "citationCount": 0,
                "partCount": 0,
                "partOfCount": 0,
                "versionCount": 0,
                "versionOfCount": 0,
                "created": "2022-01-12T05:14:59Z",
                "registered": "2022-01-12T05:15:02Z",
                "published": null,
                "updated": "2022-01-12T05:15:02Z"
            },
            "relationships": {
                "client": {
                    "data": {
                        "id": "nus.sb",
                        "type": "clients"
                    }
                }
            }
        }
    ],
    "meta": {
        "total": 12,
        "totalPages": 1,
        "page": 1,
        "states": [
            {
                "id": "findable",
                "title": "Findable",
                "count": 12
            }
        ],
        "resourceTypes": [
            {
                "id": "text",
                "title": "Text",
                "count": 12
            }
        ],
        "created": [
            {
                "id": "2022",
                "title": "2022",
                "count": 12
            }
        ],
        "published": [
            {
                "id": "2022",
                "title": "2022",
                "count": 3
            },
            {
                "id": "2021",
                "title": "2021",
                "count": 9
            }
        ],
        "registered": [
            {
                "id": "2022",
                "title": "2022",
                "count": 12
            }
        ],
        "providers": [
            {
                "id": "nus",
                "title": "National University of Singapore",
                "count": 12
            }
        ],
        "clients": [
            {
                "id": "nus.sb",
                "title": "National University of Singapore",
                "count": 12
            }
        ],
        "affiliations": [],
        "prefixes": [
            {
                "id": "10.26107",
                "title": "10.26107",
                "count": 12
            }
        ],
        "certificates": [],
        "licenses": [],
        "schemaVersions": [
            {
                "id": "4",
                "title": "Schema 4",
                "count": 12
            }
        ],
        "linkChecksStatus": [],
        "subjects": [
            {
                "id": "taxonomy",
                "title": "Taxonomy",
                "count": 3
            },
            {
                "id": "Malaysia",
                "title": "Malaysia",
                "count": 2
            },
            {
                "id": "new species",
                "title": "New Species",
                "count": 2
            },
            {
                "id": "phylogeny",
                "title": "Phylogeny",
                "count": 2
            },
            {
                "id": "Barbodes",
                "title": "Barbodes",
                "count": 1
            },
            {
                "id": "Borneo",
                "title": "Borneo",
                "count": 1
            },
            {
                "id": "Camptandriidae",
                "title": "Camptandriidae",
                "count": 1
            },
            {
                "id": "Canarium decumanum",
                "title": "Canarium Decumanum",
                "count": 1
            },
            {
                "id": "Cetacea",
                "title": "Cetacea",
                "count": 1
            },
            {
                "id": "Exagorium",
                "title": "Exagorium",
                "count": 1
            }
        ],
        "fieldsOfScience": [],
        "citations": [],
        "views": [],
        "downloads": []
    },
    "links": {
        "self": "https://api.datacite.org/dois?query=relatedIdentifiers.relatedIdentifier%3A0217-2445&registered=2022&page%5Bsize%5D=50"
    }
}';
	}
	
	$obj = json_decode($json);
	
	$dataFeed = process_datacite($obj, 'DataCite ' . $issn, $url, $issns);	
	
	print_r($dataFeed);
	
	$json_filename = $latest_dir . '/' . $issn . '.json';
 
	file_put_contents($json_filename, json_encode($dataFeed, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
}


?>
