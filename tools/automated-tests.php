<?php

require_once __DIR__ . '/../database/security.php';
require_once __DIR__ . '/../model/concursos.php';
require_once __DIR__ . '/../config/constants.php';

$failures = 0;
$routes = require __DIR__ . '/../config/routes.php';

function testCondition($condition, $message)
{
    global $failures;

    if ($condition) {
        echo "[OK] $message" . PHP_EOL;
        return;
    }

    $failures++;
    echo "[ERROR] $message" . PHP_EOL;
}

testCondition(
    escape_html('<script>alert(1)</script>') === '&lt;script&gt;alert(1)&lt;/script&gt;',
    'Escapa contenido HTML'
);
testCondition(format_score(12.3456) === '12.346', 'Formatea puntuaciones');
testCondition(format_score(12) === '12.000', 'Completa puntuaciones con tres decimales');
testCondition(youtube_video_id('https://youtu.be/dQw4w9WgXcQ') === 'dQw4w9WgXcQ', 'Acepta una URL de YouTube válida');
testCondition(youtube_video_id('https://example.com/video') === null, 'Rechaza una URL externa como vídeo');

$legacyRoutes = [
    ['c' => 'Concursos', 'a' => 'Index'],
    ['c' => 'Prueba', 'a' => 'MostrarPrueba'],
    ['c' => 'Resultado', 'a' => 'Resultado'],
    ['c' => 'ResultadoGeneral', 'a' => 'ResultadoGeneral'],
    ['c' => 'ResultadoIndividual', 'a' => 'ResultadoIndividual'],
    ['c' => 'OrdenSalida', 'a' => 'Listado'],
];
foreach ($legacyRoutes as $legacyRoute) {
    $route = $routes[strtolower($legacyRoute['c'])] ?? null;
    testCondition(
        $route !== null && in_array($legacyRoute['a'], $route['actions'], true),
        "Mantiene la ruta {$legacyRoute['c']}/{$legacyRoute['a']}"
    );
}

$previousId = $_GET['id'] ?? null;
$_GET['id'] = '15';
testCondition(request_positive_int('id') === 15, 'Acepta un identificador positivo');
$_GET['id'] = '0';
testCondition(request_positive_int('id') === null, 'Rechaza un identificador cero');
$_GET['id'] = 'abc';
testCondition(request_positive_int('id') === null, 'Rechaza un identificador no numérico');

if ($previousId === null) {
    unset($_GET['id']);
} else {
    $_GET['id'] = $previousId;
}

$concursos = new CConcursos();
$sorted = $concursos->GetConcursosByYearSorted(RESULTADOS_DEFAULT_YEAR);
testCondition(is_array($sorted) && count($sorted) > 0, 'Obtiene concursos ordenados');
testCondition(count($concursos->getDisplayConcursos(RESULTADOS_DEFAULT_YEAR)) > 0, 'Prepara concursos con el convenio nuevo');

if (count($sorted) > 1) {
    $firstDate = DateTime::createFromFormat('d/m/Y', (string)$sorted[0]->fecha);
    $secondDate = DateTime::createFromFormat('d/m/Y', (string)$sorted[1]->fecha);
    testCondition($firstDate >= $secondDate, 'Ordena concursos de fecha más reciente a más antigua');
}

$upcomingIds = $concursos->GetUpcomingConcursoIds($sorted);
testCondition(is_array($upcomingIds), 'Calcula identificadores de concursos pendientes');

if ($failures > 0) {
    echo "Pruebas automatizadas fallidas: $failures error(es)." . PHP_EOL;
    exit(1);
}

echo "Pruebas automatizadas superadas." . PHP_EOL;
exit(0);
