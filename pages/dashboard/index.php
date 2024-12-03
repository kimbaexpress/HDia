<?php
include '../../config/bdc/conex.php'; // Conexion base de datos
session_start();

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: ../../index.php");
    exit;
}


// Asegurarse de que el nombre está disponible en la sesión
$username = $_SESSION['username'];
$name = isset($_SESSION['name']) ? $_SESSION['name'] : 'Usuario'; // Fallback a 'Usuario' si no se encontró el nombre
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>hdia | Formularios</title>
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
                        HDIA <i class="fa-solid fa-viruses text-3xl text-blue-300"></i>
                    </h2>
                    <p class="text-xs text-gray-500 mt-0 text-center md:text-left ">Desarrollado por la</p>
                    <p class="text-xs text-gray-500 mt-0 text-center md:text-left mb-2">Unidad de Soporte Tecnico</p>
                    <hr class="border-t border-gray-300 mt-1 mb-2 w-4/5 md:w-full">
                    <p class="text-xs text-gray-500 mt-0 text-center md:text-left">Bienvenido/a, <?php echo $name ?></p>
                    <hr class="border-t border-gray-300 mt-2 mb-0 w-4/5 md:w-full">
                    <!-- Enlaces de navegación -->
                    <ul class="space-y-2 mt-4">
                        <li>
                            <a href="../dashboard/" class="flex items-center justify-center md:justify-start space-x-2 p-2 pt-0 pb-0 text-gray-600 rounded transition-colors group">
                                <i class="fas fa-layer-group text-gray-600 group-hover:text-gray-600"></i>
                                <span class="text-gray-600 group-hover:text-gray-600 font-medium text-sm">Formularios</span>
                            </a>
                        </li>
                        <li>
                            <a href="../buscador/" class="flex items-center justify-center md:justify-start space-x-2 p-2 text-gray-600 rounded transition-colors group">
                                <i class="fas fa-search text-gray-500 group-hover:text-gray-600"></i>
                                <span class="text-gray-500 group-hover:text-gray-600 text-sm">Buscador</span>
                            </a>
                        </li>
                        <li>
                            <a href="#"
                                class="flex items-center justify-center md:justify-start space-x-2 p-2 pt-0 pb-0 text-gray-600 rounded transition-colors group">
                                <i class="fa-regular fa-comment text-gray-500 group-hover:text-gray-600"></i>
                                <span id="conversations-span"
                                    class="text-gray-500 group-hover:text-gray-600 font-medium text-sm">Conversaciones</span>
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
                <h1 class="text-xl font-regular text-gray-700">Panel de Formularios</h1>
            </div>


            <div class="flex-1 p-5 flex items-center justify-center h-screen">
                <div class="w-full max-w-md">
                    <div class="bg-white shadow-md rounded-lg px-8 pt-6 pb-8 mb-4">
                        <div class="mb-2">
                            <span class="block text-gray-700 text-lg font-regular mb-2">
                                Selecciona un formulario:
                            </span>
                        </div>
                        <table class="table-auto w-full mb-4">
                            <tbody>
                                <tr class="border-b">
                                    <td class="px-4 py-2 w-full">Formulario Quimioterapia</td>
                                    <td class="px-4 py-2 text-right"><a href="../formularios/internacion/index.php" class="bg-gray-700 hover:bg-gray-700 text-white font-regular py-1 px-4 rounded">Iniciar</a></td>
                                </tr>
                                <tr class="border-b">
                                    <td class="px-4 py-2 w-full">Formulario Protocolo Sacarato</td>
                                    <td class="px-4 py-2 text-right"><a href="../formularios/sacarato/index.php" class="bg-gray-700 hover:bg-gray-800 text-white font-regular py-1 px-4 rounded">Iniciar</a></td>
                                </tr>
                                <tr class="border-b">
                                    <td class="px-4 py-2 w-full">Formulario Oncohematologia</td>
                                    <td class="px-4 py-2 text-right"><a href="../formularios/oncohematologia/index.php" class="bg-gray-700 hover:bg-gray-800 text-white font-regular py-1 px-4 rounded">Iniciar</a></td>
                                </tr>
                                <tr class="border-b">
                                    <td class="px-4 py-2 w-full">Formulario de ..</td>
                                    <td class="px-4 py-2 text-right"><a href="#" class="bg-gray-700 hover:bg-gray-800 text-white font-regular py-1 px-4 rounded">Iniciar</a></td>
                                </tr>
                                <tr class="border-b">
                                    <td class="px-4 py-2 w-full">Formulario de ..</td>
                                    <td class="px-4 py-2 text-right"><a href="#" class="bg-gray-700 hover:bg-gray-800 text-white font-regular py-1 px-4 rounded">Iniciar</a></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
            <script>
                $(document).ready(function() {
                    $('a:contains("Conversaciones")').click(function(e) {
                        e.preventDefault(); // Prevent default anchor click behavior
                        $("#submenuConversaciones").toggle(); // Toggle the submenu visibility
                    });
                });
            </script>
            <script>
                $(document).ready(function() {
                    // Abrir la sidebar
                    $('#menu-toggle').click(function() {
                        $('#sidebar').removeClass('-translate-x-full');
                        $('body').addClass('overflow-hidden');
                    });

                    // Cerrar la sidebar
                    $('#close-sidebar').click(function() {
                        $('#sidebar').addClass('-translate-x-full');
                        $('body').removeClass('overflow-hidden');
                    });
                });
            </script>
            <script>
                $(document).ready(function() {
                    updateUnreadCount();
                    setInterval(updateUnreadCount, 10000); // Actualizar cada 10 segundos

                    function updateUnreadCount() {
                        $.ajax({
                            url: '../../config/php/get_unread_messages.php', // Ajusta la ruta si es necesario
                            method: 'GET',
                            dataType: 'json',
                            success: function(response) {
                                if (response.status === 'success') {
                                    var unreadCount = parseInt(response.unread_count, 10);

                                    // Actualizar el título de la página
                                    if (unreadCount > 0) {
                                        document.title = '+' + unreadCount + ' HDIA | Formularios';
                                    } else {
                                        document.title = 'HDIA | Formularios';
                                    }

                                    // Actualizar el span de conversaciones
                                    var conversationsText = 'Conversaciones';
                                    if (unreadCount > 0) {
                                        $('#conversations-span').text('+' + unreadCount + ' ' + conversationsText);
                                    } else {
                                        $('#conversations-span').text(conversationsText);
                                    }
                                } else {
                                    console.error('Error al obtener el conteo de mensajes:', response.message);
                                }
                            },
                            error: function(xhr, status, error) {
                                console.error('Error en la solicitud AJAX:', error);
                                console.error('Respuesta del servidor:', xhr.responseText);
                            }
                        });
                    }
                });
            </script>

    </body>

</html>