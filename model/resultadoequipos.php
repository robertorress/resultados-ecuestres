<?php

require_once __DIR__ . '/concurso.model.php';
require_once __DIR__ . '/../database/projectpath.php';
require_once __DIR__ . '/../database/resultadojson.php';

class CResultadoEquipos extends CConcursoModel
{
    // Reglas del cálculo por equipos.
    private const TEAM_MIN_MEMBERS = 3;
    private const TEAM_MAX_MEMBERS = 4;
    private const TOP_SCORES_WHEN_FOUR = 3;

    public function getClasificacionEquipos()
    {
        $equipos = $this->readEquiposConfig();
        $puntuaciones = $this->collectScoresByBinomio();

        $clasificacion = [];
        $invalidos = [];

        foreach ($equipos as $equipo) {
            $evaluado = $this->evaluateTeam($equipo, $puntuaciones);
            if (!empty($evaluado['errores'])) {
                $invalidos[] = [
                    'nombre' => $equipo['nombre'],
                    'errores' => $evaluado['errores'],
                ];
                continue;
            }

            $clasificacion[] = $evaluado['equipo'];
        }

        usort($clasificacion, static function ($a, $b) {
            if ($a['puntos_totales'] === $b['puntos_totales']) {
                return strcmp($a['nombre'], $b['nombre']);
            }

            return $a['puntos_totales'] < $b['puntos_totales'] ? 1 : -1;
        });

        return [
            'clasificacion' => $clasificacion,
            'invalidos' => $invalidos,
        ];
    }

    private function readEquiposConfig()
    {
        $equiposPath = $this->GetEquiposDataPath();
        if ($equiposPath === null) {
            throw new Exception('No se ha podido determinar la ruta de equipos.xml para este concurso.');
        }

        $absolutePath = CProjectPath::resolveData($equiposPath);
        if (!is_file($absolutePath)) {
            throw new Exception("No existe el archivo de equipos: $equiposPath");
        }

        $xml = @simplexml_load_file($absolutePath);
        if ($xml === false) {
            throw new Exception("El archivo de equipos no tiene un XML válido: $equiposPath");
        }

        $teamNodes = [];
        if (isset($xml->equipo)) {
            foreach ($xml->equipo as $equipoNode) {
                $teamNodes[] = $equipoNode;
            }
        } elseif (isset($xml->equipos->equipo)) {
            foreach ($xml->equipos->equipo as $equipoNode) {
                $teamNodes[] = $equipoNode;
            }
        }

        if (count($teamNodes) === 0) {
            throw new Exception('El archivo equipos.xml no contiene ningún equipo.');
        }

        $equipos = [];
        foreach ($teamNodes as $teamIndex => $teamNode) {
            $nombre = trim((string)($teamNode->nombre ?? $teamNode->name ?? ''));
            if ($nombre === '') {
                $nombre = 'Equipo sin nombre';
            }

            $memberNodes = [];
            if (isset($teamNode->binomios->binomio)) {
                foreach ($teamNode->binomios->binomio as $memberNode) {
                    $memberNodes[] = $memberNode;
                }
            } elseif (isset($teamNode->binomio)) {
                foreach ($teamNode->binomio as $memberNode) {
                    $memberNodes[] = $memberNode;
                }
            }

            // Validación mínima de estructura para facilitar la detección de XML mal formado.
            if (count($memberNodes) === 0) {
                $teamPosition = $teamIndex + 1;
                throw new Exception("Estructura inválida en equipos.xml: el equipo '{$nombre}' (posición {$teamPosition}) no contiene binomios.");
            }

            $miembros = [];
            foreach ($memberNodes as $memberIndex => $memberNode) {
                $jineteNode = $memberNode->jinete ?? null;
                $caballoNode = $memberNode->caballo ?? null;

                $jineteLicencia = trim((string)($memberNode->jinete_licencia ?? $memberNode->jineteLicencia ?? $memberNode->jinete_id ?? $memberNode->jineteId ?? ($jineteNode->licencia ?? '')));
                $caballoLicencia = trim((string)($memberNode->caballo_licencia ?? $memberNode->caballoLicencia ?? $memberNode->caballo_id ?? $memberNode->caballoId ?? ($caballoNode->licencia ?? '')));
                $jineteNombre = trim((string)($memberNode->jinete_nombre ?? $memberNode->nombre_jinete ?? ($jineteNode->nombre ?? $jineteNode ?? '')));
                $caballoNombre = trim((string)($memberNode->caballo_nombre ?? $memberNode->nombre_caballo ?? ($caballoNode->nombre ?? $caballoNode ?? '')));

                if ($jineteLicencia === '' && $caballoLicencia === '' && $jineteNombre === '' && $caballoNombre === '') {
                    continue;
                }

                $miembros[] = [
                    'jinete_licencia' => $jineteLicencia,
                    'caballo_licencia' => $caballoLicencia,
                    'jinete_nombre' => $jineteNombre,
                    'caballo_nombre' => $caballoNombre,
                ];
            }

            if (count($miembros) !== count($memberNodes)) {
                $teamPosition = $teamIndex + 1;
                throw new Exception("Estructura inválida en equipos.xml: el equipo '{$nombre}' (posición {$teamPosition}) contiene binomios sin datos de jinete/caballo.");
            }

            $equipos[] = [
                'nombre' => $nombre,
                'miembros' => $miembros,
            ];
        }

        return $equipos;
    }

