<?php
// подключение библиотек

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
// получение даных с бд

$select = $db->prepare("SELECT * FROM `unic_word` WHERE `count`>=40 ORDER by `count` DESC");
$select->execute();
$result = $select->fetchAll(PDO::FETCH_ASSOC);

$insert = $db->prepare("UPDATE `unic_word` SET `keys`=:key WHERE `id`=:id");


foreach ($result as $item){

    $qw = $item['word'];
    $id = $item['id'];

    $params = [
        'index' => 'you_key_new',
        'type' => 'key',
        'from' => 0,
        'size' => 25,
        'body' => [
            'query' => [
                'match' => [
                    'key' => $qw
                ]
            ]
        ]
    ];

    $response = $client->search($params);
    $keys ='';
    foreach ($response['hits']['hits'] as $key=>$value){
        $keys .= trim($value['_source']['token']).",";
    }

    $insert->bindParam(":key", $keys);
    $insert->bindParam(":id", $id);
    $insert->execute();
}


