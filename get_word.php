<?php

use Elasticsearch\ClientBuilder;
require 'vendor/autoload.php';
$client = ClientBuilder::create()->build();
$qw = 'download';
$params = [
    'index' => 'you_key_new',
    'type' => 'key',
    'from' => 0,
    'size' => 45,
    'body' => [
        'query' => [
            'match' => [
                'key' => $qw
            ]
        ]
    ]
];

$response = $client->search($params);
echo "<pre>";
print_r($response);
echo "</pre>";
