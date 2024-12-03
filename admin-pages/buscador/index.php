<?php
include '../../config/bdc/conex.php'; // Conexión a la base de datos

session_start();

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: /login.php");
    exit;
}

// Obtener el ID y el rol del usuario desde la sesión
$creator_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;
$user_role = isset($_SESSION['role']) ? strtolower($_SESSION['role']) : 'medico'; // Convertir el rol a minúsculas

// Valores predeterminados para búsqueda
$search_nombre_pte = '';
$search_dni_pte = '';
$search_form_type = '';
$search_creation_date = '';
$search_new_date = ''; // Agrega esta línea
$search_nombre_pte = '';

// Verificar si se enviaron parámetros de búsqueda
if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $search_nombre_pte = isset($_GET['search_nombre_pte']) ? trim($_GET['search_nombre_pte']) : '';
    $search_dni_pte = isset($_GET['search_dni_pte']) ? trim($_GET['search_dni_pte']) : '';
    $search_form_type = isset($_GET['search_form_type']) ? trim($_GET['search_form_type']) : '';
    $search_creation_date = isset($_GET['search_creation_date']) ? trim($_GET['search_creation_date']) : '';
    $search_new_date = isset($_GET['search_new_date']) ? trim($_GET['search_new_date']) : ''; // Agrega esta línea
}

// Paginación
$records_per_page = 8;
$current_page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($current_page - 1) * $records_per_page;

// Construir la cláusula WHERE
$where_clauses = [];
$params = [];

// Filtros de búsqueda
if (!empty($search_nombre_pte)) {
    $where_clauses[] = "f.nombre_pte LIKE :nombre_pte";
    $params[':nombre_pte'] = '%' . $search_nombre_pte . '%';
}

if (!empty($search_dni_pte)) {
    $where_clauses[] = "f.dni_pte LIKE :dni_pte";
    $params[':dni_pte'] = '%' . $search_dni_pte . '%';
}

if (!empty($search_form_type)) {
    $where_clauses[] = "f.form_type = :form_type";
    $params[':form_type'] = $search_form_type;
}

if (!empty($search_creation_date)) {
    $where_clauses[] = "DATE(f.creation_date) = :creation_date";
    $params[':creation_date'] = $search_creation_date;
}
// Filtros de búsqueda
if (!empty($search_new_date)) {
    $where_clauses[] = "(
        STR_TO_DATE(f.fecha_tto, '%d/%m/%Y') = :search_new_date OR
        STR_TO_DATE(f.fecha_1, '%d/%m/%Y') = :search_new_date OR
        STR_TO_DATE(f.fecha_2, '%d/%m/%Y') = :search_new_date OR
        STR_TO_DATE(f.fecha_3, '%d/%m/%Y') = :search_new_date OR
        STR_TO_DATE(f.fecha_4, '%d/%m/%Y') = :search_new_date
    )";
    $params[':search_new_date'] = $search_new_date;
}


// Condición adicional según el rol del usuario
if ($user_role === 'medico') {
    $where_clauses[] = "f.creator_id = :creator_id";
    $params[':creator_id'] = $creator_id;
} elseif ($user_role === 'supervisor') {
    $where_clauses[] = "f.status = 'correcto'";
} elseif ($user_role === 'moderador') {
    $where_clauses[] = "f.status = 'correcto'";
}


// Combinar las cláusulas WHERE
$where_sql = '';
if (!empty($where_clauses)) {
    $where_sql = 'WHERE ' . implode(' AND ', $where_clauses);
}

// Contar el total de registros
$total_records_sql = "SELECT COUNT(*) FROM forms f $where_sql";
$stmt = $conn->prepare($total_records_sql);
// Vincular parámetros de búsqueda
foreach ($params as $key => &$value) {
    if ($key === ':creator_id') {
        $stmt->bindParam($key, $value, PDO::PARAM_INT);
    } else {
        $stmt->bindParam($key, $value, PDO::PARAM_STR);
    }
}

$stmt->execute();
$total_records = $stmt->fetchColumn();
// Calcular el total de páginas
$total_pages = ceil($total_records / $records_per_page);
// Definir el número máximo de botones a mostrar
$max_visible_pages = 5;
// Calcular el rango de páginas a mostrar
$start_page = max(1, $current_page - floor($max_visible_pages / 2));
$end_page = min($total_pages, $start_page + $max_visible_pages - 1);
// Ajustar el start_page si estamos cerca del final
if ($end_page - $start_page + 1 < $max_visible_pages) {
    $start_page = max(1, $end_page - $max_visible_pages + 1);
}
// Obtener los registros para la página actual con JOIN a users para obtener el nombre del creador
$sql = "
    SELECT f.*, u.name AS creador
    FROM forms f
    INNER JOIN users u ON f.creator_id = u.user_id
    $where_sql
    ORDER BY f.creation_date DESC
    LIMIT :limit OFFSET :offset
