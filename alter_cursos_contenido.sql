-- Migración: Agregar campos de contenido extendido a la tabla cursos
-- Ejecutar en producción y en local antes de usar la nueva funcionalidad

ALTER TABLE cursos
  ADD COLUMN contenido_markdown LONGTEXT NULL AFTER descripcion,
  ADD COLUMN video_url_1 VARCHAR(500) NULL AFTER contenido_markdown,
  ADD COLUMN video_url_2 VARCHAR(500) NULL AFTER video_url_1;
