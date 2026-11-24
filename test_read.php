<?php

$file = __DIR__ . '/app/Http/Controllers/ProductosController.php';
$lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
$show = array_slice($lines, 0, 12);
echo implode(PHP_EOL, $show) . PHP_EOL;
