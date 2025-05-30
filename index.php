<?php
// =================================================================================================
// SECCIÓN PHP: AUTENTICACIÓN Y CONEXIÓN A LA BASE DE DATOS
// =================================================================================================

// Inicia la sesión. Esto es crucial para mantener el estado del usuario (logueado o no).
session_start();

// Verifica si el usuario está autenticado. Si no lo está, lo redirige a la página de login.
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php"); // Asegúrate de que esta ruta es correcta
    exit; // Detiene la ejecución del script después de la redirección
}

// Datos de conexión a la base de datos (para pruebas locales).
// ¡IMPORTANTE!: Para producción, estas credenciales deberían estar en un archivo fuera del directorio web
// y ser incluidas de forma segura, o usar variables de entorno.
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root'); // ¡CAMBIA ESTO! Ej: 'root'
define('DB_PASSWORD', ''); // ¡CAMBIA ESTO! Ej: '' (vacío para XAMPP/WAMP por defecto)
define('DB_NAME', 'seniorappbbdd');

// Intenta establecer una conexión a la base de datos MySQL.
$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Verifica si la conexión a la base de datos fue exitosa. Si no, muestra un error fatal.
if ($conn->connect_error) {
    die("Error de conexión a la base de datos: " . $conn->connect_error);
}
// Establece el conjunto de caracteres a UTF-8 para asegurar el correcto manejo de tildes y eñes.
$conn->set_charset("utf8mb4");

// =================================================================================================
// SECCIÓN PHP: OBTENCIÓN DE DATOS PARA EL FILTRO DE AGENTES
// =================================================================================================

// Inicializa un array para almacenar la lista de agentes que se mostrará en el menú desplegable.
$agentes_lista = [];
// Consulta SQL para seleccionar el ID y el nombre de todos los agentes, ordenados alfabéticamente.
$sql_agentes = "SELECT id_agente, nombre_agente FROM agentes ORDER BY nombre_agente ASC";
// Ejecuta la consulta.
$result_agentes = $conn->query($sql_agentes);

if ($result_agentes) {
    // Si hay resultados, los recorre y los añade al array $agentes_lista.
    while($row = $result_agentes->fetch_assoc()) {
        $agentes_lista[] = $row;
    }
    // Libera la memoria asociada al resultado de la consulta.
    $result_agentes->free();
} else {
    // Si la consulta falla, registra el error en el log del servidor (útil para depuración).
    error_log("Error al obtener agentes: " . $conn->error);
}

// =================================================================================================
// SECCIÓN PHP: MANEJO DE PARÁMETROS DEL FILTRO
// =================================================================================================

// Obtiene el ID del agente seleccionado del parámetro GET 'agente_id'. Si no está presente, usa una cadena vacía.
$selected_agente_id = $_GET['agente_id'] ?? '';
// Obtiene la fecha de inicio del parámetro GET 'fecha_inicio'.
$fecha_inicio = $_GET['fecha_inicio'] ?? '';
// Obtiene la fecha de fin del parámetro GET 'fecha_fin'.
$fecha_fin = $_GET['fecha_fin'] ?? '';

// Este array se llenará con los datos de las evaluaciones filtradas en futuras implementaciones.
$evaluaciones_filtradas = []; 

// =================================================================================================
// SECCIÓN PHP: CIERRE DE CONEXIÓN A LA BASE DE DATOS
// =================================================================================================

