<?php
include '../../config/bdc/conex.php'; // Conexión a la base de datos
session_start();

// Verificar si el usuario está autenticado
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: /login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$conversation_id = isset($_GET['conversation_id']) ? intval($_GET['conversation_id']) : 0;

// Obtener la conversación
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

// Determinar el ID y nombre del otro usuario
$other_user_id = ($conversation['user1_id'] == $user_id) ? $conversation['user2_id'] : $conversation['user1_id'];

// Obtener el nombre del otro usuario
$stmt = $conn->prepare("SELECT name FROM users WHERE user_id = :other_user_id");
$stmt->bindParam(':other_user_id', $other_user_id, PDO::PARAM_INT);
$stmt->execute();
$other_user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($other_user) {
    $other_user_name = $other_user['name'];
} else {
    $other_user_name = 'Usuario desconocido';
}
// Asegurarse de que el nombre está disponible en la sesión
$username = $_SESSION['username'];
$name = isset($_SESSION['name']) ? $_SESSION['name'] : 'Usuario'; // Fallback a 'Usuario' si no se encontró el nombre
?>
<!-- ... código PHP inicial ... -->
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HDIA | Chat</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link rel="shortcut icon" href="../../config/favicon.ico" type="image/x-icon">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Righteous&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/boxicons@2.0.7/css/boxicons.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@100..900&display=swap" rel="stylesheet">
    <script src="https://kit.fontawesome.com/bfe519afef.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        /* Contenedor principal del chat */
        #chat {
            display: flex;
            flex-direction: column;
        }

        /* Estilos para los mensajes */
        .message {
            max-width: 60%;
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 10px;
            word-wrap: break-word;
        }

        .sent {
            background-color: #dcf8c6;
            /* Verde claro */
            align-self: flex-end;
            text-align: right;
        }

        .received {
            background-color: #D9EDFF;
            /* Blanco */
            align-self: flex-start;
            text-align: left;
        }

        /* Información del remitente y hora */
        .sender-info {
            font-size: 0.8rem;
            color: #555;
            margin-bottom: 5px;
        }

        .message-content {
            font-size: 1rem;
            color: #000;
        }

        .seen-info {
            font-size: 0.7rem;
            color: #999;
            margin-top: 5px;
        }


        /* Resetear márgenes y paddings para eliminar cualquier espacio extra por defecto */
        html,
        body {
            margin: 0;
            padding: 0;
            height: 100%;
            /* Asegura que el elemento html y body ocupen todo el alto disponible */
            width: 100%;
            /* Asegura que el elemento html y body ocupen todo el ancho disponible */
            overflow: hidden;
            /* Evita scrollbars si no son necesarios */
        }

        #fullsize-div {
            width: 100%;
            /* Asegura que el div ocupe todo el ancho del body */
            height: 100%;
            /* Asegura que el div ocupe todo el alto del body */
            background-color: #3498db;
            /* Color de fondo para visualización */
        }
    </style>
</head>

