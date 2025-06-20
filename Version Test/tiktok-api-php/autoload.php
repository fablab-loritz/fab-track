<?php
// tiktok-api-php/autoload.php

spl_autoload_register(function ($class) {
    $prefix = 'TikTokApi\\';
    // Pointage vers le dossier "src" où se trouvent tes fichiers PHP
    $base_dir = __DIR__ . '/src/';
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