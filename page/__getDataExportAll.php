<?php

include './_header.php';

function deleteByCode($arrs, $code){
    return array_filter($arrs, function($item) use($code){
        return $item['error_code'] != $code;
    });
}

function uniqueData(&$arrs){
    foreach ($arrs as $item){
        if($item['err_level'] == '2' || $item['err_level'] == '1'){
            $arrs = deleteByCode($arrs, $item['error_code']);
            uniqueData($arrs);
            break;
        }
    }
}

$timeStart = $_POST['start'] . ' 00:00:00';
$timeEnd = $_POST['end'] . ' 23:59:59';

$sql = 'SELECT error_code, err_level FROM checking WHERE updated_at BETWEEN "' . $timeStart .'" AND "'. $timeEnd . '" AND error_code like "RN2KW5726LM033679"';
$result = $db->_exec($sql);
//echo $sql;
//uniqueData($result);

$result = array_map(function($item){
    return $item['error_code'];
}, $result);

$result = array_values(array_unique($result));

$_SESSION['___DATA__EXPORT___'] = $result;
echo json_encode([
    'code' => 200,
    'message' => 'Done',
    'data' => $result
]);
return true;