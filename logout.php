<?php
session_start(); // Inicia la sesión si no está iniciada
session_unset(); // Elimina todas las variables de sesión
session_destroy(); // Destruye la sesión

// Si usas cookies de sesión, también es buena práctica eliminarla
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cerrar Sesión - Seniorapp</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
        /* Estilos específicos para la página de logout */
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh; /* Asegura que ocupe toda la altura de la ventana */
            background-color: var(--color-main-bg); /* Usa el color de fondo principal */
            color: var(--color-text-light); /* Color de texto claro */
            text-align: center;
        }
        .logout-container {
            background-color: var(--color-sidebar-bg); /* Usa el color de la barra lateral para el fondo */
            padding: 2.5rem;
            border-radius: 0.75rem; /* Bordes redondeados */
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.3); /* Sombra sutil */
            width: 100%;
            max-width: 500px; /* Ancho máximo para el contenedor */
        }
        .logout-container h2 {
            color: var(--color-text-light);
            margin-bottom: 1.5rem;
            font-weight: var(--font-weight-bold);
        }
        .logout-container p {
            font-size: 1.1rem;
            margin-bottom: 2rem;
        }
        .btn-return {
            background-color: #007bff; /* Color azul de Bootstrap por defecto */
            border-color: #007bff;
            font-weight: var(--font-weight-bold);
            padding: 0.75rem 1.5rem;
            border-radius: 0.5rem;
            transition: background-color 0.2s ease, border-color 0.2s ease;
            color: white; /* Asegura que el texto del botón sea blanco */
            text-decoration: none; /* Elimina el subrayado del enlace */
        }
        .btn-return:hover {
            background-color: #0056b3;
            border-color: #0056b3;
        }
    </style>
</head>
<body>

    <div class="logout-container">
        <h2>¡Sesión Cerrada!</h2>
        <p>Gracias por usar Seniorapp.</p>
        <a href="login.php" class="btn btn-return">Volver a Iniciar Sesión</a>
    </div>

    <script src="js/bootstrap.bundle.min.js"></script>
</body>
</html>
