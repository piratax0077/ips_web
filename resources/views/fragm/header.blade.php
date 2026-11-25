<style>
    * {
        box-sizing: border-box;
    }

    body {
        overflow-x: hidden;
        max-width: 100vw;
        padding-top: 100px; /* Compensar altura del header fijo */
        transition: padding-top 0.3s ease;
    }

    .icon_header {
        font-size: 40px;
        margin-right: 3px;
        color: #fff;
    }

    .header-fixed-wrapper nav.navbar {
        background: #fff;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        width: 100%;
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        z-index: 1030;
        transition: all 0.3s ease;
    }

    /* El wrapper ya es fijo: el nav debe participar en su altura para evitar franjas vacías. */
    .header-fixed-wrapper nav.navbar {
        position: relative;
        top: 0;
    }

    .main {
        margin-top: 0 !important;
    }

    /* Efecto de sombra al hacer scroll */
    .header-fixed-wrapper nav.navbar.scrolled {
        box-shadow: 0 4px 20px rgba(0,0,0,0.15);
        background: rgba(255, 255, 255, 0.98);
        backdrop-filter: blur(10px);
    }

    /* Contenedor del header completo (nav + submenu) */
    .header-fixed-wrapper {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        z-index: 1030;
        background: #fff;
        overflow: visible !important;
        max-height: none !important;
        height: auto !important;
    }

    /* Sobrescribir overflow del container-fluid global solo para el header */
    .header-fixed-wrapper .container-fluid {
        max-width: 100%;
        padding-right: 15px;
        padding-left: 15px;
        overflow: visible !important;
        overflow-x: visible !important;
        overflow-y: visible !important;
        max-height: none !important;
        height: auto !important;
    }

    .header-fixed-wrapper .navbar {
        padding: 0.25rem 1rem;
        overflow: visible !important;
        max-height: none !important;
        height: auto !important;
    }

    /* Navbar collapse - visible por defecto para dropdowns */
    .header-fixed-wrapper .navbar-collapse {
        overflow: visible !important;
        max-height: none !important;
        height: auto !important;
    }

    /* Asegurar que en desktop no haya restricción de altura */
    @media (min-width: 992px) {
        .header-fixed-wrapper,
        .header-fixed-wrapper .container-fluid,
        .header-fixed-wrapper .navbar,
        .header-fixed-wrapper .navbar-collapse {
            overflow: visible !important;
            max-height: none !important;
            height: auto !important;
        }
    }

    /* Scroll solo en móviles cuando el menú hamburguesa está expandido */
    @media (max-width: 991.98px) {
        .navbar-collapse.show,
        .navbar-collapse.collapsing {
            max-height: calc(100vh - 120px);
            overflow-y: auto !important;
            overflow-x: hidden;
        }

        .navbar-collapse::-webkit-scrollbar {
            width: 5px;
        }

        .navbar-collapse::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        .navbar-collapse::-webkit-scrollbar-thumb {
            background: #315866;
            border-radius: 10px;
        }

        .navbar-collapse::-webkit-scrollbar-thumb:hover {
            background: #2c4a56;
        }
    }

    .navbar-brand {
        margin-right: 0;
    }

    .navbar-nav {
        position: relative;
        overflow: visible !important;
    }

    .nav-item {
        position: relative !important;
        overflow: visible !important;
    }

    .nav-item.dropdown {
        position: relative !important;
        overflow: visible !important;
    }

    .nav-link-size {
        font-size: 15px;
        box-shadow: inset 0 0 0 0 #f2eb0d;
        color: #315866;
        margin: 0px 4px;
        padding: 6px 10px;
        transition: all .3s ease-in-out;
        border-radius: 5px;
        font-weight: 500;
        white-space: nowrap;
    }

    .nav-link-size:hover {
        box-shadow: inset 200px 0 0 0 #315866;
        color: #f2eb0d !important;
        transform: translateY(-2px);
    }

    .user-badge {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 6px 14px;
        border-radius: 25px;
        font-size: 13px;
        font-weight: 600;
        white-space: nowrap;
        box-shadow: 0 2px 8px rgba(102, 126, 234, 0.3);
        transition: all 0.3s ease;
        border: 2px solid rgba(255, 255, 255, 0.2);
    }

    .user-badge:hover {
        background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.5);
        transform: translateY(-1px);
    }

    #user_name_display {
        font-weight: 600;
        letter-spacing: 0.3px;
        text-transform: capitalize;
    }

    .user-badge i {
        font-size: 16px;
        margin-right: 6px;
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0%, 100% {
            opacity: 1;
        }
        50% {
            opacity: 0.7;
        }
    }

    /* Dropdown del usuario mejorado */
    #navbarDropdown .fa-chevron-down {
        transition: transform 0.3s ease;
    }

    #navbarDropdown[aria-expanded="true"] .fa-chevron-down {
        transform: rotate(180deg);
    }

    .cart-badge {
        position: absolute;
        top: -8px;
        right: -8px;
        min-width: 20px;
        height: 20px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 11px;
        font-weight: bold;
    }

    .icon-wrapper {
        position: relative;
        display: inline-block;
    }

    .dropdown-menu {
        border-radius: 10px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.15);
        border: none;
        position: absolute !important;
        top: 100% !important;
        left: 0 !important;
        right: auto !important;
        margin-top: 0.125rem;
        z-index: 1050;
        background: #ffffff !important;
        list-style: none !important;
        transform: none !important;
        float: none !important;
        max-height: 400px;
        overflow-y: auto;
    }

    .dropdown-menu::-webkit-scrollbar {
        width: 6px;
    }

    .dropdown-menu::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }

    .dropdown-menu::-webkit-scrollbar-thumb {
        background: #888;
        border-radius: 10px;
    }

    .dropdown-menu::-webkit-scrollbar-thumb:hover {
        background: #555;
    }

    .dropdown-menu-end {
        right: 0 !important;
        left: auto !important;
    }

    /* Asegurar que el dropdown se posicione relativo al nav-item */
    .nav-item.dropdown {
        position: relative !important;
    }

    .nav-item.dropdown .dropdown-menu {
        position: absolute !important;
        top: 100% !important;
        left: 0 !important;
    }

    /* Resetear estilos de li dentro de dropdowns */
    .dropdown-menu li {
        list-style: none !important;
        display: block !important;
        margin: 0 !important;
        padding: 0 !important;
        position: relative !important;
        float: none !important;
    }

    .dropdown-menu li:after {
        display: none !important;
    }

    /* Sobrescribir estilos globales del template para dropdowns */
    .dropdown-item,
    .dropdown-menu .dropdown-item {
        padding: 8px 15px !important;
        transition: all 0.3s;
        font-size: 14px;
        white-space: nowrap;
        color: #353535 !important;
        background-color: transparent;
        display: block;
        text-decoration: none;
        width: 100%;
        clear: both;
        font-weight: 400;
        text-align: inherit;
        border: 0;
    }

    .dropdown-item:hover,
    .dropdown-item:focus,
    .dropdown-menu .dropdown-item:hover,
    .dropdown-menu .dropdown-item:focus {
        background: #f8f9fa !important;
        color: #315866 !important;
        padding-left: 20px !important;
        text-decoration: none;
    }

    .dropdown-item i {
        color: inherit !important;
    }

    .dropdown-item .text-primary,
    .dropdown-item i.text-primary {
        color: #007bff !important;
    }

    .dropdown-item .text-success,
    .dropdown-item i.text-success {
        color: #28a745 !important;
    }

    .dropdown-item .text-info,
    .dropdown-item i.text-info {
        color: #17a2b8 !important;
    }

    .dropdown-item .text-danger,
    .dropdown-item i.text-danger {
        color: #dc3545 !important;
    }

    .dropdown-item.text-danger {
        color: #dc3545 !important;
    }

    .dropdown-item.text-danger:hover {
        color: #bd2130 !important;
    }

    .dropdown-header {
        padding: 8px 15px;
        font-size: 13px;
        font-weight: 600;
        color: #6c757d !important;
    }

    #dropdown_user_header {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border-radius: 8px 8px 0 0;
        margin: -8px -8px 0 -8px;
        padding: 12px 15px !important;
        color: #315866 !important;
        font-size: 14px;
        font-weight: 600;
    }

    #dropdown_user_header i {
        color: #667eea;
        font-size: 18px;
    }

    #dropdown_user_greeting {
        font-weight: 600;
        color: #315866;
    }

    .dropdown-divider {
        margin: 0.5rem 0;
    }

    .search-container {
        display: flex;
        flex-wrap: nowrap;
        gap: 5px;
        align-items: center;
    }

    #tags {
        min-width: 150px !important;
        max-width: 200px;
        flex: 1;
    }

    .d-flex.ms-2.search-container button {
        flex-shrink: 0;
    }

    /* Contenedor para elementos externos al collapse */
    .header-external-items {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
    }

    /* Botón toggler personalizado */
    .navbar-toggler {
        border: 2px solid #315866;
        background-color: #f2eb0d !important;
    }

    .navbar-toggler:focus {
        box-shadow: 0 0 0 0.2rem rgba(242, 235, 13, 0.5);
    }

    .navbar-toggler-icon {
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba(49, 88, 102, 1)' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e") !important;
    }

    /* Responsive adjustments */
    @media (max-width: 1200px) {
        .nav-link-size {
            font-size: 14px;
            margin: 0px 2px;
            padding: 5px 8px;
        }

        #tags {
            min-width: 120px !important;
            max-width: 150px;
        }

        .user-badge {
            font-size: 12px;
            padding: 3px 10px;
        }
    }

    @media (max-width: 992px) {
        body {
            padding-top: 110px; /* Más espacio en tablets por el menú colapsable */
        }

        .logo_header {
            max-width: 100px;
        }

        .navbar-collapse {
            max-width: 100%;
            overflow: visible !important;
            background: #4c7488;
            padding: 15px;
            margin-top: 10px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .navbar-nav {
            width: 100%;
        }

        .nav-item {
            margin: 5px 0;
        }

        .nav-link-size {
            display: block;
            width: 100%;
            text-align: left;
            padding: 10px 15px !important;
        }

        .dropdown-menu {
            position: relative !important;
            width: 100%;
            min-width: 100%;
            box-shadow: none;
            border-left: 3px solid #315866;
            margin-left: 15px;
            margin-top: 5px;
        }

        .dropdown-menu-end {
            left: 0 !important;
            right: auto !important;
        }

        #tags {
            min-width: 100%;
            max-width: 100%;
        }

        .search-container {
            width: 100%;
            margin-top: 10px;
            flex-direction: row;
        }

        .header-external-items {
            width: 100%;
            justify-content: space-between;
            margin-top: 10px;
            padding: 10px 0;
            border-top: 1px solid #e9ecef;
        }

        .icon-wrapper {
            margin: 10px 0;
        }

        .user-badge {
            display: inline-block;
        }
    }

    /* Estilos para el subheader */
    .submenu {
        background: linear-gradient(135deg, #315866 0%, #2c4a56 100%);
        color: #ffffff;
        padding: 6px 0;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        position: relative;
        z-index: 10;
    }

    .submenu-item {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        padding: 3px 8px;
    }

    .submenu-icon {
        font-size: 16px;
        /* color: #f2eb0d; */
        min-width: 20px;
        text-align: center;
    }

    .submenu-text {
        color: #ffffff;
        font-size: 13px;
        font-weight: bold;
    }

    .submenu a {
        color: #ffffff;
        text-decoration: none;
        transition: all 0.3s ease;
    }

    .submenu a:hover {
        color: #f2eb0d;
        transform: translateX(3px);
    }

    @media (max-width: 768px) {
        body {
            padding-top: 95px;
        }

        .logo_header {
            max-width: 80px;
        }

        .navbar {
            padding: 0.5rem 0.5rem;
        }

        .navbar-toggler {
            padding: 0.5rem 0.75rem;
            font-size: 1.1rem;
        }

        .submenu {
            padding: 8px 0;
        }

        .submenu-item {
            font-size: 11px;
            gap: 5px;
            padding: 3px 5px;
        }

        .submenu-icon {
            font-size: 12px;
            min-width: 16px;
        }

        .submenu-text {
            font-size: 11px;
            word-break: break-word;
        }

        .cart-badge {
            top: -5px;
            right: -5px;
            min-width: 18px;
            height: 18px;
            font-size: 10px;
        }
    }

    @media (max-width: 576px) {
        body {
            padding-top: 90px;
        }

        .logo_header {
            max-width: 70px;
        }

        .navbar {
            padding: 0.5rem 0.25rem;
        }

        .submenu-item {
            font-size: 10px;
            gap: 3px;
            padding: 2px 3px;
        }

        .submenu-icon {
            font-size: 11px;
            min-width: 14px;
        }

        .submenu-text {
            font-size: 10px;
        }

        .dropdown-item {
            font-size: 13px !important;
            padding: 6px 12px !important;
        }

        .user-badge {
            font-size: 11px;
            padding: 2px 8px;
        }
    }
</style>

<div class="header-fixed-wrapper">
<nav class="navbar navbar-expand-lg navbar-light" style="background: linear-gradient(180deg, #6f3c1f, #ab6333);">
    <div class="container-fluid">
        <img src="{{ asset('assets/images/logo_ips2025.png') }}" alt="" class="logo_header">
        <button class="navbar-toggler" style="background: yellow;" type="button" data-bs-toggle="collapse"
            data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false"
            aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse text-white p-1" id="navbarSupportedContent"
            style="border-bottom-left-radius: 10px; border-bottom-right-radius: 10px;">
            <ul class="navbar-nav me-auto mb-1 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link nav-link-size text-white" href="{{ url('/') }}">
                        <i class="fas fa-home me-1"></i>Inicio
                    </a>
                </li>

                <li class="nav-item dropdown">
                    <a class="nav-link nav-link-size text-white dropdown-toggle" href="#"
                        id="navbar_productos" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-box-open me-1"></i>Productos
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="navbar_productos">
                        <li><a class="dropdown-item" href="{{ url('repuestos') }}">
                            <i class="fas fa-list me-2"></i>Catálogo Completo
                        </a></li>
                        <li><hr class="dropdown-divider"></li>
                        {{-- <li><a class="dropdown-item" href="/neumatica/buscador">
                            <i class="fas fa-circle me-2"></i>Neumáticos
                        </a></li>
                        <li><a class="dropdown-item" href="/aceite/buscador">
                            <i class="fas fa-oil-can me-2"></i>Aceites y Lubricantes
                        </a></li>
                        <li><a class="dropdown-item" href="/filtro/buscador">
                            <i class="fas fa-filter me-2"></i>Filtros
                        </a></li> --}}
                        <li><a class="dropdown-item" href="/ofertas/buscador">
                            <i class="fas fa-tags me-2 text-danger"></i>Ofertas Especiales
                        </a></li>
                    </ul>
                </li>

                <li class="nav-item dropdown">
                    <a class="nav-link nav-link-size text-white dropdown-toggle" href="#"
                        id="navbar_servicios" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-concierge-bell me-1"></i>Servicios
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="navbar_servicios">
                        <li><a class="dropdown-item" href="{{ url('quienes-somos') }}">
                            <i class="fas fa-info-circle me-2"></i>Acerca de Nosotros
                        </a></li>
                        <li><a class="dropdown-item" href="{{ url('quienes-somos') }}#envios">
                            <i class="fas fa-shipping-fast me-2"></i>Despacho a Domicilio
                        </a></li>
                        <li><a class="dropdown-item" href="{{ url('quienes-somos') }}#retiro">
                            <i class="fas fa-store me-2"></i>Retiro en Tienda
                        </a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="{{ url('quienes-somos') }}#garantia">
                            <i class="fas fa-shield-alt me-2"></i>Garantías
                        </a></li>
                    </ul>
                </li>

                <li class="nav-item dropdown">
                    <a class="nav-link nav-link-size text-white dropdown-toggle" href="#"
                        id="navbar_ayuda" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-question-circle me-1"></i>Ayuda
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="navbar_ayuda">
                        <li><a class="dropdown-item" href="{{ url('quienes-somos') }}#contacto">
                            <i class="fas fa-envelope me-2"></i>Contacto
                        </a></li>
                        <li><a class="dropdown-item" href="{{ url('quienes-somos') }}#preguntas">
                            <i class="fas fa-question me-2"></i>Preguntas Frecuentes
                        </a></li>
                        <li><a class="dropdown-item" href="{{ url('quienes-somos') }}#como-comprar">
                            <i class="fas fa-shopping-cart me-2"></i>Cómo Comprar
                        </a></li>
                        <li><a class="dropdown-item" href="{{ url('quienes-somos') }}#terminos">
                            <i class="fas fa-file-contract me-2"></i>Términos y Condiciones
                        </a></li>
                    </ul>
                </li>

            </ul>
            <div id="gear-icon"></div>
            <!-- Authentication Links -->
            @php($externalUser = session('external_auth.user'))
            <ul class="navbar-nav ml-auto mb-1 mb-lg-0" id="no_autenticado" @if($externalUser) style="display:none" @endif>
                <li class="nav-item">
                    <a class="nav-link nav-link-size text-white" data-bs-toggle="modal" data-bs-target="#modalLogin"
                        href="#" >
                        <i class="fas fa-sign-in-alt me-1"></i>{{ __('Ingresar') }}
                    </a>
                </li>
            </ul>

            <ul class="navbar-nav ml-auto mb-1 mb-lg-0" id="autenticado" @if(!$externalUser) style="display:none" @endif>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle d-flex align-items-center nav-link-size" href="#" id="navbarDropdown"
                       role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <span class="user-badge me-2">
                            <i class="fas fa-user-circle me-1"></i>
                            <span id="user_name_display">{{ $externalUser['name'] ?? 'Usuario' }}</span>
                        </span>
                        <i class="fas fa-chevron-down" style="font-size: 12px;"></i>
                    </a>

                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                        <li><h6 class="dropdown-header" id="dropdown_user_header">
                            <i class="fas fa-user-circle me-2"></i>
                            <span id="dropdown_user_greeting">Gestión de Cuenta</span>
                        </h6></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a href="{{ url('mis_datos') }}" class="dropdown-item">
                            <i class="fas fa-id-card me-2 text-primary"></i>Mis Datos
                        </a></li>
                        <li><a href="{{ url('solicitudes') }}" class="dropdown-item">
                            <i class="fas fa-file-invoice me-2 text-success"></i>Mis Solicitudes
                        </a></li>
                        {{-- <li><a href="{{ url('historial') }}" class="dropdown-item">
                            <i class="fas fa-history me-2 text-info"></i>Historial de Compras
                        </a></li> --}}
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger" onclick="logout()" style="cursor: pointer;">
                            <i class="fas fa-sign-out-alt me-2"></i>Cerrar Sesión
                        </a></li>
                    </ul>
                </li>
            </ul>

            <div class="header-external-items">
                <a href="javascript:void(0)" class="d-flex align-items-center position-relative"
                   onclick="dame_carrito_()" style="text-decoration: none;"
                    data-bs-toggle="offcanvas" data-bs-target="#offcanvasRight" aria-controls="offcanvasRight">
                    <div class="icon-wrapper">
                        <i class="fa-solid fa-cart-shopping icon_header"></i>
                        <span class="badge bg-danger cart-badge" id="cantidad_items" @if(!$externalUser) style="display:none" @endif>0</span>
                    </div>
                </a>

                <div class="d-flex search-container">
                    <input type="text" id="tags" class="form-control me-2" onkeyup="enter_buscar(event)"
                        placeholder="🔍 Buscar..." aria-label="Search"
                        style="border-radius: 25px;">
                    <button class="btn btn-outline-light" type="button" onclick="buscar()"
                            style="border-radius: 25px; padding: 6px 12px;">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>

        </div>
    </div>
    @if (Auth::user())
        <input type="hidden" name="user_id" id="user_id" value="{{ Auth::user()->id }}">
    @endif
</nav>

<div class="container-fluid submenu">
    <div class="row align-items-center g-2 mx-0">
        <div class="col-12 col-md-4 mb-1 mb-md-0">
            <div class="submenu-item">
                <i class="fas fa-map-marker-alt submenu-icon"></i>
                <span class="submenu-text">RIO REFUGIO #9641, Santiago - Chile</span>
            </div>
        </div>
        <div class="col-12 col-md-4 mb-1 mb-md-0">
            <div class="submenu-item">
                <i class="fas fa-phone-alt submenu-icon"></i>
                <a href="tel:+56975159323" class="submenu-text">
                    +56 9 7515 9323
                </a>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="submenu-item">
                <i class="fas fa-envelope submenu-icon"></i>
                <a href="mailto:ventas_online@ipschile.cl" class="submenu-text">
                    ventas_online@ipschile.cl
                </a>
            </div>
        </div>
    </div>
</div>
</div> <!-- Cierre del header-fixed-wrapper -->

<script>
    // Función mejorada para verificar autenticación con token
    document.addEventListener('DOMContentLoaded', function() {
        syncHeaderOffset();
        verificarAutenticacion();
        actualizarCarrito();
        initScrollEffects();
    });

    function enableProtectedRouteBridge() {
        const protectedPaths = ['/carrito', '/solicitudes', '/mis_datos'];

        document.addEventListener('click', async function(event) {
            const link = event.target.closest('a[href]');
            const token = localStorage.getItem('token');
            if (!link || !token) return;

            const url = new URL(link.href, window.location.origin);
            if (url.origin !== window.location.origin || !protectedPaths.includes(url.pathname)) return;

            event.preventDefault();
            try {
                const response = await fetch('/auth/token/exchange', {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({token: token})
                });

                if (!response.ok) throw new Error('No fue posible sincronizar la sesión.');

                sessionStorage.setItem('external_session_ready', '1');
                window.location.assign(url.toString());
            } catch (error) {
                sessionStorage.removeItem('external_session_ready');
                localStorage.removeItem('token');
                window.location.assign('/login');
            }
        });
    }

    function syncHeaderOffset() {
        const header = document.querySelector('.header-fixed-wrapper');
        if (!header) return;

        const updateOffset = function() {
            document.body.style.paddingTop = header.offsetHeight + 'px';
        };

        updateOffset();
        window.addEventListener('resize', updateOffset);

        const collapse = document.getElementById('navbarSupportedContent');
        if (collapse) {
            collapse.addEventListener('shown.bs.collapse', updateOffset);
            collapse.addEventListener('hidden.bs.collapse', updateOffset);
        }
    }

    // Efecto de scroll para el header fijo
    function initScrollEffects() {
        const nav = document.querySelector('nav');
        let lastScroll = 0;

        window.addEventListener('scroll', function() {
            const currentScroll = window.pageYOffset;

            // Agregar clase 'scrolled' cuando se hace scroll
            if (currentScroll > 50) {
                nav.classList.add('scrolled');
            } else {
                nav.classList.remove('scrolled');
            }

            lastScroll = currentScroll;
        });
    }

    function verificarAutenticacion() {
        const sessionUser = @json($externalUser);

        const autenticado = document.getElementById('autenticado');
        const noAutenticado = document.getElementById('no_autenticado');
        const userNameDisplay = document.getElementById('user_name_display');

        if (sessionUser) {
            // Usuario autenticado
            autenticado.style.display = 'flex';
            noAutenticado.style.display = 'none';

            // Mostrar nombre de usuario si está disponible
            if (userNameDisplay && sessionUser.name) {
                userNameDisplay.textContent = sessionUser.name;
            } else if (userNameDisplay) {
                userNameDisplay.textContent = 'Usuario';
            }
        } else {
            // Usuario no autenticado
            autenticado.style.display = 'none';
            noAutenticado.style.display = 'flex';
        }
    }

    function actualizarCarrito() {
        const badge = document.getElementById('cantidad_items');
        const sessionAuthenticated = @json((bool) $externalUser);
        if (!sessionAuthenticated) {
            localStorage.removeItem('cart_items');
            if (badge) {
                badge.textContent = '0';
                badge.style.display = 'none';
            }
            return;
        }

        const cantidadItems = parseInt(localStorage.getItem('cart_items') || '0', 10);
        if (badge) {
            badge.textContent = cantidadItems;
            badge.style.display = cantidadItems > 0 ? 'flex' : 'none';
        }
    }

    function logout() {
        if (confirm('¿Estás seguro que deseas cerrar sesión?')) {
            // Limpiar storage
            localStorage.removeItem('token');
            localStorage.removeItem('user_name');
            localStorage.removeItem('user_id');
            localStorage.removeItem('cart_items');
            sessionStorage.clear();
            fetch('{{ route('external-auth.logout') }}', {
                method: 'POST',
                credentials: 'same-origin',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                }
            }).finally(function() {
                window.location.href = '{{ url("/") }}';
            });
        }
    }

    // Actualizar el contador del carrito periódicamente
    setInterval(actualizarCarrito, 3000);

    // ===== SISTEMA DE BÚSQUEDA =====

    /**
     * Función de búsqueda principal desde el header
     */
    function buscar() {
        const termino = document.getElementById('tags').value.trim();

        if (!termino) {
            Swal.fire({
                icon: 'warning',
                title: 'Campo vacío',
                text: 'Por favor ingrese un término de búsqueda'
            });
            return;
        }

        // Redirigir a la vista de buscador con el término
        window.location.href = `/${encodeURIComponent(termino)}/buscador`;
    }

    /**
     * Permite buscar presionando Enter
     */
    function enter_buscar(event) {
        if (event.keyCode === 13) {
            event.preventDefault();
            buscar();
        }
    }

    // Exponer funciones globalmente
    window.buscar = buscar;
    window.enter_buscar = enter_buscar;
</script>

