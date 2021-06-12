<?php
    require_once "./_header.php";
    $db->setTable('car_sealered');

    $vin_code = $_POST['code'];

    $getVinAdded = $db->one(['vin_code', $vin_code]);
    if(!empty($getVinAdded)){
        if(deleteCar($db,$vin_code)){
            echo json_encode([
                'code' => '200',
                'type' => '0',
                'time' => $getVinAdded['updated_at'],
                'folder' => substr($vin_code,0,9)
            ]);
            return true;
        }
        echo json_encode([
            'code' => '205',
            'type' => '-1',
            'data' => 'delete car error'
        ]);
        return true;
    }

    $db->setTable('rfid');
    $color = $db->one(['vin_code', $vin_code])['color'];
    $db->setTable("car_code");
    $folder = $db->one(['car_code', substr($vin_code,0,9)])['car_folder'] ?? '';
    $db->setTable('car_sealered');
    $result = $db->insert([
        'vin_code' => $vin_code,
        'folder' => $folder,
        'color' => $color,
        'user_submit' => $_SESSION['logined']['fullname'] ?? $_SESSION['logined']['username'],
        'usercode_submit' => $_SESSION['logined']['usercode'] ?? '',
    ]);

    if(!$result){
        echo json_encode([
            'code' => '205',
            'type' => '-1',
            'data' => 'Insert error'
        ]);
        return true;
    }

    if(!deleteCar($db,$vin_code)){
        echo json_encode([
            'code' => '205',
            'type' => '-1',
            'data' => 'delete car error'
        ]);
        return true;
    }

    echo json_encode([
        'code' => '200',
        'type' => '1',
        'data' => 'Insert success',
        'folder' => $getVinAdded
    ]);
    return true;

    function deleteCar($db,$code){
        $db->setTable('rfid');
        $result = $db->update(['submited' => 1], ['vin_code', $code]);
        return $result;
    }