<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

use Elasticsearch\ClientBuilder;
require 'vendor/autoload.php';
$client = ClientBuilder::create()->build();

$params = [
    'index' => 'my__test_index_next',
];

$response = $client->indices()->create($params);
print_r($response);