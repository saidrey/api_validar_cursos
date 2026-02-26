CREATE DATABASE IF NOT EXISTS diplomas_db;
USE diplomas_db;

CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    rol ENUM('admin', 'usuario') DEFAULT 'usuario',
    activo TINYINT(1) DEFAULT 1,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE cursos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(200) NOT NULL,
    descripcion TEXT,
    duracion VARCHAR(50),
    instructor VARCHAR(100),
    precio DECIMAL(10,2),
    imagen VARCHAR(255),
    activo TINYINT(1) DEFAULT 1,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE diplomas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    curso_id INT NOT NULL,
    nombre_estudiante VARCHAR(100) NOT NULL,
    tipo_documento ENUM('CC', 'TI', 'CE', 'PA', 'NIT') NOT NULL DEFAULT 'CC',
    documento VARCHAR(50) NOT NULL,
    fecha_emision DATE NOT NULL,
    codigo_verificacion VARCHAR(100) UNIQUE NOT NULL,
    activo TINYINT(1) DEFAULT 1,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (curso_id) REFERENCES cursos(id)
);

INSERT INTO usuarios (nombre, email, password, rol) 
VALUES ('Admin', 'admin@diplomas.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');
