<?php

    error_reporting(E_ALL);
    ini_set('display_errors', TRUE);
    ini_set('display_startup_errors', TRUE);

	if(!isset($_SESSION)) session_start();
	if(!isset($_SESSION['logined']) || !isset($_SESSION['logined']['status'])){
		header("Location: ./login/logout.php");
		return;
    }
    
	if($_SESSION['logined']['status'] == 'change_password'){
        header("Location: ./login/change_password.php");
        return;
    }

    if($_SESSION['logined']['status'] != 'active'){
        header("Location: ./login/logout.php");
        return;
    }

    if(strpos($_SERVER['HTTP_HOST'], 'localhost') == 0){
        $uri = explode('/', $_SERVER['REQUEST_URI']);
        $home_folder = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . '/' . $uri[1] . '/';
        echo '<script> var localhost = true;</script>';
    }else{
        $home_folder = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . '/';
    }

    $code = $_GET['code'] ?? null;
    $allowExport = false;

    echo "<script>var _prePath = './';</script>";

    if(
        isset($_SESSION['logined']['user_check'])
        && $_SESSION['logined']['user_check'] == 'check_repair'
        && isset($_SESSION['___DATA__EXPORT___'])
        && isset($_GET['all'])
        && $_GET['all'] == '1'
        && !isset($_GET['code'])
    ){
        if(empty($_SESSION['___DATA__EXPORT___'])){
            unset($_SESSION['___DATA__EXPORT___']);
            $allowExport = false;
            echo "<script>alert('Done');</script>";
            die;
        }
        $code = array_splice($_SESSION['___DATA__EXPORT___'], 0, 1);
        header('Location: ./?code=' . $code[0] . '&all=1');
        return true;
    }

    if(is_null($code)){
        include "./page/home.php";
    }else if(in_array(strtoupper($_SESSION['logined']['position']), ['ADMIN'])){
        include "./page/all.php";
    }

?>