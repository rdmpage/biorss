# bioRSS

A potential successor to ubioRSS.


## Feeds

### Work on feeds

- [Real PRISM in the RSS Wilds](https://www.crossref.org/blog/real-prism-in-the-rss-wilds/)
- [Analysing the ticTOCs collection of journal TOC feeds](https://hublog.hubmed.org/archives/001818)

### Apps that create feeds

[RSS.app](https://rss.app)

### Gotcha’s

RSS feeds are wildly variable in terms of tags included and how they handle external namespaces. Note also that dates in RSS feeds need not be in English, which means we need to translate them before converting to ISO8601.

## Validation

 Data | Validation tool
--|--
JSON-LD | https://json-ld.org/playground/
RSS feed | https://validator.w3.org/feed/
Structured data using schema.org | https://validator.schema.org
