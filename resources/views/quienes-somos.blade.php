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

                            // Agregar animaciones específicas por clase
                            if (entry.target.classList.contains('slide-left')) {
                                entry.target.classList.add('slide-in-left');
                            }
                            if (entry.target.classList.contains('slide-right')) {
                                entry.target.classList.add('slide-in-right');
                            }
                            if (entry.target.classList.contains('fade-up')) {
                                entry.target.classList.add('fade-in-up');
                            }
                            if (entry.target.classList.contains('scale-up')) {
                                entry.target.classList.add('scale-in');
                            }
                        }
                    });
                }, { threshold: 0.1 });

                // Observar todas las secciones animadas
                document.querySelectorAll('.section-animate, .slide-left, .slide-right, .fade-up').forEach(section => {
                    observer.observe(section);
                });
            } else {
                // Fallback para navegadores que no soportan Intersection Observer
                document.querySelectorAll('.section-animate').forEach(section => {
                    section.classList.add('animate-in');
                });
            }

            // Animar estadísticas con contador
            animarContadores();
        }

        /**
         * Anima los contadores de estadísticas
         */
        function animarContadores() {
            const contadores = document.querySelectorAll('.stat-number');

            contadores.forEach(contador => {
                const valorFinal = contador.textContent.replace(/[^\d]/g, '');
                const duracion = 2000; // 2 segundos
                const incremento = valorFinal / (duracion / 50);
                let valorActual = 0;

                const timer = setInterval(() => {
                    valorActual += incremento;
                    if (valorActual >= valorFinal) {
                        contador.textContent = valorFinal + '+';
                        clearInterval(timer);
                    } else {
                        contador.textContent = Math.floor(valorActual) + '+';
                    }
                }, 50);
            });
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
        /* Ajuste para header fijo */
        .container-fluid {
            /* padding-top: 20px; */
            position: relative;
            z-index: 1;
        }

        /* Variables CSS para colores corporativos */
        :root {
            --primary-color: #1a365d;
            --secondary-color: #2c5aa0;
            --accent-color: #3182ce;
            --text-dark: #2d3748;
            --text-light: #718096;
            --bg-light: #f7fafc;
            --gradient-primary: linear-gradient(135deg,  #00bb62 0%,  #99ebc3 50%, #f0f9ff 100%);
            --shadow-light: 0 4px 6px rgba(0, 0, 0, 0.07);
            --shadow-medium: 0 10px 25px rgba(0, 0, 0, 0.1);
            --shadow-heavy: 0 20px 40px rgba(0, 0, 0, 0.15);
        }

        /* Reset y base */
        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: var(--text-dark);
        }

        /* Animaciones de entrada mejoradas */
        .section-animate {
            opacity: 0;
            transform: translateY(50px);
            transition: all 0.8s cubic-bezier(0.25, 0.46, 0.45, 0.94);
        }

        .section-animate.animate-in {
            opacity: 1;
            transform: translateY(0);
        }

        /* Animaciones direccionales específicas */
        .slide-left {
            transform: translateX(-50px);
            opacity: 0;
            transition: all 0.8s cubic-bezier(0.25, 0.46, 0.45, 0.94);
        }

        .slide-left.animate-in,
        .slide-left.slide-in-left {
            transform: translateX(0);
            opacity: 1;
        }

        .slide-right {
            transform: translateX(50px);
            opacity: 0;
            transition: all 0.8s cubic-bezier(0.25, 0.46, 0.45, 0.94);
        }

        .slide-right.animate-in,
        .slide-right.slide-in-right {
            transform: translateX(0);
            opacity: 1;
        }

        .fade-up {
            transform: translateY(30px);
            opacity: 0;
            transition: all 0.8s cubic-bezier(0.25, 0.46, 0.45, 0.94);
        }

        .fade-up.animate-in,
        .fade-up.fade-in-up {
            transform: translateY(0);
            opacity: 1;
        }

        .scale-up {
            transform: scale(0.9);
            opacity: 0;
            transition: all 0.6s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        .scale-up.animate-in {
            transform: scale(1);
            opacity: 1;
        }

        /* Delays para animaciones escalonadas */
        .scale-up[data-delay="100"] { transition-delay: 0.1s; }
        .scale-up[data-delay="200"] { transition-delay: 0.2s; }
        .scale-up[data-delay="300"] { transition-delay: 0.3s; }
        .scale-up[data-delay="400"] { transition-delay: 0.4s; }

        /* Hero section con gradiente mejorado */
        .hero-section {
            background: linear-gradient(135deg, #0f3440 0%, #174c5b 55%, #b95d22 100%);
            color: white;
            padding: 4rem 0;
            margin-bottom: 0;
            position: relative;
            overflow: hidden;
            min-height: 430px;
            display: flex;
            align-items: center;
        }

        .hero-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background:
                radial-gradient(circle at 20% 20%, rgba(255,255,255,0.1) 0%, transparent 50%),
                radial-gradient(circle at 80% 80%, rgba(255,255,255,0.1) 0%, transparent 50%),
                radial-gradient(circle at 40% 40%, rgba(255,255,255,0.05) 0%, transparent 50%);
        }

        .hero-section::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 100px;
            background: linear-gradient(180deg, transparent 0%, rgba(255,255,255,0.1) 100%);
        }

        .hero-content {
            position: relative;
            z-index: 2;
        }

        .hero-section h1 {
            font-size: 4rem;
            font-weight: 800;
            margin-bottom: 1.5rem;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.2);
            letter-spacing: -1px;
        }

        .hero-section .lead {
            font-size: 1.5rem;
            font-weight: 300;
            opacity: 0.95;
            margin-bottom: 2rem;
        }

        .hero-section .h5 {
            font-size: 1.2rem;
            font-weight: 400;
            opacity: 0.9;
            line-height: 1.7;
        }

        /* Breadcrumb mejorado */
        .breadcrumb {
            background: transparent;
            padding: 1rem 0;
            margin-top: 0;
            margin-bottom: 2rem;
        }

        .breadcrumb-item a {
            color: var(--accent-color);
            text-decoration: none;
            font-weight: 500;
        }

        .breadcrumb-item a:hover {
            color: var(--primary-color);
            text-decoration: underline;
        }

        /* Estadísticas mejoradas */
        .stats-section {
            /* background: var(--gradient-primary); */
            color: #935757;
            padding: 4rem 0;
            margin: 0;
            position: relative;
            overflow: hidden;
        }

        .stats-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="white" opacity="0.1"/><circle cx="75" cy="75" r="1" fill="white" opacity="0.1"/><circle cx="75" cy="25" r="0.5" fill="white" opacity="0.1"/><circle cx="25" cy="75" r="0.5" fill="white" opacity="0.1"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
        }

        .stat-item {
            text-align: center;
            padding: 2rem 1rem;
            position: relative;
            z-index: 2;
        }

        .stat-number {
            font-size: 3.5rem;
            font-weight: 800;
            margin-bottom: 0.5rem;
            background: linear-gradient(45deg, #ffffff, #e2e8f0);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            text-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .stat-label {
            font-size: 1.1rem;
            opacity: 0.95;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        /* Secciones corporativas mejoradas */
        .company-section {
            padding: 6rem 0;
            position: relative;
            background: white;
        }

        .company-section:nth-child(even) {
            background: white;
        }

        .company-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 4px;
            background: var(--gradient-primary);
            border-radius: 2px;
        }

        .section-title {
            color: var(--primary-color);
            font-weight: 800;
            font-size: 3rem;
            margin-bottom: 3rem;
            position: relative;
            text-align: center;
            line-height: 1.2;
        }

        .section-subtitle {
            color: var(--secondary-color);
            font-weight: 700;
            font-size: 1rem;
            text-transform: uppercase;
            letter-spacing: 3px;
            margin-bottom: 1rem;
            text-align: center;
            position: relative;
        }

        .section-subtitle::after {
            content: '';
            position: absolute;
            bottom: -8px;
            left: 50%;
            transform: translateX(-50%);
            width: 40px;
            height: 2px;
            background: var(--accent-color);
        }

        /* Contenido corporativo mejorado */
        .corporate-content {
            font-size: 1.15rem;
            line-height: 1.8;
            color: var(--text-dark);
            text-align: justify;
        }

        .corporate-content p {
            margin-bottom: 1.8rem;
            font-weight: 400;
        }

        .corporate-content strong {
            color: var(--secondary-color);
            font-weight: 700;
        }

        .corporate-content .highlight {
            background: linear-gradient(120deg, rgba(49, 130, 206, 0.15) 0%, rgba(49, 130, 206, 0.05) 100%);
            padding: 0.3rem 0.6rem;
            border-radius: 6px;
            border-left: 4px solid var(--accent-color);
            font-weight: 600;
            display: inline-block;
        }

        /* Imágenes profesionales mejoradas */
        .professional-image-container {
            position: relative;
            overflow: hidden;
            border-radius: 20px;
            box-shadow: var(--shadow-heavy);
            background: #fff;
            padding: 12px;
            transition: all 0.4s ease;
        }

        .professional-image-container:hover {
            transform: translateY(-10px);
            box-shadow: 0 30px 60px rgba(0, 0, 0, 0.2);
        }

        .professional-image {
            width: 100%;
            height: 450px;
            object-fit: cover;
            border-radius: 15px;
            transition: all 0.4s ease;
            filter: brightness(1.05) contrast(1.1) saturate(1.1);
        }

        .professional-image-container:hover .professional-image {
            transform: scale(1.03);
            filter: brightness(1.1) contrast(1.15) saturate(1.2);
        }

        .image-overlay {
            position: absolute;
            top: 12px;
            left: 12px;
            right: 12px;
            bottom: 12px;
            background: linear-gradient(45deg, rgba(26, 54, 93, 0.85), rgba(44, 90, 160, 0.7));
            border-radius: 15px;
            opacity: 0;
            transition: all 0.4s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 1.2rem;
            text-align: center;
            padding: 2rem;
        }

        .professional-image-container:hover .image-overlay {
            opacity: 1;
        }

        /* Lista corporativa mejorada */
        .corporate-list {
            list-style: none;
            padding: 0;
            margin: 2rem 0;
        }

        .corporate-list li {
            position: relative;
            padding: 1.5rem 0 1.5rem 4rem;
            margin-bottom: 1.5rem;
            background: rgba(255, 255, 255, 0.9);
            border-radius: 12px;
            box-shadow: var(--shadow-light);
            border: 1px solid rgba(49, 130, 206, 0.1);
            transition: all 0.3s ease;
            font-size: 1.1rem;
            line-height: 1.7;
        }

        .corporate-list li:hover {
            transform: translateX(15px);
            box-shadow: var(--shadow-medium);
            border-color: var(--accent-color);
        }

        .corporate-list li::before {
            content: '✓';
            position: absolute;
            left: 1.5rem;
            top: 50%;
            transform: translateY(-50%);
            background: var(--gradient-primary);
            color: white;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 1rem;
            box-shadow: var(--shadow-light);
        }

        /* Cards corporativas mejoradas */
        .corporate-card {
            background: white;
            border-radius: 20px;
            padding: 3rem 2rem;
            box-shadow: var(--shadow-medium);
            border: 1px solid rgba(49, 130, 206, 0.1);
            transition: all 0.4s ease;
            height: 100%;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .corporate-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--gradient-primary);
        }

        .corporate-card:hover {
            transform: translateY(-15px);
            box-shadow: var(--shadow-heavy);
            border-color: var(--accent-color);
        }

        .card-icon {
            width: 80px;
            height: 80px;
            background: var(--gradient-primary);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 2rem;
            margin: 0 auto 2rem;
            box-shadow: var(--shadow-medium);
            transition: all 0.3s ease;
        }

        .corporate-card:hover .card-icon {
            transform: scale(1.1);
            box-shadow: var(--shadow-heavy);
        }

        .corporate-card h4 {
            color: var(--primary-color);
            font-weight: 700;
            margin-bottom: 1.5rem;
            font-size: 1.3rem;
        }

        .corporate-card p {
            color: var(--text-light);
            line-height: 1.7;
            font-size: 1rem;
        }

        /* Responsive mejorado */
        @media (max-width: 768px) {
            .hero-section {
                padding: 4rem 0;
                min-height: 60vh;
            }

            .hero-section h1 {
                font-size: 2.5rem;
            }

            .section-title {
                font-size: 2.2rem;
            }

            .company-section {
                padding: 4rem 0;
            }

            .professional-image {
                height: 350px;
            }

            .corporate-list li {
                padding: 1rem 0 1rem 3rem;
                font-size: 1rem;
            }

            .corporate-list li::before {
                width: 28px;
                height: 28px;
                left: 1rem;
            }

            .stat-number {
                font-size: 2.8rem;
            }

            .corporate-card {
                padding: 2rem 1.5rem;
                margin-bottom: 2rem;
            }

            .card-icon {
                width: 70px;
                height: 70px;
                font-size: 1.8rem;
            }
        }

        @media (max-width: 576px) {
            .hero-section h1 {
                font-size: 2rem;
            }

            .hero-section .lead {
                font-size: 1.2rem;
            }

            .section-title {
                font-size: 1.8rem;
            }

            .professional-image {
                height: 300px;
            }

            .stats-section {
                padding: 3rem 0;
            }

            .stat-number {
                font-size: 2.5rem;
            }

            .stat-label {
                font-size: 1rem;
            }
        }

        /* Animaciones adicionales */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(40px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes slideInFromLeft {
            from {
                opacity: 0;
                transform: translateX(-50px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes slideInFromRight {
            from {
                opacity: 0;
                transform: translateX(50px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .fade-in-up {
            animation: fadeInUp 0.8s ease-out;
        }

        .slide-in-left {
            animation: slideInFromLeft 0.8s ease-out;
        }

        .slide-in-right {
            animation: slideInFromRight 0.8s ease-out;
        }

        /* Mejoras en formulario */
        .form-control:focus {
            border-color: var(--accent-color);
            box-shadow: 0 0 0 0.2rem rgba(49, 130, 206, 0.25);
        }

        .btn-enviar {
            background: var(--gradient-primary);
            border: none;
            padding: 15px 35px;
            font-weight: 600;
            transition: all 0.3s ease;
            border-radius: 8px;
            font-size: 1.1rem;
        }

        .btn-enviar:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow-medium);
        }

        /* Utilidades */
        .text-gradient {
            background: var(--gradient-primary);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .bg-gradient {
            background: var(--gradient-primary);
        }

        .shadow-custom {
            box-shadow: var(--shadow-medium);
        }
    </style>
@endsection

@section('content')
    <div class="container-fluid">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb">
            <div class="container">
                <ol class="breadcrumb bg-transparent">
                    <li class="breadcrumb-item"><a href="/">Inicio</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Quiénes somos</li>
                </ol>
            </div>
        </nav>
        <!-- Hero Section -->
        <section class="hero-section">
            <div class="container">
                <div class="hero-content text-center">
                    <img src="{{ asset('assets/images/logo_ips2025.png') }}" alt="IPS Chile - Logo Corporativo" class="mb-4" style="max-width: 200px;">
                    <p class="lead mb-4">Importadores de productos, repuestos y accesorios para distintas necesidades</p>
                    <div class="row justify-content-center">
                        <div class="col-md-8">
                            <p class="h5 font-weight-light">Más de 10 años acercando variedad, calidad y atención especializada a nuestros clientes en Chile</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>



        <!-- Estadísticas -->
        <section class="stats-section">
            <div class="container">
                <div class="row">
                    <div class="col-md-3 col-6">
                        <div class="stat-item">
                            <div class="stat-number">10+</div>
                            <div class="stat-label">Años de Experiencia</div>
                        </div>
                    </div>
                    <div class="col-md-3 col-6">
                        <div class="stat-item">
                            <div class="stat-number">5000+</div>
                            <div class="stat-label">Clientes Satisfechos</div>
                        </div>
                    </div>
                    <div class="col-md-3 col-6">
                        <div class="stat-item">
                            <div class="stat-number">15000+</div>
                            <div class="stat-label">Productos en Stock</div>
                        </div>
                    </div>
                    <div class="col-md-3 col-6">
                        <div class="stat-item">
                            <div class="stat-number">100+</div>
                            <div class="stat-label">Marcas Disponibles</div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <div class="container">
            <!-- Sección: Quienes Somos -->
            <section class="company-section section-animate fade-up">
                <div class="row align-items-center">
                    <div class="col-lg-6 mb-4 slide-left">
                        <div class="section-subtitle">Nuestra Historia</div>
                        <h2 class="section-title">¿Quiénes somos?</h2>
                        <div class="corporate-content">
                            <p>
                                Somos <span class="highlight"><strong>IPS Chile</strong></span>, una empresa importadora y comercializadora con más de 10 años de experiencia en el mercado chileno.
                            </p>
                            <p>
                                Nuestro catálogo reúne <strong>repuestos, accesorios, herramientas y productos de distintas categorías</strong>, seleccionados para entregar variedad, calidad y precios competitivos tanto a clientes particulares como a empresas.
                            </p>
                            <p>
                                Nuestro compromiso es ser un <strong>socio comercial confiable</strong>, facilitando el acceso a productos útiles y soluciones oportunas, con una atención cercana que acompaña cada compra.
                            </p>
                        </div>
                    </div>
                    <div class="col-lg-6 mb-4 slide-right">
                        <div class="professional-image-container">
                            <img src="https://images.unsplash.com/photo-1600880292203-757bb62b4baf?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2070&q=80"
                                 class="professional-image"
                                 alt="Equipo profesional de IPS Chile"
                                 onerror="this.src='https://via.placeholder.com/600x400/1a365d/ffffff?text=Equipo+Profesional'">
                            <div class="image-overlay">
                                <div>Un equipo dedicado a nuestros clientes</div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Sección: Nuestra Misión -->
            <section class="company-section section-animate fade-up">
                <div class="row align-items-center">
                    <div class="col-lg-6 mb-4 order-lg-2 slide-right">
                        <div class="section-subtitle">Nuestro Propósito</div>
                        <h2 class="section-title">Nuestra Misión</h2>
                        <div class="corporate-content">
                            <ul class="corporate-list">
                                <li>Importar y comercializar una oferta diversa de productos, repuestos y accesorios que responda a las necesidades reales de nuestros clientes.</li>
                                <li>Entregar una experiencia de compra simple, confiable y cercana, respaldada por un equipo preparado y canales de atención eficientes.</li>
                                <li>Trabajar responsablemente con proveedores, colaboradores y clientes, cuidando la calidad de nuestra oferta y construyendo relaciones de largo plazo.</li>
                            </ul>
                            <p>
                                Nuestra misión está orientada a la <span class="highlight"><strong>excelencia operacional</strong></span>, la satisfacción de nuestros clientes y el crecimiento responsable de nuestro equipo y nuestra red comercial.
                            </p>
                        </div>
                    </div>
                    <div class="col-lg-6 mb-4 order-lg-1 slide-left">
                        <div class="professional-image-container">
                            <img src="https://images.unsplash.com/photo-1521790797524-b2497295b8a0?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2069&q=80"
                                 class="professional-image"
                                 alt="Misión corporativa - Compromiso con la excelencia y calidad"
                                 onerror="this.src='https://via.placeholder.com/600x400/2c5aa0/ffffff?text=Nuestra+Mision'">
                            <div class="image-overlay">
                                <div>Compromiso con la Excelencia</div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Sección: Qué Hacemos -->
            <section class="company-section section-animate" id="como-comprar">
                <div class="row">
                    <div class="col-12 text-center mb-5">
                        <div class="section-subtitle">Nuestros Servicios</div>
                        <h2 class="section-title">¿Qué hacemos?</h2>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-4 mb-4">
                        <div class="corporate-card">
                            <div class="card-icon">
                                <i class="fas fa-cogs"></i>
                            </div>
                            <h4 class="h5 mb-3" style="color: var(--primary-color);">Productos y Repuestos</h4>
                            <p>Un catálogo variado de repuestos, accesorios, herramientas y productos importados para distintas necesidades.</p>
                        </div>
                    </div>
                    <div class="col-lg-4 mb-4">
                        <div class="corporate-card">
                            <div class="card-icon">
                                <i class="fas fa-user-tie"></i>
                            </div>
                            <h4 class="h5 mb-3" style="color: var(--primary-color);">Asesoría Especializada</h4>
                            <p>Nuestro equipo brinda orientación para ayudarte a identificar el producto adecuado y resolver tus dudas antes de comprar.</p>
                        </div>
                    </div>
                    <div class="col-lg-4 mb-4">
                        <div class="corporate-card">
                            <div class="card-icon">
                                <i class="fas fa-award"></i>
                            </div>
                            <h4 class="h5 mb-3" style="color: var(--primary-color);">Garantía de Calidad</h4>
                            <p>Seleccionamos cuidadosamente nuestra oferta y entregamos atención personalizada en cada etapa de la compra.</p>
                        </div>
                    </div>
                </div>
                <div class="row align-items-center mt-5">
                    <div class="col-lg-6 mb-4">
                        <div class="corporate-content">
                            <p>
                                Nos dedicamos a la <span class="highlight"><strong>importación y comercialización de productos, repuestos y accesorios</strong></span>. Nuestro catálogo incorpora alternativas para distintas aplicaciones, categorías y tipos de clientes.
                            </p>
                            <p>
                                Nuestro equipo conoce el catálogo y está preparado para entregar una <strong>orientación clara y personalizada</strong>, ayudando a cada cliente a tomar una mejor decisión de compra.
                            </p>
                        </div>
                    </div>
                    <div class="col-lg-6 mb-4">
                        <div class="professional-image-container">
                            <img src="https://images.unsplash.com/photo-1619642751034-765dfdf7c58e?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1974&q=80"
                                 class="professional-image"
                                 alt="Organización y gestión del catálogo de productos IPS Chile"
                                 onerror="this.src='https://via.placeholder.com/600x400/dc3545/ffffff?text=Nuestras+Instalaciones'">
                            <div class="image-overlay">
                                <div>Instalaciones Modernas y Organizadas</div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Sección: Nuestra Visión -->
            <section class="company-section section-animate fade-up">
                <div class="row align-items-center">
                    <div class="col-lg-6 mb-4 order-lg-2 slide-right">
                        <div class="section-subtitle">Nuestro Futuro</div>
                        <h2 class="section-title">Nuestra Visión</h2>
                        <div class="corporate-content">
                            <ul class="corporate-list">
                                <li>Ser una <strong>empresa referente en importación y comercialización</strong>, reconocida por la variedad de su catálogo, la calidad de su servicio y su capacidad para responder a nuevas necesidades.</li>
                                <li>Consolidarnos como una <strong>empresa chilena con proyección nacional e internacional</strong>, apoyada en tecnología, infraestructura moderna, un equipo comprometido y relaciones comerciales sólidas.</li>
                                <li>Exceder consistentemente las expectativas de clientes, empleados y proveedores, creando relaciones comerciales duraderas basadas en la confianza mutua y el valor agregado diferencial.</li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-lg-6 mb-4 order-lg-1 slide-left">
                        <div class="professional-image-container">
                            <img src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1974&q=80"
                                 class="professional-image"
                                 alt="Visión empresarial de IPS Chile y su proyección comercial"
                                 onerror="this.src='https://via.placeholder.com/600x400/6c757d/ffffff?text=Nuestra+Vision'">
                            <div class="image-overlay">
                                <div>Proyección y Liderazgo Sectorial</div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Sección: Estadísticas -->
            {{-- <section class="stats-section section-animate fade-up">
                <div class="row text-center">
                    <div class="col-md-3 mb-4 scale-up" data-delay="100">
                        <div class="stat-card">
                            <div class="stat-icon">
                                <i class="fas fa-calendar-alt"></i>
                            </div>
                            <div class="stat-number" data-count="10">0</div>
                            <div class="stat-label">Años de Experiencia</div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-4 scale-up" data-delay="200">
                        <div class="stat-card">
                            <div class="stat-icon">
                                <i class="fas fa-users"></i>
                            </div>
                            <div class="stat-number" data-count="5000">0</div>
                            <div class="stat-label">Clientes Satisfechos</div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-4 scale-up" data-delay="300">
                        <div class="stat-card">
                            <div class="stat-icon">
                                <i class="fas fa-cogs"></i>
                            </div>
                            <div class="stat-number" data-count="15000">0</div>
                            <div class="stat-label">Productos en Catálogo</div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-4 scale-up" data-delay="400">
                        <div class="stat-card">
                            <div class="stat-icon">
                                <i class="fas fa-star"></i>
                            </div>
                            <div class="stat-number" data-count="98">0</div>
                            <div class="stat-label">% Satisfacción</div>
                        </div>
                    </div>
                </div>
            </section> --}}
        </div>

        <!-- Sección de Contacto -->
        <div class="container" id="contacto">
 @include('fragm.contacto')
        </div>

    </div>

    <!-- Footer -->
    @include('fragm.footer')
@endsection
