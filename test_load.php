<?php

require __DIR__ . '/vendor/autoload.php';

$class = 'App\Http\Controllers\ProductosController';

if (class_exists($class, true)) {
    echo 'Class exists: ' . $class . PHP_EOL;
} else {
    echo 'Class not found' . PHP_EOL;
}

try {
    $ref = new ReflectionClass($class);
    echo 'Reflection OK: ' . $ref->getName() . PHP_EOL;
    echo 'File: ' . $ref->getFileName() . PHP_EOL;
} catch (Throwable $e) {
    echo 'Reflection error: ' . $e->getMessage() . PHP_EOL;
}
