# Implementación JWT - Documentación

Sistema de autenticación JWT implementado en el backend PHP y frontend Angular.

---

## 📦 Componentes Implementados

### Backend (PHP)

#### 1. **Librería JWT**
```bash
composer require firebase/php-jwt
```
- Versión: 7.0.2
- Ubicación: `/workspace/backend/vendor/firebase/php-jwt`

#### 2. **JWTHandler** (`/backend/config/JWT.php`)

**Clase helper para manejo de JWT**

**Métodos:**

```php
// Generar token
JWTHandler::generateToken($user_data)
// Retorna: string (token JWT)

// Validar token
JWTHandler::validateToken($token)
// Retorna: array (datos del usuario) o null (inválido)

// Obtener token del header
JWTHandler::getBearerToken()
// Retorna: string (token) o null
```

**Configuración:**
- `$secret_key`: Clave secreta para firmar tokens
- `$algorithm`: HS256 (HMAC SHA-256)
- `$expiration_time`: 86400 segundos (24 horas)

**Payload del Token:**
```json
{
  "iat": 1708300800,
  "exp": 1708387200,
  "user_id": 1,
  "email": "admin@example.com",
  "rol": "admin"
}
```

#### 3. **Middleware de Autenticación** (`/backend/middleware/auth.php`)

**Funciones disponibles:**

**a) `requireAuth()`**
- Valida que exista token JWT válido
- Retorna datos del usuario decodificados
- Responde 401 si no hay token o es inválido

```php
$user = requireAuth();
// $user = ['user_id' => 1, 'email' => '...', 'rol' => 'admin']
```

**b) `requireAdmin()`**
- Valida token Y verifica rol admin
- Retorna datos del usuario
- Responde 403 si no es admin

```php
$user = requireAdmin();
// Solo continúa si es admin
```

**c) `requireOwnerOrAdmin($resource_user_id)`**
- Valida que sea el dueño del recurso O admin
- Útil para endpoints de perfil de usuario

```php
$user = requireOwnerOrAdmin($user_id);
// Solo continúa si es el dueño o admin
```

#### 4. **Login Actualizado** (`/backend/controllers/login.php`)

**Cambios:**
- ✅ Genera JWT al hacer login exitoso
- ✅ Retorna token en la respuesta
- ✅ Token expira en 24 horas

**Request:**
```json
POST /controllers/login.php
{
  "email": "admin@example.com",
  "password": "password123"
}
```

**Response:**
```json
{
  "mensaje": "Login exitoso",
  "token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...",
  "usuario": {
    "id": 1,
    "nombre": "Admin",
    "email": "admin@example.com",
    "rol": "admin"
  }
}
```

#### 5. **Endpoints Protegidos**

**Ejemplo: `/backend/controllers/cursos.php`**

```php
switch($method) {
    case 'GET':
        // Público - No requiere autenticación
        $stmt = $curso->leer();
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
        break;

    case 'POST':
        // Protegido - Solo admin
        $user = requireAdmin();
        // ... crear curso
        break;

    case 'PUT':
        // Protegido - Solo admin
        $user = requireAdmin();
        // ... actualizar curso
        break;

    case 'DELETE':
        // Protegido - Solo admin
        $user = requireAdmin();
        // ... eliminar curso
        break;
}
```

**Endpoints protegidos actualmente:**
- ✅ POST /cursos.php - Crear curso (admin)
- ✅ PUT /cursos.php - Actualizar curso (admin)
- ✅ DELETE /cursos.php - Eliminar curso (admin)

---

### Frontend (Angular)

#### 1. **Interface LoginResponse** (`/frontend/src/app/core/models/usuario.model.ts`)

```typescript
export interface LoginResponse {
  mensaje: string;
  token: string;
  usuario: Usuario;
}
```

#### 2. **AuthService Actualizado** (`/frontend/src/app/core/services/auth.service.ts`)

**Cambios:**
- ✅ Guarda token en localStorage
- ✅ Valida existencia de token en `estaAutenticado()`
- ✅ Limpia token en `logout()`
- ✅ Carga token al iniciar app

**Métodos:**

```typescript
// Login
login(email: string, password: string): Observable<LoginResponse>
// Guarda token y usuario en localStorage

// Logout
logout(): void
// Limpia token y usuario

// Verificar autenticación
estaAutenticado(): boolean
// Retorna true si hay usuario Y token

// Obtener usuario actual
obtenerUsuario(): Usuario | null

// Verificar si es admin
esAdmin(): boolean
```

#### 3. **Auth Interceptor** (`/frontend/src/app/core/interceptors/auth.interceptor.ts`)

**Funcionamiento:**
- Lee token de localStorage
- Agrega header `Authorization: Bearer {token}` a TODAS las peticiones HTTP
- Automático, no requiere código adicional

