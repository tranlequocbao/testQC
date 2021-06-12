<?php

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

if(!isset($_SESSION)) session_start();
$sl = $_SESSION['logined'];

if(!isset($sl['allowAdmin']) || $sl['allowAdmin'] != '1'){
    return response([
        'code' => 400,
        'message' => 'Not permision allow'
    ]);
}