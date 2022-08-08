<?php

const NAME_SPASE = 'LksKndb/Php2';
const DS = '/';

spl_autoload_register(function($class){
    $fileName = str_ireplace(['\\', NAME_SPASE], [DS, 'src'], $class);
    // 1. Ищем '_' именно в названии класса, а не во всем namespace.
    // 2. Не понятно, зачем нужно из Class_name.php делать Class/Name.php,
    //    если папок Class нет все-равно?
    // 3. Почему-то не работает DIRECTORY_SEPARATOR внутри str_replace.
    $arr = explode(DS, $fileName);
    if (strpos($arr[count($arr)-1], '_') !== false){
        $last = array_pop($arr);
        $arr[] = str_replace('_',DS,$last);
    }
    $file = implode(DS, $arr).'.php';
//    echo $file.PHP_EOL;
    file_exists($file) ? include $file : die("Класс $class не найден!");
});
