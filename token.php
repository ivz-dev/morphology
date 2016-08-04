<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

$url = "http://localhost:9200/_analyze?analysis&filter&analyzer=english&text=";
$text = "Software test engeneer";

$a = file_get_contents($url.urlencode($text));
$b = (array)json_decode($a);
$token_arr='';

foreach ($b['tokens'] as $value){
    $token_arr='';
    $token_item = (array)$value;
    $token_arr.=$token_item['token']." ";
}

print_r($token_arr);