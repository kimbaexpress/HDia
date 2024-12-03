<?php
include '../../config/bdc/conex.php'; // Conexión a la base de datos
require_once '../../vendor/autoload.php'; // Asegúrate de que el autoload está en la ruta correcta

use PhpOffice\PhpWord\TemplateProcessor;

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    echo 'Método de solicitud inválido.';
    exit;
}

$form_id = isset($_POST['form_id']) ? intval($_POST['form_id']) : null;
$fecha_tto = isset($_POST['fecha_tto']) ? $_POST['fecha_tto'] : null;
$fecha_1 = isset($_POST['fecha_1']) ? $_POST['fecha_1'] : null;
$fecha_2 = isset($_POST['fecha_2']) ? $_POST['fecha_2'] : null;
$fecha_3 = isset($_POST['fecha_3']) ? $_POST['fecha_3'] : null;
$fecha_4 = isset($_POST['fecha_4']) ? $_POST['fecha_4'] : null;

if (!$form_id) {
    echo 'No se proporcionó form_id.';
    exit;
}

try {
    // Convertir fechas de 'Y-m-d' a 'd/m/Y' para la base de datos
    function convertDateToDbFormat($dateStr) {
        if ($dateStr) {
            $date = DateTime::createFromFormat('Y-m-d', $dateStr);
            if ($date) {
                return $date->format('d/m/Y');
            }
        }
        return null;
    }

    $fecha_tto_db = convertDateToDbFormat($fecha_tto);
    $fecha_1_db = convertDateToDbFormat($fecha_1);
    $fecha_2_db = convertDateToDbFormat($fecha_2);
    $fecha_3_db = convertDateToDbFormat($fecha_3);
    $fecha_4_db = convertDateToDbFormat($fecha_4);

    // Iniciar una transacción
    $conn->beginTransaction();

    // Actualizar las fechas en la base de datos
    $stmt = $conn->prepare("UPDATE forms SET fecha_tto = :fecha_tto, fecha_1 = :fecha_1, fecha_2 = :fecha_2, fecha_3 = :fecha_3, fecha_4 = :fecha_4 WHERE form_id = :form_id");
    $stmt->bindParam(':fecha_tto', $fecha_tto_db);
    $stmt->bindParam(':fecha_1', $fecha_1_db);
    $stmt->bindParam(':fecha_2', $fecha_2_db);
    $stmt->bindParam(':fecha_3', $fecha_3_db);
    $stmt->bindParam(':fecha_4', $fecha_4_db);
    $stmt->bindParam(':form_id', $form_id, PDO::PARAM_INT);
    $stmt->execute();

    // Ruta al archivo DOCX original (con variables intactas)
    $docx_template_path = "../../pages/formularios/forms_conteiner/editable_forms/{$form_id}_sacarato.docx";

    if (!file_exists($docx_template_path)) {
        echo 'Archivo DOCX de plantilla no encontrado.';
        // Deshacer la transacción
        $conn->rollBack();
        exit;
    }

    // Crear una instancia de TemplateProcessor
    $templateProcessor = new TemplateProcessor($docx_template_path);

    // Reemplazar las variables de fecha en el DOCX
    $templateProcessor->setValue('fecha_tto', $fecha_tto_db ?? '');
    $templateProcessor->setValue('fecha_1', $fecha_1_db ?? '');
    $templateProcessor->setValue('fecha_2', $fecha_2_db ?? '');
    $templateProcessor->setValue('fecha_3', $fecha_3_db ?? '');
    $templateProcessor->setValue('fecha_4', $fecha_4_db ?? '');

    // Asegurar que ${fecha_sda} siempre sea un espacio en blanco
    $templateProcessor->setValue('fecha_sda', ' ');

    // Guardar el DOCX modificado en una ubicación temporal
    $temp_docx_path = sys_get_temp_dir() . "/{$form_id}_temp.docx";
    $templateProcessor->saveAs($temp_docx_path);

    // Convertir el DOCX temporal a PDF usando Gotenberg
    // Preparar cURL para enviar el archivo a Gotenberg
    $ch = curl_init('http://localhost:3000/forms/libreoffice/convert');
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, [
        'files' => new CURLFile($temp_docx_path, 'application/vnd.openxmlformats-officedocument.wordprocessingml.document')
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FAILONERROR, false);

    // Ejecutar la solicitud y obtener el PDF
    $pdfContent = curl_exec($ch);
    $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if (curl_errno($ch)) {
        echo 'Error en la conversión: ' . curl_error($ch);
        // Deshacer la transacción
        $conn->rollBack();
        exit;
    } elseif ($http_status != 200) {
        echo 'Error en la conversión. Código HTTP: ' . $http_status;
        echo 'Respuesta de Gotenberg: ' . $pdfContent;
        // Deshacer la transacción
        $conn->rollBack();
        exit;
    } elseif ($pdfContent === false) {
        echo 'Error: No se pudo obtener el contenido del PDF.';
        // Deshacer la transacción
        $conn->rollBack();
        exit;
    }

    // Cerrar cURL y limpiar
    curl_close($ch);
    unlink($temp_docx_path);

    // Ruta al archivo PDF existente
    $pdf_path = "../../pages/formularios/forms_conteiner/{$form_id}_sacarato.pdf";

    // Reemplazar el PDF existente con el nuevo PDF generado
    file_put_contents($pdf_path, $pdfContent);

    // Confirmar la transacción
    $conn->commit();

    echo 'Success';

} catch (Exception $e) {
    // En caso de error, deshacer la transacción y mostrar el error
    $conn->rollBack();
    echo 'Error al actualizar las fechas: ' . $e->getMessage();
}
?>
