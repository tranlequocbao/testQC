<?php

error_reporting(0);
ini_set('display_errors', FALSE);
ini_set('display_startup_errors', FALSE);


if(!isset($_SESSION)) session_start();
if(!isset($_SESSION['logined'])){
    echo json_encode([
        'code' => 401,
        'message' => 'Please login!'
    ]);
    return true;
}

function recursiveSearch($folder, $pattern) {
    $dir = new RecursiveDirectoryIterator($folder);
    $ite = new RecursiveIteratorIterator($dir);
    $files = new RegexIterator($ite, $pattern, RegexIterator::GET_MATCH);
    $fileList = array();
    foreach($files as $file) {
        if(!is_null($file[0])){
            $exp = explode('\\',$file[0]);
            $exp = end($exp);
            $exp = explode('.',$exp);
            array_pop($exp);
            $exp = implode('.', $exp);
            $fileList[] = $exp;
        }
    }
    return $fileList;
}

function getErrorPosition(array $arrs){
    $result = [];
    foreach ($arrs as $item => $arr){
        array_push($result, $arr['error_position']);
    }
    return array_unique($result);
}

function draw(&$excel, $fileName, $coordinates,$signaturePath,$path_file_excel,$heightSignature){
    $drawing = new PHPExcel_Worksheet_MemoryDrawing();
    $drawing->setName('Img of ' . $fileName)
        ->setDescription('Img of ' . $fileName)
        ->setImageResource(imagecreatefrompng($signaturePath . DIRECTORY_SEPARATOR . $fileName . '.png'))
        ->setRenderingFunction(PHPExcel_Worksheet_MemoryDrawing::RENDERING_PNG)
        ->setMimeType(PHPExcel_Worksheet_MemoryDrawing::MIMETYPE_DEFAULT)
        ->setHeight($heightSignature)
        ->setCoordinates($coordinates)
        ->setWorksheet($excel->getActiveSheet());
    $objWrite = PHPExcel_IOFactory::createWriter($excel,'Excel2007');
    $objWrite->save($path_file_excel);
}

function setValue(&$activeSheet, $location, $value, $color){
    $activeSheet->setCellValue($location,$value)->getStyle($location)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB($color);
}

require_once "../vendor/PDOconnect/pdo/PdoConnect.php";
require_once '../vendor/PHPExcel/Classes/PHPExcel.php';
require_once './configFile.php';
require_once './log.php';
$db = new pdoRequest();
$log = new Logger();

$beginTime = round(microtime(true) * 1000);
$log->write('Start time: ' . date('Y-m-d H:m:s'));

$images = $_POST['images'];
$vin_code = $_POST['vin_code'];
$vin_code_mini = $_POST['vin_code_mini'];

$username = $_SESSION['logined']['username'] ?? 'admin';

$path = [];

$db->setTable('car_code');

$folder = $db->one(['car_code',$vin_code_mini])['car_folder'];

$imgSealer = recursiveSearch("../assets/images/" . $folder . '/SEALER',"/^.*\.(jpg|jpeg|png)$/");
$imgLH = recursiveSearch("../assets/images/" . $folder . '/LH',"/^.*\.(jpg|jpeg|png)$/");
$imgRH = recursiveSearch("../assets/images/" . $folder . '/RH',"/^.*\.(jpg|jpeg|png)$/");

/*
$arr_rand = [] dùng để đựng những số đã rand trước đây. tránh trường hợp lặp lại gây ghi đè ảnh
*/

$arr_rand = [];

//default url signature
$signaturePath = realpath('../assets/images/Stamp');
$heightSignature = 68;

//uploadImg goto server
//nameFile place by Y-m-d-H-i-s so haven't duplicate.
$subSheet = 0;
$endSheet = 1;
$name_file_excel = $username . '_' . $vin_code . '_' . rand(1000,9999);
$path_file_excel = '../files/excel/' . $name_file_excel . '.xlsx';

$config          = [$configExcelSheet1, $configExcelSheet2];

//extract()
$excel = new PHPExcel();

