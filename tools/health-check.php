<?php

require_once __DIR__ . '/../database/projectpath.php';
require_once __DIR__ . '/../database/resultadojson.php';

$checks = [];
$checks['catalog'] = is_readable(CProjectPath::resolveCatalog());
$checks['data_root'] = is_dir(CProjectPath::dataRoot() . DIRECTORY_SEPARATOR . 'recursos');
$checks['php_extensions'] = extension_loaded('simplexml')
    && extension_loaded('json')
    && extension_loaded('mbstring')
    && extension_loaded('bcmath');

$healthy = !in_array(false, $checks, true);
$result = [
    'status' => $healthy ? 'ok' : 'error',
    'timestamp' => date(DATE_ATOM),
    'checks' => $checks,
];

echo json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . PHP_EOL;
exit($healthy ? 0 : 1);
