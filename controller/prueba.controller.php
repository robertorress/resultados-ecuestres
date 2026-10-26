<?php

require_once __DIR__ . '/../model/prueba.php';
require_once __DIR__ . '/base.controller.php';

class CPruebaController extends CBaseController
{
    protected $modelClass = CPrueba::class;

    protected $actionViews = [
        'Index' => 'body.php',
        'MostrarPrueba' => 'body.php',
        'Plano' => 'pruebaplano.php',
        'ListadoOrdenSalida' => 'pruebalistadoordensalida.php',
        'ListadoResultado' => 'pruebalistadoresultado.php',
    ];

}
