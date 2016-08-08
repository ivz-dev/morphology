<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

$url = "http://localhost:9200/_analyze?analysis&filter&analyzer=english&text=";
$text = "windows  xp to download";

$a = file_get_contents($url.urlencode($text));
$b = (array)json_decode($a);

foreach ($b['tokens'] as $val){
   print_r($val);
   echo "<br>";
}