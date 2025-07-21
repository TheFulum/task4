<?php

spl_autoload_register(function ($className) {
    $filePath = str_replace('\\', DIRECTORY_SEPARATOR, $className) . '.php';
    
    $directories = [
        $_SERVER['DOCUMENT_ROOT'] . '/core/',
        $_SERVER['DOCUMENT_ROOT'] . '/models/',
        $_SERVER['DOCUMENT_ROOT'] . '/controllers/'
    ];
    
    foreach ($directories as $directory) {
        $fullPath = $directory . $filePath;
        if (file_exists($fullPath)) {
            require_once $fullPath;
            return;
        }
    }
    
    throw new Exception("Класс {$className} не найден");
});