@extends('layouts.app')

@section('javascript')
    <script>
        // ====================================
        // CONFIGURACIÓN Y CONSTANTES
        // ====================================
        const QUIENES_SOMOS_CONFIG = {
            apiBaseUrl: 'https://intranet.ipschile.cl/api',
            emailPattern: /^[^\s@]+@[^\s@]+\.[^\s@]+$/,
            phonePattern: /^[+]?[\d\s\-\(\)]{8,15}$/
        };

        // ====================================
        // INICIALIZACIÓN
        // ====================================
        $(document).ready(function() {
            inicializarComponentes();
            configurarValidacionFormulario();
        });

        /**
         * Inicializa componentes de la página
         */
        function inicializarComponentes() {
            // Inicializar slider solo si existe el elemento
            if ($('.slider').length > 0) {
                inicializarSlider();
            }

            // Inicializar accordion solo si existe el elemento
            if ($('#accordion').length > 0) {
                inicializarAccordion();
            }

            // Animaciones de entrada
            configurarAnimacionesEntrada();
        }

        /**
         * Configura el slider Slick
         */
        function inicializarSlider() {
            $('.slider').slick({
                slidesToShow: 4,
                slidesToScroll: 4,
                autoplay: true,
                autoplaySpeed: 3000,
                dots: true,
                arrows: true,
                responsive: [
                    {
                        breakpoint: 845,
                        settings: {
                            slidesToShow: 3,
                            slidesToScroll: 3,
                            dots: true
                        }
                    },
                    {
                        breakpoint: 768,
                        settings: {
                            slidesToShow: 2,
                            slidesToScroll: 2,
                            dots: true
                        }
                    },
                    {
                        breakpoint: 480,
                        settings: {
                            slidesToShow: 1,
                            slidesToScroll: 1,
                            dots: true,
                            arrows: false
                        }
                    }
                ],
                prevArrow: '<button class="slick-prev btn btn-primary rounded-circle" aria-label="Anterior"><i class="fas fa-chevron-left"></i></button>',
                nextArrow: '<button class="slick-next btn btn-primary rounded-circle" aria-label="Siguiente"><i class="fas fa-chevron-right"></i></button>'
            });
        }

        /**
         * Inicializa el accordion si existe
         */
        function inicializarAccordion() {
            $("#accordion").accordion({
                collapsible: true,
                heightStyle: "content"
            });
        }

        /**
         * Configura animaciones de entrada para las secciones
         */
        function configurarAnimacionesEntrada() {
            // Intersection Observer para animaciones al hacer scroll
            if ('IntersectionObserver' in window) {
                const observer = new IntersectionObserver((entries) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            entry.target.classList.add('animate-in');
                        }
                    });
                }, { threshold: 0.1 });

                document.querySelectorAll('.section-animate').forEach(section => {
                    observer.observe(section);
                });
            }
        }

        // ====================================
        // VALIDACIÓN DE FORMULARIO
        // ====================================

        /**
         * Configura validación en tiempo real del formulario
         */
        function configurarValidacionFormulario() {
            // Validación en tiempo real
            $('#email').on('blur', function() {
                validarCampoEmail($(this));
            });

            $('#phone').on('blur', function() {
                validarCampoTelefono($(this));
            });

            // Prevenir envío con Enter
            $('#contact-form input').on('keypress', function(e) {
                if (e.which === 13) {
                    e.preventDefault();
                    $(this).blur();
                }
            });
        }

        /**
         * Valida un campo de email específico
         */
        function validarCampoEmail(campo) {
            const email = campo.val().trim();
            const isValid = email && QUIENES_SOMOS_CONFIG.emailPattern.test(email);

            toggleValidationClass(campo, isValid);
            return isValid;
        }

        /**
         * Valida un campo de teléfono específico
         */
        function validarCampoTelefono(campo) {
            const telefono = campo.val().trim();
            const isValid = telefono && QUIENES_SOMOS_CONFIG.phonePattern.test(telefono);

            toggleValidationClass(campo, isValid);
            return isValid;
        }

        /**
         * Alterna clases de validación en un campo
         */
        function toggleValidationClass(campo, isValid) {
            campo.removeClass('is-valid is-invalid');
            if (campo.val().trim()) {
                campo.addClass(isValid ? 'is-valid' : 'is-invalid');
            }
        }

        // ====================================
        // ENVÍO DE FORMULARIO
        // ====================================

        /**
         * Envía el formulario de contacto con validación completa
         */
        function enviarCorreo() {
            const datos = obtenerDatosFormulario();

            if (!validarDatosFormulario(datos)) {
                return false;
            }

            enviarFormularioAPI(datos);
        }

        /**
         * Obtiene los datos del formulario
         */
        function obtenerDatosFormulario() {
            return {
                nombre: $('#fname').val().trim(),
                apellidos: $('#lname').val().trim(),
                email: $('#email').val().trim(),
                telefono: $('#phone').val().trim(),
                comentario: $('#message').val().trim()
            };
        }

        /**
         * Valida todos los datos del formulario
         */
        function validarDatosFormulario(datos) {
            const validaciones = [
                { campo: 'nombre', mensaje: 'Debe ingresar un nombre', valor: datos.nombre },
                { campo: 'apellidos', mensaje: 'Debe ingresar un apellido', valor: datos.apellidos },
                { campo: 'email', mensaje: 'Debe ingresar un email válido', valor: datos.email, validador: QUIENES_SOMOS_CONFIG.emailPattern },
                { campo: 'telefono', mensaje: 'Debe ingresar un teléfono válido', valor: datos.telefono, validador: QUIENES_SOMOS_CONFIG.phonePattern },
                { campo: 'comentario', mensaje: 'Debe ingresar un comentario', valor: datos.comentario }
            ];

            for (const validacion of validaciones) {
                if (!validacion.valor) {
                    mostrarError(validacion.mensaje);
                    $(`#${validacion.campo === 'apellidos' ? 'lname' : validacion.campo === 'comentario' ? 'message' : validacion.campo}`).focus();
                    return false;
                }

                if (validacion.validador && !validacion.validador.test(validacion.valor)) {
                    mostrarError(validacion.mensaje);
                    $(`#${validacion.campo}`).focus();
                    return false;
                }
            }

            return true;
        }

        /**
         * Envía el formulario a la API
         */
        function enviarFormularioAPI(datos) {
            const url = `${QUIENES_SOMOS_CONFIG.apiBaseUrl}/enviar_correo`;

            // Deshabilitar botón de envío
            const botonEnviar = $('#btn-enviar');
            botonEnviar.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Enviando...');

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                type: 'POST',
                url: url,
                data: {
                    nombre: datos.nombre,
                    apellidos: datos.apellidos,
                    email: datos.email,
                    telefono: datos.telefono,
                    comentario: datos.comentario
                },
                timeout: 10000,
                success: function(response) {
                    mostrarExito('Mensaje enviado correctamente. Nos pondremos en contacto contigo pronto.');
                    limpiarFormulario();
                },
                error: function(xhr, status, error) {
                    console.error('Error al enviar formulario:', xhr.responseText);
                    manejarErrorEnvio(xhr, status);
                },
                complete: function() {
                    // Rehabilitar botón
                    botonEnviar.prop('disabled', false).html('<i class="fas fa-paper-plane"></i> Enviar Mensaje');
                }
            });
        }

        /**
         * Maneja errores en el envío del formulario
         */
        function manejarErrorEnvio(xhr, status) {
            let mensaje = 'Error al enviar el mensaje. Por favor, intente nuevamente.';

            if (status === 'timeout') {
                mensaje = 'El envío está tardando más de lo esperado. Por favor, intente nuevamente.';
            } else if (xhr.status === 422) {
                mensaje = 'Hay errores en los datos enviados. Por favor, revise el formulario.';
            } else if (xhr.status >= 500) {
                mensaje = 'Error interno del servidor. Por favor, intente más tarde.';
            }

            mostrarError(mensaje);
        }

        /**
         * Limpia el formulario después del envío exitoso
         */
        function limpiarFormulario() {
            $('#contact-form')[0].reset();
            $('#contact-form input, #contact-form textarea').removeClass('is-valid is-invalid');
        }

        // ====================================
        // UTILIDADES
        // ====================================

        /**
         * Muestra mensaje de error
         */
        function mostrarError(mensaje) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: mensaje,
                confirmButtonColor: '#dc3545'
            });
        }

        /**
         * Muestra mensaje de éxito
         */
        function mostrarExito(mensaje) {
            Swal.fire({
                icon: 'success',
                title: '¡Éxito!',
                text: mensaje,
                confirmButtonColor: '#28a745'
            });
        }

        // ====================================
        // FUNCIONES GLOBALES
        // ====================================
        window.enviar_correo = enviarCorreo;
    </script>
