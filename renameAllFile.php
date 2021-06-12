<?php

//function convert_vi_to_en($str) {
//    $str = preg_replace("/(à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ)/", "a", $str);
//    $str = preg_replace("/(è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ)/", "e", $str);
//    $str = preg_replace("/(ì|í|ị|ỉ|ĩ)/", "i", $str);
//    $str = preg_replace("/(ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ)/", "o", $str);
//    $str = preg_replace("/(ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ)/", "u", $str);
//    $str = preg_replace("/(ỳ|ý|ỵ|ỷ|ỹ)/", "y", $str);
//    $str = preg_replace("/(đ)/", "d", $str);
//    $str = preg_replace("/(À|Á|Ạ|Ả|Ã|Â|Ầ|Ấ|Ậ|Ẩ|Ẫ|Ă|Ằ|Ắ|Ặ|Ẳ|Ẵ)/", "A", $str);
//    $str = preg_replace("/(È|É|Ẹ|Ẻ|Ẽ|Ê|Ề|Ế|Ệ|Ể|Ễ)/", "E", $str);
//    $str = preg_replace("/(Ì|Í|Ị|Ỉ|Ĩ)/", "I", $str);
//    $str = preg_replace("/(Ò|Ó|Ọ|Ỏ|Õ|Ô|Ồ|Ố|Ộ|Ổ|Ỗ|Ơ|Ờ|Ớ|Ợ|Ở|Ỡ)/", "O", $str);
//    $str = preg_replace("/(Ù|Ú|Ụ|Ủ|Ũ|Ư|Ừ|Ứ|Ự|Ử|Ữ)/", "U", $str);
//    $str = preg_replace("/(Ỳ|Ý|Ỵ|Ỷ|Ỹ)/", "Y", $str);
//    $str = preg_replace("/(Đ)/", "D", $str);
//    //$str = str_replace(" ", "-", str_replace("&*#39;","",$str));
//    return $str;
//}
//
//$path = realpath('./assets/images/');
//$dirs = array_filter(glob($path. DIRECTORY_SEPARATOR . '*'), 'is_dir');
//$dirs = array_filter($dirs,function($item){
//    return !preg_match('/(Stamp)/', $item);
//});
//foreach ($dirs as $foler){
//    $fls = array_filter(glob($foler. DIRECTORY_SEPARATOR . '*'), 'is_dir');
//    $fls = array_filter($fls,function($item){
//        return preg_match('/(SEALER)/', $item);
//    });
//    foreach ($fls as $fl){
//        if ($handle = opendir($fl)) {
//            while (false !== ($fileName = readdir($handle))) {
//                if($fileName == '.' || $fileName == '..') continue;
//
//                $newName = explode('.',$fileName);
//                $ext = end($newName);
//                array_pop($newName);
//                $newName = implode('.',$newName);
//                $newName = str_replace(' ', '', $newName);
//                $newName = str_replace('_', '', $newName);
//                $newName = str_replace('.', '-', $newName);
//                $newName = convert_vi_to_en($newName);
//                rename($fl . DIRECTORY_SEPARATOR . $fileName, $fl . DIRECTORY_SEPARATOR . $newName . '.' . $ext);
//            }
//            closedir($handle);
//        }
//    }
//}
?>

<?php

$fls = array_filter(glob(realpath('./assets/images/J36A_HB'). DIRECTORY_SEPARATOR . '*'), 'is_dir');

foreach ($fls as $fl){
    if ($handle = opendir($fl)) {
        while (false !== ($fileName = readdir($handle))) {
            if($fileName == '.' || $fileName == '..') continue;
            print_r('"' . explode('.',$fileName)[0] . '" => "L",');
            echo '<br>';
        }
        closedir($handle);
    }
    echo '--------<br>';
}
