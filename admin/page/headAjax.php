<?php

if(!isset($_POST)){
    echo json_encode([
        'code' => 402,
        'data' => 'Method Post only!'
    ]);
    return true;
}

function response(array $arr){
    echo json_encode($arr);
    return true;
}

function formatValue($data){
    $result = [];
    foreach ($data as $val){
        $result[$val['name']] = $val['value'];
    }
    return $result;
}

date_default_timezone_set("Asia/Ho_Chi_Minh");
require_once __DIR__ . "/../../vendor/PDOconnect/pdo/PdoConnect.php";
require_once __DIR__ . '/../function.php';
$db = new pdoRequest();