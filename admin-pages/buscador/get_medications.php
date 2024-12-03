<?php
include '../../config/bdc/conex.php'; // Ajusta la ruta si es necesario

if (!isset($_GET['form_id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'form_id no especificado.']);
    exit;
}

$form_id = intval($_GET['form_id']);

try {
    // Obtener el nombre del paciente de la tabla 'forms'
    $stmt = $conn->prepare("SELECT nombre_pte, dni_pte FROM forms WHERE form_id = :form_id");
    $stmt->bindParam(':form_id', $form_id, PDO::PARAM_INT);
    $stmt->execute();
    $form = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$form) {
        http_response_code(404);
        echo json_encode(['error' => 'Formulario no encontrado.']);
        exit;
    }
    $nombre_pte = $form['nombre_pte'];
    $dni_pte = $form['dni_pte'];
    // Obtener las medicaciones
    $stmt = $conn->prepare("SELECT id_medication, medication_name, brought_medicine FROM patient_medications WHERE id_form = :form_id");
    $stmt->bindParam(':form_id', $form_id, PDO::PARAM_INT);
    $stmt->execute();
    $medications = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Devolver tanto las medicaciones como el nombre del paciente
    $response = [
        'nombre_pte' => $nombre_pte,
        'dni_pte' => $dni_pte,
        'medications' => $medications
    ];

    echo json_encode($response);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error en la base de datos.']);
}

?>
