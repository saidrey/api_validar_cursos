# Sistema de Registro de Correos Enviados

Sistema para registrar todos los correos enviados desde la aplicación en base de datos.

---

## 📦 Componentes Implementados

### 1. **Tabla de Base de Datos**

**Archivo**: `/backend/create_correos_enviados.sql`

```sql
CREATE TABLE correos_enviados (
    id INT AUTO_INCREMENT PRIMARY KEY,
    destinatario_email VARCHAR(255) NOT NULL,
    destinatario_nombre VARCHAR(255) NOT NULL,
    destinatario_telefono VARCHAR(20),
    asunto VARCHAR(500) NOT NULL,
    cuerpo TEXT NOT NULL,
    curso_id INT NULL,
    fecha_envio DATETIME DEFAULT CURRENT_TIMESTAMP,
    estado ENUM('enviado', 'fallido') DEFAULT 'enviado',
    error_mensaje TEXT NULL
);
```

**Campos:**
- `id`: Identificador único
- `destinatario_email`: Email del destinatario
- `destinatario_nombre`: Nombre del destinatario
- `destinatario_telefono`: Teléfono del destinatario
- `asunto`: Asunto del correo
- `cuerpo`: Contenido HTML del correo
- `curso_id`: ID del curso relacionado (nullable)
- `fecha_envio`: Fecha y hora de envío
- `estado`: 'enviado' o 'fallido'
- `error_mensaje`: Mensaje de error si falló

**Índices:**
- `idx_destinatario`: Búsqueda por email
- `idx_curso`: Filtrar por curso
- `idx_fecha`: Ordenar por fecha

---

### 2. **Modelo CorreoEnviado**

**Archivo**: `/backend/models/CorreoEnviado.php`

**Métodos:**

```php
// Registrar correo enviado exitosamente
$correo->registrarEnviado();

// Registrar correo fallido
$correo->registrarFallido();

// Obtener todos los correos
$stmt = $correo->leer();

// Obtener correos por curso
$correo->curso_id = 1;
$stmt = $correo->leerPorCurso();

// Obtener estadísticas
$stmt = $correo->obtenerEstadisticas();
```

---

### 3. **Integración en contacto.php**

**Flujo:**

```
1. Usuario envía formulario
2. Se genera correo con PHPMailer
3. Se intenta enviar:
   ├─ Éxito → Registrar en BD con estado 'enviado'
   └─ Error → Registrar en BD con estado 'fallido' + mensaje error
4. Responder al cliente
```

**Código:**

```php
// Modo producción
if($config['mode'] === 'production') {
    $mail->send();
    
    // Registrar en BD
    $correoEnviado = new CorreoEnviado($db);
    $correoEnviado->destinatario_email = $data->email;
    $correoEnviado->destinatario_nombre = $data->nombre;
    $correoEnviado->destinatario_telefono = $data->telefono;
    $correoEnviado->asunto = $mail->Subject;
    $correoEnviado->cuerpo = $mail->Body;
    $correoEnviado->curso_id = $data->curso_id;
    $correoEnviado->registrarEnviado();
}

// Si falla
catch (Exception $e) {
    $correoEnviado->error_mensaje = $e->getMessage();
    $correoEnviado->registrarFallido();
}
```

---

### 4. **Endpoint de Consulta (Admin)**

**Archivo**: `/backend/controllers/correos-enviados.php`

**Endpoints:**

```bash
# Obtener todos los correos
GET /controllers/correos-enviados.php
Header: Authorization: Bearer {token_admin}

# Obtener correos por curso
GET /controllers/correos-enviados.php?curso_id=1
Header: Authorization: Bearer {token_admin}

# Obtener estadísticas
GET /controllers/correos-enviados.php?estadisticas=true
Header: Authorization: Bearer {token_admin}
```

**Respuesta - Todos los correos:**
```json
[
  {
    "id": 1,
    "destinatario_email": "juan@example.com",
    "destinatario_nombre": "Juan Pérez",
    "destinatario_telefono": "3001234567",
    "asunto": "Información del Curso: Desarrollo Web",
    "cuerpo": "<html>...</html>",
    "curso_id": 1,
    "curso_nombre": "Desarrollo Web Full Stack",
    "fecha_envio": "2024-02-19 10:30:00",
    "estado": "enviado",
    "error_mensaje": null
  }
]
```

**Respuesta - Estadísticas:**
```json
[
  {
    "total": 15,
    "enviados": 14,
    "fallidos": 1,
    "fecha": "2024-02-19"
  }
]
```

---

## 🚀 Instalación

### Paso 1: Crear tabla en MySQL

