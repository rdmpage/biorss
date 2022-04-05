{
  "_id": "_design/key",
  "lists": {
    "text": "function(head,req) { var row; start({ 'headers': { 'Content-Type': 'text/plain' } }); while(row = getRow()) { send(row.value + \"\\n\"); } }"
  },
  "views": {
    "doi": {
      "reduce": "_sum",
      "map": "function get_doi(doc) {\n  var doi = '';\n  if (doc.message.item.doi) {\n    doi = doc.message.item.doi;\n  }\n  return doi;\n}\n\nfunction (doc) {\n  var doi = get_doi(doc);\n  if (doi != '') {\n    emit(doi, 1);\n  }\n}"
    },
    "keywords": {
      "reduce": "_sum",
      "map": "function (doc) {\n  if (doc.message.keywords) {\n    for (var i in doc.message.keywords) {\n      emit(doc.message.keywords[i], 1);\n    }\n  }\n}"
    },
    "classification": {
      "reduce": "_sum",
      "map": "function (doc) {\n  if (doc.message.classification) {\n    // need to convert object to array\n    var classification = [\"BIOTA\"];\n    for (var i in doc.message.classification) {\n      classification.push(doc.message.classification[i]);\n    }\n    emit(classification, 1);\n  }\n}"
    },
    "place": {
      "reduce": "_sum",
      "map": "function (doc) {\n  if (doc.message.contentLocation) {\n    for (var i in doc.message.contentLocation) {\n      emit(doc.message.contentLocation[i].name, 1);\n    }\n  }\n}"
    },
    "country_code": {
      "reduce": "_sum",
      "map": "function (doc) {\n  if (doc.message.contentLocation) {\n    for (var i in doc.message.contentLocation) {\n      if (doc.message.contentLocation[i].geo.addressCountry) {\n        emit(doc.message.contentLocation[i].geo.addressCountry, 1);\n      }\n    }\n  }\n}"
    },
    "lat_lon": {
      "reduce": "_sum",
      "map": "function (doc) {\n  if (doc.message.contentLocation) {\n    for (var i in doc.message.contentLocation) {\n      if (doc.message.contentLocation[i].geo) {\n        emit([doc.message.contentLocation[i].geo.latitude,doc.message.contentLocation[i].geo.longitude], 1);\n      }\n    }\n  }\n}"
    },
    "datePublished": {
      "reduce": "_sum",
      "map": "function (doc) {\n  if (doc.message.datePublished) {\n     emit(doc.message.datePublished.substring(0,10), 1);\n  }\n}"
    },
    "pdf": {
      "reduce": "_sum",
      "map": "function (doc) {\n  if (doc.message.item.pdf) {\n    emit(doc.message.item.pdf, 1);\n  }\n}"
    },
    "title": {
      "reduce": "_sum",
      "map": "function (doc) {\n  if (doc.message.item.name) {\n     emit(doc.message.item.name, 1);\n  }\n}"
    },
    "query": {
      "map": "function (doc) {\n  \n  // by default everything is in the WORLD\n  var geo_code = ['WORLD'];\n  \n  // add any country codes to our list\n  if (doc.message.contentLocation) {\n    for (var i in doc.message.contentLocation) {\n      if (doc.message.contentLocation[i].geo.addressCountry) {\n        geo_code.push(doc.message.contentLocation[i].geo.addressCountry);\n      }\n    }\n  }\n  \n  // taxonomy\n  \n  // by default everything is part of BIOTA\n  var classification = ['BIOTA'];\n  \n  // add classification path\n  if (doc.message.classification) {\n    for (var i in doc.message.classification) {\n      classification.push(doc.message.classification[i]);\n    }\n  }\n  \n // First key is geography\n for (var i in geo_code) {\n\n   // Second key is taxonomy\n   var n = classification.length;\n   for (var j = 1; j <= n; j++) {\n     var path = classification.slice(0, j);\n     \n     var key = [];\n     key.push(geo_code[i]);\n     key.push(path.join('-'));\n     \n     if (doc.message.datePublished) {\n       var date = doc.message.datePublished.substring(0,10).split('-');\n       key = key.concat(date);\n       \n       // simple object to output\n\t\t  var dataFeedElement = {};\n\t\t  dataFeedElement.url = doc.message.url;\n\t\t  dataFeedElement.name = doc.message.name;\n\t\t  dataFeedElement.datePublished = doc.message.datePublished;\n\t\t  \n\t\t  dataFeedElement.item = {};\n\n      if (doc.message.description) {\n        dataFeedElement.description = doc.message.description;\n      }\n\n      if (doc.message.image) {\n        dataFeedElement.image = doc.message.image;\n      }\n      \n      // tags, including taxon names\n      if (doc.message.keywords) {\n        dataFeedElement.keywords = doc.message.keywords;\n      }\n      \n      // localities\n      if (doc.message.contentLocation) {\n        dataFeedElement.contentLocation = doc.message.contentLocation;\n      }\n      \n      // article metadata\n      if (doc.message.item) {\n        dataFeedElement.item = doc.message.item;\n      }\n\n      \n     \n      emit(key, dataFeedElement);\n     }\n     \n    \n   }\n   \n   \n  \n }\n \n}"
    },
    "meta": {
      "reduce": "_sum",
      "map": "function (doc) {\n  if (doc.message.meta) {\n    for (var i in doc.message.meta) {\n      emit(doc.message.meta[i], 1);\n    }\n  }\n}"
    },
    "domain": {
      "reduce": "_sum",
      "map": "function (doc) {\n  if (doc.message.id) {\n    var domain = doc.message.id;\n    \n    // URL http\n    domain = domain.replace(/https?:\\/\\/(www\\.)?/, '');\n    domain = domain.replace(/\\/(.*)$/, '');\n    \n    // lsid e.g. urn:lsid:indexfungorum.org:names:816410\n    domain = domain.replace(/urn:lsid:/, '');\n    domain = domain.replace(/:(.*)$/, '');\n   \n    emit(domain, 1);\n  }\n}"
    },
    "search": {
      "map": "// Inspired by https://blog.kabir.sh/inside-wade\n// https://github.com/kbrsh/wade/blob/master/src/index.js\n\nfunction (doc) {\n  \n var stopWords = [\"about\", \"after\", \"all\", \"also\", \"am\", \"an\", \"and\", \"another\", \"any\", \"are\", \"as\", \"at\", \"be\", \"because\", \"been\", \"before\", \"being\", \"between\", \"both\", \"but\", \"by\", \"came\", \"can\", \"come\", \"could\", \"did\", \"do\", \"each\", \"for\", \"from\", \"get\", \"got\", \"has\", \"had\", \"he\", \"have\", \"her\", \"here\", \"him\", \"himself\", \"his\", \"how\", \"if\", \"in\", \"into\", \"is\", \"it\", \"like\", \"make\", \"many\", \"me\", \"might\", \"more\", \"most\", \"much\", \"must\", \"my\", \"never\", \"now\", \"of\", \"on\", \"only\", \"or\", \"other\", \"our\", \"out\", \"over\", \"said\", \"same\", \"see\", \"should\", \"since\", \"some\", \"still\", \"such\", \"take\", \"than\", \"that\", \"the\", \"their\", \"them\", \"then\", \"there\", \"these\", \"they\", \"this\", \"those\", \"through\", \"to\", \"too\", \"under\", \"up\", \"very\", \"was\", \"way\", \"we\", \"well\", \"were\", \"what\", \"where\", \"which\", \"while\", \"who\", \"with\", \"would\", \"you\", \"your\", \"a\", \"i\"];\n var punctuationRE = /[†\\/&\\—\\–\\-\\‐“”‘’…@!\"',.:;?()[\\]<>]/g;\n var whitespaceRE = /\\s+/g;\n \n  if (doc.message.name) {\n    // index article name\n    var text = doc.message.name;\n    \n    /*\n    if (doc.message.description) {\n      text += doc.message.description;\n    }\n    */\n    \n    /*\n    if (doc.message.keywords) {\n      text += ' ' + doc.message.keywords.join(' ');\n    }\n    */\n    \n   if (doc.message.classification) {\n      text += ' ' + doc.message.classification.join(' ');\n    }    \n    \n    text = text.toLowerCase();\n    text = text.replace(punctuationRE, ' ');\n    text = text.replace(/\\s\\s+/, ' ');\n    text = text.replace(/^\\s+/, '');\n    text = text.replace(/\\s+$/, '');\n    \n    var terms = text.split(whitespaceRE);\n   \n     var i = terms.length;\n\n      while((i--) !== 0) {\n        if(stopWords.indexOf(terms[i]) !== -1) {\n          terms.splice(i, 1);\n        }\n      }\n      \n      // count \n      var m = {};\n      for (var i in terms) {\n        if (!m[terms[i]]) {\n          m[terms[i]] = 0;\n        }\n        m[terms[i]]++;\n      }\n      \n      //emit(m, 1);\n      \n    var cardinality = Object.keys(m).length;\n    for (var i in m) {\n      emit(i, m[i]/cardinality);\n    }\n    \n    \n  }\n}",
      "reduce": "_sum"
    },
    "container": {
      "reduce": "_sum",
      "map": "function (doc) {\n  if (doc.message.item.container) {\n     emit(doc.message.item.container, 1);\n  }\n}"
    },
    "doi_export": {
      "map": "function get_doi(doc) {\n  var doi = '';\n  if (doc.message.item.doi) {\n    doi = doc.message.item.doi;\n  }\n  return doi;\n}\n\nfunction (doc) {\n  var doi = get_doi(doc);\n  if (doi !== '') {\n    emit(null, doi);\n  }\n}"
    },
    "doi_agency": {
      "map": "function (doc) {\n  if (doc.message.item.doi_agency) {\n    emit(doc.message.item.doi_agency, doc.message.item.doi);\n  }\n}"
    }
  },
  "language": "javascript"
}