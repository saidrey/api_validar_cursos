# BACKEND - Diplomas App

API REST desarrollada en PHP 8.2 para la gestión de cursos y diplomas.

## 📋 Tabla de Contenidos

- [Tecnologías](#tecnologías)
- [Estructura del Proyecto](#estructura-del-proyecto)
- [Base de Datos](#base-de-datos)
- [Endpoints](#endpoints)
- [Instalación](#instalación)
- [Configuración](#configuración)
- [Pruebas](#pruebas)

---

## 🛠 Tecnologías

- **PHP**: 8.2+
- **Base de datos**: MySQL 8.0
- **Librerías**: PHPMailer 6.9
- **Servidor**: Apache 2.4
- **Estándares**: PSR-12

---

## 📁 Estructura del Proyecto

```
backend/
├── config/
│   ├── Database.php          # Conexión PDO a MySQL
│   └── mail.php              # Configuración de correo
├── models/
│   ├── Usuario.php           # Modelo de usuarios
│   ├── Curso.php             # Modelo de cursos
│   └── Diploma.php           # Modelo de diplomas
├── controllers/
│   ├── usuarios.php          # CRUD de usuarios
│   ├── cursos.php            # CRUD de cursos
│   ├── diplomas.php          # CRUD de diplomas
│   ├── login.php             # Autenticación
│   ├── validar.php           # Validación de diplomas
│   └── contacto.php          # Envío de correos
├── logs/
│   └── contacto.log          # Log de mensajes de contacto
├── database.sql              # Script de creación de BD
├── alter_diplomas.sql        # Script de actualización de BD
├── index.php                 # Documentación de la API
├── composer.json             # Dependencias PHP
└── .env.example              # Variables de entorno
```

---

## 🗄 Base de Datos

### Tablas

#### **usuarios**
```sql
- id (INT, PK, AUTO_INCREMENT)
- nombre (VARCHAR 100)
- email (VARCHAR 100, UNIQUE)
- password (VARCHAR 255, HASHED)
- rol (ENUM: 'admin', 'usuario')
- activo (TINYINT)
- fecha_creacion (TIMESTAMP)
```

#### **cursos**
```sql
- id (INT, PK, AUTO_INCREMENT)
- nombre (VARCHAR 200)
- descripcion (TEXT)
- duracion (VARCHAR 50)
- instructor (VARCHAR 100)
- precio (DECIMAL 10,2)
- imagen (VARCHAR 255)
- activo (TINYINT)
- fecha_creacion (TIMESTAMP)
```

#### **diplomas**
```sql
- id (INT, PK, AUTO_INCREMENT)
- curso_id (INT, FK → cursos.id)
- nombre_estudiante (VARCHAR 100)
- tipo_documento (ENUM: 'CC', 'TI', 'CE', 'PA', 'NIT')
- documento (VARCHAR 50)
- fecha_emision (DATE)
- codigo_verificacion (VARCHAR 100, UNIQUE)
- activo (TINYINT)
- fecha_creacion (TIMESTAMP)
```

### Tipos de Documento (Colombia)
- **CC**: Cédula de Ciudadanía
- **TI**: Tarjeta de Identidad
- **CE**: Cédula de Extranjería
- **PA**: Pasaporte
- **NIT**: Número de Identificación Tributaria

### Inicializar Base de Datos

```bash
# Crear tablas desde cero
docker exec -it diplomas-mysql mysql -u diplomas_user -p < database.sql

# O actualizar tabla existente (agregar tipo_documento)
docker exec -it diplomas-mysql mysql -u diplomas_user -p < alter_diplomas.sql
```

**Usuario por defecto:**
- Email: `admin@diplomas.com`
- Password: `password`

---

## 🔌 Endpoints

### Base URL
```
http://localhost:8080
```

### Autenticación

#### Login
```http
POST /controllers/login.php
Content-Type: application/json

{
  "email": "admin@diplomas.com",
  "password": "password"
}
```

### Usuarios

```http
GET    /controllers/usuarios.php           # Listar todos
GET    /controllers/usuarios.php?id=1      # Obtener uno
POST   /controllers/usuarios.php           # Crear
PUT    /controllers/usuarios.php           # Actualizar
DELETE /controllers/usuarios.php           # Eliminar (soft delete)
```

**Ejemplo POST:**
```json
{
  "nombre": "Juan Pérez",
  "email": "juan@example.com",
  "password": "123456",
  "rol": "usuario"
}
```

### Cursos

```http
GET    /controllers/cursos.php             # Listar todos
GET    /controllers/cursos.php?id=1        # Obtener uno
POST   /controllers/cursos.php             # Crear
PUT    /controllers/cursos.php             # Actualizar
DELETE /controllers/cursos.php             # Eliminar (soft delete)
```

**Ejemplo POST:**
```json
{
  "nombre": "Desarrollo Web con PHP",
  "descripcion": "Curso completo de PHP 8",
  "duracion": "40 horas",
  "instructor": "María González",
  "precio": 299.99,
  "imagen": "curso-php.jpg"
}
```

### Diplomas

```http
GET    /controllers/diplomas.php           # Listar todos
GET    /controllers/diplomas.php?id=1      # Obtener uno
POST   /controllers/diplomas.php           # Crear
PUT    /controllers/diplomas.php           # Actualizar
DELETE /controllers/diplomas.php           # Eliminar (soft delete)
```

**Ejemplo POST:**
```json
{
  "curso_id": 1,
  "nombre_estudiante": "Carlos Rodríguez",
  "tipo_documento": "CC",
  "documento": "12345678",
  "fecha_emision": "2024-01-15"
}
```

### Validación de Diplomas

```http
# Validar por tipo de documento y número
GET /controllers/validar.php?tipo_documento=CC&documento=12345678

# Validar por código de verificación
GET /controllers/validar.php?codigo=ABC123DEF456
```

### Contacto

```http
POST /controllers/contacto.php
Content-Type: application/json

{
  "nombre": "Ana López",
  "email": "ana@example.com",
  "telefono": "3001234567",
  "mensaje": "Consulta sobre cursos"
}
```

---

## 🚀 Instalación

### 1. Instalar Dependencias

```bash
docker exec -it diplomas-backend composer update
```

### 2. Inicializar Base de Datos

```bash
docker exec -it diplomas-mysql mysql -u diplomas_user -p < /var/www/html/database.sql
# Password: diplomas_pass
```

### 3. Verificar Instalación

Accede a: `http://localhost:8080/index.php`

Deberías ver la documentación de la API en formato JSON.

---

## ⚙️ Configuración

### Conexión a Base de Datos

Archivo: `config/Database.php`

```php
private $host = 'mysql';
private $db_name = 'diplomas_db';
private $username = 'diplomas_user';
private $password = 'diplomas_pass';
```

### Configuración de Correo

#### Desarrollo (Actual)
Los mensajes se guardan en `logs/contacto.log`

#### Producción (Hostinger)

1. Copia el archivo de ejemplo:
```bash
cp .env.example .env
```

2. Edita `.env` con tus credenciales:
```env
MAIL_MODE=production
SMTP_HOST=smtp.hostinger.com
SMTP_PORT=587
SMTP_USERNAME=info@tudominio.com
SMTP_PASSWORD=tu_password
SMTP_FROM_EMAIL=info@tudominio.com
SMTP_FROM_NAME=Diplomas App
CONTACT_EMAIL=contacto@tudominio.com
```

3. Configura las variables de entorno en Apache o `.htaccess`

Ver más detalles en: `MAIL_CONFIG.md`

---

## 🧪 Pruebas

### Con Postman

1. Importa la colección: `Diplomas_API.postman_collection.json`
2. Importa el environment: `Diplomas_Environment.postman_environment.json`
3. Selecciona el environment "Diplomas App - Local"
4. Ejecuta los requests

### Orden Recomendado de Pruebas

1. **GET** `/index.php` - Verificar API
2. **POST** `/controllers/login.php` - Login
3. **POST** `/controllers/cursos.php` - Crear curso
4. **GET** `/controllers/cursos.php` - Listar cursos
5. **POST** `/controllers/diplomas.php` - Crear diploma
6. **GET** `/controllers/validar.php?tipo_documento=CC&documento=12345678` - Validar
7. **POST** `/controllers/contacto.php` - Enviar mensaje

---

## 🔒 Características de Seguridad

- ✅ **Passwords hasheados** con bcrypt
- ✅ **PDO con prepared statements** (previene SQL injection)
- ✅ **Validación de inputs** en todos los endpoints
- ✅ **CORS habilitado** para desarrollo
- ✅ **Soft deletes** (campo `activo`)
- ✅ **Códigos de verificación únicos** para diplomas

---

## 📝 Notas Técnicas

### Patrón de Arquitectura
- **MVC simplificado**: Models, Controllers, Config
- **RESTful API**: Métodos HTTP estándar (GET, POST, PUT, DELETE)
- **JSON**: Todas las respuestas en formato JSON

### Estándares de Código
- **PSR-12**: Coding standards
- **Nombres en español**: Variables y funciones del dominio
- **Comentarios mínimos**: Código auto-explicativo

### Soft Deletes
Los registros no se eliminan físicamente, solo se marca `activo = 0`:
```php
public function eliminar() {
    $query = "UPDATE {$this->table} SET activo = 0 WHERE id = :id";
    // ...
}
```

### Generación de Códigos de Verificación
```php
private function generarCodigo() {
    return strtoupper(bin2hex(random_bytes(8)));
}
// Genera: ABC123DEF456 (16 caracteres hexadecimales)
```

---

## 🐛 Troubleshooting

### Error 500 en todos los endpoints
- Verifica que PHP esté funcionando: `docker exec diplomas-backend php -v`
- Revisa logs: `docker logs diplomas-backend`
- Elimina `.htaccess` si causa problemas

### Error de conexión a base de datos
- Verifica que MySQL esté corriendo: `docker ps | grep mysql`
- Prueba la conexión: `docker exec -it diplomas-mysql mysql -u diplomas_user -p`

### PHPMailer no funciona
- Verifica instalación: `docker exec diplomas-backend ls vendor/phpmailer`
- Ejecuta: `docker exec diplomas-backend composer update`

---

## 📚 Recursos Adicionales

- `README.md` - Documentación general del proyecto
- `MAIL_CONFIG.md` - Configuración detallada de correo
- `POSTMAN_GUIDE.md` - Guía de uso de Postman
- `database.sql` - Script de creación de BD
- `alter_diplomas.sql` - Script de actualización de BD

---

## 🔄 Próximos Pasos

- [ ] Implementar JWT para autenticación
- [ ] Agregar middleware de autorización
- [ ] Implementar rate limiting
- [ ] Agregar logs de auditoría
- [ ] Crear tests unitarios
- [ ] Documentación con Swagger/OpenAPI

---

**Desarrollado con PHP 8.2 + MySQL 8.0**
