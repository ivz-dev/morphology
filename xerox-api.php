<?php

//connect to database
$dsn = 'mysql:host=localhost; dbname=youtube_key';
$user = 'root';
$password = '123';

try{
    $db = new PDO($dsn, $user, $password);
} catch(PDOException $e){
    echo $e->getMessage();
}

$insert = $db->prepare("SELECT `key` from `allkeys` WHERE id=:id");

$arr = array(349337,316778,606611);


foreach ($arr as $key){
    $insert->bindParam(":id", $key);
    $insert->execute();
    $res = $insert->fetchAll(PDO::FETCH_ASSOC);

    $key = $res[0]['key'];

    $text = urlencode($key);
    $url = "https://services.open.xerox.com/bus/op/fst-nlp-tools/PartOfSpeechTagging?inputtext=$text&language=English";
    $result = file_get_contents($url);

    $js = json_decode($result);

    $arr = json_decode(json_encode($js), True);
    $x = $arr['PartOfSpeechTaggingResponse']['PartOfSpeechTaggingResult']['file'];

    $item = $x['sentence']['lexeme-list']['lexeme'];

    echo "<b> $key </b><br>";
    foreach ($item as $val){

        $str = $val['sense-list']['sense'];
        $name = $str['base-form'];
        $part = $str['part-of-speech']['#text'];

        if ($part=='+NOUN' || $part=='+VI' ||$part=='+ADV'||$part=='+ADJ'){
            $part = trim($part, "+");
            echo trim($part, "+")." : ".$name."<br>";
        }

    }

}






