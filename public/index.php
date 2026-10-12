<?php

spl_autoload_register(function ($class) {
    $prefix  = 'ReaDall\\';
    $baseDir = __DIR__ . '/../src/';

    if (strncmp($prefix, $class, strlen($prefix)) !== 0) {
        return;
    }

    $relative = substr($class, strlen($prefix));
    $file = $baseDir . str_replace('\\', '/', $relative) . '.php';

    if (file_exists($file)) {
        require $file;
    }
});

use ReadaLL\Repository\LibraryRepository;
use ReadaLL\Services\LibraryService;
use ReadaLL\front\ConsoleReaDaLL;

try {
    $repository = new LibraryRepository();
    $service    = new LibraryService($repository);
    $app        = new ConsoleReaDaLL($service);
    $app->run();
} catch (Throwable $e) {
    echo "Fatal error: " . $e->getMessage() . "\n";
}