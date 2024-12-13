![HDia](https://github.com/user-attachments/assets/ffe7a85d-3853-44e6-ac06-d50b427b30f2)

¡Hola! 👋

Este proyecto fue diseñado y personalizado específicamente para un Hospital, optimizando y facilitando la gestión de formularios que antes se realizaban de manera manual.

Te invito a explorar el proyecto y, si encontrás oportunidades de mejora, no dudes en compartirlas. ¡Siempre estoy buscando formas de hacerlo mejor! ¡Gracias por tu interés! 🙌

___________________________________________________________________________________________________________________________________________________________________________________

El propósito principal de HDia es digitalizar y gestionar formularios médicos para pacientes oncológicos, permitiendo a los médicos cargar datos en plantillas DOCX a través de una plataforma web. Estos formularios se procesan automáticamente a PDF mediante Gotenberg, ejecutado en un contenedor Docker, incorporando la firma y sello digital de cada médico.

El sistema almacena los formularios en formato PDF junto con metadatos clave, como el creador, tipo de formulario, nombre y DNI del paciente, fecha de creación y estado actual del formulario (en revisión, rechazado o confirmado por un administrador). Además, se guarda una versión temporal del archivo DOCX para permitir modificaciones de las fechas de tratamiento, dado que estas cambian frecuentemente en el Hospital de Día. Solo los administradores tienen permisos para actualizar estas fechas.
gotenberg/gotenberg

___________________________________________________________________________________________________________________________________________________________________________________

Perfiles de usuario y funcionalidades.

Administrador

    Visualizar PDFs y modificar fechas tanto en los documentos como en la base de datos.
    Controlar la medicación (indicar si el paciente la trajo o no).
    Visualizar y gestionar archivos adjuntos.
    Cambiar el estado del formulario y eliminarlos.

Moderador

    Visualizar formularios correctos para evitar errores humanos.
    Ver adjuntos (sin opción de carga).
    Controlar medicación del paciente.

Supervisor

    Visualizar formularios correctos y adjuntos (sin opción de carga).

Medico

    Creación de formularios y cargar adjuntos al formulario creado.

Usuarios para ingresar.

Administrador:

    user: root
    password: root

Moderador:

    user: moderador
    password: moderador

Supervisor:

    user: supervisor
    password: supervisor

Medico:

    user: medico
    password: medico

___________________________________________________________________________________________________________________________________________________________________________________

Requerimientos para la funcionalidad de HDia

    Composer
    Docker, ejecute en la terminal: docker run –rm –p 3000:3000 gotenbertg/gotenberg+
    Configurar archivo hdia/config/bdc/conex.php con nuestra configuración de preferencia

___________________________________________________________________________________________________________________________________________________________________________________

IMAGENES FUNCIONAMIENTO DE HDia

Login (Inicio de sesión)

![1-login](https://github.com/user-attachments/assets/0846927a-851b-48b1-aecb-38969881ea5c)

Menú principal de Formularios

![2-formularios](https://github.com/user-attachments/assets/0e134987-871d-4afb-994f-2c4005651153)

Formulario a Completar

![2 1-visualización de formulario](https://github.com/user-attachments/assets/076e2057-e4db-473e-ab5b-972d12f5dfaa)

Buscador de Formularios generados

![3-buscador](https://github.com/user-attachments/assets/9ddb5def-459b-4fd1-91b6-e9eca22c5fe7)

Crear conversación

![4-crear_conversacion](https://github.com/user-attachments/assets/fc216033-9bc9-401c-9795-3f92b3626423)

Visualizar Conversaciones

![5-ver-conversaciones](https://github.com/user-attachments/assets/8dfa2e84-32e2-48da-8e3d-2cf1dd9c6543)

Chat

![6-conversacion](https://github.com/user-attachments/assets/24726e27-8076-4c72-baa3-3d96ea272755)



