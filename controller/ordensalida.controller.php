<?php

require_once __DIR__ . '/../model/ordensalida.php';
require_once __DIR__ . '/base.controller.php';

class COrdenSalidaController extends CBaseController
{
    protected $modelClass = COrdenSalida::class;

    protected $actionViews = [
        'Index' => 'body.php',
        'Listado' => 'pruebalistadoordensalida.php',
    ];

}
