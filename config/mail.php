<?php
// Configuración de correo
return [
    // Modo: 'development' o 'production'
    'mode' => getenv('MAIL_MODE') ?: 'development',
    
    // Configuración SMTP para Hostinger
    'smtp' => [
        'host' => getenv('SMTP_HOST') ?: 'smtp.hostinger.com',
        'port' => getenv('SMTP_PORT') ?: 587,
        'username' => getenv('SMTP_USERNAME') ?: 'info@tudominio.com',
        'password' => getenv('SMTP_PASSWORD') ?: '',
        'from_email' => getenv('SMTP_FROM_EMAIL') ?: 'info@tudominio.com',
        'from_name' => getenv('SMTP_FROM_NAME') ?: 'Diplomas App',
        'reply_to' => getenv('SMTP_REPLY_TO') ?: 'contacto@tudominio.com'
    ],
    
    // Email de destino para mensajes de contacto
    'contact_email' => getenv('CONTACT_EMAIL') ?: 'contacto@tudominio.com'
];
