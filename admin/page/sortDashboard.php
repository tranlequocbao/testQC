<?php

require_once __DIR__ . '/headAjax.php';
$db->setTable('checking');

$params = $_POST['options'];

$startTime = $params['start'] ?? '';
$endTime = $params['end'] ?? '';

if($startTime == '' || $endTime == ''){
    echo json_encode([
        'code' => 200,
        'html' => '<h2 class="text-center">Data sort error</h2>'
    ]);
    return true;
}

$timeS = strtotime($startTime);
$timeE = strtotime('+1 days', strtotime($endTime));
$timeS = date('Y-m-d',$timeS);
$timeE = date('Y-m-d',$timeE);
$datas = $db->get(['created_at', 'BETWEEN', [$timeS,$timeE]]);

$datas = _require($datas, $db);

ob_start();
require_once __DIR__ . '/table_data_dashboard.php';
$content = ob_get_contents();
ob_end_clean();

echo json_encode([
    'code' => 200,
    'html' => $content
]);
return true;
