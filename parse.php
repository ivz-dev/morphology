<?php
// подключение библиотек
require 'vendor/autoload.php';
$client = Elasticsearch\ClientBuilder::create()->build();
$url = "http://localhost:9200/_analyze?analysis&filter&analyzer=english&text=";


// получение данных
$from = $argv[1];
$to = $argv[2];

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
$params = array('from'=>$from, 'to'=>$to);

$st = $db->prepare('SELECT * FROM allkeys WHERE id>=:from AND id<:to');
$st->execute($params);
$result = $st->fetchAll(PDO::FETCH_ASSOC);

//создание индекса 
foreach($result as $key_item){
    $string = $key_item['key'];
    $token_arr='';

    $a = file_get_contents($url.urlencode($string));
    $b = (array)json_decode($a);

    foreach ($b['tokens'] as $value){
        $token_item = (array)$value;
        $token_arr.=$token_item['token']." ";
    }

	$params = [
	    'index' => 'you_key_new',
	    'type' => 'key',
	    'id' => $key_item['id'],
	    'body' => [
	        'key' => $string,
            'token' => $token_arr
        ]
	];

	$response = $client->index($params);

}

echo "$to done!";