```typescript
// Antes (sin interceptor)
this.http.post(url, data, {
  headers: { Authorization: `Bearer ${token}` }
})

// Después (con interceptor)
this.http.post(url, data)
// Token agregado automáticamente
```

#### 4. **Error Interceptor** (`/frontend/src/app/core/interceptors/error.interceptor.ts`)

**Manejo de errores JWT:**
- **401 Unauthorized**: Limpia sesión y redirige a `/login`
- **403 Forbidden**: Muestra mensaje de permisos insuficientes

---

## 🔄 Flujo Completo

### 1. Login
```
Usuario → Angular → POST /login.php → PHP
                                      ↓
                                   Valida credenciales
                                      ↓
                                   Genera JWT
                                      ↓
Angular ← { token, usuario } ← PHP
   ↓
Guarda en localStorage
```

### 2. Petición Protegida
```
Usuario → Angular → POST /cursos.php
                    Header: Authorization: Bearer {token}
                                      ↓
                                   PHP Middleware
                                      ↓
                                   Valida token
                                      ↓
                                   Verifica rol
                                      ↓
                                   Ejecuta acción
                                      ↓
Angular ← { mensaje: "Curso creado" } ← PHP
```

### 3. Token Expirado
```
Usuario → Angular → POST /cursos.php
                    Header: Authorization: Bearer {expired_token}
                                      ↓
                                   PHP Middleware
                                      ↓
                                   Token inválido
                                      ↓
Angular ← 401 Unauthorized ← PHP
   ↓
Error Interceptor
   ↓
Limpia localStorage
   ↓
Redirige a /login
```

---

## 🧪 Pruebas

### Probar Login
```bash
curl -X POST http://localhost:8080/controllers/login.php \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@example.com","password":"admin123"}'
```

**Respuesta esperada:**
```json
{
  "mensaje": "Login exitoso",
  "token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...",
  "usuario": {...}
}
```

### Probar Endpoint Protegido
```bash
# Sin token (debe fallar)
curl -X POST http://localhost:8080/controllers/cursos.php \
  -H "Content-Type: application/json" \
  -d '{"nombre":"Nuevo Curso"}'

# Con token (debe funcionar)
curl -X POST http://localhost:8080/controllers/cursos.php \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer {tu_token_aqui}" \
  -d '{"nombre":"Nuevo Curso"}'
```

---

## 🔐 Seguridad

### ✅ Implementado
- ✅ Tokens firmados con HMAC SHA-256
- ✅ Expiración de 24 horas
- ✅ Validación en cada petición protegida
- ✅ Limpieza automática en error 401
- ✅ Verificación de rol (admin)

### ⚠️ Pendiente para Producción
- ⚠️ Mover `$secret_key` a variable de entorno
- ⚠️ Usar HTTPS obligatorio
- ⚠️ Implementar refresh tokens
- ⚠️ Rate limiting en login
- ⚠️ Blacklist de tokens revocados

---

## 📊 Comparación Antes/Después

| Aspecto | Antes | Después |
|---------|-------|---------|
| Autenticación | Solo validación de credenciales | JWT con expiración |
| Sesión | Stateful (localStorage solo) | Stateless (token autofirmado) |
| Seguridad endpoints | Sin protección | Middleware con validación |
| Escalabilidad | Limitada | Alta (stateless) |
| Autorización | Manual en cada endpoint | Centralizada en middleware |
| Expiración | No | 24 horas |

---

## 🚀 Próximos Pasos

1. **Proteger más endpoints**:
   - diplomas.php (POST, PUT, DELETE)
   - usuarios.php (todos los métodos)

2. **Implementar refresh tokens**:
   - Token de acceso: 15 minutos
   - Refresh token: 7 días

3. **Agregar logging**:
   - Registrar intentos de login
   - Registrar accesos con token inválido

4. **Mejorar seguridad**:
   - Variables de entorno para secret_key
   - HTTPS obligatorio
   - Rate limiting

---

## 📝 Archivos Modificados/Creados

### Backend
- ✅ `/backend/config/JWT.php` (nuevo)
- ✅ `/backend/middleware/auth.php` (nuevo)
- ✅ `/backend/controllers/login.php` (modificado)
- ✅ `/backend/controllers/cursos.php` (modificado)
- ✅ `/backend/composer.json` (actualizado)

### Frontend
- ✅ `/frontend/src/app/core/models/usuario.model.ts` (modificado)
- ✅ `/frontend/src/app/core/services/auth.service.ts` (modificado)
- ✅ `/frontend/src/app/core/interceptors/auth.interceptor.ts` (ya existía)
- ✅ `/frontend/src/app/core/interceptors/error.interceptor.ts` (ya existía)

---

**Última actualización**: 2024  
**Estado**: ✅ Implementado y funcional
