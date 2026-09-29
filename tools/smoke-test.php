<?php

require_once __DIR__ . '/../database/concursoswrapper.php';
require_once __DIR__ . '/../database/concursowrapper.php';
require_once __DIR__ . '/../database/resultadojson.php';

$failures = 0;

function checkCondition($condition, $message)
{
    global $failures;
    if ($condition) {
        echo "[OK] $message" . PHP_EOL;
    } else {
        $failures++;
        echo "[ERROR] $message" . PHP_EOL;
    }
}

foreach (['simplexml', 'mbstring', 'bcmath', 'json'] as $extension) {
    checkCondition(extension_loaded($extension), "Extensión PHP disponible: $extension");
}

try {
    $catalog = CConcursosWrapper::getInstance('config.xml');
    $contest = $catalog->getConcursoById(1);
    checkCondition($contest !== null, 'El catálogo permite consultar el concurso 1');
    checkCondition(trim((string)$contest->datos) !== '', 'El concurso 1 tiene XML de datos');

    $contestXml = CConcursoWrapper::getInstance((string)$contest->datos);
    $tests = $contestXml->GetPruebas();
    checkCondition(count($tests) > 0, 'El concurso 1 contiene pruebas');

    $result = CResultadoJson::load((string)$tests[0]->link);
    checkCondition(isset($result['resultados']['datos']), 'La primera prueba contiene resultados');
} catch (Throwable $exception) {
    $failures++;
    echo "[ERROR] " . $exception->getMessage() . PHP_EOL;
}

if ($failures > 0) {
    echo "Prueba de humo fallida: $failures error(es)." . PHP_EOL;
    exit(1);
}

echo "Prueba de humo superada." . PHP_EOL;
exit(0);
