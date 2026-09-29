<?php

require_once __DIR__ . '/../database/concursoswrapper.php';
require_once __DIR__ . '/data/concurso-display-data.php';
require_once __DIR__ . '/../config/constants.php';

class CConcursos
{
    private $m_concursos = null;

    public function __construct()
    {
        try {
            $this->m_concursos = CConcursosWrapper::getInstance('config.xml');
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function GetConcurso($id)
    {
        if ($this->m_concursos === null) {
            return "ERROR XML";
        }

        return $this->m_concursos->getConcursoById($id);
    }

    public function GetConcursosByYear($year)
    {
        if ($this->m_concursos === null) {
            return "ERROR XML";
        }

        return $this->m_concursos->getConcursosByAnio($year);
    }

    public function GetConcursosByYearSorted($year)
    {
        $concursos = $this->GetConcursosByYear($year);
        if (!is_iterable($concursos)) {
            return [];
        }

        $concursos = iterator_to_array($concursos, false);
        usort($concursos, static function ($a, $b) {
            $fechaA = DateTime::createFromFormat('d/m/Y', (string)$a->fecha);
            $fechaB = DateTime::createFromFormat('d/m/Y', (string)$b->fecha);

            return $fechaB <=> $fechaA;
        });

        return $concursos;
    }

    public function GetUpcomingConcursoIds(array $concursos)
    {
        $today = new DateTimeImmutable('today');
        $ids = [];

        foreach ($concursos as $concurso) {
            $fecha = DateTime::createFromFormat('d/m/Y', (string)$concurso->fecha);
            if (
                strcasecmp((string)$concurso->visible, 'si') === 0
                && $fecha !== false
                && $fecha >= $today
            ) {
                $ids[] = (string)$concurso->id;
            }
        }

        return $ids;
    }

    /**
     * @return ConcursoDisplayData[]
     */
    public function getDisplayConcursos(int $year): array
    {
        $concursos = $this->GetConcursosByYearSorted($year);
        $upcomingIds = $this->GetUpcomingConcursoIds($concursos);
        $display = [];

        foreach ($concursos as $concurso) {
            if (strcasecmp((string)$concurso->visible, 'si') !== 0) {
                continue;
            }

            $isUpcoming = in_array((string)$concurso->id, $upcomingIds, true);
            $type = (string)$concurso->tipo;
            $typeImage = null;
            $typeAlt = null;

            if (mb_stripos($type, 'clásica') !== false || mb_stripos($type, 'clasica') !== false) {
                $typeImage = 'recursos/imagenes/sombrero_doma_clasica.png';
                $typeAlt = 'Doma Clásica';
            } elseif (mb_stripos($type, 'vaquera') !== false) {
                $typeImage = 'recursos/imagenes/sombrero_doma_vaquera.png';
                $typeAlt = 'Doma Vaquera';
            }

            $display[] = new ConcursoDisplayData(
                (string)$concurso->id,
                (string)$concurso->fecha,
                (string)$concurso->nombre,
                $type,
                strtolower((string)$concurso->fecha . ' ' . (string)$concurso->nombre . ' ' . $type),
                $isUpcoming,
                $isUpcoming
                    ? 'recursos/imagenes/cartel_abierto.png'
                    : 'recursos/imagenes/cartel_fin.png',
                $isUpcoming ? 'Abierto' : 'Fin',
                $isUpcoming ? 'play_circle_outline' : 'play_arrow',
                $typeImage,
                $typeAlt
            );
        }

        return $display;
    }

    public function GetFechaConcurso($concurso)
    {
        if ($concurso != null) {
            return $concurso->fecha;
        } else {
            return "ERROR XML";
        }
    }

    public function GetFechaConcursoById($id)
    {
        return $this->getConcursoFieldById($id, 'fecha');
    }

    public function GetNombreConcurso($concurso)
    {
        if ($concurso != null) {
            return $concurso->nombre;
        } else {
            return "ERROR XML";
        }
    }

    public function GetNombreConcursoById($id)
    {
        return $this->getConcursoFieldById($id, 'nombre');
    }

    private function getConcursoFieldById($id, string $field)
    {
        if ($this->m_concursos === null) {
            return "ERROR XML";
        }

        $concurso = $this->GetConcurso($id);
        return $concurso !== null ? $concurso->{$field} : null;
    }
}
