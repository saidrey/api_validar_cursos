# Configuración de Correo con PHPMailer

## Instalación

```bash
cd /workspace/backend
composer install
```

## Configuración

### Desarrollo (Local)
Por defecto está en modo desarrollo. Los mensajes se guardan en `logs/contacto.log`.

### Producción (Hostinger)

1. **Copia el archivo de ejemplo:**
```bash
cp .env.example .env
```

2. **Edita `.env` con tus credenciales de Hostinger:**
```env
MAIL_MODE=production

SMTP_HOST=smtp.hostinger.com
SMTP_PORT=587
SMTP_USERNAME=info@tudominio.com
SMTP_PASSWORD=tu_password_real
SMTP_FROM_EMAIL=info@tudominio.com
SMTP_FROM_NAME=Diplomas App
SMTP_REPLY_TO=contacto@tudominio.com

CONTACT_EMAIL=contacto@tudominio.com
```

3. **Carga las variables de entorno en Apache:**

Agrega al `.htaccess` o configuración de Apache:
```apache
SetEnv MAIL_MODE production
SetEnv SMTP_HOST smtp.hostinger.com
SetEnv SMTP_PORT 587
SetEnv SMTP_USERNAME info@tudominio.com
SetEnv SMTP_PASSWORD tu_password
```

## Obtener credenciales en Hostinger

1. Ingresa a tu panel de Hostinger
2. Ve a **Emails** → **Cuentas de correo**
3. Crea o selecciona una cuenta (ej: info@tudominio.com)
4. Usa estas credenciales en la configuración

## Configuración SMTP de Hostinger

- **Host:** smtp.hostinger.com
- **Puerto:** 587
- **Seguridad:** TLS/STARTTLS
- **Autenticación:** Requerida
- **Usuario:** tu_email@tudominio.com
- **Contraseña:** La contraseña de tu cuenta de correo

## Probar

```bash
# En Postman
POST http://localhost:8080/controllers/contacto.php
{
  "nombre": "Test",
  "email": "test@example.com",
  "telefono": "3001234567",
  "mensaje": "Mensaje de prueba"
}
```

## Notas

- En desarrollo, revisa `backend/logs/contacto.log`
- En producción, los correos se envían realmente
- El archivo `.env` NO debe subirse a Git (ya está en .gitignore)
