<?php

function getNeeded(){
    return [
        'Capo',
        'MuiRH',
        'MuiLH',
        'Ngoaicop',
        'GomaLH',
        'CuatruocLH',
        'CuasauLH',
        'ManghongLH',
        'GomaRH',
        'CuatruocRH',
        'CuasauRH',
        'ManghongRH'
    ];
}

function _require(array $arrs, $db){
    $errs   = require_once __DIR__ . '/error.php';
    $needed = getNeeded();

    $result = $res = [];
    $count  = 1;
    foreach ($arrs as $arr) {
        if(!isset($result[$arr['error_code']])){
            $result[$arr['error_code']] = [];
        }
        array_push($result[$arr['error_code']], $arr);
    }
    foreach ($result as $item => $value){
        $db->setTable('car_sealered');
        $getInfo = $db->one(['vin_code', $item]) ?? [];
        $model = $getInfo['folder'] ?? '';
        $vin_code = $item;
        $color = $getInfo['color'] ?? '';
        $tempErr = [];
        $data_temp_get = [];
        foreach ($value as $val) {
            if(!isset($tempErr[$val['error_type']][$val['error_position']])){
                $tempErr[$val['error_type']][$val['error_position']] = 1;
            }else{
                $tempErr[$val['error_type']][$val['error_position']] = (int) $tempErr[$val['error_type']][$val['error_position']] + 1;
            }
            $data_temp_get['id'] = $val['id'];
            $data_temp_get['created_at'] = $val['created_at'];
        }
        foreach ($tempErr as $t => $_i) {
            $_temp                      = [];
            $_temp['no']                = $count;
            $_temp['model']             = $model;
            $_temp['vin_code']          = $vin_code;
            $_temp['color']             = $color;
            $_temp['category_defect']   = $errs[$t] ?? 'Er';
            $_temp['id']                = $data_temp_get['id'] ?? '';
            $_temp['created_at']        = $data_temp_get['created_at'] ?? '';
            $sum                        = 0;
            foreach ($_i as $s => $v) {
                $newStr = substr($s,2);
                if(in_array($newStr,$needed)){
                    $_temp[$newStr] = $v;
                    $sum += $v;
                }
            }
            $_temp['sum'] = $sum;
            if($sum != 0){
                $count++;
                array_push($res, $_temp);
            }
        }
    }
    return $res;
}