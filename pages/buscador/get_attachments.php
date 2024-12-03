<?php
// get_attachments.php

session_start();
include '../../config/bdc/conex.php'; // Ajusta la ruta según sea necesario

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    // Retornar error
    echo json_encode(['status' => 'error', 'message' => 'No autorizado']);
    exit;
}

$user_role = isset($_SESSION['role']) ? strtolower($_SESSION['role']) : '';
$creator_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;

$form_id = isset($_GET['form_id']) ? intval($_GET['form_id']) : 0;

if ($form_id <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'ID de formulario inválido']);
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

    // Construir la ruta a la carpeta de adjuntos
    $attachments_dir = realpath(dirname(__FILE__) . '/../../pages/formularios/forms_conteiner/forms_attachments/' . $form_id);

    if (!$attachments_dir || !is_dir($attachments_dir)) {
        // No hay archivos adjuntos
        echo json_encode(['status' => 'success', 'files' => []]);
        exit;
    }

    // Obtener la lista de archivos
    $files = [];
    $dir_iterator = new DirectoryIterator($attachments_dir);
    foreach ($dir_iterator as $fileinfo) {
        if ($fileinfo->isFile()) {
            $file_name = $fileinfo->getFilename();
            $file_url = '../../pages/formularios/forms_conteiner/forms_attachments/' . $form_id . '/' . urlencode($file_name);
            $files[] = ['name' => $file_name, 'url' => $file_url];
        }
    }

    echo json_encode(['status' => 'success', 'files' => $files]);
    exit;
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Error en la base de datos']);
    exit;
}
?>
