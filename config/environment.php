<?php

$environmentName = getenv('RESULTADOS_ENV') ?: 'production';
$displayErrorsRequested = filter_var(
    getenv('RESULTADOS_DISPLAY_ERRORS') ?: '0',
    FILTER_VALIDATE_BOOLEAN
);

return [
    'name' => $environmentName,
    'display_errors' => $environmentName !== 'production' && $displayErrorsRequested,
];