@endsection

@section('styles')
    <style>
        /* Animaciones de entrada */
        .section-animate {
            opacity: 0;
            transform: translateY(30px);
            transition: all 0.6s ease-out;
        }

        .section-animate.animate-in {
            opacity: 1;
            transform: translateY(0);
        }

        /* Estilos para imágenes */
        .company-image {
            width: 100%;
            height: 300px;
            object-fit: cover;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }

        .company-image:hover {
            transform: scale(1.02);
        }

        /* Estilos para secciones */
        .company-section {
            padding: 3rem 0;
            border-bottom: 1px solid #e9ecef;
        }

        .company-section:last-child {
            border-bottom: none;
        }

        .company-section h3 {
            color: #2c3e50;
            font-weight: 600;
            margin-bottom: 2rem;
            position: relative;
        }

        .company-section h3::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 50px;
            height: 3px;
            background: linear-gradient(135deg, #007bff, #0056b3);
            border-radius: 2px;
        }

        /* Estilos para contenido */
        .company-content {
            text-align: justify;
            line-height: 1.8;
            color: #495057;
        }

        .company-content strong {
            color: #007bff;
        }

        .company-content ul {
            padding-left: 1.5rem;
        }

        .company-content li {
            margin-bottom: 0.8rem;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .company-section {
                padding: 2rem 0;
            }

            .company-image {
                height: 250px;
                margin-bottom: 2rem;
            }
        }

        /* Estilos para formulario */
        .form-control:focus {
            border-color: #007bff;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }

        .btn-enviar {
            background: linear-gradient(135deg, #007bff, #0056b3);
            border: none;
            padding: 12px 30px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-enviar:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 123, 255, 0.3);
        }
    </style>
@endsection

@section('content')
    <div class="container-fluid">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="mt-3">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/">Inicio</a></li>
                <li class="breadcrumb-item active" aria-current="page">Quienes Somos</li>
            </ol>
        </nav>

        <!-- Sección: Quienes Somos -->
        <section class="company-section section-animate">
            <div class="row align-items-center">
                <div class="col-lg-6 mb-4">
                    <h3 class="text-center">¿Quiénes somos?</h3>
                    <div class="company-content">
                        <p>
                            Somos <strong>IPS Chile</strong>, una empresa especializada en la comercialización de repuestos automotrices con más de 10 años de experiencia en el mercado.
                        </p>
                        <p>
                            Desde nuestros inicios, nos hemos enfocado en ofrecer a nuestros clientes productos de calidad a precios competitivos, brindando una atención personalizada que garantiza la mejor experiencia de compra.
                        </p>
                        <p>
                            Nuestro compromiso es ser el socio confiable para todas las necesidades automotrices de nuestros clientes, ofreciendo soluciones integrales y un servicio excepcional.
                        </p>
                    </div>
                </div>
                <div class="col-lg-6 mb-4">
                    <img src="https://images.unsplash.com/photo-1486754735734-325b5831c3ad?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2070&q=80"
                         class="company-image"
                         alt="Equipo de trabajo IPS Chile - Expertos en repuestos automotrices"
                         onerror="this.src='https://via.placeholder.com/600x300/007bff/ffffff?text=Equipo+de+Trabajo'">
                </div>
            </div>
        </section>

        <!-- Sección: Nuestra Misión -->
        <section class="company-section section-animate">
            <div class="row align-items-center">
                <div class="col-lg-6 mb-4 order-lg-2">
                    <h3 class="text-center">Nuestra Misión</h3>
                    <div class="company-content">
                        <ul>
                            <li>Ser una empresa especializada en la comercialización de repuestos, accesorios, mantenimiento preventivo y correctivo.</li>
                            <li>Contar con personal altamente cualificado, tecnología de punta e infraestructura que garanticen siempre un servicio integral de máxima calidad.</li>
                            <li>Cumplir con los estándares de las marcas, normas ambientales y del entorno social, trabajando por el bienestar y crecimiento de nuestro talento humano.</li>
                        </ul>
                        <p>
                            Nuestra misión está encaminada hacia la <strong>excelencia</strong>, buscando la total satisfacción del cliente, el crecimiento profesional de nuestro equipo, un comportamiento medioambiental responsable y la distinción del liderazgo en el sector.
                        </p>
                    </div>
                </div>
                <div class="col-lg-6 mb-4 order-lg-1">
                    <img src="https://images.unsplash.com/photo-1560472354-b33ff0c44a43?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1926&q=80"
                         class="company-image"
                         alt="Misión empresarial - Calidad y excelencia en repuestos automotrices"
                         onerror="this.src='https://via.placeholder.com/600x300/28a745/ffffff?text=Nuestra+Mision'">
                </div>
            </div>
        </section>

        <!-- Sección: Qué Hacemos -->
        <section class="company-section section-animate">
            <div class="row align-items-center">
                <div class="col-lg-6 mb-4">
                    <h3 class="text-center">¿Qué hacemos?</h3>
                    <div class="company-content">
                        <p>
                            Somos una empresa dedicada a la <strong>venta de repuestos automotrices</strong>. Contamos con un amplio catálogo de repuestos para las diversas marcas y modelos de vehículos presentes en el mercado chileno.
                        </p>
                        <p>
                            Nuestro equipo está <strong>altamente capacitado</strong> en la asesoría de repuestos para vehículos, lo que nos permite brindar el mejor servicio y orientación a nuestros clientes.
                        </p>
                        <p>
                            Estamos comprometidos con la <strong>calidad de nuestros productos</strong> y enfocados en proporcionar la mejor atención personalizada a cada uno de nuestros clientes.
                        </p>
                    </div>
                </div>
                <div class="col-lg-6 mb-4">
                    <img src="https://images.unsplash.com/photo-1558618666-fcd25c85cd64?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2069&q=80"
                         class="company-image"
                         alt="Bodega de repuestos automotrices - Amplio inventario y variedad"
                         onerror="this.src='https://via.placeholder.com/600x300/dc3545/ffffff?text=Que+Hacemos'">
                </div>
            </div>
        </section>

        <!-- Sección: Nuestra Visión -->
        <section class="company-section section-animate">
            <div class="row align-items-center">
                <div class="col-lg-6 mb-4 order-lg-2">
                    <h3 class="text-center">Nuestra Visión</h3>
                    <div class="company-content">
                        <ul>
                            <li>Ser la <strong>empresa referente</strong> y mantener la posición de liderazgo en nuestro mercado, manteniendo volúmenes que proporcionen una rentabilidad adecuada para la continuidad y crecimiento.</li>
                            <li>Consolidarnos como una <strong>empresa chilena de referencia en el sector automotriz</strong> en calidad, tecnología, infraestructura, capital humano, rentabilidad y solidez financiera.</li>
                            <li>Exceder las expectativas de clientes, empleados y proveedores, creando relaciones duraderas basadas en la confianza y el valor agregado.</li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-6 mb-4 order-lg-1">
                    <img src="https://images.unsplash.com/photo-1449824913935-59a10b8d2000?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2070&q=80"
                         class="company-image"
                         alt="Visión empresarial - Liderazgo en el sector automotriz chileno"
                         onerror="this.src='https://via.placeholder.com/600x300/6c757d/ffffff?text=Nuestra+Vision'">
                </div>
            </div>
        </section>

        <!-- Sección de Contacto -->
        @include('fragm.contacto')
    </div>

    <!-- Footer -->
    @include('fragm.footer')
@endsection
