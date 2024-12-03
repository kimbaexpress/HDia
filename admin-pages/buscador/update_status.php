<?php
session_start();
include '../../config/bdc/conex.php'; // Ajusta la ruta según tu estructura

// Verificar si el usuario está logueado y es administrador
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true || $_SESSION['role'] !== 'admin') {
    http_response_code(403); // Prohibido
    echo 'No tienes permiso para realizar esta acción.';
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtener form_id y nuevo estado
    $form_id = isset($_POST['form_id']) ? intval($_POST['form_id']) : 0;
    $status = isset($_POST['status']) ? $_POST['status'] : '';

    // Validar estado
    $valid_statuses = ['en revision', 'correcto', 'rechazado'];
    if (!in_array($status, $valid_statuses)) {
        http_response_code(400); // Solicitud incorrecta
        echo 'Estado inválido.';
        exit;
    }

    // Actualizar el estado en la base de datos
    $stmt = $conn->prepare('UPDATE forms SET status = :status WHERE form_id = :form_id');
    $stmt->bindParam(':status', $status, PDO::PARAM_STR);
    $stmt->bindParam(':form_id', $form_id, PDO::PARAM_INT);

    if ($stmt->execute()) {
        // Éxito
        echo 'Estado actualizado correctamente.';
    } else {
        // Error
        http_response_code(500); // Error interno del servidor
        echo 'Error al actualizar el estado.';
    }
} else {
    http_response_code(405); // Método no permitido
    echo 'Método no permitido.';
}
?>
