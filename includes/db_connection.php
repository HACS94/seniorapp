<?php
// includes/db_connection.php

// Define las constantes de conexión a la base de datos
define('DB_SERVER', 'localhost'); // O la IP/nombre del servidor de tu base de datos
define('DB_USERNAME', 'root'); // Tu nombre de usuario de phpMyAdmin o de la BD
define('DB_PASSWORD', ''); // Tu contraseña de phpMyAdmin o de la BD
define('DB_NAME', 'seniorappbbdd'); // El nombre de tu base de datos

// Intenta establecer una conexión a la base de datos MySQL
$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Verifica la conexión
if ($conn->connect_error) {
    die("Error de conexión a la base de datos: " . $conn->connect_error);
}

// Opcional: Establecer el conjunto de caracteres a UTF-8 para evitar problemas con tildes y eñes
$conn->set_charset("utf8mb4");

//echo "Conexión a la base de datos establecida correctamente."; // Solo para pruebas, eliminar en producción

// Puedes cerrar la conexión al final de tu script si es necesario, pero mysqli la cierra automáticamente al terminar la ejecución.
// $conn->close();
?>
