# Extracting data from BioRSS into tables 

Need to also get data from ORCID and CrossRef, doing this using CouchDB caches, but also thinking about RDF (eventually).

## Taxa

taxa__n__.tsv have taxa to depth n

## Papers

`dois.tsv` has list of DOIs

## Authors

- get lists of ORCIDs and author counts for a set of DOIs `doi-process.php`

## Countries

- get country codes using a Wikidata query

`country-codes.csv`.


## ORCID for country

See also “Introducing ORCID” https://doi.org/10.1126/science.356.6339.691

DOI - country code
DOI - ORCID
DOI - 
ORCID - address/affiliation

```
SELECT countrydoi.country_code AS paper_country, orcidaddress.country_code AS author_country FROM countrydoi INNER JOIN orcidaddress USING(doi);
```


### Journal (not done)

- need list of journals for articles in 2022
- need countries for those journals (via Wikidata)
- need countries for each article

### GenBank (not done)

- need list of which paper used DNA, this needs a map between DOI and GenBank accessions, which we don’t have. Could develop some APIs to do this, but not done yet.

- for DNA barcodes I’m harvesting all sequences and getting DOIs from EMBL records, but many lack DOIs, even when they would exist.

- code in `doi-to-genbank.php` but this is really a separate project.


### Funding

- need DOI to funder(s)
- need funders to country (via Wikidata)

Funders of works by country 
```
SELECT 
    countrydoi.country_code AS paper_country, 
    -- doifunder.funder AS funder, 
    funder.country_code AS funder_country 
FROM countrydoi 
INNER JOIN doifunder ON  countrydoi.doi = doifunder.doi
INNER JOIN funder  USING(funder)
WHERE funder_country <> "";
```

We could plot country where work done versus funding country, then flip it to where is work funded, ie. Where in the world is China funding?

Works by country then funder

```
SELECT 
    countrydoi.country_code AS paper_country, 
    funder.country_code AS funder_country,
    COUNT(countrydoi.doi) AS papers
FROM countrydoi 
INNER JOIN doifunder ON  countrydoi.doi = doifunder.doi
INNER JOIN funder  USING(funder)
WHERE funder_country <> ""
GROUP BY paper_country, funder_country;
```

### Access

Use Unpaywall API to find out if a freely accessible PDF exists for a work given that work’s DOI. The result is in table doi-oa.tsv

Load this, then compute DOI - Access - country

5808 DOIs, 2684 are freely accessible.

### Access by country work is about

SELECT country_code, count(doi) AS c FROM `_doi-oa_20230227` INNER JOIN countrydoi USING(doi)  GROUP BY(country_code);

SELECT country_code, count(doi) AS c FROM `_doi-oa_20230227` INNER JOIN countrydoi USING(doi) WHERE access=1  GROUP BY(country_code);

### Access by country of author

SELECT country_code, count(doi) AS c FROM `_doi-oa_20230227` INNER JOIN orcidaddress USING(doi) GROUP BY(country_code);

SELECT country_code, count(doi) AS c FROM `_doi-oa_20230227` INNER JOIN orcidaddress USING(doi) WHERE access=1 GROUP BY(country_code);




