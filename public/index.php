<?php

declare(strict_types=1);

use ReaDaLL\Controller\MemberController;
use ReaDaLL\Database;
use ReaDaLL\Repository\MysqlMemberRepository;

$config = require __DIR__ . '/../config.php';

spl_autoload_register(function (string $class): void {
    $prefix = 'ReaDaLL\\';
    if (!str_starts_with($class, $prefix)) {
        return;
    }

    $relative = str_replace('\\', '/', substr($class, strlen($prefix)));
    $file     = __DIR__ . '/../src/' . $relative . '.php';

    if (is_file($file)) {
        require $file;
    }
});

$pdo        = Database::connect($config['db']);
$repository = new MysqlMemberRepository($pdo);
$controller = new MemberController($repository, $config['base_url']);

$route = $_GET['route'] ?? 'members';

match ($route) {
    'members'        => $controller->index(),
    'members/create' => $controller->create(),
    default          => (function () {
        http_response_code(404);
        echo 'Not found';
    })(),
};