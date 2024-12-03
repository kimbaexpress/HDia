<?php
include '../../config/bdc/conex.php';
session_start();

header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    http_response_code(401);
    echo json_encode(['error' => 'No autenticado']);
    exit;
}

$user_id = $_SESSION['user_id'];
$conversation_id = isset($_GET['conversation_id']) ? intval($_GET['conversation_id']) : 0;

if ($conversation_id <= 0) {
    http_response_code(400);
    echo json_encode(['error' => 'Conversación inválida']);
    exit;
}

// Verificar que el usuario participa en la conversación
$stmt = $conn->prepare("
    SELECT * FROM conversations
    WHERE conversation_id = :conversation_id
      AND (user1_id = :user_id OR user2_id = :user_id)
");
$stmt->bindParam(':conversation_id', $conversation_id, PDO::PARAM_INT);
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmt->execute();
$conversation = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$conversation) {
    http_response_code(403);
    echo json_encode(['error' => 'No tiene permiso para ver esta conversación']);
    exit;
}

// Obtener todos los mensajes de la conversación
$stmt = $conn->prepare("
    SELECT m.*, u.name AS sender_name
    FROM messages m
    JOIN users u ON m.sender_id = u.user_id
    WHERE m.conversation_id = :conversation_id
    ORDER BY m.sent_time ASC
");
$stmt->bindParam(':conversation_id', $conversation_id, PDO::PARAM_INT);
$stmt->execute();
$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Marcar los mensajes recibidos como vistos
$stmt = $conn->prepare("
    UPDATE messages
    SET seen_by_receiver = TRUE, seen_time = NOW()
    WHERE conversation_id = :conversation_id
      AND sender_id != :user_id
      AND seen_by_receiver = FALSE
");
$stmt->bindParam(':conversation_id', $conversation_id, PDO::PARAM_INT);
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmt->execute();

echo json_encode(['messages' => $messages]);
?>
