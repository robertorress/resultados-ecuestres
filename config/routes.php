<?php

return [
    'concursos' => [
        'file' => 'controller/concursos.controller.php',
        'class' => 'CConcursosController',
        'actions' => ['Index', 'Listado'],
    ],
    'prueba' => [
        'file' => 'controller/prueba.controller.php',
        'class' => 'CPruebaController',
        'actions' => ['Index', 'MostrarPrueba', 'Plano', 'ListadoOrdenSalida', 'ListadoResultado'],
    ],
    'resultado' => [
        'file' => 'controller/resultado.controller.php',
        'class' => 'CResultadoController',
        'actions' => ['Index', 'Listado', 'Resultado'],
    ],
    'resultadogeneral' => [
        'file' => 'controller/resultadogeneral.controller.php',
        'class' => 'CResultadoGeneralController',
        'actions' => ['Index', 'Listado', 'ResultadoGeneral'],
    ],
    'resultadoequipos' => [
        'file' => 'controller/resultadoequipos.controller.php',
        'class' => 'CResultadoEquiposController',
        'actions' => ['Index', 'Listado', 'ResultadoEquipos'],
    ],
    'resultadoindividual' => [
        'file' => 'controller/resultadoindividual.controller.php',
        'class' => 'CResultadoIndividualController',
        'actions' => ['Index', 'Listado', 'ResultadoIndividual'],
    ],
    'ordensalida' => [
        'file' => 'controller/ordensalida.controller.php',
        'class' => 'COrdenSalidaController',
        'actions' => ['Index', 'Listado'],
    ],
];
