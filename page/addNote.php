<?php
    require_once "./_header.php";

    $car_code = $_POST['car_code'] ?? '';
    $note_car = $_POST['note_car'] ?? '';

    if($car_code == ''){
        echo json_encode([
            'code' => 205,
            'message' => 'Car code or Note car empty!'
        ]);
        return true;
    }

    $db->setTable('note_car');

    $getNote = $db->one(['vin_code', $car_code]);

    if(isset($_POST['loadNote']) && $_POST['loadNote'] == '1'){
        echo json_encode([
            'code' => 200,
            'note' => $getNote['note'] ?? ''
        ]);
        return true;
    }

    if(!empty($getNote)){
        if(!$db->update(['note' => $note_car],['vin_code', $car_code])){
            echo json_encode([
                'code' => 203,
                'message' => 'Update note error!'
            ]);
            return true;
        }
        echo json_encode([
            'code' => 200,
            'message' => 'Update note success!'
        ]);
        return true;
    }

    $insert = [
        'vin_code' => $car_code,
        'username_add' => $_SESSION['logined']['username'],
        'note' => $note_car
    ];

    if(!$db->insert($insert)){
        echo json_encode([
            'code' => 203,
            'message' => 'Insert note error!'
        ]);
        return true;
    }

    echo json_encode([
        'code' => 200,
        'message' => 'Insert note success!'
    ]);
    return true;
