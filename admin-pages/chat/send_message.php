<?php
include '../../config/bdc/conex.php'; // Conexión a la base de datos
session_start();

// Verificar si el usuario está autenticado
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: /login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$conversation_id = isset($_POST['conversation_id']) ? intval($_POST['conversation_id']) : 0;
$message_content = trim($_POST['message']);

if ($conversation_id <= 0) {
    echo "Conversación inválida.";
    exit;
}

// Verificar que la conversación existe y que el usuario participa en ella
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
    echo "Conversación no encontrada o no tiene permiso para verla.";
    exit;
}

// Insertar el mensaje
$stmt = $conn->prepare("
    INSERT INTO messages (conversation_id, sender_id, content, sent_time)
    VALUES (:conversation_id, :sender_id, :content, NOW())
");
$stmt->bindParam(':conversation_id', $conversation_id, PDO::PARAM_INT);
$stmt->bindParam(':sender_id', $user_id, PDO::PARAM_INT);
$stmt->bindParam(':content', $message_content, PDO::PARAM_STR);
$stmt->execute();

// Actualizar el tiempo del último mensaje en la conversación
$stmt = $conn->prepare("
    UPDATE conversations
    SET last_message_time = NOW()
    WHERE conversation_id = :conversation_id
");
$stmt->bindParam(':conversation_id', $conversation_id, PDO::PARAM_INT);
$stmt->execute();

// Redirigir al chat
header("Location: chat.php?conversation_id=$conversation_id");
exit;
?>
