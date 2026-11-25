@extends('layouts.app')

@section('styles')
<link href="{{ asset('css/catalogo-repuestos.css') }}" rel="stylesheet">
<style>
    .product-image-trigger{padding:0;border:0;border-radius:.6rem;background:transparent;cursor:pointer}
    .product-image-trigger .img-thumbnail{transition:transform .2s ease,box-shadow .2s ease,border-color .2s ease}
    .product-image-trigger:hover .img-thumbnail,.product-image-trigger:focus-visible .img-thumbnail{transform:scale(1.06);border-color:#188c58;box-shadow:0 7px 18px rgba(24,140,88,.2)}
    #tablaRepuestos tbody tr.catalog-row-focus>td{animation:catalogRowFocus 1.4s ease}
    @keyframes catalogRowFocus{0%,100%{background-color:transparent}25%,75%{background-color:#e8f5ee}}
</style>
@endsection

@section('javascript')
    <script>
        $(document).ready(function() {
            // Inicializar componentes
            initializeComponents();

            // Cargar datos iniciales
            cargarRepuestos();
        });

        function initializeComponents() {
            // Inicializar DataTable
            if ($.fn.DataTable) {
                const tablaRepuestos = $('#tablaRepuestos').DataTable({
                    responsive: true,
                    processing: true,
                    order: [[2, 'asc']],
                    autoWidth: false,
                    language: {
                        url: '/assets/datatables/es-ES.json', // Usar archivo local para evitar CORS
                        paginate: {
                            previous: 'Anterior',
                            next: 'Siguiente'
                        }
                    },
                    columnDefs: [
                        { orderable: false, targets: [0, 6] }, // Imagen y Acciones no ordenables
                        { width: "80px", targets: 0 }, // Ancho fijo para imagen
                        { width: "175px", targets: 6 } // Ancho fijo para acciones
                    ],
                    pageLength: 12,
                    lengthChange: false,
                    dom: '<"row"<"col-sm-12"B>>' + // Botones en fila completa
                         '<"row"<"col-sm-12"f>>' + // Buscador en fila completa
                         '<"row"<"col-sm-12"tr>>' + // Tabla
                         '<"row"<"col-sm-5"i><"col-sm-7"p>>', // Info y paginación
                    buttons: [
                        'copy', 'csv', 'excel', 'pdf', 'print'
                    ],
                    initComplete: function() {
                        // Personalizar el input de búsqueda
                        $('.dataTables_filter input')
                            .addClass('form-control form-control-lg')
                            .attr('placeholder', 'Buscar por código o descripción...')
                            .attr('aria-label', 'Buscar repuestos por código, descripción o marca')
                            .css('width', '100%');

                        // Agregar icono de búsqueda
                        $('.dataTables_filter label').prepend('<i class="fas fa-search me-2"></i>');

                        // Mejorar el estilo del label
                        $('.dataTables_filter label').css({
                            'width': '100%',
                            'display': 'flex',
                            'align-items': 'center'
                        });

                        // Reunir el buscador y el selector de vista para aprovechar
                        // mejor el espacio horizontal del encabezado del catálogo.
                        const controlesCatalogo = $('#tablaRepuestos_filter').closest('.row');
                        controlesCatalogo.addClass('catalog-controls-row');
                        $('.catalog-toolbar').appendTo(controlesCatalogo.children().first());

                        const pieTabla = $('#tablaRepuestos_wrapper > .row').last().addClass('catalog-footer');
                        $('#catalogCards').insertBefore(pieTabla);
                        aplicarVistaCatalogo(localStorage.getItem('catalogView') || 'table');
                        actualizarPaginaActiva();
                    }
                });
                tablaRepuestos.on('draw.dt', function() {
                    renderizarTarjetas();
                    actualizarPaginaActiva();
                });
                configurarReposicionPaginacion($('#tablaRepuestos'));
            }

            // Inicializar slider si existe
            if ($("#slider-range").length) {
                $("#slider-range").slider({
                    range: true,
                    min: 0,
                    max: 900000,
                    step: 5000,
                    values: [0, 200000],
                    slide: function(event, ui) {
                        $("#amount").val("$ " + formatNumber(ui.values[0]) + " - $ " + formatNumber(ui.values[1]));
                    }
                });

                $("#amount").val("$ " + formatNumber($("#slider-range").slider("values", 0)) +
                    " - $ " + formatNumber($("#slider-range").slider("values", 1)));
            }

            // Inicializar slider de imágenes si existe
            if ($('.slider').length) {
                $('.slider').slick({
                    slidesToShow: 4,
                    slidesToScroll: 4,
                    autoplay: true,
                    autoplaySpeed: 2000,
                    responsive: [
                        {
                            breakpoint: 845,
                            settings: {
                                slidesToShow: 3,
                                slidesToScroll: 3
                            }
                        },
                        {
                            breakpoint: 768,
                            settings: {
                                slidesToShow: 2,
                                slidesToScroll: 2
                            }
                        },
                        {
                            breakpoint: 480,
                            settings: {
                                slidesToShow: 1,
                                slidesToScroll: 1
                            }
                        }
                    ],
                    prevArrow: '<button class="slick-prev my-2 bg-danger text-warning rounded" aria-label="Anterior"><i class="fa-solid fa-arrow-left"></i></button>',
                    nextArrow: '<button class="slick-next my-2 bg-danger text-warning rounded" aria-label="Siguiente"><i class="fa-solid fa-arrow-right"></i></button>'
                });
            }
        }

        function configurarReposicionPaginacion(tabla) {
            let debeReposicionar = false;
            tabla.on('page.dt', () => debeReposicionar = true);
            tabla.on('draw.dt', function() {
                if (!debeReposicionar) return;
                debeReposicionar = false;
                window.setTimeout(() => desplazarAlPrimerProducto(tabla), 120);
            });

            $(document)
                .off('click.catalogPagination', '#tablaRepuestos_wrapper .paginate_button, #tablaRepuestos_wrapper .page-link')
                .on('click.catalogPagination', '#tablaRepuestos_wrapper .paginate_button, #tablaRepuestos_wrapper .page-link', function() {
                    if ($(this).hasClass('disabled') || $(this).closest('.page-item').hasClass('disabled')) return;
                    window.setTimeout(() => desplazarAlPrimerProducto(tabla), 180);
                });
        }

        function desplazarAlPrimerProducto(tabla) {
            const vistaTarjetas = !$('#catalogCards').hasClass('d-none');
            const primerProducto = vistaTarjetas
                ? document.querySelector('#catalogCards .catalog-product-card')
                : tabla.find('tbody tr').get(0);

            if (!primerProducto || primerProducto.classList.contains('dataTables_empty')) return;

            primerProducto.scrollIntoView({behavior: 'smooth', block: 'start'});

            if (!vistaTarjetas) {
                primerProducto.setAttribute('tabindex', '-1');
                primerProducto.classList.add('catalog-row-focus');
                primerProducto.focus({preventScroll: true});
                window.setTimeout(() => primerProducto.classList.remove('catalog-row-focus'), 1400);
            }
        }

        function actualizarPaginaActiva() {
            window.requestAnimationFrame(function() {
                const selectoresActivos = '#tablaRepuestos_wrapper .page-item.active .page-link, #tablaRepuestos_wrapper .paginate_button.current, #tablaRepuestos_wrapper .paginate_button.current a';
                document.querySelectorAll(selectoresActivos).forEach(function(elemento) {
                    elemento.style.setProperty('color', '#ffffff', 'important');
                    elemento.style.setProperty('-webkit-text-fill-color', '#ffffff', 'important');
                });
            });
        }

        function cargarRepuestos() {
            const token = localStorage.getItem('token');
            const requestHeaders = {"Accept": "application/json"};

            if (token) {
                requestHeaders.Authorization = 'Bearer ' + token;
            }

            $.ajax({
                type: 'GET',
                url: '{{ config("app.api_url", "https://intranet.ipschile.cl") }}/api/repuestos',
                beforeSend: function() {
                    mostrarCargando();
                },
                headers: requestHeaders,
                data: { token: token }, // Enviar el token también en el cuerpo
                data: token ? {token: token} : {},
                success: function(response) {
                    console.log('Repuestos cargados:', response);
                    ocultarCargando();
                    if (response.status === 'Token is Invalid' || response.status === 'Token is Expired') {
                        manejarTokenInvalido(response.status);
                        return;
                    }
                    if (response && response[0]) {
                        cargarDatosEnTabla(response[0]);
                    } else {
                        mostrarError('No se encontraron repuestos');
                    }
                },
                error: function(xhr) {
                    ocultarCargando();
                    console.error('Error al cargar repuestos:', xhr);
                    if (xhr.status === 401) {
                        manejarTokenInvalido('Token is Invalid');
                    } else {
                        mostrarError('Error al cargar los repuestos. Por favor, intente nuevamente.');
                    }
                }
            });
        }

        function cargarDatosEnTabla(repuestos) {
            const tabla = $('#tablaRepuestos').DataTable();
            tabla.clear();

            repuestos.sort(function(a, b) {
                return (a.descripcion || '').localeCompare(b.descripcion || '', 'es', {
                    sensitivity: 'base',
                    numeric: true
                });
            });
            $('#catalogCount').text(formatNumber(repuestos.length));

            repuestos.forEach(function(item) {
                const precioDisplay = generarPrecioCatalogo(item);
                const imagen = `<button type="button" class="product-image-trigger"
                               onclick="vista_rapida(${item.id})" data-bs-toggle="modal"
                               data-bs-target="#modalVistaRapida" title="Vista rápida">
                               <img src="https://intranet.ipschile.cl/storage/${item.urlfoto}"
                               width="60" class="img-thumbnail"
                               onerror="this.src='{{ asset('assets/images/no-image.png') }}'"
                               alt="${item.descripcion}"></button>`;
                const stockDisplay = item.stock_actual > 100 ? 'DISPONIBLE' : item.stock_actual;
                const acciones = `
                <div class="d-flex align-items-center">
                    <button class="btn btn-outline-success btn-sm"
                            onclick="vista_rapida(${item.id})"
                            data-bs-toggle="modal"
                            data-bs-target="#modalVistaRapida"
                            title="Vista rápida">
                        <i class="fas fa-eye"></i>
                    </button>
                    <input type="number"
                           id="cantidad_${item.id}"
                           class="form-control d-inline-block mx-1"
                           style="width: 75px;"
                           min="1"
                           value=""
                           title="Cantidad">
                    <button class="btn btn-outline-primary btn-sm ms-1"
                            onclick="agregarCarrito(${item.id})"
                            title="Agregar al carrito">
                        <i class="fas fa-cart-plus"></i>
                    </button>
                </div>
                `;

                tabla.row.add([
                    imagen,
                    item.codigo_interno || 'N/A',
                    item.descripcion || 'Sin descripción',
                    item.marcarepuesto || 'Sin marca',
                    precioDisplay,
                    stockDisplay,
                    acciones
                ]);
            });

            tabla.draw();
        }

        function cambiarVistaCatalogo(vista) {
            localStorage.setItem('catalogView', vista);
            aplicarVistaCatalogo(vista);
        }

        function aplicarVistaCatalogo(vista) {
            const mostrarTarjetas = vista === 'cards';
            $('#tablaRepuestos').toggleClass('catalog-table-hidden', mostrarTarjetas);
            $('#catalogCards').toggleClass('d-none', !mostrarTarjetas);
            $('.catalog-view-button')
                .removeClass('is-active')
                .attr('aria-pressed', 'false');
            $(`.catalog-view-button[data-view="${mostrarTarjetas ? 'cards' : 'table'}"]`)
                .addClass('is-active')
                .attr('aria-pressed', 'true');
            renderizarTarjetas();
        }

        function renderizarTarjetas() {
            if (!$.fn.DataTable || !$.fn.DataTable.isDataTable('#tablaRepuestos')) return;

            const tabla = $('#tablaRepuestos').DataTable();
            const filas = tabla.rows({ page: 'current', search: 'applied', order: 'applied' }).data().toArray();
            const contenedor = $('#catalogCards');

            if (!filas.length) {
                contenedor.html('<div class="catalog-cards-empty"><i class="fas fa-search"></i><p>No se encontraron repuestos.</p></div>');
                return;
            }

            contenedor.html(filas.map(function(fila) {
                const accionesTarjeta = fila[6]
                    .replace('cantidad_', 'cantidad_card_')
                    .replace(/onclick="agregarCarrito\((\d+)\)"/, 'onclick="agregarCarrito($1, \'card\')"');
                return `
                    <article class="catalog-product-card">
                        <div class="catalog-product-card__image">${fila[0]}</div>
                        <div class="catalog-product-card__body">
                            <span class="catalog-product-card__code">Código: ${fila[1]}</span>
                            <h3>${fila[2]}</h3>
                            <div class="catalog-product-card__brand">${fila[3]}</div>
                            <div class="catalog-product-card__meta">
                                <div class="catalog-product-card__price">${fila[4]}</div>
                                <span class="catalog-product-card__stock"><i class="fas fa-box"></i> ${fila[5]}</span>
                            </div>
                            <div class="catalog-product-card__actions">${accionesTarjeta}</div>
                        </div>
                    </article>`;
            }).join(''));
        }

        function generarPrecioCatalogo(item) {
            const primerPrecioValido = (...valores) => valores.map(Number).find(valor => valor > 0) || 0;
            const precioNormal = primerPrecioValido(item.precio_original, item.precio_venta);
            const precioOferta = primerPrecioValido(item.precio_final, item.precio_actualizado, item.precio_venta);
            const porcentaje = Number(item.porcentaje_descuento_web || item.porcentaje_descuento || 0);
            const tieneDescuento = precioNormal > 0 && precioOferta > 0 && precioOferta < precioNormal;

            if (!tieneDescuento) {
                const badgeOferta = item.oferta == 1 ? ' <span class="badge bg-danger">Oferta</span>' : '';
                return `<div data-price="${precioNormal}"><strong>$${formatNumber(precioNormal)}</strong>${badgeOferta}</div>`;
            }

            const badgeDescuento = porcentaje > 0
                ? `<span class="badge bg-success ms-1">-${porcentaje}%</span>`
                : '<span class="badge bg-success ms-1">Oferta</span>';

            return `
                <div data-price="${precioOferta}" class="catalog-price">
                    <small class="text-muted d-block">Normal: <span class="text-decoration-line-through">$${formatNumber(precioNormal)}</span></small>
                    <strong class="text-success">Nuevo precio: $${formatNumber(precioOferta)}</strong>
                    ${badgeDescuento}
                </div>
            `;
        }

        function vistaRapida(idRepuesto) {
            // Implementar vista rápida
            console.log('Vista rápida del repuesto:', idRepuesto);
            // Aquí puedes cargar los datos del repuesto específico
        }

        function agregarCarrito(idRepuesto, origen = 'table') {
            const token = localStorage.getItem('token');
            const inputCantidad = origen === 'card' ? `#cantidad_card_${idRepuesto}` : `#cantidad_${idRepuesto}`;
            const cantidad = parseInt($(inputCantidad).val()) || 1;

            if (!token) {
                mostrarError('Debe iniciar sesión para agregar productos al carrito');
                return;
            }

            if(cantidad < 1) {
                mostrarError('La cantidad debe ser al menos 1');
                return;
            }

            const url = "{{ env('URL_AGREGAR_CARRITO') }}";
            const data = {
                idrep: idRepuesto,
                cantidad: cantidad,
                token: token
            };

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                type: 'POST',
                url: url,
                data: data,
                headers: {
                    "Authorization": 'Bearer ' + token
                },
                beforeSend: function() {
                    mostrarCargando();
                },
                success: function(response) {
                    if (response.message === 'OK') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Repuesto agregado al carrito',
                            timer: 2000,
                            showConfirmButton: false
                        });
                        // Actualizar contador del carrito global
                        if (typeof revisar_carrito === 'function') {
                            revisar_carrito();
                        }
                        // Actualizar contador del carrito si existe
                        if (typeof actualizarCarrito === 'function') {
                            actualizarCarrito();
                        }
                    }
                },
                error: function(xhr) {
                    const mensaje = xhr.responseJSON?.message || 'Error al agregar el producto al carrito';
                    mostrarError(mensaje);
                }
            });
        }

        function filtrarPorRango() {
            if (!$("#slider-range").length) return;

            const minimo = $("#slider-range").slider("values", 0);
            const maximo = $("#slider-range").slider("values", 1);

            // Filtrar DataTable por rango de precios
            $.fn.dataTable.ext.search.push(
                function(settings, data, dataIndex) {
                    const priceMatch = data[4].match(/data-price=["']([0-9.]+)["']/);
                    const precio = priceMatch ? Number(priceMatch[1]) : (parseInt(data[4].replace(/[^0-9]/g, '')) || 0);
                    return precio >= minimo && precio <= maximo;
                }
            );

            $('#tablaRepuestos').DataTable().draw();

            // Remover filtro después de aplicarlo
            $.fn.dataTable.ext.search.pop();
        }

        // Funciones de utilidad
        function formatNumber(number) {
            return new Intl.NumberFormat('es-CL').format(number);
        }

        function mostrarCargando() {
            Swal.fire({
                icon: 'info',
                text: 'Cargando...',
                showConfirmButton: false,
                allowOutsideClick: false,
                allowEscapeKey: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
        }

        function ocultarCargando() {
            Swal.close();
        }

        function mostrarError(mensaje) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: mensaje
            });
        }

        function manejarTokenInvalido(status) {
            const mensaje = status === 'Token is Expired'
                ? 'Su sesión ha expirado. Debe volver a ingresar.'
                : 'Debe iniciar sesión';

            Swal.fire({
                icon: 'warning',
                title: 'Sesión requerida',
                text: mensaje
            }).then(() => {
                redirigirLogin();
            });
        }

        function redirigirLogin() {
            setTimeout(() => {
                window.location.href = "/login";
            }, 1000);
        }

        // Función para buscar repuestos
        function buscarRepuestos() {
            const termino = $('#buscadorRepuestos').val().trim();

            if (termino.length >= 3) {
                const tabla = $('#tablaRepuestos').DataTable();
                tabla.search(termino).draw();
            } else if (termino.length === 0) {
                const tabla = $('#tablaRepuestos').DataTable();
                tabla.search('').draw();
            }
        }

        // Event listeners
        $(document).on('keyup', '#buscadorRepuestos', function() {
            buscarRepuestos();
        });

        $(document).on('click', '#btnFiltrarPrecio', function() {
            filtrarPorRango();
        });
    </script>
@endsection

@section('content')
    <header class="catalog-hero">
        <div class="container-fluid px-3 px-lg-5">
            <nav class="catalog-breadcrumb" aria-label="Migas de pan"><a href="/">Inicio</a><i class="fas fa-chevron-right"></i><span>Repuestos</span></nav>
            <div class="catalog-hero__row">
                <div>
                    <span class="catalog-eyebrow">Catálogo IPS Autopartes</span>
                    <h1>Encuentra el repuesto que necesitas</h1>
                    <p>Busca por descripción, código o marca. El catálogo está ordenado alfabéticamente para que encuentres cada producto más rápido.</p>
                </div>
                <div class="catalog-stat"><strong id="catalogCount">—</strong><span>productos disponibles</span></div>
            </div>
        </div>
    </header>
    <div class="container-fluid catalog-container px-3 px-lg-5" id="contenedor">
        <!-- Breadcrumb -->
        {{-- <nav style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='currentColor'/%3E%3C/svg%3E&#34;);" aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('/') }}">Inicio</a></li>
                <li class="breadcrumb-item active" aria-current="page">Repuestos</li>
            </ol>
        </nav> --}}

        <section class="row">
            <!-- Sidebar de filtros -->
            {{-- <div class="col-md-12 col-lg-3">
                <!-- Buscador -->
                <div class="card mb-3">
                    <div class="card-header">
                        <h6 class="card-title mb-0">
                            <i class="fas fa-search me-2"></i>Buscar Repuestos
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="input-group">
                            <input type="text"
                                   class="form-control"
                                   id="buscadorRepuestos"
                                   placeholder="Buscar por descripción, marca, código..."
                                   autocomplete="off">
                            <button class="btn btn-outline-secondary" type="button">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                        <small class="text-muted">Ingrese al menos 3 caracteres</small>
                    </div>
                </div>

                <!-- Filtro por precio -->
                <div class="card mb-3">
                    <div class="card-header">
                        <h6 class="card-title mb-0">
                            <i class="fas fa-dollar-sign me-2"></i>Filtro por Precio
                        </h6>
                    </div>
                    <div class="card-body text-center">
                        <label for="amount" class="form-label">Rango de precios:</label>
                        <input type="text"
                               id="amount"
                               name="range"
                               class="form-control text-center mb-3"
                               readonly
                               style="border: 0; background: transparent; font-weight: bold;" />
                        <div id="slider-range" class="mb-3"></div>
                        <button type="button"
                                id="btnFiltrarPrecio"
                                class="btn btn-primary btn-sm w-100">
                            <i class="fas fa-filter me-1"></i>Aplicar Filtro
                        </button>
                    </div>
                </div>

                <!-- Filtros adicionales (comentados para uso futuro) -->
                <!--
                <div class="card mb-3">
                    <div class="card-header">
                        <h6 class="card-title mb-0">Familias</h6>
                    </div>
                    <div class="card-body">
                        <p class="text-muted small text-center">Seleccione una familia:</p>
                        <div id="container_familias"></div>
                    </div>
                </div>
                -->
            </div> --}}

            <!-- Contenido principal -->
            <div class="col-md-12 col-lg-12">
                <div class="card catalog-card">
                    <div class="card-header catalog-card__header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-cogs me-2"></i>Catálogo de Repuestos
                        </h5>
                        <div class="d-flex gap-2">
                            <button class="catalog-refresh" onclick="cargarRepuestos()" title="Actualizar catálogo">
                                <i class="fas fa-sync-alt"></i><span>Actualizar</span>
                            </button>
                        </div>
                    </div>
                    <div class="card-body catalog-card__body">
                        <div class="catalog-toolbar" aria-label="Tipo de vista del catálogo">
                            <span>Ver como</span>
                            <div class="catalog-view-switch" role="group">
                                <button type="button" class="catalog-view-button" data-view="table" onclick="cambiarVistaCatalogo('table')" aria-pressed="true">
                                    <i class="fas fa-list"></i><span>Tabla</span>
                                </button>
                                <button type="button" class="catalog-view-button" data-view="cards" onclick="cambiarVistaCatalogo('cards')" aria-pressed="false">
                                    <i class="fas fa-th"></i><span>Tarjetas</span>
                                </button>
                            </div>
                        </div>
                        <div id="catalogTableContainer" class="table-responsive">
                            <table id="tablaRepuestos" class="table table-striped table-hover" style="width:100%">
                                <thead class="table">
                                    <tr>
                                        <th>Imagen</th>
                                        <th>Código</th>
                                        <th>Descripción</th>
                                        <th>Marca</th>
                                        <th>Precio</th>
                                        <th>Stock</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Los datos se cargan dinámicamente -->
                                </tbody>
                            </table>
                        </div>
                        <div id="catalogCards" class="catalog-products-grid d-none" aria-live="polite"></div>
                    </div>
                </div>
            </div>
        </section>
    </div>


@endsection

@push('styles')
<style>
    .card {
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        border: 1px solid rgba(0, 0, 0, 0.125);
    }

    .card-header {
        background-color: #f8f9fa;
        border-bottom: 1px solid rgba(0, 0, 0, 0.125);
    }

    .table th {
        font-weight: 600;
        border-top: none;
    }

    .img-thumbnail {
        border-radius: 0.375rem;
    }

    #slider-range {
        margin: 1rem 0;
    }

    .ui-slider .ui-slider-handle {
        background: #007bff;
        border-color: #007bff;
    }

    .ui-slider .ui-slider-range {
        background: #007bff;
    }

        /* Hacer que el buscador ocupe todo el ancho */
    div.dataTables_wrapper div.dataTables_filter {
        width: 100%;
        text-align: left !important;
    }

    div.dataTables_wrapper div.dataTables_filter input {
        width: 100% !important;
        max-width: 100%;
    }

    /* Opcional: Mejorar el diseño general */
    div.dataTables_wrapper div.dataTables_filter label {
        width: 100%;
        display: flex;
        align-items: center;
        gap: 10px;
    }


    @media (max-width: 768px) {
        .table-responsive {
            font-size: 0.875rem;
        }
    }
</style>
@endpush
