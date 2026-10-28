<?php

require_once __DIR__ . '/projectpath.php';

class CResultadoJson
{
    private static $cache = [];

    public static function load($path)
    {
        if (!is_string($path) || trim($path) === '') {
            throw new Exception('La ruta del resultado está vacía.');
        }

        $path = CProjectPath::resolveData($path);
        if (!is_file($path) || !is_readable($path)) {
            throw new Exception("El archivo de resultados no existe o no se puede leer: $path");
        }

        $fileVersion = self::getFileVersion($path);
        if (isset(self::$cache[$path]) && self::$cache[$path]['version'] === $fileVersion) {
            return self::$cache[$path]['data'];
        }

        for ($attempt = 0; $attempt < 2; $attempt++) {
            $content = file_get_contents($path);
            if ($content === false || trim($content) === '') {
                throw new Exception("El archivo de resultados está vacío o no se puede leer: $path");
            }

            $readVersion = self::getFileVersion($path);
            if ($readVersion !== $fileVersion) {
                $fileVersion = $readVersion;
                continue;
            }

            try {
                $data = json_decode($content, true, 512, JSON_THROW_ON_ERROR);
            } catch (JsonException $exception) {
                if ($attempt === 0 && self::getFileVersion($path) !== $readVersion) {
                    $fileVersion = self::getFileVersion($path);
                    continue;
                }

                throw new Exception("JSON inválido en $path: " . $exception->getMessage(), 0, $exception);
            }

            if (!is_array($data) || !isset($data['concurso']) || !is_array($data['concurso'])) {
                throw new Exception("Estructura inválida en $path: falta 'concurso'.");
            }

            if (
                !isset($data['resultados']) ||
                !is_array($data['resultados']) ||
                !isset($data['resultados']['cabecera']) ||
                !is_array($data['resultados']['cabecera']) ||
                !isset($data['resultados']['datos']) ||
                !is_array($data['resultados']['datos'])
            ) {
                throw new Exception("Estructura inválida en $path: faltan datos de resultados.");
            }

            self::$cache[$path] = [
                'version' => $readVersion,
                'data' => $data,
            ];

            return $data;
        }

        throw new Exception("El archivo de resultados cambió mientras se leía: $path");
    }

    private static function getFileVersion($path)
    {
        $mtime = @filemtime($path);
        $size = @filesize($path);
        if ($mtime === false || $size === false) {
            throw new Exception("No se puede consultar el estado del archivo de resultados: $path");
        }

        return $mtime . ':' . $size;
    }
}
