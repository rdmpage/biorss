# BioRSS: taming the taxonomic firehose

## Introduction


A potential successor to [uBioRSS](http://www.ubio.org/rss/) and [uBioRSS Nomina Nova](http://ubio.org/rss/index_nov.php). 




### Streams

I use the term “stream” to mean any temporally ordered list of publications or taxa, for example a list of the latest articles published in a journal, or a list of new species recently added to a taxonomic database. The original ubioRSS relied solely on journal Really Simple Syndication (RSS) feeds which are often made available by publishers. RSS feeds come in three similar formats, so that the same code can be used to consume data from multiple feeds.

However, the use of RSS by journals has declined, with many publishers not providing this access to their content. Hence we need to add other streams if we are to increase coverage of the taxonomic literature. These include Google Scholar email alerts, PubMed searches, and querying taxonomic databases. Literature searches for terms such as “n. sp.” can return articles more likely to be about new species than simply consuming RSS feeds for multiple journals.

IPNI, ZooBank, Index Fungorum


The advantage of using journal-specific RSS feeds is that each journal only publishes its own content. However, using search engines and taxonomic databases raises the prospect of the same publication appearing in multiple feeds. One way to detect duplicate is using DOIs: if two articles share the same DOI then they are the same article. However, articles retrieved by search engines may not have DOIs associated with them, even if the article does, in fact, have a DOI. For example, Google Scholar searches often include links to PDFs rather than DOIs. Hence to help deduplicate the data we will need tools to retrieve DOIs for PDFs.

### Approach 

1. RSS feeds from journals are regularly polled and added. RSS converted to “internal” format, then augmented by adding DOIs, geography and taxa. Store the status of each feed in `feed status.json`. Sadly many RSS feeds don’t support conditional GET.

2. Some sources (e.g., Google Scholar, ZooBank) will be converted directly to “internal” format, then augmented.

3. Feed item is modelled as a schema.org `DataFeedItem` with the publication as an `item`.


### Enhancing records

RSS feeds and other streams typically lack detailed metadata about the items in that stream. This is by design, for each item in an RSS feed we typically only need a title, a description, a date, and a link to the item. This enables clients such as feed readers to display enough information about an item for the user of that client to judge whether they want to read that item. However, for our purposes we could benefit from a little more information. For example, for a scientific publication we would like the DOI.

To enhance an item the workflow calls a web service that takes the link URL, resolves it to a web page, and attempts to extract metadata from that web page. 


## Methods


### Data sources


### Taxonomic name parsing

Possible taxonomic names are extracted from text using Patrick Leary’s TaxonFinder http://taxonfinder.org. In order to support hierarchical navigation each name is then matched to the GBIF backbone classification using the Global Names Index https://index.globalnames.org. Hence for each name matched to GBIF we have an ordered list of taxa from the root (“life”) to that name. To simplify navigation I assign each paper to a single place in the GBIF classification by extracting the majority-rule  classification (Margush T, McMorris ), that is, the longest path that occurs in more than half of the paths for each name. 

### Geoparsing

Typically geoparsing involves taking a body of text and undertaking the following two steps: (a) using Named-entity recognition (NER) to identity named entities in the text (e.g., place names), and (b) using a gazetteer of geographic names (e.g., [GeoNames](http://www.geonames.org) try and match the place names found by NER. 

For BioRSS I use a simple geoparser that is focussed on large areas such as countries and major divisions within countries. A small gazetteer is constructed from Wikidata using searches for countries, first-order administrative areas (ADM1), large islands, etc. The place names are stored in a trie data structure, along with the corresponding latitude and longitude. To geoparse a block of text (such as the title or abstract for a paper) that text is processed using the FlashText algorithm (Singh, 2007). The algorithm starts with the first word in the text, if that exists in the gazetteer we first try to extend that word to see if it matches a multi-word place name). Once we can’t extend the match any further we output the match and move to the next unmatched word in the block of text and repeat the process until we reach the end of the text.

### Data storage

The …


### Interface

The goal of the interface is …




## References

Patrick R. Leary, David P. Remsen, Catherine N. Norton, David J. Patterson, Indra Neil Sarkar, uBioRSS: Tracking taxonomic literature using RSS, Bioinformatics, Volume 23, Issue 11, June 2007, Pages 1434–1436, https://doi.org/10.1093/bioinformatics/btm109

Mindell, D. P., Fisher, B. L., Roopnarine, P., Eisen, J., Mace, G. M., Page, R. D. M., & Pyle, R. L. (2011). Aggregating, Tagging and Integrating Biodiversity Research. PLoS ONE, 6(8), e19491. doi:10.1371/journal.pone.0019491

Margush T, McMorris FR. 1981. Consensusn-trees. Bulletin of Mathematical Biology 43:239–244. DOI: 10.1016/S0092-8240(81)90019-7.

Singh, V. (2017). Replace or Retrieve Keywords In Documents at Scale. CoRR, abs/1711.00046. http://arxiv.org/abs/1711.00046
