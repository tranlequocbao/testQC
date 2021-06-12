<?php

if(!isset($_SESSION)) session_start();
if(!isset($_SESSION['logined'])){
    echo json_encode([
        'code' => 401,
        'message' => 'Please login!'
    ]);
    return true;
}

error_reporting(E_ALL);

$name_file_excel = 'test' . rand(1000,99999);

//require_once "../vendor/PDOconnect/pdo/PdoConnect.php";
require_once '../vendor/PHPExcel/Classes/PHPExcel.php';
require_once '../vendor/PHPExcel/Classes/PHPExcel/IOFactory.php';
//require_once './configFile.php';
//$db = new pdoRequest();

PHPExcel_Settings::setZipClass(PHPExcel_Settings::PCLZIP);
$tempExcel = PHPExcel_IOFactory::load('./temp_demo.xls');

$tempExcel->setActiveSheetIndex(1);

$tempExcel->getActiveSheet()
    ->setCellValue('V22', 'test edit 1')
    ->setCellValue('V23', 'test edit 2')
    ->setCellValue('V24', 'test edit 3')
    ->setCellValue('V25', 'test edit 4')
    ->setCellValue('V26', 'test edit 5')
    ->setCellValue('V27', 'test edit 6')
    ->setCellValue('V28', 'test edit 7');

$objExcel = PHPExcel_IOFactory::createWriter($tempExcel, 'Excel2007');
$objExcel->save('./' . $name_file_excel . '.xls');

die;
