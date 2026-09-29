<?php

require_once __DIR__ . '/../model/resultado.php';
require_once __DIR__ . '/base.controller.php';

class CResultadoController extends CBaseController
{
    protected $actionViews = [
        'Index' => 'body.php',
        'Listado' => 'pruebalistadoresultado.php',
        'Resultado' => 'pruebaresultado.php',
    ];

    public function __construct()
    {
        $this->initializeConcursoModel(CResultado::class);
    }

}
