<?php

    if(!isset($_SESSION)) session_start();
    if(!isset($_SESSION['logined'])){
        echo json_encode([
            'code' => 401,
            'data' => 'Please login!'
        ]);
        die;
    }
    if(!isset($allowGet) || !!$allowGet){
        if(!isset($_POST)){
            echo json_encode([
                'code' => 402,
                'data' => 'Method Post only!'
            ]);
            die;
        }
    }
    function response($aryData){
        echo json_encode($aryData);
        return true;
    }
    date_default_timezone_set("Asia/Ho_Chi_Minh");
    require_once "../vendor/PDOconnect/pdo/PdoConnect.php";
    $db = new pdoRequest();