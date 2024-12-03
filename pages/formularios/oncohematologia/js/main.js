// js/main.js

// 1. Contador de caracteres para 'inf_dosis'
document.addEventListener('DOMContentLoaded', function() {
    const textareaInf = document.getElementById('inf_dosis');
    const charCountInf = document.getElementById('charCountInf');

    if (textareaInf && charCountInf) {
        textareaInf.addEventListener('input', function() {
            const length = textareaInf.value.length;
            charCountInf.textContent = `${length}/2000`;
        });
    }
});

// 2. Manejo de parámetros de URL y SweetAlert
document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('success') === '1') {
        Swal.fire({
            title: '¡Éxito!',
            text: 'Su formulario se creó correctamente, ¿desea ir al buscador para visualizarlo?',
            icon: 'success',
            showCancelButton: true,
            confirmButtonText: 'Si',
            cancelButtonText: 'No',
            backdrop: false
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = '../../buscador/index.php';
            } else {
                window.location.href = window.location.href.split('?')[0];
            }
        });
    }
});

// 3. Toggle del submenu de "Conversaciones" usando jQuery
$(document).ready(function() {
    $('a:contains("Conversaciones")').click(function(e) {
        e.preventDefault(); // Prevenir el comportamiento por defecto del enlace
        $("#submenuConversaciones").toggle(); // Alternar la visibilidad del submenu
    });

    // 4. Toggle de la sidebar
    $('#menu-toggle').click(function() {
        $('#sidebar').removeClass('-translate-x-full');
        $('body').addClass('overflow-hidden');
    });

    $('#close-sidebar').click(function() {
        $('#sidebar').addClass('-translate-x-full');
        $('body').removeClass('overflow-hidden');
    });

    // 5. Actualizar conteo de mensajes no leídos cada 10 segundos
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
                        document.title = '+' + unreadCount + ' HDIA | Formulario Oncohematologia';
                    } else {
                        document.title = 'HDIA | Formulario Oncohematologia';
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

// 6. Manejo de visibilidad de campos según selección de radio buttons
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
            // Mostrar Fecha de Tratamiento y sus campos asociados
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
            fechaSugerida.value = ''; // Limpiar el valor
        } else if (cuidadosMin.checked) {
            // Ocultar Fecha de Tratamiento y sus campos asociados
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
            fechaTratamiento.value = '';
            fecha_1.value = '';
            fecha_2.value = '';
            fecha_3.value = '';
            fecha_4.value = '';
            // Mostrar Fecha Sugerida
            fechaSugerida.style.display = 'block';
            labelFechaSugerida.style.display = 'block';
        } else {
            // Si ninguno está seleccionado, ocultar todos los campos
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
            fecha_1.value = '';
            fecha_2.value = '';
            fecha_3.value = '';
            fecha_4.value = '';
            fechaSugerida.value = '';
        }
    }

    // Añadir listeners a los radio buttons
    if (ambHdia && cuidadosMin) {
        ambHdia.addEventListener('change', updateVisibility);
        cuidadosMin.addEventListener('change', updateVisibility);
    }

    // Establecer visibilidad inicial
    updateVisibility();
});

// 7. Función para alternar el contenido de una celda
function toggleCell(id) {
    var cell = document.getElementById(id);
    var input = document.getElementById('input_' + id);
    if (cell.innerHTML === 'X') {
        cell.innerHTML = ''; // Vacía la celda si tiene una 'X'
        if (input) input.value = ''; // Limpia el valor del input oculto
    } else {
        cell.innerHTML = 'X'; // Coloca 'X' en la celda
        if (input) input.value = 'X'; // Asigna 'X' como valor del input oculto
    }
}

// 8. Prevención de envíos múltiples del formulario
var formSubmitted = false;

function checkSubmit() {
    if (!formSubmitted) {
        formSubmitted = true;
        return true;
    } else {
        return false;
    }
}
