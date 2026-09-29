<?php

class CProjectPath
{
    private static function paths()
    {
        static $paths;
        if ($paths === null) {
            $paths = require dirname(__DIR__) . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'paths.php';
        }

        return $paths;
    }

    public static function resolve($relativePath)
    {
        return self::resolveFromRoot($relativePath, self::paths()['app_root']);
    }

    public static function resolveData($relativePath)
    {
        return self::resolveFromRoot($relativePath, self::paths()['data_root']);
    }

    public static function resolveCatalog()
    {
        $catalog = self::paths()['catalog_path'];
        return self::isAbsolute($catalog) ? $catalog : self::resolve($catalog);
    }

    public static function dataRoot()
    {
        return self::paths()['data_root'];
    }

    public static function logRoot()
    {
        return self::paths()['log_root'];
    }

    private static function resolveFromRoot($relativePath, $root)
    {
        if (!is_string($relativePath) || trim($relativePath) === '') {
            throw new InvalidArgumentException('La ruta del recurso está vacía.');
        }

        $path = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $relativePath);
        if (self::isAbsolute($path)) {
            return $path;
        }

        return $root . DIRECTORY_SEPARATOR . ltrim($path, DIRECTORY_SEPARATOR);
    }

    private static function isAbsolute($path)
    {
        return preg_match('/^[A-Za-z]:\\\\|^\\\\\\\\|^\\\\/', $path) === 1;
    }
}
