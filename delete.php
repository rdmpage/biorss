<?php

// Delete records from CouchDB to start afresh

require_once (dirname(__FILE__) . '/config.inc.php');
require_once (dirname(__FILE__) . '/couchsimple.php');

// Get list of documents
// http://127.0.0.1:5984/biorss/_design/housekeeping/_view/id

$json = '{
    "total_rows": 146,
    "offset": 0,
    "rows": [
        {
            "id": "https://journals.plos.org/plosone/article?id=10.1371/journal.pone.0258454",
            "key": null,
            "value": "https://journals.plos.org/plosone/article?id=10.1371/journal.pone.0258454"
        },
        {
            "id": "https://link.springer.com/article/10.1007/s11230-021-10009-1",
            "key": null,
            "value": "https://link.springer.com/article/10.1007/s11230-021-10009-1"
        },
        {
            "id": "https://phytokeys.pensoft.net/article/56874/",
            "key": null,
            "value": "https://phytokeys.pensoft.net/article/56874/"
        },
        {
            "id": "https://phytokeys.pensoft.net/article/57681/",
            "key": null,
            "value": "https://phytokeys.pensoft.net/article/57681/"
        },
        {
            "id": "https://phytokeys.pensoft.net/article/57725/",
            "key": null,
            "value": "https://phytokeys.pensoft.net/article/57725/"
        },
        {
            "id": "https://phytokeys.pensoft.net/article/57759/",
            "key": null,
            "value": "https://phytokeys.pensoft.net/article/57759/"
        },
        {
            "id": "https://phytokeys.pensoft.net/article/58825/",
            "key": null,
            "value": "https://phytokeys.pensoft.net/article/58825/"
        },
        {
            "id": "https://phytokeys.pensoft.net/article/59849/",
            "key": null,
            "value": "https://phytokeys.pensoft.net/article/59849/"
        },
        {
            "id": "https://phytokeys.pensoft.net/article/60137/",
            "key": null,
            "value": "https://phytokeys.pensoft.net/article/60137/"
        },
        {
            "id": "https://phytokeys.pensoft.net/article/60337/",
            "key": null,
            "value": "https://phytokeys.pensoft.net/article/60337/"
        },
        {
            "id": "https://phytokeys.pensoft.net/article/60592/",
            "key": null,
            "value": "https://phytokeys.pensoft.net/article/60592/"
        },
        {
            "id": "https://phytokeys.pensoft.net/article/61031/",
            "key": null,
            "value": "https://phytokeys.pensoft.net/article/61031/"
        },
        {
            "id": "https://phytokeys.pensoft.net/article/61044/",
            "key": null,
            "value": "https://phytokeys.pensoft.net/article/61044/"
        },
        {
            "id": "https://phytokeys.pensoft.net/article/61054/",
            "key": null,
            "value": "https://phytokeys.pensoft.net/article/61054/"
        },
        {
            "id": "https://phytokeys.pensoft.net/article/61467/",
            "key": null,
            "value": "https://phytokeys.pensoft.net/article/61467/"
        },
        {
            "id": "https://phytokeys.pensoft.net/article/61630/",
            "key": null,
            "value": "https://phytokeys.pensoft.net/article/61630/"
        },
        {
            "id": "https://phytokeys.pensoft.net/article/61996/",
            "key": null,
            "value": "https://phytokeys.pensoft.net/article/61996/"
        },
        {
            "id": "https://phytokeys.pensoft.net/article/62023/",
            "key": null,
            "value": "https://phytokeys.pensoft.net/article/62023/"
        },
        {
            "id": "https://phytokeys.pensoft.net/article/62037/",
            "key": null,
            "value": "https://phytokeys.pensoft.net/article/62037/"
        },
        {
            "id": "https://phytokeys.pensoft.net/article/62050/",
            "key": null,
            "value": "https://phytokeys.pensoft.net/article/62050/"
        },
        {
            "id": "https://phytokeys.pensoft.net/article/62059/",
            "key": null,
            "value": "https://phytokeys.pensoft.net/article/62059/"
        },
        {
            "id": "https://phytokeys.pensoft.net/article/62514/",
            "key": null,
            "value": "https://phytokeys.pensoft.net/article/62514/"
        },
        {
            "id": "https://phytokeys.pensoft.net/article/62552/",
            "key": null,
            "value": "https://phytokeys.pensoft.net/article/62552/"
        },
        {
            "id": "https://phytokeys.pensoft.net/article/62671/",
            "key": null,
            "value": "https://phytokeys.pensoft.net/article/62671/"
        },
        {
            "id": "https://phytokeys.pensoft.net/article/62741/",
            "key": null,
            "value": "https://phytokeys.pensoft.net/article/62741/"
        },
        {
            "id": "https://phytokeys.pensoft.net/article/62774/",
            "key": null,
            "value": "https://phytokeys.pensoft.net/article/62774/"
        },
        {
            "id": "https://phytokeys.pensoft.net/article/62802/",
            "key": null,
            "value": "https://phytokeys.pensoft.net/article/62802/"
        },
        {
            "id": "https://phytokeys.pensoft.net/article/62915/",
            "key": null,
            "value": "https://phytokeys.pensoft.net/article/62915/"
        },
        {
            "id": "https://phytokeys.pensoft.net/article/62922/",
            "key": null,
            "value": "https://phytokeys.pensoft.net/article/62922/"
        },
        {
            "id": "https://phytokeys.pensoft.net/article/62947/",
            "key": null,
            "value": "https://phytokeys.pensoft.net/article/62947/"
        },
        {
            "id": "https://phytokeys.pensoft.net/article/62953/",
            "key": null,
            "value": "https://phytokeys.pensoft.net/article/62953/"
        },
        {
            "id": "https://phytokeys.pensoft.net/article/63116/",
            "key": null,
            "value": "https://phytokeys.pensoft.net/article/63116/"
        },
        {
            "id": "https://phytokeys.pensoft.net/article/63346/",
            "key": null,
            "value": "https://phytokeys.pensoft.net/article/63346/"
        },
        {
            "id": "https://phytokeys.pensoft.net/article/63378/",
            "key": null,
            "value": "https://phytokeys.pensoft.net/article/63378/"
        },
        {
            "id": "https://phytokeys.pensoft.net/article/63383/",
            "key": null,
            "value": "https://phytokeys.pensoft.net/article/63383/"
        },
        {
            "id": "https://phytokeys.pensoft.net/article/63401/",
            "key": null,
            "value": "https://phytokeys.pensoft.net/article/63401/"
        },
        {
            "id": "https://phytokeys.pensoft.net/article/63619/",
            "key": null,
            "value": "https://phytokeys.pensoft.net/article/63619/"
        },
        {
            "id": "https://phytokeys.pensoft.net/article/63878/",
            "key": null,
            "value": "https://phytokeys.pensoft.net/article/63878/"
        },
        {
            "id": "https://phytokeys.pensoft.net/article/63994/",
            "key": null,
            "value": "https://phytokeys.pensoft.net/article/63994/"
        },
        {
            "id": "https://phytokeys.pensoft.net/article/64042/",
            "key": null,
            "value": "https://phytokeys.pensoft.net/article/64042/"
        },
        {
            "id": "https://phytokeys.pensoft.net/article/64245/",
            "key": null,
            "value": "https://phytokeys.pensoft.net/article/64245/"
        },
        {
            "id": "https://phytokeys.pensoft.net/article/64426/",
            "key": null,
            "value": "https://phytokeys.pensoft.net/article/64426/"
        },
        {
            "id": "https://phytokeys.pensoft.net/article/64465/",
            "key": null,
            "value": "https://phytokeys.pensoft.net/article/64465/"
        },
        {
            "id": "https://phytokeys.pensoft.net/article/64485/",
            "key": null,
            "value": "https://phytokeys.pensoft.net/article/64485/"
        },
        {
            "id": "https://phytokeys.pensoft.net/article/64564/",
            "key": null,
            "value": "https://phytokeys.pensoft.net/article/64564/"
        },
        {
            "id": "https://phytokeys.pensoft.net/article/64609/",
            "key": null,
            "value": "https://phytokeys.pensoft.net/article/64609/"
        },
        {
            "id": "https://phytokeys.pensoft.net/article/64750/",
            "key": null,
            "value": "https://phytokeys.pensoft.net/article/64750/"
        },
        {
            "id": "https://phytokeys.pensoft.net/article/64764/",
            "key": null,
            "value": "https://phytokeys.pensoft.net/article/64764/"
        },
        {
            "id": "https://phytokeys.pensoft.net/article/65087/",
            "key": null,
            "value": "https://phytokeys.pensoft.net/article/65087/"
        },
        {
            "id": "https://phytokeys.pensoft.net/article/65326/",
            "key": null,
            "value": "https://phytokeys.pensoft.net/article/65326/"
        },
        {
            "id": "https://phytokeys.pensoft.net/article/65433/",
            "key": null,
            "value": "https://phytokeys.pensoft.net/article/65433/"
        },
        {
            "id": "https://phytokeys.pensoft.net/article/65443/",
            "key": null,
            "value": "https://phytokeys.pensoft.net/article/65443/"
        },
        {
            "id": "https://phytokeys.pensoft.net/article/65519/",
            "key": null,
            "value": "https://phytokeys.pensoft.net/article/65519/"
        },
        {
            "id": "https://phytokeys.pensoft.net/article/65812/",
            "key": null,
            "value": "https://phytokeys.pensoft.net/article/65812/"
        },
        {
            "id": "https://phytokeys.pensoft.net/article/65813/",
            "key": null,
            "value": "https://phytokeys.pensoft.net/article/65813/"
        },
        {
            "id": "https://phytokeys.pensoft.net/article/65836/",
            "key": null,
            "value": "https://phytokeys.pensoft.net/article/65836/"
        },
        {
            "id": "https://phytokeys.pensoft.net/article/65990/",
            "key": null,
            "value": "https://phytokeys.pensoft.net/article/65990/"
        },
        {
            "id": "https://phytokeys.pensoft.net/article/66018/",
            "key": null,
            "value": "https://phytokeys.pensoft.net/article/66018/"
        },
        {
            "id": "https://phytokeys.pensoft.net/article/66312/",
            "key": null,
            "value": "https://phytokeys.pensoft.net/article/66312/"
        },
        {
            "id": "https://phytokeys.pensoft.net/article/66462/",
            "key": null,
            "value": "https://phytokeys.pensoft.net/article/66462/"
        },
        {
            "id": "https://phytokeys.pensoft.net/article/66748/",
            "key": null,
            "value": "https://phytokeys.pensoft.net/article/66748/"
        },
        {
            "id": "https://phytokeys.pensoft.net/article/67009/",
            "key": null,
            "value": "https://phytokeys.pensoft.net/article/67009/"
        },
        {
            "id": "https://phytokeys.pensoft.net/article/67126/",
            "key": null,
            "value": "https://phytokeys.pensoft.net/article/67126/"
        },
        {
            "id": "https://phytokeys.pensoft.net/article/67289/",
            "key": null,
            "value": "https://phytokeys.pensoft.net/article/67289/"
        },
        {
            "id": "https://phytokeys.pensoft.net/article/67436/",
            "key": null,
            "value": "https://phytokeys.pensoft.net/article/67436/"
        },
        {
            "id": "https://phytokeys.pensoft.net/article/67622/",
            "key": null,
            "value": "https://phytokeys.pensoft.net/article/67622/"
        },
        {
            "id": "https://phytokeys.pensoft.net/article/67624/",
            "key": null,
            "value": "https://phytokeys.pensoft.net/article/67624/"
        },
        {
            "id": "https://phytokeys.pensoft.net/article/67634/",
            "key": null,
            "value": "https://phytokeys.pensoft.net/article/67634/"
        },
        {
            "id": "https://phytokeys.pensoft.net/article/68300/",
            "key": null,
            "value": "https://phytokeys.pensoft.net/article/68300/"
        },
        {
            "id": "https://phytokeys.pensoft.net/article/68323/",
            "key": null,
            "value": "https://phytokeys.pensoft.net/article/68323/"
        },
        {
            "id": "https://phytokeys.pensoft.net/article/68635/",
            "key": null,
            "value": "https://phytokeys.pensoft.net/article/68635/"
        },
        {
            "id": "https://phytokeys.pensoft.net/article/68782/",
            "key": null,
            "value": "https://phytokeys.pensoft.net/article/68782/"
        },
        {
            "id": "https://phytokeys.pensoft.net/article/69016/",
            "key": null,
            "value": "https://phytokeys.pensoft.net/article/69016/"
        },
        {
            "id": "https://phytokeys.pensoft.net/article/69037/",
            "key": null,
            "value": "https://phytokeys.pensoft.net/article/69037/"
        },
        {
            "id": "https://phytokeys.pensoft.net/article/69045/",
            "key": null,
            "value": "https://phytokeys.pensoft.net/article/69045/"
        },
        {
            "id": "https://phytokeys.pensoft.net/article/69074/",
            "key": null,
            "value": "https://phytokeys.pensoft.net/article/69074/"
        },
        {
            "id": "https://phytokeys.pensoft.net/article/69180/",
            "key": null,
            "value": "https://phytokeys.pensoft.net/article/69180/"
        },
        {
            "id": "https://phytokeys.pensoft.net/article/69194/",
            "key": null,
            "value": "https://phytokeys.pensoft.net/article/69194/"
        },
        {
            "id": "https://phytokeys.pensoft.net/article/69546/",
            "key": null,
            "value": "https://phytokeys.pensoft.net/article/69546/"
        },
        {
            "id": "https://phytokeys.pensoft.net/article/69667/",
            "key": null,
            "value": "https://phytokeys.pensoft.net/article/69667/"
        },
        {
            "id": "https://phytokeys.pensoft.net/article/69740/",
            "key": null,
            "value": "https://phytokeys.pensoft.net/article/69740/"
        },
        {
            "id": "https://phytokeys.pensoft.net/article/69852/",
            "key": null,
            "value": "https://phytokeys.pensoft.net/article/69852/"
        },
        {
            "id": "https://phytokeys.pensoft.net/article/70045/",
            "key": null,
            "value": "https://phytokeys.pensoft.net/article/70045/"
        },
        {
            "id": "https://phytokeys.pensoft.net/article/70099/",
            "key": null,
            "value": "https://phytokeys.pensoft.net/article/70099/"
        },
        {
            "id": "https://phytokeys.pensoft.net/article/70119/",
            "key": null,
            "value": "https://phytokeys.pensoft.net/article/70119/"
        },
        {
            "id": "https://phytokeys.pensoft.net/article/70285/",
            "key": null,
            "value": "https://phytokeys.pensoft.net/article/70285/"
        },
        {
            "id": "https://phytokeys.pensoft.net/article/70685/",
            "key": null,
            "value": "https://phytokeys.pensoft.net/article/70685/"
        },
        {
            "id": "https://phytokeys.pensoft.net/article/70745/",
            "key": null,
            "value": "https://phytokeys.pensoft.net/article/70745/"
        },
        {
            "id": "https://phytokeys.pensoft.net/article/70844/",
            "key": null,
            "value": "https://phytokeys.pensoft.net/article/70844/"
        },
        {
            "id": "https://phytokeys.pensoft.net/article/70949/",
            "key": null,
            "value": "https://phytokeys.pensoft.net/article/70949/"
        },
        {
            "id": "https://phytokeys.pensoft.net/article/71045/",
            "key": null,
            "value": "https://phytokeys.pensoft.net/article/71045/"
        },
        {
            "id": "https://phytokeys.pensoft.net/article/71049/",
            "key": null,
            "value": "https://phytokeys.pensoft.net/article/71049/"
        },
        {
            "id": "https://phytokeys.pensoft.net/article/71063/",
            "key": null,
            "value": "https://phytokeys.pensoft.net/article/71063/"
        },
        {
            "id": "https://phytokeys.pensoft.net/article/71167/",
            "key": null,
            "value": "https://phytokeys.pensoft.net/article/71167/"
        },
        {
            "id": "https://phytokeys.pensoft.net/article/71259/",
            "key": null,
            "value": "https://phytokeys.pensoft.net/article/71259/"
        },
        {
            "id": "https://phytokeys.pensoft.net/article/71505/",
            "key": null,
            "value": "https://phytokeys.pensoft.net/article/71505/"
        },
        {
            "id": "https://phytokeys.pensoft.net/article/71522/",
            "key": null,
            "value": "https://phytokeys.pensoft.net/article/71522/"
        },
        {
            "id": "https://phytokeys.pensoft.net/article/71642/",
            "key": null,
            "value": "https://phytokeys.pensoft.net/article/71642/"
        },
        {
            "id": "https://phytokeys.pensoft.net/article/72170/",
            "key": null,
            "value": "https://phytokeys.pensoft.net/article/72170/"
        },
        {
            "id": "https://phytokeys.pensoft.net/article/72285/",
            "key": null,
            "value": "https://phytokeys.pensoft.net/article/72285/"
        },
        {
            "id": "https://phytokeys.pensoft.net/article/73210/",
            "key": null,
            "value": "https://phytokeys.pensoft.net/article/73210/"
        },
        {
            "id": "https://phytokeys.pensoft.net/article/73421/",
            "key": null,
            "value": "https://phytokeys.pensoft.net/article/73421/"
        },
        {
            "id": "https://pubmed.ncbi.nlm.nih.gov/34732763/?utm_source=Rested&utm_medium=rss&utm_campaign=pubmed-2&utm_content=1rE397IRBYU0-ogsyRnEw9o91K808u0evolcHK9IDZ0PVH5cqD&fc=20211108074834&ff=20211108074851&v=2.15.0",
            "key": null,
            "value": "https://pubmed.ncbi.nlm.nih.gov/34732763/?utm_source=Rested&utm_medium=rss&utm_campaign=pubmed-2&utm_content=1rE397IRBYU0-ogsyRnEw9o91K808u0evolcHK9IDZ0PVH5cqD&fc=20211108074834&ff=20211108074851&v=2.15.0"
        },
        {
            "id": "https://pubmed.ncbi.nlm.nih.gov/34733243/?utm_source=Rested&utm_medium=rss&utm_campaign=pubmed-2&utm_content=1rE397IRBYU0-ogsyRnEw9o91K808u0evolcHK9IDZ0PVH5cqD&fc=20211108074834&ff=20211108074851&v=2.15.0",
            "key": null,
            "value": "https://pubmed.ncbi.nlm.nih.gov/34733243/?utm_source=Rested&utm_medium=rss&utm_campaign=pubmed-2&utm_content=1rE397IRBYU0-ogsyRnEw9o91K808u0evolcHK9IDZ0PVH5cqD&fc=20211108074834&ff=20211108074851&v=2.15.0"
        },
        {
            "id": "https://pubmed.ncbi.nlm.nih.gov/34733399/?utm_source=Rested&utm_medium=rss&utm_campaign=pubmed-2&utm_content=1rE397IRBYU0-ogsyRnEw9o91K808u0evolcHK9IDZ0PVH5cqD&fc=20211108074834&ff=20211108074851&v=2.15.0",
            "key": null,
            "value": "https://pubmed.ncbi.nlm.nih.gov/34733399/?utm_source=Rested&utm_medium=rss&utm_campaign=pubmed-2&utm_content=1rE397IRBYU0-ogsyRnEw9o91K808u0evolcHK9IDZ0PVH5cqD&fc=20211108074834&ff=20211108074851&v=2.15.0"
        },
        {
            "id": "https://pubmed.ncbi.nlm.nih.gov/34733400/?utm_source=Rested&utm_medium=rss&utm_campaign=pubmed-2&utm_content=1rE397IRBYU0-ogsyRnEw9o91K808u0evolcHK9IDZ0PVH5cqD&fc=20211108074834&ff=20211108074851&v=2.15.0",
            "key": null,
            "value": "https://pubmed.ncbi.nlm.nih.gov/34733400/?utm_source=Rested&utm_medium=rss&utm_campaign=pubmed-2&utm_content=1rE397IRBYU0-ogsyRnEw9o91K808u0evolcHK9IDZ0PVH5cqD&fc=20211108074834&ff=20211108074851&v=2.15.0"
        },
        {
            "id": "https://pubmed.ncbi.nlm.nih.gov/34733585/?utm_source=Rested&utm_medium=rss&utm_campaign=pubmed-2&utm_content=1rE397IRBYU0-ogsyRnEw9o91K808u0evolcHK9IDZ0PVH5cqD&fc=20211108074834&ff=20211108074851&v=2.15.0",
            "key": null,
            "value": "https://pubmed.ncbi.nlm.nih.gov/34733585/?utm_source=Rested&utm_medium=rss&utm_campaign=pubmed-2&utm_content=1rE397IRBYU0-ogsyRnEw9o91K808u0evolcHK9IDZ0PVH5cqD&fc=20211108074834&ff=20211108074851&v=2.15.0"
        },
        {
            "id": "https://pubmed.ncbi.nlm.nih.gov/34737072/?utm_source=Rested&utm_medium=rss&utm_campaign=pubmed-2&utm_content=1rE397IRBYU0-ogsyRnEw9o91K808u0evolcHK9IDZ0PVH5cqD&fc=20211108074834&ff=20211108074851&v=2.15.0",
            "key": null,
            "value": "https://pubmed.ncbi.nlm.nih.gov/34737072/?utm_source=Rested&utm_medium=rss&utm_campaign=pubmed-2&utm_content=1rE397IRBYU0-ogsyRnEw9o91K808u0evolcHK9IDZ0PVH5cqD&fc=20211108074834&ff=20211108074851&v=2.15.0"
        },
        {
            "id": "https://pubmed.ncbi.nlm.nih.gov/34737075/?utm_source=Rested&utm_medium=rss&utm_campaign=pubmed-2&utm_content=1rE397IRBYU0-ogsyRnEw9o91K808u0evolcHK9IDZ0PVH5cqD&fc=20211108074834&ff=20211108074851&v=2.15.0",
            "key": null,
            "value": "https://pubmed.ncbi.nlm.nih.gov/34737075/?utm_source=Rested&utm_medium=rss&utm_campaign=pubmed-2&utm_content=1rE397IRBYU0-ogsyRnEw9o91K808u0evolcHK9IDZ0PVH5cqD&fc=20211108074834&ff=20211108074851&v=2.15.0"
        },
        {
            "id": "https://pubmed.ncbi.nlm.nih.gov/34738732/?utm_source=Rested&utm_medium=rss&utm_campaign=pubmed-2&utm_content=1rE397IRBYU0-ogsyRnEw9o91K808u0evolcHK9IDZ0PVH5cqD&fc=20211108074834&ff=20211108074851&v=2.15.0",
            "key": null,
            "value": "https://pubmed.ncbi.nlm.nih.gov/34738732/?utm_source=Rested&utm_medium=rss&utm_campaign=pubmed-2&utm_content=1rE397IRBYU0-ogsyRnEw9o91K808u0evolcHK9IDZ0PVH5cqD&fc=20211108074834&ff=20211108074851&v=2.15.0"
        },
        {
            "id": "https://pubmed.ncbi.nlm.nih.gov/34738903/?utm_source=Rested&utm_medium=rss&utm_campaign=pubmed-2&utm_content=1rE397IRBYU0-ogsyRnEw9o91K808u0evolcHK9IDZ0PVH5cqD&fc=20211108074834&ff=20211108074851&v=2.15.0",
            "key": null,
            "value": "https://pubmed.ncbi.nlm.nih.gov/34738903/?utm_source=Rested&utm_medium=rss&utm_campaign=pubmed-2&utm_content=1rE397IRBYU0-ogsyRnEw9o91K808u0evolcHK9IDZ0PVH5cqD&fc=20211108074834&ff=20211108074851&v=2.15.0"
        },
        {
            "id": "https://pubmed.ncbi.nlm.nih.gov/34739363/?utm_source=Rested&utm_medium=rss&utm_campaign=pubmed-2&utm_content=1rE397IRBYU0-ogsyRnEw9o91K808u0evolcHK9IDZ0PVH5cqD&fc=20211108074834&ff=20211108074851&v=2.15.0",
            "key": null,
            "value": "https://pubmed.ncbi.nlm.nih.gov/34739363/?utm_source=Rested&utm_medium=rss&utm_campaign=pubmed-2&utm_content=1rE397IRBYU0-ogsyRnEw9o91K808u0evolcHK9IDZ0PVH5cqD&fc=20211108074834&ff=20211108074851&v=2.15.0"
        },
        {
            "id": "https://pubmed.ncbi.nlm.nih.gov/34739364/?utm_source=Rested&utm_medium=rss&utm_campaign=pubmed-2&utm_content=1rE397IRBYU0-ogsyRnEw9o91K808u0evolcHK9IDZ0PVH5cqD&fc=20211108074834&ff=20211108074851&v=2.15.0",
            "key": null,
            "value": "https://pubmed.ncbi.nlm.nih.gov/34739364/?utm_source=Rested&utm_medium=rss&utm_campaign=pubmed-2&utm_content=1rE397IRBYU0-ogsyRnEw9o91K808u0evolcHK9IDZ0PVH5cqD&fc=20211108074834&ff=20211108074851&v=2.15.0"
        },
        {
            "id": "https://pubmed.ncbi.nlm.nih.gov/34739365/?utm_source=Rested&utm_medium=rss&utm_campaign=pubmed-2&utm_content=1rE397IRBYU0-ogsyRnEw9o91K808u0evolcHK9IDZ0PVH5cqD&fc=20211108074834&ff=20211108074851&v=2.15.0",
            "key": null,
            "value": "https://pubmed.ncbi.nlm.nih.gov/34739365/?utm_source=Rested&utm_medium=rss&utm_campaign=pubmed-2&utm_content=1rE397IRBYU0-ogsyRnEw9o91K808u0evolcHK9IDZ0PVH5cqD&fc=20211108074834&ff=20211108074851&v=2.15.0"
        },
        {
            "id": "https://pubmed.ncbi.nlm.nih.gov/34739366/?utm_source=Rested&utm_medium=rss&utm_campaign=pubmed-2&utm_content=1rE397IRBYU0-ogsyRnEw9o91K808u0evolcHK9IDZ0PVH5cqD&fc=20211108074834&ff=20211108074851&v=2.15.0",
            "key": null,
            "value": "https://pubmed.ncbi.nlm.nih.gov/34739366/?utm_source=Rested&utm_medium=rss&utm_campaign=pubmed-2&utm_content=1rE397IRBYU0-ogsyRnEw9o91K808u0evolcHK9IDZ0PVH5cqD&fc=20211108074834&ff=20211108074851&v=2.15.0"
        },
        {
            "id": "https://pubmed.ncbi.nlm.nih.gov/34739370/?utm_source=Rested&utm_medium=rss&utm_campaign=pubmed-2&utm_content=1rE397IRBYU0-ogsyRnEw9o91K808u0evolcHK9IDZ0PVH5cqD&fc=20211108074834&ff=20211108074851&v=2.15.0",
            "key": null,
            "value": "https://pubmed.ncbi.nlm.nih.gov/34739370/?utm_source=Rested&utm_medium=rss&utm_campaign=pubmed-2&utm_content=1rE397IRBYU0-ogsyRnEw9o91K808u0evolcHK9IDZ0PVH5cqD&fc=20211108074834&ff=20211108074851&v=2.15.0"
        },
        {
            "id": "https://pubmed.ncbi.nlm.nih.gov/34739692/?utm_source=Rested&utm_medium=rss&utm_campaign=pubmed-2&utm_content=1rE397IRBYU0-ogsyRnEw9o91K808u0evolcHK9IDZ0PVH5cqD&fc=20211108074834&ff=20211108074851&v=2.15.0",
            "key": null,
            "value": "https://pubmed.ncbi.nlm.nih.gov/34739692/?utm_source=Rested&utm_medium=rss&utm_campaign=pubmed-2&utm_content=1rE397IRBYU0-ogsyRnEw9o91K808u0evolcHK9IDZ0PVH5cqD&fc=20211108074834&ff=20211108074851&v=2.15.0"
        },
        {
            "id": "https://scholar.google.com/scholar?cluster=11928194027814941781",
            "key": null,
            "value": "https://scholar.google.com/scholar?cluster=11928194027814941781"
        },
        {
            "id": "https://scholar.google.com/scholar?cluster=13225604711119307064",
            "key": null,
            "value": "https://scholar.google.com/scholar?cluster=13225604711119307064"
        },
        {
            "id": "https://scholar.google.com/scholar?cluster=1363572735628374224",
            "key": null,
            "value": "https://scholar.google.com/scholar?cluster=1363572735628374224"
        },
        {
            "id": "https://scholar.google.com/scholar?cluster=14000657745695815347",
            "key": null,
            "value": "https://scholar.google.com/scholar?cluster=14000657745695815347"
        },
        {
            "id": "https://scholar.google.com/scholar?cluster=16744467821797299176",
            "key": null,
            "value": "https://scholar.google.com/scholar?cluster=16744467821797299176"
        },
        {
            "id": "https://scholar.google.com/scholar?cluster=17909855075247027964",
            "key": null,
            "value": "https://scholar.google.com/scholar?cluster=17909855075247027964"
        },
        {
            "id": "https://scholar.google.com/scholar?cluster=18014335097563085259",
            "key": null,
            "value": "https://scholar.google.com/scholar?cluster=18014335097563085259"
        },
        {
            "id": "https://scholar.google.com/scholar?cluster=5531427322025884984",
            "key": null,
            "value": "https://scholar.google.com/scholar?cluster=5531427322025884984"
        },
        {
            "id": "https://scholar.google.com/scholar?cluster=6229237295102482543",
            "key": null,
            "value": "https://scholar.google.com/scholar?cluster=6229237295102482543"
        },
        {
            "id": "https://scholar.google.com/scholar?cluster=8937124637945268496",
            "key": null,
            "value": "https://scholar.google.com/scholar?cluster=8937124637945268496"
        },
        {
            "id": "https://www.biotaxa.org/Zootaxa/article/view/zootaxa.5060.1.1",
            "key": null,
            "value": "https://www.biotaxa.org/Zootaxa/article/view/zootaxa.5060.1.1"
        },
        {
            "id": "https://www.ingentaconnect.com/content/aspt/sb/pre-prints/content-2100006",
            "key": null,
            "value": "https://www.ingentaconnect.com/content/aspt/sb/pre-prints/content-2100006"
        },
        {
            "id": "https://www.ingentaconnect.com/content/nhn/pimj/2021/00000046/00000001/art00001",
            "key": null,
            "value": "https://www.ingentaconnect.com/content/nhn/pimj/2021/00000046/00000001/art00001"
        },
        {
            "id": "https://www.ingentaconnect.com/content/nhn/pimj/2021/00000046/00000001/art00002",
            "key": null,
            "value": "https://www.ingentaconnect.com/content/nhn/pimj/2021/00000046/00000001/art00002"
        },
        {
            "id": "https://www.ingentaconnect.com/content/nhn/pimj/2021/00000046/00000001/art00003",
            "key": null,
            "value": "https://www.ingentaconnect.com/content/nhn/pimj/2021/00000046/00000001/art00003"
        },
        {
            "id": "https://www.ingentaconnect.com/content/nhn/pimj/2021/00000046/00000001/art00004",
            "key": null,
            "value": "https://www.ingentaconnect.com/content/nhn/pimj/2021/00000046/00000001/art00004"
        },
        {
            "id": "https://www.ingentaconnect.com/content/nhn/pimj/2021/00000046/00000001/art00005",
            "key": null,
            "value": "https://www.ingentaconnect.com/content/nhn/pimj/2021/00000046/00000001/art00005"
        },
        {
            "id": "https://www.ingentaconnect.com/content/nhn/pimj/2021/00000046/00000001/art00006",
            "key": null,
            "value": "https://www.ingentaconnect.com/content/nhn/pimj/2021/00000046/00000001/art00006"
        },
        {
            "id": "https://www.ingentaconnect.com/content/nhn/pimj/2021/00000046/00000001/art00007",
            "key": null,
            "value": "https://www.ingentaconnect.com/content/nhn/pimj/2021/00000046/00000001/art00007"
        },
        {
            "id": "https://www.ingentaconnect.com/content/nhn/pimj/2021/00000046/00000001/art00008",
            "key": null,
            "value": "https://www.ingentaconnect.com/content/nhn/pimj/2021/00000046/00000001/art00008"
        },
        {
            "id": "https://www.ingentaconnect.com/content/nhn/pimj/2021/00000046/00000001/art00009",
            "key": null,
            "value": "https://www.ingentaconnect.com/content/nhn/pimj/2021/00000046/00000001/art00009"
        },
        {
            "id": "https://www.ingentaconnect.com/content/nhn/pimj/2021/00000046/00000001/art00010",
            "key": null,
            "value": "https://www.ingentaconnect.com/content/nhn/pimj/2021/00000046/00000001/art00010"
        },
        {
            "id": "https://www.ingentaconnect.com/content/nhn/pimj/2021/00000046/00000001/art00011",
            "key": null,
            "value": "https://www.ingentaconnect.com/content/nhn/pimj/2021/00000046/00000001/art00011"
        },
        {
            "id": "https://www.mapress.com/zt/article/view/zootaxa.5060.2.2",
            "key": null,
            "value": "https://www.mapress.com/zt/article/view/zootaxa.5060.2.2"
        },
        {
            "id": "https://www.mdpi.com/2309-608X/7/11/919/pdf",
            "key": null,
            "value": "https://www.mdpi.com/2309-608X/7/11/919/pdf"
        },
        {
            "id": "https://www.researchgate.net/profile/Thorsten-Lumbsch/publication/355469004_Contributions_to_the_phylogeny_of_Lepraria_Stereocaulaceae_species_from_the_Southern_Hemisphere_including_three_new_species/links/617556520be8ec17a9225f67/Contributions-to-the-phylogeny-of-Lepraria-Stereocaulaceae-species-from-the-Southern-Hemisphere-including-three-new-species.pdf",
            "key": null,
            "value": "https://www.researchgate.net/profile/Thorsten-Lumbsch/publication/355469004_Contributions_to_the_phylogeny_of_Lepraria_Stereocaulaceae_species_from_the_Southern_Hemisphere_including_three_new_species/links/617556520be8ec17a9225f67/Contributions-to-the-phylogeny-of-Lepraria-Stereocaulaceae-species-from-the-Southern-Hemisphere-including-three-new-species.pdf"
        },
        {
            "id": "https://www.sciencedirect.com/science/article/pii/S0967063721001953",
            "key": null,
            "value": "https://www.sciencedirect.com/science/article/pii/S0967063721001953"
        },
        {
            "id": "https://www.tandfonline.com/doi/abs/10.1080/14772000.2021.1980449",
            "key": null,
            "value": "https://www.tandfonline.com/doi/abs/10.1080/14772000.2021.1980449"
        },
        {
            "id": "https://zookeys.pensoft.net/article/72596/download/pdf/",
            "key": null,
            "value": "https://zookeys.pensoft.net/article/72596/download/pdf/"
        }
    ]
}';

$obj = json_decode($json);

foreach ($obj->rows as $row)
{
	//echo $row->value . "\n";		
	$couch->add_update_or_delete_document(null, $row->value, 'delete');
}

?>