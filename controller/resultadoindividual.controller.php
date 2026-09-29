<?php

require_once __DIR__ . '/../model/resultadoindividual.php';
require_once __DIR__ . '/base.controller.php';

class CResultadoIndividualController extends CBaseController
{
    protected $actionViews = [
        'Index' => 'body.php',
        'Listado' => 'pruebaresultadoindividual.php',
        'ResultadoIndividual' => 'pruebaresultadoindividual.php',
    ];

    public function __construct()
    {
        $this->initializeConcursoModel(CResultadoIndividual::class);
    }

}
