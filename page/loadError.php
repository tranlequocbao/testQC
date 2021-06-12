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

if(
    (isset($_SESSION['type_export']) && $_SESSION['type_export'] == 'SEALER')
    || $_POST['sealer'] == 'true'
){
    $db->setTable('sealer_checking');
}else{
    $db->setTable('checking');
}

$code = $_POST['code'] ?? '';

if($code == ''){
    echo json_encode([
        'code' => 205,
        'message' => 'Please request code!'
    ]);
    return true;
}

$get = [
    ['error_code',$code],
    ['recoat_flag','0']
];

if($_SESSION['logined']['position'] != 'ADMIN'){
    $errUser = $_POST['sealer'] == 'true' ? "SEALER" : $_SESSION['logined']['username'];
    array_push($get,['error_user', $errUser]);
}

$result = $db->get($get) ?? [];

$color = 'Not found';

if($_SESSION['logined']['position'] == 'SEALER'){
    $db->setTable('rfid');
}else{
    $db->setTable('car_sealered');
}

$ss = $db->one(['vin_code', $code]);

if(!is_null($ss)){
    $color = $ss['color'];
}

$db->setTable('sealer_checking');
$getSealerError = $db->get(['error_code',$code]);
$getSealerError = array_filter($getSealerError, function ($aa){
    return $aa['err_level'] != '3' || ($aa['err_note'] != null && $aa['err_note'] != '');
});

echo json_encode([
    'code' => 200,
    'message' => 'success',
    'data' => gettype($result),
    'color' => $color,
    'hasSealerError' => count($getSealerError)
]);
return true;