<?php

require_once __DIR__ . '/../model/concursos.php';

abstract class CBaseController
{
    protected $m_model;
    protected $actionViews = [];
    protected $modelClass = null;

    public function __construct()
    {
        if (is_string($this->modelClass) && $this->modelClass !== '') {
            $this->initializeConcursoModel($this->modelClass);
        }
    }

    protected function initializeConcursoModel($modelClass)
    {
        $id = -1;
        $path = '';
        $requestId = request_positive_int('id');
        $jornadaId = request_positive_int('jornada');

        if ($requestId === null) {
            $this->m_model = new $modelClass($id, $path, $jornadaId);
            return;
        }

        $concurso = (new CConcursos())->GetConcurso($requestId);
        if ($concurso !== null) {
            $id = (string)$concurso->id;
            $path = (string)$concurso->datos;
        }

        $this->m_model = new $modelClass($id, $path, $jornadaId);
    }

    protected function render($view, $header = 'header.php')
    {
        require_once __DIR__ . '/../view/' . $header;
        require_once __DIR__ . '/../view/' . $view;
        require_once __DIR__ . '/../view/footer.php';
    }

    public function __call($action, $arguments)
    {
        if (!isset($this->actionViews[$action])) {
            throw new BadMethodCallException('Accion de controlador no definida: ' . $action);
        }

        $view = $this->actionViews[$action];
        $header = is_array($view) ? $view['header'] : 'header.php';
        $body = is_array($view) ? $view['body'] : $view;
        $this->render($body, $header);
    }
}
