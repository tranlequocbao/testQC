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

$type = $_POST['type'];
$code = $_POST['code'];

if($type == 'getlist'){
    if(isset($_SESSION['type_export']) && $_SESSION['type_export'] == 'SEALER'){
        $db->setTable('sealer_checking');
    }else{
        $db->setTable('checking');
    }
    $result = $db->likeEnd(['error_code', $code]);
    if(!$result){
        $db->setTable('plan_vin');
        $result_re = $db->likeEnd(['vin_code', $code]);
        if(!$result_re){
            echo json_encode([
                'code' => 201,
                'message' => 'SQL ERROR!'
            ]);
            return true;
        }
        echo json_encode([
            'code' => 200,
            'data' => [$result_re[0]['vin_code']]
        ]);
        return true;
    }
    $result = array_reduce($result, function ($a,$b){
        return array_merge($a, [$b['error_code']]);
    },[]);
    echo json_encode([
        'code' => 200,
        'data' => array_unique($result)
    ]);
    return true;
}

if($type == 'loadall'){
    //load folder
    $db->setTable('car_sealered');
    $code_min = $_POST['code_min'];
    $code = $_POST['code'];
    $folder = $db->one(['vin_code', $code])['folder'] ?? null;
    if(!$folder){
        echo json_encode([
            'code' => 203,
            'message' => 'Not found car code!'
        ]);
        return true;
    }
    //load error
    if(isset($_SESSION['type_export']) && $_SESSION['type_export'] == 'SEALER'){
        $db->setTable('sealer_checking');
    }else{
        $db->setTable('checking');
    }
    $error = $db->get([['error_code', $code], ['recoat_flag','0']]);
    if(!$error){
        $error = [];
    }
    //load img
    function recursiveSearch($folder, $pattern) {
        $dir = new RecursiveDirectoryIterator($folder);
        $ite = new RecursiveIteratorIterator($dir);
        $files = new RegexIterator($ite, $pattern, RegexIterator::GET_MATCH);
        $fileList = array();
        foreach($files as $file) {
            $fileList[] = array(
                'image' => str_replace('\\','/' ,$file[0])
            );
        }
        return $fileList;
    }

    $img_rh = $img_lh = $img_sealer = [];

    $img_sealer = recursiveSearch("../assets/images/" . $folder . '/SEALER',"/^.*\.(jpg|jpeg|png)$/");
    $img_rh = recursiveSearch("../assets/images/" . $folder . '/RH',"/^.*\.(jpg|jpeg|png)$/");
    $img_lh = recursiveSearch("../assets/images/" . $folder . '/LH',"/^.*\.(jpg|jpeg|png)$/");
//    if(isset($_SESSION['type_export']) && $_SESSION['type_export'] == 'SEALER'){
//
//    }else{
//
//    }

    echo json_encode([
        'code'          => 200,
        'error'         => $error,
        'image_lh'      => $img_lh,
        'image_rh'      => $img_rh,
        'image_sealer'  => $img_sealer

    ]);
    return true;
}



