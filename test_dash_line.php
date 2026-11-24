<?php

$file = __DIR__ . '/resources/views/admin/dashboard.blade.php';
$lines = file($file, FILE_IGNORE_NEW_LINES);
$show = $lines[663..670]; // líneas 664-671 (índices 663 a 670)
echo implode(PHP_EOL, $show) . PHP_EOL;