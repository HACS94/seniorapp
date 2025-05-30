<?php
// api/delete_agent.php

header('Content-Type: application/json'); // Indicar que la respuesta será JSON

// Iniciar sesión para verificar autenticación (seguridad)
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    echo json_encode(['success' => false, 'message' => 'Acceso no autorizado.']);
    exit;
}

// Conexión a la base de datos
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root'); // ¡CAMBIA ESTO!
define('DB_PASSWORD', ''); // ¡CAMBIA ESTO!
define('DB_NAME', 'seniorappbbdd');

$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Error de conexión a la base de datos: ' . $conn->connect_error]);
    exit;
}
$conn->set_charset("utf8mb4");

$response = ['success' => false, 'message' => ''];

// Verificar que la solicitud sea POST y que se haya enviado el ID del agente
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id_agente'])) {
    $id_agente = $_POST['id_agente'];

    // Validar que el ID del agente sea un número entero (o el tipo de tu anexo)
    if (!filter_var($id_agente, FILTER_VALIDATE_INT)) {
        $response['message'] = 'ID de agente inválido.';
    } else {
        // Preparar la consulta DELETE
        $sql = "DELETE FROM agentes WHERE id_agente = ?";
        
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("i", $id_agente); // 'i' para entero
            
            if ($stmt->execute()) {
                if ($stmt->affected_rows > 0) {
                    $response['success'] = true;
                    $response['message'] = 'Agente eliminado correctamente.';
                } else {
                    $response['message'] = 'No se encontró el agente con el ID proporcionado.';
                }
            } else {
                $response['message'] = 'Error al ejecutar la eliminación: ' . $stmt->error;
            }
            $stmt->close();
        } else {
            $response['message'] = 'Error al preparar la consulta: ' . $conn->error;
        }
    }
} else {
    $response['message'] = 'Solicitud inválida.';
}

$conn->close();
echo json_encode($response);
?>
