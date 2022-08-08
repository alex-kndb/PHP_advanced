<?php

const PATH = 'src'.DIRECTORY_SEPARATOR;

spl_autoload_register(function($class){
    $class = str_replace('\\', DIRECTORY_SEPARATOR, $class);
    // ищем '_' именно в названии класса, а не во всем namespace
    $arr = explode('\\', $class);
    if (strpos($arr[count($arr)-1], '_') !== false){
        $last = array_pop($arr);
        $arr[] = str_ireplace('_','\\',$last);
    }
    $file = PATH.implode(DIRECTORY_SEPARATOR, $arr).'.php';
    file_exists($file) ? include $file : die("Класс $class не найден!");
});
