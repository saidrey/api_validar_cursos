<?php
header("Content-Type: application/json; charset=UTF-8");

$response = array(
    "API" => "Diplomas App Backend",
    "version" => "1.0",
    "status" => "online",
    "endpoints" => array(
        "usuarios" => "/controllers/usuarios.php",
        "cursos" => "/controllers/cursos.php",
        "diplomas" => "/controllers/diplomas.php",
        "login" => "/controllers/login.php",
        "validar" => "/controllers/validar.php",
        "contacto" => "/controllers/contacto.php"
    )
);

echo json_encode($response, JSON_PRETTY_PRINT);
