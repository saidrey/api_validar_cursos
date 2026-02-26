# Guía de Uso - Colección Postman

## Importar en Postman

1. Abre Postman
2. Click en **Import** (esquina superior izquierda)
3. Arrastra los archivos:
   - `Diplomas_API.postman_collection.json`
   - `Diplomas_Environment.postman_environment.json`
4. Selecciona el environment "Diplomas App - Local" en el dropdown superior derecho

## Orden de Pruebas Recomendado

### 1. Verificar API
```
GET Documentación API
```

### 2. Autenticación
```
POST Login
- Email: admin@diplomas.com
- Password: password
```

### 3. Crear Curso
```
POST Crear Curso
```
Guarda el ID del curso creado para usarlo en diplomas.

### 4. Listar Cursos
```
GET Listar Cursos
GET Obtener Curso (id=1)
```

### 5. Crear Usuario
```
POST Crear Usuario
```

### 6. Crear Diploma
```
POST Crear Diploma
```
Usa el curso_id del paso 3.

### 7. Validar Diploma
```
GET Validar por Documento (documento=12345678)
GET Validar por Código (usa el código generado)
```

### 8. Enviar Contacto
```
POST Enviar Mensaje
```

## Variables de Entorno

- `base_url`: http://localhost:8080
- `admin_email`: admin@diplomas.com
- `admin_password`: password

## Notas Importantes

- Asegúrate de que Docker esté corriendo
- La base de datos debe estar inicializada con `database.sql`
- Los endpoints DELETE hacen soft delete (activo=0)
- El código de verificación de diplomas se genera automáticamente

## Ejemplos de Respuestas

### Login Exitoso
```json
{
  "mensaje": "Login exitoso",
  "usuario": {
    "id": 1,
    "nombre": "Admin",
    "email": "admin@diplomas.com",
    "rol": "admin"
  }
}
```

### Diploma Válido
```json
{
  "valido": true,
  "diplomas": [
    {
      "id": 1,
      "curso_nombre": "Desarrollo Web con PHP",
      "nombre_estudiante": "Carlos Rodríguez",
      "documento": "12345678",
      "fecha_emision": "2024-01-15",
      "codigo_verificacion": "ABC123DEF456"
    }
  ]
}
```
