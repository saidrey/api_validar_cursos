<?php
class Curso {
    private $conn;
    private $table = 'cursos';

    public $id;
    public $nombre;
    public $descripcion;
    public $resumen;
    public $duracion;
    public $instructor;
    public $precio;
    public $imagen;
    public $activo;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function crear() {
        $query = "INSERT INTO {$this->table} (nombre, descripcion, resumen, duracion, instructor, precio, imagen) 
                  VALUES (:nombre, :descripcion, :resumen, :duracion, :instructor, :precio, :imagen)";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':nombre', $this->nombre);
        $stmt->bindParam(':descripcion', $this->descripcion);
        $stmt->bindParam(':resumen', $this->resumen);
        $stmt->bindParam(':duracion', $this->duracion);
        $stmt->bindParam(':instructor', $this->instructor);
        $stmt->bindParam(':precio', $this->precio);
        $stmt->bindParam(':imagen', $this->imagen);

        return $stmt->execute();
    }

    public function leer() {
        $query = "SELECT * FROM {$this->table} WHERE activo = 1 ORDER BY fecha_creacion DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function leerUno() {
        $query = "SELECT * FROM {$this->table} WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function actualizar() {
        $query = "UPDATE {$this->table} SET
                  nombre = :nombre,
                  descripcion = :descripcion,
                  resumen = :resumen,
                  duracion = :duracion,
                  instructor = :instructor,
                  precio = :precio,
                  imagen = :imagen,
                  activo = :activo
                  WHERE id = :id";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':nombre', $this->nombre);
        $stmt->bindParam(':descripcion', $this->descripcion);
        $stmt->bindParam(':resumen', $this->resumen);
        $stmt->bindParam(':duracion', $this->duracion);
        $stmt->bindParam(':instructor', $this->instructor);
        $stmt->bindParam(':precio', $this->precio);
        $stmt->bindParam(':imagen', $this->imagen);
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
}
