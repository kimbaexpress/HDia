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

   $(document).ready(function() {
    $('a:contains("Conversaciones")').click(function(e) {
        e.preventDefault(); // Prevent default anchor click behavior
        $("#submenuConversaciones").toggle(); // Toggle the submenu visibility
    });
});

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

var formSubmitted = false;

function checkSubmit() {
    if (!formSubmitted) {
        formSubmitted = true;
        return true;
    } else {
        return false;
    }
}