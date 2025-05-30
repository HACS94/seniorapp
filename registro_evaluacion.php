<!DOCTYPE html>
<?php

// Inicia sesión y verifica si el usuario está autenticado
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.html");
    exit;
}
?>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard de Evaluaciones</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

    <div class="d-flex" id="wrapper">
        <div class="bg-dark border-right d-flex flex-column" id="sidebar-wrapper">
            <div class="sidebar-header p-3 d-flex justify-content-between align-items-center">
                <span class="sidebar-brand text-white">
                   <span class="sidebar-text">Seniorapp</span>
                </span>
                <button class="btn btn-link text-white d-none d-md-block" id="sidebar-toggle-desktop">
                    <i class="fas fa-bars"></i>
                </button>
                <button class="btn btn-link text-white d-md-none" id="sidebar-close-mobile">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="list-group list-group-flush flex-grow-1">
                <a href="index.php" class="list-group-item list-group-item-action bg-dark text-white active">
                    <i class="fas fa-home me-2"></i> <span class="sidebar-text">Inicio</span>
                </a>
                <a href="registro_evaluacion.php" class="list-group-item list-group-item-action bg-dark text-white">
                    <i class="fas fa-edit me-2"></i> <span class="sidebar-text">Registrar Evaluación</span>
                </a>
                <a href="registro_agente.php" class="list-group-item list-group-item-action bg-dark text-white">
                    <i class="fas fa-user-plus me-2"></i> <span class="sidebar-text">Registrar Agente</span>
                </a>
                <a href="lista_agentes.php" class="list-group-item list-group-item-action bg-dark text-white">
                    <i class="fas fa-users me-2"></i> <span class="sidebar-text">Lista de Agentes</span>
                </a>
                <a href="analisis_evaluaciones.php" class="list-group-item list-group-item-action bg-dark text-white">
                   <i class="fas fa-line-chart"></i><span class="sidebar-text">Analisis</span>
                </a>
            </div>
            <div class="sidebar-footer mt-auto p-3">
                <a class="list-group-item list-group-item-action bg-dark text-white" href="#">
                    <i class="fas fa-user-circle me-2"></i> <span class="sidebar-text">Hola, <?php echo htmlspecialchars($_SESSION['nombre_evaluador'] ?? 'Evaluador'); ?></span>
                </a>
                <a class="list-group-item list-group-item-action bg-dark text-white" href="logout.php">
                    <i class="fas fa-sign-out-alt me-2"></i> <span class="sidebar-text">Salir</span>
                </a>
            </div>
        </div>
        <div id="page-content-wrapper">
            <nav class="navbar navbar-expand-lg navbar-dark border-bottom">
                <div class="container-fluid">
                    <button class="btn btn-dark d-md-none" id="sidebar-toggle-mobile">
                        <i class="fas fa-bars"></i>
                    </button>

                    <a class="navbar-brand d-none d-md-block" href="#"></a> 
                </div>
            </nav>

            <div class="container-fluid p-4">
                <h1>Registro de Evaluaicones</h1>
                <p>Aquí se mostrarán El formulario que evaluara a los agentes</p>
            </div>
        </div>
        </div>
    <script src="js/bootstrap.bundle.min.js"></script>
    <script src="js/chart.umd.js"></script>
    <script src="js/main.js"></script>

</body>
</html>