$excel->getProperties()
    ->setCreator('CDD')
    ->setTitle('Export Thông tin xe: ' . $vin_code)
    ->setLastModifiedBy(date('d/m/Y'))
    ->setSubject('Export')
    ->setDescription('Export Thông tin xe: ' . $vin_code)
    ->setKeywords($vin_code)
    ->setCategory('QC1K');

foreach ($images as $is => $image) {
    $path = [];
    foreach ($image as $item => $valImg) {
        $img = str_replace('data:image/png;base64,', '', $valImg);
        $img = str_replace(' ', '+', $img);
        $data = base64_decode($img);

        $rand = 0;
        do{
            $rand = rand(1000,9999);
        }while(in_array($rand, $arr_rand));

        //$date = (new DateTime())->format('Y-m-d-H-i-s');
        $date = new DateTime();
        $date = $date->format('Y-m-d-H-i-s');
        $fileName = $username . '_' . implode("", explode('-', $date)). '_' . $rand;
        $img_path = '../files/images/' . $fileName . '.png';

        file_put_contents($img_path, $data);
        array_push($path, $img_path);
    }
    //export file excel

//    $excel->setActiveSheetIndex($subSheet);

    $tempExcel  = PHPExcel_IOFactory::load('../files/excel/temp_demo.xls');

    //load data of file temp
    //    $db->setTable('car_code');
    //    $car_type = $db->one(['car_code', $vin_code_mini]);
    //    if($car_type['type_export'] == 'old'){
    //        $tempExcel  = PHPExcel_IOFactory::load('../files/excel/temp.xls');
    //    }else{
    //        $tempExcel  = PHPExcel_IOFactory::load('../files/excel/temp2.xls');
    //    }

    $sheetNameFile = $tempExcel->getSheetNames();
    $sheet = $tempExcel->getSheetByName($sheetNameFile[$subSheet]);
    $excel->addExternalSheet($sheet);
//
//    foreach($tempExcel->getSheetNames() as $sheetName){
//        $sheet = $tempExcel->getSheetByName($sheetName);
////        $sheet->setTitle($sheetName);
//        $excel->addExternalSheet($sheet);
//        unset($sheet);
//    }

    $excel->setActiveSheetIndexByName('Worksheet');
    $sheetIndex = $excel->getActiveSheetIndex();
    $excel->removeSheetByIndex($sheetIndex);

    $excel->setActiveSheetIndex($subSheet);

    // include 'get_data_excel.php';

    //thông tin trên phiếu
    //get thông tin từ db

    $activeSheet = $excel->getActiveSheet();
    //allow config
    $cf = $config[$subSheet];
    extract($cf);
    //ghi thong tin ns submit len excel
    if($subSheet == 0){

        $db->setTable('history_err_sealer');
        $user_check = $db->one([['err_code', $vin_code], ['err_level', '2']])['err_user_fullname'] ?? '';
        $hasError = $db->one([['err_code', $vin_code], ['err_level', 'IN', ['1','2','3']]]) ?? false;
        $db->setTable('car_sealered');
        $user_create = $db->one(['vin_code', $vin_code])['user_submit'] ?? '';

        date_default_timezone_set("Asia/Ho_Chi_Minh");
        $dayCreate = date('d-m-Y');

        $db->setTable('car_code');
        $getInCarCode = $db->one(['car_code', $vin_code_mini]);
        $car_type = $getInCarCode['car_type'] ?? '';
        $car_folder = $getInCarCode['car_folder'] ?? '';

        $db->setTable('plan_vin');
        $color = $db->one(['vin_code', $vin_code])['color'] ?? '';

        $vinCode_v2     = $activeSheet->getCell($vincodeLocation)->getValue() . ' ' . $vin_code;
        $dayCreate      = $activeSheet->getCell($dayexportLocation)->getValue() . ' ' . $dayCreate;
        $car_type       = $activeSheet->getCell($cartypeLocation)->getValue() . ' ' . $car_type;
        $car_folder     = $activeSheet->getCell($carfolerLocation)->getValue() . ' ' . $car_folder;
        $color          = $activeSheet->getCell($colorLocation)->getValue() . ' ' . $color;

        $activeSheet->setCellValue($userCheckLocation,$user_check);
        $activeSheet->setCellValue($userCreateLocation,$user_create);
        $activeSheet->setCellValue($vincodeLocation,$vinCode_v2);
        $activeSheet->setCellValue($dayexportLocation,$dayCreate);
        $activeSheet->setCellValue($cartypeLocation,$car_type);
        $activeSheet->setCellValue($carfolerLocation,$car_folder);
        $activeSheet->setCellValue($colorLocation,$color);

        $db->setTable('car_sealered');
        $userSubmitSealer = $db->one(['vin_code',$vin_code]);

        if(count($userSubmitSealer) > 0 && !!$hasError){
            //draw img sheet SEALER
            draw($excel,$userSubmitSealer['usercode_submit'], $signatureSubmit1, $signaturePath, $path_file_excel,$heightSignature);
            draw($excel,$userSubmitSealer['usercode_submit'], $signatureSubmit2, $signaturePath, $path_file_excel,$heightSignature);
        }

        //check error
        $errorSealerConfig = $configFile['SEALER'];
        $db->setTable('sealer_checking');
        $checkingErrorSealer = $db->get(['error_code', $vin_code]) ?? [];
        $checkingErrorSealer = getErrorPosition($checkingErrorSealer);
        foreach ($errorSealerConfig as $item => $err){
            if(in_array($item, $checkingErrorSealer)){
                setValue($activeSheet,$err, 'X', 'c0392b');
            }else{
                setValue($activeSheet,$err, 'V', '27ae60');
            }
        }

    }else if($subSheet == 1){
        $db->setTable('checking');
        $aryErrIdGetDb = $db->get([['error_code', $vin_code], ['err_level', 'IN', ['2','3']]]);

        $aryErrId
            = $aryErrIdRh
            = $aryErrIdLh
            = [];
        foreach ($aryErrIdGetDb as $errId){
            if($errId['error_user'] == 'RH'){
                array_push($aryErrIdRh, $errId['err_id']);
            }else if($errId['error_user'] == 'LH'){
                array_push($aryErrIdLh, $errId['err_id']);
            }
            array_push($aryErrId, $errId['err_id']);
        }

        $db->setTable('history_err');
        $userPolishRh = $db->one([['err_id', 'IN', $aryErrIdRh], ['err_user_change', 'POLISH']])['err_user_fullname'] ?? '';
        $userPolishLh = $db->one([['err_id', 'IN', $aryErrIdLh], ['err_user_change', 'POLISH']])['err_user_fullname'] ?? '';
        $userRepairLh = $db->one([['err_id', 'IN', $aryErrIdLh], ['err_user_change', 'REPAIR']])['err_user_fullname'] ?? '';
        $userRepairRh = $db->one([['err_id', 'IN', $aryErrIdRh], ['err_user_change', 'REPAIR']])['err_user_fullname'] ?? '';
        $userRepairV2 = $db->one([['err_id', 'IN', $aryErrId], ['err_user_change', 'REPAIR_V2']])['err_user_fullname'] ?? '';

        $userPolish = implode(' - ', [$userPolishLh, $userPolishRh]);
        $userRepair = implode(' - ', [$userRepairLh, $userRepairRh]);

//        $userPolish = trim(implode(' - ', [$userPolishLh, $userPolishRh]), ' - ');
//        $userRepair = trim(implode(' - ', [$userRepairLh, $userRepairRh]), ' - ');
//        $userPolish = implode(' - ', [$userPolishLh, $userPolishRh]);
//        $userRepair = implode(' - ', [$userRepairLh, $userRepairRh]);

        $activeSheet->setCellValue($userPolishLocation,$userPolish);
        $activeSheet->setCellValue($userRepairLocation,$userRepair);
        $activeSheet->setCellValue($userRepairV2Location,$userRepairV2);

        $db->setTable('history_err');
        $userSubmitLH = $db->one([['err_code', $vin_code],['err_user_change', 'LH'], ['err_level', '1']]);
        $userSubmitRH = $db->one([['err_code', $vin_code],['err_user_change', 'RH'], ['err_level', '1']]);

        //draw img sheet QC1K
        //LH
        if(gettype($userSubmitLH) == 'array' && count($userSubmitLH) > 0){
            draw($excel,$userSubmitLH['err_user_code'], $signatureLH, $signaturePath, $path_file_excel,$heightSignature);
        }
        //RH
        if(gettype($userSubmitRH) == 'array' && count($userSubmitRH) > 0){
            draw($excel,$userSubmitRH['err_user_code'], $signatureRH, $signaturePath, $path_file_excel,$heightSignature);
        }

        //check error
        $errorLHConfig = $configFile['LH'];
        $errorRHConfig = $configFile['RH'];
        $localNow = '';
        $write = true;
        $db->setTable('checking');
        $checkingErrorQC1K = $db->get(['error_code', $vin_code]) ?? [];
        $checkingErrorQC1K = getErrorPosition($checkingErrorQC1K);
        foreach (array_merge($errorLHConfig, $errorRHConfig) as $item => $err){
            $val = 'V';
            $color = '27ae60';
            if(in_array($item, $checkingErrorQC1K)){
                $val = 'X';
                $color = 'c0392b';
            }
            if($item != $localNow){
                $localNow = $item;
                setValue($activeSheet,$err, $val, $color);
                $write = true;
                continue;
            }
            if($write){
                setValue($activeSheet,$err, $val, $color);
                $write = false;
            }
        }
    }

    foreach ($path as $item => $value) {
        $drawing = new PHPExcel_Worksheet_MemoryDrawing();
        if($count != 0 && $count % 3 == 0){
            $now = 0;
            $i += $_sSum;
        }
        $rowDraw = $ss[$now];
        $drawing->setName('Img' . $i)
            ->setDescription('Error of img ' . $i)
            ->setImageResource(imagecreatefrompng($value))
            ->setRenderingFunction(PHPExcel_Worksheet_MemoryDrawing::RENDERING_PNG)
            ->setMimeType(PHPExcel_Worksheet_MemoryDrawing::MIMETYPE_DEFAULT)
            ->setHeight($height)
            ->setCoordinates($rowDraw.$i)
            ->setWorksheet($excel->getActiveSheet());
        $objWrite = PHPExcel_IOFactory::createWriter($excel,'Excel2007');
        $objWrite->save($path_file_excel);
        $now++;
        $count++;
    }
    $subSheet++;
    if($subSheet <= $endSheet)
        $excel->createSheet();
}

