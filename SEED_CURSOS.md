# Insertar Cursos de Ejemplo

Para poblar la base de datos con cursos de ejemplo, ejecuta desde tu máquina host:

```bash
docker exec -i diplomas-mysql mysql -u diplomas_user -pdiplomas_pass diplomas_db < backend/seed_cursos.sql
```

O accede a phpMyAdmin en http://localhost:8081 y ejecuta el contenido de `seed_cursos.sql`
