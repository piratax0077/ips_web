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
            transition: transform 0.3s ease;
        }

        #modalVistaRapida .carousel-inner img:hover {
            transform: scale(1.02);
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
                isLoading: false,
                isAddingToCart: false
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

                    if (error.responseJSON && error.responseJSON.message) {
                        message = error.responseJSON.message;
                    }

                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: message
                    });
                }
            },

            // API functions
            api: {
                makeRequest: function(options) {
                    const defaults = {
                        type: 'GET',
                        headers: {
                            'X-CSRF-TOKEN': IPSApp.config.csrfToken
                        }
                    };

                    if (IPSApp.utils.getToken()) {
                        defaults.headers.Authorization = 'Bearer ' + IPSApp.utils.getToken();
                    }

                    return $.ajax($.extend(true, defaults, options));
                },

                getUser: function() {
                    return this.makeRequest({
                        url: IPSApp.config.apiBaseUrl + IPSApp.config.endpoints.getUser,
                        type: 'POST'
                    });
                },

                getCart: function() {
                    return this.makeRequest({
                        url: IPSApp.config.apiBaseUrl + IPSApp.config.endpoints.revisarCarrito
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
                    IPSApp.api.getUser()
                        .done(function(resp) {
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
                            console.error('Error getting user:', error);
                            IPSApp.auth.showUnauthenticated();
                        });
                },

                updateUI: function(user) {
                    $('#no_autenticado').hide();
                    $('#autenticado').show();
                    $('#contenedor_descuentos').show();
                    $('#contenedor_ofertas').show();

                    const firstName = user.name.split(' ')[0];
                    $('#navbarDropdown').text(firstName);

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
                            localStorage.setItem('token', resp.token);
                            window.location.href = "/";
                        })
                        .fail(function(error) {
                            IPSApp.utils.handleApiError(error, 'Credenciales incorrectas');
                        })
                        .always(function() {
                            IPSApp.utils.hideLoading();
                        });
                },

                logout: function() {
                    localStorage.clear();
                    window.location.href = '/';
                }
            },

            // Cart functions
            cart: {
                review: function() {
                    IPSApp.api.getCart()
                        .done(function(data) {
                            IPSApp.cart.updateUI(data);
                        })
                        .fail(function(error) {
                            console.error('Error reviewing cart:', error);
                        });
                },

                updateUI: function(cartData) {
                    if (cartData.status || !cartData.repuestos) {
                        $('#cantidad_items').empty();
                        return;
                    }

                    const itemCount = cartData.repuestos.length;
                    $('#cantidad_items').text(itemCount);

                    // Update mis_datos page specific elements
                    if (window.location.href.includes('/mis_datos')) {
                        const itemText = itemCount === 1 ?
                            `Tiene ${itemCount} item en el carrito.` :
                            `Tiene ${itemCount} items en el carrito.`;

                        $('#cantidad_items_misdatos').text(itemText);

                        if (itemCount > 0) {
                            $('#btn_pagar').html(`
                                <img src="#" alt="" class="image_transbank_carrito mt-5 mb-5">
                                <a href="/carrito" class="btn btn-warning btn-sm w-100">Ir a pagar</a>
                            `);
                        }
                    }

                    IPSApp.state.cart = cartData.repuestos;
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

                    items.forEach(function(item) {
                        const priceFormatted = Number(item.precio_venta).toFixed(0);
                        const total = item.cantidad * priceFormatted;
                        const image = `<img src="${IPSApp.config.apiBaseUrl}/storage/${item.urlfoto}" width="60" alt="${item.descripcion}">`;
                        const priceDisplay = '$' + IPSApp.utils.commaSeparateNumber(priceFormatted) +
                                           (item.oferta == 1 ? ' <span class="badge bg-danger">Oferta</span>' : '');

                        table.row.add([
                            image,
                            item.descripcion,
                            item.cantidad,
                            priceDisplay,
                            '$' + IPSApp.utils.commaSeparateNumber(total)
                        ]);
                    });

                    table.draw();
                },

                addItem: function() {
                    if (IPSApp.state.isAddingToCart) {
                        return false;
                    }

                    IPSApp.state.isAddingToCart = true;

                    const productId = $('#idrepuesto_modal').val();
                    const quantity = parseInt($('#cantidad').val());
                    const stock = parseInt($('#stock_repuesto').val());

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
                                $('#modalVistaRapida').modal('hide');
                                setTimeout(() => IPSApp.cart.show(), 500);
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
                    $('#stock_repuesto').val(1);
                    $('#stock_repuesto_').text('-');
                    $('#cantidad').val(1);
                    $('#vista_rapida_titulo').text('Vista Rápida del Repuesto');
                    $('#codigo_interno_repuesto_').text('-');
                    $('#marca_repuesto_').text('-');
                    $('#precio').text('-');
                    $('#carousel-inner').empty();
                    $('#preview_photos').empty();
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
                    $('#stock_repuesto').val(product.stock_actual);

                    // Set stock display
                    IPSApp.product.renderStock(product.stock_actual);

                    // Set price
                    IPSApp.product.renderPrice(product.precio_venta, discount);

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

                renderPrice: function(basePrice, discount) {
                    const priceFormatted = Number(basePrice).toFixed(0);
                    let priceHtml;

                    if (!discount || discount == 0) {
                        priceHtml = `<span class="fs-4">$${IPSApp.utils.commaSeparateNumber(priceFormatted)}</span>`;
                    } else {
                        priceHtml = `
                            <span class="fs-4">$${IPSApp.utils.commaSeparateNumber(discount.precio_actualizado)}</span>
                            <br><small class="text-muted text-decoration-line-through">$${IPSApp.utils.commaSeparateNumber(priceFormatted)}</small>
                            <span class="badge bg-danger ms-2">OFERTA</span>
                        `;
                    }

                    $('#precio').html(priceHtml);
                },

                renderAdditionalInfo: function(rectifiers, alternators) {
                    if (rectifiers && rectifiers.length > 0) {
                        $('#tr_rectificadores').removeClass('d-none');
                        $('#rectificadores').html(rectifiers.map(r => r.rectificador).join('<br>'));
                    } else {
                        $('#tr_rectificadores').addClass('d-none');
                    }

                    if (alternators && alternators.length > 0) {
                        $('#tr_alternadores').removeClass('d-none');
                        $('#alternadores').html(alternators.map(a => a.alternador).join('<br>'));
                    } else {
                        $('#tr_alternadores').addClass('d-none');
                    }
                },

                renderImages: function(photos, description) {
                    $('#carousel-inner').empty();
                    $('#preview_photos').empty();

                    if (photos && photos.length > 0) {
                        photos.forEach((photo, index) => {
                            const isActive = index === 0 ? 'active' : '';
                            const imageUrl = `${IPSApp.config.apiBaseUrl}/storage/${photo.urlfoto}`;

                            // Carousel item
                            $('#carousel-inner').append(`
                                <div class="carousel-item ${isActive}">
                                    <img src="${imageUrl}" class="d-block w-100"
                                         alt="${description}"
                                         style="height: 300px; object-fit: contain; background: #f8f9fa;"
                                         onerror="this.src='{{ asset('assets/images/no-image.png') }}'">
                                </div>
                            `);

                            // Thumbnail
                            $('#preview_photos').append(`
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
                        $('#carousel-inner').html(`
                            <div class="carousel-item active">
                                <img src="${defaultImage}" class="d-block w-100"
                                     alt="Sin imagen disponible"
                                     style="height: 300px; object-fit: contain; background: #f8f9fa;">
                            </div>
                        `);

                        $('#preview_photos').html(`
                            <div class="col-12 text-center">
                                <img src="${defaultImage}" class="img-thumbnail"
                                     alt="Sin imagen" style="height: 80px; object-fit: cover;">
                                <p class="small text-muted mt-1">Sin imágenes disponibles</p>
                            </div>
                        `);
                    }
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
                    const carousel = new bootstrap.Carousel(document.getElementById('carouselExampleControls'));
                    carousel.to(slideIndex);
                },

                increaseQuantity: function() {
                    const maxStock = parseInt($('#stock_repuesto').val());
                    const currentQuantity = parseInt($('#cantidad').val());

                    if (currentQuantity < maxStock) {
                        $('#cantidad').val(currentQuantity + 1);
                    } else {
                        $('#cantidad').val(maxStock);
                    }
                },

                decreaseQuantity: function() {
                    const currentQuantity = parseInt($('#cantidad').val());
                    if (currentQuantity > 1) {
                        $('#cantidad').val(currentQuantity - 1);
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

                // Setup CSRF token for all AJAX requests
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': IPSApp.config.csrfToken
                    }
                });

                // Initialize DataTable
                $('#table_detalle_nv_app').DataTable({
                    responsive: true,
                    language: {
                        url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
                    },
                    pageLength: 10,
                    order: []
                });

                // Initialize AOS
                AOS.init({
                    duration: 650,
                    once: true
                });

                // Load initial data
                IPSApp.auth.getUser();
                IPSApp.cart.review();

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

    <!-- MODALS -->

    <!-- Modal Vista Rápida Mejorado -->
    <div class="modal fade" id="modalVistaRapida" tabindex="-1" aria-labelledby="modalVistaRapidaLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-light">
                    <div class="d-flex align-items-center">
                        <img src="{{ asset('assets/images/logo_ips_colores.jpeg') }}" alt="IPS Logo"
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
                                    <div id="carouselExampleControls" class="carousel slide mb-3" data-bs-ride="carousel">
                                        <div class="carousel-inner rounded" id="carousel-inner" style="height: 300px;">
                                            <!-- Las imágenes se cargan dinámicamente -->
                                        </div>
                                        <button class="carousel-control-prev" type="button"
                                            data-bs-target="#carouselExampleControls" data-bs-slide="prev">
                                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                            <span class="visually-hidden">Anterior</span>
                                        </button>
                                        <button class="carousel-control-next" type="button"
                                            data-bs-target="#carouselExampleControls" data-bs-slide="next">
                                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                            <span class="visually-hidden">Siguiente</span>
                                        </button>
                                    </div>

                                    <!-- Miniaturas -->
                                    <div class="row g-2" id="preview_photos">
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
                                                    <td id="precio" class="fs-5 fw-bold text-success">-</td>
                                                </tr>
                                                <tr id="tr_rectificadores" class="d-none">
                                                    <td class="fw-bold">
                                                        <i class="fas fa-cog me-2 text-secondary"></i>Rectificadores
                                                    </td>
                                                    <td id="rectificadores" class="small text-muted"></td>
                                                </tr>
                                                <tr id="tr_alternadores" class="d-none">
                                                    <td class="fw-bold">
                                                        <i class="fas fa-bolt me-2 text-warning"></i>Alternadores
                                                    </td>
                                                    <td id="alternadores" class="small text-muted"></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>

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
                                                               name="cantidad" id="cantidad" min="1" value="1"
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
                    <input type="hidden" name="stock_repuesto" id="stock_repuesto">
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
    <div class="modal fade" id="modalLogin" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <img src="{{ asset('assets/images/logo_ips_colores.jpeg') }}" alt=""
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
                        SysMaFra 2025&copy; <cite title="Source Title">Todos los derechos reservados.</cite>
                    </figcaption>
                    <p class="text-center"></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Lateral Carrito -->
    <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasRight" aria-labelledby="offcanvasRightLabel">
        <div class="offcanvas-header">
            <img src="{{ asset('assets/images/logo_ips_colores.png') }}" alt="" class="logo_header">
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
                        <th>Imagen</th>
                        <th>Descripción</th>
                        <th>Cantidad</th>
                        <th>Precio Unitario</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>

            <div class="mt-3">
                <a href="/carrito" class="btn btn-outline-success btn-sm">
                    <i class="fas fa-shopping-cart"></i> Revisar carrito
                </a>
            </div>
        </div>
    </div>

    @yield('javascript')
</body>

</html>