<body>

    <body class="bg-gray-100 flex h-screen">
        <div class="flex h-full">
            <!-- Sidebar -->
            <div id="sidebar" class="fixed inset-0 transform -translate-x-full transition-transform duration-300 ease-in-out z-50 md:relative md:translate-x-0 md:inset-auto md:h-full md:w-55 bg-white p-5 flex flex-col">
                <!-- Contenido superior -->
                <div class="flex flex-col items-center md:items-start">
                    <!-- Botón de cierre para móviles -->
                    <div class="flex justify-end w-full md:hidden">
                        <button id="close-sidebar" class="text-gray-600 focus:outline-none">
                            <i class="fas fa-bars text-2xl"></i>
                        </button>
                    </div>
                    <!-- Logo y mensaje de bienvenida -->
                    <h2 class="text-4xl text-gray-700 text-center font-bold md:text-left flex items-center justify-center md:justify-start">
                        HDIA <i class="fa-solid fa-viruses text-3xl"></i>
                    </h2>
                    <p class="text-xs text-gray-500 mt-0 text-center md:text-left ">Desarrollado por</p>
                    <p class="text-xs text-gray-500 mt-0 text-center md:text-left mb-2">Beato Federico</p>
                    <hr class="border-t border-gray-300 mt-1 mb-2 w-4/5 md:w-full">
                    <p class="text-xs text-gray-500 mt-0 text-center md:text-left">Bienvenido/a, <?php echo $name ?></p>
                    <hr class="border-t border-gray-300 mt-2 mb-0 w-4/5 md:w-full">
                    <!-- Enlaces de navegación -->
                    <ul class="space-y-2 mt-4">
                        <li>
                            <a href="../dashboard/" class="flex items-center justify-center md:justify-start space-x-2 p-2 pt-0 pb-0 text-gray-600 rounded transition-colors group">
                                <i class="fas fa-layer-group text-gray-500 group-hover:text-gray-600"></i>
                                <span class="text-gray-500 group-hover:text-gray-600 font-medium text-sm">Formularios</span>
                            </a>
                        </li>
                        <li>
                            <a href="../buscador/" class="flex items-center justify-center md:justify-start space-x-2 p-2 text-gray-600 rounded transition-colors group">
                                <i class="fas fa-search text-gray-500 group-hover:text-gray-600"></i>
                                <span class="text-gray-500 group-hover:text-gray-600 text-sm">Buscador</span>
                            </a>
                        </li>
                        <li>
                            <a href="#" class="flex items-center justify-center md:justify-start space-x-2 p-2 pt-0 pb-0 text-gray-600 rounded transition-colors group">
                                <i class="fa-regular fa-comment text-gray-600 group-hover:text-gray-600"></i>
                                <span id="conversations-span" class="text-gray-600 group-hover:text-gray-600 font-medium text-sm">Conversaciones</span>
                            </a>

                            <!--Div dentro de LI para hacer el submenu del botón de conversaciones. -->
                            <div class="space-y-2 ml-4 mt-1 hidden" id="submenuConversaciones">
                                <!-- Nueva Conversación -->
                                <a href="../chat" class="flex items-center space-x-2 p-2 text-gray-600 rounded transition-colors group" style="padding-bottom: 0px;">
                                    <i class="text-gray-500 fa-regular fa-paper-plane group-hover:text-gray-600 "></i>
                                    <span class="text-gray-500 group-hover:text-gray-600 text-sm">Crear nueva</span>
                                </a>
                                <!-- Visualizar Conversación -->
                                <a href="../chat/conversations.php" class="flex items-center space-x-2 p-2 text-gray-600 rounded transition-colors group" style="padding-bottom: 0px;">
                                    <i class="text-gray-500 fa-regular fa-comments group-hover:text-gray-600"></i>
                                    <span class="text-gray-500 group-hover:text-gray-600 text-sm">Buscar</span>
                                </a>
                            </div>
                        </li>
                    </ul>
                </div>
                <!-- Botón Cerrar sesión -->
                <div class="mt-auto mb-4 flex justify-center md:justify-start">
                    <a href="../../config/logout.php" class="flex items-center space-x-2 group">
                        <i class="fas fa-sign-out-alt text-gray-500 group-hover:text-gray-600"></i>
                        <span class="text-gray-500 group-hover:text-gray-600">Cerrar sesión</span>
                    </a>
                </div>
            </div>


        </div>

        <!-- INICIO DEL CODIGO DESPUES DE LA SIDEBAR -->

        <div id="main-content" class="flex-1 flex flex-col">
            <!-- Encabezado -->
            <div class="bg-white flex items-center p-5 border-b">
                <!-- Ícono de menú para móviles -->
                <button id="menu-toggle" class="text-gray-600 focus:outline-none mr-3 md:hidden">
                    <i class="fas fa-bars text-2xl"></i>
                </button>
                <h1 class="text-xl font-regular text-gray-700">Conversación con <?php echo htmlspecialchars($other_user_name); ?></h1>
            </div>

            <div class="container mx-auto px-4 py-8 flex flex-col h-full min-h-screen fullsize-div">



                <!-- Área de mensajes -->
                <div id="chat" class="h-5/6 overflow-y-auto bg-white p-4 shadow rounded mb-4">
                    <!-- Los mensajes se cargarán aquí mediante AJAX -->

                </div>

                <!-- Formulario para enviar mensaje -->
                <form id="messageForm" class="flex">
                    <textarea name="message" id="message" rows="1" required
                        class="flex-1 shadow appearance-none border rounded py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                        placeholder="Escribe tu mensaje..."></textarea>
                    <button type="submit"
                        class="ml-4 bg-gray-700 hover:bg-gray-700 text-white font-regular py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                        Enviar Mensaje
                    </button>
                </form>
            </div>
        </div>

        </div>
        </div>
        <script src="js/chat.js"></script>
        <script>
            const conversationId = <?php echo $conversation_id; ?>;
            let lastMessageId = 0;

            // Función para cargar mensajes
            function loadMessages() {
                fetch(`get_messages.php?conversation_id=${conversationId}`)
                    .then(response => response.json())
                    .then(data => {
                        console.log('Datos recibidos:', data);
                        if (data.error) {
                            console.error(data.error);
                            return;
                        }
                        const chat = document.getElementById('chat');

                        // Comprobar si el usuario está en la parte inferior
                        const isAtBottom = chat.scrollHeight - chat.scrollTop <= chat.clientHeight + 1;

                        // Limpiar el área de chat
                        chat.innerHTML = '';

                        data.messages.forEach(message => {
                            // Crear elemento de mensaje
                            const messageDiv = document.createElement('div');
                            messageDiv.classList.add('message');

                            // Agregar clases según el remitente
                            if (parseInt(message.sender_id) === <?php echo $user_id; ?>) {
                                messageDiv.classList.add('sent');
                            } else {
                                messageDiv.classList.add('received');
                            }

                            // Crear contenido del mensaje
                            const senderInfo = document.createElement('div');
                            senderInfo.classList.add('sender-info');
                            senderInfo.innerHTML = `<span>${message.sender_name}</span> <span class="text-xs text-gray-500">${message.sent_time}</span>`;

                            const content = document.createElement('div');
                            content.classList.add('message-content');
                            content.innerHTML = message.content.replace(/\n/g, '<br>');

                            messageDiv.appendChild(senderInfo);
                            messageDiv.appendChild(content);

                            if (parseInt(message.sender_id) === <?php echo $user_id; ?>) {
                                const seenInfo = document.createElement('div');
                                seenInfo.classList.add('seen-info');
                                if (message.seen_by_receiver == "1") {
                                    seenInfo.textContent = `Visto el ${message.seen_time}`;
                                } else {
                                    seenInfo.textContent = 'No visto aún';
                                }
                                messageDiv.appendChild(seenInfo);
                            }

                            chat.appendChild(messageDiv);
                        });

                        // Solo desplazamos el scroll si el usuario estaba en la parte inferior
                        if (isAtBottom) {
                            chat.scrollTop = chat.scrollHeight;
                        }
                    })
                    .catch(error => console.error('Error al cargar mensajes:', error));
            }


            // Cargar mensajes inicialmente
            loadMessages();

            // Recargar mensajes cada 3 segundos
            setInterval(loadMessages, 3000);

            // Manejar el envío del formulario de mensaje
            document.getElementById('messageForm').addEventListener('submit', function(e) {
                e.preventDefault();
                const messageInput = document.getElementById('message');
                const message = messageInput.value.trim();
                if (message === '') return;

                // Enviar el mensaje mediante fetch
                fetch('send_message.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: `conversation_id=${conversationId}&message=${encodeURIComponent(message)}`
                    })
                    .then(response => response.text())
                    .then(data => {
                        // Limpiar el campo de mensaje
                        messageInput.value = '';
                        // Cargar mensajes nuevamente
                        loadMessages();
                    })
                    .catch(error => console.error('Error al enviar mensaje:', error));
            });
        </script>
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    </body>
</html>