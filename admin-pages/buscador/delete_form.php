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
    // Obtener form_id y file_name
    $form_id = isset($_POST['form_id']) ? intval($_POST['form_id']) : 0;
    $file_name = isset($_POST['file_name']) ? $_POST['file_name'] : '';

    // Validar form_id y file_name
    if ($form_id <= 0 || empty($file_name)) {
        http_response_code(400); // Solicitud incorrecta
        echo 'Datos inválidos.';
        exit;
    }

    try {
        // Iniciar transacción
        $conn->beginTransaction();

        // Eliminar las medicaciones asociadas en patient_medications
        $stmt = $conn->prepare('DELETE FROM patient_medications WHERE id_form = :id_form');
        $stmt->bindParam(':id_form', $form_id, PDO::PARAM_INT);
        $stmt->execute();

        // Ahora eliminar el registro del formulario en forms
        $stmt = $conn->prepare('DELETE FROM forms WHERE form_id = :form_id');
        $stmt->bindParam(':form_id', $form_id, PDO::PARAM_INT);
        $stmt->execute();

        // Ruta al archivo PDF
        $file_path = '../../pages/formularios/forms_conteiner/' . $file_name;

        // Intentar eliminar el archivo si existe
        if (file_exists($file_path)) {
            unlink($file_path);
        }

        // Confirmar transacción
        $conn->commit();

        // Éxito
        echo 'Formulario eliminado correctamente.';
    } catch (PDOException $e) {
        $conn->rollBack();
        http_response_code(500);
        echo 'Error: ' . $e->getMessage();
    }
} else {
    http_response_code(405); // Método no permitido
    echo 'Método no permitido.';
}
?>