$path_fake_file_excel = '../files/exported/' . $name_file_excel . '.xlsx';

if(!file_exists($path_fake_file_excel)){
    $f = fopen($path_fake_file_excel, 'w');
    fclose($f);
}
$code = '200';
$data = 'DONE';
if(!copy($path_file_excel, $path_fake_file_excel)){
    $code = '204';
    $data = 'Copy file error';
}

//export to pdf file for print()

$path_fake_file_pdf = '../files/exported/' . $name_file_excel . '.pdf';
$path_file_pdf = '../files/pdf/' . $name_file_excel . '.pdf';

require '../vendor/autoload.php';

use \ConvertApi\ConvertApi;

ConvertApi::setApiSecret('ZCOQk0BmY3ah3WMq');
$result = ConvertApi::convert('pdf', [
    'File' => '../files/excel/'.$name_file_excel.'.xlsx',
    'PdfResolution' => '2400',
], 'xlsx'
);
$result->saveFiles($path_file_pdf);

if(!copy($path_file_pdf, $path_fake_file_pdf)){
    $code = '204';
    $data = 'Copy file pdf error';
}

echo json_encode([
    'code' => $code ?? '200',
    'data' => $data ?? 'Done',
    'url' => 'files/pdf/'.$name_file_excel.'.pdf'
]);

$ds = DIRECTORY_SEPARATOR;
//remove all image
if ($handle = opendir(__DIR__ . $ds . '..' . $ds . 'files' . $ds . 'images')) {
    while (false !== ($entry = readdir($handle))) {
        if ($entry != "." && $entry != "..") {
            unlink($entry);
        }
    }
    closedir($handle);
}

$endTime = round(microtime(true) * 1000);
$log->write('End time: ' . date('Y-m-d H:m:s'));
$log->write('Swart: ' . floor(($endTime - $beginTime)/1000));
$log->write(PHP_EOL);

return true;