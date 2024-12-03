<?php
require_once '../../../vendor/autoload.php';
require_once '../../../config/bdc/conex.php';

session_start();
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_POST['form_token']) || $_POST['form_token'] !== $_SESSION['form_token']) {
        // Token inválido o falta de token
        die('Token de formulario inválido. Por favor, recargue la página e inténtelo de nuevo.');
    }
    // Una vez validado el token, eliminarlo para evitar reutilización
    unset($_SESSION['form_token']);
    // ... continuar con el procesamiento
}

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: ../../../index.php");
    exit;
}
$creator_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;
$username = isset($_SESSION['username']) ? $_SESSION['username'] : 'default';
$name = isset($_SESSION['name']) ? $_SESSION['name'] : 'Usuario';

use PhpOffice\PhpWord\TemplateProcessor;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $templateFile = 'formulario_sacarato.docx';
    $tempFile = tempnam(sys_get_temp_dir(), 'PHPWord') . '.docx';

    // Obtener los datos del formulario
    $nombre_pte = $_POST['nombre_pte'];
    $dx_pte = $_POST['dx_pte'];
    $domi_pte = $_POST['domi_pte'];
    $tel_pte = $_POST['tel_pte'];
    $hc_pte = $_POST['hc_pte'];
    $os_pte = $_POST['os_pte'];
    $nro_afi_pte = $_POST['nro_afi_pte'];
    $fecha_input = $_POST['fecha'];
    $dni = $_POST['dni'];
    $habitacion_pte = $_POST['habitacion_pte'];
    $fecha_inicio = $_POST['fecha_inicio'];
    $today = $_POST['today'];
    $peso_pte = $_POST['peso_pte'];
    $nac_pte = $_POST['nac_pte'];
    $ev_pte = $_POST['ev_pte'];
    $infu_1 = $_POST['infu_1'];
    $infu_2 = $_POST['infu_2'];
    $infu_3 = $_POST['infu_3'];
    $fecha_ini = $_POST['fecha_ini'];
    $fecha_sda_input = $_POST['fecha_sda'];
    $fecha_tto_input = $_POST['fecha_tto'];
    $cantidad_hierro = $_POST['cantidad_hierro'];

    // Función para formatear fechas de manera segura
    function format_date_input($date_input)
    {
        if (!empty($date_input)) {
            $date = DateTime::createFromFormat('Y-m-d', $date_input);
            if ($date) {
                return $date->format('d/m/Y');
            } else {
                // Manejar formato de fecha inválido si es necesario
                return '';
            }
        } else {
            return '';
        }
    }

    // Utilizar la función para cada campo de fecha
    $fecha_tto = format_date_input($fecha_tto_input);
    $fecha_sda = format_date_input($fecha_sda_input);
    $fecha_ini = format_date_input($fecha_ini);
    $fecha = format_date_input($fecha_input);
    $fecha_1 = format_date_input($_POST['fecha_1']);
    $fecha_2 = format_date_input($_POST['fecha_2']);
    $fecha_3 = format_date_input($_POST['fecha_3']);
    $fecha_4 = format_date_input($_POST['fecha_4']);

    // Obtener los valores de los radio buttons y fechas
    $tipo_tratamiento = isset($_POST['tipo_tratamiento']) ? $_POST['tipo_tratamiento'] : '';

    $signaturePath = '../../../config/src/signatures/' . $username . '.png';

    // Obtener la fecha y hora actual en Buenos Aires
    date_default_timezone_set('America/Argentina/Buenos_Aires');
    $creation_date = date('Y-m-d H:i:s');

    try {
        // Iniciar una transacción
        $conn->beginTransaction();

        // Preparar la sentencia de inserción en 'forms', incluyendo el campo 'fecha_tto' y las fechas adicionales
        $stmt = $conn->prepare("INSERT INTO forms (creator_id, form_type, nombre_pte, hc_pte, dni_pte, creation_date, fecha_tto, fecha_1, fecha_2, fecha_3, fecha_4) VALUES (:creator_id, :form_type, :nombre_pte, :hc_pte, :dni_pte, :creation_date, :fecha_tto, :fecha_1, :fecha_2, :fecha_3, :fecha_4)");

        $stmt->bindParam(':creator_id', $creator_id, PDO::PARAM_INT);
        $stmt->bindValue(':form_type', 'sacarato', PDO::PARAM_STR); // Valor de form_type
        $stmt->bindParam(':nombre_pte', $nombre_pte, PDO::PARAM_STR);
        $stmt->bindParam(':hc_pte', $hc_pte, PDO::PARAM_STR);
        $stmt->bindParam(':dni_pte', $dni, PDO::PARAM_STR);
        $stmt->bindParam(':creation_date', $creation_date, PDO::PARAM_STR);

        // Asignar NULL a 'fecha_tto' si está vacía
        $stmt->bindValue(':fecha_tto', !empty($fecha_tto) ? $fecha_tto : null, PDO::PARAM_STR);

        // Asignar NULL a las fechas si están vacías
        $stmt->bindValue(':fecha_1', !empty($fecha_1) ? $fecha_1 : null, PDO::PARAM_STR);
        $stmt->bindValue(':fecha_2', !empty($fecha_2) ? $fecha_2 : null, PDO::PARAM_STR);
        $stmt->bindValue(':fecha_3', !empty($fecha_3) ? $fecha_3 : null, PDO::PARAM_STR);
        $stmt->bindValue(':fecha_4', !empty($fecha_4) ? $fecha_4 : null, PDO::PARAM_STR);

        $stmt->execute();

        // Obtener el 'form_id' recién insertado
        $form_id = $conn->lastInsertId();

        // **Primera instancia de procesamiento de plantilla (para el DOCX editable)**
        // Cargar y modificar la plantilla DOCX
        $templateProcessorEditable = new TemplateProcessor($templateFile);

        // Establecer valores comunes (sin reemplazar las fechas que queremos mantener como marcadores)
        $templateProcessorEditable->setValue('nombre_pte', htmlspecialchars($nombre_pte));
        $templateProcessorEditable->setValue('fecha', htmlspecialchars($fecha));
        $templateProcessorEditable->setValue('dni', htmlspecialchars($dni));
        $templateProcessorEditable->setValue('hc_pte', htmlspecialchars($hc_pte));
        $templateProcessorEditable->setValue('tel_pte', htmlspecialchars($tel_pte));
        $templateProcessorEditable->setValue('os_pte', htmlspecialchars($os_pte));
        $templateProcessorEditable->setValue('nro_afi_pte', htmlspecialchars($nro_afi_pte));
        $templateProcessorEditable->setValue('dx_pte', htmlspecialchars($dx_pte));
        $templateProcessorEditable->setValue('domi_pte', htmlspecialchars($domi_pte));
        $templateProcessorEditable->setValue('habitacion_pte', htmlspecialchars($habitacion_pte));
        $templateProcessorEditable->setValue('fecha_inicio', htmlspecialchars($fecha_inicio));
        $templateProcessorEditable->setValue('today', htmlspecialchars($today));
        $templateProcessorEditable->setValue('peso_pte', htmlspecialchars($peso_pte));
        $templateProcessorEditable->setValue('nac_pte', htmlspecialchars($nac_pte));
        $templateProcessorEditable->setValue('ev_pte', htmlspecialchars($ev_pte));
        $templateProcessorEditable->setValue('fecha_ini', htmlspecialchars($fecha_ini));
        $templateProcessorEditable->setValue('infu_1', htmlspecialchars($infu_1));
        $templateProcessorEditable->setValue('infu_2', htmlspecialchars($infu_2));
        $templateProcessorEditable->setValue('infu_3', htmlspecialchars($infu_3));
        $templateProcessorEditable->setValue('cantidad_hierro', htmlspecialchars($cantidad_hierro));

        // Lógica para los radio buttons y fechas (sin reemplazar las fechas)
        if ($tipo_tratamiento === 'ambulatorio') {
            $templateProcessorEditable->setValue('ahd', 'X');
            // No reemplazamos 'f_trat' (fecha de tratamiento)
            $templateProcessorEditable->setValue('icm', '');
            $templateProcessorEditable->setValue('f_sug', '');
        } elseif ($tipo_tratamiento === 'cuidados_minimos') {
            $templateProcessorEditable->setValue('icm', 'X');
            // No reemplazamos 'f_sug' (fecha sugerida)
            $templateProcessorEditable->setValue('ahd', '');
            $templateProcessorEditable->setValue('f_trat', '');
        } else {
            $templateProcessorEditable->setValue('ahd', '');
            $templateProcessorEditable->setValue('f_trat', '');
            $templateProcessorEditable->setValue('icm', '');
            $templateProcessorEditable->setValue('f_sug', '');
        }

        // Ajustar la imagen de la firma con tamaño específico
        $templateProcessorEditable->setImageValue('med_signature', array('path' => $signaturePath, 'width' => 193, 'height' => 172, 'ratio' => false));

        // Guardar el DOCX editable
        $form_id_temp_file_name = $form_id . '_sacarato.docx';
        $docx_file_path = '../forms_conteiner/editable_forms/' . $form_id_temp_file_name;
        // Asegúrate de que el directorio existe o créalo si es necesario
        if (!is_dir('../forms_conteiner/editable_forms/')) {
            mkdir('../forms_conteiner/editable_forms/', 0777, true);
        }
        $templateProcessorEditable->saveAs($docx_file_path);

        // **Segunda instancia de procesamiento de plantilla (para el PDF)**
        // Cargar y modificar la plantilla DOCX nuevamente
        $templateProcessorPDF = new TemplateProcessor($templateFile);

        // Reemplazar todas las variables, incluyendo las fechas
        $templateProcessorPDF->setValue('fecha_tto', htmlspecialchars($fecha_tto));
        $templateProcessorPDF->setValue('fecha_1', htmlspecialchars($fecha_1));
        $templateProcessorPDF->setValue('fecha_2', htmlspecialchars($fecha_2));
        $templateProcessorPDF->setValue('fecha_3', htmlspecialchars($fecha_3));
        $templateProcessorPDF->setValue('fecha_4', htmlspecialchars($fecha_4));
        $templateProcessorPDF->setValue('fecha_sda', htmlspecialchars($fecha_sda));
        $templateProcessorPDF->setValue('fecha', htmlspecialchars($fecha));
        $templateProcessorPDF->setValue('nombre_pte', htmlspecialchars($nombre_pte));
        $templateProcessorPDF->setValue('dni', htmlspecialchars($dni));
        $templateProcessorPDF->setValue('hc_pte', htmlspecialchars($hc_pte));
        $templateProcessorPDF->setValue('tel_pte', htmlspecialchars($tel_pte));
        $templateProcessorPDF->setValue('os_pte', htmlspecialchars($os_pte));
        $templateProcessorPDF->setValue('nro_afi_pte', htmlspecialchars($nro_afi_pte));
        $templateProcessorPDF->setValue('dx_pte', htmlspecialchars($dx_pte));
        $templateProcessorPDF->setValue('domi_pte', htmlspecialchars($domi_pte));
        $templateProcessorPDF->setValue('habitacion_pte', htmlspecialchars($habitacion_pte));
        $templateProcessorPDF->setValue('fecha_inicio', htmlspecialchars($fecha_inicio));
        $templateProcessorPDF->setValue('today', htmlspecialchars($today));
        $templateProcessorPDF->setValue('peso_pte', htmlspecialchars($peso_pte));
        $templateProcessorPDF->setValue('nac_pte', htmlspecialchars($nac_pte));
        $templateProcessorPDF->setValue('ev_pte', htmlspecialchars($ev_pte));
        $templateProcessorPDF->setValue('fecha_ini', htmlspecialchars($fecha_ini));
        $templateProcessorPDF->setValue('infu_1', htmlspecialchars($infu_1));
        $templateProcessorPDF->setValue('infu_2', htmlspecialchars($infu_2));
        $templateProcessorPDF->setValue('infu_3', htmlspecialchars($infu_3));
        $templateProcessorPDF->setValue('cantidad_hierro', htmlspecialchars($cantidad_hierro));

        // Lógica para los radio buttons y fechas
        if ($tipo_tratamiento === 'ambulatorio') {
            $templateProcessorPDF->setValue('ahd', 'X');
            $templateProcessorPDF->setValue('f_trat', htmlspecialchars($fecha_tto));
            $templateProcessorPDF->setValue('icm', '');
            $templateProcessorPDF->setValue('f_sug', '');
        } elseif ($tipo_tratamiento === 'cuidados_minimos') {
            $templateProcessorPDF->setValue('icm', 'X');
            $templateProcessorPDF->setValue('f_sug', htmlspecialchars($fecha_sda));
            $templateProcessorPDF->setValue('ahd', '');
            $templateProcessorPDF->setValue('f_trat', '');
        } else {
            $templateProcessorPDF->setValue('ahd', '');
            $templateProcessorPDF->setValue('f_trat', '');
            $templateProcessorPDF->setValue('icm', '');
            $templateProcessorPDF->setValue('f_sug', '');
        }

        // Ajustar la imagen de la firma con tamaño específico
        $templateProcessorPDF->setImageValue('med_signature', array('path' => $signaturePath, 'width' => 193, 'height' => 172, 'ratio' => false));
        $templateProcessorPDF->saveAs($tempFile);

        // Preparar el cURL para enviar el archivo a Gotenberg
        $ch = curl_init('http://localhost:3000/forms/libreoffice/convert');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, [
            'files' => new CURLFile($tempFile, 'application/vnd.openxmlformats-officedocument.wordprocessingml.document')
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FAILONERROR, false);

        // Ejecutar la solicitud y recoger la respuesta, que será el PDF
        $pdfContent = curl_exec($ch);
        $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if (curl_errno($ch)) {
            echo 'Error en la conversión: ' . curl_error($ch);
            exit;
        } elseif ($http_status != 200) {
            echo 'Error en la conversión. Código HTTP: ' . $http_status;
            echo 'Respuesta de Gotenberg: ' . $pdfContent;
            exit;
        } elseif ($pdfContent === false) {
            echo 'Error: No se pudo obtener el contenido del PDF.';
            exit;
        }

        // Cerrar cURL y limpiar
        curl_close($ch);
        unlink($tempFile);

        // Definir el nombre del archivo usando 'form_id'
        $file_name = $form_id . '_sacarato.pdf';
        $file_path = '../forms_conteiner/' . $file_name;

        // Guardar el PDF en el directorio especificado
        file_put_contents($file_path, $pdfContent);

        // Actualizar el registro con el 'file_name'
        $stmt = $conn->prepare("UPDATE forms SET file_name = :file_name WHERE form_id = :form_id");
        $stmt->bindParam(':file_name', $file_name, PDO::PARAM_STR);
        $stmt->bindParam(':form_id', $form_id, PDO::PARAM_INT);
        $stmt->execute();

        // Confirmar la transacción
        $conn->commit();

        // Redirigir a 'index.php' con un parámetro que indique éxito
        header("Location: index.php?success=1");
        exit;
    } catch (PDOException $e) {
        // En caso de error, deshacer la transacción y mostrar el error
        $conn->rollBack();
        echo "Error en la base de datos: " . $e->getMessage();
        exit;
    }
}
?>
