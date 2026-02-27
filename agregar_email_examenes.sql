-- Agregar campo email a la tabla examenes (ejecutar si la tabla ya existe)
ALTER TABLE examenes ADD COLUMN email VARCHAR(255) NOT NULL DEFAULT '';
