<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="api-base-url" content="{{ config('app.api_base_url', 'https://intranet.ipschile.cl') }}">
    <meta name="app-env" content="{{ config('app.env') }}">

    <title>IPS Autopartes</title>
    <link rel="icon" href="/favicon.png?v=2" type="image/png">

    <!-- Preconnect to external domains for better performance -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://cdn.jsdelivr.net">
    <link rel="preconnect" href="https://cdnjs.cloudflare.com">

    <!-- DNS Prefetch for API domain -->
    <link rel="dns-prefetch" href="//intranet.ipschile.cl">

    <!-- FONTS -->
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">

    <!-- CRITICAL CSS - Load first -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

    <!-- ICONS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.2/css/all.min.css"
        integrity="sha512-1sCRPdkRXhBV2PBLUdRb4tMg1w2YPf37qatUFeS7zlBy7jJI8Lf4VHwWfZZfpXtYSLy85pkm9GaYVYMfw5BC1A=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- CUSTOM CSS -->
    <link href="{{ asset('css/web.css') }}" rel="stylesheet">
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">

    <!-- THIRD PARTY CSS -->
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link rel="stylesheet" href="//code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/paginationjs/2.1.4/pagination.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/nprogress/0.2.0/nprogress.min.css">

    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.dataTables.min.css">

    <!-- Custom Inline Styles -->
    <style>
        /* Carrito Sidebar - Más ancho */
        #offcanvasRight {
            width: 70% !important;
            max-width: 900px;
        }

        @media (max-width: 768px) {
            #offcanvasRight {
                width: 95% !important;
            }
        }

        /* Loading overlay */
        .loading-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.9);
            z-index: 9999;
        }

        /* Modal improvements */
        #modalVistaRapida .modal-dialog {
            max-width: 90%;
        }

        #modalVistaRapida .carousel-inner img {
            border-radius: 8px;
            transition: transform 0.18s ease;
        }

        #modalVistaRapida .img-thumbnail {
            transition: all 0.3s ease;
            cursor: pointer;
        }

        #modalVistaRapida .img-thumbnail:hover {
            transform: scale(1.1);
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }

        #modalVistaRapida .table td {
            padding: 0.75rem 0.5rem;
            border: none;
        }

        #modalVistaRapida .table td:first-child {
            background-color: #f8f9fa;
            border-radius: 4px;
            margin-bottom: 2px;
        }

        #modalVistaRapida .input-group {
            max-width: 200px;
        }

        #modalVistaRapida .carousel-control-prev,
        #modalVistaRapida .carousel-control-next {
            background: rgba(0, 0, 0, 0.1);
            border-radius: 4px;
            width: 40px;
            height: 40px;
            top: 50%;
            transform: translateY(-50%);
        }

        #modalVistaRapida .carousel-control-prev {
            left: 10px;
        }

        #modalVistaRapida .carousel-control-next {
            right: 10px;
        }

        #modalVistaRapida .badge {
            font-size: 0.85em;
            padding: 0.5em 0.75em;
        }

        #modalVistaRapida .card {
            border: 1px solid #e3e6f0;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
        }

        .product-rating{display:grid;grid-template-columns:auto 1fr;align-items:center;gap:.35rem 1rem;padding:1rem 1.15rem;border:1px solid #e0e7e9;border-radius:12px;background:#f8fafb}.product-rating__summary{display:flex;align-items:baseline;gap:.45rem}.product-rating__summary strong{color:#173f4d;font-size:1.45rem;line-height:1}.product-rating__summary span{color:#6c7b81;font-size:.78rem}.product-rating__stars{display:flex;gap:.15rem}.product-rating__star{padding:.15rem;border:0;background:transparent;color:#d5dde0;font-size:1.35rem;line-height:1;transition:color .15s,transform .15s}.product-rating__star:not(.product-rating__star--readonly):hover,.product-rating__star:focus-visible,.product-rating__star.is-selected{color:#f1a41c;transform:translateY(-1px)}.product-rating__stars:has(button):hover .product-rating__star{color:#f1a41c}.product-rating__star:not(.product-rating__star--readonly):hover~.product-rating__star{color:#d5dde0}.product-rating__star--readonly:has(.fas){color:#f1a41c}.product-rating>small{grid-column:1/-1;color:#6c7b81}.product-rating--dark{margin-top:1rem;border-color:rgba(255,255,255,.15);background:rgba(255,255,255,.08)}.product-rating--dark .product-rating__summary strong{color:#fff}.product-rating--dark .product-rating__summary span,.product-rating--dark>small{color:rgba(255,255,255,.7)}

        /* Zoom de producto compartido por vista rápida y detalle */
        .product-zoom-frame {
            position: relative;
            height: 100%;
            overflow: hidden;
            background: #f8f9fa;
            cursor: zoom-in;
            touch-action: pan-y;
        }

        .product-zoom-frame::after {
            content: '\f00e  Acercar';
            position: absolute;
            right: 12px;
            bottom: 12px;
            z-index: 3;
            padding: 7px 11px;
            border-radius: 999px;
            background: rgba(16, 47, 58, .84);
            color: #fff;
            font: 600 .75rem/1 "Font Awesome 6 Free", Nunito, sans-serif;
            letter-spacing: .01em;
            pointer-events: none;
            box-shadow: 0 4px 14px rgba(0, 0, 0, .18);
        }

        .product-zoom-image {
            transform-origin: var(--zoom-x, 50%) var(--zoom-y, 50%);
            will-change: transform;
        }

        @media (hover: hover) and (pointer: fine) {
            .product-zoom-frame:hover .product-zoom-image {
                transform: scale(2.35);
            }
        }

        .product-lightbox {
            position: fixed;
            inset: 0;
            z-index: 1090;
            display: none;
            align-items: center;
            justify-content: center;
            padding: 28px;
            background: rgba(8, 20, 25, .94);
        }

        .product-lightbox.is-open { display: flex; }
        .product-lightbox__stage {
            width: min(1120px, 100%);
            height: min(82vh, 820px);
            overflow: hidden;
            cursor: zoom-in;
            touch-action: none;
        }
        .product-lightbox__image {
            width: 100%;
            height: 100%;
            object-fit: contain;
            transform: scale(var(--lightbox-scale, 1));
            transform-origin: var(--lightbox-x, 50%) var(--lightbox-y, 50%);
            transition: transform .16s ease;
            user-select: none;
            -webkit-user-drag: none;
        }
        .product-lightbox__close,
        .product-lightbox__tools {
            position: absolute;
            z-index: 2;
        }
        .product-lightbox__close { top: 18px; right: 20px; }
        .product-lightbox__tools { left: 50%; bottom: 20px; transform: translateX(-50%); }
        .product-lightbox .btn { box-shadow: 0 4px 16px rgba(0,0,0,.25); }

        @media (max-width: 767px) {
            .product-zoom-frame::after { content: '\f00e'; padding: 9px; }
            .product-lightbox { padding: 58px 12px 70px; }
            .product-lightbox__stage { height: 75vh; }
        }

        @media (max-width: 768px) {
            #modalVistaRapida .modal-dialog {
                max-width: 95%;
                margin: 0.5rem;
            }

            #modalVistaRapida .carousel-inner {
                height: 250px !important;
            }

            #modalVistaRapida .col-md-5,
            #modalVistaRapida .col-md-7 {
                margin-bottom: 1rem;
            }
        }

        /* Error states */
        .error-state {
            padding: 2rem;
            text-align: center;
            color: #6c757d;
        }

        /* Cart loading */
        .cart-loading {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 200px;
        }

        /* Botón de WhatsApp flotante */
        .whatsapp-float {
            position: fixed;
            bottom: 20px;
            right: 20px;
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, #25d366 0%, #128c7e 100%);
            color: #ffffff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 32px;
            box-shadow: 0 4px 15px rgba(37, 211, 102, 0.4);
            z-index: 1000;
            transition: all 0.3s ease;
            text-decoration: none;
            animation: pulse-whatsapp 2s infinite;
        }

        .whatsapp-float:hover {
            transform: scale(1.1) translateY(-3px);
            box-shadow: 0 6px 20px rgba(37, 211, 102, 0.6);
            background: linear-gradient(135deg, #128c7e 0%, #25d366 100%);
            color: #ffffff;
        }

        .whatsapp-float i {
            animation: shake 1s ease-in-out infinite;
        }

        @keyframes pulse-whatsapp {
            0%, 100% {
                box-shadow: 0 4px 15px rgba(37, 211, 102, 0.4);
            }
            50% {
                box-shadow: 0 4px 25px rgba(37, 211, 102, 0.7);
            }
        }

        @keyframes shake {
            0%, 100% {
                transform: rotate(0deg);
            }
            10%, 30%, 50%, 70%, 90% {
                transform: rotate(-10deg);
            }
            20%, 40%, 60%, 80% {
                transform: rotate(10deg);
            }
        }

        /* Responsive - ajustar posición en móviles */
        @media (max-width: 768px) {
            .whatsapp-float {
                width: 50px;
                height: 50px;
                font-size: 26px;
                bottom: 15px;
                left: 15px;
            }
        }

        @media (max-width: 576px) {
            .whatsapp-float {
                width: 45px;
                height: 45px;
                font-size: 24px;
                bottom: 10px;
                left: 10px;
            }
        }
    </style>

    @yield('styles')
</head>

<body>
    <!-- Loading Overlay -->
    <div class="loading-overlay" id="globalLoadingOverlay">
        <div class="d-flex justify-content-center align-items-center h-100">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Cargando...</span>
            </div>
        </div>
    </div>

    @include('fragm.header')

    <!-- Botón de WhatsApp fijo -->
    <a href="https://wa.me/56975159323?text=Hola,%20necesito%20información"
       target="_blank"
       class="whatsapp-float"
       id="whatsapp-button"
       aria-label="Contactar por WhatsApp">
        <i class="fab fa-whatsapp"></i>
    </a>

    <div class="main">
        @yield('content')
    </div>

    {{-- @include('fragm.footer') --}}

    <!-- Hidden inputs for configuration -->
    <input type="hidden" id="id_usuario" value="">
    <input type="hidden" name="id_vendedor" id="id_vendedor" value="">

    <!-- CORE JAVASCRIPT LIBRARIES -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"
        integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>

    <!-- Bootstrap and Popper -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"
        integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js"
        integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous"></script>

    <!-- THIRD PARTY LIBRARIES -->
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/paginationjs/2.1.4/pagination.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/nprogress/0.2.0/nprogress.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>

    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script>
        // Idioma predeterminado para todas las tablas DataTables del sitio.
        // Las vistas pueden extender esta configuracion sin volver al ingles.
        $.extend(true, $.fn.dataTable.defaults, {
            language: {
                url: '/assets/datatables/es-ES.json',
                paginate: {
                    first: 'Primero',
                    last: 'Último',
                    next: 'Siguiente',
                    previous: 'Anterior'
                }
            }
        });
    </script>

    <!-- CUSTOM JAVASCRIPT -->
    <script src="{{ asset('js/main.js') }}"></script>

    <!-- APPLICATION CORE SCRIPT -->
    <script>
        /**
         * IPS Autopartes Application Core
         * Refactored for better performance, maintainability and security
         */

        // Application Configuration
        const IPSApp = {
            config: {
                apiBaseUrl: document.querySelector('meta[name="api-base-url"]')?.content || 'https://intranet.ipschile.cl',
                csrfToken: document.querySelector('meta[name="csrf-token"]')?.content,
                environment: document.querySelector('meta[name="app-env"]')?.content || 'production',
                endpoints: {
                    getUser: '/api/get-user',
                    revisarCarrito: '/api/revisar_carrito',
                    agregarCarrito: "{{ env('URL_AGREGAR_CARRITO') }}",
                    login: '/api/login',
                    repuesto: '/api/{id}/repuesto'
                }
            },

            // State management
            state: {
                user: null,
                cart: [],
                cartItems: [],
                numeroCarrito: null,
                isLoading: false,
                isAddingToCart: false,
                cartReviewRequest: null,
                cartReviewVersion: 0,
                quantityUpdates: {}
            },

            // Utility functions
            utils: {
                showLoading: function(message = 'Cargando...') {
                    IPSApp.state.isLoading = true;
                    $('#globalLoadingOverlay').show();
                },

                hideLoading: function() {
                    IPSApp.state.isLoading = false;
                    $('#globalLoadingOverlay').hide();
                },

                formatPrice: function(price) {
                    return new Intl.NumberFormat('es-CL', {
                        style: 'currency',
                        currency: 'CLP',
                        minimumFractionDigits: 0
                    }).format(price);
                },

                commaSeparateNumber: function(val) {
                    return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                },

                debounce: function(func, wait) {
                    let timeout;
                    return function executedFunction(...args) {
                        const later = () => {
                            clearTimeout(timeout);
                            func(...args);
                        };
                        clearTimeout(timeout);
                        timeout = setTimeout(later, wait);
                    };
                },

                getToken: function() {
                    return localStorage.getItem('token');
                },

                isTokenValid: function() {
                    const token = this.getToken();
                    if (!token) return false;

                    try {
                        const payload = JSON.parse(atob(token.split('.')[1]));
                        return payload.exp * 1000 > Date.now();
                    } catch (e) {
                        return false;
                    }
                },

                handleApiError: function(error, customMessage = null) {
                    console.error('API Error:', error);

                    let message = customMessage || 'Ha ocurrido un error. Por favor, intente nuevamente.';
                    const isMoroso = error.responseJSON && error.responseJSON.code === 'CLIENTE_MOROSO';

                    if (error.responseJSON && error.responseJSON.message) {
                        message = error.responseJSON.message;
                    }

                    Swal.fire({
                        icon: isMoroso ? 'warning' : 'error',
                        title: isMoroso ? 'Acceso restringido' : 'Error',
                        text: message
                    });
                }
            },

            // API functions
            api: {
                makeRequest: function(options) {
                    // Construir headers de forma segura
                    const headers = {
                        'X-CSRF-TOKEN': IPSApp.config.csrfToken
                    };

                    const token = IPSApp.utils.getToken();
                    if (token) {
                        headers['Authorization'] = 'Bearer ' + token;
                    }

                    // Combinar con headers adicionales si existen en options
                    if (options.headers) {
                        Object.assign(headers, options.headers);
                    }

                    console.log('makeRequest - URL:', options.url);
                    console.log('makeRequest - Token presente:', token ? 'SÍ (' + token.substring(0, 20) + '...)' : 'NO');
                    console.log('makeRequest - Headers completos:', headers);
                    console.log('makeRequest - Type:', options.type || 'GET');

                    // Construir configuración de AJAX sin $.extend para evitar problemas
                    const ajaxConfig = {
                        url: options.url,
                        type: options.type || 'GET',
                        headers: headers,
                        dataType: 'json'
                    };

                    // Preparar data y token según el método HTTP
                    let requestData = options.data || {};

                    if (token) {
                        if (options.type === 'POST') {
                            // POST: Enviar token en el body
                            requestData = Object.assign({}, requestData, { token: token });
                            console.log('Token enviado en body (POST)');
                        } else {
                            // GET: Enviar token como query parameter
                            requestData = Object.assign({}, requestData, { token: token });
                            console.log('Token enviado como query parameter (GET)');
                        }
                    }

                    if (Object.keys(requestData).length > 0) {
                        ajaxConfig.data = requestData;
                    }

                    // Agregar otras opciones si existen
                    if (options.contentType !== undefined) {
                        ajaxConfig.contentType = options.contentType;
                    }

                    return $.ajax(ajaxConfig);
                },

                getUser: function() {
                    return this.makeRequest({
                        url: IPSApp.config.apiBaseUrl + IPSApp.config.endpoints.getUser,
                        type: 'POST'
                    });
                },

                getCart: function() {
                    return this.makeRequest({
                        url: IPSApp.config.apiBaseUrl + IPSApp.config.endpoints.revisarCarrito,
                        type: 'GET'  // GET con token en query parameter
                    });
                },

                addToCart: function(productId, quantity) {
                    return this.makeRequest({
                        url: IPSApp.config.endpoints.agregarCarrito,
                        type: 'POST',
                        data: {
                            idrep: productId,
                            cantidad: quantity
                        }
                    });
                },

                getProduct: function(productId) {
                    return this.makeRequest({
                        url: IPSApp.config.apiBaseUrl + IPSApp.config.endpoints.repuesto.replace('{id}', productId)
                    });
                },

                login: function(email, password) {
                    return this.makeRequest({
                        url: IPSApp.config.apiBaseUrl + IPSApp.config.endpoints.login,
                        type: 'POST',
                        data: {
                            email: email,
                            password: password
                        }
                    });
                }
            },

            // Authentication functions
            auth: {
                getUser: function() {
                    const token = IPSApp.utils.getToken();
                    console.log('=== GETUSER DEBUG ===');
                    console.log('Token en localStorage:', token ? 'Presente' : 'Ausente');
                    if (token) {
                        console.log('Token (primeros 30 chars):', token.substring(0, 30) + '...');
                    }
                    console.log('Llamando a API getUser...');

                    IPSApp.api.getUser()
                        .done(function(resp) {
                            console.log('=== RESPUESTA GETUSER ===');
                            console.log('Respuesta completa:', resp);
                            if (resp.user) {
                                IPSApp.state.user = resp.user;
                                IPSApp.auth.updateUI(resp.user);

                                // Auto-fill form data if on mis_datos page
                                if (window.location.href.includes('/mis_datos')) {
                                    IPSApp.auth.fillUserForm(resp.user);
                                }
                            } else {
                                IPSApp.auth.showUnauthenticated();
                            }
                        })
                        .fail(function(error) {
                            console.error('=== ERROR GETUSER ===');
                            console.error('Error completo:', error);
                            console.error('Response JSON:', error.responseJSON);
                            console.error('Status HTTP:', error.status);
                            console.error('Status Text:', error.statusText);
                            if (error.responseJSON && error.responseJSON.code === 'CLIENTE_MOROSO') {
                                localStorage.removeItem('token');
                                sessionStorage.clear();
                                IPSApp.utils.handleApiError(error);
                            }
                            IPSApp.auth.showUnauthenticated();
                        });
                },

                updateUI: function(user) {
                    $('#no_autenticado').hide();
                    $('#autenticado').show();
                    $('#contenedor_descuentos').show();
                    $('#contenedor_ofertas').show();

                    // Formatear nombre: Primera letra mayúscula, resto minúsculas
                    const formatName = (name) => {
                        return name.split(' ')
                            .map(word => word.charAt(0).toUpperCase() + word.slice(1).toLowerCase())
                            .slice(0, 2) // Tomar solo nombre y primer apellido
                            .join(' ');
                    };

                    const formattedName = formatName(user.name);
                    const firstName = user.name.split(' ')[0];

                    // Actualizar el span dentro del badge
                    $('#user_name_display').text(formattedName);

                    // Actualizar el saludo en el dropdown con hora del día
                    const hour = new Date().getHours();
                    let greeting = 'Hola';
                    if (hour < 12) greeting = 'Buenos días';
                    else if (hour < 20) greeting = 'Buenas tardes';
                    else greeting = 'Buenas noches';

                    $('#dropdown_user_greeting').text(`${greeting}, ${firstName}`);

                    // Agregar efecto de entrada suave
                    $('#autenticado').hide().fadeIn(600);

                    $('#id_vendedor').val(user.id);
                    $('#id_usuario').val(user.id);
                },

                showUnauthenticated: function() {
                    $('#no_autenticado').show();
                    $('#autenticado').hide();
                    $('#contenedor_descuentos').hide();
                    $('#contenedor_ofertas').hide();
                },

                fillUserForm: function(user) {
                    const elements = {
                        'name': user.name,
                        'email': user.email,
                        'telefono': user.telefono,
                        'id_usuario': user.id
                    };

                    Object.keys(elements).forEach(key => {
                        const element = document.getElementById(key);
                        if (element) {
                            element.value = elements[key] || '';
                        }
                    });
                },

                login: function() {
                    const email = $('#email_').val().trim();
                    const password = $('#password_').val().trim();

                    if (!email || !password) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Campos requeridos',
                            text: 'Por favor ingrese email y contraseña'
                        });
                        return;
                    }

                    IPSApp.utils.showLoading('Iniciando sesión...');

                    IPSApp.api.login(email, password)
                        .done(function(resp) {
                            console.log('Login response:', resp);
                            if (resp.token) {
                                localStorage.setItem('token', resp.token);
                                // Configurar el token para futuras peticiones
                                $.ajaxSetup({
                                    headers: {
                                        'Authorization': 'Bearer ' + resp.token,
                                        'X-CSRF-TOKEN': IPSApp.config.csrfToken
                                    }
                                });
                                $.ajax({
                                    url: '/auth/token/exchange',
                                    type: 'POST',
                                    headers: {'X-CSRF-TOKEN': IPSApp.config.csrfToken},
                                    data: {token: resp.token}
                                }).done(function() {
                                    sessionStorage.setItem('external_session_ready', '1');
                                    window.location.href = "/";
                                }).fail(function() {
                                    localStorage.removeItem('token');
                                    IPSApp.utils.handleApiError({}, 'No fue posible validar la sesión en el sitio.');
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: 'No se recibió el token de autenticación'
                                });
                            }
                        })
                        .fail(function(error) {
                            IPSApp.utils.handleApiError(error, 'Credenciales incorrectas');
                        })
                        .always(function() {
                            IPSApp.utils.hideLoading();
                        });
                },

                logout: function() {
                    $.ajax({
                        url: '/auth/token/logout',
                        type: 'POST',
                        headers: {'X-CSRF-TOKEN': IPSApp.config.csrfToken}
                    }).always(function() {
                        localStorage.removeItem('token');
                        localStorage.removeItem('user_name');
                        localStorage.removeItem('user_id');
                        localStorage.removeItem('cart_items');
                        sessionStorage.clear();
                        window.location.href = '/';
                    });
                }
            },

            // Cart functions
            cart: {
                review: function() {
                    const reviewVersion = ++IPSApp.state.cartReviewVersion;

                    if (IPSApp.state.cartReviewRequest) {
                        IPSApp.state.cartReviewRequest.abort();
                    }

                    IPSApp.state.cartReviewRequest = IPSApp.api.getCart()
                        .done(function(data) {
                            if (reviewVersion !== IPSApp.state.cartReviewVersion) return;
                            IPSApp.cart.updateUI(data);
                        })
                        .fail(function(error) {
                            if (error && error.statusText === 'abort') return;
                            console.error('Error reviewing cart:', error);
                        })
                        .always(function() {
                            if (reviewVersion === IPSApp.state.cartReviewVersion) {
                                IPSApp.state.cartReviewRequest = null;
                            }
                        });
                },

                updateUI: function(cartData) {
                    console.log('Cart data received:', cartData);
                    if (cartData.status || !cartData.repuestos) {
                        $('#cantidad_items').text('0').hide();
                        localStorage.setItem('cart_items', 0);
                        IPSApp.state.cartItems = [];
                        IPSApp.state.numeroCarrito = null;
                        return;
                    }

                    if (cartData.repuestos.length === 0) {
                        IPSApp.state.cartItems = [];
                        IPSApp.state.numeroCarrito = null;
                    }

                    // Contar productos diferentes (no sumar cantidades)
                    const totalProductos = cartData.repuestos.length;

                    console.log('Número de productos diferentes en el carrito:', totalProductos);

                    // Actualizar badge del header
                    const badge = $('#cantidad_items');
                    if (badge.length) {
                        badge.text(totalProductos);
                        badge.css('display', totalProductos > 0 ? 'flex' : 'none');
                    }

                    // Guardar en localStorage para sincronización
                    localStorage.setItem('cart_items', totalProductos);

                    // Guardar número de carrito - obtenido del primer repuesto
                    if (cartData.repuestos.length > 0 && cartData.repuestos[0].numero_carrito) {
                        IPSApp.state.numeroCarrito = cartData.repuestos[0].numero_carrito;
                        console.log('Número de carrito guardado:', IPSApp.state.numeroCarrito);
                    }

                    // Update mis_datos page specific elements
                    if (window.location.href.includes('/mis_datos')) {
                        const itemText = totalProductos === 1 ?
                            `Tiene ${totalProductos} item en el carrito.` :
                            `Tiene ${totalProductos} items en el carrito.`;

                        $('#cantidad_items_misdatos').text(itemText);

                        if (totalProductos > 0) {
                            $('#btn_ir_carrito').show();
                        } else {
                            $('#btn_ir_carrito').hide();
                        }
                    }

                },

                show: function() {
                    if (!IPSApp.utils.getToken()) {
                        Swal.fire({
                            icon: 'info',
                            title: 'Inicie sesión',
                            text: 'Debe registrarse para ver su carrito.'
                        });
                        return;
                    }

                    IPSApp.utils.showLoading('Cargando carrito...');

                    IPSApp.api.getCart()
                        .done(function(data) {
                            console.log('Descuento web resuelto:', data.descuento_web || null);
                            // Guardar número de carrito - obtenido del primer repuesto
                            if (data.repuestos && data.repuestos.length > 0 && data.repuestos[0].numero_carrito) {
                                IPSApp.state.numeroCarrito = data.repuestos[0].numero_carrito;
                                console.log('Número de carrito guardado en show():', IPSApp.state.numeroCarrito);
                            } else {
                                console.warn('No se pudo obtener el número de carrito');
                            }
                            IPSApp.cart.renderTable(data.repuestos || []);
                        })
                        .fail(function(error) {
                            IPSApp.utils.handleApiError(error, 'Error al cargar el carrito');
                        })
                        .always(function() {
                            IPSApp.utils.hideLoading();
                        });
                },

                renderTable: function(items) {
                    const table = $('#table_detalle_nv_app').DataTable();
                    table.clear();

                    // Guardar items en el estado
                    IPSApp.state.cartItems = items;

                    let subtotal = 0;

                    items.forEach(function(item) {
                        const priceFormatted = Number(item.precio_final || item.precio_venta).toFixed(0);
                        const originalFormatted = Number(item.precio_original || priceFormatted).toFixed(0);
                        const porcentajeWeb = Number(item.porcentaje_descuento_web || 0);
                        const total = item.cantidad * priceFormatted;
                        subtotal += total;

                        const image = `<img src="${IPSApp.config.apiBaseUrl}/storage/${item.urlfoto}" width="50" alt="${item.descripcion}" class="rounded">`;
                        const originalDisplay = porcentajeWeb > 0
                            ? `<div class="text-muted text-decoration-line-through small">$${IPSApp.utils.commaSeparateNumber(originalFormatted)}</div>`
                            : '';
                        const discountBadge = porcentajeWeb > 0
                            ? ` <span class="badge bg-success">-${porcentajeWeb}% web</span>`
                            : '';
                        const priceDisplay = originalDisplay + '<strong>$' + IPSApp.utils.commaSeparateNumber(priceFormatted) + '</strong>' +
                                           discountBadge + (item.oferta == 1 ? ' <span class="badge bg-danger">Oferta</span>' : '');

                        // Control de cantidad editable
                        const cantidadControl = `
                            <div class="input-group input-group-sm" style="width: 120px;">
                                <button class="btn btn-outline-secondary btn-sm" type="button"
                                        onclick="IPSApp.cart.cambiarCantidad(${item.id}, -1)">
                                    <i class="fas fa-minus"></i>
                                </button>
                                <input type="number"
                                       class="form-control form-control-sm text-center"
                                       id="cantidad_offcanvas_${item.id}"
                                       value="${item.cantidad}"
                                       min="1"
                                       max="1000"
                                       onchange="IPSApp.cart.actualizarCantidad(${item.id}, this.value)"
                                       onkeypress="if(event.key === 'Enter') IPSApp.cart.actualizarCantidad(${item.id}, this.value)">
                                <button class="btn btn-outline-secondary btn-sm" type="button"
                                        onclick="IPSApp.cart.cambiarCantidad(${item.id}, 1)">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        `;

                        // Botón de eliminar
                        const btnEliminar = `
                            <button class="btn btn-outline-danger btn-sm"
                                    onclick="IPSApp.cart.eliminarItem(${item.id})"
                                    title="Eliminar producto">
                                <i class="fas fa-trash"></i>
                            </button>
                        `;

                        table.row.add([
                            image,
                            item.codigo_interno || 'N/A',
                            item.descripcion,
                            cantidadControl,
                            priceDisplay,
                            `<span id="total_offcanvas_${item.id}">$${IPSApp.utils.commaSeparateNumber(total)}</span>`,
                            btnEliminar
                        ]);
                    });

                    table.draw();

                    // Calcular y mostrar totales
                    IPSApp.cart.recalcularTotales();
                },

                cambiarCantidad: function(idProducto, incremento) {
                    const inputCantidad = $(`#cantidad_offcanvas_${idProducto}`);
                    const cantidadActual = parseInt(inputCantidad.val()) || 1;
                    const nuevaCantidad = Math.max(1, cantidadActual + incremento);

                    if (nuevaCantidad <= 1000) {
                        inputCantidad.val(nuevaCantidad);
                        IPSApp.cart.actualizarCantidad(idProducto, nuevaCantidad);
                    }
                },

                actualizarCantidad: function(idProducto, nuevaCantidad) {
                    const cantidad = parseInt(nuevaCantidad);

                    if (isNaN(cantidad) || cantidad < 1) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'La cantidad debe ser mayor a 0'
                        });
                        const item = IPSApp.state.cartItems.find(i => i.id == idProducto);
                        if (item) {
                            $(`#cantidad_offcanvas_${idProducto}`).val(item.cantidad);
                        }
                        return;
                    }

                    if (cantidad > 1000) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'La cantidad máxima es 1000'
                        });
                        $(`#cantidad_offcanvas_${idProducto}`).val(1000);
                        return;
                    }

                    IPSApp.cart.ejecutarActualizacionCantidad(idProducto, cantidad);
                },

                ejecutarActualizacionCantidad: function(idProducto, nuevaCantidad) {
                    const token = IPSApp.utils.getToken();
                    if (!token) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Debe iniciar sesión para actualizar el carrito'
                        });
                        return;
                    }

                    if (IPSApp.state.quantityUpdates[idProducto]) {
                        const itemActual = IPSApp.state.cartItems.find(i => i.id == idProducto);
                        if (itemActual) {
                            $(`#cantidad_offcanvas_${idProducto}`).val(itemActual.cantidad);
                        }
                        return;
                    }

                    if (!IPSApp.state.numeroCarrito) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'No se encontró el número de carrito. Por favor, recargue la página.'
                        });
                        console.error('Número de carrito no disponible:', IPSApp.state.numeroCarrito);
                        return;
                    }

                    // Obtener la cantidad anterior desde los datos guardados, NO del input
                    const item = IPSApp.state.cartItems.find(i => i.id == idProducto);
                    const cantidadAnterior = item ? item.cantidad : nuevaCantidad;

                    const inputCantidad = $(`#cantidad_offcanvas_${idProducto}`);
                    const controlesCantidad = inputCantidad.closest('.input-group').find('button, input');
                    IPSApp.state.quantityUpdates[idProducto] = true;
                    controlesCantidad.prop('disabled', true);

                    // Mostrar mensaje de carga
                    Swal.fire({
                        title: 'Actualizando...',
                        text: 'Por favor espere',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    const datosEnvio = {
                        id_repuesto: idProducto,
                        numero_carrito: IPSApp.state.numeroCarrito,
                        nueva_cantidad: nuevaCantidad,
                        token: token
                    };

                    console.log('Enviando actualización de cantidad:', datosEnvio);
                    console.log('Cantidad anterior guardada:', cantidadAnterior);

                    $.ajax({
                        type: 'POST',
                        url: IPSApp.config.apiBaseUrl + '/api/actualizar_cantidad_carrito',
                        data: datosEnvio,
                        headers: {
                            'Authorization': 'Bearer ' + token,
                            'X-CSRF-TOKEN': IPSApp.config.csrfToken
                        },
                        success: function(response) {
                            console.log('Respuesta actualización cantidad:', response);
                            if (response.success || response.message === 'OK') {
                                // Actualizar datos locales
                                if (item) {
                                    item.cantidad = nuevaCantidad;

                                    // Actualizar total del producto
                                    const precio = Number(item.precio_final || item.precio_venta || 0).toFixed(0);
                                    const nuevoTotal = nuevaCantidad * precio;
                                    $(`#total_offcanvas_${idProducto}`).html(`$${IPSApp.utils.commaSeparateNumber(nuevoTotal)}`);
                                }

                                // Recalcular totales generales
                                IPSApp.cart.recalcularTotales();

                                // Si estamos en la vista de carrito, recargar los datos
                                if (window.location.pathname === '/carrito' && typeof window.recargarVistaCarrito === 'function') {
                                    console.log('Recargando vista de carrito después de actualización de cantidad');
                                    window.recargarVistaCarrito();
                                }

                                Swal.fire({
                                    icon: 'success',
                                    title: 'Actualizado',
                                    text: 'Cantidad actualizada exitosamente',
                                    timer: 1500,
                                    showConfirmButton: false
                                });
                            }
                        },
                        error: function(xhr) {
                            console.error('Error al actualizar cantidad:', xhr);

                            // Restaurar la cantidad anterior desde los datos guardados
                            inputCantidad.val(cantidadAnterior);
                            console.log('Restaurando cantidad anterior:', cantidadAnterior);

                            let mensajeError = 'Error al actualizar la cantidad';
                            let stockDisponible = null;

                            // Intentar parsear la respuesta JSON del error
                            if (xhr.responseJSON) {
                                if (xhr.responseJSON.message) {
                                    mensajeError = xhr.responseJSON.message;
                                }
                                if (xhr.responseJSON.stock_disponible !== undefined) {
                                    stockDisponible = xhr.responseJSON.stock_disponible;
                                }
                            } else if (xhr.responseText) {
                                try {
                                    const errorData = JSON.parse(xhr.responseText);
                                    if (errorData.message) {
                                        mensajeError = errorData.message;
                                    }
                                    if (errorData.stock_disponible !== undefined) {
                                        stockDisponible = errorData.stock_disponible;
                                    }
                                } catch (e) {
                                    console.error('No se pudo parsear el error:', e);
                                }
                            }

                            // Cerrar loading antes de mostrar error
                            Swal.close();

                            // Mostrar mensaje específico según el tipo de error
                            if (xhr.status === 500 && (mensajeError.toLowerCase().includes('stock') || stockDisponible !== null)) {
                                const nombreProducto = item ? item.descripcion : 'el producto';

                                // Si hay stock disponible, ofrecer ajustar a esa cantidad
                                if (stockDisponible !== null && stockDisponible > 0) {
                                    Swal.fire({
                                        icon: 'warning',
                                        title: 'Stock Insuficiente',
                                        html: `
                                            <p>${mensajeError}</p>
                                            <p><strong>Stock disponible: ${stockDisponible} unidades</strong></p>
                                            <p class="text-muted">¿Desea ajustar la cantidad a ${stockDisponible} unidades?</p>
                                        `,
                                        showCancelButton: true,
                                        confirmButtonText: `Sí, ajustar a ${stockDisponible}`,
                                        cancelButtonText: 'No, mantener anterior',
                                        confirmButtonColor: '#28a745',
                                        cancelButtonColor: '#6c757d'
                                    }).then((result) => {
                                        if (result.isConfirmed) {
                                            // Ajustar al stock máximo disponible
                                            inputCantidad.val(stockDisponible);
                                            delete IPSApp.state.quantityUpdates[idProducto];
                                            controlesCantidad.prop('disabled', false);
                                            IPSApp.cart.ejecutarActualizacionCantidad(idProducto, stockDisponible);
                                        } else {
                                            // Ya está restaurada la cantidad anterior
                                            inputCantidad.val(cantidadAnterior);
                                        }
                                    });
                                } else if (stockDisponible === 0) {
                                    // Sin stock disponible
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Sin Stock',
                                        html: `
                                            <p>${mensajeError}</p>
                                            <p><strong>No hay stock disponible para este producto</strong></p>
                                            <p class="text-muted">Se recomienda eliminar el producto del carrito</p>
                                        `,
                                        showCancelButton: true,
                                        confirmButtonText: 'Eliminar del carrito',
                                        cancelButtonText: 'Cancelar',
                                        confirmButtonColor: '#d33',
                                        cancelButtonColor: '#6c757d'
                                    }).then((result) => {
                                        if (result.isConfirmed) {
                                            IPSApp.cart.eliminarItem(idProducto);
                                        } else {
                                            inputCantidad.val(cantidadAnterior);
                                        }
                                    });
                                } else {
                                    // Stock no especificado
                                    Swal.fire({
                                        icon: 'warning',
                                        title: 'Stock Insuficiente',
                                        html: `
                                            <p>${mensajeError}</p>
                                            <p class="text-muted">La cantidad se ha restaurado a ${cantidadAnterior} unidades</p>
                                        `,
                                        confirmButtonText: 'Entendido',
                                        confirmButtonColor: '#f39c12'
                                    });
                                }
                            } else if (xhr.status === 401) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Sesión Expirada',
                                    text: 'Debe iniciar sesión nuevamente'
                                }).then(() => {
                                    window.location.href = '/login';
                                });
                            } else if (xhr.status === 404) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: 'Producto no encontrado en el carrito'
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: mensajeError
                                });
                            }
                        },
                        complete: function() {
                            delete IPSApp.state.quantityUpdates[idProducto];
                            controlesCantidad.prop('disabled', false);
                        }
                    });
                },

                recalcularTotales: function() {
                    let montoNeto = 0;
                    let porcentajeWeb = 0;

                    if (IPSApp.state.cartItems) {
                        IPSApp.state.cartItems.forEach(item => {
                            const precio = Number(item.precio_final || item.precio_venta).toFixed(0);
                            montoNeto += (item.cantidad * precio);
                            porcentajeWeb = Math.max(porcentajeWeb, Number(item.porcentaje_descuento_web || 0));
                        });
                    }

                    const iva = Math.round(montoNeto * 0.19);
                    const totalConIva = montoNeto + iva;

                    $('#cart_subtotal').text('$' + IPSApp.utils.commaSeparateNumber(montoNeto));
                    $('#cart_iva').text('$' + IPSApp.utils.commaSeparateNumber(iva));
                    $('#cart_total').text('$' + IPSApp.utils.commaSeparateNumber(totalConIva));
                    $('#cart_discount_info')
                        .toggleClass('d-none', porcentajeWeb <= 0)
                        .text(porcentajeWeb > 0 ? `Descuento web aplicado: ${porcentajeWeb}%` : '');
                    $('#cart_totals_section').removeClass('d-none');
                },

                updateCartCount: function() {
                    // Actualizar el contador del badge en el header
                    IPSApp.cart.review();
                },

                eliminarItem: function(idRepuesto) {
                    if (!IPSApp.state.numeroCarrito) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'No se encontró el carrito'
                        });
                        return;
                    }

                    Swal.fire({
                        title: '¿Eliminar producto?',
                        text: 'Esta acción no se puede deshacer',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Sí, eliminar',
                        cancelButtonText: 'Cancelar'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            IPSApp.cart.ejecutarEliminacionItem(idRepuesto);
                        }
                    });
                },

                ejecutarEliminacionItem: function(idRepuesto) {
                    const token = IPSApp.utils.getToken();
                    if (!token) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Debe iniciar sesión'
                        });
                        return;
                    }

                    const url = `${IPSApp.config.apiBaseUrl}/api/eliminar_item_carrito/${idRepuesto}/${IPSApp.state.numeroCarrito}`;

                    Swal.fire({
                        title: 'Eliminando...',
                        text: 'Por favor espere',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    $.ajax({
                        type: 'GET',
                        url: url,
                        headers: {
                            'Authorization': 'Bearer ' + token
                        },
                        data: { 'token': token },
                        success: function(response) {
                            const repuestos = response.repuestos || [];

                            if (repuestos.length === 0) {
                                // Carrito vacío
                                IPSApp.state.cartItems = [];
                                const table = $('#table_detalle_nv_app').DataTable();
                                table.clear().draw();
                                $('#cart_totals_section').addClass('d-none');

                                Swal.fire({
                                    icon: 'info',
                                    title: 'Carrito vacío',
                                    text: 'Tu carrito está vacío',
                                    timer: 2000,
                                    showConfirmButton: false
                                });
                            } else {
                                // Actualizar carrito
                                IPSApp.cart.renderTable(repuestos);

                                Swal.fire({
                                    icon: 'success',
                                    title: 'Eliminado',
                                    text: 'Producto eliminado exitosamente',
                                    timer: 1500,
                                    showConfirmButton: false
                                });
                            }

                            // Actualizar contador global del carrito
                            IPSApp.cart.updateCartCount();

                            // Si estamos en la vista de carrito, recargar los datos
                            if (window.location.pathname === '/carrito' && typeof window.recargarVistaCarrito === 'function') {
                                window.recargarVistaCarrito();
                            }
                        },
                        error: function(xhr) {
                            Swal.close();
                            IPSApp.utils.handleApiError(xhr, 'Error al eliminar el producto');
                        }
                    });
                },

                addItem: function() {
                    if (IPSApp.state.isAddingToCart) {
                        return false;
                    }

                    IPSApp.state.isAddingToCart = true;

                    const productId = $('#idrepuesto_modal').val();
                    const quantity = parseInt($('#cantidad_modal').val());
                    const stock = parseInt($('#stock_repuesto_modal').val());

                    if (quantity > stock) {
                        IPSApp.state.isAddingToCart = false;
                        Swal.fire({
                            title: "Error",
                            text: 'Cantidad supera el stock actual',
                            icon: "error"
                        });
                        return false;
                    }

                    if (!IPSApp.utils.getToken()) {
                        IPSApp.state.isAddingToCart = false;
                        Swal.fire({
                            title: "Error",
                            text: 'Debe registrarse para poder comprar.',
                            icon: "error"
                        });
                        return false;
                    }

                    IPSApp.api.addToCart(productId, quantity)
                        .done(function(resp) {
                            if (resp.message === 'OK') {
                                Swal.fire({
                                    title: "Agregado al carrito",
                                    text: 'El repuesto ha sido agregado al carrito exitosamente.',
                                    icon: "success",
                                    timer: 2000,
                                    showConfirmButton: false
                                });
                                $('#modalVistaRapida').modal('hide');
                                setTimeout(() => IPSApp.cart.show(), 500);
                                // actualizamos el contador del carrito
                                IPSApp.cart.updateCartCount();
                            }
                        })
                        .fail(function(error) {
                            IPSApp.utils.handleApiError(error, 'El repuesto ya existe en el carrito');
                            $('#modalVistaRapida').modal('hide');
                        })
                        .always(function() {
                            IPSApp.state.isAddingToCart = false;
                        });
                }
            },

            // Product functions
            product: {
                showQuickView: function(productId) {
                    // Show loading state
                    $('#modalLoadingState').removeClass('d-none');
                    $('#modalbody_vistarapida').addClass('d-none');

                    // Clear previous data
                    IPSApp.product.clearModal();

                    IPSApp.api.getProduct(productId)
                        .done(function(data) {
                            IPSApp.product.renderModal(data, productId);
                        })
                        .fail(function(error) {
                            IPSApp.product.showError(productId);
                        });
                },

                clearModal: function() {
                    $('#stock_repuesto_modal').val(1);
                    $('#stock_repuesto_').text('-');
                    $('#cantidad_modal').val(1);
                    $('#vista_rapida_titulo').text('Vista Rápida del Repuesto');
                    $('#codigo_interno_repuesto_').text('-');
                    $('#marca_repuesto_').text('-');
                    $('#precio_modal').text('-');
                    $('#quickview-carousel-inner').empty();
                    $('#quickview-preview-photos').empty();
                    $('#quickview-rating').empty();
                },

                renderModal: function(data, productId) {
                    const [product, photos, , , discount, , , rectifiers, alternators] = data;

                    // Hide loading, show content
                    $('#modalLoadingState').addClass('d-none');
                    $('#modalbody_vistarapida').removeClass('d-none');

                    // Set basic info
                    $('#id_repuesto_detalle').attr('href', `/repuesto/${productId}`);
                    $('#idrepuesto_modal').val(product.id);
                    $('#vista_rapida_titulo').text(product.descripcion);
                    $('#codigo_interno_repuesto_').text(product.codigo_interno || 'N/A');
                    $('#marca_repuesto_').text(product.marcarepuesto || 'Sin marca');
                    $('#stock_repuesto_modal').val(product.stock_actual);
                    IPSApp.product.renderRating('#quickview-rating', product.valoracion, product.id);

                    // Set stock display
                    IPSApp.product.renderStock(product.stock_actual);

                    // Set price
                    IPSApp.product.renderPrice(product, discount);

                    // Set additional info
                    IPSApp.product.renderAdditionalInfo(rectifiers, alternators);

                    // Set images
                    IPSApp.product.renderImages(photos, product.descripcion);
                },

                renderStock: function(stock) {
                    let stockHtml;
                    if (stock > 100) {
                        stockHtml = '<span class="badge bg-success">DISPONIBLE</span>';
                    } else if (stock > 0) {
                        stockHtml = `<span class="badge bg-warning">${stock} unidades</span>`;
                    } else {
                        stockHtml = '<span class="badge bg-danger">SIN STOCK</span>';
                    }
                    $('#stock_repuesto_').html(stockHtml);
                },

                renderPrice: function(product, discount) {
                    const firstValidPrice = (...values) => values.map(Number).find(value => value > 0) || 0;
                    const normalPrice = firstValidPrice(product.precio_original, product.precio_venta);
                    const offerPrice = firstValidPrice(
                        product.precio_final,
                        product.precio_actualizado,
                        discount && discount.precio_actualizado,
                        normalPrice
                    );
                    const percentage = Number(
                        product.porcentaje_descuento_web ||
                        product.porcentaje_descuento ||
                        (discount && (discount.porcentaje_descuento_web || discount.porcentaje)) ||
                        0
                    );
                    const hasDiscount = normalPrice > 0 && offerPrice > 0 && offerPrice < normalPrice;
                    let priceHtml;

                    if (!hasDiscount) {
                        priceHtml = `<span class="fs-4">$${IPSApp.utils.commaSeparateNumber(normalPrice.toFixed(0))}</span>`;
                    } else {
                        const discountBadge = percentage > 0 ? `-${percentage}%` : 'OFERTA';
                        priceHtml = `
                            <small class="text-muted d-block">Precio normal</small>
                            <span class="text-muted text-decoration-line-through">$${IPSApp.utils.commaSeparateNumber(normalPrice.toFixed(0))}</span>
                            <br><small class="text-success d-block mt-1">Nuevo precio</small>
                            <span class="fs-4 text-success">$${IPSApp.utils.commaSeparateNumber(offerPrice.toFixed(0))}</span>
                            <span class="badge bg-danger ms-2">${discountBadge}</span>
                        `;
                    }

                    $('#precio_modal').html(priceHtml);
                },

                renderAdditionalInfo: function(rectifiers, alternators) {
                    if (rectifiers && rectifiers.length > 0) {
                        $('#tr_rectificadores_modal').removeClass('d-none');
                        $('#rectificadores_modal').html(rectifiers.map(r => r.rectificador).join('<br>'));
                    } else {
                        $('#tr_rectificadores_modal').addClass('d-none');
                    }

                    if (alternators && alternators.length > 0) {
                        $('#tr_alternadores_modal').removeClass('d-none');
                        $('#alternadores_modal').html(alternators.map(a => a.alternador).join('<br>'));
                    } else {
                        $('#tr_alternadores_modal').addClass('d-none');
                    }
                },

                renderImages: function(photos, description) {
                    $('#quickview-carousel-inner').empty();
                    $('#quickview-preview-photos').empty();

                    if (photos && photos.length > 0) {
                        photos.forEach((photo, index) => {
                            const isActive = index === 0 ? 'active' : '';
                            const imageUrl = `${IPSApp.config.apiBaseUrl}/storage/${photo.urlfoto}`;

                            // Carousel item
                            $('#quickview-carousel-inner').append(`
                                <div class="carousel-item ${isActive}">
                                    <div class="product-zoom-frame" title="Mueva el cursor para acercar; haga clic para ampliar">
                                        <img src="${imageUrl}" class="d-block w-100 product-zoom-image"
                                             alt="${description}"
                                             style="height: 300px; object-fit: contain; background: #f8f9fa;"
                                             onerror="this.src='{{ asset('assets/images/no-image.png') }}'">
                                    </div>
                                </div>
                            `);

                            // Thumbnail
                            $('#quickview-preview-photos').append(`
                                <div class="col-3">
                                    <img src="${imageUrl}"
                                         class="img-thumbnail w-100"
                                         alt="${description}"
                                         style="height: 60px; object-fit: cover; cursor: pointer;"
                                         onclick="IPSApp.product.goToSlide(${index})"
                                         onerror="this.src='{{ asset('assets/images/no-image.png') }}'">
                                </div>
                            `);
                        });
                    } else {
                        // Default image
                        const defaultImage = `${IPSApp.config.apiBaseUrl}/storage/fotozzz/d42e48078f97443083b70b04a710b31b.png`;
                        $('#quickview-carousel-inner').html(`
                            <div class="carousel-item active">
                                <div class="product-zoom-frame" title="Haga clic para ampliar">
                                    <img src="${defaultImage}" class="d-block w-100 product-zoom-image"
                                         alt="Sin imagen disponible"
                                         style="height: 300px; object-fit: contain; background: #f8f9fa;">
                                </div>
                            </div>
                        `);

                        $('#quickview-preview-photos').html(`
                            <div class="col-12 text-center">
                                <img src="${defaultImage}" class="img-thumbnail"
                                     alt="Sin imagen" style="height: 80px; object-fit: cover;">
                                <p class="small text-muted mt-1">Sin imágenes disponibles</p>
                            </div>
                        `);
                    }
                },

                renderRating: function(containerSelector, summary, productId, editable = false) {
                    const rating = summary || {};
                    const average = Number(rating.promedio || 0);
                    const count = Number(rating.cantidad || 0);
                    const userRating = Number(rating.valoracion_usuario || 0);
                    const stars = [1, 2, 3, 4, 5].map(value => editable ? `
                        <button type="button" class="product-rating__star ${value <= userRating ? 'is-selected' : ''}"
                                onclick="IPSApp.product.saveRating(${Number(productId)}, ${value}, '${containerSelector}')"
                                aria-label="Calificar con ${value} estrella${value === 1 ? '' : 's'}" title="${value} estrella${value === 1 ? '' : 's'}">
                            <i class="${value <= (userRating || Math.round(average)) ? 'fas' : 'far'} fa-star"></i>
                        </button>` : `<span class="product-rating__star product-rating__star--readonly"><i class="${value <= Math.round(average) ? 'fas' : 'far'} fa-star"></i></span>`).join('');
                    const countLabel = count === 1 ? '1 valoración' : `${count} valoraciones`;
                    $(containerSelector).html(`
                        <div class="product-rating__summary"><strong>${average.toFixed(1)}</strong><span>${countLabel}</span></div>
                        <div class="product-rating__stars" role="group" aria-label="Valoración del producto">${stars}</div>
                        <small>${editable ? (userRating ? `Tu valoración: ${userRating}/5 · Puedes cambiarla` : 'Selecciona una estrella para calificar') : 'Promedio de clientes que compraron este producto'}</small>`);
                },

                saveRating: function(productId, value, containerSelector) {
                    const token = IPSApp.utils.getToken();
                    if (!token) {
                        Swal.fire({icon: 'info', title: 'Inicia sesión', text: 'Debes iniciar sesión para calificar productos.'});
                        return;
                    }

                    $.ajax({
                        type: 'POST',
                        url: `${IPSApp.config.apiBaseUrl}/api/repuestos/${productId}/valoracion`,
                        headers: {'Authorization': 'Bearer ' + token, 'Accept': 'application/json'},
                        data: {token: token, valoracion: value},
                        success: function(response) {
                            IPSApp.product.renderRating(containerSelector, response.valoracion, productId, true);
                            Swal.fire({icon: 'success', title: 'Valoración guardada', text: response.message, timer: 1600, showConfirmButton: false});
                        },
                        error: function(xhr) {
                            IPSApp.utils.handleApiError(xhr, 'No fue posible guardar tu valoración');
                        }
                    });
                },

                showError: function(productId) {
                    $('#modalLoadingState').addClass('d-none');
                    $('#modalbody_vistarapida').removeClass('d-none');

                    $('#modalbody_vistarapida').html(`
                        <div class="alert alert-danger text-center">
                            <i class="fas fa-exclamation-triangle fa-2x mb-2"></i>
                            <h5>Error al cargar la información</h5>
                            <p>No se pudo cargar la información del repuesto. Por favor, intente nuevamente.</p>
                            <button class="btn btn-primary btn-sm" onclick="IPSApp.product.showQuickView(${productId})">
                                <i class="fas fa-redo me-1"></i>Reintentar
                            </button>
                        </div>
                    `);
                },

                goToSlide: function(slideIndex) {
                    const carousel = new bootstrap.Carousel(document.getElementById('quickViewCarousel'));
                    carousel.to(slideIndex);
                },

                increaseQuantity: function() {
                    const maxStock = parseInt($('#stock_repuesto_modal').val());
                    const currentQuantity = parseInt($('#cantidad_modal').val());

                    if (currentQuantity < maxStock) {
                        $('#cantidad_modal').val(currentQuantity + 1);
                    } else {
                        $('#cantidad_modal').val(maxStock);
                    }
                },

                decreaseQuantity: function() {
                    const currentQuantity = parseInt($('#cantidad_modal').val());
                    if (currentQuantity > 1) {
                        $('#cantidad_modal').val(currentQuantity - 1);
                    }
                }
            },

            // Search functions
            search: {
                handleKeyPress: function(event) {
                    if (event.keyCode === 13) {
                        const searchValue = $('#tags').val().trim();
                        if (searchValue) {
                            IPSApp.search.execute(searchValue);
                        } else {
                            Swal.fire({
                                icon: 'error',
                                text: 'Debe ingresar algo para buscar'
                            });
                        }
                    }
                },

                execute: function(searchTerm = null) {
                    const value = searchTerm || $('#tags').val().trim();

                    if (!value) {
                        Swal.fire({
                            icon: 'error',
                            text: 'Debe ingresar algo para buscar'
                        });
                        return false;
                    }

                    window.location.href = `/${encodeURIComponent(value)}/buscador`;
                }
            },

            // Initialize application
            init: function() {
                // Configure NProgress
                NProgress.configure({
                    easing: 'ease',
                    speed: 500
                });

                // Setup AJAX progress indicator
                $(document).ajaxStart(() => NProgress.start())
                           .ajaxStop(() => NProgress.done());

                // Log inicial
                const token = IPSApp.utils.getToken();
                console.log('=== INICIALIZACIÓN APP ===');
                console.log('Token en localStorage:', token ? 'Presente' : 'No encontrado');
                if (token) {
                    console.log('Token guardado correctamente');
                }

                // Initialize DataTable
                $('#table_detalle_nv_app').DataTable({
                    responsive: true,
                    language: {
                        url: '/assets/datatables/es-ES.json'
                    },
                    order: [],
                    info: true,
                    paging: false,
                    searching: false,
                    lengthChange: false
                });

                // Initialize AOS
                AOS.init({
                    duration: 650,
                    once: true
                });

                // La sesión Laravel es la autoridad para la interfaz del usuario.
                const sessionUser = @json(session('external_auth.user'));
                if (sessionUser) {
                    IPSApp.state.user = sessionUser;
                    IPSApp.auth.updateUI(sessionUser);
                    if (window.location.href.includes('/mis_datos')) {
                        IPSApp.auth.fillUserForm(sessionUser);
                    }
                    IPSApp.cart.review();
                } else {
                    IPSApp.auth.showUnauthenticated();
                }

                // Global error handler
                window.addEventListener('error', function(e) {
                    console.error('Global error:', e.error);
                    if (IPSApp.config.environment !== 'production') {
                        console.error(e);
                    }
                });

                console.log('IPS Autopartes App initialized successfully');
            }
        };

        // Global function aliases for backward compatibility
        window.getUser = IPSApp.auth.getUser;
        window.revisar_carrito = IPSApp.cart.review;
        window.vista_rapida = IPSApp.product.showQuickView;
        window.agregar_carrito_ = IPSApp.cart.addItem;
        window.aumentar = IPSApp.product.increaseQuantity;
        window.disminuir = IPSApp.product.decreaseQuantity;
        window.logout = IPSApp.auth.logout;
        window.dame_carrito_ = IPSApp.cart.show;
        window.login = IPSApp.auth.login;
        window.enter_buscar = IPSApp.search.handleKeyPress;
        window.buscar = IPSApp.search.execute;
        window.goToSlide = IPSApp.product.goToSlide;
        window.commaSeparateNumber = IPSApp.utils.commaSeparateNumber;

        // Initialize when DOM is ready
        $(document).ready(function() {
            IPSApp.init();
        });
    </script>

    <!-- Visor ampliado compartido -->
    <div class="product-lightbox" id="productLightbox" role="dialog" aria-modal="true" aria-label="Imagen ampliada del producto" aria-hidden="true">
        <button type="button" class="btn btn-light rounded-circle product-lightbox__close" aria-label="Cerrar imagen ampliada">
            <i class="fas fa-times"></i>
        </button>
        <div class="product-lightbox__stage">
            <img class="product-lightbox__image" src="" alt="">
        </div>
        <div class="btn-group product-lightbox__tools" role="group" aria-label="Controles de zoom">
            <button type="button" class="btn btn-light" data-zoom-action="out" aria-label="Alejar"><i class="fas fa-minus"></i></button>
            <button type="button" class="btn btn-light" data-zoom-action="reset">100%</button>
            <button type="button" class="btn btn-light" data-zoom-action="in" aria-label="Acercar"><i class="fas fa-plus"></i></button>
        </div>
    </div>

    <script>
        (function () {
            const lightbox = document.getElementById('productLightbox');
            if (!lightbox) return;
            const stage = lightbox.querySelector('.product-lightbox__stage');
            const largeImage = lightbox.querySelector('.product-lightbox__image');
            const resetButton = lightbox.querySelector('[data-zoom-action="reset"]');
            let scale = 1;

            function setScale(nextScale) {
                scale = Math.min(4, Math.max(1, nextScale));
                largeImage.style.setProperty('--lightbox-scale', scale);
                resetButton.textContent = Math.round(scale * 100) + '%';
                stage.style.cursor = scale > 1 ? 'zoom-out' : 'zoom-in';
            }

            function closeLightbox() {
                lightbox.classList.remove('is-open');
                lightbox.setAttribute('aria-hidden', 'true');
                document.body.style.overflow = '';
                largeImage.src = '';
            }

            document.addEventListener('pointermove', function (event) {
                const frame = event.target.closest('.product-zoom-frame');
                if (!frame || event.pointerType === 'touch') return;
                const image = frame.querySelector('.product-zoom-image');
                const rect = frame.getBoundingClientRect();
                image.style.setProperty('--zoom-x', ((event.clientX - rect.left) / rect.width * 100) + '%');
                image.style.setProperty('--zoom-y', ((event.clientY - rect.top) / rect.height * 100) + '%');
            });

            document.addEventListener('click', function (event) {
                const image = event.target.closest('.product-zoom-image');
                if (!image) return;
                largeImage.src = image.currentSrc || image.src;
                largeImage.alt = image.alt || 'Producto ampliado';
                largeImage.style.setProperty('--lightbox-x', '50%');
                largeImage.style.setProperty('--lightbox-y', '50%');
                setScale(1);
                lightbox.classList.add('is-open');
                lightbox.setAttribute('aria-hidden', 'false');
                document.body.style.overflow = 'hidden';
                lightbox.querySelector('.product-lightbox__close').focus();
            });

            stage.addEventListener('pointermove', function (event) {
                if (scale <= 1 || event.pointerType === 'touch') return;
                const rect = stage.getBoundingClientRect();
                largeImage.style.setProperty('--lightbox-x', ((event.clientX - rect.left) / rect.width * 100) + '%');
                largeImage.style.setProperty('--lightbox-y', ((event.clientY - rect.top) / rect.height * 100) + '%');
            });
            stage.addEventListener('click', function () { setScale(scale > 1 ? 1 : 2); });
            lightbox.querySelector('.product-lightbox__close').addEventListener('click', closeLightbox);
            lightbox.addEventListener('click', function (event) { if (event.target === lightbox) closeLightbox(); });
            lightbox.querySelector('[data-zoom-action="in"]').addEventListener('click', function () { setScale(scale + .5); });
            lightbox.querySelector('[data-zoom-action="out"]').addEventListener('click', function () { setScale(scale - .5); });
            resetButton.addEventListener('click', function () { setScale(1); });
            document.addEventListener('keydown', function (event) { if (event.key === 'Escape' && lightbox.classList.contains('is-open')) closeLightbox(); });
        })();
    </script>

    <!-- MODALS -->

    <!-- Modal Vista Rápida Mejorado -->
    <div class="modal fade" id="modalVistaRapida" tabindex="-1" aria-labelledby="modalVistaRapidaLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-light">
                    <div class="d-flex align-items-center">
                        <img src="{{ asset('assets/images/logo_ips2025.png') }}" alt="IPS Logo"
                            class="logo_header rounded me-3" style="height: 40px;">
                        <h5 class="modal-title mb-0" id="vista_rapida_titulo">Vista Rápida del Repuesto</h5>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <!-- Loading State -->
                <div class="modal-body text-center" id="modalLoadingState">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Cargando...</span>
                    </div>
                    <p class="mt-2 text-muted">Cargando información del repuesto...</p>
                </div>

                <!-- Content State -->
                <div class="modal-body d-none" id="modalbody_vistarapida">
                    <div class="row">
                        <!-- Columna de Imágenes -->
                        <div class="col-md-5">
                            <div class="card border-0 shadow-sm">
                                <div class="card-body p-3">
                                    <!-- Carousel Principal -->
                                    <div id="quickViewCarousel" class="carousel slide mb-3" data-bs-ride="carousel">
                                        <div class="carousel-inner rounded" id="quickview-carousel-inner" style="height: 300px;">
                                            <!-- Las imágenes se cargan dinámicamente -->
                                        </div>
                                        <button class="carousel-control-prev" type="button"
                                            data-bs-target="#quickViewCarousel" data-bs-slide="prev">
                                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                            <span class="visually-hidden">Anterior</span>
                                        </button>
                                        <button class="carousel-control-next" type="button"
                                            data-bs-target="#quickViewCarousel" data-bs-slide="next">
                                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                            <span class="visually-hidden">Siguiente</span>
                                        </button>
                                    </div>

                                    <!-- Miniaturas -->
                                    <div class="row g-2" id="quickview-preview-photos">
                                        <!-- Las miniaturas se cargan dinámicamente -->
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Columna de Información -->
                        <div class="col-md-7">
                            <div class="card border-0 shadow-sm">
                                <div class="card-body">
                                    <!-- Información Principal -->
                                    <div class="table-responsive">
                                        <table class="table table-borderless">
                                            <tbody>
                                                <tr>
                                                    <td class="fw-bold" style="width: 40%;">
                                                        <i class="fas fa-barcode me-2 text-primary"></i>Código Interno
                                                    </td>
                                                    <td id="codigo_interno_repuesto_" class="text-muted">-</td>
                                                </tr>
                                                <tr>
                                                    <td class="fw-bold">
                                                        <i class="fas fa-boxes me-2 text-success"></i>Stock
                                                    </td>
                                                    <td id="stock_repuesto_" class="text-muted">-</td>
                                                </tr>
                                                <tr>
                                                    <td class="fw-bold">
                                                        <i class="fas fa-tag me-2 text-info"></i>Marca
                                                    </td>
                                                    <td id="marca_repuesto_" class="text-muted">-</td>
                                                </tr>
                                                <tr>
                                                    <td class="fw-bold">
                                                        <i class="fas fa-dollar-sign me-2 text-warning"></i>Precio
                                                    </td>
                                                    <td id="precio_modal" class="fs-5 fw-bold text-success">-</td>
                                                </tr>
                                                <tr id="tr_rectificadores_modal" class="d-none">
                                                    <td class="fw-bold">
                                                        <i class="fas fa-cog me-2 text-secondary"></i>Rectificadores
                                                    </td>
                                                    <td id="rectificadores_modal" class="small text-muted"></td>
                                                </tr>
                                                <tr id="tr_alternadores_modal" class="d-none">
                                                    <td class="fw-bold">
                                                        <i class="fas fa-bolt me-2 text-warning"></i>Alternadores
                                                    </td>
                                                    <td id="alternadores_modal" class="small text-muted"></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>

                                    <div id="quickview-rating" class="product-rating mt-3"></div>

                                    <hr class="my-3">

                                    <!-- Selector de Cantidad -->
                                    <div class="card bg-light">
                                        <div class="card-body">
                                            <h6 class="card-title mb-3">
                                                <i class="fas fa-shopping-cart me-2"></i>Seleccionar Cantidad
                                            </h6>
                                            <div class="row align-items-center">
                                                <div class="col-md-6">
                                                    <div class="input-group">
                                                        <button class="btn btn-outline-secondary" type="button" onclick="disminuir()">
                                                            <i class="fas fa-minus"></i>
                                                        </button>
                                                        <input type="number" class="form-control text-center"
                                                               name="cantidad" id="cantidad_modal" min="1" value="1"
                                                               style="max-width: 80px;">
                                                        <button class="btn btn-outline-secondary" type="button" onclick="aumentar()">
                                                            <i class="fas fa-plus"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <a href="#" id="id_repuesto_detalle" class="btn btn-link btn-sm">
                                                        <i class="fas fa-external-link-alt me-1"></i>Ver detalle completo
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Campos Ocultos -->
                    <input type="hidden" name="stock_repuesto" id="stock_repuesto_modal">
                    <input type="hidden" name="idrepuesto_modal" id="idrepuesto_modal">
                </div>

                <div class="modal-footer bg-light">
                    <div class="d-flex w-100 justify-content-between align-items-center">
                        <div>
                            <small class="text-muted">
                                <i class="fas fa-info-circle me-1"></i>
                                Agregue el producto a su carrito para continuar con la compra
                            </small>
                        </div>
                        <div>
                            <button type="button" class="btn btn-secondary btn-sm me-2" data-bs-dismiss="modal">
                                <i class="fas fa-times me-1"></i>Cerrar
                            </button>
                            <button class="btn btn-success btn-sm" onclick="agregar_carrito_()"
                                    data-bs-toggle="offcanvas" data-bs-target="#offcanvasRight"
                                    aria-controls="offcanvasRight">
                                <i class="fas fa-cart-plus me-1"></i>Agregar al carrito
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL Login -->
    @include('modals.login_moderno')
    @if(false)
    <div class="modal fade" id="modalLogin" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <img src="{{ asset('assets/images/logo_ips2025.png') }}" alt=""
                        class="logo_header rounded">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="formulario">
                        <div class="row">
                            <div class="col-md-12">
                                <!-- Email input -->
                                <div class="d-flex p-2 my-3 bd-highlight">
                                    <i class="fa-solid fa-user icon_header m-1 p-2 border border-1 rounded "></i>
                                    <input id="email_" type="email"
                                        class="form-control form-control-sm @error('email') is-invalid @enderror"
                                        name="email" value="{{ old('email') }}" required autocomplete="email"
                                        autofocus placeholder="Ingrese su email">

                                    @error('email')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-12">
                                <!-- Password input -->
                                <div class="d-flex p-2 my-3 bd-highlight">
                                    <i class="fa-solid fa-key icon_header m-1 p-2 border border-1 rounded "></i>
                                    <input id="password_" type="password"
                                        class="form-control form-control-sm @error('password') is-invalid @enderror"
                                        name="password" required autocomplete="current-password"
                                        placeholder="Ingrese su contraseña">

                                    @error('password')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center">
                            <!-- Checkbox -->
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="remember" id="remember"
                                    {{ old('remember') ? 'checked' : '' }}>

                                <label class="form-check-label" for="remember">
                                    {{ __('Recordarme') }}
                                </label>
                            </div>
                            @if (Route::has('password.request'))
                                <a class="btn btn-link" href="{{ route('password.request') }}">
                                    {{ __('¿Olvidaste tu contraseña?') }}
                                </a>
                            @endif
                        </div>

                        <div class="text-lg-end mt-4 pt-2">
                            <button type="button" class="btn btn-outline-dark btn-sm float-right" onclick="login()"
                                style="padding-left: 2.5rem; padding-right: 2.5rem;">
                                {{ __('Login') }}
                            </button>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <figcaption class="blockquote-footer">
                        SysMaFra {{ date('Y') }}&copy; <cite title="Source Title">Todos los derechos reservados.</cite>
                    </figcaption>
                    <p class="text-center"></p>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Modal Lateral Carrito -->
    <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasRight" aria-labelledby="offcanvasRightLabel">
        <div class="offcanvas-header">
            <img src="{{ asset('assets/images/logo_ips2025.png') }}" alt="" class="logo_header">
            <h5 id="offcanvasRightLabel">Nota de Venta</h5>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"
                aria-label="Close"></button>
        </div>
        <div class="offcanvas-body" style="font-size: 12px;">
            <div class="cart-loading d-none" id="cartLoading">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Cargando carrito...</span>
                </div>
            </div>

            <table id="table_detalle_nv_app" class="display nowrap" style="width:100%;">
                <thead>
                    <tr>
                        <th>Img</th>
                        <th>Código</th>
                        <th>Descripción</th>
                        <th>Cant.</th>
                        <th>P. Unit.</th>
                        <th>Total</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>

            <!-- Totales del carrito -->
            <div id="cart_totals_section" class="d-none mt-4">
                <div class="card">
                    <div class="card-body p-3">
                        <h6 class="card-title mb-3"><i class="fas fa-calculator me-2"></i>Resumen de Compra</h6>
                        <div id="cart_discount_info" class="alert alert-success py-2 d-none" role="status"></div>
                        <table class="table table-sm table-borderless mb-0">
                            <tr>
                                <td class="text-end"><strong>Subtotal (Neto):</strong></td>
                                <td class="text-end" style="width: 120px;"><span id="cart_subtotal" class="text-muted">$0</span></td>
                            </tr>
                            <tr>
                                <td class="text-end"><strong>IVA (19%):</strong></td>
                                <td class="text-end"><span id="cart_iva" class="text-muted">$0</span></td>
                            </tr>
                            <tr class="border-top">
                                <td class="text-end"><strong class="fs-6">Total:</strong></td>
                                <td class="text-end"><strong class="fs-6 text-success" id="cart_total">$0</strong></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>

            <div class="mt-3">
                <a href="/carrito" class="btn btn-outline-success btn-sm w-100">
                    <i class="fas fa-shopping-cart"></i> Revisar carrito completo
                </a>
            </div>
        </div>
    </div>

    @yield('javascript')
</body>

</html>
