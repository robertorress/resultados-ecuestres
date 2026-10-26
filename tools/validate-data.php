<?php

require_once __DIR__ . '/../database/projectpath.php';
require_once __DIR__ . '/../database/resultadojson.php';

$root = dirname(__DIR__);
$errors = 0;
$checkedJson = 0;
$checkedContests = 0;
$contestIds = [];

function reportError($message)
{
    global $errors;
    $errors++;
    echo "[ERROR] $message" . PHP_EOL;
}

function reportOk($message)
{
    echo "[OK] $message" . PHP_EOL;
}

function requireNode(SimpleXMLElement $xml, $path, $file)
{
    $nodes = $xml->xpath($path);
    if (!$nodes) {
        reportError("$file: falta el nodo $path");
        return false;
    }

    return true;
}

function validateContestXml($relativePath)
{
    global $checkedContests;

    try {
        $path = CProjectPath::resolveData($relativePath);
        if (!is_file($path) || !is_readable($path)) {
            reportError("No existe el XML del concurso: $relativePath");
            return;
        }

        libxml_use_internal_errors(true);
        $xml = simplexml_load_file($path);
        if ($xml === false) {
            reportError("XML inválido: $relativePath");
            libxml_clear_errors();
            return;
        }
        libxml_clear_errors();

        $checkedContests++;
        requireNode($xml, '/root/concurso', $relativePath);
        requireNode($xml, '/root/fecha', $relativePath);
        requireNode($xml, '//prueba', $relativePath);

        $date = DateTime::createFromFormat('d/m/Y', (string)$xml->fecha);
        if (!$date || $date->format('d/m/Y') !== (string)$xml->fecha) {
            reportError("$relativePath: fecha inválida");
        }

        foreach ($xml->pruebas->prueba as $prueba) {
            $link = trim((string)$prueba->link);
            if ($link === '') {
                reportError("$relativePath: prueba sin enlace JSON");
                continue;
            }

            $jsonPath = CProjectPath::resolveData($link);
            if (!is_file($jsonPath)) {
                reportError("$relativePath: no existe $link");
                continue;
            }

            try {
                CResultadoJson::load($link);
            } catch (Throwable $exception) {
                reportError("$link: " . $exception->getMessage());
            }
        }

        foreach ($xml->ordensalidas->ordensalida as $orden) {
            $link = trim((string)$orden);
            if ($link !== '' && !is_file(CProjectPath::resolveData($link))) {
                reportError("$relativePath: no existe orden de salida $link");
            }
        }

        if (isset($xml->planos->plano)) {
            foreach ($xml->planos->plano as $plano) {
                $link = trim((string)$plano);
                if ($link !== '' && !is_file(CProjectPath::resolveData($link))) {
                    reportError("$relativePath: no existe plano $link");
                }
            }
        }
    } catch (Throwable $exception) {
        reportError("$relativePath: " . $exception->getMessage());
    }
}

$configPath = CProjectPath::resolveCatalog();
if (!is_file($configPath) || !is_readable($configPath)) {
    reportError('No existe config.xml');
} else {
    libxml_use_internal_errors(true);
    $config = simplexml_load_file($configPath);
    if ($config === false) {
        reportError('config.xml no es un XML válido');
    } else {
        foreach ($config->children() as $yearNode) {
            if (!isset($yearNode->concursos)) {
                continue;
            }

            foreach ($yearNode->concursos->concurso as $contest) {
                $id = trim((string)$contest->id);
                if (!ctype_digit($id) || (int)$id < 1) {
                    reportError('config.xml: ID de concurso inválido');
                } elseif (isset($contestIds[$id])) {
                    reportError("config.xml: ID de concurso duplicado: $id");
                } else {
                    $contestIds[$id] = true;
                }

                foreach (['nombre', 'fecha', 'datos', 'tipo', 'visible'] as $field) {
                    if (trim((string)$contest->{$field}) === '') {
                        reportError("config.xml: concurso $id sin campo $field");
                    }
                }

                $configDate = DateTime::createFromFormat('d/m/Y', trim((string)$contest->fecha));
                if (!$configDate || $configDate->format('d/m/Y') !== trim((string)$contest->fecha)) {
                    reportError("config.xml: concurso $id con fecha inválida");
                }

                if (strcasecmp(trim((string)$contest->visible), 'no') === 0) {
                    continue;
                }

                $dataPath = trim((string)$contest->datos);
                if ($dataPath === '') {
                    reportError("config.xml: concurso $id sin XML de datos");
                } else {
                    validateContestXml($dataPath);
                }
            }
        }
    }
    libxml_clear_errors();
}

foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator(CProjectPath::dataRoot() . DIRECTORY_SEPARATOR . 'recursos')) as $file) {
    if (strtolower($file->getExtension()) !== 'json') {
        continue;
    }

    $checkedJson++;
    try {
        CResultadoJson::load($file->getPathname());
    } catch (Throwable $exception) {
        reportError($file->getPathname() . ': ' . $exception->getMessage());
    }
}

if ($errors === 0) {
    reportOk("$checkedContests concursos y $checkedJson JSON validados.");
    exit(0);
}

echo "Validación finalizada con $errors error(es)." . PHP_EOL;
exit(1);
