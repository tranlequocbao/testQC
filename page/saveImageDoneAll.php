<?php

if(!isset($_SESSION)) session_start();
if(!isset($_SESSION['logined'])){
    echo json_encode([
        'code' => 401,
        'message' => 'Please login!'
    ]);
    return true;
}

function getErrorPosition(array $arrs){
    $result = [];
    foreach ($arrs as $item => $arr){
        array_push($result, $arr['error_position']);
    }
    return array_unique($result);
}

function draw(&$excel, $fileName, $coordinates,$signaturePath,$path_file_excel,$heightSignature){
    if(!file_exists($signaturePath . DIRECTORY_SEPARATOR . $fileName . '.png')){
        return true;
    }
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
    return true;
}

function setValue(&$activeSheet, $location, $value, $color){
    return $activeSheet->setCellValue($location,$value)->getStyle($location)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB($color);
}

function updateToTableCarExportByTool($db, $vincode, $begin = false){
    $db->setTable('car_export_by_tool');
    $action = 'insert';
    $where = null;
    $aryQuery = [
        'car_code' => $vincode,
        'user_submit_code' => $_SESSION['logined']['usercode'] ?? '',
        'user_submit_name' => $_SESSION['logined']['fullname'] ?? 'Unknow'
    ];
    $getCar = $db->one(['car_code', $vincode]);
    if(!empty($getCar)){
        $action = 'update';
        $aryQuery = [
            'count_submit' => (int)$getCar['count_submit'] + 1
        ];
        if($begin){
            $aryQuery['exported'] = '0';
            $aryQuery['finished'] = '0';
        }else{
            $aryQuery['exported'] = '1';
            $aryQuery['finished'] = '0';
        }
        $where = ['car_code', $vincode];
    }
    if(!$db->{$action}($aryQuery, $where)){
        return false;
    }
    return true;
}

require_once "../vendor/PDOconnect/pdo/PdoConnect.php";
require_once '../vendor/PHPExcel/Classes/PHPExcel.php';
require_once './configFile.php';
require_once './log.php';
$db = new pdoRequest();
$log = new Logger();

if(isset($_POST['tool']) && $_POST['tool'] == '1' && !updateToTableCarExportByTool($db, $_POST['vin_code'], true)){
    echo json_encode([
        'code' => 201,
        'message' => 'Warning: updateToTableCarExportByTool error begin'
    ]);
    return true;
}

$images = $_POST['images'];
$vin_code = $_POST['vin_code'];
$vin_code_mini = $_POST['vin_code_mini'];

$username = $_SESSION['logined']['username'] ?? 'admin';

$path = [];

$db->setTable('car_code');

$folder = $db->one(['car_code',$vin_code_mini])['car_folder'];

//default url signature
$signaturePath = realpath('../assets/images/Stamp');
$heightSignature = 68;

//uploadImg goto server
//nameFile place by Y-m-d-H-i-s so haven't duplicate.
$subSheet = 0;
$endSheet = 1;

$config          = [$configExcelSheet1, $configExcelSheet2];

$ds = DIRECTORY_SEPARATOR;
$path_export_temp = __DIR__ . $ds . '..' . $ds . 'files' . $ds . 'export_temp' . $ds . $vin_code;
$name_file_excel = $vin_code;
$path_file_excel = $path_export_temp . $ds . 'excel' . $ds . $name_file_excel . '.xlsx';

//make folder if not exists
if (!file_exists($path_export_temp. $ds . 'images' . $ds . 'SEALER')) {
    mkdir($path_export_temp. $ds . 'images' . $ds . 'SEALER', 0777, true);
}
if (!file_exists($path_export_temp. $ds . 'images' . $ds . 'QC1K')) {
    mkdir($path_export_temp. $ds . 'images' . $ds . 'QC1K', 0777, true);
}
if (!file_exists($path_export_temp. $ds . 'excel')) {
    mkdir($path_export_temp. $ds . 'excel', 0777, true);
}
if (!file_exists($path_export_temp. $ds . 'doned')) {
    mkdir($path_export_temp. $ds . 'doned', 0777, true);
}

