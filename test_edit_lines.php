<?php

$file = __DIR__ . '/resources/views/admin/productos/edit.blade.php';
$lines = file($file, FILE_IGNORE_NEW_LINES);
echo 'Total lines: ' . count($lines) . PHP_EOL;
$start = 70;
$end = 105;
$linesOut = array_slice($lines, $start, $end - $start);
echo implode(PHP_EOL, $linesOut) . PHP_EOL;
