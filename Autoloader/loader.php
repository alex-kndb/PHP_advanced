<?php

spl_autoload_register(function($class){
    $class = 'src\Profiles\Class_User';
    $file = str_replace('\\', DIRECTORY_SEPARATOR,$class).'.php';
    file_exists($file) ? include $file : die("Класс $class не найден!");

// здесь сделал замену в имени класса '_' на '\'
// но не понял как это вообще внедрить в проект
//
//    $arr = explode('\\', $class);
//    if (strpos($arr[count($arr)-1], '_') !== false){
//        $last = array_pop($arr);
//        $arr[] = str_ireplace('_','\\',$last);
//    }
//    $str = implode(DIRECTORY_SEPARATOR, $arr);
});