Ejecuta desde phpMyAdmin o terminal:

```bash
mysql -u diplomas_user -pdiplomas_pass diplomas_db < backend/create_correos_enviados.sql
```

O desde phpMyAdmin:
1. Selecciona base de datos `diplomas_db`
2. Pestaña SQL
3. Pega contenido de `create_correos_enviados.sql`
4. Ejecutar

### Paso 2: Verificar tabla creada

```sql
SHOW TABLES LIKE 'correos_enviados';
DESCRIBE correos_enviados;
```

---

## 📊 Consultas Útiles

### Ver últimos 10 correos enviados
```sql
SELECT 
    id,
    destinatario_nombre,
    destinatario_email,
    asunto,
    estado,
    fecha_envio
FROM correos_enviados
ORDER BY fecha_envio DESC
LIMIT 10;
```

### Correos por curso
```sql
SELECT 
    ce.*,
    c.nombre as curso_nombre
FROM correos_enviados ce
LEFT JOIN cursos c ON ce.curso_id = c.id
WHERE ce.curso_id = 1
ORDER BY ce.fecha_envio DESC;
```

### Estadísticas del día
```sql
SELECT 
    COUNT(*) as total,
    SUM(CASE WHEN estado = 'enviado' THEN 1 ELSE 0 END) as enviados,
    SUM(CASE WHEN estado = 'fallido' THEN 1 ELSE 0 END) as fallidos
FROM correos_enviados
WHERE DATE(fecha_envio) = CURDATE();
```

### Correos fallidos
```sql
SELECT 
    destinatario_email,
    asunto,
    error_mensaje,
    fecha_envio
FROM correos_enviados
WHERE estado = 'fallido'
ORDER BY fecha_envio DESC;
```

---

## 🎯 Casos de Uso

### 1. **Auditoría**
- Ver todos los correos enviados a un usuario
- Verificar si se envió correo de un curso específico
- Revisar correos fallidos

### 2. **Reenvío**
- Obtener cuerpo del correo desde BD
- Reenviar correo que falló

### 3. **Reportes**
- Correos enviados por día/mes
- Tasa de éxito/fallo
- Cursos más solicitados

### 4. **Panel Admin**
- Listar todos los correos
- Filtrar por curso, fecha, estado
- Ver detalles de cada correo

---

## 🔐 Seguridad

### Protección del Endpoint
- ✅ Requiere autenticación JWT
- ✅ Solo usuarios con rol 'admin'
- ✅ Middleware `requireAdmin()`

### Datos Sensibles
- ⚠️ El cuerpo del correo puede contener información sensible
- ⚠️ Solo accesible por administradores
- ⚠️ Considerar encriptar campo `cuerpo` en producción

---

## 📈 Ventajas del Sistema

1. **Historial Completo**
   - Todos los correos registrados
   - Búsqueda y filtrado fácil

2. **Debugging**
   - Ver correos fallidos
   - Mensaje de error detallado

3. **Auditoría**
   - Quién recibió qué correo
   - Cuándo se envió

4. **Reenvío**
   - Recuperar contenido de correos
   - Reenviar si falló

5. **Estadísticas**
   - Correos por día/mes
   - Tasa de éxito

6. **Cumplimiento**
   - Registro de comunicaciones
   - Evidencia de envío

---

## 🔄 Flujo Completo

```
Usuario → Formulario Contacto
    ↓
Backend → Validar datos
    ↓
PHPMailer → Enviar correo SMTP
    ↓
    ├─ Éxito → Guardar en BD (estado: enviado)
    │           └─ Responder 200 OK
    │
    └─ Error → Guardar en BD (estado: fallido)
                └─ Responder 503 Error
```

---

## 📝 Archivos Creados/Modificados

### Nuevos
- ✅ `/backend/create_correos_enviados.sql`
- ✅ `/backend/models/CorreoEnviado.php`
- ✅ `/backend/controllers/correos-enviados.php`

### Modificados
- ✅ `/backend/controllers/contacto.php`

---

## 🎨 Próximas Mejoras

1. **Panel Admin Frontend**
   - Componente Angular para ver correos
   - Filtros por fecha, curso, estado
   - Paginación

2. **Reenvío de Correos**
   - Endpoint para reenviar correo fallido
   - Botón en panel admin

3. **Notificaciones**
   - Alertar admin cuando falla correo
   - Dashboard con estadísticas

4. **Limpieza Automática**
   - Eliminar correos antiguos (>6 meses)
   - Cron job para limpieza

---

**Estado**: ✅ Implementado y funcional  
**Requiere**: Crear tabla en MySQL
