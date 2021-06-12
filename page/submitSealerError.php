<?php
    require_once "./_header.php";

    if(isset($_POST['all']) && $_POST['all'] == 'true'){
        $mess = [];
        $db->setTable('sealer_checking');
        $aryAll = $db->get([['error_code', $_POST['code']], ['err_level', '2']]);

        foreach ($aryAll as $item => $vals){
            $db->setTable('sealer_checking');
            if(!$db->update(['err_level' => '3'], ['err_id', $vals['err_id']])){
                array_push($mess, 'Error update with error id ' . $vals['err_id']);
            }else{
                $db->setTable('history_err_sealer');
                $aryInsert = [
                    'err_id' => $vals['err_id'],
                    'err_user_change' => $_SESSION['logined']['username'],
                    'err_user_code' => $_SESSION['logined']['usercode'] ?? $_SESSION['logined']['username'],
                    'err_user_fullname' => $_SESSION['logined']['fullname'] ?? $_SESSION['logined']['username'],
                    'err_level'=> '3',
                    'err_time_change' => date('H:m:s'),
                    'err_date_change' => date('d-m-Y'),
                ];
                if(!$db->insert($aryInsert)){
                    array_push($mess, 'Error insert history with error id ' . $vals['err_id']);
                }
            }

        }

        if(empty($mess)){
            echo json_encode([
                'code' => 200,
                'message' => implode(', ', $mess)
            ]);
            return true;
        }else{
            echo json_encode([
                'code' => 204,
                'message' => implode(', ', $mess)
            ]);
            return true;
        }
    }

    $errid = $_POST['errid'];
    $note = $_POST['note'] ?? '';
    $userCode = $_SESSION['logined']['usercode'] ?? $_SESSION['logined']['username'];
    $userFullname = $_SESSION['logined']['fullname'] ?? $_SESSION['logined']['username'];

    $db->setTable('sealer_checking');

    $aryupdate = [
        'error_fullname_user' => $userFullname,
        'error_user_code' => $userCode,
        'err_level' => '0',
        'err_note' => $note
    ];

    if($db->update($aryupdate, ['err_id', $errid])){
        echo json_encode([
            'code' => 200,
            'message' => 'Create request success!'
        ]);
        return true;
    }
    echo json_encode([
        'code' => 204,
        'message' => 'Create request error!'
    ]);
    return true;
