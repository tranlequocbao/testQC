<?php

    if(!isset($_SESSION)) session_start();
    if(!isset($_SESSION['logined'])){
        echo json_encode([
            'code' => 401,
            'message' => 'Please login!'
        ]);
        return true;
    }
    require_once "../vendor/PDOconnect/pdo/PdoConnect.php";
    $db = new pdoRequest();
    $db->setTable('car_code');

    $code = $_POST['code'] ?? '';
    $car_code = $_POST['car_code'] ?? '';

    $aryUserInsertCode = ['LH', 'RH'];

    if($code == ''){
        echo json_encode([
            'code' => 205,
            'message' => 'Please request code!'
        ]);
        return true;
    }

    $result = $db->one(['car_code',$code]);
    if($result == null){
        echo json_encode([
            'code' => 201,
            'message' => 'No result',
            'dd' => $code
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
//                $exp = $_SESSION['logined']['position'] != 'ADMIN' ? explode('\\',$file[0]) : $file[0];
                $exp = explode('\\',$file[0]);
                $fileList[] = array(
                    'image' => end($exp)
                );
            }
        }
        return $fileList;
    }
    $folder = $_POST['sealer'] == 'true' ? "SEALER" : $_SESSION['logined']['position'];

    $arr_img = recursiveSearch("../assets/images/" . $result['car_folder'] . '/' . $folder,"/^.*\.(jpg|jpeg|png)$/");

    //Update car to polish
    //status insert (return status but not drag element)
    $sttInsertCode = 1;
    if(in_array($_SESSION['logined']['position'],$aryUserInsertCode)){
        $db->setTable('polish_car');
        if(!$db->one(['vin_code', $car_code])){
            $db->setTable('rfid');
            $color = $db->one(['vin_code', $car_code])['color'];
            $db->setTable("car_code");
            $folder = $db->one(['car_code', $code])['car_folder'] ?? '';
            $db->setTable('polish_car');
            $aryInsert = [
                'vin_code' => $car_code,
                'folder' => $folder,
                'color' => $color,
                'user_submit' => $_SESSION['logined']['fullname'] ?? $_SESSION['logined']['username'],
                'usercode_submit' => $_SESSION['logined']['usercode'] ?? '',
            ];
            if(!$db->insert($aryInsert)){
                $sttInsertCode = 0;
            }
        }
    }

    echo json_encode([
        'code' => 200,
        'message' => 'success',
        'data' => $arr_img,
        'folder' => $result['car_folder'],
        'sttInsert' => $sttInsertCode
    ]);
    return true;