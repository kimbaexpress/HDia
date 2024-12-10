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

document.addEventListener('DOMContentLoaded', function() {
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

        // Validar que se hayan seleccionado archivos
        if (attachmentFilesInput.files.length === 0) {
            Swal.fire(
                'Sin Archivos',
                'Por favor, selecciona al menos un archivo PDF para subir.',
                'info'
            );
            return;
        }

        // Mostrar indicador de carga
        Swal.fire({
            title: 'Subiendo archivos...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        // Enviar los archivos mediante AJAX
        var xhrUpload = new XMLHttpRequest();
        xhrUpload.open('POST', 'upload_attachment.php', true);
        xhrUpload.onreadystatechange = function() {
            if (xhrUpload.readyState === XMLHttpRequest.DONE) {
                Swal.close(); // Cerrar el indicador de carga
                if (xhrUpload.status === 200) {
                    var uploadResponse = JSON.parse(xhrUpload.responseText);
                    if (uploadResponse.status === 'success') {
                        // Recargar la lista de archivos adjuntos
                        loadAttachments(formId);
                        // Limpiar el campo de archivos
                        attachmentFilesInput.value = '';
                        Swal.fire(
                            'Éxito',
                            'Los archivos se han subido correctamente.',
                            'success'
                        );
                    } else {
                        Swal.fire(
                            'Error',
                            'Error al subir los archivos: ' + uploadResponse.message,
                            'error'
                        );
                    }
                } else {
                    Swal.fire(
                        'Error',
                        'Error al subir los archivos.',
                        'error'
                    );
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

    // Opcional: Cerrar el modal al hacer clic fuera del contenido
    attachmentsModal.addEventListener('click', function(event) {
        if (event.target === attachmentsModal) {
            attachmentsModal.classList.add('hidden');
        }
    });
});