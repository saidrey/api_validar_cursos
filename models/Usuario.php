<?php
class Usuario {
    private $conn;
    private $table = 'usuarios';

    public $id;
    public $nombre;
    public $email;
    public $password;
    public $rol;
    public $activo;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function crear() {
        $query = "INSERT INTO {$this->table} (nombre, email, password, rol) VALUES (:nombre, :email, :password, :rol)";
        $stmt = $this->conn->prepare($query);

        $this->password = password_hash($this->password, PASSWORD_BCRYPT);

        $stmt->bindParam(':nombre', $this->nombre);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':password', $this->password);
        $stmt->bindParam(':rol', $this->rol);

        return $stmt->execute();
    }

    public function leer() {
        $query = "SELECT id, nombre, email, rol, activo, fecha_creacion FROM {$this->table} WHERE activo = 1 ORDER BY fecha_creacion DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function leerUno() {
        $query = "SELECT id, nombre, email, rol, activo FROM {$this->table} WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function actualizar() {
        $query = "UPDATE {$this->table} SET nombre = :nombre, email = :email, rol = :rol, activo = :activo WHERE id = :id";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':nombre', $this->nombre);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':rol', $this->rol);
        $stmt->bindParam(':activo', $this->activo);
        $stmt->bindParam(':id', $this->id);

        return $stmt->execute();
    }

    public function cambiarPassword() {
        $query = "UPDATE {$this->table} SET password = :password WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $hashed = password_hash($this->password, PASSWORD_BCRYPT);
        $stmt->bindParam(':password', $hashed);
        $stmt->bindParam(':id', $this->id);
        return $stmt->execute();
    }

    public function eliminar() {
        $query = "UPDATE {$this->table} SET activo = 0 WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);
        return $stmt->execute();
    }

    public function login() {
        $query = "SELECT id, nombre, email, password, rol FROM {$this->table} WHERE email = :email AND activo = 1 LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $this->email);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function emailExiste($email): bool {
        $query = "SELECT id FROM {$this->table} WHERE email = :email LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }
}
