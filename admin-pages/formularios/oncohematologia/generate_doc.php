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
$creator_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0; // Asegúrate de que 'user_id' esté establecido en la sesión
$username = isset($_SESSION['username']) ? $_SESSION['username'] : 'default';
$name = isset($_SESSION['name']) ? $_SESSION['name'] : 'Usuario'; // Fallback a 'Usuario' si no se encontró el nombre
// Formar la ruta de la imagen de la firma


use PhpOffice\PhpWord\TemplateProcessor;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $templateFile = 'formulario_oncohematologia.docx';
    $tempFile = tempnam(sys_get_temp_dir(), 'PHPWord') . '.docx';  // Asegúrate de añadir la extensión .docx

    // Obtener los datos del formulario
    $nombre_pte = $_POST['nombre_pte'];
    $inputDate = $_POST['fecha'];
    $dateObject = DateTime::createFromFormat('Y-m-d', $inputDate);
    $fecha = $_POST['fecha'];
    $dni = $_POST['dni'];
    $edad_pte = $_POST['edad_pte'];
    $hc_pte = $_POST['hc_pte'];
    $tel_pte = $_POST['tel_pte'];
    $os_pte = $_POST['os_pte'];
    $nro_afi_pte = $_POST['nro_afi_pte'];
    $dx_pte = $_POST['dx_pte'];
    $domi_pte = $_POST['domi_pte'];
    $inf_dosis = $_POST['inf_dosis'];
    $edad_pte = $_POST['edad_pte'];
    $alergias_pte = $_POST['alergias_pte'];
    $obs_pte = $_POST['obs_pte'];
    $habitacion_pte = $_POST['habitacion_pte'];
    $sector_pte = $_POST['sector_pte'];
    $diaadm_pte = $_POST['diaadm_pte'];
    $hpte = $_POST['hpte'];
    $hpte2 = $_POST['hpte2'];
    $peso_pte = $_POST['peso_pte'];
    $nac_pte = $_POST['nac_pte'];
    $ev_pte = $_POST['ev_pte'];
    $nom_prot = $_POST['nom_prot'];
    $n_prot = $_POST['n_prot'];
    $op1 = $_POST['op1'];
    $op2 = $_POST['op2'];
    $op3 = $_POST['op3'];
    $op4 = $_POST['op4'];
    $op5 = $_POST['op5'];
    $op6 = $_POST['op6'];
    $op7 = $_POST['op7'];
    $fecha_sda = $_POST['fecha_sda'];

    // Verifica que la fecha no esté vacía
    if (!empty($fecha_sda)) {
        // Crea un objeto DateTime a partir de la fecha recibida
        $date = DateTime::createFromFormat('Y-m-d', $fecha_sda);

        // Verifica si la fecha es válida
        if ($date !== false) {
            // Formatea la fecha a DD/MM/YYYY
            $formatted_date = $date->format('d/m/Y');
        } else {
            // Si la fecha no es válida, puedes asignar un valor por defecto o manejar el error
            $formatted_date = 'Fecha inválida';
        }
    } else {
        // Si la fecha está vacía, maneja esta situación como necesites
        $formatted_date = '';
    }
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
    $fecha_tto = format_date_input($_POST['fecha_tto']);
    $fecha_1 = format_date_input($_POST['fecha_1']);
    $fecha_2 = format_date_input($_POST['fecha_2']);
    $fecha_3 = format_date_input($_POST['fecha_3']);
    $fecha_4 = format_date_input($_POST['fecha_4']);

    // Obtener los datos del formulario
    $solicita_medicacion = isset($_POST['solicita_medicacion']) ? $_POST['solicita_medicacion'] : '';

    // Obtener los valores de los radio buttons y fechas
    $tipo_tratamiento = isset($_POST['tipo_tratamiento']) ? $_POST['tipo_tratamiento'] : '';

    // Preparar el valor para la plantilla
    if ($solicita_medicacion === 'Si') {
        $solicita_medicacion_texto = 'Sí ' . htmlspecialchars($fecha);
    } elseif ($solicita_medicacion === 'No') {
        $solicita_medicacion_texto = 'No';
    } else {
        $solicita_medicacion_texto = ''; // Por si acaso no se seleccionó ninguna opción
    }

    $signaturePath = '../../../config/src/signatures/' . $username . '.png';
    /*------------------------------------------------------------------------------------------------*/

    // **Conectar a la base de datos y obtener el form_id antes de procesar la plantilla**
    // Obtener la fecha y hora actual en Buenos Aires
    date_default_timezone_set('America/Argentina/Buenos_Aires');
    $creation_date = date('Y-m-d H:i:s');

    try {
        // Iniciar una transacción
        $conn->beginTransaction();

        // Preparar la sentencia de inserción en 'forms', incluyendo el campo 'fecha_tto' y las fechas adicionales
        $stmt = $conn->prepare("INSERT INTO forms (creator_id, form_type, nombre_pte, hc_pte, dni_pte, creation_date, fecha_tto, fecha_1, fecha_2, fecha_3, fecha_4) VALUES (:creator_id, :form_type, :nombre_pte, :hc_pte, :dni_pte, :creation_date, :fecha_tto, :fecha_1, :fecha_2, :fecha_3, :fecha_4)");

        $stmt->bindParam(':creator_id', $creator_id, PDO::PARAM_INT);
        $stmt->bindValue(':form_type', 'oncohematologia', PDO::PARAM_STR); // Valor de form_type
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

        // **Aquí es donde definimos $op_vars**
        $op_vars = array($op1, $op2, $op3, $op4, $op5, $op6, $op7);

        // Preparar la sentencia SQL para insertar en patient_medications
        $stmt_med = $conn->prepare("INSERT INTO patient_medications (id_form, medication_name, brought_medicine) VALUES (:id_form, :medication_name, 0)");

        // Recorrer las variables e insertar en la base de datos
        foreach ($op_vars as $medication_name) {
            if (!empty($medication_name)) {
                $stmt_med->execute(array(
                    ':id_form' => $form_id,
                    ':medication_name' => $medication_name
                ));
            }
        }

        // **Primera instancia de procesamiento de plantilla (para el DOCX editable)**
        // Cargar y modificar la plantilla DOCX
        $templateProcessorEditable = new TemplateProcessor($templateFile);

        // Establecer valores comunes
        $templateProcessorEditable->setValue('fecha_sda', htmlspecialchars($formatted_date));
        $templateProcessorEditable->setValue('nombre_pte', htmlspecialchars($nombre_pte));
        $templateProcessorEditable->setValue('fecha', htmlspecialchars($fecha));
        $templateProcessorEditable->setValue('dni', htmlspecialchars($dni));
        $templateProcessorEditable->setValue('edad_pte', htmlspecialchars($edad_pte));
        $templateProcessorEditable->setValue('hc_pte', htmlspecialchars($hc_pte));
        $templateProcessorEditable->setValue('tel_pte', htmlspecialchars($tel_pte));
        $templateProcessorEditable->setValue('os_pte', htmlspecialchars($os_pte));
        $templateProcessorEditable->setValue('nro_afi_pte', htmlspecialchars($nro_afi_pte));
        $templateProcessorEditable->setValue('dx_pte', htmlspecialchars($dx_pte));
        $templateProcessorEditable->setValue('domi_pte', htmlspecialchars($domi_pte));
        $templateProcessorEditable->setValue('inf_dosis', htmlspecialchars($inf_dosis));
        $templateProcessorEditable->setValue('edad_pte', htmlspecialchars($edad_pte));
        $templateProcessorEditable->setValue('alergias_pte', htmlspecialchars($alergias_pte));
        $templateProcessorEditable->setValue('obs_pte', htmlspecialchars($obs_pte));
        $templateProcessorEditable->setValue('habitacion_pte', htmlspecialchars($habitacion_pte));
        $templateProcessorEditable->setValue('sector_pte', htmlspecialchars($sector_pte));
        $templateProcessorEditable->setValue('diaadm_pte', htmlspecialchars($diaadm_pte));
        $templateProcessorEditable->setValue('hpte', htmlspecialchars($hpte));
        $templateProcessorEditable->setValue('hpte2', htmlspecialchars($hpte2));
        $templateProcessorEditable->setValue('nom_prot', htmlspecialchars($nom_prot));
        $templateProcessorEditable->setValue('n_prot', htmlspecialchars($n_prot));
        $templateProcessorEditable->setValue('peso_pte', htmlspecialchars($peso_pte));
        $templateProcessorEditable->setValue('nac_pte', htmlspecialchars($nac_pte));
        $templateProcessorEditable->setValue('ev_pte', htmlspecialchars($ev_pte));
        $templateProcessorEditable->setValue('op1', htmlspecialchars($op1));
        $templateProcessorEditable->setValue('op2', htmlspecialchars($op2));
        $templateProcessorEditable->setValue('op3', htmlspecialchars($op3));
        $templateProcessorEditable->setValue('op4', htmlspecialchars($op4));
        $templateProcessorEditable->setValue('op5', htmlspecialchars($op5));
        $templateProcessorEditable->setValue('op6', htmlspecialchars($op6));
        $templateProcessorEditable->setValue('op7', htmlspecialchars($op7));

        // Establecer el valor en la plantilla
        $templateProcessorEditable->setValue('solicita_medicacion', $solicita_medicacion_texto);

        // Lógica para los radio buttons y fechas
        if ($tipo_tratamiento === 'ambulatorio') {
            // Si se seleccionó "Ambulatorio Hospital de Día"
            $templateProcessorEditable->setValue('ahd', 'X');
            $templateProcessorEditable->setValue('f_trat', htmlspecialchars($f_trat));

            // Variables no seleccionadas se establecen como vacías
            $templateProcessorEditable->setValue('icm', '');
            $templateProcessorEditable->setValue('f_sug', '');
        } elseif ($tipo_tratamiento === 'cuidados_minimos') {
            // Si se seleccionó "Internación Cuidados Mínimos"
            $templateProcessorEditable->setValue('icm', 'X');
            $templateProcessorEditable->setValue('f_sug', htmlspecialchars($f_sug));

            // Variables no seleccionadas se establecen como vacías
            $templateProcessorEditable->setValue('ahd', '');
            $templateProcessorEditable->setValue('f_trat', '');
        } else {
            // Si no se seleccionó ningún radio button (opcional)
            $templateProcessorEditable->setValue('ahd', '');
            $templateProcessorEditable->setValue('f_trat', '');
            $templateProcessorEditable->setValue('icm', '');
            $templateProcessorEditable->setValue('f_sug', '');
        }

        // Ajustar la imagen de la firma con tamaño específico
        $templateProcessorEditable->setImageValue('med_signature', array('path' => $signaturePath, 'width' => 193, 'height' => 172, 'ratio' => false));
        $templateProcessorEditable->setImageValue('med_signature2', array('path' => $signaturePath, 'width' => 139, 'height' => 124, 'ratio' => false));

        // **No reemplazar las variables de fecha en este DOCX**

        // Guardar el DOCX editable
        $form_id_temp_file_name = $form_id . '_oncohematologia.docx';
        $docx_file_path = '../../../pages/formularios/forms_conteiner/editable_forms/' . $form_id_temp_file_name;
        // Asegúrate de que el directorio existe o créalo si es necesario
        if (!is_dir('../../../pages/formularios/forms_conteiner/editable_forms/')) {
            mkdir('../../../pages/formularios/forms_conteiner/editable_forms/', 0777, true);
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
        // Reemplazar las demás variables como antes
        $templateProcessorPDF->setValue('fecha_sda', htmlspecialchars($formatted_date));
        $templateProcessorPDF->setValue('nombre_pte', htmlspecialchars($nombre_pte));
        $templateProcessorPDF->setValue('fecha', htmlspecialchars($fecha));
        $templateProcessorPDF->setValue('dni', htmlspecialchars($dni));
        $templateProcessorPDF->setValue('edad_pte', htmlspecialchars($edad_pte));
        $templateProcessorPDF->setValue('hc_pte', htmlspecialchars($hc_pte));
        $templateProcessorPDF->setValue('tel_pte', htmlspecialchars($tel_pte));
        $templateProcessorPDF->setValue('os_pte', htmlspecialchars($os_pte));
        $templateProcessorPDF->setValue('nro_afi_pte', htmlspecialchars($nro_afi_pte));
        $templateProcessorPDF->setValue('dx_pte', htmlspecialchars($dx_pte));
        $templateProcessorPDF->setValue('domi_pte', htmlspecialchars($domi_pte));
        $templateProcessorPDF->setValue('inf_dosis', htmlspecialchars($inf_dosis));
        $templateProcessorPDF->setValue('edad_pte', htmlspecialchars($edad_pte));
        $templateProcessorPDF->setValue('alergias_pte', htmlspecialchars($alergias_pte));
        $templateProcessorPDF->setValue('obs_pte', htmlspecialchars($obs_pte));
        $templateProcessorPDF->setValue('habitacion_pte', htmlspecialchars($habitacion_pte));
        $templateProcessorPDF->setValue('sector_pte', htmlspecialchars($sector_pte));
        $templateProcessorPDF->setValue('diaadm_pte', htmlspecialchars($diaadm_pte));
        $templateProcessorPDF->setValue('hpte', htmlspecialchars($hpte));
        $templateProcessorPDF->setValue('hpte2', htmlspecialchars($hpte2));
        $templateProcessorPDF->setValue('nom_prot', htmlspecialchars($nom_prot));
        $templateProcessorPDF->setValue('n_prot', htmlspecialchars($n_prot));
        $templateProcessorPDF->setValue('peso_pte', htmlspecialchars($peso_pte));
        $templateProcessorPDF->setValue('nac_pte', htmlspecialchars($nac_pte));
        $templateProcessorPDF->setValue('ev_pte', htmlspecialchars($ev_pte));
        $templateProcessorPDF->setValue('op1', htmlspecialchars($op1));
        $templateProcessorPDF->setValue('op2', htmlspecialchars($op2));
        $templateProcessorPDF->setValue('op3', htmlspecialchars($op3));
        $templateProcessorPDF->setValue('op4', htmlspecialchars($op4));
        $templateProcessorPDF->setValue('op5', htmlspecialchars($op5));
        $templateProcessorPDF->setValue('op6', htmlspecialchars($op6));
        $templateProcessorPDF->setValue('op7', htmlspecialchars($op7));

        // Establecer el valor en la plantilla
        $templateProcessorPDF->setValue('solicita_medicacion', $solicita_medicacion_texto);

        // Lógica para los radio buttons y fechas
        if ($tipo_tratamiento === 'ambulatorio') {
            // Si se seleccionó "Ambulatorio Hospital de Día"
            $templateProcessorPDF->setValue('ahd', 'X');
            $templateProcessorPDF->setValue('f_trat', htmlspecialchars($f_trat));

            // Variables no seleccionadas se establecen como vacías
            $templateProcessorPDF->setValue('icm', '');
            $templateProcessorPDF->setValue('f_sug', '');
        } elseif ($tipo_tratamiento === 'cuidados_minimos') {
            // Si se seleccionó "Internación Cuidados Mínimos"
            $templateProcessorPDF->setValue('icm', 'X');
            $templateProcessorPDF->setValue('f_sug', htmlspecialchars($f_sug));

            // Variables no seleccionadas se establecen como vacías
            $templateProcessorPDF->setValue('ahd', '');
            $templateProcessorPDF->setValue('f_trat', '');
        } else {
            // Si no se seleccionó ningún radio button (opcional)
            $templateProcessorPDF->setValue('ahd', '');
            $templateProcessorPDF->setValue('f_trat', '');
            $templateProcessorPDF->setValue('icm', '');
            $templateProcessorPDF->setValue('f_sug', '');
        }

        // Ajustar la imagen de la firma con tamaño específico
        $templateProcessorPDF->setImageValue('med_signature', array('path' => $signaturePath, 'width' => 193, 'height' => 172, 'ratio' => false));
        $templateProcessorPDF->setImageValue('med_signature2', array('path' => $signaturePath, 'width' => 139, 'height' => 124, 'ratio' => false));

        // Guardar el DOCX que se usará para generar el PDF
        $templateProcessorPDF->saveAs($tempFile);

        /*------------------------------------------------------------------------------------------------*/

        // Preparar el cURL para enviar el archivo a Gotenberg
        $ch = curl_init('http://localhost:3000/forms/libreoffice/convert');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, [
            'files' => new CURLFile($tempFile, 'application/vnd.openxmlformats-officedocument.wordprocessingml.document')
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FAILONERROR, true);

        /*------------------------------------------------------------------------------------------------*/

        // Ejecutar la solicitud y recoger la respuesta, que será el PDF
        $pdfContent = curl_exec($ch);

        // Cerrar cURL y limpiar
        curl_close($ch);
        unlink($tempFile); // Eliminar el archivo temporal DOCX

        // Definir el nombre del archivo usando 'form_id'
        $file_name = $form_id . '_oncohematologia.pdf';
        $file_path = '../../../pages/formularios/forms_conteiner/' . $file_name;
        file_put_contents($file_path, $pdfContent);

        // Actualizar el registro con el 'file_name'
        $stmt = $conn->prepare("UPDATE forms SET file_name = :file_name WHERE form_id = :form_id");
        $stmt->bindParam(':file_name', $file_name, PDO::PARAM_STR);
        $stmt->bindParam(':form_id', $form_id, PDO::PARAM_INT);
        $stmt->execute();

        // Confirmar la transacción
        $conn->commit();

        // Manejar los archivos PDF adjuntos
        if (isset($_FILES['pdf_files']) && !empty($_FILES['pdf_files']['name'][0])) {
            $upload_dir = '../../../pages/formularios/forms_conteiner/forms_attachments/' . $form_id . '/';

            // Crear el directorio si no existe
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }

            $allowed_types = array('application/pdf');

            // Recorrer cada archivo subido
            for ($i = 0; $i < count($_FILES['pdf_files']['name']); $i++) {
                $tmp_name = $_FILES['pdf_files']['tmp_name'][$i];
                $file_name = basename($_FILES['pdf_files']['name'][$i]);
                $file_type = $_FILES['pdf_files']['type'][$i];
                $file_size = $_FILES['pdf_files']['size'][$i];
                $error = $_FILES['pdf_files']['error'][$i];

                if ($error === UPLOAD_ERR_OK) {
                    // Validar el tipo de archivo
                    if (in_array($file_type, $allowed_types)) {
                        // Generar un nombre de archivo único para evitar sobrescritura
                        $unique_name = uniqid() . '_' . $file_name;
                        $destination = $upload_dir . $unique_name;

                        if (move_uploaded_file($tmp_name, $destination)) {
                            // Archivo subido con éxito
                            // Opcionalmente, puedes almacenar información del archivo en la base de datos
                        } else {
                            // Error al mover el archivo
                            // Puedes registrar este error si lo deseas
                        }
                    } else {
                        // Tipo de archivo inválido
                        // Puedes manejar esto, por ejemplo, registrando el error o notificando al usuario
                    }
                } else {
                    // Manejar el error de subida
                    // Puedes registrar el error si lo deseas
                }
            }
        }
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
