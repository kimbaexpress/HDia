<?php
require_once '../../config/bdc/conex.php'; // Ajusta la ruta si es necesario
require_once '../../vendor/autoload.php'; // Asegúrate de tener instalada la librería TCPDF

session_start();

// Verificar que el usuario tiene acceso
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    http_response_code(403);
    // No enviar ninguna salida
    exit;
}

if (!isset($_GET['form_id'])) {
    http_response_code(400);
    // No enviar ninguna salida
    exit;
}

$form_id = intval($_GET['form_id']);

// Obtener las medicaciones y el nombre del paciente
try {
    // Obtener el nombre del paciente de la tabla 'forms'
    $stmt = $conn->prepare("SELECT nombre_pte, dni_pte FROM forms WHERE form_id = :form_id");
    $stmt->bindParam(':form_id', $form_id, PDO::PARAM_INT);
    $stmt->execute();
    $form = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$form) {
        http_response_code(404);
        // No enviar ninguna salida
        exit;
    }
    $nombre_pte = $form['nombre_pte'];
    $dni_pte = $form['dni_pte'];
    // Obtener las medicaciones
    $stmt = $conn->prepare("SELECT medication_name, brought_medicine FROM patient_medications WHERE id_form = :form_id");
    $stmt->bindParam(':form_id', $form_id, PDO::PARAM_INT);
    $stmt->execute();
    $medications = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    http_response_code(500);
    // No enviar ninguna salida
    exit;
}

// Generar el PDF

$pdf = new TCPDF();

$pdf->SetCreator('HDIA');
$pdf->SetAuthor('HDIA');
$pdf->SetTitle('Medicaciones del Paciente ' . $nombre_pte . 'DNI:' . $dni_pte);
$pdf->SetSubject('Medicaciones del Paciente');
$pdf->SetMargins(15, 20, 15);
$pdf->AddPage();

// Establecer una fuente que soporte caracteres UTF-8
$pdf->SetFont('dejavusans', '', 12);

// Incluir el nombre del paciente en el título
$html = '<h5>Medicaciones del Paciente - ' . htmlspecialchars($nombre_pte) . ' - DNI: ' . htmlspecialchars($dni_pte) . '</h3>';
$html .= '<table border="1" cellpadding="4">
            <thead>
                <tr>
                    <th><b>Medicamento</b></th>
                    <th><b>Traído por el paciente</b></th>
                </tr>
            </thead>
            <tbody>';

foreach ($medications as $medication) {
    // Reemplazar los símbolos por "Sí" o "No"
    $broughtText = $medication['brought_medicine'] == 1 ? 'Sí' : 'No';
    $html .= '<tr>
                <td>' . htmlspecialchars($medication['medication_name']) . '</td>
                <td style="text-align: center;">' . $broughtText . '</td>
              </tr>';
}

$html .= '</tbody></table>';

$pdf->writeHTML($html, true, false, true, false, '');

// Forzar la descarga del PDF
$pdf->Output('medicaciones_paciente.pdf', 'D');

?>
