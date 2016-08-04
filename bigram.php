<?php
// получение данных
$from = $argv[1];
$to = $argv[2];

// подключение к бд
$dsn = 'mysql:host=localhost; dbname=youtube_key';
$user = 'root';
$password = '123';

try{
    $dbh = new PDO($dsn, $user, $password);
}
catch (PDOException $e){
    echo $e->getMessage();
}
$select = $dbh->prepare("SELECT * FROM `allkeys` WHERE id>=:to and id<:from"); //выборка ключей
$count_qw = $dbh->prepare("SELECT * FROM `bigram` WHERE bigram=:bigram_val"); //выборка для проверки наличия би-грамы
$bigram_insert = $dbh->prepare("INSERT INTO `bigram` (bigram, key_id, count) VALUES (:bigram, :key_id, :count)");
$bigram_incr = $dbh->prepare("UPDATE `bigram` SET `count` = `count`+1 WHERE `id`=:current_id");
$bigram_concat = $dbh->prepare("UPDATE `bigram` SET `key_id`=CONCAT(`key_id`,:id) WHERE `id`=:current_id");

$select->bindParam(':to', $to);
$select->bindParam(':from', $from);
$select->execute();
$key_array=$select->fetchAll(PDO::FETCH_ASSOC);


foreach($key_array as $key){

    $id = $key['id'];
    $word_arr = explode(" ", analyze($key['key']));
    $bigram_arr = getNgram($word_arr, 2);

    foreach ($bigram_arr as $value){
        $count_qw->bindParam(":bigram_val", $value);
        $count_qw->execute();
        $count = $count_qw->fetchAll(PDO::FETCH_ASSOC);
        $current_id = $count['id'];

        if(count($count)==0){
            $bigram_insert->bindParam(":bigram", $value);
            $bigram_insert->bindParam(":key_id", $id);
            $val_cnt = 1;
            $bigram_insert->bindParam(":count", $val_cnt);
            $bigram_insert->execute();
        }
        else{

            $bigram_incr->bindParam(":current_id", $value);
            $bigram_incr->execute();

            $id_str = ", ".$id;
            $bigram_concat->bindParam(":id", $id_str);
            $bigram_concat->bindParam(":current_id", $current_id);
            $bigram_concat->execute();
        }
    }
}
//функция для получения н-грам
function getNgram($word, $n){
    $ngram_arr = [];
    if (count($word)>1){
        if($n==2){
            for ($i=0; $i<count($word)-1; $i++){
                $ngram_arr [] = $word[$i]." ".$word[$i+1];
            }
        }
        else if($n==3){
            for ($i=0; $i<count($word)-2; $i++){
                $ngram_arr [] = $word[$i]." ".$word[$i+1]." ".$word[$i+2];
            }
        }
    }
    return ($ngram_arr);
}
// функция для токенизации строки
function analyze($text){
    $url = "http://localhost:9200/_analyze?analysis&filter&analyzer=english&text=";
    $a = file_get_contents($url.urlencode($text));
    $b = (array)json_decode($a);
    $token_arr='';

    foreach ($b['tokens'] as $value){
        $token_item = (array)$value;
        $token_arr.=$token_item['token']." ";
    }
    return trim($token_arr);
}

