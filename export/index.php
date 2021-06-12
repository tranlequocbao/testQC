<?php

    error_reporting(E_ALL);
    ini_set('display_errors', TRUE);
    ini_set('display_startup_errors', TRUE);

    if(!isset($_SESSION)) session_start();

	if(
	        !isset($_SESSION['logined'])
        ||  !isset($_SESSION['logined']['status'])
        ||  $_SESSION['logined']['status'] != 'active'
        ||  $_SESSION['logined']['user_check'] != 'EXPORT'
    ){
        $_SESSION['error'] = 'Chưa đăng nhập hoặc tài khoản không được quyền truy cập!';
		header("Location: ../login/logout.php");
		return;
    }
    
	if($_SESSION['logined']['status'] == 'change_password'){
        header("Location: ./login/change_password.php");
        return;
    }

    if(strpos($_SERVER['HTTP_HOST'], 'localhost') == 0){
        $uri = explode('/', $_SERVER['REQUEST_URI']);
        $home_folder = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . '/' . $uri[1] . '/';
        echo '<script> var localhost = true;</script>';
    }else{
        $home_folder = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . '/';
    }

    function getName($arrs){
        foreach ($arrs as $ikey => $arr) {
            $temp = $arr['image'];
            $temp = explode(".",$temp);
            $arrs[$ikey]['_name'] = $temp[0];
        }
        return $arrs;
    }

    function _json_replace_new_line($arrs, $column){
        foreach ($arrs as $item => $arr){
            $arrs[$item][$column] = preg_replace('/\r|\n/','\n',trim($arr[$column]));
        }
        return $arrs;
    }

    function recursiveSearch($folder, $pattern) {
        $dir = new RecursiveDirectoryIterator($folder);
        $ite = new RecursiveIteratorIterator($dir);
        $files = new RegexIterator($ite, $pattern, RegexIterator::GET_MATCH);
        $fileList = array();
        foreach($files as $file) {
            if(!is_null($file[0])){
                $exp = explode('\\',$file[0]);
                $fileList[] = array(
                    'image' => end($exp)
                );
            }
        }
        return $fileList;
    }

    $userCode = $_GET['code_user'] ?? null;

    echo "<script>var _prePath = '../';</script>";

    if(isset($_GET['name'])){
        $url = $_GET['name'];
        echo "
            <script>
                var href = window.location.href.split('?');
                setTimeout(function() {
                    window.open(href[0] + 'view.php?url=" . $url . "')
                    window.open(href[0] + 'download.php?url=" . $url . "')
                },300)
                setTimeout(function() {
                    window.location.href = href[0];
                },300)
            </script>
        ";
        die;
    }

    include "./home.php";

?>