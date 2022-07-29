<?php
require_once __DIR__ . '/config.inc.php';

spl_autoload_register(function ($class) {
    $prefix = 'gif_header\\';
    $base_dir = __DIR__ . '/classes/';
    $len = strlen($prefix);
    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.def.php';
    if (file_exists($file)) {
        require $file;
    }
});
