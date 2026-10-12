<?php

spl_autoload_register(function ($class) {
    $prefix  = 'App\\';
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

use App\Repositories\LibraryRepository;
use App\Services\LibraryService;
use App\UI\ConsoleApp;

try {
    $repository = new LibraryRepository();
    $service    = new LibraryService($repository);
    $app        = new ConsoleApp($service);
    $app->run();
} catch (Throwable $e) {
    echo "Fatal error: " . $e->getMessage() . "\n";
}