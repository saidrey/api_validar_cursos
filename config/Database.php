<?php
class Database {
    private $host;
    private $db_name;
    private $username;
    private $password;

    public function __construct() {
        $this->host     = getenv('DB_HOST')  ?: 'localhost';
        $this->db_name  = getenv('DB_NAME')  ?: 'diplomas_db';
        $this->username = getenv('DB_USER')  ?: 'diplomas_user';
        $this->password = getenv('DB_PASS')  ?: 'diplomas_pass';
    }
    private $conn;

    public function getConnection() {
        $this->conn = null;
        try {
            $this->conn = new PDO(
                "mysql:host={$this->host};dbname={$this->db_name};charset=utf8",
                $this->username,
                $this->password
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $e) {
            echo "Error de conexión: " . $e->getMessage();
        }
        return $this->conn;
    }
}
