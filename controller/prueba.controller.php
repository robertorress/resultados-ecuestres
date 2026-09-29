<?php

require_once __DIR__ . '/../model/prueba.php';
require_once __DIR__ . '/base.controller.php';

class CPruebaController extends CBaseController
{
    protected $actionViews = [
        'Index' => 'body.php',
        'MostrarPrueba' => 'body.php',
        'ListadoOrdenSalida' => 'pruebalistadoordensalida.php',
        'ListadoResultado' => 'pruebalistadoresultado.php',
    ];

    public function __construct()
    {
        $this->initializeConcursoModel(CPrueba::class);
    }

}
