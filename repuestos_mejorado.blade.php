@extends('layouts.app')

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
                $('#tablaRepuestos').DataTable({
                    responsive: true,
                    processing: true,
                    language: {
                        url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
                    },
                    columnDefs: [
                        { orderable: false, targets: [0, 6] }, // Imagen y Acciones no ordenables
                        { width: "80px", targets: 0 }, // Ancho fijo para imagen
                        { width: "120px", targets: 6 } // Ancho fijo para acciones
                    ],
                    pageLength: 25,
                    dom: 'Bfrtip',
                    buttons: [
                        'copy', 'csv', 'excel', 'pdf', 'print'
                    ]
                });
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

        function cargarRepuestos() {
            const token = localStorage.getItem('token');

            if (!token) {
                mostrarError('Debe iniciar sesión para ver los repuestos');
                redirigirLogin();
                return;
            }

            $.ajax({
                type: 'GET',
                url: '{{ config("app.api_url", "https://intranet.ipschile.cl") }}/api/repuestos',
                beforeSend: function() {
                    mostrarCargando();
                },
                headers: {
                    "Authorization": 'Bearer ' + token,
                    "Accept": "application/json"
                },
                success: function(response) {
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

            repuestos.forEach(function(item) {
                const precioFormateado = formatNumber(parseInt(item.precio_venta));
                const imagen = `<img src="https://intranet.ipschile.cl/storage/${item.urlfoto}"
                               width="60" class="img-thumbnail"
                               onerror="this.src='{{ asset('assets/images/no-image.png') }}'"
                               alt="${item.descripcion}">`;
                const stockDisplay = item.stock_actual > 100 ? 'DISPONIBLE' : item.stock_actual;
                const ofertagBadge = item.oferta == 1 ? ' <span class="badge bg-danger">Oferta</span>' : '';
                const acciones = `
                    <button class="btn btn-outline-success btn-sm"
                            onclick="vistaRapida(${item.id})"
                            data-bs-toggle="modal"
                            data-bs-target="#modalVistaRapida"
                            title="Vista rápida">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button class="btn btn-outline-primary btn-sm ms-1"
                            onclick="agregarCarrito(${item.id})"
                            title="Agregar al carrito">
                        <i class="fas fa-cart-plus"></i>
                    </button>
                `;

                tabla.row.add([
                    imagen,
                    item.codigo_interno || 'N/A',
                    item.descripcion || 'Sin descripción',
                    item.marcarepuesto || 'Sin marca',
                    '$' + precioFormateado + ofertagBadge,
                    stockDisplay,
                    acciones
                ]);
            });

            tabla.draw();
        }

        function vistaRapida(idRepuesto) {
            // Implementar vista rápida
            console.log('Vista rápida del repuesto:', idRepuesto);
            // Aquí puedes cargar los datos del repuesto específico
        }

        function agregarCarrito(idRepuesto) {
            const token = localStorage.getItem('token');

            if (!token) {
                mostrarError('Debe iniciar sesión para agregar productos al carrito');
                return;
            }

            const url = "{{ env('URL_AGREGAR_CARRITO') }}";
            const data = {
                idrep: idRepuesto,
                cantidad: 1
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
                    const precio = parseInt(data[4].replace(/[^0-9]/g, '')) || 0;
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
    <div class="container-fluid" id="contenedor" data-aos="flip-up">
        <!-- Breadcrumb -->
        <nav style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='currentColor'/%3E%3C/svg%3E&#34;);" aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('/') }}">Inicio</a></li>
                <li class="breadcrumb-item active" aria-current="page">Repuestos</li>
            </ol>
        </nav>

        <section class="row">
            <!-- Sidebar de filtros -->
            <div class="col-md-12 col-lg-3">
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
            </div>

            <!-- Contenido principal -->
            <div class="col-md-12 col-lg-9">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-cogs me-2"></i>Catálogo de Repuestos
                        </h5>
                        <div class="d-flex gap-2">
                            <button class="btn btn-outline-secondary btn-sm" onclick="cargarRepuestos()" title="Actualizar">
                                <i class="fas fa-sync-alt"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="tablaRepuestos" class="table table-striped table-hover" style="width:100%">
                                <thead class="table-dark">
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
                    </div>
                </div>
            </div>
        </section>
    </div>

    <!-- Modal para vista rápida (placeholder) -->
    <div class="modal fade" id="modalVistaRapida" tabindex="-1" aria-labelledby="modalVistaRapidaLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalVistaRapidaLabel">Vista Rápida del Repuesto</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Contenido del modal se carga dinámicamente -->
                    <div class="text-center">
                        <div class="spinner-border" role="status">
                            <span class="visually-hidden">Cargando...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
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

    @media (max-width: 768px) {
        .table-responsive {
            font-size: 0.875rem;
        }
    }
</style>
@endpush
