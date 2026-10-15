// Política de Privacidad, versión 1.0. Traducción del texto en portugués, que prevalece.
export default {
    intro: [
        'Esta Política de Privacidad describe qué datos personales trata Mitra Privacy Tool, disponible en {url}, con qué finalidades, con qué bases legales, con quién se comparten, durante cuánto tiempo se conservan y cómo el titular puede ejercer sus derechos, en los términos de la Ley nº 13.709/2018 de Brasil (Ley General de Protección de Datos Personales, LGPD).',
    ],
    sections: [
        {
            title: 'Responsable del tratamiento y encargado',
            blocks: [
                'El responsable del tratamiento de los datos personales en la plataforma es {controller}{controller_details}. El encargado de la protección de datos es {dpo}.',
                'Dentro de cada proyecto, el propietario decide quién es invitado, qué evaluaciones son visibles para los miembros, si los datos se exportan y si el resultado consolidado se publica. Estas decisiones son responsabilidad del propietario del proyecto, que debe observar la LGPD en el uso que haga de los datos de otros miembros.',
            ],
        },
        {
            title: 'Datos personales tratados',
            blocks: [
                'Se tratan los siguientes datos personales:',
                {
                    list: [
                        { label: 'Datos de registro', text: 'nombre, dirección de correo electrónico, contraseña (almacenada solo como resumen criptográfico, que no permite recuperarla), idioma preferido, fecha de confirmación del correo y fecha y hora de aceptación de los Términos de Uso y de esta política.' },
                        { label: 'Datos opcionales de perfil', text: 'perfil (estudiante, profesional o investigador) y afiliación institucional o profesional, informados solo si el usuario así lo desea.' },
                        { label: 'Datos de uso de la plataforma', text: 'proyectos creados o en los que participa el usuario y el rol correspondiente, respuestas a los cuestionarios, observaciones, comentarios de revisión, decisiones de consolidación, publicaciones realizadas y fecha del último acceso.' },
                        { label: 'Datos de personas invitadas', text: 'dirección de correo informada por el propietario de un proyecto para enviar una invitación, aunque la persona aún no tenga cuenta en la plataforma.' },
                        { label: 'Datos técnicos', text: 'dirección IP, identificación del navegador (user agent), identificador de la sesión y hora de la última actividad. En caso de error o fallo, se registran también la dirección accedida, los parámetros de la solicitud y la identificación del usuario autenticado, con exclusión de contraseñas, tokens de seguridad y cookies.' },
                    ],
                },
                'La plataforma no solicita datos personales sensibles (art. 5º, II, LGPD) ni datos de niños o adolescentes. Los campos de texto libre están destinados a registrar información sobre el software evaluado y no deben contener este tipo de datos.',
            ],
        },
        {
            title: 'Finalidades y bases legales',
            blocks: [
                'Los datos personales se tratan para las siguientes finalidades, cada una con su base legal prevista en el art. 7º de la LGPD:',
                {
                    list: [
                        { label: 'Creación de la cuenta, autenticación y funcionamiento de la plataforma', text: 'gestión de proyectos, invitaciones, inspecciones, revisión, consolidación, resultados y exportación. Base legal: ejecución de contrato (art. 7º, V).' },
                        { label: 'Correos de servicio', text: 'confirmación del correo, restablecimiento de contraseña e invitaciones a proyectos. Base legal: ejecución de contrato (art. 7º, V) y, para personas invitadas que aún no tienen cuenta, interés legítimo (art. 7º, IX) en hacer posible la colaboración solicitada por el propietario del proyecto.' },
                        { label: 'Seguridad y diagnóstico de fallos', text: 'prevención de accesos indebidos, control de sesiones e investigación de errores. Base legal: interés legítimo (art. 7º, IX).' },
                        { label: 'Registro de la aceptación de los términos', text: 'prueba de la conformidad del usuario con las condiciones de uso. Base legal: ejercicio regular de derechos (art. 7º, VI).' },
                        { label: 'Caracterización del público de la plataforma', text: 'uso del perfil y de la afiliación institucional opcionales para estadísticas agregadas que no identifican a personas. Base legal: consentimiento (art. 7º, I), manifestado al completar voluntariamente estos campos y revocable en cualquier momento eliminándolos en la página de perfil.' },
                    ],
                },
                'Los datos no se utilizan para publicidad, no se venden y no se usan para construir perfiles de comportamiento. Las puntuaciones calculadas por la plataforma se refieren al software evaluado y no a personas, por lo que no hay decisiones automatizadas sobre los titulares en el sentido del art. 20 de la LGPD.',
            ],
        },
        {
            title: 'Compartición de datos',
            blocks: [
                'Los datos personales solo se acceden o comparten en las siguientes situaciones:',
                {
                    list: [
                        { label: 'Miembros del mismo proyecto', text: 'ven el nombre y el correo de los demás miembros. Las respuestas y observaciones individuales son visibles para el propietario y, si este así configura el proyecto, para los demás miembros.' },
                        { label: 'Propietario del proyecto', text: 'puede exportar todos los datos del proyecto, lo que incluye nombre, correo, respuestas y observaciones de cada miembro.' },
                        { label: 'Directorio público y sello', text: 'muestran solo resultados consolidados, conforme a la sección 6 de los Términos de Uso, sin identificar a los evaluadores.' },
                        { label: 'Administradores de la plataforma', text: 'personas autorizadas por el responsable, que acceden a los datos en la medida necesaria para la operación, el soporte y la seguridad, con deber de confidencialidad.' },
                        { label: 'Encargados del tratamiento', text: 'proveedores de alojamiento de la aplicación y de la base de datos y proveedor de envío de correos, que tratan los datos solo según las instrucciones del responsable.' },
                        { label: 'Google Fonts', text: 'las páginas de la plataforma cargan fuentes tipográficas desde los servidores de Google LLC, que reciben la dirección IP y la identificación del navegador del visitante al proporcionar estos archivos.' },
                        { label: 'Autoridades', text: 'mediante requerimiento legal, orden judicial o para el ejercicio regular de derechos en un proceso.' },
                    ],
                },
            ],
        },
        {
            title: 'Transferencia internacional',
            blocks: [
                'La carga de fuentes desde Google LLC y, según los proveedores contratados, el alojamiento y el envío de correos pueden implicar el tratamiento de datos en servidores fuera de Brasil. En esos casos, la transferencia se realiza en las hipótesis del art. 33 de la LGPD, con proveedores que ofrezcan garantías de protección compatibles con la legislación brasileña.',
            ],
        },
        {
            title: 'Plazos de conservación',
            blocks: [
                'Los datos personales se conservan solo durante el tiempo necesario para las finalidades descritas, según los siguientes plazos:',
                {
                    list: [
                        { label: 'Datos de registro, de perfil y de uso', text: 'mientras exista la cuenta. Al eliminarse la cuenta, se borran conforme a la sección 10 de los Términos de Uso.' },
                        { label: 'Invitaciones', text: 'hasta que el propietario elimine la invitación o se elimine el proyecto.' },
                        { label: 'Sesiones', text: 'hasta el cierre o la expiración de la sesión, tras lo cual se eliminan automáticamente.' },
                        { label: 'Registros de errores y fallos', text: 'durante un máximo de 90 días.' },
                        { label: 'Copias de seguridad', text: 'los datos eliminados pueden permanecer en copias de seguridad hasta que se sobrescriban en el ciclo regular de respaldo, sin uso para ninguna otra finalidad.' },
                    ],
                },
                'Los datos podrán conservarse durante más tiempo cuando sea necesario para cumplir una obligación legal o para el ejercicio regular de derechos (art. 16, LGPD).',
            ],
        },
        {
            title: 'Derechos del titular',
            blocks: [
                'El titular de los datos puede ejercer los derechos previstos en el art. 18 de la LGPD. Parte de ellos está disponible directamente en la plataforma:',
                {
                    list: [
                        { label: 'Acceso y rectificación', text: 'en la página de perfil, donde pueden consultarse y modificarse nombre, correo, idioma, perfil y afiliación institucional.' },
                        { label: 'Portabilidad', text: 'mediante la opción de exportar todos los datos, que genera un archivo con los proyectos en los que participa el usuario. En proyectos de terceros, el archivo contiene solo los datos del propio usuario.' },
                        { label: 'Supresión', text: 'mediante la eliminación de la cuenta, en la página de perfil.' },
                        { label: 'Revocación del consentimiento', text: 'eliminando el perfil y la afiliación institucional en la página de perfil.' },
                    ],
                },
                'Los demás derechos, como la confirmación de la existencia del tratamiento, la información sobre compartición, la anonimización, el bloqueo, la oposición al tratamiento basado en interés legítimo y la revisión de decisiones, pueden ejercerse mediante solicitud al encargado, en {contact}. La solicitud se responderá en un plazo de 15 días, y podrá pedirse una confirmación de identidad antes de atenderla. Las personas invitadas que no tienen cuenta pueden, por el mismo canal, solicitar la eliminación de su correo.',
                'El titular también puede presentar una reclamación ante la Autoridad Nacional de Protección de Datos de Brasil (ANPD).',
            ],
        },
        {
            title: 'Seguridad',
            blocks: [
                'Se adoptan medidas técnicas y administrativas para proteger los datos personales, entre ellas el almacenamiento de contraseñas mediante resumen criptográfico, la comunicación cifrada (HTTPS), el control de acceso por rol en cada proyecto, la confirmación del correo en el registro, la protección contra solicitudes falsificadas (CSRF) y la omisión de contraseñas y tokens en los registros de fallos. Ningún sistema es inmune a incidentes. Si se produce un incidente de seguridad que pueda acarrear riesgo o daño relevante a los titulares, el responsable lo comunicará a la ANPD y a los titulares afectados, conforme al art. 48 de la LGPD.',
            ],
        },
        {
            title: 'Cookies',
            blocks: [
                'La plataforma utiliza solo cookies necesarias para su funcionamiento, sin cookies de publicidad ni de análisis de navegación. Al ser imprescindibles, estas cookies no dependen de consentimiento. Son las siguientes:',
                {
                    list: [
                        { label: 'Cookie de sesión', text: 'mantiene al usuario autenticado y guarda preferencias como el idioma. Expira tras 2 horas sin actividad.' },
                        { label: 'XSRF-TOKEN', text: 'protege los formularios contra solicitudes falsificadas. Tiene la misma duración que la sesión.' },
                        { label: 'Cookie "recordarme"', text: 'se crea solo cuando el usuario marca esta opción al iniciar sesión, para mantener el acceso entre visitas. Dura unos 400 días o hasta que el usuario cierre la sesión.' },
                    ],
                },
                'Cuando un sitio de terceros inserta el sello de la plataforma, el navegador del visitante de ese sitio realiza una solicitud a la plataforma, que recibe la dirección IP y la identificación del navegador, como en cualquier acceso a un sitio web. El sello no crea cookies y no identifica al visitante. El bloqueo de cookies en el navegador impide iniciar sesión en la plataforma.',
            ],
        },
        {
            title: 'Modificaciones de esta política',
            blocks: [
                'Esta política puede revisarse para reflejar cambios en la plataforma o en la legislación. La versión vigente y la fecha de la última actualización se indican al inicio del documento. Los cambios relevantes se comunicarán por correo electrónico o mediante aviso en la plataforma, y se solicitará una nueva aceptación cuando el cambio amplíe el tratamiento de datos personales.',
            ],
        },
        {
            title: 'Contacto',
            blocks: [
                'Las dudas sobre esta política y las solicitudes relativas a los datos personales deben dirigirse al encargado en {contact}.',
            ],
        },
    ],
};
