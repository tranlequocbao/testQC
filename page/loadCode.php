<?php

if(!isset($_SESSION)) session_start();
if(!isset($_SESSION['logined'])){
    echo json_encode([
        'code' => 401,
        'message' => 'Please login!'
    ]);
    return true;
}
require_once "../vendor/PDOconnect/pdo/PdoConnect.php";
$db = new pdoRequest();
$db->setTable('car_sealered');

$code = $_POST['code'];

$result = $db->likeEnd(['vin_code', strtoupper($code)]);
if($result == null){
    echo json_encode([
        'code' => 205,
        'message' => null
    ]);
    return true;
}
if(!$result){
    echo json_encode([
        'code' => 202,
        'message' => 'Get db error!'
    ]);
    return true;
}
echo json_encode([
    'code' => 200,
    'message' => $result[0]['vin_code']
]);
return true;