
<?php

    require_once "./_header.php";

    $checking = $_SESSION['logined']['position'] == 'SEALER' ? 'sealer_checking' : 'checking';
    $checking_error = $_SESSION['logined']['position'] == 'SEALER' ? 'history_err_sealer' : 'history_err';

    $db->setTable($checking);

    $type = $_POST['type'];
    $error = $_POST['error'];

    
    $error_code = $_POST['error_code'];

    if(gettype($error) != 'array'){
        $error_toadoX = $error['toadoX'];
        $error_toadoY = $error['toadoY'];
        $error_type = $error['type_error'];
        $error_position = $error['error_position'];
        $err_other = $error['err_other'];
        $insert = $db->insert([
            'error_code' => $error_code ,
            'error_position' => $error_position ,
            'error_toadoX' => $error_toadoX,
            'error_toadoY' => $error_toadoY,
            'error_type' => $error_type,
            'error_user' => strtoupper($_SESSION['logined']['username']),
            'error_fullname_user' => $_SESSION['logined']['fullname'] ?? $_SESSION['logined']['username'],
            'error_user_code' => $_SESSION['logined']['usercode'] ?? $_SESSION['logined']['username'],
            'err_other' => $err_other
        ]);
        if(!$insert){
            echo json_encode([
                'code' => 202,
                'message' => "Sql insert error!"
            ]);
            return true;
        }
        $err_id = "ERR_MV_" . $insert;
        if(!$db->update(['err_id' => $err_id], ['id', $insert])){
            echo json_encode([
                'code' => 202,
                'message' => "SQL FAIL",
                'error' => "Error update error_id"
            ]);
            return true;
        }
        $db->setTable($checking_error);
        $hough = date("H:m:s");
        $date = date("d-m-Y");
        $arr_in = [
            'error_code' => $error_code,
            'err_id'=>$err_id,
            'err_user_change' => strtoupper($_SESSION['logined']['username']),
            'err_time_change' => $hough,
            'err_date_change'=>$date,
            'err_user_code'=>$_SESSION['logined']['usercode'] ?? $_SESSION['logined']['username'],
            'err_user_fullname'=>$_SESSION['logined']['fullname'] ?? $_SESSION['logined']['username']
        ];
        if(!$db->insert($arr_in)){
            echo json_encode([
                'code' => 202,
                'message' => "SQL FAIL",
                'error' => "Error insert history error"
            ]);
            return true;
        }
        echo json_encode([
            'code' => 200,
            'message' => "Insert success",
            'dd' => $insert
        ]);
        return true;
    }

    $error_sum = 0;
    $iss = [];

    $db->setTable($checking);
    foreach ($error as $item => $value){
        $error_toadoX = $value['toadoX'];
        $error_toadoY = $value['toadoY'];
        $error_type = $value['type_error'];
        $error_position = $value['error_position'];
        $err_other = $value['err_other'];
        $insert = $db->insert([
            'error_code' => $error_code ,
            'error_position' => $error_position ,
            'error_toadoX' => $error_toadoX,
            'error_toadoY' => $error_toadoY,
            'error_type' => $error_type,
            'error_user' => strtoupper($_SESSION['logined']['username']),
            'error_fullname_user' => $_SESSION['logined']['fullname'] ?? $_SESSION['logined']['username'],
            'error_user_code' => $_SESSION['logined']['usercode'] ?? $_SESSION['logined']['username'],
            'err_other' => $err_other
        ]);
        if(!$insert){
            $error_sum++;
        }
        array_push($iss, $insert);
    }

    if($error_sum != 0){
        echo json_encode([
            'code' => 202,
            'message' => "SQL FAIL",
            'error' => $error_sum
        ]);
        return true;
    }

    $err_update = false;
    $err_insert_history = false;
    foreach ($iss as $item => $value){
        $db->setTable($checking);
        $err_id = "ERR_MV_" . $value;
        if(!$db->update(['err_id' => $err_id], ['id', $value])){
            $err_update = true;
            break;
        }
        $db->setTable($checking_error);
        $hough = date("H:m:s");
        $date = date("d-m-Y");
        $arr_in = [
            'err_code'=>$error_code,
            'err_id'=>$err_id,
            'err_user_change' => strtoupper($_SESSION['logined']['username']),
            'err_time_change' => $hough,
            'err_date_change'=>$date,
            'err_user_code'=>$_SESSION['logined']['usercode'] ?? $_SESSION['logined']['username'],
            'err_user_fullname'=>$_SESSION['logined']['fullname'] ?? $_SESSION['logined']['username'],
        ];
        if(!$db->insert($arr_in)){
            $err_insert_history = true;
            break;
        }
    }
    if($err_update){
        echo json_encode([
            'code' => 202,
            'message' => "SQL FAIL",
            'error' => "Error update error_id"
        ]);
        return true;
    }
    if($err_insert_history){
        echo json_encode([
            'code' => 202,
            'message' => "SQL FAIL",
            'error' => "Error insert history error"
        ]);
        return true;
    }

    echo json_encode([
        'code' => 200,
        'message' => "Insert success",
        'error' => $error_sum,
        'data' => $iss ?? ''
    ]);
    return true;