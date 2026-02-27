-- Tabla de exámenes presentados por usuarios
CREATE TABLE IF NOT EXISTS examenes (
  id INT AUTO_INCREMENT PRIMARY KEY,
  usuario_id INT NOT NULL,
  curso_id INT NOT NULL,
  nota DECIMAL(5,2) NOT NULL,
  fecha_presentacion DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (usuario_id) REFERENCES usuarios(id),
  FOREIGN KEY (curso_id) REFERENCES cursos(id)
);
