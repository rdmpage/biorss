# bioRSS

A potential successor to [uBioRSS](http://www.ubio.org/rss/) and [uBioRSS Nomina Nova](http://ubio.org/rss/index_nov.php). See also my experience with [bioGUID](https://github.com/rdmpage/bioguid/tree/master/www/rss) a decade or more ago.

## Idea

Take RSS feeds from journals and databases, creating them if needed, then index by taxon and geography. Output RSS feeds keyed by taxon and/or geography. Create simple visualisations.

Original goal was to rely on RSS feeds, or generate my own RSS from various sources. Now seems better to use RSS if available, but otherwise generate schema.org-style JSON and use that directly for other, potentially richer sources.


## Feeds

### Reading on feeds

- [Real PRISM in the RSS Wilds](https://www.crossref.org/blog/real-prism-in-the-rss-wilds/)
- [Analysing the ticTOCs collection of journal TOC feeds](https://hublog.hubmed.org/archives/001818)

### Apps that create feeds

[RSS.app](https://rss.app)

### Google Scholar Alerts

Google Scholar can send email alerts for a search term, so an obvious approach is to use these alerts as a source. How do we do this? One approach is to use a service such as [CloudMailin](https://www.cloudmailin.com) which can take an email sent to a CloudMailin email address and forward that email as a JSON document to a URL (webhook). We can then parse the contents of the email. For debugging purposes we can use a service such as [PostBin](https://postb.in) to receive these emails, for example https://postb.in/1632815014159-2470838529989. When using PostBin note that you can retrieve the body of the request using a URL like https://postb.in/api/bin/[bin-id]/req/[request-id].

The Google Scholar alert email is in HTML so we need to parse it and extract the information we require. Note that Google Scholar doesn’t include DOIs in the results, so we may have to resolve URLs and go hunting for DOIs. Some links may be PDFs, ideally we can find the corresponding HTML link so that we can parse that.

### Pensoft

Lyubo mentions OAI endpoint, investigate further.

### PubMed

PubMed supports the creation of RSS feeds based on user searches, e.g.  [("new species") OR ("n. sp.") OR ("sp. nov.") OR ("n. gen.") OR ("gen. nov.") OR ("n. comb.") OR ("comb. nov.”)](https://pubmed.ncbi.nlm.nih.gov/rss-feed/?feed_id=1rE397IRBYU0-ogsyRnEw9o91K808u0evolcHK9IDZ0PVH5cqD&amp;v=2.15.0&amp;utm_source=Rested&amp;utm_medium=rss&amp;utm_content=1rE397IRBYU0-ogsyRnEw9o91K808u0evolcHK9IDZ0PVH5cqD&amp;fc=20211108074834&amp;utm_campaign=pubmed-2&amp;ff=20211108074851)

### Wanfang

Scrape using JSON.

### ZooBank

ZooBank has RSS but it doesn’t seem to be updated(?). Can also query using year as a search term. JSON data doesn’t have precise time, nor does it have the DOI. GBIF https://www.gbif.org/dataset/c8227bb4-4143-443f-8cb2-51f9576aff14 https://doi.org/10.15468/wkr0kn seems to lag behind ZooBank.


### Zootaxa

Zootaxa has RSS feeds, but also has a taxon search feature, e.g., https://www.mapress.com/zt/search/search?query=Coleoptera&authors=&dateFromYear=2021&dateFromMonth=11&dateFromDay=&dateToYear=&dateToMonth=&dateToDay=&subject=&title=&abstract=&indexTerms= which might be used to generate taxon-specific feeds.

### Gotcha’s

RSS feeds are variable in terms of tags included and how they handle external namespaces. Note also that dates in RSS feeds need not be in English, which means we need to translate them before converting to ISO8601.

## Validation

 Data | Validation tool
--|--
JSON-LD | https://json-ld.org/playground/
OPML | http://validator.opml.org
RSS feed | https://validator.w3.org/feed/
Structured data using schema.org | https://validator.schema.org

## Visualisation

Feed is a list in descending time order, taxon facet is a treemap, geography facet is a map.

## References

Patrick R. Leary, David P. Remsen, Catherine N. Norton, David J. Patterson, Indra Neil Sarkar, uBioRSS: Tracking taxonomic literature using RSS, Bioinformatics, Volume 23, Issue 11, June 2007, Pages 1434–1436, https://doi.org/10.1093/bioinformatics/btm109

Little, D. P. (2020). Recognition of Latin scientific names using artificial neural networks. Applications in Plant Sciences, 8(7). doi:10.1002/aps3.11378

Mindell, D. P., Fisher, B. L., Roopnarine, P., Eisen, J., Mace, G. M., Page, R. D. M., & Pyle, R. L. (2011). Aggregating, Tagging and Integrating Biodiversity Research. PLoS ONE, 6(8), e19491. doi:10.1371/journal.pone.0019491
