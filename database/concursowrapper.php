<?php

require_once __DIR__ . '/projectpath.php';

/**
 * Clase Singleton para la gestión centralizada de un concurso ecuestres.
 */
class CConcursoWrapper
{
    private static $instance = null;
    private $xml;

    /**
     * Constructor privado: Carga y valida el archivo XML.
     * @param string $path Ruta al archivo XML.
     * @throws Exception Si el archivo no existe o el formato es inválido.
     */
    private function __construct($path)
    {
        $path = $path === 'config.xml' ? CProjectPath::resolveCatalog() : CProjectPath::resolve($path);
        if (!file_exists($path)) {
            throw new Exception("Archivo XML no encontrado en la ruta: $path");
        }

        $this->xml = @simplexml_load_file($path);
        if ($this->xml === false) {
            throw new Exception("El archivo XML tiene errores de sintaxis.");
        }
    }

    /**
     * Obtiene la instancia única de la clase (Singleton).
     * Ejemplo: $wrapper = ConcursosWrapper::getInstance('concursos.xml');
     */
    public static function getInstance($path = 'concurso.xml')
    {
        if (self::$instance === null) {
            self::$instance = new CConcursoWrapper($path);
        }
        return self::$instance;
    }

    public function GetConcurso()
    {
        return $this->xml->concurso[0];
    }

    public function GetLogo()
    {
        return $this->xml->logo[0];
    }

    public function GetOrdenSalida($id)
    {
        return $this->xml->ordensalidas->ordensalida[$id];
    }

    public function GetOrdenSalidas()
    {
        return $this->xml->ordensalidas->ordensalida;
    }

    public function GetSorteoOrdenSalida()
    {
        return $this->xml->ordensalidas->sorteo;
    }

    public function GetPlanos()
    {
        if (isset($this->xml->planos->plano)) {
            return $this->xml->planos->plano;
        }

        return [];
    }

    public function GetAnuncios()
    {
        if (!isset($this->xml->anuncios)) {
            return null;
        }

        return $this->xml->anuncios->anuncio;
    }

    public function GetPruebas()
    {
        return $this->xml->pruebas->prueba;
    }

    public function GetEquipo()
    {
        if (isset($this->xml->equipos)) {
            return $this->xml->equipos[0];
        }

        return $this->xml->equipo[0] ?? null;
    }

    public function GetFecha()
    {
        return $this->xml->fecha[0];
    }

    public function GetUbicacionNombre()
    {
        if (isset($this->xml->ubicacion->nombre) && trim((string)$this->xml->ubicacion->nombre) !== '') {
            return $this->xml->ubicacion->nombre[0];
        }

        return null;
    }

    public function GetUbicacionVisible()
    {
        if (!isset($this->xml->ubicacion->visible)) {
            return true;
        }

        $visible = mb_strtolower(trim((string)$this->xml->ubicacion->visible), 'UTF-8');
        $visible = strtr($visible, [
            'á' => 'a',
            'é' => 'e',
            'í' => 'i',
            'ó' => 'o',
            'ú' => 'u',
        ]);

        return in_array($visible, ['si', 'yes'], true);
    }

    public function GetUbicacionLatitud()
    {
        if (isset($this->xml->ubicacion->coordenadas->latitud) && trim((string)$this->xml->ubicacion->coordenadas->latitud) !== '') {
            return $this->xml->ubicacion->coordenadas->latitud[0];
        }

        return null;
    }

    public function GetUbicacionLongitud()
    {
        if (isset($this->xml->ubicacion->coordenadas->longitud) && trim((string)$this->xml->ubicacion->coordenadas->longitud) !== '') {
            return $this->xml->ubicacion->coordenadas->longitud[0];
        }

        return null;
    }
}
