<?php
// lista_agentes.php

// Inicia sesión y verifica si el usuario está autenticado
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php"); // Asegúrate de que esta ruta es correcta
    exit;
}

// Datos de conexión a la base de datos (para pruebas locales)
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root'); // ¡CAMBIA ESTO! Ej: 'root'
define('DB_PASSWORD', ''); // ¡CAMBIA ESTO! Ej: '' (vacío para XAMPP/WAMP por defecto)
define('DB_NAME', 'seniorappbbdd');

// Intenta establecer una conexión a la base de datos MySQL
$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Verifica la conexión
if ($conn->connect_error) {
    die("Error de conexión a la base de datos: " . $conn->connect_error);
}
$conn->set_charset("utf8mb4");

$agentes = []; // Inicializa un array para almacenar los agentes

// Consulta para obtener todos los agentes
// CORRECCIÓN: Se agregó 'fecha_registro' a la selección
$sql = "SELECT id_agente, nombre_agente, fecha_registro FROM agentes ORDER BY nombre_agente ASC"; // Ordena por nombre para mejor visualización
$result = $conn->query($sql);

if ($result) {
    if ($result->num_rows > 0) {
        // Recorre los resultados y los guarda en el array
        while($row = $result->fetch_assoc()) {
            $agentes[] = $row;
        }
    }
    $result->free(); // Libera el conjunto de resultados
} else {
    // Manejo de error si la consulta falla
    die("Error al ejecutar la consulta: " . $conn->error);
}

