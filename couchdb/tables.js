{
  "_id": "_design/tables",
  "views": {
    "country": {
      "reduce": "_sum",
      "map": "function(doc) {\n\n    if (doc.message.datePublished) {\n        var date = doc.message.datePublished.substring(0, 4);\n\n        // do we have a country?\n        if (doc.message.contentLocation) {\n            for (var i in doc.message.contentLocation) {\n                if (doc.message.contentLocation[i].geo.addressCountry) {\n                    var key = [];\n                    key.push(date);\n                    key.push(doc.message.contentLocation[i].geo.addressCountry);\n                    emit(key, 1);\n                }\n            }\n        }\n    }\n}"
    },
    "taxa": {
      "reduce": "_sum",
      "map": "function(doc) {\n    if (doc.message.datePublished) {\n        var key = [];\n\n        var date = doc.message.datePublished.substring(0, 4);\n        key.push(date);\n\n        if (doc.message.classification) {\n            // need to convert object to array\n            key.push(\"BIOTA\");\n            for (var i in doc.message.classification) {\n                key.push(doc.message.classification[i]);\n            }\n            emit(key, 1);\n        }\n    }\n}"
    },
    "doi": {
      "reduce": "_sum",
      "map": "function get_doi(doc) {\n  var doi = '';\n  if (doc.message.item.doi) {\n    doi = doc.message.item.doi;\n    \n    // clean up\n    doi = doi.replace(/https?\\/\\/(dx\\.)?doi.org\\//, '');\n    \n    if (!doi.match(/^10\\.\\d+/)) {\n      doi = '';\n    }\n  }\n  return doi;\n}\n\nfunction(doc) {\n  \n    if (doc.message.datePublished) {\n        var key = [];\n\n        var date = doc.message.datePublished.substring(0, 4);\n        key.push(date);\n        \n        var doi = get_doi(doc);\n\n        if (doi !== \"\") {\n            // need to convert object to array\n            key.push(doi);\n            emit(key, 1);\n        }\n    }\n}"
    },
    "doi-country": {
      "map": "function get_doi(doc) {\n  var doi = '';\n  if (doc.message.item.doi) {\n    doi = doc.message.item.doi;\n    \n    // clean up\n    doi = doi.replace(/https?\\/\\/(dx\\.)?doi.org\\//, '');\n    \n    if (!doi.match(/^10\\.\\d+/)) {\n      doi = '';\n    }\n  }\n  return doi;\n}\n\nfunction(doc) {\n  var doi = get_doi(doc);\n  \n    if (doi !== '') {\n         // do we have a country?\n        if (doc.message.contentLocation) {\n            for (var i in doc.message.contentLocation) {\n                if (doc.message.contentLocation[i].geo.addressCountry) {\n                    emit(doi, doc.message.contentLocation[i].geo.addressCountry);\n                }\n            }\n        }\n    }\n}"
    },
    "year-doi-country": {
      "map": "function get_doi(doc) {\n  var doi = '';\n  if (doc.message.item.doi) {\n    doi = doc.message.item.doi;\n    \n    // clean up\n    doi = doi.replace(/https?\\/\\/(dx\\.)?doi.org\\//, '');\n    \n    if (!doi.match(/^10\\.\\d+/)) {\n      doi = '';\n    }\n  }\n  return doi;\n}\n\nfunction(doc) {\n  var doi = get_doi(doc);\n  \n    if (doi !== '') {\n\n        var key = [];\n\n        var date = doc.message.datePublished.substring(0, 4);\n        key.push(date);\n        \n        key.push(doi);\n      \n      \n         // do we have a country?\n        if (doc.message.contentLocation) {\n            for (var i in doc.message.contentLocation) {\n                if (doc.message.contentLocation[i].geo.addressCountry) {\n                    key.push(doc.message.contentLocation[i].geo.addressCountry);\n                    emit(key, 1);\n                }\n            }\n        }\n    }\n}",
      "reduce": "_sum"
    }
  },
  "language": "javascript"
}