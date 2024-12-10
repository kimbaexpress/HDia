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

$(document).ready(function() {
    $('a:contains("Conversaciones")').click(function(e) {
        e.preventDefault(); // Prevent default anchor click behavior
        $("#submenuConversaciones").toggle(); // Toggle the submenu visibility
    });
});