<?php
class CorreoEnviado {
    private $conn;
    private $table = 'correos_enviados';

    public $id;
    public $destinatario_email;
    public $destinatario_nombre;
    public $destinatario_telefono;
    public $asunto;
    public $cuerpo;
    public $curso_id;
    public $fecha_envio;
    public $estado;
    public $error_mensaje;

    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Registrar correo enviado exitosamente
     */
    public function registrarEnviado() {
        $query = "INSERT INTO {$this->table} 
                  (destinatario_email, destinatario_nombre, destinatario_telefono, asunto, cuerpo, curso_id, estado) 
                  VALUES (:email, :nombre, :telefono, :asunto, :cuerpo, :curso_id, 'enviado')";
        
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':email', $this->destinatario_email);
        $stmt->bindParam(':nombre', $this->destinatario_nombre);
        $stmt->bindParam(':telefono', $this->destinatario_telefono);
        $stmt->bindParam(':asunto', $this->asunto);
        $stmt->bindParam(':cuerpo', $this->cuerpo);
        $stmt->bindParam(':curso_id', $this->curso_id);

        return $stmt->execute();
    }

    /**
     * Registrar correo fallido
     */
    public function registrarFallido() {
        $query = "INSERT INTO {$this->table} 
                  (destinatario_email, destinatario_nombre, destinatario_telefono, asunto, cuerpo, curso_id, estado, error_mensaje) 
                  VALUES (:email, :nombre, :telefono, :asunto, :cuerpo, :curso_id, 'fallido', :error)";
        
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':email', $this->destinatario_email);
        $stmt->bindParam(':nombre', $this->destinatario_nombre);
        $stmt->bindParam(':telefono', $this->destinatario_telefono);
        $stmt->bindParam(':asunto', $this->asunto);
        $stmt->bindParam(':cuerpo', $this->cuerpo);
        $stmt->bindParam(':curso_id', $this->curso_id);
        $stmt->bindParam(':error', $this->error_mensaje);

        return $stmt->execute();
    }

    /**
     * Obtener todos los correos enviados
     */
    public function leer() {
        $query = "SELECT ce.*, c.nombre as curso_nombre 
                  FROM {$this->table} ce
                  LEFT JOIN cursos c ON ce.curso_id = c.id
                  ORDER BY ce.fecha_envio DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    /**
     * Obtener correos por curso
     */
    public function leerPorCurso() {
        $query = "SELECT ce.*, c.nombre as curso_nombre 
                  FROM {$this->table} ce
                  LEFT JOIN cursos c ON ce.curso_id = c.id
                  WHERE ce.curso_id = :curso_id
                  ORDER BY ce.fecha_envio DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':curso_id', $this->curso_id);
        $stmt->execute();
        return $stmt;
    }

    /**
     * Eliminar un correo por ID (hard delete)
     */
    public function eliminar() {
        $query = "DELETE FROM {$this->table} WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * Obtener estadísticas de correos
     */
    public function obtenerEstadisticas() {
        $query = "SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN estado = 'enviado' THEN 1 ELSE 0 END) as enviados,
                    SUM(CASE WHEN estado = 'fallido' THEN 1 ELSE 0 END) as fallidos,
                    DATE(fecha_envio) as fecha
                  FROM {$this->table}
                  GROUP BY DATE(fecha_envio)
                  ORDER BY fecha DESC
                  LIMIT 30";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }
}
