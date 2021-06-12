<?php

$url = '../' . $_GET['url'];
//$actual_link = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]" . '../' . $url;
//print_r($actual_link);

//echo '<pre>';
//print_r();
//echo '</pre>';
////print_r($url);
//die;

//header("Content-Type: application/force-download");
//header('Content-Type: application/pdf');
//header('Content-Disposition: inline; filename="' . realpath($url) . '"');
?>
<style>
    *{
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }
    iframe{
        width: 100vw;
        height: 100vh;
    }
</style>
<iframe src="<?=$url?>" frameborder="0" ></iframe>
