<?php
// подключение библиотек

require 'vendor/autoload.php';
$client = Elasticsearch\ClientBuilder::create()->build();
$qw = "dell inspiron memori test";

$params = [
    'index' => 'you_cat_new',
    'type' => 'key',
    'from' => 0,
    'size' => 30,
    'body' => [
        'query' => [
            'match' => [
                'token' => $qw
            ]
        ]
    ]
];

$response = $client->search($params);

foreach ($response['hits']['hits'] as $key=>$value){
    echo $key." : ".$value['_source']['category']."<br>";
}