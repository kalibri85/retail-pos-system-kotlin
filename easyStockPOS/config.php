<?php
session_start();
//Base path
define('BASE_PATH', __DIR__);
//Load classes automatically
spl_autoload_register(function($class){
    $path = __DIR__.'/classes/'.$class.'.php';
    if(file_exists($path)){
        require_once $path;
    }
});
?>