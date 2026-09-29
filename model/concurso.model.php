<?php

require_once __DIR__ . '/../database/concursowrapper.php';

abstract class CConcursoModel
{
    protected $m_concurso;
    protected $m_id = -1;

    public function __construct($id, $path)
    {
        $this->m_id = $id;
        $this->m_concurso = CConcursoWrapper::getInstance($path);
    }

    public function GetConcursoId()
    {
        return $this->m_id;
    }

    public function GetConcurso()
    {
        return $this->m_concurso->GetConcurso();
    }

    public function GetLogo()
    {
        return $this->m_concurso->GetLogo();
    }

    public function GetOrdenSalida($id)
    {
        return $this->m_concurso->GetOrdenSalida($id);
    }

    public function GetOrdenSalidas()
    {
        return $this->m_concurso->GetOrdenSalidas();
    }

    public function GetSorteoOrdenSalida()
    {
        return $this->m_concurso->GetSorteoOrdenSalida();
    }

    public function GetPruebas()
    {
        return $this->m_concurso->GetPruebas();
    }

    public function GetPrueba($id)
    {
        foreach ($this->GetPruebas() as $prueba) {
            if ((string)$prueba->id === (string)$id) {
                return $prueba;
            }
        }

        return null;
    }

    public function GetFecha()
    {
        return $this->m_concurso->GetFecha();
    }

    public function GetUbicacionNombre()
    {
        return $this->m_concurso->GetUbicacionNombre();
    }

    public function GetUbicacionLatitud()
    {
        return $this->m_concurso->GetUbicacionLatitud();
    }

    public function GetUbicacionLongitud()
    {
        return $this->m_concurso->GetUbicacionLongitud();
    }
}
