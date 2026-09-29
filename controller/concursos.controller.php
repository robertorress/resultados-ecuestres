<?php

require_once __DIR__ . '/base.controller.php';

class CConcursosController extends CBaseController
{
    protected $actionViews = [
        'Index' => ['body' => 'body_concursos.php', 'header' => 'header_concursos.php'],
        'Listado' => ['body' => 'body_concursos.php', 'header' => 'header_concursos.php'],
    ];

    public function __construct()
    {
        $this->m_model = new CConcursos();
    }

}
