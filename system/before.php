<?php defined('FRTCFTYU') or die('No direct script access.');

/*
 * Поиск класса в директории
 * @param $class_name Имя класса
 * @param $dir Директория, в которой надо искать
 * @return Польный путь до файла или null, если файл не был найден
 *
*/
function class_for_dir($class_name, $dir)
{
    $arrpath = explode('_', $class_name);
    if(count($arrpath) > 1) {
       foreach($arrpath as $item) {
         $dir .=  DIRECTORY_SEPARATOR . $item;
       }
       $dir .= EXT;
       if(file_exists($dir)) {
           return $dir;
       }
    }
    return null;
}

/*
 * Autoload system classes function 
 *
*/
function sys_class_autoload($class_name) 
{
    if(preg_match('/[_]+/', $class_name)) {
        $file = class_for_dir($class_name, SYSTEM . 'classes');
        if( ! $file ) {
            return false;
        }
    }
    else {
        $file = SYSTEM . 'classes' . DIRECTORY_SEPARATOR . $class_name. EXT;
        if( file_exists($file) == false ) {
            return false;
        }
    }
    
    require_once ($file);
}

/*
 * Autoload application classes function 
 *
*/
function app_class_autoload($class_name) 
{
    if(preg_match('/[_]+/', $class_name)) {
        $file = class_for_dir($class_name, APPPATH . 'classes');
        if( ! $file ) {
            return false;
        }
    }
    else {
        $file = APPPATH . 'classes' . DIRECTORY_SEPARATOR . $class_name. EXT;
        if( file_exists($file) == false ) {
            return false;
        }
    }
    
    require_once ($file);
}

/*
 * Autoload application function 
 *
*/
function application_autoload($class_name) 
{
    if(preg_match('/[_]+/', $class_name)) {
        $file = class_for_dir($class_name, APPPATH);
        if( ! $file ) {
            return false;
        }
    }
    else {
        $file = APPPATH . DIRECTORY_SEPARATOR . $class_name. EXT;
        if( file_exists($file) == false ) {
            return false;
        }
    }
    
    require_once ($file);

}

/*
 * Autoload modules function 
 *
*/
function modules_autoload($class_name) 
{
    foreach (scandir(MODPATH) as $module) {
        if(preg_match('/[_]+/', $class_name)) {
            $file = class_for_dir($class_name, MODPATH . $module);
            if( ! $file ) {
                continue;
            }
        }
        else {
            $file = MODPATH . $module . DIRECTORY_SEPARATOR . $class_name. EXT;
            if( file_exists($file) == false ) {
                continue;
            }
        }
        require_once ($file);
        break;        
    }
}

/*
 * Register autoload functions 
 *
*/
spl_autoload_register('sys_class_autoload');
spl_autoload_register('app_class_autoload');
spl_autoload_register('application_autoload');
spl_autoload_register('modules_autoload');




