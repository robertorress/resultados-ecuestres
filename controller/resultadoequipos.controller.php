<?php

require_once __DIR__ . '/../model/resultadoequipos.php';
require_once __DIR__ . '/base.controller.php';

class CResultadoEquiposController extends CBaseController
{
    protected $modelClass = CResultadoEquipos::class;

    protected $actionViews = [
        'Index' => 'body.php',
        'Listado' => 'pruebaresultadoequipos.php',
        'ResultadoEquipos' => 'pruebaresultadoequipos.php',
    ];
}
