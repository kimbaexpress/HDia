<?php
include '../../config/bdc/conex.php'; // Conexión a la base de datos

session_start();

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: ../../index.php");
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

// Condición adicional según el rol del usuario
if ($user_role === 'medico') {
    $where_clauses[] = "f.creator_id = :creator_id";
    $params[':creator_id'] = $creator_id;
} elseif ($user_role === 'supervisor') {
    $where_clauses[] = "LOWER(f.status) = :status_correcto";
    $params[':status_correcto'] = 'correcto';
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


// Combinar las cláusulas WHERE
$where_sql = '';
if (!empty($where_clauses)) {
    $where_sql = 'WHERE ' . implode(' AND ', $where_clauses);
}

// Contar el total de registros
$total_records_sql = "SELECT COUNT(*) FROM forms f $where_sql";
$stmt = $conn->prepare($total_records_sql);
foreach ($params as $key => &$value) {
    // Asumiendo que todos los parámetros son strings excepto creator_id
    if ($key === ':creator_id') {
        $stmt->bindParam($key, $value, PDO::PARAM_INT);
    } else {
        $stmt->bindParam($key, $value, PDO::PARAM_STR);
    }
}
$stmt->execute();
$total_records = $stmt->fetchColumn();

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
                            <label for="search_nombre_pte" class="block text-gray-700">Historia Clínica:</label>
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
                        <!-- Agrega este bloque en el formulario de búsqueda -->
                        <div class="w-1/2 md:w-1/5 px-2 mb-4">
                            <label for="search_new_date" class="block text-gray-700">Fecha TTO - 1 - 2 - 3 - 4:</label>
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

                                <th class="py-3 px-6 text-left">Tipo de Formulario</th>
                                <th class="py-3 px-6 text-left">PACIENTE</th>
                                <th class="py-3 px-6 text-left">DNI Paciente</th>
                                <th class="py-3 px-6 text-left">Fecha de Creación</th>
                                <th class="py-3 px-6 text-left">Estado</th>
                                <th class="py-3 px-6 text-center">Acciones</th>
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
                                <tr class="border-b border-gray-200 hover:bg-gray-100">
                                    <td class="py-3 px-6 text-left"><?php echo htmlspecialchars($form['form_id']); ?></td>

                                    <td class="py-3 px-6 text-left capitalize"><?php echo htmlspecialchars($form['form_type']); ?></td>
                                    <td class="py-3 px-6 text-left"><?php echo htmlspecialchars($form['nombre_pte']); ?></td>
                                    <td class="py-3 px-6 text-left"><?php echo htmlspecialchars($form['dni_pte']); ?></td>
                                    <td class="py-3 px-6 text-left">
                                        <?php
                                        // Formatear la fecha a d/m/Y H:i
                                        echo date('d/m/Y H:i', strtotime($form['creation_date']));
                                        ?>
                                    </td>
                                    <td class="py-3 px-6 text-left status-cell uppercase">
                                        <span class="flex items-center">
                                            <span class="inline-block w-2 h-2 mr-2 rounded-full <?php echo $status_color_bg; ?>"></span>
                                            <span><?php echo strtoupper($status); ?></span>
                                        </span>
                                    </td>
                                    <td class="py-3 px-6 text-center">
                                        <a href="../formularios/forms_conteiner/<?php echo urlencode($form['file_name']); ?>" target="_blank" class="text-gray-700 hover:text-gray-800">
                                            <i class="text-gray-700 fa-regular fa-file-pdf group-hover:text-gray-800 "></i>
                                        </a>
                                        <button class="attachments-button text-gray-700 hover:text-gray-800" data-form-id="<?php echo htmlspecialchars($form['form_id']); ?>">
                                            <i class="fas fa-folder text-gray-700 group-hover:text-gray-800 pl-2"></i>
                                        </button>
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

                <script src="js/main.js"></script>
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
                                <button type="submit" class="ml-2 px-4 py-2 bg-gray-700 text-white rounded hover:bg-gray-800">Subir</button>
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
                <!-- Incluir SweetAlert2 para mejores alertas -->
                <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


    </body>

</html>