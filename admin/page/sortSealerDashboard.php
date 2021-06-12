<?php

require_once __DIR__ . '/headAjax.php';

if(!isset($params)){
    $params = $_POST['options'];
}

$loadAll = (isset($params['all']) && $params['all'] == '1') ? true : false;
$startTime = $params['start'] ?? '';
$endTime = $params['end'] ?? '';
$p = $params['p'] ?? '1';
$p = (int)$p;

if(!$loadAll && ($startTime == '' || $endTime == '')){
    echo json_encode([
        'code' => 200,
        'html' => '<h2 class="text-center">Not see time start or time end!</h2>',
        'content' => '<h2 class="text-center">Not see time start or time end!</h2>',
        'pagging' => ''
    ]);
    return true;
}

$timeS = strtotime($startTime);
$timeE = strtotime($endTime);
$timeS = date('Y-m-d',$timeS);
$timeE = date('Y-m-d',$timeE);

$arySelect = [
    'car_sealered.vin_code as vin_code',
    'car_sealered.folder as folder',
    'car_sealered.color as color',
    'COUNT(checking.error_code) as sum_error'
];

$queryWhereDate = '';
if(!$loadAll){
    $queryWhereDate = ' WHERE DATE(checking.created_at) BETWEEN "'. $timeS .' 00:00:00" AND "'. $timeE . ' 23:59:59"';
}
$sql = 'SELECT '.implode(', ', $arySelect).' FROM car_sealered LEFT JOIN checking ON checking.error_code = car_sealered.vin_code ' . $queryWhereDate . ' GROUP BY car_sealered.vin_code';

$allRecord = $db->_exec($sql);

if($allRecord == null){
    $datas = [
        'datas' => [],
        'countPage' => 1,
        'pageNow' => 1,
        'allRecord' => 0,
        'all' => $params['all'] ?? '0',
        'startTime' => $startTime ?? '',
        'endTime' => $endTime ?? ''
    ];
}else{
    $sqlHasLimit = $sql . ' LIMIT ' . ($p * 10 - 10) . ', 10';
    $query = $db->_exec($sqlHasLimit);
    $db->setTable('checking');
    foreach ($query as $key => $item){
        $tempSql = 'SELECT err_level as level FROM checking WHERE error_code = "' . $item['vin_code'] . '" AND recoat_flag = 0';
        $tempSql .= ' ORDER BY err_level ASC LIMIT 1';
        $tempQuery = $db->_exec($tempSql);
        if(empty($tempQuery)){
            $query[$key]['btn'] = 'btn-danger';
            $query[$key]['status'] = '';
            $query[$key]['type_car'] = '<span class="badge badge-info">Not Recoat</span>';
            continue;
        }
        $getRecoat = $db->one([['error_code', $item['vin_code']], ['recoat_flag', '1']]);
        $query[$key]['btn'] = 'btn-danger';
        $query[$key]['type_car'] = !empty($getRecoat) ? 'Recoat' : 'Not Recoat';
        $bgTypeCar = !empty($getRecoat) ? 'danger' : 'info';
        switch ($tempQuery[0]['level']){
            case '1' :
                $query[$key]['status'] = 'QC1K';
                break;
            case '2' :
                $query[$key]['status'] = 'Fixing';
                break;
            case '3' :
                $query[$key]['btn'] = 'btn-primary';
                $query[$key]['status'] = 'Done';
                break;
            default:
                $query[$key]['status'] = 'Level error';
                break;
        }

        $query[$key]['type_car'] = '<span class="badge badge-' . $bgTypeCar . '">' . $query[$key]['type_car'] . '</span>';
    }
    $datas = [
        'datas' => $query,
        'countPage' => ceil(count($allRecord) / 10),
        'pageNow' => $p,
        'allRecord' => count($allRecord),
        'startTime' => $startTime,
        'endTime' => $endTime,
        'all' => $params['all'] ?? '0'
    ];
}

ob_start();
require_once __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'detail.php';
$content = ob_get_contents();
ob_end_clean();

echo json_encode([
    'code' => 200,
    'html' => $content,
    'content' => $content,
    'pagging' => ''
]);
return true;
