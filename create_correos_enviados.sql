-- Tabla para registrar todos los correos enviados
CREATE TABLE IF NOT EXISTS correos_enviados (
    id INT AUTO_INCREMENT PRIMARY KEY,
    destinatario_email VARCHAR(255) NOT NULL,
    destinatario_nombre VARCHAR(255) NOT NULL,
    destinatario_telefono VARCHAR(20),
    asunto VARCHAR(500) NOT NULL,
    cuerpo TEXT NOT NULL,
    curso_id INT NULL,
    fecha_envio DATETIME DEFAULT CURRENT_TIMESTAMP,
    estado ENUM('enviado', 'fallido') DEFAULT 'enviado',
    error_mensaje TEXT NULL,
    INDEX idx_destinatario (destinatario_email),
    INDEX idx_curso (curso_id),
    INDEX idx_fecha (fecha_envio),
    FOREIGN KEY (curso_id) REFERENCES cursos(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
