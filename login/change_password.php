<?php
if(!isset($_SESSION)) session_start();
if(!isset($_POST['submit']) || !isset($_SESSION['logined'])){
    $_SESSION['notif'] = "First, please login!";
    header('Location: ./');
    return;
}

require_once "../vendor/PDOconnect/pdo/PdoConnect.php";

$db = new pdoRequest();
$db->setTable('admin_account');

$old_password = $_POST['old_username'];
$new_password = $_POST['new_password'];
$re_new_password = $_POST['re_new_password'];

if($new_password != $re_new_password){
    $_SESSION['error'] = 'Password not match!';
    header('Location: ./');
    return;
}

$result = $db->one([['username', $_SESSION['logined']['username']], ['password', sha1($old_password)]]);

if(!empty($result)){
    $result = $db->update(['change_password' => 1, 'password' => sha1($new_password)], ['id', $result['id']], true);
    if($result){
        unset($_SESSION['change_password']);
        $_SESSION['logined']['status'] = 'active';
        header('Location: ../');
        return;
    }
    $_SESSION['error'] = $result;
    header('Location: ./');
    return;
}
else{
    $_SESSION['error'] = 'Old password not match!';
    header('Location: ./');
    return;
}