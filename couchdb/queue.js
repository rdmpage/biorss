{
  "_id": "_design/queue",
  "views": {
    "no_doi": {
      "map": "// List of items without a DOI\nfunction (doc) {\n  if (!doc.message.item.doi) {\n    emit(doc['message-timestamp'], doc._id);\n  }\n}"
    },
    "doi_no_agency": {
      "map": "// List of items with DOI but without DOI agency\nfunction (doc) {\n  if (doc.message.item.doi) {\n    if (!doc.message.item.doi_agency) {\n      emit(doc['message-timestamp'], doc._id);\n    }\n  }\n}"
    },
    "doi_no_wikidata": {
      "map": "// List of items with DOI but without DOI agency\nfunction (doc) {\n  if (doc.message.item.doi) {\n    if (!doc.message.item.wikidata) {\n      emit(doc['message-timestamp'], doc._id);\n    }\n  }\n}"
    },
    "oup_image": {
      "map": "// List of items OUP image (likely broken)\nfunction (doc) {\n  if (doc.message.image) {\n    if (doc.message.image.match(/^data:(application|text)\\/xml;/))\n    emit(doc['message-timestamp'], doc._id);\n  }\n}"
    }
  },
  "language": "javascript"
}