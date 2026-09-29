<?php

require_once __DIR__ . '/../model/ordensalida.php';
require_once __DIR__ . '/base.controller.php';

class COrdenSalidaController extends CBaseController
{
    protected $actionViews = [
        'Index' => 'body.php',
        'Listado' => 'pruebalistadoordensalida.php',
    ];

    public function __construct()
    {
        $this->initializeConcursoModel(COrdenSalida::class);
    }

}
