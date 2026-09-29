<?php

$environment = require __DIR__ . '/config/environment.php';
require_once __DIR__ . '/config/constants.php';
$displayErrors = $environment['display_errors'];

error_reporting(E_ALL);
ini_set('display_errors', $displayErrors ? '1' : '0');
ini_set('display_startup_errors', $displayErrors ? '1' : '0');
ini_set('log_errors', '1');
