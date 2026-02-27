<?php
class Curso {
    private $conn;
    private $table = 'cursos';

    public $id;
    public $nombre;
    public $descripcion;
    public $contenido_markdown;
    public $video_url_1;
    public $video_url_2;
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
        $query = "INSERT INTO {$this->table} (nombre, descripcion, contenido_markdown, video_url_1, video_url_2, resumen, duracion, instructor, precio, imagen)
                  VALUES (:nombre, :descripcion, :contenido_markdown, :video_url_1, :video_url_2, :resumen, :duracion, :instructor, :precio, :imagen)";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':nombre', $this->nombre);
        $stmt->bindParam(':descripcion', $this->descripcion);
        $stmt->bindParam(':contenido_markdown', $this->contenido_markdown);
        $stmt->bindParam(':video_url_1', $this->video_url_1);
        $stmt->bindParam(':video_url_2', $this->video_url_2);
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
                  contenido_markdown = :contenido_markdown,
                  video_url_1 = :video_url_1,
                  video_url_2 = :video_url_2,
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
        $stmt->bindParam(':contenido_markdown', $this->contenido_markdown);
        $stmt->bindParam(':video_url_1', $this->video_url_1);
        $stmt->bindParam(':video_url_2', $this->video_url_2);
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
