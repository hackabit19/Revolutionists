<?php
use Elasticsearch\Clientbuilder;
require vendor/autoload.php;

$dotenv = new Dotenv\Dotenv(__DIR__);
    $dotenv->load();
$elasticsearch_host = getenv('ELASTICSEARCH_HOST');


// connecting to elasticsearch_host
$hosts = [
      [
        'host' => $elasticsearch_host
      ]
    ];

    $client = ClientBuilder::create()->setHosts($hosts)->build();


