<?php
    require_once "./_header.php";
    $code = $_POST['code'];
    $mess = '';

    $db->setTable('checking');
    if(!$db->update(['recoat_flag' => 1], ['error_code', $code])){
        echo json_encode([
            'code' => 204,
            'message' => 'Update checking table error!'
        ]);
        return true;
    }
    //set del error sealer

//    $db->setTable('sealer_checking');
//    if(!$db->update(['recoat_flag' => 1], ['error_code', $code])){
//        echo json_encode([
//            'code' => 204,
//            'message' => 'Update sealer checking table error!'
//        ]);
//        return true;
//    }
    $aryUpdate = [
        'user_recoat' => $_POST['usercode'] ?? $_SESSION['logined']['usercode']
    ];
    $db->setTable('car_sealered');
    if(!$db->update($aryUpdate,['vin_code', $code])){
        echo json_encode([
            'code' => 204,
            'message' => 'Update car_sealered table error!'
        ]);
        return true;
    }
    $db->setTable('polish_car');
    if(!$db->update($aryUpdate,['vin_code', $code])){
        echo json_encode([
            'code' => 204,
            'message' => 'Update polish_car table error!'
        ]);
        return true;
    }
    echo json_encode([
        'code' => 200,
        'message' => 'Success'
    ]);
    return true;