<?php
include '../../config/bdc/conex.php'; // Conexión a la base de datos
session_start();

// Establecer el header de contenido
header('Content-Type: application/json');

// Verificar si el usuario está autenticado
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    echo json_encode(['status' => 'error', 'message' => 'Usuario no autenticado.']);
    exit;
}

// Obtener los datos del formulario
$sender_id = $_SESSION['user_id'];
$receiver_id = isset($_POST['receiver_id']) ? intval($_POST['receiver_id']) : 0;
$message_content = trim($_POST['message']);

// Validar que se haya proporcionado un receiver_id válido
if ($receiver_id <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'Debe seleccionar un usuario válido.']);
    exit;
}

// Validar que el mensaje no esté vacío
if (empty($message_content)) {
    echo json_encode(['status' => 'error', 'message' => 'El mensaje no puede estar vacío.']);
    exit;
}

// Validar que el usuario receptor exista
$stmt = $conn->prepare("SELECT user_id FROM users WHERE user_id = :receiver_id");
$stmt->bindParam(':receiver_id', $receiver_id, PDO::PARAM_INT);
$stmt->execute();
$receiver_exists = $stmt->fetchColumn();

if (!$receiver_exists) {
    echo json_encode(['status' => 'error', 'message' => 'El usuario seleccionado no existe.']);
    exit;
}

try {
    // Iniciar una transacción
    $conn->beginTransaction();

    // Verificar si ya existe una conversación entre estos usuarios
    $stmt = $conn->prepare("
        SELECT conversation_id FROM conversations
        WHERE (user1_id = :user1 AND user2_id = :user2)
           OR (user1_id = :user2 AND user2_id = :user1)
    ");
    $stmt->bindParam(':user1', $sender_id, PDO::PARAM_INT);
    $stmt->bindParam(':user2', $receiver_id, PDO::PARAM_INT);
    $stmt->execute();
    $conversation = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($conversation) {
        // La conversación ya existe
        $conversation_id = $conversation['conversation_id'];
    } else {
        // Crear una nueva conversación
        $stmt = $conn->prepare("
            INSERT INTO conversations (user1_id, user2_id, last_message_time)
            VALUES (:user1_id, :user2_id, NOW())
        ");
        $stmt->bindParam(':user1_id', $sender_id, PDO::PARAM_INT);
        $stmt->bindParam(':user2_id', $receiver_id, PDO::PARAM_INT);
        $stmt->execute();
        $conversation_id = $conn->lastInsertId();
    }

    // Insertar el mensaje inicial
    $stmt = $conn->prepare("
        INSERT INTO messages (conversation_id, sender_id, content, sent_time)
        VALUES (:conversation_id, :sender_id, :content, NOW())
    ");
    $stmt->bindParam(':conversation_id', $conversation_id, PDO::PARAM_INT);
    $stmt->bindParam(':sender_id', $sender_id, PDO::PARAM_INT);
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

    // Confirmar la transacción
    $conn->commit();

    // Retornar éxito
    echo json_encode(['status' => 'success', 'conversation_id' => $conversation_id]);

} catch (Exception $e) {
    // Revertir la transacción en caso de error
    $conn->rollBack();
    echo json_encode(['status' => 'error', 'message' => 'Ocurrió un error al crear la conversación.']);
    exit;
}
?>
