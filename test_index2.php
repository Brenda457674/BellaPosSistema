<?php

$file = __DIR__ . '/resources/views/admin/productos/index.blade.php';
$lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
$show = array_slice($lines, 13, 18);
echo implode(PHP_EOL, $show) . PHP_EOL;
