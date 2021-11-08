# bioRSS

A potential successor to [uBioRSS](http://www.ubio.org/rss/) and [uBioRSS Nomina Nova] (http://ubio.org/rss/index_nov.php)

## Idea

Take RSS feeds from journals and databases, creating them if needed, then index by taxon and geography. Output RSS feeds keyed by taxon and/or geography. Create simple visualisations.

Patrick R. Leary, David P. Remsen, Catherine N. Norton, David J. Patterson, Indra Neil Sarkar, uBioRSS: Tracking taxonomic literature using RSS, Bioinformatics, Volume 23, Issue 11, June 2007, Pages 1434–1436, https://doi.org/10.1093/bioinformatics/btm109

Little, D. P. (2020). Recognition of Latin scientific names using artificial neural networks. Applications in Plant Sciences, 8(7). doi:10.1002/aps3.11378

## Feeds

### Reading on feeds

- [Real PRISM in the RSS Wilds](https://www.crossref.org/blog/real-prism-in-the-rss-wilds/)
- [Analysing the ticTOCs collection of journal TOC feeds](https://hublog.hubmed.org/archives/001818)

### Apps that create feeds

[RSS.app](https://rss.app)

### Google Scholar Alerts

Google Scholar can send email alerts for a search term, so an obvious approach is to use these alerts as a source. How do we do this? One approach is to use a service such as [CloudMailin](https://www.cloudmailin.com) which can take an email sent to a CloudMailin email address and forward that email as a JSON document to a URL (webhook). We can then parse the contents of the email. For debugging purposes we can use a service such as [PostBin](https://postb.in) to receive these emails, for example https://postb.in/1632815014159-2470838529989. When using PostBin note that you can retrieve the body of the request using a URL like https://postb.in/api/bin/[bin-id]/req/[request-id].

The Google Scholar alert email is in HTML so we need to parse it and extract the information we require. Note that Google Scholar doesn’t include DOIs in the results, so we may have to resolve URLs and go hunting for DOIs. Some links may be PDFs, ideally we can find the corresponding HTML link so that we can parse that.


### Gotcha’s

RSS feeds are variable in terms of tags included and how they handle external namespaces. Note also that dates in RSS feeds need not be in English, which means we need to translate them before converting to ISO8601.

## Validation

 Data | Validation tool
--|--
JSON-LD | https://json-ld.org/playground/
OPML | http://validator.opml.org
RSS feed | https://validator.w3.org/feed/
Structured data using schema.org | https://validator.schema.org
