<?php
header("Cache-Control: no-cache, must-revalidate");
header("Expires: Sat, 1 Jul 2000 05:00:00 GMT");
include '../../../config/bdc/conex.php'; // Conexion base de datos

session_start();

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: ../../../index.php");
    exit;
}

// Generar un token único y almacenarlo en la sesión
if (!isset($_SESSION['form_token'])) {
    $_SESSION['form_token'] = bin2hex(random_bytes(32));
}
$form_token = $_SESSION['form_token'];



// Asegurarse de que el nombre está disponible en la sesión
$username = $_SESSION['username'];
$name = isset($_SESSION['name']) ? $_SESSION['name'] : 'Usuario'; // Fallback a 'Usuario' si no se encontró el nombre
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>hdia | Formulario</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link rel="shortcut icon" href="../../../config/favicon.ico" type="image/x-icon">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Righteous&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/boxicons@2.0.7/css/boxicons.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@100..500&display=swap" rel="stylesheet">
    <script src="https://kit.fontawesome.com/bfe519afef.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <link rel="stylesheet" href="css/style.css">
    <style>
        td.py-2:hover {
            cursor: pointer;
            background-color: #f0f0f0;
        }

        td.py-2.selected {
            background-color: #d0e0f0;
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
                            <a href="../../dashboard/" class="flex items-center justify-center md:justify-start space-x-2 p-2 pt-0 pb-0 text-gray-600 rounded transition-colors group">
                                <i class="fas fa-layer-group text-gray-600 group-hover:text-gray-600"></i>
                                <span class="text-gray-600 group-hover:text-gray-600 font-medium text-sm">Formularios</span>
                            </a>
                        </li>
                        <li>
                            <a href="../../buscador/" class="flex items-center justify-center md:justify-start space-x-2 p-2 text-gray-600 rounded transition-colors group">
                                <i class="fas fa-search text-gray-500 group-hover:text-gray-600"></i>
                                <span class="text-gray-500 group-hover:text-gray-600 text-sm">Buscador</span>
                            </a>
                        </li>
                        <li>
                            <a href="#" class="flex items-center justify-center md:justify-start space-x-2 p-2 pt-0 pb-0 text-gray-600 rounded transition-colors group">
                                <i class="fa-regular fa-comment text-gray-500 group-hover:text-gray-600"></i>
                                <span class="text-gray-500 group-hover:text-gray-600 font-medium text-sm" id="conversations-span">Conversaciones</span>
                            </a>
                            <!--Div dentro de LI para hacer el submenu del botón de conversaciones. -->
                            <div class="space-y-2 ml-4 mt-1 hidden" id="submenuConversaciones">
                                <!-- Nueva Conversación -->
                                <a href="../../chat" class="flex items-center space-x-2 p-2 text-gray-600 rounded transition-colors group" style="padding-bottom: 0px;">
                                    <i class="text-gray-500 fa-regular fa-paper-plane group-hover:text-gray-600 "></i>
                                    <span class="text-gray-500 group-hover:text-gray-600 text-sm">Crear nueva</span>
                                </a>
                                <!-- Visualizar Conversación -->
                                <a href="../../chat/conversations.php" class="flex items-center space-x-2 p-2 text-gray-600 rounded transition-colors group" style="padding-bottom: 0px;">
                                    <i class="text-gray-500 fa-regular fa-comments group-hover:text-gray-600"></i>
                                    <span class="text-gray-500 group-hover:text-gray-600 text-sm">Buscar</span>
                                </a>
                            </div>
                        </li>
                    </ul>
                </div>
                <!-- Botón Cerrar sesión -->
                <div class="mt-auto mb-4 flex justify-center md:justify-start">
                    <a href="../../../config/logout.php" class="flex items-center space-x-2 group">
                        <i class="fas fa-sign-out-alt text-gray-500 group-hover:text-gray-600"></i>
                        <span class="text-gray-500 group-hover:text-gray-600">Cerrar sesión</span>
                    </a>
                </div>
            </div>


        </div>

        <!-- INICIO DEL CODIGO DESPUES DE LA SIDEBAR -->
        <div id="main-content" class="flex-1 flex flex-col h-full overflow-hidden">
            <!-- Encabezado -->
            <div class="bg-white flex items-center p-5 border-b">
                <!-- Ícono de menú para móviles -->
                <button id="menu-toggle" class="text-gray-600 focus:outline-none mr-3 md:hidden">
                    <i class="fas fa-bars text-2xl"></i>
                </button>
                <h1 class="text-xl font-regular text-gray-700">Panel de Formularios</h1>
            </div>

            <div class="flex-1 p-10 overflow-auto">
                <div class="block p-6 rounded-lg shadow-lg bg-white max-h-full overflow-auto">
                    <form id="myForm" action="generate_doc.php" method="post" onsubmit="return checkSubmit();">

                        <input type="hidden" name="form_token" value="<?php echo $form_token; ?>">

                        <div>
                            <p>FORMULARIO PROTOCOLO SACARATO</p>
                            <hr class="border-t border-gray-300 mt-2 mb-4 w-64">
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div class="form-group">
                                <label for="nombre_pte" class="form-label inline-block mb-2 text-gray-700">Nombre del Paciente:</label>
                                <input type="text" class="form-control block w-full px-4 py-2 text-gray-700 bg-white border border-solid border-gray-300 rounded transition ease-in-out m-0 focus:text-gray-700 focus:bg-white focus:border-blue-600 focus:outline-none" id="nombre_pte" name="nombre_pte" placeholder="Nombre y Apellido" required>
                            </div>
                            <div class="form-group">
                                <label for="today" class="form-label inline-block mb-2 text-gray-700">Fecha:</label>
                                <input type="text" class="form-control block w-full px-4 py-2 text-gray-700 bg-white border border-solid border-gray-300 rounded transition ease-in-out m-0 focus:text-gray-700 focus:bg-white focus:border-blue-600 focus:outline-none" id="today" name="today" placeholder="dd/mm/aaaa" required>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="form-group mb-6">
                                <label for="dni" class="form-label inline-block mb-2 text-gray-700">DNI del paciente:</label>
                                <input type="text" class="form-control block w-full px-4 py-2 text-gray-700 bg-white border border-solid border-gray-300 rounded transition ease-in-out m-0 focus:text-gray-700 focus:bg-white focus:border-blue-600 focus:outline-none" id="dni" name="dni" placeholder="DNI" required>
                            </div>
                            <div class="form-group mb-6">
                                <label for="domicilio_pte" class="form-label inline-block mb-2 text-gray-700">Domicilio del paciente:</label>
                                <input type="text" class="form-control block w-full px-4 py-2 text-gray-700 bg-white border border-solid border-gray-300 rounded transition ease-in-out m-0 focus:text-gray-700 focus:bg-white focus:border-blue-600 focus:outline-none" id="domi_pte" name="domi_pte" placeholder="Domicilio" required>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-4">

                            <div class="form-group">
                                <label for="nac_pte" class="form-label inline-block mb-2 text-gray-700">Fecha de nacimiento del paciente:</label>
                                <input type="text" class="form-control block w-full px-4 py-2 text-gray-700 bg-white border border-solid border-gray-300 rounded transition ease-in-out m-0 focus:text-gray-700 focus:bg-white focus:border-blue-600 focus:outline-none" id="nac_pte" name="nac_pte" placeholder="dd/mm/aaaa" required>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div class="form-group">
                                <label for="hc_pte" class="form-label inline-block mb-2 text-gray-700">Historia Clínica del paciente:</label>
                                <input type="text" class="form-control block w-full px-4 py-2 text-gray-700 bg-white border border-solid border-gray-300 rounded transition ease-in-out m-0 focus:text-gray-700 focus:bg-white focus:border-blue-600 focus:outline-none" id="hc_pte" name="hc_pte" placeholder="Historia Clinica" required>
                            </div>
                            <div class="form-group">
                                <label for="telefono" class="form-label inline-block mb-2 text-gray-700">Teléfono del paciente:</label>
                                <input type="text" class="form-control block w-full px-4 py-2 text-gray-700 bg-white border border-solid border-gray-300 rounded transition ease-in-out m-0 focus:text-gray-700 focus:bg-white focus:border-blue-600 focus:outline-none" id="tel_pte" name="tel_pte" placeholder="Telefono" required>
                            </div>
                        </div>
                        <div class="form-group mb-6">
                            <label for="os_pte" class="form-label inline-block mb-2 text-gray-700">Obra Social:</label>
                            <input type="text" class="form-control block w-full px-4 py-2 text-gray-700 bg-white border border-solid border-gray-300 rounded transition ease-in-out m-0 focus:text-gray-700 focus:bg-white focus:border-blue-600 focus:outline-none" id="os_pte" name="os_pte" placeholder="Obra Social" required>
                        </div>
                        <div class="form-group mb-6">
                            <label for="nro_afi_pte" class="form-label inline-block mb-2 text-gray-700">N° Afiliado:</label>
                            <input type="text" class="form-control block w-full px-4 py-2 text-gray-700 bg-white border border-solid border-gray-300 rounded transition ease-in-out m-0 focus:text-gray-700 focus:bg-white focus:border-blue-600 focus:outline-none" id="nro_afi_pte" name="nro_afi_pte" placeholder="N° Afiliado" required>
                        </div>
                        <div class="form-group mb-6">
                            <label for="dx_pte" class="form-label inline-block mb-2 text-gray-700">Diagnostico:</label>
                            <input type="text" class="form-control block w-full px-4 py-2 text-gray-700 bg-white border border-solid border-gray-300 rounded transition ease-in-out m-0 focus:text-gray-700 focus:bg-white focus:border-blue-600 focus:outline-none" id="dx_pte" name="dx_pte" placeholder="DX Paciente" required>
                        </div>

                      <!-- CHECK BOXS  -->
                      <div class="mb-6">
                            <span class="text-gray-700">Tipo de Tratamiento:</span>
                            <div class="mt-2">
                                <label class="inline-flex items-center">
                                    <input type="radio" class="form-radio" name="tipo_tratamiento" id="amb_hdia" value="ambulatorio">
                                    <span class="ml-2">Ambulatorio Hospital de Día</span>
                                </label>
                                <label class="inline-flex items-center ml-6">
                                    <input type="radio" class="form-radio" name="tipo_tratamiento" id="cuidados_min" value="cuidados_minimos">
                                    <span class="ml-2">Internación Cuidados Mínimos</span>
                                </label>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div class="form-group">
                                <label for="fecha_tto" class="form-label inline-block mb-2 text-gray-700">Fecha de Tratamiento:</label>
                                <input type="date" class="form-control block w-full px-4 py-2 text-gray-700 bg-white border border-solid border-gray-300 rounded transition ease-in-out m-0 focus:text-gray-700 focus:bg-white focus:border-blue-600 focus:outline-none" id="fecha_tto" name="fecha_tto" placeholder="dd/mm/aaaa">

                                <label for="fecha_1" class="form-label inline-block mb-2 text-gray-700">Fecha 1:</label>
                                <input type="date" class="form-control block w-full px-4 py-2 text-gray-700 bg-white border border-solid border-gray-300 rounded transition ease-in-out m-0 focus:text-gray-700 focus:bg-white focus:border-blue-600 focus:outline-none" id="fecha_1" name="fecha_1" placeholder="dd/mm/aaaa">

                                <label for="fecha_2" class="form-label inline-block mb-2 text-gray-700">Fecha 2:</label>
                                <input type="date" class="form-control block w-full px-4 py-2 text-gray-700 bg-white border border-solid border-gray-300 rounded transition ease-in-out m-0 focus:text-gray-700 focus:bg-white focus:border-blue-600 focus:outline-none" id="fecha_2" name="fecha_2" placeholder="dd/mm/aaaa">

                                <label for="fecha_3" class="form-label inline-block mb-2 text-gray-700">Fecha 3:</label>
                                <input type="date" class="form-control block w-full px-4 py-2 text-gray-700 bg-white border border-solid border-gray-300 rounded transition ease-in-out m-0 focus:text-gray-700 focus:bg-white focus:border-blue-600 focus:outline-none" id="fecha_3" name="fecha_3" placeholder="dd/mm/aaaa">

                                <label for="fecha_4" class="form-label inline-block mb-2 text-gray-700">Fecha 4:</label>
                                <input type="date" class="form-control block w-full px-4 py-2 text-gray-700 bg-white border border-solid border-gray-300 rounded transition ease-in-out m-0 focus:text-gray-700 focus:bg-white focus:border-blue-600 focus:outline-none" id="fecha_4" name="fecha_4" placeholder="dd/mm/aaaa">
                            </div>
                            <div class="form-group">
                                <label for="fecha_sda" class="form-label inline-block mb-2 text-gray-700">Fecha Sugerida:</label>
                                <input type="date" class="form-control block w-full px-4 py-2 text-gray-700 bg-white border border-solid border-gray-300 rounded transition ease-in-out m-0 focus:text-gray-700 focus:bg-white focus:border-blue-600 focus:outline-none" id="fecha_sda" name="fecha_sda" placeholder="dd/mm/aaaa">
                            </div>
                        </div>

                        <!-- CHECK BOXS  -->

                    







                        <div class="pt-5">
                            <p>PROTOCOLO SACARATO</p>
                            <hr class="border-t border-gray-300 mt-2 mb-4 w-64">
                        </div>
                        <div class="form-group">
                            <label for="fecha_inicio" class="form-label inline-block mb-2 text-gray-700">Fecha de Inicio:</label>
                            <input type="text" class="form-control block w-full px-4 py-2 text-gray-700 bg-white border border-solid border-gray-300 rounded transition ease-in-out m-0 focus:text-gray-700 focus:bg-white focus:border-blue-600 focus:outline-none" id="fecha_inicio" name="fecha_inicio" placeholder="dd/mm/aaaa">
                        </div>

                        <!-- HABITACIÓN Y SECTOR -->
                        <div class="grid grid-cols-2 gap-4">
                            <div class="form-group">
                                <label for="habitacion" class="form-label inline-block mb-2 text-gray-700">Cama:</label>
                                <input type="text" class="form-control block w-full px-4 py-2 text-gray-700 bg-white border border-solid border-gray-300 rounded transition ease-in-out m-0 focus:text-gray-700 focus:bg-white focus:border-blue-600 focus:outline-none" id="habitacion_pte" name="habitacion_pte" placeholder="Número de habitación" required>
                            </div>
                            <div class="form-group">
                                <label for="peso" class="form-label inline-block mb-2 text-gray-700">Peso:</label>
                                <input type="text" class="form-control block w-full px-4 py-2 text-gray-700 bg-white border border-solid border-gray-300 rounded transition ease-in-out m-0 focus:text-gray-700 focus:bg-white focus:border-blue-600 focus:outline-none" id="peso_pte" name="peso_pte" placeholder="Peso del paciente" required>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="form-group">
                                <label for="peso" class="form-label inline-block mb-2 text-gray-700">1ra Infusion:</label>
                                <input type="text" class="form-control block w-full px-4 py-2 text-gray-700 bg-white border border-solid border-gray-300 rounded transition ease-in-out m-0 focus:text-gray-700 focus:bg-white focus:border-blue-600 focus:outline-none" id="infu_1" name="infu_1" placeholder="1ra Infusion" required>
                            </div>
                            <div class="form-group">
                                <label for="peso" class="form-label inline-block mb-2 text-gray-700">2da Infusion:</label>
                                <input type="text" class="form-control block w-full px-4 py-2 text-gray-700 bg-white border border-solid border-gray-300 rounded transition ease-in-out m-0 focus:text-gray-700 focus:bg-white focus:border-blue-600 focus:outline-none" id="infu_2" name="infu_2" placeholder="2da Infusion" required>
                            </div>

                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="form-group">
                                <label for="peso" class="form-label inline-block mb-2 text-gray-700">3ra Infusion:</label>
                                <input type="text" class="form-control block w-full px-4 py-2 text-gray-700 bg-white border border-solid border-gray-300 rounded transition ease-in-out m-0 focus:text-gray-700 focus:bg-white focus:border-blue-600 focus:outline-none" id="infu_3" name="infu_3" placeholder="3ra Infusion" required>
                            </div>
                        </div>

                        <div class="pt-5">
                            <p>PREMEDICAR CON HIDROCORTISONA 100 MG + BENADRYL 2 ML ev
                                DILUIR:</p>
                            <div class="grid grid-cols-2 gap-4">
                                <input type="text" class="form-control block w-full px-4 py-2 text-gray-700 bg-white border border-solid border-gray-300 rounded transition ease-in-out m-0 focus:text-gray-700 focus:bg-white focus:border-blue-600 focus:outline-none" id="cantidad_hierro" name="cantidad_hierro" placeholder="Cantidad de Hierro a Diluir" required>
                            </div>
                            DE HIERRO SACARATO EN 200 ML DE SF A PASAR EN 60
                            MINUTOS.
                        </div>






                        <div class="pt-5">
                            <p>EVALUACIÓN MEDICA DEL HOSPITAL</p>
                            <hr class="border-t border-gray-300 mt-2 mb-4 w-64">
                        </div>

                        <div class="form-group">
                            <label for="ev_pte" class="form-label inline-block mb-2 text-gray-700">Evaluación Medica: </label>
                            <textarea class="form-control block w-full px-4 py-2 text-gray-700 bg-white border border-solid border-gray-300 rounded transition ease-in-out m-0 focus:text-gray-700 focus:bg-white focus:border-blue-600 focus:outline-none" id="ev_pte" name="ev_pte" placeholder="Evaluación medica" maxlength="500"></textarea>
                            <p id="charCount">0/500</p>

                            <script>
                                const textarea = document.getElementById('ev_pte');
                                const charCount = document.getElementById('charCount');

                                textarea.addEventListener('input', function() {
                                    const length = textarea.value.length;
                                    charCount.textContent = `${length}/500`;
                                });
                            </script>
                        </div>

                        <button type="submit" class="bg-gray-700 hover:bg-gray-700 text-white font-regular rounded p-2" onclick="this.disabled=true; this.form.submit();">Generar Archivo</button>

                </div>
                </form>
                <script>
                    // Verificar si hay un parámetro 'success' en la URL
                    const urlParams = new URLSearchParams(window.location.search);
                    if (urlParams.get('success') === '1') {
                        Swal.fire({
                            title: '¡Éxito!',
                            text: 'Su formulario se creó correctamente, ¿desea ir al buscador para visualizarlo?',
                            icon: 'success',
                            showCancelButton: true,
                            confirmButtonText: 'Sí',
                            cancelButtonText: 'No',
                            backdrop: false
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.href = '../../buscador/index.php';
                            } else {
                                // Recargar la página actual para reiniciar el formulario
                                window.location.href = window.location.href.split('?')[0];
                            }
                        });
                    }
                </script>

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
                    document.addEventListener('DOMContentLoaded', function() {
                        const ambHdia = document.getElementById('amb_hdia');
                        const cuidadosMin = document.getElementById('cuidados_min');
                        const fechaTratamiento = document.getElementById('fecha_tto');
                        const fecha_1 = document.getElementById('fecha_1');
                        const fecha_2 = document.getElementById('fecha_2');
                        const fecha_3 = document.getElementById('fecha_3');
                        const fecha_4 = document.getElementById('fecha_4');
                        const fechaSugerida = document.getElementById('fecha_sda');
                        const labelFechaTratamiento = document.querySelector('label[for="fecha_tto"]');
                        const labelF1 = document.querySelector('label[for="fecha_1"]');
                        const labelF2 = document.querySelector('label[for="fecha_2"]');
                        const labelF3 = document.querySelector('label[for="fecha_3"]');
                        const labelF4 = document.querySelector('label[for="fecha_4"]');
                        const labelFechaSugerida = document.querySelector('label[for="fecha_sda"]');

                        function updateVisibility() {
                            if (ambHdia.checked) {
                                // Mostrar Fecha de Tratamiento
                                fechaTratamiento.style.display = 'block';
                                labelFechaTratamiento.style.display = 'block';
                                fecha_1.style.display = 'block';
                                fecha_2.style.display = 'block';
                                fecha_3.style.display = 'block';
                                fecha_4.style.display = 'block';
                                labelF1.style.display = 'block';
                                labelF2.style.display = 'block';
                                labelF3.style.display = 'block';
                                labelF4.style.display = 'block';
                                // Ocultar Fecha Sugerida
                                fechaSugerida.style.display = 'none';
                                labelFechaSugerida.style.display = 'none';
                                fechaSugerida.value = ''; // Limpiar el valor cuando no es visible
                            } else if (cuidadosMin.checked) {
                                // Ocultar Fecha de Tratamiento
                                fechaTratamiento.style.display = 'none';
                                fecha_1.style.display = 'none';
                                fecha_2.style.display = 'none';
                                fecha_3.style.display = 'none';
                                fecha_4.style.display = 'none';
                                labelFechaTratamiento.style.display = 'none';
                                labelF1.style.display = 'none';
                                labelF2.style.display = 'none';
                                labelF3.style.display = 'none';
                                labelF4.style.display = 'none';
                                fechaTratamiento.value = ''; // Limpiar el valor cuando no es visible
                                fecha_1.value = ''; // Limpiar el valor cuando no es visible
                                fecha_2.value = ''; // Limpiar el valor cuando no es visible
                                fecha_3.value = ''; // Limpiar el valor cuando no es visible
                                fecha_4.value = ''; // Limpiar el valor cuando no es visible 
                                // Mostrar Fecha Sugerida
                                fechaSugerida.style.display = 'block';
                                labelFechaSugerida.style.display = 'block';
                            } else {
                                // Si ninguno está seleccionado, todo se oculta
                                fechaTratamiento.style.display = 'none';
                                fecha_1.style.display = 'none';
                                fecha_2.style.display = 'none';
                                fecha_3.style.display = 'none';
                                fecha_4.style.display = 'none';
                                labelFechaTratamiento.style.display = 'none';
                                labelF1.style.display = 'none';
                                labelF2.style.display = 'none';
                                labelF3.style.display = 'none';
                                labelF4.style.display = 'none';
                                fechaSugerida.style.display = 'none';
                                labelFechaSugerida.style.display = 'none';
                                fechaTratamiento.value = '';
                                fecha_1.value = ''; // Limpiar el valor cuando no es visible
                                fecha_2.value = ''; // Limpiar el valor cuando no es visible
                                fecha_3.value = ''; // Limpiar el valor cuando no es visible
                                fecha_4.value = ''; // Limpiar el valor cuando no es visible 
                                fechaSugerida.value = '';
                            }
                        }

                        // Añadir listeners a ambos radio buttons
                        ambHdia.addEventListener('change', updateVisibility);
                        cuidadosMin.addEventListener('change', updateVisibility);

                        // Establecer visibilidad inicial
                        updateVisibility();
                    });
                </script>


                <script>
                    function toggleCell(id) {
                        var cell = document.getElementById(id);
                        var input = document.getElementById('input_' + id);
                        if (cell.innerHTML === 'X') {
                            cell.innerHTML = ''; // Vacía la celda si tiene una 'X'
                            input.value = ''; // Limpia el valor del input oculto
                        } else {
                            cell.innerHTML = 'X'; // Coloca 'X' en la celda
                            input.value = 'X'; // Asigna 'X' como valor del input oculto
                        }
                    }
                </script>
                <script>
                    $(document).ready(function() {
                        updateUnreadCount();
                        setInterval(updateUnreadCount, 10000); // Actualizar cada 10 segundos

                        function updateUnreadCount() {
                            $.ajax({
                                url: '../../../config/php/get_unread_messages.php', // Ajusta la ruta si es necesario
                                method: 'GET',
                                dataType: 'json',
                                success: function(response) {
                                    if (response.status === 'success') {
                                        var unreadCount = parseInt(response.unread_count, 10);

                                        // Actualizar el título de la página
                                        if (unreadCount > 0) {
                                            document.title = '+' + unreadCount + ' HDIA | Formulario Sacarato';
                                        } else {
                                            document.title = 'HDIA | Formulario Sacarato';
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
                <script>
                    var formSubmitted = false;

                    function checkSubmit() {
                        if (!formSubmitted) {
                            formSubmitted = true;
                            return true;
                        } else {
                            return false;
                        }
                    }
                </script>

    </body>



</html>