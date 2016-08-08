<?php
// подключение библиотек

require 'vendor/autoload.php';
$client = Elasticsearch\ClientBuilder::create()->build();
$qw = 'set up';

$params = [
    'index' => 'you_key_new',
    'type' => 'key',
    'from' => 0,
    'size' => 30,
    'body' => [
        'query' => [
            'match' => [
                'key' => $qw
            ]
        ]
    ]
];

$response = $client->search($params);

foreach ($response['hits']['hits'] as $key=>$value){
    echo $key." : ".$value['_source']['token']."<br>";
}