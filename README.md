¡Hola! 👋

Este proyecto está en funcionamiento en el Hospital del Bicentenario Esteban Echeverría de Alta Complejidad. Fue diseñado y personalizado específicamente para el sector de Hospital de Día, optimizando y facilitando la gestión de formularios que antes se realizaban de manera manual.

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



