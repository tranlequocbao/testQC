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

    include "./page/history.php";
