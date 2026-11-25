@extends('layouts.app')

@section('styles')
    <link href="{{ asset('css/catalogo-repuestos.css') }}" rel="stylesheet">
    <link href="{{ asset('css/buscador-repuestos.css') }}" rel="stylesheet">
    <style>
        .search-price { display: flex; flex-direction: column; align-items: center; gap: 2px; line-height: 1.2; }
        .search-price__normal { color: #89969b; font-weight: 600; }
        .search-price__offer { color: #158c58; font-size: 1.08rem; }
        .search-price .badge { margin-top: 3px; font-size: .67rem; }
        #vista-cards .search-price { align-items: flex-start; }
        .product-image-trigger{padding:0;border:0;border-radius:.6rem;background:transparent;cursor:pointer}
        .product-image-trigger .img-thumbnail{transition:transform .2s ease,box-shadow .2s ease,border-color .2s ease}
        .product-image-trigger:hover .img-thumbnail,.product-image-trigger:focus-visible .img-thumbnail{transform:scale(1.06);border-color:#188c58;box-shadow:0 7px 18px rgba(24,140,88,.2)}
        #tablaRepuestos tbody tr.catalog-row-focus>td{animation:catalogRowFocus 1.4s ease}
        @keyframes catalogRowFocus{0%,100%{background-color:transparent}25%,75%{background-color:#e8f5ee}}
    </style>
@endsection

@section('javascript')
    <script>
        // Configuración global
        const CONFIG = {
            baseApiUrl: 'https://intranet.ipschile.cl/api',
            imageBaseUrl: 'https://intranet.ipschile.cl/storage',
            defaultPageSize: 12,
            maxDisplayStock: 100
        };

        // Variables globales
        let currentTag = '';
        let tablaRepuestos = null;
        let currentView = 'table'; // 'table' o 'cards'

        // Inicialización cuando el documento esté listo
        $(document).ready(function() {
            inicializarComponentes();
            extraerTagDeURL();

            if (currentTag) {
                cargarRepuestos(currentTag);
            }
        });

        /**
         * Inicializa todos los componentes de la página
         */
        function inicializarComponentes() {
            inicializarSliderPrecio();
            inicializarDataTable();
            configurarEventos();
        }

        /**
         * Configura el slider de rango de precios
         */
        function inicializarSliderPrecio() {
            $("#slider-range").slider({
                range: true,
                min: 0,
                max: 600000,
                step: 5000,
                values: [0, 50000],
                slide: function(event, ui) {
                    $("#amount").val("$ " + formatearNumero(ui.values[0]) + " - $ " + formatearNumero(ui.values[1]));
                }
            });

            $("#amount").val("$ " + formatearNumero($("#slider-range").slider("values", 0)) +
                " - $ " + formatearNumero($("#slider-range").slider("values", 1)));
        }

        /**
         * Inicializa DataTable
         */
        function inicializarDataTable() {
            tablaRepuestos = $('#tablaRepuestos').DataTable({
                responsive: true,
                language: {
                    url: '/assets/datatables/es-ES.json'
                },
                pageLength: 25,
                order: [[3, 'asc']], // Ordenar por descripción
                columnDefs: [
                    { orderable: false, targets: [0, 6] }, // Imagen y acciones no ordenables
                    { className: "text-center", targets: [0, 4, 5, 6] }
                ]
            });
            configurarReposicionPaginacion($('#tablaRepuestos'));
        }

        function configurarReposicionPaginacion(tabla) {
            let debeReposicionar = false;
            tabla.on('page.dt', () => debeReposicionar = true);
            tabla.on('draw.dt', function() {
                if (!debeReposicionar) return;
                debeReposicionar = false;
                window.requestAnimationFrame(function() {
                    const primeraFila = tabla.find('tbody tr').get(0);
                    if (!primeraFila || primeraFila.classList.contains('dataTables_empty')) return;
                    primeraFila.setAttribute('tabindex', '-1');
                    primeraFila.classList.add('catalog-row-focus');
                    primeraFila.scrollIntoView({behavior: 'smooth', block: 'center'});
                    primeraFila.focus({preventScroll: true});
                    window.setTimeout(() => primeraFila.classList.remove('catalog-row-focus'), 1400);
                });
            });
        }

        /**
         * Configura eventos de la página
         */
        function configurarEventos() {
            // Toggle entre vista tabla y cards
            $('#btnVistaTabla').on('click', () => cambiarVista('table'));
            $('#btnVistaCards').on('click', () => cambiarVista('cards'));

            // Filtro de marca
            $('#marcas_repuestos').on('change', function() {
                const marca = $(this).val();
                if (marca && marca !== '0') {
                    filtrarPorMarca(marca);
                }
            });
        }

        /**
         * Extrae el tag de búsqueda de la URL
         */
        function extraerTagDeURL() {
            const url = window.location.pathname;
            const segments = url.split('/');
            currentTag = segments[1] || '';

            if (currentTag) {
                // Decodificar URL y mostrar tag buscado
                const tagDecodificado = decodeURIComponent(currentTag).replace(/\+/g, ' ');
                mostrarTagBuscado(tagDecodificado);
            }
        }

        /**
         * Muestra el tag buscado en la interfaz
         */
        function mostrarTagBuscado(tag) {
            $('#searchTermTitle').text(`“${tag}”`);
            $('#datos_busqueda').html(`
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-search"></i> Búsqueda actual
                        </h5>
                    </div>
                    <div class="card-body">
                        <p class="mb-0"><strong>"${tag}"</strong></p>
                    </div>
                </div>
            `);
        }

        /**
         * Carga repuestos desde la API
         */
        function cargarRepuestos(tag) {
            const token = localStorage.getItem('token');
            if (!token) {
                mostrarErrorAutenticacion();
                return;
            }

            const url = `${CONFIG.baseApiUrl}/${tag}/buscador`;

            mostrarEstadoCarga(true, 'Buscando repuestos...');

            $.ajax({
                type: 'GET',
                url: url,
                data:{token: token},
                headers: {
                    "Authorization": 'Bearer ' + token
                },
                success: function(response) {
                    manejarRespuestaExitosa(response);
                },
                error: function(xhr) {
                    manejarErrorAPI(xhr);
                },
                complete: function() {
                    mostrarEstadoCarga(false);
                }
            });
        }

        /**
         * Maneja respuesta exitosa de la API
         */
        function manejarRespuestaExitosa(response) {
            if (validarTokenRespuesta(response)) {
                return;
            }

            const repuestos = response[0] || [];

            if (repuestos.length === 0) {
                mostrarSinResultados();
                return;
            }

            cargarRepuestosEnTabla(repuestos);
            cargarMarcasDisponibles(repuestos);

            // Mostrar mensaje de resultados
            mostrarMensajeResultados(repuestos.length);
        }

        /**
         * Valida si la respuesta indica problemas con el token
         */
        function validarTokenRespuesta(response) {
            if (response.status === 'Token is Invalid' || response.status === 'Token is Expired') {
                Swal.fire({
                    icon: 'warning',
                    title: 'Sesión Expirada',
                    text: 'Debe iniciar sesión nuevamente',
                    confirmButtonText: 'Ir al Login'
                }).then(() => {
                    window.location.href = "/login";
                });
                return true;
            }
            return false;
        }

        /**
         * Carga repuestos en la tabla DataTable
         */
        function cargarRepuestosEnTabla(repuestos) {
            tablaRepuestos.clear();

            repuestos.forEach(function(item) {
                const imagen = `<button type="button" class="product-image-trigger"
                              onclick="vista_rapida(${item.id})" data-bs-toggle="modal"
                              data-bs-target="#modalVistaRapida" title="Vista rápida">
                              <img src="${CONFIG.imageBaseUrl}/${item.urlfoto}"
                              width="50" height="50" class="img-thumbnail"
                              onerror="this.src='{{ asset('assets/images/no-image.png') }}'"
                              alt="${item.descripcion}"></button>`;

                const stock = item.stock_actual > CONFIG.maxDisplayStock ? 'DISPONIBLE' : item.stock_actual;
                const precioFormateado = generarPrecioProducto(item);

                const acciones = `
                    <button class="btn btn-outline-primary btn-sm"
                            onclick="vista_rapida(${item.id})"
                            data-bs-toggle="modal"
                            data-bs-target="#modalVistaRapida"
                            title="Vista rápida">
                        <i class="fas fa-eye"></i>
                    </button>
                    <a href="/repuesto/${item.id}"
                       class="btn btn-outline-info btn-sm ms-1"
                       title="Ver detalle">
                        <i class="fas fa-info-circle"></i>
                    </a>
                `;

                tablaRepuestos.row.add([
                    imagen,
                    item.codigo_interno || '-',
                    item.marcarepuesto || '-',
                    item.descripcion || '-',
                    precioFormateado,
                    stock,
                    acciones
                ]);
            });

            tablaRepuestos.draw();
        }

        function generarPrecioProducto(item) {
            const primerPrecioValido = (...valores) => valores.map(Number).find(valor => valor > 0) || 0;
            const precioNormal = primerPrecioValido(item.precio_original, item.precio_venta);
            const precioOferta = primerPrecioValido(item.precio_final, item.precio_actualizado, item.precio_venta);
            const porcentaje = Number(item.porcentaje_descuento_web || 0);

            if (!(precioOferta > 0 && precioOferta < precioNormal)) {
                return `<div class="search-price" data-price="${precioNormal}"><strong>$${formatearNumero(precioNormal)}</strong></div>`;
            }

            const etiqueta = porcentaje > 0 ? `-${porcentaje}% cliente` : 'Oferta';
            return `
                <div class="search-price" data-price="${precioOferta}">
                    <small class="search-price__normal">Normal <del>$${formatearNumero(precioNormal)}</del></small>
                    <strong class="search-price__offer">$${formatearNumero(precioOferta)}</strong>
                    <span class="badge bg-success">${etiqueta}</span>
                </div>`;
        }

        /**
         * Extrae y carga marcas únicas de los repuestos
         */
        function cargarMarcasDisponibles(repuestos) {
            const marcasUnicas = [...new Set(repuestos.map(item => item.marcarepuesto))]
                .filter(marca => marca && marca.trim())
                .sort();

            const selectMarcas = $('#marcas_repuestos');
            selectMarcas.empty().append('<option value="0">Todas las marcas</option>');

            marcasUnicas.forEach(marca => {
                selectMarcas.append(`<option value="${marca}">${marca}</option>`);
            });
        }

        /**
         * Muestra estado de carga
         */
        function mostrarEstadoCarga(mostrar, mensaje = 'Cargando...') {
            if (mostrar) {
                Swal.fire({
                    title: mensaje,
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    showConfirmButton: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
            } else {
                Swal.close();
            }
        }

        /**
         * Muestra mensaje cuando no hay resultados
         */
        function mostrarSinResultados() {
            $('#container').html(`
                <div class="text-center py-5">
                    <img src="{{ asset('assets/images/sinresultados.png') }}"
                         alt="Sin resultados" class="mb-3" style="max-width: 200px;">
                    <h4>No se encontraron resultados</h4>
                    <p class="text-muted">Intenta con otros términos de búsqueda</p>
                </div>
            `);
        }

        /**
         * Muestra mensaje con cantidad de resultados
         */
        function mostrarMensajeResultados(cantidad) {
            const mensaje = cantidad === 1 ?
                `Encontrado ${cantidad} repuesto` :
                `Encontrados ${cantidad} repuestos`;

            $('#mensaje').html(`<p class="text-info"><i class="fas fa-info-circle"></i> ${mensaje}</p>`);
            $('#searchResultCount').text(cantidad.toLocaleString('es-CL'));
        }

        /**
         * Filtra repuestos por marca
         */
        function filtrarPorMarca(marca) {
            const url = `${CONFIG.baseApiUrl}/${currentTag}/ordenar_buscador_marca/${marca}`;

            mostrarEstadoCarga(true, 'Filtrando por marca...');

            $.ajax({
                type: 'GET',
                url: url,
                headers: {
                    "Authorization": 'Bearer ' + localStorage.getItem('token')
                },
                success: function(repuestos) {
                    if (currentView === 'table') {
                        cargarRepuestosEnTabla(repuestos);
                    } else {
                        mostrarRepuestosEnCards(repuestos);
                    }
                    mostrarMensajeResultados(repuestos.length);
                },
                error: function(xhr) {
                    manejarErrorAPI(xhr);
                },
                complete: function() {
                    mostrarEstadoCarga(false);
                }
            });
        }

        /**
         * Filtra por rango de precios
         */
        function filtrarRangoPrecio() {
            const minimo = $('#slider-range').slider("values", 0);
            const maximo = $('#slider-range').slider("values", 1);
            const url = `${CONFIG.baseApiUrl}/ordenar_tag_rango_precio/${minimo}/${maximo}/${currentTag}`;

            mostrarEstadoCarga(true, 'Filtrando por precio...');

            $.ajax({
                type: 'GET',
                url: url,
                headers: {
                    "Authorization": 'Bearer ' + localStorage.getItem('token')
                },
                success: function(repuestos) {
                    if (repuestos.length === 0) {
                        mostrarSinResultados();
                        return;
                    }

                    if (currentView === 'table') {
                        cargarRepuestosEnTabla(repuestos);
                    } else {
                        mostrarRepuestosEnCards(repuestos);
                    }
                    mostrarMensajeResultados(repuestos.length);
                },
                error: function(xhr) {
                    manejarErrorAPI(xhr);
                },
                complete: function() {
                    mostrarEstadoCarga(false);
                }
            });
        }

        /**
         * Maneja errores de API
         */
        function manejarErrorAPI(xhr) {
            let mensaje = 'Error al cargar los datos';

            if (xhr.status === 401) {
                mensaje = 'No tiene permisos para realizar esta acción';
            } else if (xhr.status === 404) {
                mensaje = 'No se encontraron resultados para su búsqueda';
            } else if (xhr.status >= 500) {
                mensaje = 'Error interno del servidor. Intente más tarde.';
            }

            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: mensaje
            });
        }

        /**
         * Muestra error de autenticación
         */
        function mostrarErrorAutenticacion() {
            Swal.fire({
                icon: 'warning',
                title: 'Acceso Requerido',
                text: 'Debe iniciar sesión para realizar búsquedas',
                confirmButtonText: 'Ir al Login'
            }).then(() => {
                window.location.href = "/login";
            });
        }

        /**
         * Cambia entre vista de tabla y vista de cards
         */
        function cambiarVista(vista) {
            currentView = vista;

            if (vista === 'table') {
                $('#vista-tabla').show();
                $('#vista-cards').hide();
                $('#btnVistaTabla').addClass('active');
                $('#btnVistaCards').removeClass('active');
            } else {
                $('#vista-tabla').hide();
                $('#vista-cards').show();
                $('#btnVistaTabla').removeClass('active');
                $('#btnVistaCards').addClass('active');

                // Si hay datos en la tabla, convertirlos a cards
                if (tablaRepuestos && tablaRepuestos.data().length > 0) {
                    convertirTablaACards();
                }
            }
        }

        /**
         * Convierte los datos de la tabla a vista de cards
         */
        function convertirTablaACards() {
            const datos = tablaRepuestos.data().toArray();
            mostrarRepuestosEnCards(datos);
        }

        /**
         * Muestra repuestos en formato de cards
         */
        function mostrarRepuestosEnCards(repuestos) {
            let html = '<div class="row">';

            repuestos.forEach(function(item, index) {
                // Si item es un array (datos de DataTable), extraer la información
                if (Array.isArray(item)) {
                    const imagen = item[0];
                    const codigo = item[1];
                    const marca = item[2];
                    const descripcion = item[3];
                    const precio = item[4];
                    const stock = item[5];
                    const acciones = item[6];

                    html += `
                        <div class="col-xl-4 col-lg-6 col-md-6 mb-4">
                            <div class="card h-100">
                                <div class="text-center p-3">
                                    ${imagen}
                                </div>
                                <div class="card-body">
                                    <h6 class="card-title">${descripcion}</h6>
                                    <p class="text-muted mb-1"><small>Código: ${codigo}</small></p>
                                    <p class="text-secondary mb-1">${marca}</p>
                                    <p class="text-primary fw-bold fs-5">${precio}</p>
                                    <p class="text-info"><small>Stock: ${stock}</small></p>
                                </div>
                                <div class="card-footer">
                                    ${acciones}
                                </div>
                            </div>
                        </div>
                    `;
                } else {
                    // Si es un objeto (datos directos de API)
                    const stock = item.stock_actual > CONFIG.maxDisplayStock ? 'DISPONIBLE' : item.stock_actual;

                    html += `
                        <div class="col-xl-4 col-lg-6 col-md-6 mb-4">
                            <div class="card h-100">
                                <div class="text-center p-3">
                                    <button type="button" class="product-image-trigger"
                                            onclick="vista_rapida(${item.id})" data-bs-toggle="modal"
                                            data-bs-target="#modalVistaRapida" title="Vista rápida">
                                    <img src="${CONFIG.imageBaseUrl}/${item.urlfoto}"
                                         class="img-thumbnail"
                                         style="max-width: 150px; max-height: 150px;"
                                         onerror="this.src='{{ asset('assets/images/no-image.png') }}'"
                                         alt="${item.descripcion}"></button>
                                </div>
                                <div class="card-body">
                                    <h6 class="card-title">${item.descripcion}</h6>
                                    <p class="text-muted mb-1"><small>Código: ${item.codigo_interno || '-'}</small></p>
                                    <p class="text-secondary mb-1">${item.marcarepuesto || '-'}</p>
                                    <div class="mb-2">${generarPrecioProducto(item)}</div>
                                    <p class="text-info"><small>Stock: ${stock}</small></p>
                                </div>
                                <div class="card-footer">
                                    <button class="btn btn-outline-primary btn-sm"
                                            onclick="vista_rapida(${item.id})"
                                            data-bs-toggle="modal"
                                            data-bs-target="#modalVistaRapida">
                                        <i class="fas fa-eye"></i> Vista rápida
                                    </button>
                                    <a href="/repuesto/${item.id}"
                                       class="btn btn-outline-info btn-sm ms-1">
                                        <i class="fas fa-info-circle"></i> Detalle
                                    </a>
                                </div>
                            </div>
                        </div>
                    `;
                }
            });

            html += '</div>';
            $('#container').html(html);
        }

        /**

         */
        function formatearNumero(num) {
            return parseInt(num).toLocaleString('es-CL');
        }

        /**
         * Búsqueda principal por OEM
         */
        function buscarPorOem() {
            const oem = $('#buscar_por_oem').val().trim();
            if (!oem) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Campo requerido',
                    text: 'Debe ingresar un código OEM'
                });
                return;
            }
            window.location.href = `/busqueda_por_oem/${oem}`;
        }

        /**
         * Realiza una nueva búsqueda
         */
        function buscarPrincipal() {
            const termino = $('#tag_principal').val().trim();
            if (!termino) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Campo requerido',
                    text: 'Ingrese un término de búsqueda'
                });
                return;
            }
            window.location.href = `/${termino}/buscador`;
        }

        // Funciones para el carrito (mantener compatibilidad)
        function agregar_carrito(id_repuesto) {
            const cantidad = 1;
            const token = localStorage.getItem('token');

            if (!token) {
                mostrarErrorAutenticacion();
                return;
            }

            const url = "{{ env('URL_AGREGAR_CARRITO') }}";
            const data = {
                idrep: id_repuesto,
                cantidad: cantidad
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
                success: function(resp) {
                    if (resp.message === 'OK') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Repuesto agregado al carrito',
                            showDenyButton: true,
                            confirmButtonText: 'Ir al carrito',
                            denyButtonText: 'Seguir comprando'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.href = "/carrito";
                            }
                        });
                    }
                },
                error: function(xhr) {
                    manejarErrorAPI(xhr);
                }
            });
        }

        // Funciones globales requeridas
        window.filtrar_rango_precio = filtrarRangoPrecio;
        window.buscar_por_oem = buscarPorOem;
        window.buscar_main = buscarPrincipal;
        window.agregar_carrito = agregar_carrito;
        window.cambiarVista = cambiarVista;
    </script>
