<?php
// delete_attachment.php

session_start();
include '../../config/bdc/conex.php'; // Ajusta la ruta según sea necesario

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    // Retornar error
    echo json_encode(['status' => 'error', 'message' => 'No autorizado']);
    exit;
}

$creator_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;
$user_role = isset($_SESSION['role']) ? strtolower($_SESSION['role']) : '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $form_id = isset($_POST['form_id']) ? intval($_POST['form_id']) : 0;
    $file_name = isset($_POST['file_name']) ? $_POST['file_name'] : '';

    if ($form_id <= 0 || empty($file_name)) {
        echo json_encode(['status' => 'error', 'message' => 'Datos inválidos']);
        exit;
    }

    // Verificar si el usuario tiene acceso a este formulario
    try {
        if ($user_role === 'medico') {
            // Si es médico, solo puede acceder a sus propios formularios
            $stmt = $conn->prepare("SELECT * FROM forms WHERE form_id = :form_id AND creator_id = :creator_id");
            $stmt->bindParam(':form_id', $form_id, PDO::PARAM_INT);
            $stmt->bindParam(':creator_id', $creator_id, PDO::PARAM_INT);
        } else {
            // Otros roles pueden acceder a todos los formularios
            $stmt = $conn->prepare("SELECT * FROM forms WHERE form_id = :form_id");
            $stmt->bindParam(':form_id', $form_id, PDO::PARAM_INT);
        }
        $stmt->execute();
        $form = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$form) {
            echo json_encode(['status' => 'error', 'message' => 'Formulario no encontrado o acceso denegado']);
            exit;
        }

        // Ruta al archivo
        $file_path = '../../pages/formularios/forms_conteiner/forms_attachments/' . $form_id . '/' . $file_name;

        // Verificar si el archivo existe
        if (file_exists($file_path)) {
            // Eliminar el archivo
            if (unlink($file_path)) {
                echo json_encode(['status' => 'success']);
                exit;
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Error al eliminar el archivo.']);
                exit;
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Archivo no encontrado.']);
            exit;
        }
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Error en la base de datos.']);
        exit;
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Método no permitido.']);
    exit;
}
?>
