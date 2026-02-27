-- Agregar campo preguntas (JSON) a la tabla cursos
-- Ejecutar en phpMyAdmin o cliente MySQL

ALTER TABLE cursos ADD COLUMN preguntas JSON NULL;
