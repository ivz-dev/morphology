<?php

use Elasticsearch\ClientBuilder;
require 'vendor/autoload.php';
$client = ClientBuilder::create()->build();

$params = [
    'index' => 'unic_word',
    'type' => 'key',
    'version'=>true,
    'from' => 95,
    'size' => 100,
];

$response = $client->search($params);
echo "<pre>";
print_r($response);
echo "</pre>";
