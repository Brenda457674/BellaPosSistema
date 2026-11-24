<?php

require __DIR__ . '/vendor/autoload.php';

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;

$app = Application::configure(basePath: __DIR__)
    ->withRouting(
        web: __DIR__ . '/routes/web.php',
    )
    ->create();

$router = $app->make('router');
$routes = $router->getRoutes();
foreach ($routes as $name => $route) {
    $action = $route->getAction();
    $controller = $action['controller'] ?? null;
    $uri = $route->uri();
    $methods = implode('|', $route->methods());
    echo $methods, ' ', $uri, ' => ', $controller, ' | name: ', $name, PHP_EOL;
}
