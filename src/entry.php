<?php

namespace App;
if (!defined('FRAMEWORK')) die;

/**
 * Реализация PSR-4, приведенная в примере стандарта.
 */
spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $base_dir = __DIR__ . '/App/';
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }
    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
    if (file_exists($file)) {
        require $file;
    }
});

require_once __DIR__ . '/config.php';
$app = new App();
require_once __DIR__ . '/app-globals.php';
$app->run();

