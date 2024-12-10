<?php
session_start();
include '../bdc/conex.php'; // Ajusta la ruta según tu estructura de archivos

// Habilitar la visualización de errores para depuración (quitar en producción)
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Verificar si el usuario está autenticado
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    echo json_encode(['status' => 'error', 'message' => 'Usuario no autenticado']);
    exit;
}

$user_id = $_SESSION['user_id'];

// Consulta actualizada para obtener el número de mensajes no leídos
$stmt = $conn->prepare("
    SELECT COUNT(*) as unread_count
    FROM messages m
    INNER JOIN conversations c ON m.conversation_id = c.conversation_id
    WHERE (c.user1_id = :user_id OR c.user2_id = :user_id)
      AND m.sender_id != :user_id
      AND m.seen_time IS NULL
");
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmt->execute();
$result = $stmt->fetch(PDO::FETCH_ASSOC);

$unread_count = $result['unread_count'] ?? 0;

// Establecer el header de contenido
header('Content-Type: application/json');

// Retornar el conteo en formato JSON
echo json_encode(['status' => 'success', 'unread_count' => $unread_count]);
?>