";
$stmt = $conn->prepare($sql);
// Vincular parámetros de búsqueda
foreach ($params as $key => &$value) {
    if ($key === ':creator_id') {
        $stmt->bindParam($key, $value, PDO::PARAM_INT);
    } else {
        $stmt->bindParam($key, $value, PDO::PARAM_STR);
    }
}
// Vincular limit y offset
$stmt->bindValue(':limit', $records_per_page, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$forms = $stmt->fetchAll(PDO::FETCH_ASSOC);
// Asegurarse de que el nombre está disponible en la sesión
$username = $_SESSION['username'];
$name = isset($_SESSION['name']) ? $_SESSION['name'] : 'Usuario'; // Fallback a 'Usuario' si no se encontró el nombre
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HDIA | Buscador</title>
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
    <style>

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
                        HDIA <i class="fa-solid fa-viruses text-3xl text-blue-300"></i>
                    </h2>
                    <p class="text-xs text-gray-500 mt-0 text-center md:text-left ">Desarrollado por la</p>
                    <p class="text-xs text-gray-500 mt-0 text-center md:text-left mb-2">Unidad de Soporte Tecnico</p>
                    <hr class="border-t border-gray-300 mt-1 mb-2 w-4/5 md:w-full">
                    <p class="text-xs text-gray-500 mt-0 text-center md:text-left">Bienvenido/a, <?php echo $name ?></p>
                    <hr class="border-t border-gray-300 mt-2 mb-0 w-4/5 md:w-full">
                    <!-- Enlaces de navegación -->
                    <ul class="space-y-2 mt-4">
                        <?php if ($user_role === 'admin'): ?>
                            <li>
                                <a href="../dashboard/" class="flex items-center justify-center md:justify-start space-x-2 p-2 pt-0 pb-0 text-gray-600 rounded transition-colors group">
                                    <i class="fas fa-layer-group text-gray-500 group-hover:text-gray-600"></i>
                                    <span class="text-gray-500 group-hover:text-gray-600 font-medium text-sm">Formularios</span>
                                </a>
                            </li>
                        <?php endif; ?>
                        <li>
                            <a href="../buscador/" class="flex items-center justify-center md:justify-start space-x-2 p-2 text-gray-600 rounded transition-colors group">
                                <i class="fas fa-search text-gray-600 group-hover:text-gray-600"></i>
                                <span class="text-gray-600 group-hover:text-gray-600 text-sm">Buscador</span>
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
        <!-- INICIO DE CONTENIDO DESPS DE LA SIDEBAR -->
        <div id="main-content" class="flex-1 flex flex-col">
            <!-- Encabezado -->
            <div class="bg-white flex items-center p-5 border-b">
                <!-- Ícono de menú para móviles -->
                <button id="menu-toggle" class="text-gray-600 focus:outline-none mr-3 md:hidden">
                    <i class="fas fa-bars text-2xl"></i>
                </button>
                <h1 class="text-xl font-regular text-gray-700">Mis Formularios</h1>
            </div>


            <!-- Formulario de búsqueda -->
            <div class="p-4">
                <form method="GET" action="" class="mb-6">
                    <div class="flex flex-wrap items-end -mx-2">
                        <div class="w-full md:w-1/5 px-2 mb-4">
                            <label for="search_nombre_pte" class="block text-gray-700">Nombre de Paciente:</label>
                            <input type="text" name="search_nombre_pte" id="search_nombre_pte" value="<?php echo htmlspecialchars($search_nombre_pte); ?>" class="w-full px-3 py-2 border rounded">
                        </div>
                        <div class="w-full md:w-1/5 px-2 mb-4">
                            <label for="search_dni_pte" class="block text-gray-700">DNI Paciente:</label>
                            <input type="text" name="search_dni_pte" id="search_dni_pte" value="<?php echo htmlspecialchars($search_dni_pte); ?>" class="w-full px-3 py-2 border rounded">
                        </div>
                        <div class="w-full md:w-1/5 px-2 mb-4">
                            <label for="search_form_type" class="block text-gray-700">Tipo de Formulario:</label>
                            <select name="search_form_type" id="search_form_type" class="w-full px-3 py-2 border rounded">
                                <option value="">Todos</option>
                                <option value="internacion" <?php if ($search_form_type == 'internacion') echo 'selected'; ?>>Internación</option>
                                <option value="sacarato" <?php if ($search_form_type == 'sacarato') echo 'selected'; ?>>Sacarato</option>
                            </select>
                        </div>
                        <div class="w-full md:w-1/5 px-2 mb-4">
                            <label for="search_creation_date" class="block text-gray-700">Fecha de Creación:</label>
                            <input type="date" name="search_creation_date" id="search_creation_date" value="<?php echo htmlspecialchars($search_creation_date); ?>" class="w-full px-3 py-2 border rounded">
                        </div>
                        <div class="w-1/2 md:w-1/5 px-2 mb-4">
                            <label for="search_new_date" class="block text-gray-700">Fecha TTO / 1 / 2 / 3 / 4:</label>
                            <input type="date" name="search_new_date" id="search_new_date" value="<?php echo htmlspecialchars($search_new_date); ?>" class="w-full px-3 py-2 border rounded">
                        </div>

                        <div class="w-full md:w-auto px-2 mb-4">
                            <button type="submit" class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded">Buscar</button>

                            <a href="../buscador/index.php" class=" space-x-2 group ml-2">
                                <i class="fa-solid fa-arrows-rotate text-gray-600 group-hover:text-gray-600"></i>

                            </a>

                        </div>
                    </div>
                </form>

                <!-- Tabla de resultados -->
                <div class="bg-white shadow rounded">
                    <table class="min-w-full table-auto">
                        <thead>
                            <tr class="bg-gray-200 text-gray-700 uppercase text-sm leading-normal">
                                <th class="py-3 px-6 text-left">N°</th>
                                <th class="py-3 px-6 text-left">Creador</th>
                                <th class="py-3 px-6 text-left">Tipo de Formulario</th>
                                <th class="py-3 px-6 text-left">Nombre de Paciente</th>
                                <th class="py-3 px-6 text-left">DNI Paciente</th>
                                <th class="py-3 px-6 text-left">Fecha de Creación</th>
                                <th class="py-3 px-6 text-left">Estado</th>
                                <th class="py-3 px-6 text-left">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-600 text-sm font-light">
                            <?php foreach ($forms as $form):

                                $status = strtolower($form['status']); // Convertimos el estado a minúsculas para facilitar la comparación
                                $status_color_text = '';
                                $status_color_bg = '';
                                if ($status == 'correcto') {
                                    $status_color_text = 'text-green-600';
                                    $status_color_bg = 'bg-green-500';
                                } elseif ($status == 'rechazado') {
                                    $status_color_text = 'text-red-600';
                                    $status_color_bg = 'bg-red-500';
                                } elseif ($status == 'en revision') {
                                    $status_color_text = 'text-blue-600';
                                    $status_color_bg = 'bg-blue-500';
                                } ?>

                                <tr class="border-b border-gray-200 hover:bg-gray-100" data-form-id="<?php echo $form['form_id']; ?>">
                                    <td class="py-3 px-6 text-left"><?php echo htmlspecialchars($form['form_id']); ?></td>
                                    <td class="py-3 px-6 text-left"><?php echo htmlspecialchars($form['creador']); ?></td>
                                    <td class="py-3 px-6 text-left capitalize"><?php echo htmlspecialchars($form['form_type']); ?></td>
                                    <td class="py-3 px-6 text-left uppercase"><?php echo htmlspecialchars($form['nombre_pte']); ?></td>
                                    <td class="py-3 px-6 text-left"><?php echo htmlspecialchars($form['dni_pte']); ?></td>
                                    <td class="py-3 px-6 text-left">
                                        <?php
                                        // Formatear la fecha a d/m/Y H:i
                                        echo date('d/m/Y H:i', strtotime($form['creation_date']));
                                        ?>
                                    </td>
                                    <!-- Agregar la columna de estado -->
                                    <!-- Agregar la columna de estado -->
                                    <td class="py-3 px-6 text-left status-cell uppercase">
                                        <span class="flex items-center">
                                            <span class="inline-block w-2 h-2 mr-2 rounded-full <?php echo $status_color_bg; ?>"></span>
                                            <span><?php echo strtoupper($status); ?></span>
                                        </span>
                                    </td>

                                    <td class="py-3 px-6 text-left">
                                        <a href="../../pages/formularios/forms_conteiner/<?php echo urlencode($form['file_name']); ?>" target="_blank" class="text-blue-500 hover:text-blue-700">
                                            <i class="text-gray-700 fa-regular fa-file-pdf group-hover:text-gray-800 "></i>
                                        </a>
                                        <?php if ($user_role === 'admin'): ?>
                                            <!-- Botón para cambiar Fechas -->
                                            <button class="edit-button" data-form-id="<?php echo $form['form_id']; ?>">
                                                <i class="text-gray-700 fa-solid fa-pen-to-square group-hover:text-gray-800 ml-2"></i>
                                            </button>
                                        <?php endif; ?>


                                        <?php if ($user_role === 'admin' || $user_role === 'moderador'): ?>
                                            <!-- Botón para cambiar estado de Medicación -->
                                            <button class="medication-button" data-form-id="<?php echo $form['form_id']; ?>">
                                                <i class="text-gray-700 fa-solid fa-capsules group-hover:text-gray-800 ml-2"></i>
                                            </button>
                                        <?php endif; ?>



                                        <button class="attachments-button" data-form-id="<?php echo $form['form_id']; ?>">
                                            <i class="text-gray-700 fa-solid fa-folder group-hover:text-gray-800 pl-2"></i>
                                        </button>

                                        <?php if ($user_role === 'admin'): ?>
                                            <!-- Botón para cambiar el estado -->
                                            <button class="status-button" data-form-id="<?php echo $form['form_id']; ?>" data-current-status="<?php echo $form['status']; ?>">
                                                <i class="text-gray-700 fa-solid fa-sliders group-hover:text-gray-800 ml-2"></i>
                                            </button>
                                            <!-- Botón para eliminar -->
                                            <button class="delete-button" data-form-id="<?php echo $form['form_id']; ?>" data-file-name="<?php echo $form['file_name']; ?>">
                                                <i class="text-gray-700 fa-solid fa-trash-alt group-hover:text-gray-800 ml-2"></i>
                                            </button>
                                        <?php endif; ?>
                                    </td>


                                </tr>
                            <?php endforeach; ?>
                            <?php if (empty($forms)): ?>
                                <tr>
                                    <td colspan="7" class="py-3 px-6 text-center">No se encontraron registros.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Paginación -->
                <!-- Paginación -->
                <div class="mt-4">
                    <?php if ($total_pages > 1): ?>
                        <nav class="flex justify-center">
                            <ul class="flex pl-0 list-none rounded">
                                <!-- Botón Primero -->
                                <?php if ($current_page > 1): ?>
                                    <li>
                                        <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => 1])); ?>" class="px-3 py-2 ml-0 leading-tight text-gray-500 bg-white border border-gray-300 hover:bg-gray-100 hover:text-gray-700">Primero</a>
                                    </li>
                                <?php endif; ?>

                                <!-- Botón Anterior -->
                                <?php if ($current_page > 1): ?>
                                    <li>
                                        <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $current_page - 1])); ?>" class="px-3 py-2 leading-tight text-gray-500 bg-white border border-gray-300 hover:bg-gray-100 hover:text-gray-700">Anterior</a>
                                    </li>
                                <?php endif; ?>

                                <!-- Números de página -->
                                <?php for ($i = $start_page; $i <= $end_page; $i++): ?>
                                    <?php if ($i == $current_page): ?>
                                        <li>
                                            <span class="px-3 py-2 leading-tight text-gray-600 bg-gray-50 border border-gray-300"><?php echo $i; ?></span>
                                        </li>
                                    <?php else: ?>
                                        <li>
                                            <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $i])); ?>" class="px-3 py-2 leading-tight text-gray-500 bg-white border border-gray-300 hover:bg-gray-100 hover:text-gray-700"><?php echo $i; ?></a>
                                        </li>
                                    <?php endif; ?>
                                <?php endfor; ?>

                                <!-- Botón Siguiente -->
                                <?php if ($current_page < $total_pages): ?>
                                    <li>
                                        <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $current_page + 1])); ?>" class="px-3 py-2 leading-tight text-gray-500 bg-white border border-gray-300 hover:bg-gray-100 hover:text-gray-700">Siguiente</a>
                                    </li>
                                <?php endif; ?>

                                <!-- Botón Último -->
                                <?php if ($current_page < $total_pages): ?>
                                    <li>
                                        <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $total_pages])); ?>" class="px-3 py-2 leading-tight text-gray-500 bg-white border border-gray-300 hover:bg-gray-100 hover:text-gray-700">Último</a>
                                    </li>
                                <?php endif; ?>
                            </ul>
                        </nav>
                    <?php endif; ?>
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
                <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

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
                                            document.title = '+' + unreadCount + ' HDIA | Buscador';
                                        } else {
                                            document.title = 'HDIA | Buscador';
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
                <!-- Modal para cambiar el estado -->
                <div id="status-modal" class="fixed inset-0 flex items-center justify-center z-50 hidden">
                    <!-- Fondo oscuro -->
                    <div class="absolute inset-0 bg-black opacity-50"></div>
                    <!-- Contenido del modal -->
                    <div class="bg-white rounded-lg shadow-lg z-50 p-6">
                        <h2 class="text-xl font-semibold mb-4">Cambiar Estado del Formulario</h2>
                        <form id="status-form">
                            <input type="hidden" name="form_id" id="modal-form-id">
                            <div class="mb-4">
                                <label for="modal-status" class="block text-gray-700">Estado:</label>
                                <select name="status" id="modal-status" class="w-full px-3 py-2 border rounded">
                                    <option value="en revision">En revisión</option>
                                    <option value="correcto">Correcto</option>
                                    <option value="rechazado">Rechazado</option>
                                </select>
                            </div>
                            <div class="flex justify-end">
                                <button type="button" id="modal-cancel" class="mr-2 px-4 py-2 bg-gray-500 text-white rounded">Cancelar</button>
                                <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded">Guardar</button>
                            </div>
                        </form>
                    </div>
                </div>
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        // Obtener elementos del modal
                        var modal = document.getElementById('status-modal');
                        var modalFormId = document.getElementById('modal-form-id');
                        var modalStatus = document.getElementById('modal-status');
                        var modalCancel = document.getElementById('modal-cancel');
                        var statusForm = document.getElementById('status-form');

                        // Manejar clic en los botones de estado
                        var statusButtons = document.querySelectorAll('.status-button');
                        statusButtons.forEach(function(button) {
                            button.addEventListener('click', function() {
                                var formId = this.getAttribute('data-form-id');
                                var currentStatus = this.getAttribute('data-current-status');

                                // Establecer el form_id en el modal
                                modalFormId.value = formId;

                                // Establecer el estado actual en el select
                                modalStatus.value = currentStatus;

                                // Mostrar el modal
                                modal.classList.remove('hidden');
                            });
                        });

                        // Manejar el botón de cancelar
                        modalCancel.addEventListener('click', function() {
                            // Ocultar el modal
                            modal.classList.add('hidden');
                        });

                        // Manejar el envío del formulario
                        // Manejar el envío del formulario
                        statusForm.addEventListener('submit', function(e) {
                            e.preventDefault();

                            var formId = modalFormId.value;
                            var newStatus = modalStatus.value;

                            // Enviar solicitud AJAX para actualizar el estado
                            var xhr = new XMLHttpRequest();
                            xhr.open('POST', 'update_status.php', true);
                            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

                            xhr.onreadystatechange = function() {
                                if (xhr.readyState === XMLHttpRequest.DONE) {
                                    if (xhr.status === 200) {
                                        // Éxito, ocultar el modal
                                        modal.classList.add('hidden');

                                        // Actualizar el estado en la tabla
                                        var row = document.querySelector('tr[data-form-id="' + formId + '"]');
                                        if (row) {
                                            var statusCell = row.querySelector('.status-cell');
                                            if (statusCell) {
                                                // Actualizar el texto del estado
                                                statusCell.querySelector('span span:nth-child(2)').textContent = newStatus.toUpperCase();

                                                // Actualizar el color del círculo
                                                var circleSpan = statusCell.querySelector('span span:first-child');
                                                if (circleSpan) {
                                                    // Remover las clases de color previas
                                                    circleSpan.classList.remove('bg-green-500', 'bg-red-500', 'bg-blue-500');

                                                    // Añadir la clase de color correspondiente
                                                    var status = newStatus.toLowerCase();
                                                    if (status == 'correcto') {
                                                        circleSpan.classList.add('bg-green-500');
                                                    } else if (status == 'rechazado') {
                                                        circleSpan.classList.add('bg-red-500');
                                                    } else if (status == 'en revision') {
                                                        circleSpan.classList.add('bg-blue-500');
                                                    }
                                                }
                                            }
                                        }

                                        // Actualizar el atributo data-current-status del botón
                                        var button = document.querySelector('.status-button[data-form-id="' + formId + '"]');
                                        if (button) {
                                            button.setAttribute('data-current-status', newStatus);
                                        }

                                        // Mostrar mensaje de éxito (opcional)
                                        alert('Estado actualizado correctamente.');
                                    } else {
                                        // Error
                                        alert('Error al actualizar el estado.');
                                    }
                                }
                            };

                            xhr.send('form_id=' + encodeURIComponent(formId) + '&status=' + encodeURIComponent(newStatus));
                        });
                    });
                </script>
                <!-- Incluir SweetAlert2 -->
                <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        // ... (código existente para el cambio de estado)

                        // Manejar clic en los botones de eliminación
                        var deleteButtons = document.querySelectorAll('.delete-button');
                        deleteButtons.forEach(function(button) {
                            button.addEventListener('click', function() {
                                var formId = this.getAttribute('data-form-id');
                                var fileName = this.getAttribute('data-file-name');
                                var row = this.closest('tr');

                                // Mostrar advertencia con SweetAlert
                                Swal.fire({
                                    title: '¿Estás seguro?',
                                    text: "Esta acción no se puede deshacer.",
                                    icon: 'warning',
                                    showCancelButton: true,
                                    confirmButtonColor: '#d33',
                                    cancelButtonColor: '#3085d6',
                                    confirmButtonText: 'Sí, eliminar',
                                    cancelButtonText: 'Cancelar',
                                    backdrop: false
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        // Enviar solicitud AJAX para eliminar el formulario
                                        var xhr = new XMLHttpRequest();
                                        xhr.open('POST', 'delete_form.php', true);
                                        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

                                        xhr.onreadystatechange = function() {
                                            if (xhr.readyState === XMLHttpRequest.DONE) {
                                                if (xhr.status === 200) {
                                                    // Eliminar la fila de la tabla
                                                    row.parentNode.removeChild(row);

                                                    // Mostrar mensaje de éxito
                                                    Swal.fire(
                                                        'Eliminado',
                                                        'El formulario ha sido eliminado.',
                                                        'success'
                                                    );
                                                } else {
                                                    // Mostrar mensaje de error
                                                    Swal.fire(
                                                        'Error',
                                                        'No se pudo eliminar el formulario.',
                                                        'error'
                                                    );
                                                }
                                            }
                                        };

                                        xhr.send('form_id=' + encodeURIComponent(formId) + '&file_name=' + encodeURIComponent(fileName));
                                    }
                                });
                            });
                        });
                    });
                </script>
                <!-- Modal para las medicaciones -->
                <div id="medication-modal" class="fixed inset-0 flex items-center justify-center z-50 hidden">
                    <!-- Fondo oscuro -->
                    <div class="absolute inset-0 bg-black opacity-50"></div>
                    <!-- Contenido del modal -->
                    <div class="bg-white rounded-lg shadow-lg z-50 p-6 w-full max-w-md">
                        <!-- Actualizamos el título del modal -->
                        <h2 class="text-base font-regular mb-4">Medicaciones del Paciente</h2>
                        <form id="medication-form">
                            <input type="hidden" name="form_id" id="medication-form-id">
                            <div id="medication-list" class="mb-4">
                                <!-- Aquí se cargarán las medicaciones mediante AJAX -->
                            </div>
                            <div class="flex justify-between">
                                <button type="button" id="download-pdf" class="px-4 py-2 bg-blue-500 text-white rounded">Descargar PDF</button>
                                <button type="button" id="medication-cancel" class="px-4 py-2 bg-gray-500 text-white rounded">Cerrar</button>
                            </div>
                        </form>
                    </div>
                </div>

                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        // Obtener elementos del modal de medicaciones
                        var medicationModal = document.getElementById('medication-modal');
                        var medicationFormId = document.getElementById('medication-form-id');
                        var medicationList = document.getElementById('medication-list');
                        var medicationCancel = document.getElementById('medication-cancel');
                        var modalTitle = medicationModal.querySelector('h2');
                        var downloadPdfButton = document.getElementById('download-pdf');

                        // Manejar clic en los botones de medicación
                        var medicationButtons = document.querySelectorAll('.medication-button');
                        medicationButtons.forEach(function(button) {
                            button.addEventListener('click', function() {
                                var formId = this.getAttribute('data-form-id');

                                // Establecer el form_id en el modal
                                medicationFormId.value = formId;

                                // Limpiar la lista de medicaciones
                                medicationList.innerHTML = '';

                                // Mostrar el modal
                                medicationModal.classList.remove('hidden');

                                // Obtener las medicaciones mediante AJAX
                                var xhr = new XMLHttpRequest();
                                xhr.open('GET', 'get_medications.php?form_id=' + encodeURIComponent(formId), true);

                                xhr.onreadystatechange = function() {
                                    if (xhr.readyState === XMLHttpRequest.DONE) {
                                        if (xhr.status === 200) {
                                            var response = JSON.parse(xhr.responseText);
                                            var nombre_pte = response.nombre_pte;
                                            var dni_pte = response.dni_pte;
                                            var medications = response.medications;

                                            // Actualizar el título del modal con el nombre del paciente
                                            modalTitle.textContent = 'Medicaciones del Paciente: ' + nombre_pte + ' - DNI: ' + dni_pte;

                                            // Generar el HTML de la tabla de medicaciones
                                            var table = document.createElement('table');
                                            table.className = 'min-w-full bg-white';

                                            var thead = document.createElement('thead');
                                            var trHead = document.createElement('tr');

                                            var thMedication = document.createElement('th');
                                            thMedication.className = 'py-2 px-4 bg-gray-200 text-left';
                                            thMedication.textContent = 'Medicamento';

                                            var thBrought = document.createElement('th');
                                            thBrought.className = 'py-2 px-4 bg-gray-200 text-center';
                                            thBrought.textContent = 'Traído por el paciente';

                                            trHead.appendChild(thMedication);
                                            trHead.appendChild(thBrought);
                                            thead.appendChild(trHead);
                                            table.appendChild(thead);

                                            var tbody = document.createElement('tbody');

                                            medications.forEach(function(medication) {
                                                var tr = document.createElement('tr');

                                                var tdMedication = document.createElement('td');
                                                tdMedication.className = 'border px-4 py-2';
                                                tdMedication.textContent = medication.medication_name;

                                                var tdBrought = document.createElement('td');
                                                tdBrought.className = 'border px-4 py-2 text-center';

                                                var checkbox = document.createElement('input');
                                                checkbox.type = 'checkbox';
                                                checkbox.className = 'form-checkbox';
                                                checkbox.checked = medication.brought_medicine == 1;
                                                checkbox.dataset.medicationId = medication.id_medication;

                                                // Añadir evento al checkbox
                                                checkbox.addEventListener('change', function() {
                                                    var brought = this.checked ? 1 : 0;
                                                    var medicationId = this.dataset.medicationId;

                                                    // Enviar solicitud AJAX para actualizar brought_medicine
                                                    var xhrUpdate = new XMLHttpRequest();
                                                    xhrUpdate.open('POST', 'update_medication.php', true);
                                                    xhrUpdate.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

                                                    xhrUpdate.onreadystatechange = function() {
                                                        if (xhrUpdate.readyState === XMLHttpRequest.DONE) {
                                                            if (xhrUpdate.status !== 200) {
                                                                // Mostrar mensaje de error
                                                                alert('Error al actualizar la medicación.');
                                                            }
                                                        }
                                                    };

                                                    xhrUpdate.send('id_medication=' + encodeURIComponent(medicationId) + '&brought_medicine=' + encodeURIComponent(brought));
                                                });

                                                tdBrought.appendChild(checkbox);
                                                tr.appendChild(tdMedication);
                                                tr.appendChild(tdBrought);
                                                tbody.appendChild(tr);
                                            });

                                            table.appendChild(tbody);
                                            medicationList.appendChild(table);
                                        } else {
                                            // Mostrar mensaje de error
                                            alert('Error al obtener las medicaciones.');
                                        }
                                    }
                                };

                                xhr.send();
                            });
                        });

                        // Manejar el botón de descarga
                        downloadPdfButton.addEventListener('click', function() {
                            var formId = medicationFormId.value;
                            // Redirigir a download_medications.php
                            window.open('download_medications.php?form_id=' + encodeURIComponent(formId), '_blank');
                        });

                        // Manejar el botón de cerrar
                        medicationCancel.addEventListener('click', function() {
                            // Ocultar el modal
                            medicationModal.classList.add('hidden');
                        });
                    });
                </script>
                <!-- Modal para los archivos adjuntos -->
                <div id="attachments-modal" class="fixed inset-0 flex items-center justify-center z-50 hidden">
                    <!-- Fondo oscuro -->
                    <div class="absolute inset-0 bg-black opacity-50"></div>
                    <!-- Contenido del modal -->
                    <div class="bg-white rounded-lg shadow-lg z-50 p-6 w-full max-w-lg h-1/2 overflow-hidden">
                        <h2 class="text-xl font-semibold mb-4">Archivos Adjuntos</h2>

                        <!-- Formulario para subir nuevos archivos -->
                        <form id="upload-form" class="mb-4" enctype="multipart/form-data">
                            <input type="hidden" name="form_id" id="upload-form-id">

                            <div class="flex items-center">

                                <input type="file" name="attachment_files[]" id="attachment_files" accept="application/pdf" multiple class="w-full px-3 py-2 border rounded">
                                <?php if ($user_role === 'admin'): ?>
                                    <button type="submit" class="ml-2 px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">Subir</button>
                                <?php endif; ?>
                            </div>

                        </form>

                        <!-- Lista de archivos adjuntos con scroll -->
                        <div id="attachments-list" class="mb-4 overflow-y-auto max-h-60">
                            <!-- Aquí se cargarán los archivos adjuntos mediante AJAX -->
                        </div>

                        <div class="flex justify-end">
                            <button type="button" id="attachments-close" class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600">Cerrar</button>
                        </div>
                    </div>
                </div>

                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        // ... código existente ...

                        // Variables del modal de archivos adjuntos
                        var attachmentsModal = document.getElementById('attachments-modal');
                        var attachmentsList = document.getElementById('attachments-list');
                        var attachmentsClose = document.getElementById('attachments-close');
                        var uploadForm = document.getElementById('upload-form');
                        var uploadFormId = document.getElementById('upload-form-id');

                        var attachmentFilesInput = document.getElementById('attachment_files');

                        // Manejar clic en los botones de archivos adjuntos
                        var attachmentsButtons = document.querySelectorAll('.attachments-button');
                        attachmentsButtons.forEach(function(button) {
                            button.addEventListener('click', function() {
                                var formId = this.getAttribute('data-form-id');

                                // Limpiar contenido previo
                                attachmentsList.innerHTML = '';
                                attachmentFilesInput.value = '';
                                uploadFormId.value = formId;

                                // Mostrar el modal
                                attachmentsModal.classList.remove('hidden');

                                // Cargar los archivos adjuntos
                                loadAttachments(formId);
                            });
                        });

                        // Función para cargar los archivos adjuntos
                        function loadAttachments(formId) {
                            // Obtener la lista de archivos adjuntos mediante AJAX
                            var xhr = new XMLHttpRequest();
                            xhr.open('GET', 'get_attachments.php?form_id=' + encodeURIComponent(formId), true);
                            xhr.onreadystatechange = function() {
                                if (xhr.readyState === XMLHttpRequest.DONE) {
                                    attachmentsList.innerHTML = ''; // Limpiar la lista
                                    if (xhr.status === 200) {
                                        var response = JSON.parse(xhr.responseText);
                                        if (response.status === 'success') {
                                            var files = response.files;
                                            if (files.length > 0) {
                                                var ul = document.createElement('ul');
                                                files.forEach(function(file) {
                                                    var li = document.createElement('li');
                                                    li.className = 'flex items-center justify-between py-2 border-b';

                                                    var fileInfo = document.createElement('div');
                                                    fileInfo.className = 'flex items-center';

                                                    var fileIcon = document.createElement('i');

                                                    fileInfo.appendChild(fileIcon);

                                                    var fileLink = document.createElement('a');
                                                    fileLink.href = file.url;
                                                    fileLink.textContent = file.name;
                                                    fileLink.target = '_blank';
                                                    fileLink.className = 'text-blue-500 hover:underline';
                                                    fileInfo.appendChild(fileLink);

                                                    li.appendChild(fileInfo);

                                                    // Botón de eliminar

                                                    var deleteButton = document.createElement('button');
                                                    <?php if ($user_role === 'admin'): ?>
                                                        deleteButton.className = 'text-gray-600 hover:text-gray-700 focus:outline-none';
                                                        deleteButton.innerHTML = '<i class="fas fa-trash-alt mr-6"></i>';
                                                    <?php endif; ?>
                                                    deleteButton.dataset.fileName = file.name;
                                                    deleteButton.dataset.formId = formId;

                                                    // Evento de clic para eliminar el archivo
                                                    deleteButton.addEventListener('click', function() {
                                                        var fileName = this.dataset.fileName;
                                                        var formId = this.dataset.formId;

                                                        if (confirm('¿Estás seguro de que deseas eliminar este archivo?')) {
                                                            // Enviar solicitud AJAX para eliminar el archivo
                                                            var xhrDelete = new XMLHttpRequest();
                                                            xhrDelete.open('POST', 'delete_attachment.php', true);
                                                            xhrDelete.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                                                            xhrDelete.onreadystatechange = function() {
                                                                if (xhrDelete.readyState === XMLHttpRequest.DONE) {
                                                                    if (xhrDelete.status === 200) {
                                                                        var deleteResponse = JSON.parse(xhrDelete.responseText);
                                                                        if (deleteResponse.status === 'success') {
                                                                            // Recargar la lista de archivos adjuntos
                                                                            loadAttachments(formId);
                                                                        } else {
                                                                            alert('Error al eliminar el archivo.');
                                                                        }
                                                                    } else {
                                                                        alert('Error al eliminar el archivo.');
                                                                    }
                                                                }
                                                            };
                                                            xhrDelete.send('form_id=' + encodeURIComponent(formId) + '&file_name=' + encodeURIComponent(fileName));
                                                        }
                                                    });

                                                    li.appendChild(deleteButton);

                                                    ul.appendChild(li);
                                                });
                                                attachmentsList.appendChild(ul);
                                            } else {
                                                attachmentsList.textContent = 'No hay archivos adjuntos.';
                                            }
                                        } else {
                                            attachmentsList.textContent = 'Error al obtener los archivos adjuntos.';
                                        }
                                    } else {
                                        attachmentsList.textContent = 'Error al obtener los archivos adjuntos.';
                                    }
                                }
                            };
                            xhr.send();
                        }

                        // Manejar el envío del formulario de carga
                        uploadForm.addEventListener('submit', function(e) {
                            e.preventDefault();

                            var formId = uploadFormId.value;
                            var formData = new FormData(uploadForm);

                            // Enviar los archivos mediante AJAX
                            var xhrUpload = new XMLHttpRequest();
                            xhrUpload.open('POST', 'upload_attachment.php', true);
                            xhrUpload.onreadystatechange = function() {
                                if (xhrUpload.readyState === XMLHttpRequest.DONE) {
                                    if (xhrUpload.status === 200) {
                                        var uploadResponse = JSON.parse(xhrUpload.responseText);
                                        if (uploadResponse.status === 'success') {
                                            // Recargar la lista de archivos adjuntos
                                            loadAttachments(formId);
                                            // Limpiar el campo de archivos
                                            attachmentFilesInput.value = '';
                                        } else {
                                            alert('Error al subir los archivos: ' + uploadResponse.message);
                                        }
                                    } else {
                                        alert('Error al subir los archivos.');
                                    }
                                }
                            };
                            xhrUpload.send(formData);
                        });

                        // Manejar el botón de cerrar
                        attachmentsClose.addEventListener('click', function() {
                            // Ocultar el modal
                            attachmentsModal.classList.add('hidden');
                        });

                        // ... código existente ...
                    });
                </script>


                <!-- Modal for editing dates -->
                <div id="edit-modal" class="fixed inset-0 flex items-center justify-center z-50 hidden">
                    <!-- Dark overlay -->
                    <div class="absolute inset-0 bg-black opacity-50"></div>
                    <!-- Modal content -->
                    <div class="bg-white rounded-lg shadow-lg z-50 p-6 w-full max-w-md">
                        <h2 class="text-xl font-semibold mb-4">Editar Fechas del Formulario</h2>
                        <form id="edit-form">
                            <input type="hidden" name="form_id" id="edit-form-id">
                            <div class="mb-4">
                                <label for="fecha_tto" class="block text-gray-700">Fecha Tratamiento:</label>
                                <input type="date" name="fecha_tto" id="fecha_tto" class="w-full px-3 py-2 border rounded">
                            </div>
                            <div class="mb-4">
                                <label for="fecha_1" class="block text-gray-700">Fecha 1:</label>
                                <input type="date" name="fecha_1" id="fecha_1" class="w-full px-3 py-2 border rounded">
                            </div>
                            <div class="mb-4">
                                <label for="fecha_2" class="block text-gray-700">Fecha 2:</label>
                                <input type="date" name="fecha_2" id="fecha_2" class="w-full px-3 py-2 border rounded">
                            </div>
                            <div class="mb-4">
                                <label for="fecha_3" class="block text-gray-700">Fecha 3:</label>
                                <input type="date" name="fecha_3" id="fecha_3" class="w-full px-3 py-2 border rounded">
                            </div>
                            <div class="mb-4">
                                <label for="fecha_4" class="block text-gray-700">Fecha 4:</label>
                                <input type="date" name="fecha_4" id="fecha_4" class="w-full px-3 py-2 border rounded">
                            </div>
                            <div class="flex justify-end">
                                <button type="button" id="edit-cancel" class="mr-2 px-4 py-2 bg-gray-500 text-white rounded">Cancelar</button>
                                <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded">Guardar</button>
                            </div>
                        </form>
                    </div>
                </div>
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        // Get elements of the edit modal
                        var editModal = document.getElementById('edit-modal');
                        var editFormId = document.getElementById('edit-form-id');
                        var fecha_tto = document.getElementById('fecha_tto');
                        var fecha_1 = document.getElementById('fecha_1');
                        var fecha_2 = document.getElementById('fecha_2');
                        var fecha_3 = document.getElementById('fecha_3');
                        var fecha_4 = document.getElementById('fecha_4');
                        var editCancel = document.getElementById('edit-cancel');
                        var editForm = document.getElementById('edit-form');

                        // Handle click on edit buttons
                        var editButtons = document.querySelectorAll('.edit-button');
                        editButtons.forEach(function(button) {
                            button.addEventListener('click', function() {
                                var formId = this.getAttribute('data-form-id');

                                // Set the form_id in the modal
                                editFormId.value = formId;

                                // Clear previous values
                                fecha_tto.value = '';
                                fecha_1.value = '';
                                fecha_2.value = '';
                                fecha_3.value = '';
                                fecha_4.value = '';

                                // Show the modal
                                editModal.classList.remove('hidden');

                                // Get the dates via AJAX
                                var xhr = new XMLHttpRequest();
                                xhr.open('GET', 'obtener_fechas.php?form_id=' + encodeURIComponent(formId), true);

                                xhr.onreadystatechange = function() {
                                    if (xhr.readyState === XMLHttpRequest.DONE) {
                                        if (xhr.status === 200) {
                                            var response = JSON.parse(xhr.responseText);
                                            if (response.status === 'success') {
                                                // Set the date values
                                                fecha_tto.value = response.fecha_tto || '';
                                                fecha_1.value = response.fecha_1 || '';
                                                fecha_2.value = response.fecha_2 || '';
                                                fecha_3.value = response.fecha_3 || '';
                                                fecha_4.value = response.fecha_4 || '';
                                            } else {
                                                alert('Error al obtener las fechas.');
                                            }
                                        } else {
                                            alert('Error al obtener las fechas.');
                                        }
                                    }
                                };

                                xhr.send();
                            });
                        });

                        // Handle cancel button
                        editCancel.addEventListener('click', function() {
                            // Hide the modal
                            editModal.classList.add('hidden');
                        });

                        // Handle form submission
                        editForm.addEventListener('submit', function(e) {
                            e.preventDefault();

                            var formId = editFormId.value;
                            var fechaTto = fecha_tto.value;
                            var fecha1 = fecha_1.value;
                            var fecha2 = fecha_2.value;
                            var fecha3 = fecha_3.value;
                            var fecha4 = fecha_4.value;

                            // Send AJAX request to update the dates
                            var xhr = new XMLHttpRequest();
                            xhr.open('POST', 'update_fechas.php', true);
                            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

                            xhr.onreadystatechange = function() {
                                if (xhr.readyState === XMLHttpRequest.DONE) {
                                    if (xhr.status === 200) {
                                        // Success, hide the modal
                                        editModal.classList.add('hidden');
                                        // Optionally, show a success message
                                        alert('Fechas actualizadas correctamente.');
                                    } else {
                                        alert('Error al actualizar las fechas.');
                                    }
                                }
                            };

                            var params = 'form_id=' + encodeURIComponent(formId) +
                                '&fecha_tto=' + encodeURIComponent(fechaTto) +
                                '&fecha_1=' + encodeURIComponent(fecha1) +
                                '&fecha_2=' + encodeURIComponent(fecha2) +
                                '&fecha_3=' + encodeURIComponent(fecha3) +
                                '&fecha_4=' + encodeURIComponent(fecha4);

                            xhr.send(params);
                        });
                    });
                </script>



    </body>

</html>