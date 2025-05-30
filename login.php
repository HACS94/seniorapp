<?php
// public/login.php

// Inicia la sesión. Esto es crucial para poder leer mensajes de error/éxito
// que puedan ser pasados desde el script de procesamiento del login (api/login_api.php).
session_start();

// Inicializa variables para mensajes de error/éxito
$login_message = '';
$message_type = ''; // 'success' o 'danger' (para estilos de Bootstrap)

// Verifica si hay un mensaje en la sesión (ej. después de un intento de login fallido)
if (isset($_SESSION['login_message'])) {
    $login_message = $_SESSION['login_message'];
    $message_type = $_SESSION['message_type'] ?? 'danger'; // Por defecto, si no se especifica, es un error
    
    // Una vez que el mensaje se ha leído, se debe eliminar de la sesión
    // para que no se muestre de nuevo al recargar la página.
    unset($_SESSION['login_message']);
    unset($_SESSION['message_type']);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Dashboard de Evaluaciones</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
        /* Estilos específicos para la página de login */
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh; /* Asegura que ocupe toda la altura de la ventana */
            background-color: var(--color-main-bg); /* Usa el color de fondo principal */
            color: var(--color-text-light); /* Color de texto claro */
        }
        .login-container {
            background-color: var(--color-sidebar-bg); /* Usa el color de la barra lateral para el fondo del formulario */
            padding: 2.5rem;
            border-radius: 0.75rem; /* Bordes redondeados */
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.3); /* Sombra sutil */
            width: 100%;
            max-width: 400px; /* Ancho máximo para el formulario */
            text-align: center;
        }
        .login-container h2 {
            color: var(--color-text-light);
            margin-bottom: 1.5rem;
            font-weight: var(--font-weight-bold);
        }
        .form-control {
            background-color: #3a3a3a; /* Un gris un poco más claro para los inputs */
            border: 1px solid #555;
            color: var(--color-text-light);
            margin-bottom: 1rem;
        }
        .form-control::placeholder {
            color: var(--color-text-muted);
        }
        .form-control:focus {
            background-color: #4a4a4a;
            border-color: #666;
            color: var(--color-text-light);
            box-shadow: 0 0 0 0.25rem rgba(255, 255, 255, 0.1);
        }
        .btn-primary {
            background-color: #007bff; /* Color azul de Bootstrap por defecto, puedes cambiarlo */
            border-color: #007bff;
            font-weight: var(--font-weight-bold);
            padding: 0.75rem 1.5rem;
            border-radius: 0.5rem;
            transition: background-color 0.2s ease, border-color 0.2s ease;
        }
        .btn-primary:hover {
            background-color: #0056b3;
            border-color: #0056b3;
        }
        .alert {
            margin-top: 1rem;
            font-size: 0.9rem;
            padding: 0.75rem 1.25rem;
            border-radius: 0.5rem;
        }
    </style>
</head>
<body>

    <div class="login-container">
        <h2>Iniciar Sesión</h2>

        <?php if (!empty($login_message)): ?>
            <div class="alert alert-<?php echo $message_type; ?>" role="alert">
                <?php echo htmlspecialchars($login_message); ?>
            </div>
        <?php endif; ?>

        <form action="api/login_api.php" method="POST">
            <div class="mb-3">
                <input type="text" class="form-control" id="username" name="username" placeholder="Usuario o Email" required>
            </div>
            <div class="mb-3">
                <input type="password" class="form-control" id="password" name="password" placeholder="Contraseña" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Entrar</button>
        </form>
    </div>

    <script src="js/bootstrap.bundle.min.js"></script>
    <script src="js/login.js"></script>
</body>
</html>