// Cierra la conexión a la base de datos.
$conn->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Principal - Seniorapp</title>
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
                <h1>Dashboard Principal</h1>
                <p>Aquí se mostrarán los filtros y gráficos.</p>

                <div class="card bg-dark text-white mb-4">
                    <div class="card-header">
                        Filtros de Evaluación
                    </div>
                    <div class="card-body">
                        <form action="index.php" method="GET">
                            <div class="row g-3 align-items-end">
                                <div class="col-md-4">
                                    <label for="agente_id" class="form-label">Agente:</label>
                                    <select class="form-select bg-secondary text-white border-secondary" id="agente_id" name="agente_id">
                                        <option value="">Seleccione un agente</option>
                                        <?php foreach ($agentes_lista as $agente): ?>
                                            <option value="<?php echo htmlspecialchars($agente['id_agente']); ?>"
                                                <?php echo ($selected_agente_id == $agente['id_agente']) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($agente['nombre_agente']); ?> (ID: <?php htmlspecialchars($agente['id_agente']); ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label for="fecha_inicio" class="form-label">Fecha Inicio:</label>
                                    <input type="date" class="form-control bg-secondary text-white border-secondary" id="fecha_inicio" name="fecha_inicio"
                                           value="<?php echo htmlspecialchars($fecha_inicio); ?>">
                                </div>
                                <div class="col-md-3">
                                    <label for="fecha_fin" class="form-label">Fecha Fin:</label>
                                    <input type="date" class="form-control bg-secondary text-white border-secondary" id="fecha_fin" name="fecha_fin"
                                           value="<?php echo htmlspecialchars($fecha_fin); ?>">
                                </div>
                                <div class="col-md-2">
                                    <button type="submit" class="btn btn-primary w-100">Aplicar Filtro</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <?php if ($selected_agente_id && $fecha_inicio && $fecha_fin): ?>
                    <div class="card bg-dark text-white">
                        <div class="card-header">
                            Resultados para Agente: <?php echo htmlspecialchars($selected_agente_id); ?> (<?php echo htmlspecialchars($agentes_lista[array_search($selected_agente_id, array_column($agentes_lista, 'id_agente'))]['nombre_agente'] ?? 'N/A'); ?>)
                            <br>
                            Rango: <?php echo htmlspecialchars($fecha_inicio); ?> a <?php echo htmlspecialchars($fecha_fin); ?>
                        </div>
                        <div class="card-body">
                            <p>Contenido de los gráficos y análisis de evaluaciones.</p>
                            
                            <div class="row">
                                <div class="col-md-6 mb-4">
                                    <div style="position: relative; height:350px;">
                                        <canvas id="myChart" class="bg-secondary p-3 rounded"></canvas>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-4">
                                    <div style="position: relative; height:350px;">
                                        <canvas id="donutChart" class="bg-secondary p-3 rounded"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info bg-dark text-white border-secondary" role="alert">
                        Selecciona un agente y un rango de fechas para ver los resultados.
                    </div>
                <?php endif; ?>

            </div>
            </div>
        <script src="js/bootstrap.bundle.min.js"></script>
    <script src="js/chart.umd.js"></script>
    <script src="js/main.js"></script>

    <script>
        // =================================================================================================
        // SECCIÓN JAVASCRIPT: CONFIGURACIÓN Y ACTUALIZACIÓN DE GRÁFICOS CON CHART.JS
        // =================================================================================================

        const ctx = document.getElementById('myChart'); // Contexto para el gráfico de barras
        const donutCtx = document.getElementById('donutChart'); // Contexto para el gráfico de dona

        let myChart; // Variable para la instancia del gráfico de barras
        let myDonutChart; // Variable para la instancia del gráfico de dona

        // Función para generar un número entero aleatorio entre min y max (inclusive)
        function getRandomInt(min, max) {
            min = Math.ceil(min);
            max = Math.floor(max);
            return Math.floor(Math.random() * (max - min + 1)) + min;
        }

        // Función para generar un array de datos aleatorios para el gráfico de barras
        function generateRandomData() {
            return [
                getRandomInt(50, 100), // Saludo (ejemplo de ítem)
                getRandomInt(50, 100), // Escucha (ejemplo de ítem)
                getRandomInt(50, 100), // Tono (ejemplo de ítem)
                getRandomInt(50, 100)  // Claridad (ejemplo de ítem)
            ];
        }

        // Función para generar un array de datos aleatorios para el gráfico de dona (ej. Cumplido vs A Mejorar)
        function generateDonutData() {
            const cumplido = getRandomInt(40, 80); // Porcentaje de cumplimiento
            const aMejorar = 100 - cumplido;      // Porcentaje a mejorar (complemento de 100)
            return [cumplido, aMejorar];
        }

        // Inicialización del Gráfico de Barras
        if (ctx) {
            myChart = new Chart(ctx, {
                type: 'bar', // Tipo de gráfico: barras
                data: {
                    labels: ['Saludo', 'Escucha', 'Tono', 'Claridad'], // Etiquetas para las barras
                    datasets: [{
                        label: '% de Cumplimiento', // Etiqueta del dataset
                        data: generateRandomData(), // Datos aleatorios iniciales
                        backgroundColor: [ // Colores de fondo para las barras
                            'rgba(75, 192, 192, 0.6)',
                            'rgba(153, 102, 255, 0.6)',
                            'rgba(255, 159, 64, 0.6)',
                            'rgba(255, 99, 132, 0.6)'
                        ],
                        borderColor: [ // Colores del borde de las barras
                            'rgba(75, 192, 192, 1)',
                            'rgba(153, 102, 255, 1)',
                            'rgba(255, 159, 64, 1)',
                            'rgba(255, 99, 132, 1)'
                        ],
                        borderWidth: 1 // Ancho del borde de las barras
                    }]
                },
                options: {
                    scales: { // Configuración de los ejes
                        y: {
                            beginAtZero: true, // El eje Y comienza en 0
                            max: 100 // El eje Y va hasta 100 (para porcentajes)
                        }
                    },
                    responsive: true, // El gráfico se adapta al tamaño de su contenedor
                    maintainAspectRatio: false // Importante: Permite que el contenedor controle la altura del gráfico
                }
            });
        }

        // Inicialización del Gráfico de Dona
        if (donutCtx) {
            myDonutChart = new Chart(donutCtx, {
                type: 'doughnut', // Tipo de gráfico: dona/doughnut
                data: {
                    labels: ['Cumplido', 'A Mejorar'], // Etiquetas para las secciones de la dona
                    datasets: [{
                        label: 'Rendimiento General', // Etiqueta del dataset
                        data: generateDonutData(), // Datos aleatorios iniciales
                        backgroundColor: [ // Colores de fondo para las secciones
                            'rgba(75, 192, 192, 0.8)', // Verde azulado
                            'rgba(255, 99, 132, 0.8)'  // Rojo
                        ],
                        borderColor: [ // Colores del borde de las secciones
                            'rgba(75, 192, 192, 1)',
                            'rgba(255, 99, 132, 1)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true, // El gráfico se adapta al tamaño de su contenedor
                    maintainAspectRatio: false, // Importante: Permite que el contenedor controle la altura del gráfico
                    plugins: {
                        legend: { // Configuración de la leyenda
                            position: 'top', // Posición de la leyenda
                            labels: {
                                color: '#f8f9fa' // Color del texto de la leyenda para el tema oscuro
                            }
                        },
                        title: { // Configuración del título del gráfico
                            display: true,
                            text: 'Rendimiento General del Agente', // Título del gráfico
                            color: '#f8f9fa' // Color del texto del título
                        }
                    }
                }
            });
        }

        // =================================================================================================
        // SECCIÓN JAVASCRIPT: ACTUALIZACIÓN PERIÓDICA DE DATOS PARA AMBOS GRÁFICOS
        // =================================================================================================
        setInterval(() => {
            if (myChart) { // Si el gráfico de barras existe, actualiza sus datos
                myChart.data.datasets[0].data = generateRandomData();
                myChart.update();
            }
            if (myDonutChart) { // Si el gráfico de dona existe, actualiza sus datos
                myDonutChart.data.datasets[0].data = generateDonutData();
                myDonutChart.update();
            }
        }, 3000); // Actualiza ambos gráficos cada 3000 milisegundos (3 segundos)
    </script>
</body>
</html>