    private function collectScoresByBinomio()
    {
        $scores = [];

        foreach ($this->GetPruebas() as $prueba) {
            $results = CResultadoJson::load((string)$prueba->link);
            $pruebaTitulo = trim((string)$prueba->titulo);
            $concurso = $results['concurso'] ?? [];
            $datos = $results['resultados']['datos'] ?? [];

            foreach ($datos as $dato) {
                $finalScore = $this->extractFinalScore($dato, $concurso);
                if ($finalScore === null) {
                    continue;
                }

                $jineteLicencia = trim((string)($dato['jinete_licencia'] ?? ''));
                $caballoLicencia = trim((string)($dato['caballo_licencia'] ?? ''));
                $jineteNombre = trim((string)($dato['jinete_nombre'] ?? ''));
                $caballoNombre = trim((string)($dato['caballo_nombre'] ?? ''));

                $scores[] = [
                    'jinete_licencia' => mb_strtoupper($jineteLicencia, 'UTF-8'),
                    'caballo_licencia' => mb_strtoupper($caballoLicencia, 'UTF-8'),
                    'jinete_nombre' => $jineteNombre,
                    'caballo_nombre' => $caballoNombre,
                    'jinete_nombre_normalizado' => $this->normalizeRiderName($jineteNombre),
                    'caballo_nombre_normalizado' => $this->normalizeText($caballoNombre),
                    'prueba' => $pruebaTitulo,
                    'puntos' => $finalScore,
                ];
            }
        }

        return $scores;
    }

