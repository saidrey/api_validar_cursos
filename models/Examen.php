<?php
class Examen {
    private $conn;
    private $table = 'examenes';

    public $id;
    public $usuario_id;
    public $email;
    public $curso_id;
    public $nota;
    public $fecha_presentacion;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function listarPorUsuario($usuario_id) {
        $query = "SELECT e.id, e.curso_id, e.nota, e.fecha_presentacion, c.nombre AS curso_nombre
                  FROM {$this->table} e
                  LEFT JOIN cursos c ON e.curso_id = c.id
                  WHERE e.usuario_id = :usuario_id
                  ORDER BY e.fecha_presentacion DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt;
    }

    public function crear() {
        $query = "INSERT INTO {$this->table} (usuario_id, email, curso_id, nota, fecha_presentacion)
                  VALUES (:usuario_id, :email, :curso_id, :nota, NOW())";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':usuario_id', $this->usuario_id, PDO::PARAM_INT);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':curso_id', $this->curso_id, PDO::PARAM_INT);
        $stmt->bindParam(':nota', $this->nota);
        return $stmt->execute();
    }
}
