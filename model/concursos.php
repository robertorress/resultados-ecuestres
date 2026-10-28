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
            $fechaA = self::getEndDate($a);
            $fechaB = self::getEndDate($b);

            $timestampA = $fechaA instanceof DateTimeInterface ? $fechaA->getTimestamp() : PHP_INT_MIN;
            $timestampB = $fechaB instanceof DateTimeInterface ? $fechaB->getTimestamp() : PHP_INT_MIN;

            return $timestampB <=> $timestampA;
        });

        return $concursos;
    }

    public function GetUpcomingConcursoIds(array $concursos)
    {
        $today = new DateTimeImmutable('today');
        $ids = [];

        foreach ($concursos as $concurso) {
            $fecha = self::getEndDate($concurso);
            if (
                strcasecmp((string)$concurso->visible, 'si') === 0
                && $fecha instanceof DateTimeInterface
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
        $upcomingIdSet = array_fill_keys($this->GetUpcomingConcursoIds($concursos), true);
        $display = [];

        foreach ($concursos as $concurso) {
            if (strcasecmp((string)$concurso->visible, 'si') !== 0) {
                continue;
            }

            $isUpcoming = isset($upcomingIdSet[(string)$concurso->id]);
            $type = (string)$concurso->tipo;
            $fecha = self::getDateDisplay($concurso);
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
                $fecha,
                (string)$concurso->nombre,
                $type,
                strtolower($fecha . ' ' . (string)$concurso->nombre . ' ' . $type),
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
            return self::getDateDisplay($concurso);
        } else {
            return "ERROR XML";
        }
    }

    public function GetFechaConcursoById($id)
    {
        $concurso = $this->GetConcurso($id);
        return $concurso !== null ? self::getDateDisplay($concurso) : null;
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

    private static function getStartDate($concurso)
    {
        $value = trim((string)($concurso->fecha_inicio ?? ''));
        if ($value === '') {
            $value = trim((string)($concurso->fecha ?? ''));
        }

        return self::parseDate($value);
    }

    private static function getEndDate($concurso)
    {
        $value = trim((string)($concurso->fecha_fin ?? ''));
        if ($value === '') {
            $value = trim((string)($concurso->fecha ?? ''));
        }

        return self::parseDate($value) ?: self::getStartDate($concurso);
    }

    private static function getDateDisplay($concurso)
    {
        $start = trim((string)($concurso->fecha_inicio ?? ''));
        if ($start === '') {
            $start = trim((string)($concurso->fecha ?? ''));
        }

        $end = trim((string)($concurso->fecha_fin ?? ''));
        if ($end === '' || $end === $start) {
            return $start;
        }

        $startDate = self::parseDate($start);
        $endDate = self::parseDate($end);
        return $startDate !== null && $endDate !== null ? $start . ' - ' . $end : $start;
    }

    private static function parseDate($value)
    {
        $date = DateTime::createFromFormat('d/m/Y', $value);
        return $date !== false && $date->format('d/m/Y') === $value ? $date : null;
    }
}