    private function evaluateTeam($teamConfig, $allScores)
    {
        $errores = [];
        $miembros = $teamConfig['miembros'];
        $numeroMiembros = count($miembros);

        if ($numeroMiembros < self::TEAM_MIN_MEMBERS || $numeroMiembros > self::TEAM_MAX_MEMBERS) {
            $errores[] = "No cumple los requisitos: el equipo debe tener " . self::TEAM_MIN_MEMBERS . " o " . self::TEAM_MAX_MEMBERS . " binomios (actual: {$numeroMiembros}).";
        }

        $seenBinomios = [];
        foreach ($miembros as $miembro) {
            $key = $this->buildMemberIdentityKey($miembro);
            if ($key === null) {
                continue;
            }

            if (isset($seenBinomios[$key])) {
                $errores[] = 'No cumple los requisitos: hay un binomio repetido dentro del equipo.';
                break;
            }

            $seenBinomios[$key] = true;
        }

        $evaluacionMiembros = [];

        foreach ($miembros as $miembro) {
            $memberScores = $this->matchMemberScores($miembro, $allScores);
            if (count($memberScores) === 0) {
                $jinete = trim((string)$miembro['jinete_nombre']);
                $caballo = trim((string)$miembro['caballo_nombre']);
                $jineteLicencia = trim((string)$miembro['jinete_licencia']);
                $caballoLicencia = trim((string)$miembro['caballo_licencia']);

                if ($jinete === '') {
                    $jinete = $jineteLicencia !== '' ? $jineteLicencia : 'Jinete sin identificar';
                }
                if ($caballo === '') {
                    $caballo = $caballoLicencia !== '' ? $caballoLicencia : 'Caballo sin identificar';
                }

                $errores[] = "No se encuentra el binomio: {$jinete} / {$caballo}.";
            }

            usort($memberScores, static function ($a, $b) {
                if ($a['puntos'] === $b['puntos']) {
                    return strcmp($a['prueba'], $b['prueba']);
                }

                return $a['puntos'] < $b['puntos'] ? 1 : -1;
            });

            $bestScore = $memberScores[0] ?? null;
            $memberTotal = 0.0;
            if ($bestScore !== null) {
                $memberTotal = $bestScore['puntos'];
            }

            $evaluacionMiembros[] = [
                'jinete' => $miembro['jinete_nombre'] !== '' ? $miembro['jinete_nombre'] : $miembro['jinete_licencia'],
                'jinete_licencia' => $miembro['jinete_licencia'],
                'caballo' => $miembro['caballo_nombre'] !== '' ? $miembro['caballo_nombre'] : $miembro['caballo_licencia'],
                'caballo_licencia' => $miembro['caballo_licencia'],
                'puntos' => $memberTotal,
                'resultados' => $bestScore !== null ? [$bestScore] : [],
                'sumado' => true,
            ];
        }

        $sumables = [];
        foreach ($evaluacionMiembros as $idx => $miembroEvaluado) {
            $sumables[] = [
                'idx' => $idx,
                'puntos' => (float)$miembroEvaluado['puntos'],
            ];
        }

        usort($sumables, static function ($a, $b) {
            if ($a['puntos'] === $b['puntos']) {
                return $a['idx'] <=> $b['idx'];
            }

            return $a['puntos'] < $b['puntos'] ? 1 : -1;
        });

        // Si hay 4 binomios, solo suman 3; en el resto, suman todos.
        $limiteSumados = count($evaluacionMiembros) >= self::TEAM_MAX_MEMBERS ? self::TOP_SCORES_WHEN_FOUR : count($evaluacionMiembros);
        $indicesSumados = [];
        for ($i = 0; $i < count($sumables); $i++) {
            if ($i < $limiteSumados) {
                $indicesSumados[$sumables[$i]['idx']] = true;
            }
        }

        $totalTeamPoints = 0.0;
        foreach ($evaluacionMiembros as $idx => &$miembroEvaluado) {
            $miembroEvaluado['sumado'] = isset($indicesSumados[$idx]);
            if ($miembroEvaluado['sumado']) {
                $totalTeamPoints += (float)$miembroEvaluado['puntos'];
            }
        }
        unset($miembroEvaluado);

        usort($evaluacionMiembros, static function ($a, $b) {
            if ($a['sumado'] !== $b['sumado']) {
                return $a['sumado'] ? -1 : 1;
            }

            if ($a['puntos'] === $b['puntos']) {
                $aNombre = ($a['jinete'] ?? '') . '|' . ($a['caballo'] ?? '');
                $bNombre = ($b['jinete'] ?? '') . '|' . ($b['caballo'] ?? '');
                return strcmp($aNombre, $bNombre);
            }

            return $a['puntos'] < $b['puntos'] ? 1 : -1;
        });

        return [
            'errores' => $errores,
            'equipo' => [
                'nombre' => $teamConfig['nombre'],
                'puntos_totales' => $totalTeamPoints,
                'miembros' => $evaluacionMiembros,
            ],
        ];
    }