//remove all image SEALER
if ($handle = opendir($path_export_temp. $ds . 'images' . $ds . 'SEALER')) {
    while (false !== ($entry = readdir($handle))) {
        if ($entry != "." && $entry != "..") {
            try{
                unlink($path_export_temp. $ds . 'images' . $ds . 'SEALER' . $ds . $entry);
            }catch (Exception $e){
                $log->write($e);
            }
        }
    }
    closedir($handle);
}

//remove all image QC1K
if ($handle = opendir($path_export_temp. $ds . 'images' . $ds . 'QC1K')) {
    while (false !== ($entry = readdir($handle))) {
        if ($entry != "." && $entry != "..") {
            try{
                unlink($path_export_temp. $ds . 'images' . $ds . 'QC1K' . $ds . $entry);
            }catch (Exception $e){
                $log->write($e);
            }
        }
    }
    closedir($handle);
}

//remove all file excel
//if ($handle = opendir($path_export_temp. $ds . 'excel')) {
//    while (false !== ($entry = readdir($handle))) {
//        if ($entry != "." && $entry != "..") {
//            try{
//                unlink($path_export_temp. $ds . 'excel' . $ds . $entry);
//            }catch (Exception $e){
//                $log->write($e);
//            }
//        }
//    }
//    closedir($handle);
//}

foreach ($images as $is => $image){
    foreach ($image as $nameImg => $valImg) {
        $img = str_replace('data:image/png;base64,', '', $valImg);
        $img = str_replace(' ', '+', $img);
        $data = base64_decode($img);

        $img_path = $path_export_temp. $ds . 'images' . $ds . strtoupper($is) . $ds . $nameImg . '.png';

        file_put_contents($img_path, $data);
    }
}

//get info car
$db->setTable('car_code');
$infoCar = $db->one(['car_code', $vin_code_mini]);

$aryCarGAY = ['J72A', 'J72K'];

$nameFileExcelUse = 'temp_demo.xls';
$configFile = !in_array($infoCar['car_folder'], $aryCarGAY) ? $configFileCarOld : $configFileCarGAY;
if(isset($infoCar)&&$infoCar['type_export'] == 'new'){
    
    $nameFileExcelUse = 'temp_pg.xls';
    $configFile = $configFileCarNew;
}

$data_res = [];

$excel  = PHPExcel_IOFactory::load('../files/excel/' . $nameFileExcelUse);
$excel  ->getProperties()
        ->setCreator('CDD')
        ->setTitle('Export Thông tin xe: ' . $vin_code)
        ->setLastModifiedBy(date('d/m/Y'))
        ->setSubject('Export')
        ->setDescription('Export Thông tin xe: ' . $vin_code)
        ->setKeywords($vin_code)
        ->setCategory('QC1K');

