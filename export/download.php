<?php

$url = '../' . $_GET['url'];
$nameFile = explode('/',$url);
$nameFile = $nameFile[count($nameFile) - 1];
//$actual_link = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]" . '../' . $url;
//print_r($actual_link);

header("Content-Type: application/force-download");
header('Content-Type: application/pdf');
header('Content-Disposition: attachment; filename="' . $nameFile . '"');
header("Content-Type: application/octet-stream");
header("Content-Length: " . filesize(realpath($url)));
readfile(realpath($url));