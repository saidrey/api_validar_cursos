# Deploy Automático con GitHub Actions

## ¿Qué hace?

Cada vez que se hace un `push` a la rama `main`, GitHub Actions despliega automáticamente el código al servidor de Hostinger via FTPS.

No es necesario conectarse por FTP manualmente ni subir archivos a mano. El flujo completo es:

```
git push origin main  →  GitHub Actions  →  Hostinger (api.aulavirtualcentrodecompetencia.com)
```

---

## Cómo funciona el workflow

El archivo de configuración está en `.github/workflows/deploy.yml` y ejecuta estos pasos:

### 1. Checkout del código
Descarga el código del repositorio en el servidor de CI (Ubuntu).

### 2. Instalar PHP y Composer
Instala PHP 8.2 y Composer en el entorno de CI para poder instalar las dependencias.

### 3. Instalar dependencias PHP
```bash
composer install --no-dev --optimize-autoloader
```
Instala solo las dependencias de producción (sin paquetes de desarrollo) y optimiza el autoloader para mejor rendimiento.

### 4. Deploy via FTPS
Sube los archivos al servidor usando el action `SamKirkland/FTP-Deploy-Action`. Solo se suben los archivos necesarios para que la API funcione.

---

## Archivos que se despliegan vs. los que se omiten

### Se suben al servidor:
- `config/` — configuración de DB, JWT, mail, paginación
- `controllers/` — lógica de cada endpoint
- `models/` — acceso a base de datos
- `middleware/` — verificación JWT
- `vendor/` — dependencias instaladas por Composer en CI
- `index.php` — health check / entrada
- `composer.json` y `composer.lock`

### Se omiten (nunca se suben):
| Archivo / Carpeta | Razón |
|-------------------|-------|
| `.htaccess` | Contiene las credenciales de producción (`SetEnv`). Vive solo en el servidor y git nunca lo toca. |
| `.env` | Credenciales locales de desarrollo. |
| `uploads/` | Imágenes subidas por usuarios en producción. No se sobreescriben. |
| `logs/` | Logs del servidor. |
| `public_html/` | Copia local de producción usada como referencia. No pertenece al repo. |
| `*.sql` | Scripts de base de datos. Se aplican manualmente. |
| `*.md` | Documentación. El servidor la bloquea de todas formas. |
| `Dockerfile` / `.dockerignore` | Solo para entorno de desarrollo local con Docker. |
| `debug-mail.php` | Archivo de debug, no debe estar en producción. |

---

## Configuración de credenciales (Secrets)

Las credenciales FTP no están en el código. Se guardan como **secrets** en el repositorio de GitHub y el workflow las consume en tiempo de ejecución.

Para configurarlos: **GitHub repo → Settings → Secrets and variables → Actions**

| Secret | Descripción |
|--------|-------------|
| `FTP_SERVER` | IP o dominio del servidor FTP de Hostinger |
| `FTP_USERNAME` | Usuario FTP del subdominio `api.aulavirtualcentrodecompetencia.com` |
| `FTP_PASSWORD` | Contraseña del usuario FTP |

---

## Separación de entornos

El mismo código funciona en local y en producción gracias a que la configuración se lee desde variables de entorno (`getenv()`):

| Entorno | Cómo se configuran las variables |
|---------|----------------------------------|
| **Local (Docker)** | `docker-compose.yml` define las variables |
| **Producción (Hostinger)** | `.htaccess` en el servidor define las variables con `SetEnv` |

Los archivos PHP de configuración (`config/Database.php`, `config/mail.php`, `config/JWT.php`) tienen fallbacks para desarrollo local. En producción siempre leen desde `getenv()`, por lo que los fallbacks nunca se usan allá.

---

## Cómo agregar el .htaccess en un servidor nuevo

Si se necesita desplegar en un servidor nuevo, usar `.htaccess.example` como base:

1. Copiar `.htaccess.example` y renombrarlo a `.htaccess` en el servidor
2. Descomentar y completar los bloques de `SetEnv` con las credenciales reales
3. Ese archivo **nunca se sube al repositorio**

---

## Flujo de trabajo diario

```bash
# 1. Hacer cambios en el código
# 2. Probar en local con Docker
# 3. Cuando todo funciona:

git add .
git commit -m "descripción del cambio"
git push origin main

# GitHub Actions se encarga del resto automáticamente
```

El deploy tarda aproximadamente 1-2 minutos en completarse. Se puede ver el estado en la pestaña **Actions** del repositorio en GitHub.
