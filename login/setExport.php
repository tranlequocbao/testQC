<?php

if(!isset($_SESSION)) session_start();
if(!isset($_SESSION['logined'])){
    $_SESSION['notif'] = "First, please login!";
    header('Location: ./');
    return;
}
if($_GET['type'] == 'in'){
    $_SESSION['old_user_position'] = $_SESSION['logined']['position'];
    $_SESSION['logined']['position'] = 'ADMIN';
    $_SESSION['type_export'] = $_GET['exp'];
    header('Location: ../?code=' . $_GET['code'] ?? '');
}else if($_GET['type'] == 'out'){
    if(isset($_SESSION['old_user_position'])){
        $_SESSION['logined']['position'] = $_SESSION['old_user_position'];
        unset($_SESSION['old_user_position']);
        unset($_SESSION['type_export']);
        header('Location: ../');
    }else{
        header('Location: ./logout.php');
    }
}