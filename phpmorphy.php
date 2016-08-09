<?php
// подключение к бд.
$dsn = 'mysql:host=localhost; dbname=youtube_key';
$user = 'root';
$password = '123';


$from = $argv[1];
$to = $argv[2];

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

$insert = $db->prepare("INSERT INTO `clear_key` (key) VALUES (:key)");


foreach ($result as $item){
    $key = delete_duplicates_words($item['key']);
    $insert->bindParam(":key", $key);
    $insert->execute();
}

function delete_duplicates_words($text)
{
    $text = implode(array_reverse(preg_split('//u', $text)));
    $text = preg_replace('/(\b[\pL0-9]++\b)(?=.*?\1)/siu', '', $text);
    $text = implode(array_reverse(preg_split('//u', $text)));
    return $text;
}

