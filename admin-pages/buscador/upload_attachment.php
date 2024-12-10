<?php
// upload_attachment.php

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

        // Manejar los archivos subidos
        if (isset($_FILES['attachment_files']) && !empty($_FILES['attachment_files']['name'][0])) {
            $upload_dir = '../../pages/formularios/forms_conteiner/forms_attachments/' . $form_id . '/';

            // Crear el directorio si no existe
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }

            $allowed_types = array('application/pdf');

            // Recorrer cada archivo subido
            for ($i = 0; $i < count($_FILES['attachment_files']['name']); $i++) {
                $tmp_name = $_FILES['attachment_files']['tmp_name'][$i];
                $file_name = basename($_FILES['attachment_files']['name'][$i]);
                $file_type = $_FILES['attachment_files']['type'][$i];
                $file_size = $_FILES['attachment_files']['size'][$i];
                $error = $_FILES['attachment_files']['error'][$i];

                if ($error === UPLOAD_ERR_OK) {
                    // Validar el tipo de archivo
                    if (in_array($file_type, $allowed_types)) {
                        // Use form_id and original filename with spaces replaced by underscores
                        $path_parts = pathinfo($file_name);
                        $filename_without_ext = $path_parts['filename'];
                        $extension = $path_parts['extension'];

                        // Reemplazar espacios y caracteres especiales por guiones bajos
                        $clean_filename = preg_replace('/[^A-Za-z0-9\-]/', '_', $filename_without_ext);

                        // Base name
                        $base_name = $form_id . '_' . $clean_filename;

                        // Iniciar con el nombre base
                        $unique_name = $base_name . '.' . $extension;

                        // Verificar si existe un archivo con el mismo nombre
                        $copy_number = 1;
                        while (file_exists($upload_dir . $unique_name)) {
                            $unique_name = $base_name . '_copia_' . $copy_number . '.' . $extension;
                            $copy_number++;
                        }

                        $destination = $upload_dir . $unique_name;

                        if (move_uploaded_file($tmp_name, $destination)) {
                            // Archivo subido con éxito
                            // Opcionalmente, puedes almacenar información del archivo en la base de datos
                        } else {
                            // Error al mover el archivo
                            echo json_encode(['status' => 'error', 'message' => 'Error al mover el archivo.']);
                            exit;
                        }
                    } else {
                        // Tipo de archivo inválido
                        echo json_encode(['status' => 'error', 'message' => 'Tipo de archivo inválido.']);
                        exit;
                    }
                } else {
                    // Manejar el error de subida
                    echo json_encode(['status' => 'error', 'message' => 'Error al subir el archivo.']);
                    exit;
                }
            }

            echo json_encode(['status' => 'success']);
            exit;
        } else {
            echo json_encode(['status' => 'error', 'message' => 'No se han seleccionado archivos.']);
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
