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
$select = $dbh->prepare("SELECT * FROM `allkeys` WHERE id=:id"); //выборка ключей
$count_qw = $dbh->prepare("SELECT * FROM `trigram` WHERE trigram=:trigram_val"); //выборка для проверки наличия би-грамы
$trigram_insert = $dbh->prepare("INSERT INTO `trigram` (trigram, key_id, count) VALUES (:trigram, :key_id, :count)");
$trigram_incr = $dbh->prepare("UPDATE `trigram` SET `count` = `count`+1 WHERE `trigram`=:trigram");
$trigram_concat = $dbh->prepare("UPDATE `trigram` SET `key_id`=CONCAT(`key_id`,:id) WHERE `trigram`=:trigram");

for ($i=$from; $i<$to; $i++){

    $select->bindParam(':id', $id);
    $id = $i;
    $select->execute();
    $key=$select->fetch(PDO::FETCH_ASSOC);

    $word_arr = explode(" ", analyze($key['key']));
    $trigram_arr = getNgram($word_arr, 3);

    foreach ($trigram_arr as $value){
        $count_qw->bindParam(":trigram_val", $value);
        $count_qw->execute();
        $count = $count_qw->fetchAll(PDO::FETCH_ASSOC);

        if(count($count)==0){
            $trigram_insert->bindParam(":trigram", $value);
            $trigram_insert->bindParam(":key_id", $id);
            $val_cnt = 1;
            $trigram_insert->bindParam(":count", $val_cnt);
            $trigram_insert->execute();
        }
        else{
            $trigram_incr->bindParam(":trigram", $value);
            $trigram_incr->execute();

            $id_str = ", ".$id;
            $trigram_concat->bindParam(":id", $id_str);
            $trigram_concat->bindParam(":trigram", $value);
            $trigram_concat->execute();
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

