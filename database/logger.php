<?php

require_once __DIR__ . '/projectpath.php';

function log_application_event($level, $message, array $context = [])
{
    $record = [
        'timestamp' => date(DATE_ATOM),
        'level' => $level,
        'message' => $message,
        'context' => $context,
    ];

    $encoded = json_encode($record, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    $logRoot = CProjectPath::logRoot();
    $date = date('Y-m-d');
    $generalLog = $logRoot . DIRECTORY_SEPARATOR . 'application-' . $date . '.log';
    $errorLog = $logRoot . DIRECTORY_SEPARATOR . 'errors-' . $date . '.log';

    if (!is_dir($logRoot) && !mkdir($logRoot, 0750, true) && !is_dir($logRoot)) {
        error_log($encoded);
        return;
    }

    $written = file_put_contents($generalLog, $encoded . PHP_EOL, FILE_APPEND | LOCK_EX);
    if ($written === false) {
        error_log($encoded);
        return;
    }

    if (strtolower((string)$level) === 'error') {
        file_put_contents($errorLog, $encoded . PHP_EOL, FILE_APPEND | LOCK_EX);
    }
}
