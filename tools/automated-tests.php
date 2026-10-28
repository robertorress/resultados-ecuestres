<?php

require_once __DIR__ . '/../database/security.php';
require_once __DIR__ . '/../model/resultadoequipos.php';
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

function writeFixtureFile($path, $content)
{
    $dir = dirname($path);
    if (!is_dir($dir)) {
        mkdir($dir, 0777, true);
    }

    file_put_contents($path, $content);
}

function deleteDirectoryRecursive($path)
{
    if (!is_dir($path)) {
        return;
    }

    $entries = scandir($path);
    foreach ($entries as $entry) {
        if ($entry === '.' || $entry === '..') {
            continue;
        }

        $fullPath = $path . DIRECTORY_SEPARATOR . $entry;
        if (is_dir($fullPath)) {
            deleteDirectoryRecursive($fullPath);
        } else {
            @unlink($fullPath);
        }
    }

    @rmdir($path);
}

function resetConcursoWrapperSingleton()
{
    $reflection = new ReflectionClass('CConcursoWrapper');
    $property = $reflection->getProperty('instance');
    $property->setAccessible(true);
    $property->setValue(null, null);
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
    ['c' => 'Prueba', 'a' => 'Plano'],
    ['c' => 'Resultado', 'a' => 'Resultado'],
    ['c' => 'ResultadoGeneral', 'a' => 'ResultadoGeneral'],
    ['c' => 'ResultadoEquipos', 'a' => 'ResultadoEquipos'],
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

$fixtureRoot = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'resultados-equipos-tests-' . uniqid('', true);

try {
    // Fixture principal de resultados por equipos.
    $concursoPath = $fixtureRoot . DIRECTORY_SEPARATOR . 'evento-ok' . DIRECTORY_SEPARATOR . 'concurso.xml';
    $equiposPath = $fixtureRoot . DIRECTORY_SEPARATOR . 'evento-ok' . DIRECTORY_SEPARATOR . 'equipos.xml';
    $resultado1Path = $fixtureRoot . DIRECTORY_SEPARATOR . 'resultados' . DIRECTORY_SEPARATOR . 'prueba-1.json';
    $resultado2Path = $fixtureRoot . DIRECTORY_SEPARATOR . 'resultados' . DIRECTORY_SEPARATOR . 'prueba-2.json';

    writeFixtureFile($concursoPath, "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n"
        . "<root>\n"
        . "  <concurso>Concurso de pruebas</concurso>\n"
        . "  <fecha>01/01/2026</fecha>\n"
        . "  <ubicacion><nombre>Centro de pruebas</nombre><visible>SI</visible></ubicacion>\n"
        . "          <anuncios><visible>SI</visible><anuncio><texto>Aviso de prueba</texto></anuncio></anuncios>
        <equipos>si</equipos>\n"
        . "  <pruebas>\n"
        . "    <prueba><id>1</id><link>{$resultado1Path}</link><titulo>PRUEBA 1</titulo></prueba>\n"
        . "    <prueba><id>2</id><link>{$resultado2Path}</link><titulo>PRUEBA 2</titulo></prueba>\n"
        . "  </pruebas>\n"
        . "</root>\n");

    writeFixtureFile($equiposPath, <<<'XML'
<?xml version="1.0" encoding="UTF-8"?>
<equipos>
  <equipo>
    <nombre>Equipo válido</nombre>
    <binomios>
      <binomio><jinete><licencia>J1</licencia><nombre>Ana Uno</nombre></jinete><caballo><licencia>C1</licencia><nombre>Caballo Uno</nombre></caballo></binomio>
      <binomio><jinete><licencia>J2</licencia><nombre>Berta Dos</nombre></jinete><caballo><licencia>C2</licencia><nombre>Caballo Dos</nombre></caballo></binomio>
      <binomio><jinete><licencia>J3</licencia><nombre>Carlos Tres</nombre></jinete><caballo><licencia>C3</licencia><nombre>Caballo Tres</nombre></caballo></binomio>
      <binomio><jinete><licencia>NO-EXISTE</licencia><nombre>Lucia Cuatro</nombre></jinete><caballo><licencia>C4</licencia><nombre>Caballo Cuatro</nombre></caballo></binomio>
    </binomios>
  </equipo>
  <equipo>
    <nombre>Equipo duplicado</nombre>
    <binomios>
      <binomio><jinete><licencia>J1</licencia><nombre>Ana Uno</nombre></jinete><caballo><licencia>C1</licencia><nombre>Caballo Uno</nombre></caballo></binomio>
      <binomio><jinete><licencia>J1</licencia><nombre>Ana Uno</nombre></jinete><caballo><licencia>C1</licencia><nombre>Caballo Uno</nombre></caballo></binomio>
      <binomio><jinete><licencia>J2</licencia><nombre>Berta Dos</nombre></jinete><caballo><licencia>C2</licencia><nombre>Caballo Dos</nombre></caballo></binomio>
    </binomios>
  </equipo>
  <equipo>
    <nombre>Equipo no encontrado</nombre>
    <binomios>
      <binomio><jinete><licencia>J1</licencia><nombre>Ana Uno</nombre></jinete><caballo><licencia>C1</licencia><nombre>Caballo Uno</nombre></caballo></binomio>
      <binomio><jinete><licencia>J2</licencia><nombre>Berta Dos</nombre></jinete><caballo><licencia>C2</licencia><nombre>Caballo Dos</nombre></caballo></binomio>
      <binomio><jinete><licencia>JX</licencia><nombre>Inexistente</nombre></jinete><caballo><licencia>CX</licencia><nombre>Caballo X</nombre></caballo></binomio>
    </binomios>
  </equipo>
  <equipo>
    <nombre>Equipo corto</nombre>
    <binomios>
      <binomio><jinete><licencia>J1</licencia><nombre>Ana Uno</nombre></jinete><caballo><licencia>C1</licencia><nombre>Caballo Uno</nombre></caballo></binomio>
      <binomio><jinete><licencia>J2</licencia><nombre>Berta Dos</nombre></jinete><caballo><licencia>C2</licencia><nombre>Caballo Dos</nombre></caballo></binomio>
    </binomios>
  </equipo>
</equipos>
XML);

    writeFixtureFile($resultado1Path, <<<'JSON'
{
  "concurso": {"potros": 0, "infantiles": 0, "siete_annos": 0},
  "resultados": {
    "cabecera": [],
    "datos": [
      {"puesto": 1, "jinete_licencia": "J1", "jinete_nombre": "D. Ana Uno", "caballo_licencia": "C1", "caballo_nombre": "Caballo Uno", "total": 75},
      {"puesto": 2, "jinete_licencia": "J2", "jinete_nombre": "Dña. Berta Dos", "caballo_licencia": "C2", "caballo_nombre": "Caballo Dos", "total": 80},
      {"puesto": 3, "jinete_licencia": "J3", "jinete_nombre": "Carlos Tres", "caballo_licencia": "C3", "caballo_nombre": "Caballo Tres", "total": 90},
      {"puesto": 4, "jinete_licencia": "J4", "jinete_nombre": "Dña. Lucia Cuatro", "caballo_licencia": "C4", "caballo_nombre": "Caballo Cuatro", "total": 70}
    ]
  }
}
JSON);

    writeFixtureFile($resultado2Path, <<<'JSON'
{
  "concurso": {"potros": 0, "infantiles": 0, "siete_annos": 0},
  "resultados": {
    "cabecera": [],
    "datos": [
      {"puesto": 1, "jinete_licencia": "J1", "jinete_nombre": "Dña. Ana Uno", "caballo_licencia": "C1", "caballo_nombre": "Caballo Uno", "total": 60},
      {"puesto": 2, "jinete_licencia": "J2", "jinete_nombre": "Berta Dos", "caballo_licencia": "C2", "caballo_nombre": "Caballo Dos", "total": 65}
    ]
  }
}
JSON);

    putenv('RESULTADOS_DATA_ROOT=' . $fixtureRoot);
    resetConcursoWrapperSingleton();
    $resultadoEquiposModel = new CResultadoEquipos(1, $concursoPath);
    $clasificacionEquipos = $resultadoEquiposModel->getClasificacionEquipos();

    testCondition($resultadoEquiposModel->GetUbicacionVisible() === true, 'Acepta SI como ubicación visible');
    testCondition($resultadoEquiposModel->GetAnunciosVisible() === true, 'Acepta SI como anuncios visibles');
    testCondition(count($clasificacionEquipos['clasificacion']) === 1, 'Clasifica solo equipos válidos');
    testCondition(count($clasificacionEquipos['invalidos']) === 3, 'Marca equipos inválidos por reglas y binomios no encontrados');

    $equipoValido = $clasificacionEquipos['clasificacion'][0] ?? null;
    testCondition($equipoValido !== null && $equipoValido['nombre'] === 'Equipo válido', 'Conserva equipo válido en el ranking');
    testCondition($equipoValido !== null && abs($equipoValido['puntos_totales'] - 245.0) < 0.0001, 'Suma solo las 3 mejores notas cuando hay 4 integrantes');

    if ($equipoValido !== null) {
        $miembros = $equipoValido['miembros'];
        testCondition(count($miembros) === 4, 'Muestra los 4 integrantes en el detalle del equipo');
        testCondition(($miembros[0]['sumado'] ?? false) === true, 'Ordena primero miembros sumados');
        testCondition(($miembros[3]['sumado'] ?? true) === false, 'Coloca al final el miembro no sumado');
        testCondition(($miembros[3]['puntos'] ?? 0) === 70.0, 'Marca como no sumado el integrante con menor puntuación');
    }

    $erroresInvalidos = '';
    foreach ($clasificacionEquipos['invalidos'] as $invalido) {
        $erroresInvalidos .= ' ' . implode(' ', $invalido['errores']);
    }
    testCondition(strpos($erroresInvalidos, 'binomio repetido') !== false, 'Detecta binomio repetido dentro del equipo');
    testCondition(strpos($erroresInvalidos, 'No se encuentra el binomio') !== false, 'Informa binomios no encontrados');
    testCondition(strpos($erroresInvalidos, 'debe tener 3 o 4 binomios') !== false, 'Informa tamaño inválido de equipo');

    // Fixture de estructura inválida de equipos.xml.
    $invalidConcursoPath = $fixtureRoot . DIRECTORY_SEPARATOR . 'evento-invalid-xml' . DIRECTORY_SEPARATOR . 'concurso.xml';
    $invalidEquiposPath = $fixtureRoot . DIRECTORY_SEPARATOR . 'evento-invalid-xml' . DIRECTORY_SEPARATOR . 'equipos.xml';
    writeFixtureFile($invalidConcursoPath, "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n"
        . "<root>\n"
        . "  <concurso>Concurso inválido</concurso>\n"
        . "  <fecha>01/01/2026</fecha>\n"
        . "  <equipos>si</equipos>\n"
        . "  <pruebas>\n"
        . "    <prueba><id>1</id><link>{$resultado1Path}</link><titulo>PRUEBA 1</titulo></prueba>\n"
        . "  </pruebas>\n"
        . "</root>\n");

    writeFixtureFile($invalidEquiposPath, <<<'XML'
<?xml version="1.0" encoding="UTF-8"?>
<equipos>
  <equipo>
    <nombre>Sin binomios</nombre>
  </equipo>
</equipos>
XML);

    $invalidStructureDetected = false;
    resetConcursoWrapperSingleton();
    try {
        $invalidModel = new CResultadoEquipos(2, $invalidConcursoPath);
        $invalidModel->getClasificacionEquipos();
    } catch (Exception $exception) {
        $invalidStructureDetected = strpos($exception->getMessage(), 'Estructura inválida en equipos.xml') !== false;
    }

    testCondition($invalidStructureDetected, 'Valida la estructura mínima de equipos.xml');

    $anunciosPath = $fixtureRoot . DIRECTORY_SEPARATOR . 'evento-anuncios' . DIRECTORY_SEPARATOR . 'concurso.xml';
    writeFixtureFile($anunciosPath, <<<'XML'
<?xml version="1.0" encoding="UTF-8"?>
<root>
  <concurso>Concurso con anuncios</concurso>
  <fecha>01/01/2026</fecha>
  <anuncios>
    <anuncio>Aviso sin enlace</anuncio>
    <anuncio>
      <titulo>Aviso importante</titulo>
      <texto>Aviso con enlace</texto>
      <fecha>25/10/2026</fecha>
      <tipo>importante</tipo>
      <link><texto>ABRIR AVISO</texto><url>https://example.com/aviso</url></link>
    </anuncio>
  </anuncios>
</root>
XML);

    resetConcursoWrapperSingleton();
    $anunciosModel = new CResultadoEquipos(3, $anunciosPath);
    $anuncios = $anunciosModel->GetAnuncios();
    testCondition($anuncios !== null && count($anuncios) === 2, 'Lee todos los anuncios definidos');
    testCondition($anuncios !== null && trim((string)$anuncios[0]) === 'Aviso sin enlace', 'Lee el texto directo de un anuncio');
    testCondition(
        $anuncios !== null
        && trim((string)$anuncios[1]->titulo) === 'Aviso importante'
        && trim((string)$anuncios[1]->texto) === 'Aviso con enlace'
        && trim((string)$anuncios[1]->fecha) === '25/10/2026'
        && trim((string)$anuncios[1]->tipo) === 'importante'
        && trim((string)$anuncios[1]->link->texto) === 'ABRIR AVISO'
        && trim((string)$anuncios[1]->link->url) === 'https://example.com/aviso',
        'Lee el texto y enlace opcional de un anuncio'
    );

    resetConcursoWrapperSingleton();
    $sinAnunciosModel = new CResultadoEquipos(4, $concursoPath);
    testCondition($sinAnunciosModel->GetAnuncios() === null, 'No crea anuncios cuando falta el bloque anuncios');
} finally {
    deleteDirectoryRecursive($fixtureRoot);
    putenv('RESULTADOS_DATA_ROOT');
}

if ($failures > 0) {
    echo "Pruebas automatizadas fallidas: $failures error(es)." . PHP_EOL;
    exit(1);
}

echo "Pruebas automatizadas superadas." . PHP_EOL;
exit(0);
