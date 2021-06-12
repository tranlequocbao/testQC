<?php

class Logger{
    private $namFile = '';
    public function __construct(){
        date_default_timezone_set('Asia/Ho_Chi_Minh');
        $this->namFile = date('d_m_Y') . '.txt';
    }
    public function write($string){
        $d = DIRECTORY_SEPARATOR;
        $preString = date("H:m:i") . ' ';
        $file = fopen(__DIR__ . $d . '..' . $d . 'log' . $d . $this->namFile, 'a+');
        fwrite($file, PHP_EOL);
        fwrite($file, $preString . $string);
        fclose($file);
        return true;
    }
}