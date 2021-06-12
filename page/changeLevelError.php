<?php
require_once "./_header.php";

$err_id = $_POST['id'] ?? '';
$level = $_POST['level'] ?? '';
$arrError = $_POST['arrError'] ?? '';
$arr_res = [];
$sealer = $_POST['sealer'] ?? false;
$err_note = $_POST['noteError'] ?? '';
$err_code = $_POST['err_code'] ?? '';
$povilish = $_POST['polish'] ?? '';
$usercode = $_POST['usercode'] ?? '';

$isUserCheckV2 = isset($_SESSION['logined']['user_check']) && strtolower($_SESSION['logined']['user_check']) == 'check_repair';

if ($err_id == '' || $level == '') {
    echo json_encode([
        'code' => 401,
        'type' => 'error',
        'res' => 'Not found id and level!'
    ]);
    return true;
}

date_default_timezone_set("Asia/Ho_Chi_Minh");
require_once "../vendor/PDOconnect/pdo/PdoConnect.php";
$db = new pdoRequest();

$table = 'checking';
$table_history = 'history_err';
if ($sealer == 'true') {
    $table = 'sealer_checking';
    $table_history = 'history_err_sealer';
}
$arySubmit = [
    'fullname' => $_SESSION['logined']['fullname'],
    'usercode' => is_null($usercode) ? $_SESSION['logined']['usercode'] : $usercode,
    'username' => $_SESSION['logined']['user_check'] ?? $_SESSION['logined']['username'],
    'car_code' => $err_code,
    'povilish' => $povilish
];
if ($arrError != '' && $arrError != '0') {
    $db->setTable('car_qc1k_submit');
    if (!$db->insert($arySubmit)) {
        echo json_encode([
            'code' => 204,
            'type' => 'error',
            'res' => 'Error insert submit car'
        ]);
        return true;
    }
    foreach ($arrError as $item => $value) {
        $usercode = $value['usercode'] != '' ? $value['usercode'] : $_SESSION['logined']['usercode'];
        $aryUpdate = [
            'err_level' => $level,
            'err_note' => $err_note
        ];
        $aryUpdate = ['aryUpdate' => $aryUpdate, 'err_code' => $err_code];
        if (!queryChangeLevelError($db, $aryUpdate, $value['err_id'], $arr_res, $table, $table_history, $usercode)) {
            echo json_encode([
                'code' => 204,
                'type' => 'error',
                'res' => $arr_res
            ]);
            return true;
        }
    }
    if ($isUserCheckV2) {
        if (updateToCarSubmitRepairV2($db, $err_code)['status'] != 1) {
            echo json_encode([
                'code' => 201,
                'type' => 'Warning: updateToCarSubmitRepairV2 error!',
                'res' => $arr_res[0]
            ]);
            return true;
        }
    }
    echo json_encode([
        'code' => 200,
        'type' => 'success 1',
        'res' => $arr_res[0]
    ]);
    return true;
}
$db->setTable('car_qc1k_submit');
if (!$db->insert($arySubmit)) {
    echo json_encode([
        'code' => 204,
        'type' => 'error',
        'res' => 'Error insert submit car'
    ]);
    return true;
}
$usercode = $_POST['usercode'] ?? $_SESSION['logined']['usercode'];
$aryUpdate = [
    'err_level' => $level,
    'err_note' => $err_note
];
$aryUpdate = ['aryUpdate' => $aryUpdate, 'err_code' => $err_code];
queryChangeLevelError($db, $aryUpdate, $err_id, $arr_res, $table, $table_history, $usercode);
if ($isUserCheckV2) {
    if (updateToCarSubmitRepairV2($db, $err_code)['status'] != 1) {
        echo json_encode([
            'code' => 201,
            'type' => 'Warning: updateToCarSubmitRepairV2 error!',
            'res' => $arr_res[0]
        ]);
        return true;
    }
}
echo json_encode([
    'code' => 200,
    'type' => 'success 2',
    'res' => $arr_res[0]
]);
return true;
function queryChangeLevelError($db, $aryUpdate, $err_id, &$arr_res, $table, $table_history, $usercode = null)
{
    $err_code = $aryUpdate['err_code'];
    $aryUpdate = $aryUpdate['aryUpdate'];
    $db->setTable($table);
    if (!$db->update($aryUpdate, ['err_id', $err_id])) {
        array_push($arr_res, [
            'code' => 208,
            'message' => 'Update level fail'
        ]);
        return false;
    }
    $db->setTable($table_history);
    $err_insert_history = [
        'err_code' => $err_code ?? '',
        'err_id' => $err_id,
        'err_user_change' => $_SESSION['logined']['username'],
        'err_user_code' => $usercode ?? $_SESSION['logined']['username'],
        'err_user_fullname' => $_SESSION['logined']['fullname'] ?? $_SESSION['logined']['username'],
        'err_level' => $aryUpdate['err_level'],
        'err_time_change' => date('H:m:s'),
        'err_date_change' => date('d-m-Y'),
    ];
    if (!$db->insert($err_insert_history)) {
        array_push($arr_res, [
            'code' => 208,
            'message' => 'Insert history fail'
        ]);
        return false;
    }
    array_push($arr_res, [
        'code' => 200,
        'message' => 'Success',
        'note' => $aryUpdate['err_note'] ?? ''
    ]);
    return true;
}

function updateToCarSubmitRepairV2($db, $vincode)
{
    $db->setTable('car_submit_by_repair_v2');
    $action = 'insert';
    $where = null;
    $aryQuery = [
        'car_code' => $vincode,
        'user_submit_code' => $_SESSION['logined']['usercode'] ?? '',
        'user_submit_name' => $_SESSION['logined']['fullname'] ?? 'Unknow'
    ];
    $getCar = $db->one(['car_code', $vincode]);
    if (!empty($getCar)) {
        $action = 'update';
        $aryQuery = [
            'count_submit' => (int)$getCar['count_submit'] + 1,
            'finished' => 0
        ];
        $where = ['car_code', $vincode];
    }
    if (!$db->{$action}($aryQuery, $where)) {
        return [
            'status' => 2,
            'message' => $action . ' car_submit_by_repair_v2 false'
        ];
    }
    return [
        'status' => 1,
        'message' => 'Done'
    ];
}
