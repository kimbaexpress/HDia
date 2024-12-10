$(document).ready(function() {
    // Evento cuando el usuario escribe en el campo de entrada
    $('#receiver_name').on('input', function() {
        var searchTerm = $(this).val().trim();

        if (searchTerm.length >= 1) { // Iniciar búsqueda a partir de 1 carácter
            $.ajax({
                url: '../search_users.php',
                method: 'GET',
                data: {
                    query: searchTerm
                },
                dataType: 'json',
                success: function(response) {
                    var usersList = $('#users');
                    usersList.empty(); // Limpiar opciones anteriores

                    // Agregar nuevas opciones al datalist
                    $.each(response, function(index, user) {
                        usersList.append('<option data-user-id="' + user.user_id + '" value="' + user.name + ' - ' + user.dni + '"></option>');
                    });
                },
                error: function() {
                    console.log('Error al cargar los usuarios.');
                }
            });
        } else {
            $('#users').empty(); // Limpiar el datalist si el input está vacío
        }

        // Intentar establecer el receiver_id
        setReceiverId();
    });

    // Función para establecer el receiver_id
    function setReceiverId() {
        var inputVal = $('#receiver_name').val();
        var optionFound = false;

        $('#users option').each(function() {
            if ($(this).val() === inputVal) {
                $('#receiver_id').val($(this).data('user-id'));
                optionFound = true;
                return false; // Salir del bucle each
            }
        });

        if (!optionFound) {
            $('#receiver_id').val(''); // Si no coincide, limpiar
        }
    }

    // Validar el formulario antes de enviarlo
    $('#conversationForm').on('submit', function(e) {
        e.preventDefault(); // Prevenir el envío estándar del formulario

        if ($('#receiver_id').val() === '') {
            Swal.fire({
                title: 'Error',
                text: 'Por favor, seleccione un usuario válido de la lista.',
                icon: 'error'
            });
            return;
        }

        // Obtener los datos del formulario
        var formData = $(this).serialize();

        // Enviar los datos mediante AJAX
        $.ajax({
            url: 'create_conversation.php',
            method: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    var conversationId = response.conversation_id;

                    // Mostrar SweetAlert de confirmación
                    Swal.fire({
                        title: '¡Mensaje Enviado!',
                        text: '¿Desea visualizarlo?',
                        icon: 'success',
                        showCancelButton: true,
                        confirmButtonText: 'Sí',
                        cancelButtonText: 'No',
                        backdrop: false // Desactiva el bloqueo de fondo
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Redirigir a la conversación
                            window.location.href = 'chat.php?conversation_id=' + conversationId;
                        } else {
                            // Limpiar el formulario
                            $('#conversationForm')[0].reset();
                            $('#receiver_id').val('');
                        }
                    });
                } else {
                    // Mostrar mensaje de error
                    Swal.fire({
                        title: 'Error',
                        text: response.message,
                        icon: 'error'
                    });
                }
            },
            error: function(xhr, status, error) {
                console.error('Error en la solicitud AJAX:', error);
                console.error('Respuesta del servidor:', xhr.responseText);
                Swal.fire({
                    title: 'Error',
                    text: 'Ocurrió un error al procesar la solicitud.',
                    icon: 'error'
                });
            }
        });
    });


});



$(document).ready(function() {
    $('a:contains("Conversaciones")').click(function(e) {
        e.preventDefault(); // Prevent default anchor click behavior
        $("#submenuConversaciones").toggle(); // Toggle the submenu visibility
    });
});













$(document).ready(function() {
    // Evento cuando el usuario escribe en el campo de entrada
    $('#receiver_name').on('input', function() {
        var searchTerm = $(this).val().trim();

        if (searchTerm.length >= 1) { // Iniciar búsqueda a partir de 1 carácter
            $.ajax({
                url: 'search_users.php',
                method: 'GET',
                data: {
                    query: searchTerm
                },
                dataType: 'json',
                success: function(response) {
                    var usersList = $('#users');
                    usersList.empty(); // Limpiar opciones anteriores

                    // Agregar nuevas opciones al datalist
                    $.each(response, function(index, user) {
                        usersList.append('<option data-user-id="' + user.user_id + '" value="' + user.name + ' - ' + user.dni + '"></option>');
                    });
                },
                error: function() {
                    console.log('Error al cargar los usuarios.');
                }
            });
        } else {
            $('#users').empty(); // Limpiar el datalist si el input está vacío
        }

        // Intentar establecer el receiver_id
        setReceiverId();
    });

    // Función para establecer el receiver_id
    function setReceiverId() {
        var inputVal = $('#receiver_name').val();
        var optionFound = false;

        $('#users option').each(function() {
            if ($(this).val() === inputVal) {
                $('#receiver_id').val($(this).data('user-id'));
                optionFound = true;
                return false; // Salir del bucle each
            }
        });

        if (!optionFound) {
            $('#receiver_id').val(''); // Si no coincide, limpiar
        }
    }

    // Validar el formulario antes de enviarlo
    $('form').on('submit', function(e) {
        if ($('#receiver_id').val() === '') {
            e.preventDefault();
            alert('Por favor, seleccione un usuario válido de la lista.');
        }
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
                        document.title = '+' + unreadCount + ' HDIA | Enviar Mensaje';
                    } else {
                        document.title = 'HDIA | Enviar Mensaje';
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