for ($subSheet = 0; $subSheet <= 1; $subSheet++) {

    $excel->setActiveSheetIndex($subSheet);
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

        $db->setTable('checking');
        $dayCreate1 = $db->one(['error_code', $vin_code])['updated_at'] ??'';
        $dayCreate1 = substr($dayCreate1,0,10);
        $dayCreate2 = $db->get(['error_code', $vin_code], null, ['created_at' => "DESC"]);
        // var_dump($dayCreate2);
        // die;
        
        $db->setTable('car_code');
        $getInCarCode = $db->one(['car_code', $vin_code_mini]);
        $car_type = $getInCarCode['car_type'] ?? '';
        $car_folder = $getInCarCode['car_folder'] ?? '';

        $db->setTable('plan_vin');
        $color = $db->one(['vin_code', $vin_code])['color'] ?? '';

        $vinCode_v2     = $activeSheet->getCell($vincodeLocation)->getValue() . ' ' . $vin_code;
        $dayCreate1      = $activeSheet->getCell($dayexportLocation)->getValue() . ' ' . $dayCreate1;
        $car_type       = $activeSheet->getCell($cartypeLocation)->getValue() . ' ' . $car_type;
        $car_folder     = $activeSheet->getCell($carfolerLocation)->getValue() . ' ' . $car_folder;
        $color          = $activeSheet->getCell($colorLocation)->getValue() . ' ' . $color;

        $activeSheet->setCellValue($userCheckLocation,$user_check);
        $activeSheet->setCellValue($userCreateLocation,$user_create);
        $activeSheet->setCellValue($vincodeLocation,$vinCode_v2);
        $activeSheet->setCellValue($dayexportLocation,$dayCreate1);
        $activeSheet->setCellValue($cartypeLocation,$car_type);
        $activeSheet->setCellValue($carfolerLocation,$car_folder);
        $activeSheet->setCellValue($colorLocation,$color);

        $db->setTable('car_sealered');
        $userSubmitSealer = $db->one(['vin_code',$vin_code]);

        //codition old: count($userSubmitSealer) > 0 && !!$hasError
        if(count($userSubmitSealer) > 0){
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
                setValue($activeSheet,$err, 'V', 'ffffff');
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

        $activeSheet->setCellValue($userPolishLocation,$userPolish);
        $activeSheet->setCellValue($userRepairLocation,$userRepair);
        $activeSheet->setCellValue($userRepairV2Location,$userRepairV2);

        $db->setTable('history_err');
        $userSubmitLH = $db->one([['err_code', $vin_code],['err_user_change', 'LH'], ['err_level', '1']]);
        $userSubmitRH = $db->one([['err_code', $vin_code],['err_user_change', 'RH'], ['err_level', '1']]);

        //check error
        $errorLHConfig = $configFile['LH'];
        $errorRHConfig = $configFile['RH'];
        $localNow = '';
        $write = true;
        $db->setTable('checking');
        $checkingErrorQC1K = $db->get([['error_code', $vin_code], ['recoat_flag','0']]) ?? [];
        $checkingErrorQC1K = getErrorPosition($checkingErrorQC1K);
        $arySaveBeforeData = [];
        foreach (array_merge($errorLHConfig, $errorRHConfig) as $item => $err){
            $val = 'V';
            $color = 'ffffff';

            if(isset($arySaveBeforeData['location'])){
                if($arySaveBeforeData['location'] != $err){
                    $arySaveBeforeData = [];
                }elseif(in_array($item, $checkingErrorQC1K)){
                    $val = 'X';
                    $color = 'c0392b';
                    $arySaveBeforeData['flag'] = 0;
                    setValue($activeSheet, $err, $val, $color);
                    continue;
                }else{
                    continue;
                }
            }
            if(empty($arySaveBeforeData)){
                $arySaveBeforeData = [
                    'item' => $item,
                    'location' => $err,
                    'flag' => 1
                ];
            }

            if(in_array($item, $checkingErrorQC1K)){
                $val = 'X';
                $color = 'c0392b';
                $arySaveBeforeData['flag'] = 0;
            }

            setValue($activeSheet, $err, $val, $color);
        }

        //draw img sheet QC1K
        //LH
        if(gettype($userSubmitLH) == 'array' && count($userSubmitLH) > 0){
            draw($excel,$userSubmitLH['err_user_code'], $signatureLH1, $signaturePath, $path_file_excel,$heightSignature);
            draw($excel,$userSubmitLH['err_user_code'], $signatureLH2, $signaturePath, $path_file_excel,$heightSignature);
        }
        //RH
        if(gettype($userSubmitRH) == 'array' && count($userSubmitRH) > 0){
            draw($excel,$userSubmitRH['err_user_code'], $signatureRH1, $signaturePath, $path_file_excel,$heightSignature);
            draw($excel,$userSubmitRH['err_user_code'], $signatureRH2, $signaturePath, $path_file_excel,$heightSignature);
        }
    }

    if($subSheet < $endSheet){
        $excel->createSheet();
    }
}

while(1){
    try{
        $excel->setActiveSheetIndexByName('Worksheet');
        $sheetIndex = $excel->getActiveSheetIndex();
        $excel->removeSheetByIndex($sheetIndex);
    }catch (Exception $exception){
        break;
    }
}

$db->setTable('car_submit_by_repair_v2');
if(!$db->update(['exported' => 1], ['car_code', $vin_code])){
    echo json_encode([
        'code' => 201,
        'message' => 'Warning: update v2 error'
    ]);
    return true;
}

if(isset($_POST['tool']) && $_POST['tool'] == '1' && !updateToTableCarExportByTool($db, $vin_code)){
    echo json_encode([
        'code' => 201,
        'message' => 'Warning: updateToTableCarExportByTool error'
    ]);
    return true;
}

echo json_encode([
    'code' => 200,
    'message' => 'Done',
    'data' => $data_res
]);

return true;