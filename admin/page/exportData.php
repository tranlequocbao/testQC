<?php

require_once __DIR__ . '/headAjax.php';

$db->setTable('checking');

$params = $_POST['options'] ?? [];

$startTime = $params['start'] ?? '';
$endTime = $params['end'] ?? '';

$tempStartTime = '';
$tempEndTime = '';

if($startTime == '' && $endTime == ''){
    $datas = $db->all();
}else if($startTime == ''){
    $time = strtotime($endTime);
    $newformat = date('Y-m-d',$time);
    $datas = $db->get(['created_at', '<=', $newformat]);
    $tempEndTime = explode('/', $endTime);
    $tempEndTime = implode('-', [$tempEndTime[1], $tempEndTime[0], $tempEndTime[2]]);
}else if($endTime == ''){
    $time = strtotime($startTime);
    $newformat = date('Y-m-d',$time);
    $datas = $db->get(['created_at', '>=', $newformat]);
    $tempStartTime = explode('/', $startTime);
    $tempStartTime = implode('-', [$tempStartTime[1], $tempStartTime[0], $tempStartTime[2]]);
}else{
    $timeS = strtotime($startTime);
    $timeE = strtotime('+1 days', strtotime($endTime));
    $timeS = date('Y-m-d',$timeS);
    $timeE = date('Y-m-d',$timeE);
    $datas = $db->get(['created_at', 'BETWEEN', [$timeS,$timeE]]);
    $tempStartTime = explode('/', $startTime);
    $tempStartTime = implode('-', [$tempStartTime[1], $tempStartTime[0], $tempStartTime[2]]);
    $tempEndTime = explode('/', $endTime);
    $tempEndTime = implode('-', [$tempEndTime[1], $tempEndTime[0], $tempEndTime[2]]);
}

$datas = _require($datas, $db);

require_once __DIR__ . '/../../vendor/PHPExcel/Classes/PHPExcel.php';
require_once __DIR__ . '/../../vendor/PHPExcel/Classes/PHPExcel/IOFactory.php';

$excel = PHPExcel_IOFactory::createReader('Excel5');
$excel = $excel->load(__DIR__ . '/../../files/excel/baocao.xls');
$excel->setActiveSheetIndex(0);
$activeSheet = $excel->getActiveSheet();

$row = 5;
$date = date('d-m-Y');
if($tempStartTime != '' || $tempEndTime != ''){
    $date = $tempStartTime . '_' . $tempEndTime;
}
$name = 'ADMIN_' . $date . '_' . round(microtime(true) * 1000) . '.xlsx';
$path_file = 'files/adminExport/' . $name;

foreach ($datas as $item => $data){

    $roofLH = $data['MuiLH'] ?? 0;
    $roofRH = $data['MuiRH'] ?? 0;
    $roof = $roofLH + $roofRH;
    $roof = $roof == 0 ? '' :  $roof;

    $activeSheet->setCellValue('A' . $row, $data['no']);
    $activeSheet->setCellValue('B' . $row, $data['model']);
    $activeSheet->setCellValue('C' . $row, $data['vin_code']);
    $activeSheet->setCellValue('D' . $row, $data['color']);
    $activeSheet->setCellValue('E' . $row, $data['category_defect']);
    $activeSheet->setCellValue('F' . $row, $data['Capo'] ?? '');
    $activeSheet->setCellValue('G' . $row, $roof);
    $activeSheet->setCellValue('H' . $row, $data['Ngoaicop'] ?? '');
    $activeSheet->setCellValue('I' . $row, $data['GomaLH'] ?? '');
    $activeSheet->setCellValue('J' . $row, $data['CuatruocLH'] ?? '');
    $activeSheet->setCellValue('K' . $row, $data['CuasauLH'] ?? '');
    $activeSheet->setCellValue('L' . $row, $data['ManghongLH'] ?? '');
    $activeSheet->setCellValue('M' . $row, $data['GomaRH'] ?? '');
    $activeSheet->setCellValue('N' . $row, $data['CuatruocRH'] ?? '');
    $activeSheet->setCellValue('O' . $row, $data['CuasauRH'] ?? '');
    $activeSheet->setCellValue('P' . $row, $data['ManghongRH'] ?? '');
    $activeSheet->setCellValue('R' . $row, $data['sum'] ?? 0);

    //set border
    $activeSheet->getStyle('A' . $row . ':R' . $row)->applyFromArray([
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