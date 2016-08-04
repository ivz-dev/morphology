<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

// подключение библиотек
require 'vendor/autoload.php';
$client = Elasticsearch\ClientBuilder::create()->build();

$id_pattern = '/[0-9]{5}/';

$f = fopen("category.csv", "r");

// Читать построчно до конца файла
while(!feof($f)) {
    $string = fgets($f);
    preg_match($id_pattern, $string, $id);
    $cat_array = explode(",", $string)[1];
    echo $cat_array."<br>";

            $params = [
                'index' => 'category_index_full',
                'type' => 'category',
                'body' => [
                    'category' => $cat_array,
                    'g_id' => $id[0],
                ]
            ];


        $response = $client->index($params);
    }


fclose($f);

echo "Done!";