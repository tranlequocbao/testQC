<?php

require_once __DIR__ . '/headAjax.php';

$params     = $_POST['options'];

$loadAll    = (isset($params['all']) && $params['all'] == '1') ? true : false;
$startTime  = $params['start'] ?? '';
$endTime    = $params['end'] ?? '';

if(!$loadAll && ($startTime == '' || $endTime == '')){
    echo json_encode([
        'code' => 209,
        'message' => 'Date sort error!'
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

$datas = $db->_exec($sql);

if($datas == null){
    echo json_encode([
        'code' => 303,
        'message' => 'Sort options error or database not have record!'
    ]);
    return true;
}

require_once __DIR__ . '/../../vendor/PHPExcel/Classes/PHPExcel.php';
require_once __DIR__ . '/../../vendor/PHPExcel/Classes/PHPExcel/IOFactory.php';

$excel = PHPExcel_IOFactory::createReader('Excel5');
$excel = $excel->load(__DIR__ . '/../../files/excel/exportDetail.xls');
$excel->setActiveSheetIndex(0);
$activeSheet = $excel->getActiveSheet();

$row = 3;
$date = date('d-m-Y');
if($timeS != '' || $timeE != ''){
    $date = $timeS . '_' . $timeE;
}
$name = 'ADMIN_DETAIL_' . $date . '_' . round(microtime(true) * 1000) . '.xlsx';
$path_file = 'files/adminExport/' . $name;

foreach ($datas as $item => $data){

    $activeSheet->setCellValue('B' . $row, ($item + 1));
    $activeSheet->setCellValue('C' . $row, $data['vin_code']);
    $activeSheet->setCellValue('D' . $row, $data['folder']);
    $activeSheet->setCellValue('E' . $row, $data['color']);
    $activeSheet->setCellValue('F' . $row, $data['sum_error']);

    if(count($datas) != ($item + 1)){
        //set style
        $activeSheet->duplicateStyle(
            $activeSheet->getStyle('B' . $row . ':F' . $row),
            'B' . ($row+1) . ':F' . ($row+1)
        );
    }

    //set border
    $activeSheet->getStyle('B' . $row . ':F' . $row)->applyFromArray([
        'borders' => [
            'allborders' => [
                'style' => PHPExcel_Style_Border::BORDER_THIN,
                'color' => ['rgb' => '000000']
            ]
        ]
    ]);

    //set auto height
    $activeSheet->getRowDimension($row)->setRowHeight(-1);
    $row++;
}

$objWrite = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
$objWrite->save(__DIR__ . '/../../' . $path_file);

echo json_encode([
    'code' => 200,
    'url' => $path_file
]);