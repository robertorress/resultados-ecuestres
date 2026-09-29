<?php

return [
    'app_root' => getenv('RESULTADOS_APP_ROOT') ?: dirname(__DIR__),
    'data_root' => getenv('RESULTADOS_DATA_ROOT') ?: dirname(__DIR__),
    'catalog_path' => getenv('RESULTADOS_CATALOG_PATH') ?: 'config.xml',
    'log_root' => getenv('RESULTADOS_LOG_ROOT') ?: dirname(__DIR__) . DIRECTORY_SEPARATOR . 'logs',
];
