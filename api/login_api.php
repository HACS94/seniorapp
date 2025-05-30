<?php
// api/login_api.php

// 1. Iniciar sesión: Crucial para almacenar información del usuario si el login es exitoso
session_start();
require_once '../includes/db_connection.php'; // Incluir la conexión compartida

// 4. Verificar que la solicitud sea POST (el formulario de login envía datos por POST)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 5. Obtener y sanear los datos del formulario
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // 6. Validar que los campos no estén vacíos
    if (empty($username) || empty($password)) {
        $_SESSION['login_message'] = "Por favor, ingresa tu usuario/email y contraseña.";
        $_SESSION['message_type'] = "danger";
        header("Location: ../login.php"); // Asegúrate que esta ruta es correcta
        exit();
    }

    // 7. Preparar la consulta SQL para buscar el evaluador por email
    $sql = "SELECT id_evaluador, nombre, email, password_hash, rol FROM evaluadores WHERE email = ?";
    
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("s", $param_username);
        $param_username = $username;

        if ($stmt->execute()) {
            $stmt->store_result();

            if ($stmt->num_rows == 1) {
                $stmt->bind_result($id_evaluador, $nombre, $email, $hashed_password, $rol);
                if ($stmt->fetch()) {
                    // *** Importante: Ajusta esta línea según cómo esté la contraseña en tu BD ***
                    // Opción A (Recomendada y segura): Si la contraseña está hasheada con password_hash()
                    if (password_verify($password, $hashed_password)) { 
                    // Opción B (No segura para producción, solo para pruebas si está en texto plano):
                    // if ($password === $hashed_password) { 
                        $_SESSION['loggedin'] = TRUE;
                        $_SESSION['id_evaluador'] = $id_evaluador;
                        $_SESSION['nombre_evaluador'] = $nombre;
                        $_SESSION['email_evaluador'] = $email;
                        $_SESSION['rol_evaluador'] = $rol;

                        header("Location: ../index.php"); // Asegúrate que esta ruta es correcta
                        exit();
                    } else {
                        $_SESSION['login_message'] = "Usuario o contraseña incorrectos.";
                        $_SESSION['message_type'] = "danger";
                        header("Location: ../login.php"); // Asegúrate que esta ruta es correcta
                        exit();
                    }
                }
            } else {
                $_SESSION['login_message'] = "Usuario o contraseña incorrectos.";
                $_SESSION['message_type'] = "danger";
                header("Location: ../login.php"); // Asegúrate que esta ruta es correcta
                exit();
            }
        } else {
            $_SESSION['login_message'] = "Ocurrió un error al intentar iniciar sesión. Por favor, inténtalo de nuevo.";
            $_SESSION['message_type'] = "danger";
            header("Location: ../login.php"); // Asegúrate que esta ruta es correcta
            exit();
        }

        $stmt->close();
    } else {
        $_SESSION['login_message'] = "Ocurrió un error interno. Por favor, inténtalo de nuevo.";
        $_SESSION['message_type'] = "danger";
        header("Location: ../login.php"); // Asegúrate que esta ruta es correcta
        exit();
    }
    
    // 8. Cerrar la conexión a la base de datos
    $conn->close();

} else {
    // Si se intenta acceder a este script directamente sin enviar el formulario POST
    $_SESSION['login_message'] = "Acceso no autorizado.";
    $_SESSION['message_type'] = "danger";
    header("Location: ../login.php"); // Asegúrate que esta ruta es correcta
    exit();
}
?>