// Cierra la conexión a la base de datos
$conn->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Agentes - Seniorapp</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
        /* Estilos adicionales si fueran necesarios para la tabla */
        .table-responsive {
            margin-top: 1.5rem;
        }
        .table-dark {
            --bs-table-bg: var(--color-sidebar-bg); /* Fondo de tabla oscuro */
            --bs-table-color: var(--color-text-light); /* Color de texto claro */
            --bs-table-border-color: rgba(255, 255, 255, 0.1); /* Bordes más sutiles */
        }
        .table-dark th {
            color: var(--color-text-muted);
        }
        .table-dark tbody tr:hover {
            background-color: rgba(255, 255, 255, 0.05); /* Ligeramente más claro al pasar el mouse */
        }
        /* Estilos para el modal personalizado */
        .custom-modal-content {
            background-color: var(--color-sidebar-bg);
            color: var(--color-text-light);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        .custom-modal-header {
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        .custom-modal-footer {
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }
        .custom-modal-header .btn-close {
            filter: invert(1); /* Para que la 'x' sea blanca en fondo oscuro */
        }
        /* Estilos para mensajes de feedback */
        .feedback-message {
            margin-top: 1rem;
            padding: 1rem;
            border-radius: 0.5rem;
            display: none; /* Oculto por defecto */
        }
        .feedback-message.success {
            background-color: rgba(40, 167, 69, 0.2);
            color: #28a745;
            border: 1px solid #28a745;
        }
        .feedback-message.error {
            background-color: rgba(220, 53, 69, 0.2);
            color: #dc3545;
            border: 1px solid #dc3545;
        }
    </style>
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
                <h1>Lista de Agentes</h1>
                <p>Aquí se muestran todos los agentes registrados en el sistema.</p>

                <div class="d-flex justify-content-end mb-3">
                    <a href="registro_agente.php" class="btn btn-primary">
                        <i class="fas fa-user-plus me-2"></i> Registrar Nuevo Agente
                    </a>
                </div>

                <div id="feedbackMessage" class="feedback-message" role="alert"></div>

                <div class="table-responsive">
                    <table class="table table-dark table-striped table-hover">
                        <thead>
                            <tr>
                                <th scope="col">Anexo (ID)</th>
                                <th scope="col">Nombre del Agente</th>
                                <th scope="col">Fecha de Registro</th>
                                <th scope="col">Acciones</th> </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($agentes)): ?>
                                <?php foreach ($agentes as $agente): ?>
                                    <tr id="agent-row-<?php echo htmlspecialchars($agente['id_agente']); ?>">
                                        <td><?php echo htmlspecialchars($agente['id_agente']); ?></td>
                                        <td><?php echo htmlspecialchars($agente['nombre_agente']); ?></td>
                                        <td><?php echo htmlspecialchars($agente['fecha_registro']); ?></td>
                                        <td>
                                            <button class="btn btn-danger btn-sm delete-agent-btn" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#confirmDeleteModal" 
                                                    data-id="<?php echo htmlspecialchars($agente['id_agente']); ?>"
                                                    data-name="<?php echo htmlspecialchars($agente['nombre_agente']); ?>">
                                                <i class="fas fa-trash"></i> Eliminar
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4">No hay agentes registrados.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content custom-modal-content">
                <div class="modal-header custom-modal-header">
                    <h5 class="modal-title" id="confirmDeleteModalLabel">Confirmar Eliminación</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    ¿Estás seguro de que deseas eliminar al agente <strong id="agentNameToDelete"></strong> (ID: <span id="agentIdToDelete"></span>)?
                    Esta acción no se puede deshacer.
                </div>
                <div class="modal-footer custom-modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Eliminar</button>
                </div>
            </div>
        </div>
    </div>

    <script src="js/bootstrap.bundle.min.js"></script>
    <script src="js/main.js"></script>

    <script>
        // =================================================================================================
        // SECCIÓN JAVASCRIPT: LÓGICA PARA ELIMINAR AGENTES
        // =================================================================================================

        const confirmDeleteModal = document.getElementById('confirmDeleteModal');
        const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');
        const agentIdToDeleteSpan = document.getElementById('agentIdToDelete');
        const agentNameToDeleteStrong = document.getElementById('agentNameToDelete');
        const feedbackMessageDiv = document.getElementById('feedbackMessage');

        let agentIdToDelete = null; // Variable para almacenar el ID del agente a eliminar

        // Escuchar el evento 'show.bs.modal' del modal de Bootstrap
        confirmDeleteModal.addEventListener('show.bs.modal', function (event) {
            // Botón que disparó el modal
            const button = event.relatedTarget; 
            // Extraer información de los atributos data-*
            agentIdToDelete = button.getAttribute('data-id');
            const agentName = button.getAttribute('data-name');

            // Actualizar el contenido del modal
            agentIdToDeleteSpan.textContent = agentIdToDelete;
            agentNameToDeleteStrong.textContent = agentName;
        });

        // Manejar el clic en el botón de confirmación dentro del modal
        confirmDeleteBtn.addEventListener('click', async function() {
            // Ocultar el modal de Bootstrap
            const modal = bootstrap.Modal.getInstance(confirmDeleteModal);
            modal.hide();

            // Mostrar mensaje de carga (opcional, pero buena UX)
            showFeedback('Procesando eliminación...', 'info');

            try {
                const response = await fetch('api/delete_agent.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `id_agente=${encodeURIComponent(agentIdToDelete)}`
                });

                const result = await response.json();

                if (result.success) {
                    showFeedback(result.message, 'success');
                    // Eliminar la fila de la tabla sin recargar la página
                    const rowToRemove = document.getElementById(`agent-row-${agentIdToDelete}`);
                    if (rowToRemove) {
                        rowToRemove.remove();
                    }
                    // Opcional: Si no quedan agentes, mostrar el mensaje "No hay agentes registrados"
                    const tableBody = document.querySelector('table tbody');
                    if (tableBody && tableBody.children.length === 1 && tableBody.children[0].childElementCount === 1) { // Si solo queda la fila "No hay agentes"
                        // No hacer nada, ya está la fila de "No hay agentes"
                    } else if (tableBody && tableBody.children.length === 0) { // Si no queda ninguna fila
                        const noAgentsRow = `<tr><td colspan="4">No hay agentes registrados.</td></tr>`;
                        tableBody.innerHTML = noAgentsRow;
                    }

                } else {
                    showFeedback(result.message, 'error');
                }
            } catch (error) {
                console.error('Error al eliminar agente:', error);
                showFeedback('Ocurrió un error al intentar eliminar el agente. Inténtalo de nuevo.', 'error');
            }
        });

        // Función para mostrar mensajes de feedback
        function showFeedback(message, type) {
            feedbackMessageDiv.textContent = message;
            feedbackMessageDiv.className = `feedback-message ${type}`; // Limpia clases anteriores y añade la nueva
            feedbackMessageDiv.style.display = 'block';

            // Ocultar el mensaje después de 5 segundos
            setTimeout(() => {
                feedbackMessageDiv.style.display = 'none';
            }, 5000);
        }
    </script>
</body>
</html>