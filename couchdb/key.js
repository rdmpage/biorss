{
  "_id": "_design/key",
  "views": {
    "doi": {
      "reduce": "_sum",
      "map": "function get_doi(doc) {\n  var doi = '';\n  if (doc.message.doi) {\n    doi = doc.message.doi;\n  }\n  return doi;\n}\n\nfunction (doc) {\n  var doi = get_doi(doc);\n  if (doi != '') {\n    emit(doi, 1);\n  }\n}"
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
    "image": {
      "reduce": "_sum",
      "map": "function (doc) {\n  if (doc.message.image) {\n     emit(doc.message.image, 1);\n  }\n}"
    },
    "pdf": {
      "reduce": "_sum",
      "map": "function (doc) {\n  if (doc.message.pdf) {\n    emit(doc.message.pdf, 1);\n  }\n}"
    },
    "title": {
      "reduce": "_sum",
      "map": "function (doc) {\n  if (doc.message.name) {\n     emit(doc.message.name, 1);\n  }\n}"
    },
    "query": {
      "map": "function (doc) {\n  \n  // by default everything is in the WORLD\n  var geo_code = ['WORLD'];\n  \n  // add any country codes to our list\n  if (doc.message.contentLocation) {\n    for (var i in doc.message.contentLocation) {\n      if (doc.message.contentLocation[i].geo.addressCountry) {\n        geo_code.push(doc.message.contentLocation[i].geo.addressCountry);\n      }\n    }\n  }\n  \n  // taxonomy\n  \n  // by default everything is part of BIOTA\n  var classification = ['BIOTA'];\n  \n  // add classification path\n  if (doc.message.classification) {\n    for (var i in doc.message.classification) {\n      classification.push(doc.message.classification[i]);\n    }\n  }\n  \n // First key is geography\n for (var i in geo_code) {\n\n   // Second key is taxonomy\n   var n = classification.length;\n   for (var j = 1; j < n; j++) {\n     var path = classification.slice(0, j);\n     \n     var key = [];\n     key.push(geo_code[i]);\n     key.push(path.join('-'));\n     \n     if (doc.message.datePublished) {\n       var date = doc.message.datePublished.substring(0,10).split('-');\n       key = key.concat(date);\n       \n       // simple object to output\n\t\t  var dataFeedElement = {};\n\t\t  dataFeedElement.url = doc.message.url;\n\t\t  dataFeedElement.name = doc.message.name;\n\t\t  dataFeedElement.datePublished = doc.message.datePublished;\n\n      if (doc.message.description) {\n        dataFeedElement.description = doc.message.description;\n      }\n\n      if (doc.message.doi) {\n        dataFeedElement.doi = doc.message.doi;\n      }\n      if (doc.message.image) {\n        dataFeedElement.image = doc.message.image;\n      }\n      \n      // tags, including taxon names\n     if (doc.message.keywords) {\n        dataFeedElement.keywords = doc.message.keywords;\n      }\n      \n      // localities\n      if (doc.message.contentLocation) {\n        dataFeedElement.contentLocation = doc.message.contentLocation;\n      }\n     \n        emit(key, dataFeedElement);\n     }\n     \n    \n   }\n   \n   \n  \n }\n \n}"
    }
  },
  "language": "javascript"
}