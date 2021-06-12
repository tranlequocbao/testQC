
<?php
if(!isset($_SESSION)) session_start();
if(!isset($_POST)){
    echo json_encode([
        'code' => 401,
        'message' => 'You haven\'t permision!'
    ]);
    return true;
}
require_once "../vendor/PDOconnect/pdo/PdoConnect.php";
$db = new pdoRequest();
$checking = $_SESSION['logined']['position'] == 'SEALER' ? 'sealer_checking' : 'checking';
$user   = $_POST['user'] != '' ? $_POST['user'] : $_SESSION['logined']['position'];
$id     = $_POST['err_id'];
$checking_error = $user == 'SEALER' ? 'history_err_sealer' : 'history_err';

$db->setTable($checking_error);
$select = $db->get(['err_id', $id], null, ['created_at' => "ASC"]);
$db->setTable($checking);
$type = $db->one(['err_id', $select[0]['err_id']]);
echo json_encode([
    'code' => 200,
    'data' => $select,
    'type' => $type
]);
return true;
?>