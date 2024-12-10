{
  "_id": "_design/export",
  "_rev": "3-390ef580dbee1f805b89e15dbb5e0949",
  "views": {
    "json": {
      "map": "function (doc) {\n  if (doc.message.type == 'DataFeedItem') {\n    if (doc.message.item) \n    {\n      emit(doc._id, doc.message.item);\n    }\n  }}"
    }
  },
  "language": "javascript"
}
{
  "_id": "_design/keys",
  "_rev": "2-35f2946ea02526a885be348ff5b5899f",
  "views": {
    "name": {
      "map": "function (doc) {\n  if (doc.message.type == 'DataFeedItem' && doc.message.name) {\n    emit(doc.message.name, null);\n  }\n}"
    },
    "doi": {
      "reduce": "_sum",
      "map": "function (doc) {\n  if (doc.message.type == 'DataFeedItem') {\n    if (doc.message.item.doi) \n    {\n      emit(doc.message.item.doi, 1);\n    }\n  }\n}"
    }
  },
  "language": "javascript"
}
{
  "_id": "_design/queue",
  "_rev": "44-7f0e552eac7556b8ae5a7280c8cbffea",
  "views": {
    "DataFeed": {
      "map": "function (doc) {\n  if (doc.message.type == 'DataFeed') {\n    \n    // when was this feed last modified?\n    var d = new Date(doc['message-modified']);\n    var last = Math.round(d.getTime() / 1000);\n \n    // what is the time now?\n    var now = Math.round(Date.now() / 1000);\n    \n    // how much time has elapsed?\n    var time_since_harvested = parseInt(now - last);\n    \n    \n    var output = {};\n    output.modified = last;\n    output.now = now;\n    output.elapsed = parseInt(time_since_harvested);\n    output.polling_interval = parseInt(doc.message.polling_interval);\n    \n    if (0) {\n      emit(doc._id,  output);\n    } else {\n        // if the elapsed time is > polling interval, add to queue\n        if (output.elapsed > output.polling_interval) {\n          emit(doc._id,  output.elapsed);\n      }\n    }\n  }\n}"
    },
    "doi": {
      "map": "function (doc) {\n  if (doc.message.type == 'DataFeedItem') {\n    var add_to_queue = true;\n    \n    if (add_to_queue) {\n      // don't add if we have a DOI\n      if (doc.message.item.doi) {\n        add_to_queue = false;\n      }\n    }\n    \n    if (add_to_queue) {\n      // Don't add if DOI bot has already visited\n      if (doc.message.bots) {\n        if (doc.message.bots.doi) {\n          add_to_queue = false;\n        }\n      }\n    }    \n    \n    if (add_to_queue) \n    {\n      emit(doc['message-timestamp'], doc._id);\n    }\n  }\n}"
    }
  },
  "language": "javascript"
}
{
  "_id": "_design/types",
  "_rev": "3-7cf6577027645d9983192764450f039e",
  "views": {
    "DataFeed": {
      "reduce": "_sum",
      "map": "function (doc) {\n  if (doc.message.type == 'DataFeed') {\n    emit(doc.message.title, doc.message.url);\n  }\n}"
    }
  },
  "language": "javascript"
}
