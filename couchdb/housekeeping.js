{
  "_id": "_design/housekeeping",
  "views": {
    "id": {
      "map": "function (doc) {\n  emit(null, doc._id);\n}"
    },
    "no_date": {
      "map": "function (doc) {\n  if (!doc.message.datePublished) {\n     emit(doc._id, 1);\n  }\n}",
      "reduce": "_sum"
    }
  },
  "language": "javascript"
}