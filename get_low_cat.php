<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

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

$insert = $dbh->prepare("INSERT INTO `category` (cat, cat_id) VALUES (:cat, :id)");


// 1. Счиатать строки с файла в массив
$id_pattern = '/[0-9]{5}/';

$f = fopen("category.csv", "r");
$m = fopen("category.csv", "r");
while(!feof($f)) {
    $string = fgets($f);
    $cat_string = explode(",", $string);
    $category = '';
    for($i=1; $i<count($cat_string); $i++){
        $category.=$cat_string[$i];
    }

    $category_array[] = trim(trim($category), '"');
    $category_array_id[] = trim(trim(explode(",", $string)[0]), '"');
}

fclose($f);

// 2. Если item_next[end-1] == item[end] делаем item_next -> item, если нет, то забираем item_next[end]
$low_cat_arr = [];
for($i=1; $i<count($category_array); $i++){

    $arr_prev = explode("/", $category_array[$i-1]);
    $arr_next = explode("/", $category_array[$i]);

    if($arr_next[count($arr_next)-2]==end($arr_prev)){
        continue;
    }
    else{
        $low_cat_arr[] = $category_array_id[$i-1].":".end($arr_prev);
    }
}

$low_cat_arr = array_unique($low_cat_arr);



foreach ($low_cat_arr as $key){
    $insert->bindParam(":cat", $cat);
    $insert->bindParam(":id", $id);
    $cat = explode(":", $key)[1];
    $id = explode(":", $key)[0];
    $insert->execute();
}



