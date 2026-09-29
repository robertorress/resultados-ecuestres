<?php

require_once __DIR__ . '/configuracion.php';
require_once __DIR__ . '/database/security.php';
require_once __DIR__ . '/database/logger.php';

$routes = require __DIR__ . '/config/routes.php';

$routeName = strtolower((string)($_GET['c'] ?? 'concursos'));
$route = $routes[$routeName] ?? null;
$action = (string)($_GET['a'] ?? 'Index');

if ($route === null || !in_array($action, $route['actions'], true)) {
    http_response_code(404);
    echo 'Ruta no encontrada.';
    exit;
}

if ($routeName !== 'concursos' && request_positive_int('id') === null) {
    http_response_code(400);
    echo 'Parámetro id inválido.';
    exit;
}

try {
    require_once __DIR__ . '/' . $route['file'];
    $controller = new $route['class'];
    $controller->{$action}();
} catch (Throwable $exception) {
    log_application_event('error', 'Unhandled application exception', [
        'route' => $routeName,
        'action' => $action,
        'uri' => $_SERVER['REQUEST_URI'] ?? '',
        'exception' => get_class($exception),
        'message' => $exception->getMessage(),
    ]);
    http_response_code(500);
    if ($displayErrors) {
        echo '<pre>' . htmlspecialchars($exception->getMessage(), ENT_QUOTES, 'UTF-8') . '</pre>';
    } else {
        echo 'Se ha producido un error interno.';
    }
}
