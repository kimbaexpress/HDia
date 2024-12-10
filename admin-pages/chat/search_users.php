<?php
include '../../config/bdc/conex.php'; // Conexión a la base de datos
session_start();

// Verificar si el usuario está autenticado
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    echo json_encode([]);
    exit;
}

$user_id = $_SESSION['user_id'];
$searchTerm = isset($_GET['query']) ? trim($_GET['query']) : '';

if ($searchTerm !== '') {
    // Preparar la consulta para buscar usuarios que coincidan con el término, excluyendo al usuario actual
    $stmt = $conn->prepare("SELECT user_id, name, dni FROM users WHERE (name LIKE :searchTerm OR dni LIKE :searchTerm) AND user_id != :current_user_id LIMIT 10");
    $likeTerm = '%' . $searchTerm . '%';
    $stmt->bindParam(':searchTerm', $likeTerm, PDO::PARAM_STR);
    $stmt->bindParam(':current_user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Retornar los usuarios en formato JSON
    header('Content-Type: application/json');
    echo json_encode($users);
} else {
    // Retornar un array vacío si el término de búsqueda está vacío
    echo json_encode([]);
}
?>