@endsection

@section('content')
    <header class="catalog-hero search-hero">
        <div class="container-fluid px-3 px-lg-5">
            <nav class="catalog-breadcrumb" aria-label="Migas de pan">
                <a href="{{ url('/') }}">Inicio</a><i class="fas fa-chevron-right"></i>
                <a href="{{ route('repuestos') }}">Repuestos</a><i class="fas fa-chevron-right"></i>
                <span>Resultados</span>
            </nav>
            <div class="catalog-hero__row">
                <div>
                    <span class="catalog-eyebrow">Catálogo IPS Autopartes</span>
                    <h1>Resultados para <span id="searchTermTitle">tu búsqueda</span></h1>
                    <p>Compara disponibilidad, marca y precio. Puedes alternar entre una vista compacta o tarjetas.</p>
                </div>
                <div class="catalog-stat"><strong id="searchResultCount">—</strong><span>repuestos encontrados</span></div>
            </div>
        </div>
    </header>
    <div class="container-fluid catalog-container px-3 px-lg-5" id="contenedor" data-aos="fade-up">
        <!-- Breadcrumb -->
        {{-- <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/">Inicio</a></li>
                <li class="breadcrumb-item"><a href="/repuestos">Productos</a></li>
                <li class="breadcrumb-item active">Buscador</li>
            </ol>
        </nav> --}}

        <div class="row">

            <!-- Contenido Principal -->
            <div class="col-xl-12 col-lg-12 col-md-12 card catalog-card search-results-card">
                <!-- Controles de Vista -->
                <div class="catalog-card__header search-toolbar d-flex justify-content-between align-items-center">
                    <div><h5 class="card-title mb-1"><i class="fas fa-magnifying-glass me-2"></i>Repuestos encontrados</h5><div id="mensaje"></div></div>
                    <div class="btn-group view-switch" role="group" aria-label="Cambiar vista">
                        <button type="button"
                                id="btnVistaTabla"
                                class="btn btn-outline-secondary active">
                            <i class="fas fa-table-list"></i><span>Tabla</span>
                        </button>
                        <button type="button"
                                id="btnVistaCards"
                                class="btn btn-outline-secondary">
                            <i class="fas fa-grip"></i><span>Tarjetas</span>
                        </button>
                    </div>
                </div>

                <!-- Tabla de Repuestos -->
                <div id="vista-tabla" class="catalog-card__body table-responsive">
                    <table id="tablaRepuestos" class="table table-striped table-hover" style="width:100%">
                        <thead>
                            <tr>
                                <th>Imagen</th>
                                <th>Código</th>
                                <th>Marca</th>
                                <th>Descripción</th>
                                <th>Precio</th>
                                <th>Stock</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>

                <!-- Vista de Cards (oculta por defecto) -->
                <div id="vista-cards" class="catalog-card__body search-card-grid" style="display: none;">
                    <div id="container"></div>
                    <div id="pagination"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Campos ocultos para compatibilidad -->
    <input type="hidden" id="marca_repuesto" name="marca_repuesto" value="">
    <input type="hidden" id="tag_principal" name="tag_principal" value="">
@endsection
