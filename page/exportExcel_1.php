<?php

if(!isset($_SESSION)) session_start();
if(!isset($_SESSION['logined'])){
    echo json_encode([
        'code' => 401,
        'message' => 'Please login!'
    ]);
    return true;
}

//func get image name
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

function draw(&$excel, $image, $coordinates,$path_file_excel,$heightSignature){
    $drawing = new PHPExcel_Worksheet_MemoryDrawing();
    $drawing->setName('Img of ' . md5($image))
        ->setDescription('Img of ' . md5($image))
        ->setImageResource(imagecreatefrompng($image))
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

date_default_timezone_set("Asia/Ho_Chi_Minh");
require_once "../vendor/PDOconnect/pdo/PdoConnect.php";
require_once '../vendor/PHPExcel/Classes/PHPExcel.php';
require_once './configFile.php';
require_once './log.php';
$db = new pdoRequest();
$log = new Logger();

$beginTime = round(microtime(true) * 1000);
$log->write('Start time: ' . date('Y-m-d H:m:s'));

$images         = $_POST['images'];
$vin_code       = $_POST['vin_code'];
$vin_code_mini  = $_POST['vin_code_mini'];
$username       = $_SESSION['logined']['username'] ?? 'admin';
$config         = [$configExcelSheet1, $configExcelSheet2];

$db->setTable('car_code');
$folder = $db->one(['car_code',$vin_code_mini])['car_folder'];

$imgSealer  = recursiveSearch("../assets/images/" . $folder . '/SEALER',"/^.*\.(jpg|jpeg|png)$/");
$imgLH      = recursiveSearch("../assets/images/" . $folder . '/LH',"/^.*\.(jpg|jpeg|png)$/");
$imgRH      = recursiveSearch("../assets/images/" . $folder . '/RH',"/^.*\.(jpg|jpeg|png)$/");

//default url signature
$signaturePath = realpath('../assets/images/Stamp');
$heightSignature = 68;

$pathImg = [];
//save image
foreach ($images as $is => $image){
    $path = [];
    foreach ($image as $item => $valImg) {
        $img = str_replace('data:image/png;base64,', '', $valImg);
        $img = str_replace(' ', '+', $img);
        $data = base64_decode($img);
        $rand = rand(10,9999) . rand(10,9999);
        $date = new DateTime();
        $date = $date->format('Y-m-d-H-i-s');
        $fileName = $username . '_' . implode("", explode('-', $date)). '_' . $rand;
        $img_path = '../files/images/' . $fileName . '.png';

        file_put_contents($img_path, $data);
        array_push($path, $img_path);
    }
    array_push($pathImg, $path);
}

//conf file excel
$name_file_excel = $username . '_' . $vin_code . '_' . rand(1000,9999);
$path_file_excel = '../files/excel/' . $name_file_excel . '.xlsx';

PHPExcel_Settings::setZipClass(PHPExcel_Settings::PCLZIP);
$tempExcel = PHPExcel_IOFactory::load('./temp_demo.xls');

$tempExcel->setActiveSheetIndex(0);
$activeSheet = $tempExcel->getActiveSheet();

//get config
extract($config[0]);

$db->setTable('history_err_sealer');
$user_check = $db->one([['err_code', $vin_code], ['err_level', '2']])['err_user_fullname'] ?? '';
$hasError = $db->one([['err_code', $vin_code], ['err_level', 'IN', ['1','3']]]) ?? false;

$db->setTable('car_sealered');
$user_create = $db->one(['vin_code', $vin_code])['user_submit'] ?? '';

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
    $image = $signaturePath . DIRECTORY_SEPARATOR . $userSubmitSealer['usercode_submit'] . '.png';
    //draw img sheet SEALER
    draw($tempExcel,$image, $signatureSubmit1, $path_file_excel,$heightSignature);
    draw($tempExcel,$image, $signatureSubmit2, $path_file_excel,$heightSignature);
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

$tempExcel->setActiveSheetIndex(1);
$activeSheet = $tempExcel->getActiveSheet();

//get config
extract($config[1]);

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

$activeSheet->setCellValue($userPolishLocation,$userPolish);
$activeSheet->setCellValue($userRepairLocation,$userRepair);
$activeSheet->setCellValue($userRepairV2Location,$userRepairV2);

$db->setTable('history_err');
$userSubmitLH = $db->one([['err_code', $vin_code],['err_user_change', 'LH'], ['err_level', '1']]);
$userSubmitRH = $db->one([['err_code', $vin_code],['err_user_change', 'RH'], ['err_level', '1']]);

//draw img sheet QC1K
//LH
if(count($userSubmitLH) > 0){
    $imageLH = $signaturePath . DIRECTORY_SEPARATOR . $userSubmitLH['err_user_code'] . '.png';
    draw($tempExcel,$imageLH, $signatureLH, $path_file_excel,$heightSignature);
}
//RH
if(count($userSubmitRH) > 0){
    $imageRH = $signaturePath . DIRECTORY_SEPARATOR . $userSubmitRH['err_user_code'] . '.png';
    draw($tempExcel,$imageRH, $signatureRH, $path_file_excel,$heightSignature);
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

foreach ($config as $vl => $cf){
    extract($cf);
    $tempExcel->setActiveSheetIndex($vl);
    foreach ($pathImg[$vl] as $item => $value) {
        if($count != 0 && $count % 3 == 0){
            $now = 0;
            $i += $_sSum;
        }
        $rowDraw = $ss[$now];
        draw($tempExcel, $value, $rowDraw.$i, $path_file_excel, $height);
        $now++;
        $count++;
    }
}

//save
$objExcel = PHPExcel_IOFactory::createWriter($tempExcel, 'Excel2007');
$objExcel->save($path_file_excel);

$path_fake_file_excel = '../files/exported/' . $name_file_excel . '.xlsx';

$code = '200';
$data = 'DONE';
if(!copy($path_file_excel, $path_fake_file_excel)){
    $code = '204';
    $data = 'Copy file error';
}

require '../vendor/autoload.php';

use \ConvertApi\ConvertApi;

$path_fake_file_pdf = '../files/exported/' . $name_file_excel . '.pdf';
$path_file_pdf = '../files/pdf/' . $name_file_excel . '.pdf';

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

$endTime = round(microtime(true) * 1000);
$log->write('End time: ' . date('Y-m-d H:m:s'));
$log->write('Swart: ' . floor(($endTime - $beginTime)/1000));
$log->write(PHP_EOL);