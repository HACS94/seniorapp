<?php
// =================================================================================================
// SECCIÓN PHP: AUTENTICACIÓN Y CONEXIÓN A LA BASE DE DATOS
// (Este es el inicio de tu archivo analisis_evaluaciones.php)
// =================================================================================================
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php"); // Asegúrate de que esta ruta es correcta
    exit;
}

// =================================================================================================
// DATOS DE CONEXIÓN A LA BASE DE DATOS
// ¡ASEGÚRATE DE RELLENAR CON TUS PROPIAS CREDENCIALES!
// =================================================================================================
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root'); // <-- ¡CAMBIA ESTO!
define('DB_PASSWORD', ''); // <-- ¡CAMBIA ESTO!
define('DB_NAME', 'seniorappbbdd');

// Crear la conexión a la base de datos
$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Verificar la conexión
if ($conn->connect_error) {
    die("Error de conexión a la base de datos: " . $conn->connect_error);
}
$conn->set_charset("utf8mb4"); // Configurar el charset para evitar problemas con caracteres especiales

// =================================================================================================
// SECCIÓN PHP: OBTENCIÓN DE DATOS PARA EL FILTRO DE AGENTES (desde la DB)
// =================================================================================================
$agentes_lista = [];
$sql_agentes = "SELECT id_agente, nombre_agente FROM agentes ORDER BY nombre_agente ASC";
$result_agentes = $conn->query($sql_agentes); // Esta es la línea 16 o similar, ahora $conn está definido.

if ($result_agentes) {
    while($row = $result_agentes->fetch_assoc()) {
        $agentes_lista[] = $row;
    }
    $result_agentes->free();
} else {
    // Es bueno loggear errores para depuración
    error_log("Error al obtener agentes: " . $conn->error);
}

// =================================================================================================
// SECCIÓN PHP: OBTENER EL TOTAL DE AGENTES (opcional, como habíamos hablado)
// =================================================================================================
$total_agentes = 0;
$sql_total_agentes = "SELECT COUNT(id_agente) AS total FROM agentes";
$result_total_agentes = $conn->query($sql_total_agentes);
if ($result_total_agentes && $row = $result_total_agentes->fetch_assoc()) {
    $total_agentes = $row['total'];
}
$result_total_agentes->free();

// =================================================================================================
// SECCIÓN PHP: OBTENER EL TOTAL DE EVALUADORES
// =================================================================================================
$total_evaluadores = 0;
$sql_total_evaluadores = "SELECT COUNT(id_evaluador) AS total FROM evaluadores"; // Asume 'id_evaluador' es la PK
$result_total_evaluadores = $conn->query($sql_total_evaluadores);

if ($result_total_evaluadores) {
    $row = $result_total_evaluadores->fetch_assoc();
    $total_evaluadores = $row['total'];
    $result_total_evaluadores->free(); // Liberar el resultado
} else {
    error_log("Error al obtener el total de evaluadores: " . $conn->error);
}

// =================================================================================================
// CIERRE DE CONEXIÓN A LA BASE DE DATOS
// =================================================================================================
$conn->close();

// NOTA: Recuerda que la lógica para obtener datos de evaluación para los gráficos se hará
// via AJAX/Fetch a un nuevo endpoint PHP (ej. api/get_evaluations.php) y no directamente aquí.
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Análisis de Evaluaciones - Seniorapp</title>
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
                <h1>Bienvenido al Dashboard General</h1>
                <p>Aquí puedes ver un resumen rápido de la actividad de Seniorapp.</p>
                <p>Usa el menú lateral para navegar a las secciones específicas, como el **Análisis de Evaluaciones**.</p>
                <div class="row">
    <div class="col-md-4 mb-3">
        <div class="card bg-dark text-white">
            <div class="card-body">
                <h5 class="card-title">Total Agentes</h5>
                <p class="card-text fs-2"><?php echo $total_agentes; ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-3">
        <div class="card bg-dark text-white">
            <div class="card-body">
                <h5 class="card-title">Evaluaciones Registradas (Últimos 30 días)</h5>
                <p class="card-text fs-2">falta definir variables</p> </div>
        </div>
    </div>
     <div class="col-md-4 mb-3">
        <div class="card bg-dark text-white">
            <div class="card-body">
                <h5 class="card-title">Evaluadores Activos</h5>
<p class="card-text fs-2"><?php echo $total_evaluadores; ?></p>        </div>
    </div>
</div>
            </div>
        </div>
    </div>

    <script src="js/bootstrap.bundle.min.js"></script>
    <script src="js/main.js"></script> 
</body>
</html>
