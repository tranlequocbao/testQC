<?php

if(!isset($_SESSION)) header('Location: ./'); session_start(); 
if(!isset($_POST['submit'])){
    $_SESSION['notif'] = "First, please login!";
    header('Location: ./');
    return;
}

require_once "../vendor/PDOconnect/pdo/PdoConnect.php";

$db = new pdoRequest();
$db->setTable('admin_account');

$permision = ["LH", "RH", "ADMIN", 'SEALER'];

if(isset($_POST['typeLogin'])){
    $db->setTable('account_qr_code');
    $usercode = $_POST['usercode'];
    $result = $db->one(['usercode', $usercode]);
    $error = false;
    $message = 'Has an error!';
    if(!empty($result)){
        $db->setTable('admin_account');
        $position = ($db->one(['id', $result['position']])) ?? [];

        if(empty($position)){
            $error = true;
            $message = 'Position this qr code not found!';
        }else{
            $_SESSION['logined']['fullname'] = $result['fullname'];
            $_SESSION['logined']['usercode'] = $usercode;
            $_SESSION['logined']['username'] = strtoupper($position['username']);
            $_SESSION['logined']['position'] = strtoupper($position['position']);
            $_SESSION['logined']['location'] = $result['location'];
            $_SESSION['logined']['isCreateError'] = $result['permision_create_error'] ?? 1;
            $_SESSION['logined']['status'] = 'active';
            $_SESSION['logined']['split'] = strtoupper($result['split_sealer']) ?? 0;
            if(!in_array($position['position'], $permision)){
                $_SESSION['logined']['position'] = 'ADMIN';
                $_SESSION['logined']['user_check'] = $position['position'];
            }
            if(strtoupper($position['position']) == 'EXPORT'){
                header("Location: ../export");
                return;
            }
            if(strtoupper($position['position']) == 'ADMIN'){
                $_SESSION['logined']['user_check'] = 'EXPORT';
                $_SESSION['logined']['allowAdmin'] = '1';
                header('Location: ../admin');
                return;
            }
            header('Location: ../');
            return;
        }
    }else{
        $error = true;
        $message = 'Usercode not exists!';
    }
    if($error){
        $_SESSION['error'] = $message;
        header('Location: ./');
        return;
    }
}

$username = $_POST['username'];
$password = $_POST['password'];

$result = $db->one([['username', $username], ['password', sha1($password)]]);
if(!empty($result)){
    //get permision
    $_SESSION['logined']['username'] = $username;
    $_SESSION['logined']['position'] = $result['position'];
    if($result['change_password'] == '0'){
        $_SESSION['change_password'] = true;
        header('Location: ./');
        return;
    }
    $_SESSION['logined']['status'] = 'active';
    if(!in_array($result['position'], $permision)){
        $_SESSION['logined']['position'] = 'ADMIN';
        $_SESSION['logined']['user_check'] = $result['position'];
    }
    if(strtoupper($result['position']) == 'EXPORT'){
        header("Location: ../export");
        return;
    }
    header('Location: ../');
    return;
}
else{
    $_SESSION['old']['username'] = $username;
    $_SESSION['error'] = 'Username or Password error!';
    header('Location: ./');
    return;
}