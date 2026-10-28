<?php

require_once __DIR__ . '/../database/concursowrapper.php';

abstract class CConcursoModel
{
    protected $m_concurso;
    protected $m_id = -1;
    protected $m_path = '';
    protected $m_jornadaId = null;

    public function __construct($id, $path, $jornadaId = null)
    {
        $this->m_id = $id;
        $this->m_path = (string)$path;
        $this->m_jornadaId = $jornadaId;
        $this->m_concurso = CConcursoWrapper::getInstance($path);

        if ($this->m_jornadaId !== null && $this->m_concurso->GetJornada($this->m_jornadaId) === null) {
            $this->m_jornadaId = null;
        }
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

    public function GetPlanos()
    {
        return $this->m_concurso->GetPlanos();
    }

    public function GetAnuncios()
    {
        return $this->m_concurso->GetAnuncios();
    }

    public function GetAnunciosVisible()
    {
        return $this->m_concurso->GetAnunciosVisible();
    }

    public function IsAnuncioVisible($anuncio)
    {
        return $this->m_concurso->IsAnuncioVisible($anuncio);
    }

    public function GetPruebas()
    {
        return $this->m_concurso->GetPruebas($this->m_jornadaId);
    }

    public function GetPruebasJornada($jornadaId)
    {
        return $this->m_concurso->GetPruebas($jornadaId);
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
        return $this->m_concurso->GetFecha($this->m_jornadaId);
    }

    public function GetFechaInicio()
    {
        return $this->m_concurso->GetFechaInicio();
    }

    public function GetFechaFin()
    {
        return $this->m_concurso->GetFechaFin();
    }

    public function GetJornadas()
    {
        return $this->m_concurso->GetJornadas();
    }

    public function HasJornadas()
    {
        return $this->m_concurso->HasJornadas();
    }

    public function GetJornadaId()
    {
        $jornada = $this->m_concurso->GetJornada($this->m_jornadaId);
        return $jornada === null ? null : (string)$jornada->id;
    }

    public function GetJornadaNombre()
    {
        $jornada = $this->m_concurso->GetJornada($this->m_jornadaId);
        if ($jornada === null) {
            return null;
        }

        $titulo = trim((string)($jornada->titulo ?? ''));
        return $titulo !== '' ? $titulo : trim((string)$jornada->fecha);
    }

    public function GetConcursoDataPath()
    {
        return $this->m_path;
    }

    public function GetEquiposDataPath()
    {
        if (trim($this->m_path) === '') {
            return null;
        }

        $normalizedPath = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $this->m_path);
        return dirname($normalizedPath) . DIRECTORY_SEPARATOR . 'equipos.xml';
    }

    public function ShouldShowResultadosEquipos()
    {
        $equipoValue = (string)($this->m_concurso->GetEquipo() ?? '');
        $normalized = mb_strtolower(trim($equipoValue), 'UTF-8');
        $normalized = strtr($normalized, [
            'á' => 'a',
            'é' => 'e',
            'í' => 'i',
            'ó' => 'o',
            'ú' => 'u',
        ]);

        if ($normalized === '') {
            return false;
        }

        if (in_array($normalized, ['si', 'yes'], true)) {
            return true;
        }

        if (in_array($normalized, ['no'], true)) {
            return false;
        }

        return false;
    }

    public function GetUbicacionNombre()
    {
        return $this->m_concurso->GetUbicacionNombre();
    }

    public function GetUbicacionVisible()
    {
        return $this->m_concurso->GetUbicacionVisible();
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
