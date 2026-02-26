# Backend - Diplomas App

API REST en PHP 8.2 para gestión de cursos y diplomas.

## Estructura

```
backend/
├── config/
│   └── Database.php          # Configuración de conexión PDO
├── models/
│   ├── Usuario.php           # Modelo de usuarios
│   ├── Curso.php             # Modelo de cursos
│   └── Diploma.php           # Modelo de diplomas
├── controllers/
│   ├── usuarios.php          # CRUD usuarios
│   ├── cursos.php            # CRUD cursos
│   ├── diplomas.php          # CRUD diplomas
│   ├── login.php             # Autenticación
│   ├── validar.php           # Validación de diplomas
│   └── contacto.php          # Envío de correos
├── database.sql              # Script de base de datos
└── index.php                 # Documentación API
```

## Endpoints

### Usuarios
- `GET /controllers/usuarios.php` - Listar usuarios
- `GET /controllers/usuarios.php?id=1` - Obtener usuario
- `POST /controllers/usuarios.php` - Crear usuario
- `PUT /controllers/usuarios.php` - Actualizar usuario
- `DELETE /controllers/usuarios.php` - Eliminar usuario

### Cursos
- `GET /controllers/cursos.php` - Listar cursos
- `GET /controllers/cursos.php?id=1` - Obtener curso
- `POST /controllers/cursos.php` - Crear curso
- `PUT /controllers/cursos.php` - Actualizar curso
- `DELETE /controllers/cursos.php` - Eliminar curso

### Diplomas
- `GET /controllers/diplomas.php` - Listar diplomas
- `GET /controllers/diplomas.php?id=1` - Obtener diploma
- `POST /controllers/diplomas.php` - Crear diploma
- `PUT /controllers/diplomas.php` - Actualizar diploma
- `DELETE /controllers/diplomas.php` - Eliminar diploma

### Autenticación
- `POST /controllers/login.php` - Login
  ```json
  {
    "email": "admin@diplomas.com",
    "password": "password"
  }
  ```

### Validación
- `GET /controllers/validar.php?documento=12345678` - Validar por documento
- `GET /controllers/validar.php?codigo=ABC123` - Validar por código

### Contacto
- `POST /controllers/contacto.php` - Enviar mensaje
  ```json
  {
    "nombre": "Juan Pérez",
    "email": "juan@example.com",
    "telefono": "123456789",
    "mensaje": "Consulta sobre cursos"
  }
  ```

## Inicializar Base de Datos

```bash
docker exec -it diplomas-mysql mysql -u diplomas_user -p < database.sql
# Password: diplomas_pass
```

## Usuario por Defecto

- Email: `admin@diplomas.com`
- Password: `password`
