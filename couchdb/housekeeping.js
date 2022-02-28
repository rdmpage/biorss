{
  "_id": "_design/housekeeping",
  "lists": {
    "text": "function(head,req) { var row; start({ 'headers': { 'Content-Type': 'text/plain' } }); while(row = getRow()) { send(row.value + \"\\n\"); } }"
  },
  "views": {
    "id": {
      "map": "function (doc) {\n  emit(null, doc._id);\n}"
    },
    "no_date": {
      "map": "function (doc) {\n  if (!doc.message.datePublished) {\n     emit(doc._id, 1);\n  }\n}",
      "reduce": "_sum"
    },
    "keys": {
      "map": "function (doc) {\n  for (var i in doc.message) {\n     emit(i, 1);\n  }\n}",
      "reduce": "_sum"
    },
    "no_doi": {
      "reduce": "_sum",
      "map": "function get_doi(doc) {\n  var doi = '';\n  if (doc.message.item.doi) {\n    doi = doc.message.item.doi;\n  }\n  return doi;\n}\n\nfunction (doc) {\n  if (doc.message.id) {\n    var domain = doc.message.id;\n    domain = domain.replace(/https?:\\/\\/(www\\.)?/, '');\n    domain = domain.replace(/\\/(.*)$/, '');\n    var doi = get_doi(doc);\n    if (doi === '') {\n      emit(domain, 1);\n    }\n  }\n}"
    },
    "backup": {
      "map": "function (doc) {\n  emit(null, JSON.stringify(doc));\n}"
    }
  },
  "language": "javascript"
}