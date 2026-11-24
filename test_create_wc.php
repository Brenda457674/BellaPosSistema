<?php

$file = __DIR__ . '/resources/views/admin/productos/create.blade.php';
$lines = file($file, FILE_IGNORE_NEW_LINES);
echo 'Total lines: ' . count($lines) . PHP_EOL;
for ($i = count($lines) - 35; $i < count($lines); $i++) {
    echo ($i + 1) . ': ' . $lines[$i] . PHP_EOL;
}
