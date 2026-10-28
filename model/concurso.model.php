<?php

require_once __DIR__ . '/../database/concursowrapper.php';
require_once __DIR__ . '/../database/resultadojson.php';

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

    /**
     * Reúne los binomios de todas las pruebas del concurso para la tabla de participantes.
     *
     * En concursos con jornadas, las pruebas se recorren todas (no solo la jornada
     * seleccionada en la URL), porque esta información se muestra en la portada
     * general del concurso.
     */
    public function GetParticipantes()
    {
        $participantes = [];
        $pruebas = [];

        if ($this->HasJornadas()) {
            // Guardamos también el nombre de la jornada para distinguir pruebas iguales
            // celebradas en días diferentes.
            foreach ($this->GetJornadas() as $jornada) {
                $jornadaId = (string)$jornada->id;
                $jornadaNombre = trim((string)($jornada->titulo ?? ''));
                if ($jornadaNombre === '') {
                    $jornadaNombre = trim((string)$jornada->fecha);
                }

                foreach ($this->GetPruebasJornada($jornadaId) as $prueba) {
                    $pruebas[] = [$prueba, $jornadaNombre];
                }
            }
        } else {
            foreach ($this->GetPruebas() as $prueba) {
                $pruebas[] = [$prueba, null];
            }
        }

        // CResultadoJson valida la estructura del archivo y mantiene una caché por ruta,
        // por lo que la misma fuente no se vuelve a leer innecesariamente durante la petición.
        foreach ($pruebas as [$prueba, $jornadaNombre]) {
            $link = trim((string)($prueba->link ?? ''));
            if ($link === '') {
                continue;
            }

            $resultado = CResultadoJson::load($link);
            foreach ($resultado['resultados']['datos'] as $dato) {
                if (!is_array($dato)) {
                    continue;
                }

                $jinete = trim((string)($dato['jinete_nombre'] ?? ''));
                $caballo = trim((string)($dato['caballo_nombre'] ?? ''));
                if ($jinete === '' && $caballo === '') {
                    continue;
                }

                $participantes[] = [
                    'jinete' => $jinete !== '' ? $jinete : trim((string)($dato['jinete_licencia'] ?? '')),
                    'caballo' => $caballo !== '' ? $caballo : trim((string)($dato['caballo_licencia'] ?? '')),
                    'prueba' => trim((string)($prueba->titulo ?? '')),
                    'jornada' => $jornadaNombre,
                ];
            }
        }

        // Además del jinete, usamos caballo y prueba como desempates para que el orden
        // sea determinista cuando un jinete participa en varias pruebas.
        usort($participantes, static function (array $a, array $b): int {
            $jineteComparison = strcoll(
                mb_strtolower($a['jinete'], 'UTF-8'),
                mb_strtolower($b['jinete'], 'UTF-8')
            );
            if ($jineteComparison !== 0) {
                return $jineteComparison;
            }

            $caballoComparison = strcoll(
                mb_strtolower($a['caballo'], 'UTF-8'),
                mb_strtolower($b['caballo'], 'UTF-8')
            );
            if ($caballoComparison !== 0) {
                return $caballoComparison;
            }

            return strcoll(
                mb_strtolower($a['prueba'], 'UTF-8'),
                mb_strtolower($b['prueba'], 'UTF-8')
            );
        });

        return $participantes;
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
