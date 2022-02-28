<?php

// Delete records from CouchDB to start afresh

require_once (dirname(__FILE__) . '/config.inc.php');
require_once (dirname(__FILE__) . '/couchsimple.php');

// Get list of documents
// http://127.0.0.1:5984/biorss/_design/housekeeping/_view/id

$json = '{
    "total_rows": 854,
    "offset": 232,
    "rows": [
        {
            "id": "https://doi.org/10.5281/zenodo.4588314",
            "key": "Israel Journal of Entomology",
            "value": 1
        },
        {
            "id": "https://doi.org/10.5281/zenodo.4588315",
            "key": "Israel Journal of Entomology",
            "value": 1
        },
        {
            "id": "https://doi.org/10.5281/zenodo.4661938",
            "key": "Israel Journal of Entomology",
            "value": 1
        },
        {
            "id": "https://doi.org/10.5281/zenodo.4661939",
            "key": "Israel Journal of Entomology",
            "value": 1
        },
        {
            "id": "https://doi.org/10.5281/zenodo.4682872",
            "key": "Israel Journal of Entomology",
            "value": 1
        },
        {
            "id": "https://doi.org/10.5281/zenodo.4682873",
            "key": "Israel Journal of Entomology",
            "value": 1
        },
        {
            "id": "https://doi.org/10.5281/zenodo.4718390",
            "key": "Israel Journal of Entomology",
            "value": 1
        },
        {
            "id": "https://doi.org/10.5281/zenodo.4718391",
            "key": "Israel Journal of Entomology",
            "value": 1
        },
        {
            "id": "https://doi.org/10.5281/zenodo.4719051",
            "key": "Israel Journal of Entomology",
            "value": 1
        },
        {
            "id": "https://doi.org/10.5281/zenodo.4719052",
            "key": "Israel Journal of Entomology",
            "value": 1
        },
        {
            "id": "https://doi.org/10.5281/zenodo.4730133",
            "key": "Israel Journal of Entomology",
            "value": 1
        },
        {
            "id": "https://doi.org/10.5281/zenodo.4730134",
            "key": "Israel Journal of Entomology",
            "value": 1
        },
        {
            "id": "https://doi.org/10.5281/zenodo.4730422",
            "key": "Israel Journal of Entomology",
            "value": 1
        },
        {
            "id": "https://doi.org/10.5281/zenodo.4730423",
            "key": "Israel Journal of Entomology",
            "value": 1
        },
        {
            "id": "https://doi.org/10.5281/zenodo.4736114",
            "key": "Israel Journal of Entomology",
            "value": 1
        },
        {
            "id": "https://doi.org/10.5281/zenodo.4736115",
            "key": "Israel Journal of Entomology",
            "value": 1
        },
        {
            "id": "https://doi.org/10.5281/zenodo.4745915",
            "key": "Israel Journal of Entomology",
            "value": 1
        },
        {
            "id": "https://doi.org/10.5281/zenodo.4745916",
            "key": "Israel Journal of Entomology",
            "value": 1
        },
        {
            "id": "https://doi.org/10.5281/zenodo.4817731",
            "key": "Israel Journal of Entomology",
            "value": 1
        },
        {
            "id": "https://doi.org/10.5281/zenodo.4817732",
            "key": "Israel Journal of Entomology",
            "value": 1
        },
        {
            "id": "https://doi.org/10.5281/zenodo.5063022",
            "key": "Israel Journal of Entomology",
            "value": 1
        },
        {
            "id": "https://doi.org/10.5281/zenodo.5063023",
            "key": "Israel Journal of Entomology",
            "value": 1
        },
        {
            "id": "https://doi.org/10.5281/zenodo.5084793",
            "key": "Israel Journal of Entomology",
            "value": 1
        },
        {
            "id": "https://doi.org/10.5281/zenodo.5089704",
            "key": "Israel Journal of Entomology",
            "value": 1
        }
    ]
}';

$obj = json_decode($json);

foreach ($obj->rows as $row)
{
	//echo $row->value . "\n";		
	$couch->add_update_or_delete_document(null, $row->value, 'delete');
}

?>