-- Agregar campo resumen a la tabla cursos
ALTER TABLE cursos ADD COLUMN resumen TEXT AFTER descripcion;
