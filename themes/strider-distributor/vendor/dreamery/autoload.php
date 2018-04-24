<?php
/**
 * @param string $class The fully-qualified class name.
 * @return void
 */
spl_autoload_register(function($class) {

    $prefix = 'Dreamery\\';
    $base_dir = __DIR__ . '/src/';
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        // no, move to the next registered autoloader
        return;
    }

    $relative_class = substr($class, $len);

    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});