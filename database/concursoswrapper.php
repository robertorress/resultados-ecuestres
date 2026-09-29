<?php

require_once __DIR__ . '/projectpath.php';

/**
 * Clase Singleton para la gestión centralizada de concursos ecuestres.
 */
class CConcursosWrapper
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
    public static function getInstance($path = 'config.xml')
    {
        if (self::$instance === null) {
            self::$instance = new CConcursosWrapper($path);
        }
        return self::$instance;
    }

    /**
     * Devuelve todos los concursos de un año específico.
     * Ejemplo: $lista = $wrapper->getConcursosByAnio(2026);
     */
    public function getConcursosByAnio($anio)
    {
        try {
            $tagAnio = "a" . trim($anio);
            if (!isset($this->xml->{$tagAnio})) {
                throw new Exception("No existen datos para el año: $anio");
            }
            return $this->xml->{$tagAnio}->concursos->concurso;
        } catch (Exception $e) {
            throw new Exception("Error al filtrar por año: " . $e->getMessage());
        }
    }

    /**
     * Busca un concurso por su ID único mediante XPath.
     * Ejemplo: $concurso = $wrapper->getConcursoById(1);
     */
    public function getConcursoById($id)
    {
        try {
            $id = filter_var($id, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
            if ($id === false) {
                throw new Exception('El identificador del concurso no es válido.');
            }

            $res = $this->xml->xpath("//concurso[id='$id']");
            if (!$res || empty($res)) {
                throw new Exception("Concurso con ID $id no localizado.");
            }
            return $res[0]; // Retorna el primer nodo encontrado
        } catch (Exception $e) {
            throw new Exception("Error en la búsqueda por ID: " . $e->getMessage());
        }
    }

    /**
     * Devuelve el nombre del concurso.
     * Ejemplo: echo $wrapper->getNombre($concurso);
     */
    public function getNombre($concurso)
    {
        try {
            if (!isset($concurso->nombre)) throw new Exception("Nodo 'nombre' ausente.");
            return (string)$concurso->nombre;
        } catch (Exception $e) {
            return "Nombre no disponible";
        }
    }

    /**
     * Devuelve la fecha del concurso.
     * Ejemplo: echo $wrapper->getFecha($concurso);
     */
    public function getFecha($concurso)
    {
        try {
            if (!isset($concurso->fecha)) throw new Exception("Nodo 'fecha' ausente.");
            return (string)$concurso->fecha;
        } catch (Exception $e) {
            return "Fecha no disponible";
        }
    }
}
