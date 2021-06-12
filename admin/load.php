<?php

if(!isset($_POST)){
    echo json_encode([
        'code' => 402,
        'data' => 'Method Post only!'
    ]);
    return true;
}

date_default_timezone_set("Asia/Ho_Chi_Minh");
require_once './function.php';
require_once "../vendor/PDOconnect/pdo/PdoConnect.php";
$db = new pdoRequest();
$config = require_once 'config.php';

$page = $_POST['page'] ?? 'dashboard';
$params = $_POST['params'] ?? [];

switch($page){
    case 'user' :
        $goto = $params['goto'] ?? '1';
        $countRow = (int)$config['limitRowInPage'];
        $db->setTable('account_qr_code');
        $allRow = $db->count();
        $userGetByCode = $db->getPage((int)$goto, $countRow, ['position', 'ASC', 'number']);
        $db->setTable('admin_account');
        $userGetByAcc = $db->all();
        $user = [];
        foreach ($userGetByCode as $item => $val){
            foreach ($userGetByAcc as $i => $v){
                if($v['id'] == $val['position']){
                    $val['position'] = strtoupper($v['position']);
                    break;
                }
            }
            array_push($user, $val);
        }
        break;
    case 'user-add':
    case 'user-edit' :
        $db->setTable('admin_account');
        $position = $db->all(['id', 'position', 'allowAll'],null,['id', 'ASC', 'number']);
        foreach ($position as $item => $val){
            if($val['position'] == 'ADMIN'){
                unset($position[$item]);
                break;
            }
        }
        if($page == 'user-edit'){
            $db->setTable('account_qr_code');
            $id = $params['id'] ?? '';
            $_user = $db->one(['id', $id]);
            if(empty($_user)){
                echo json_encode([
                    'code' => 200,
                    'content' => '<h3 class="text-center mt-3">ID not found!</h3>'
                ]);
                return true;
            }
        }
        break;
    case 'dashboard' :
        $db->setTable('checking');
        $datas = $db->all([], 14, ['created_at', 'DESC']);
        $datas = _require($datas, $db);
        break;
    case 'detail' :
        $params = [
            'p' => 1,
            'start' => date('m/d/Y'),
            'end' => date('m/d/Y')
        ];
        include './page/sortDetail.php';
        return true;
    case 'sealer_dashboard' :
        include './page/sortSealerDashboard.php';
        return true;
}

ob_start();
require_once './' . $page . '.php';
$content = ob_get_contents();
ob_end_clean();

echo json_encode([
    'code' => 200,
    'content' => $content
]);
return true;