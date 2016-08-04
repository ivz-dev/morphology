<?php

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
$st = $db->query('SELECT max(id) FROM allkeys');
$result = $st->fetch(PDO::FETCH_ASSOC);

$max_count = $result['max(id)']; // количество записей в таблице
$count_req=100000; //количество записей, для обработки в одном запросе

//расчет количества потоков
$count = ceil($max_count/$count_req);
echo $count;

//запуск скриптов
for($i = 0; $i<=$count; $i++){
    if($i*$count_req<$max_count){

        $from = $i*$count_req;

        if ($from+$count_req<$max_count) {
            $to = $from+$count_req;
        }
        else{
            $to = $max_count;
        }
        echo $i;
        exec("php /var/www/es/html/trigram.php $from $to &");

    }

}