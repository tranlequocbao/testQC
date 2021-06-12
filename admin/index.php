<?php

error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);

$allowGet = true;
include '../page/_header.php';
$sl = $_SESSION['logined'] ?? [];

if(empty($sl) || !isset($sl['status'])){
    header("Location: ../login/logout.php");
    return;
}

if(!isset($sl['allowAdmin']) || $sl['allowAdmin'] != '1'){
    header('Location: ../login/logout.php');
    return;
}



if($sl['status'] == 'change_password'){
    header("Location: ../login/change_password.php");
    return;
}

if($sl['status'] != 'active'){
    header("Location: ../login/logout.php");
    return;
}

if(strpos($_SERVER['HTTP_HOST'], 'localhost') == 0){
    $uri = explode('/', $_SERVER['REQUEST_URI']);
    $home_folder = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . '/' . $uri[1] . '/';
    echo '<script> var localhost = true;</script>';
}else{
    $home_folder = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . '/';
}

include 'home.php';