-- Agregar campo tipo_documento a la tabla diplomas
ALTER TABLE diplomas 
ADD COLUMN tipo_documento ENUM('CC', 'TI', 'CE', 'PA', 'NIT') NOT NULL DEFAULT 'CC' 
AFTER nombre_estudiante;

-- CC: Cédula de Ciudadanía
-- TI: Tarjeta de Identidad
-- CE: Cédula de Extranjería
-- PA: Pasaporte
-- NIT: Número de Identificación Tributaria
