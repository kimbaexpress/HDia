<?php
include '../../config/bdc/conex.php'; // Ajusta la ruta si es necesario

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); // Método no permitido
    echo json_encode(['error' => 'Método no permitido.']);
    exit;
}

if (!isset($_POST['id_medication']) || !isset($_POST['brought_medicine'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Datos incompletos.']);
    exit;
}

$id_medication = intval($_POST['id_medication']);
$brought_medicine = intval($_POST['brought_medicine']) ? 1 : 0;

try {
    $stmt = $conn->prepare("UPDATE patient_medications SET brought_medicine = :brought_medicine WHERE id_medication = :id_medication");
    $stmt->bindParam(':brought_medicine', $brought_medicine, PDO::PARAM_INT);
    $stmt->bindParam(':id_medication', $id_medication, PDO::PARAM_INT);
    $stmt->execute();

    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error en la base de datos.']);
}
?>
