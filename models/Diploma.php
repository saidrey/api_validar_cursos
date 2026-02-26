<?php
class Diploma {
    private $conn;
    private $table = 'diplomas';

    public $id;
    public $curso_id;
    public $nombre_estudiante;
    public $tipo_documento;
    public $documento;
    public $fecha_emision;
    public $codigo_verificacion;
    public $activo;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function crear() {
        $query = "INSERT INTO {$this->table} (curso_id, nombre_estudiante, tipo_documento, documento, fecha_emision, codigo_verificacion) 
                  VALUES (:curso_id, :nombre_estudiante, :tipo_documento, :documento, :fecha_emision, :codigo_verificacion)";
        $stmt = $this->conn->prepare($query);

        $this->codigo_verificacion = $this->generarCodigo();

        $stmt->bindParam(':curso_id', $this->curso_id);
        $stmt->bindParam(':nombre_estudiante', $this->nombre_estudiante);
        $stmt->bindParam(':tipo_documento', $this->tipo_documento);
        $stmt->bindParam(':documento', $this->documento);
        $stmt->bindParam(':fecha_emision', $this->fecha_emision);
        $stmt->bindParam(':codigo_verificacion', $this->codigo_verificacion);

        return $stmt->execute();
    }

    public function leer() {
        $query = "SELECT d.*, c.nombre as curso_nombre 
                  FROM {$this->table} d 
                  LEFT JOIN cursos c ON d.curso_id = c.id 
                  WHERE d.activo = 1 
                  ORDER BY d.fecha_creacion DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function leerUno() {
        $query = "SELECT d.*, c.nombre as curso_nombre 
                  FROM {$this->table} d 
                  LEFT JOIN cursos c ON d.curso_id = c.id 
                  WHERE d.id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function validarPorDocumento() {
        $query = "SELECT d.*, c.nombre as curso_nombre, c.instructor 
                  FROM {$this->table} d 
                  LEFT JOIN cursos c ON d.curso_id = c.id 
                  WHERE d.tipo_documento = :tipo_documento AND d.documento = :documento AND d.activo = 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':tipo_documento', $this->tipo_documento);
        $stmt->bindParam(':documento', $this->documento);
        $stmt->execute();
        return $stmt;
    }

    public function validarPorCodigo() {
        $query = "SELECT d.*, c.nombre as curso_nombre, c.instructor 
                  FROM {$this->table} d 
                  LEFT JOIN cursos c ON d.curso_id = c.id 
                  WHERE d.codigo_verificacion = :codigo AND d.activo = 1 LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':codigo', $this->codigo_verificacion);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function actualizar() {
        $query = "UPDATE {$this->table} SET
                  curso_id = :curso_id,
                  nombre_estudiante = :nombre_estudiante,
                  tipo_documento = :tipo_documento,
                  documento = :documento,
                  fecha_emision = :fecha_emision,
                  activo = :activo
                  WHERE id = :id";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':curso_id', $this->curso_id);
        $stmt->bindParam(':nombre_estudiante', $this->nombre_estudiante);
        $stmt->bindParam(':tipo_documento', $this->tipo_documento);
        $stmt->bindParam(':documento', $this->documento);
        $stmt->bindParam(':fecha_emision', $this->fecha_emision);
        $stmt->bindParam(':activo', $this->activo);
        $stmt->bindParam(':id', $this->id);

        return $stmt->execute();
    }

    public function eliminar() {
        $query = "UPDATE {$this->table} SET activo = 0 WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);
        return $stmt->execute();
    }

    private function generarCodigo() {
        return strtoupper(bin2hex(random_bytes(8)));
    }
}
