{
  "_id": "_design/visualise",
  "views": {
    "date-country": {
      "reduce": "_sum",
      "map": "function(doc) {\n  // count countries by date \n\n  if (doc.message.datePublished) {\n    // date\n    var date = doc.message.datePublished.substring(0, 10).split('-');\n\n    // country codes\n    if (doc.message.contentLocation) {\n      for (var i in doc.message.contentLocation) {\n        if (doc.message.contentLocation[i].geo.addressCountry) {\n          var key = []\n          key.push(date[0]);\n          key.push(date[1]);\n          key.push(doc.message.contentLocation[i].geo.addressCountry);\n\n          emit(key, 1);\n        }\n      }\n    }\n  }\n}"
    },
    "date-classification": {
      "reduce": "_sum",
      "map": "function(doc) {\n\n  // count taxa by date \n\n  if (doc.message.datePublished) {\n    var date = doc.message.datePublished.substring(0, 10).split('-');\n\n    var classification = [\"BIOTA\"];\n    \n    for (var i in doc.message.classification) {\n      classification.push(doc.message.classification[i]);\n    }\n    \n    var n = classification.length;\n    for (var j = 1; j <= n; j++) {\n      var path = classification.slice(0, j);\n\n      var key = []\n      key.push(date[0]);\n      key.push(date[1]);\n      \n      key.push(path.join(\"-\"));\n\n      /*\n      var m = path.length;\n      for (var k = 0; k < m; k++) {\n        key.push(path[k]);\n      }\n      */\n\n      emit(key, 1);\n    }\n  }\n}"
    },
    "year-country": {
      "reduce": "_sum",
      "map": "function(doc) {\n  // count countries by date \n\n  if (doc.message.datePublished) {\n    // date\n    var date = doc.message.datePublished.substring(0, 10).split('-');\n\n    // country codes\n    if (doc.message.contentLocation) {\n      for (var i in doc.message.contentLocation) {\n        if (doc.message.contentLocation[i].geo.addressCountry) {\n          var key = []\n          key.push(parseInt(date[0]));\n          key.push(doc.message.contentLocation[i].geo.addressCountry);\n\n          emit(key, 1);\n        }\n      }\n    }\n  }\n}"
    },
    "year-classification": {
      "reduce": "_sum",
      "map": "function(doc) {\n\n  // count taxa by date \n\n  if (doc.message.datePublished) {\n    var date = doc.message.datePublished.substring(0, 10).split('-');\n\n    var classification = [\"BIOTA\"];\n    \n    for (var i in doc.message.classification) {\n      classification.push(doc.message.classification[i]);\n    }\n    \n    var n = classification.length;\n    if (n >= 3) {\n      var path = classification.slice(1, 3);\n\n      var key = []\n      key.push(parseInt(date[0]));\n      key.push(path[0]);\n      key.push(path[1]);\n   \n      emit(key, 1);\n    }\n  }\n}"
    }
  },
  "language": "javascript"
}