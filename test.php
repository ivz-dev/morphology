<?php
//подключение библиотек
require 'vendor/autoload.php';
$client = Elasticsearch\ClientBuilder::create()->build();
$url = "http://localhost:9200/_analyze?analysis&filter&analyzer=english&text=";

$id_pattern = '/[0-9]{5}/';

$f = fopen("category.csv", "r");

// Читать построчно до конца файла
while(!feof($f)) {

    $string = fgets($f);
    preg_match($id_pattern, $string, $id);
    $category_array = explode("/", explode(",", $string)[1]);
    $category [] = trim(trim(end($category_array)), '"');
}

fclose($f);

$category = array_unique($category);

foreach ($category as $val){
    $string = $val;
    $token_arr='';

    $a = file_get_contents($url.urlencode($string));
    $b = (array)json_decode($a);

    foreach ($b['tokens'] as $value){
        $token_item = (array)$value;
        $token_arr.=$token_item['token']." ";
    }

    $params = [
        'index' => 'you_cat_new',
        'type' => 'key',
        'body' => [
            'category' => $string,
            'token' => $token_arr
        ]
    ];

    $response = $client->index($params);
}


