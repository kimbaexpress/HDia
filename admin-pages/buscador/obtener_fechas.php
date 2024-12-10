<?php
include '../../config/bdc/conex.php'; // Connection to the database

if (!isset($_GET['form_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'No form_id provided.']);
    exit;
}

$form_id = intval($_GET['form_id']);

try {
    $stmt = $conn->prepare("SELECT fecha_tto, fecha_1, fecha_2, fecha_3, fecha_4 FROM forms WHERE form_id = :form_id");
    $stmt->bindParam(':form_id', $form_id, PDO::PARAM_INT);
    $stmt->execute();
    $form = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($form) {
        // Convert dates from 'd/m/Y' to 'Y-m-d' format for the input fields
        function convertDateToInputFormat($dateStr) {
            if ($dateStr && $dateStr != 'NULL') {
                $date = DateTime::createFromFormat('d/m/Y', $dateStr);
                if ($date) {
                    return $date->format('Y-m-d');
                }
            }
            return '';
        }

        $fecha_tto = convertDateToInputFormat($form['fecha_tto']);
        $fecha_1 = convertDateToInputFormat($form['fecha_1']);
        $fecha_2 = convertDateToInputFormat($form['fecha_2']);
        $fecha_3 = convertDateToInputFormat($form['fecha_3']);
        $fecha_4 = convertDateToInputFormat($form['fecha_4']);

        echo json_encode([
            'status' => 'success',
            'fecha_tto' => $fecha_tto,
            'fecha_1' => $fecha_1,
            'fecha_2' => $fecha_2,
            'fecha_3' => $fecha_3,
            'fecha_4' => $fecha_4
        ]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Form not found.']);
    }
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'Database error.']);
}