    private function matchMemberScores($member, $allScores)
    {
        $jineteLicencia = mb_strtoupper(trim((string)$member['jinete_licencia']), 'UTF-8');
        $caballoLicencia = mb_strtoupper(trim((string)$member['caballo_licencia']), 'UTF-8');
        $jineteNombreNormalizado = $this->normalizeRiderName((string)$member['jinete_nombre']);
        $caballoNombreNormalizado = $this->normalizeText((string)$member['caballo_nombre']);

        // Estrategia: por cada campo (jinete y caballo), priorizar licencia si existe
        // en resultados; si no, usar nombre normalizado.
        $jineteLicenciaExiste = false;
        $caballoLicenciaExiste = false;
        foreach ($allScores as $score) {
            if ($jineteLicencia !== '' && (string)$score['jinete_licencia'] === $jineteLicencia) {
                $jineteLicenciaExiste = true;
            }
            if ($caballoLicencia !== '' && (string)$score['caballo_licencia'] === $caballoLicencia) {
                $caballoLicenciaExiste = true;
            }
        }

        $usaLicenciaJinete = $jineteLicencia !== '' && $jineteLicenciaExiste;
        $usaLicenciaCaballo = $caballoLicencia !== '' && $caballoLicenciaExiste;

        $matchingScores = [];
        foreach ($allScores as $score) {
            $matchJinete = false;
            if ($usaLicenciaJinete) {
                $matchJinete = (string)$score['jinete_licencia'] === $jineteLicencia;
            } elseif ($jineteNombreNormalizado !== '') {
                $matchJinete = $score['jinete_nombre_normalizado'] === $jineteNombreNormalizado;
            }

            $matchCaballo = false;
            if ($usaLicenciaCaballo) {
                $matchCaballo = (string)$score['caballo_licencia'] === $caballoLicencia;
            } elseif ($caballoNombreNormalizado !== '') {
                $matchCaballo = $score['caballo_nombre_normalizado'] === $caballoNombreNormalizado;
            }

            if ($matchJinete && $matchCaballo) {
                $matchingScores[] = $score;
            }
        }

        return $matchingScores;
    }

    private function extractFinalScore($dato, $concurso)
    {
        $puesto = $dato['puesto'] ?? null;
        if (!is_numeric($puesto) || (int)$puesto <= 0) {
            return null;
        }

        $isPotros = isset($concurso['potros']) && (int)$concurso['potros'] === 1;
        $isInfantiles = isset($concurso['infantiles']) && (int)$concurso['infantiles'] === 1;
        $isSieteAnnos = isset($concurso['siete_annos']) && (int)$concurso['siete_annos'] === 1;

        if ($isPotros && isset($dato['potros']['total']) && is_numeric($dato['potros']['total'])) {
            return (float)$dato['potros']['total'];
        }

        if ($isInfantiles && isset($dato['infantiles']['total']) && is_numeric($dato['infantiles']['total'])) {
            return (float)$dato['infantiles']['total'];
        }

        if ($isSieteAnnos && isset($dato['siete_annos']['total']) && is_numeric($dato['siete_annos']['total'])) {
            return (float)$dato['siete_annos']['total'];
        }

        if (isset($dato['total']) && is_numeric($dato['total'])) {
            return (float)$dato['total'];
        }

        return null;
    }

    private function buildMemberIdentityKey($member)
    {
        $jineteLicencia = mb_strtoupper(trim((string)$member['jinete_licencia']), 'UTF-8');
        $caballoLicencia = mb_strtoupper(trim((string)$member['caballo_licencia']), 'UTF-8');
        if ($jineteLicencia !== '' && $caballoLicencia !== '') {
            return 'L|' . $jineteLicencia . '|' . $caballoLicencia;
        }

        $jineteNombre = $this->normalizeRiderName((string)$member['jinete_nombre']);
        $caballoNombre = $this->normalizeText((string)$member['caballo_nombre']);
        if ($jineteNombre !== '' && $caballoNombre !== '') {
            return 'N|' . $jineteNombre . '|' . $caballoNombre;
        }

        return null;
    }

    private function normalizeText($value)
    {
        $value = mb_strtolower(trim((string)$value), 'UTF-8');
        $value = strtr($value, [
            'á' => 'a',
            'é' => 'e',
            'í' => 'i',
            'ó' => 'o',
            'ú' => 'u',
            'ü' => 'u',
            'ñ' => 'n',
        ]);
        $value = preg_replace('/\s+/u', ' ', $value);

        return $value ?? '';
    }

    private function normalizeRiderName($value)
    {
        $value = $this->normalizeText($value);
        $value = preg_replace('/^((d|da|dna|dno|don|dona|sr|sra|mr|mrs|ms)\.?\s+)+/u', '', $value);
        $value = preg_replace('/\s+/u', ' ', $value);

        return $value ?? '';
    }
}
