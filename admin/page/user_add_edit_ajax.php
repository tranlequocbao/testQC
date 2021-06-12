<?php

require_once '_header.php';
require_once '../../vendor/PDOconnect/pdo/PdoConnect.php';

$data = $_POST ?? [];

$db = new pdoRequest();
$db->setTable('account_qr_code');

//delete

if(isset($data['_type']) && $data['_type'] == 'del'){
    if(!$db->one(['id', $data['id']])){
        return response([
            'code' => 201,
            'message' => 'User code not found!'
        ]);
    }
    if(!$db->delete(['id', $data['id']])){
        return response([
            'code' => 203,
            'message' => 'Delete user error!'
        ]);
    }
    return response([
        'code' => 200,
        'message' => 'Delete user success!'
    ]);
}

$data = $data['data'] ?? [];

if(empty($data)){
    return response([
        'code' => 206,
        'message' => 'Data insert empty!'
    ]);
}

$aryInsert = formatValue($data);
if(!isset($aryInsert['permision_create_error'])){
    $aryInsert['permision_create_error'] = '0';
}else if($aryInsert['permision_create_error'] == 'on'){
    $aryInsert['permision_create_error'] = '1';
}else{
    $aryInsert['permision_create_error'] = '';
}
if(!isset($aryInsert['split_sealer'])){
    $aryInsert['split_sealer'] = '';
}

$hasUserCode = $db->one(['usercode', $aryInsert['usercode']]);

//edit

if(isset($aryInsert['_id'])){
    $id = $aryInsert['_id'];
    unset($aryInsert['_id']);
    unset($aryInsert['usercode']);
    if(!$hasUserCode){
        return response([
            'code' => 201,
            'message' => 'User code not found!'
        ]);
    }
    if(!$db->update($aryInsert, ['id', $id])){
        return response([
            'code' => 203,
            'message' => 'Update user error!'
        ]);
    }
    return response([
        'code' => 200,
        'message' => 'Update user success!'
    ]);
}

//insert

if($hasUserCode){
    return response([
        'code' => 201,
        'message' => 'User code has exists!'
    ]);
}

if(!$db->insert($aryInsert)){
    return response([
        'code' => 202,
        'message' => 'Insert data error!'
    ]);
}
return response([
    'code' => 200,
    'message' => 'Insert user success!'
]);