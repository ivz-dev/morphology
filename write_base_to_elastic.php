<?php
$from = $argv[1];
$to = $argv[2];

require 'vendor/autoload.php';
$client = Elasticsearch\ClientBuilder::create()->build();

// подключение к бд.
$dsn = 'mysql:host=localhost; dbname=youtube_key';
$user = 'root';
$password = '123';

try{
    $db = new PDO($dsn, $user, $password);
}
catch (PDOException $e){
    echo $e->getMessage();
}

$select = $db->prepare("SELECT * FROM `allkeys` WHERE id>:from and id<:to");
$select->bindParam(":from", $from);
$select->bindParam(":to", $to);
$select->execute();

$result = $select->fetchAll(PDO::FETCH_ASSOC);


foreach ($result as $value){

    $key = $value['key'];

    $params = [
        'index' => 'full_key',
        'type' => 'key',
        'body' => [
            'key' => $key
        ]
    ];

    $response = $client->index($params);
}


