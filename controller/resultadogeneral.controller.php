<?php

require_once __DIR__ . '/../model/resultadogeneral.php';
require_once __DIR__ . '/base.controller.php';

class CResultadoGeneralController extends CBaseController
{
    protected $modelClass = CResultadoGeneral::class;

    protected $actionViews = [
        'Index' => 'body.php',
        'Listado' => 'pruebaresultadogeneral.php',
        'ResultadoGeneral' => 'pruebaresultadogeneral.php',
    ];

}
