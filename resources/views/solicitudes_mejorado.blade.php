@extends('layouts.app')

@section('javascript')
    <script>
        // ====================================
        // CONFIGURACIÓN Y CONSTANTES
        // ====================================
        const SOLICITUDES_CONFIG = {
            apiBaseUrl: 'https://intranet.ipschile.cl/api',
            imageBaseUrl: 'https://intranet.ipschile.cl/storage'
        };

        // Variables globales
        let solicitudesData = {
            tablaSolicitudes: null,
            tablaDetalle: null,
            solicitudes: [],
            solicitudActual: null
        };

        // ====================================
        // INICIALIZACIÓN
        // ====================================
        $(document).ready(function() {
            inicializarTablas();
            cargarSolicitudesCompra();
        });

        /**
         * Inicializa las tablas DataTable
         */
        function inicializarTablas() {
            // Tabla principal de solicitudes
            solicitudesData.tablaSolicitudes = $('#table_solicitudes').DataTable({
                responsive: true,
                language: {
                    url: '/assets/datatables/es-ES.json'
                },
                pageLength: 10,
                order: [[0, 'desc']], // Ordenar por fecha descendente
                columnDefs: [
                    { orderable: false, targets: [3, 4] }, // Acciones no ordenables
                    { className: "text-center", targets: [1, 2, 3, 4] }
                ]
            });

            // Tabla de detalle de productos
            solicitudesData.tablaDetalle = $('#tabla_detalle_productos').DataTable({
                responsive: true,
                language: {
                    url: '/assets/datatables/es-ES.json'
                },
                pageLength: 5,
                columnDefs: [
                    { orderable: false, targets: [0, 6] }, // Imagen y acciones no ordenables
                    { className: "text-center", targets: [0, 4, 5, 6] }
                ]
            });
        }

        // ====================================
        // FUNCIONES DE CARGA DE DATOS
        // ====================================

        /**
         * Carga las solicitudes de compra desde la API
         */
        function cargarSolicitudesCompra() {
            const token = localStorage.getItem('token');
            if (!token) {
                mostrarErrorAutenticacion();
                return;
            }

            const url = `${SOLICITUDES_CONFIG.apiBaseUrl}/dame_solicitudes_compra`;

            mostrarEstadoCarga(true, 'Cargando solicitudes...');

            $.ajax({
                type: 'GET',
                url: url,
                headers: {
                    "Authorization": 'Bearer ' + token
                },
                success: function(response) {
                    console.log('Respuesta solicitudes:', response);
                    manejarRespuestaSolicitudes(response);
                },
                error: function(xhr) {
                    console.error('Error al cargar solicitudes:', xhr);
                    manejarErrorAPI(xhr, 'Error al cargar las solicitudes');
                },
                complete: function() {
                    mostrarEstadoCarga(false);
                }
            });
        }

        /**
         * Maneja la respuesta de la API de solicitudes
         */
        function manejarRespuestaSolicitudes(response) {
            if (validarTokenRespuesta(response)) {
                return;
            }

            const compras = response[0] || [];
            const solicitudes = response[1] || [];

            solicitudesData.solicitudes = compras;

            if (compras.length === 0) {
                mostrarSolicitudesVacias();
                return;
            }

            mostrarSolicitudesEnTabla(compras);
            mostrarContenedorSolicitudes();
        }

        /**
         * Muestra las solicitudes en la tabla principal
         */
        function mostrarSolicitudesEnTabla(compras) {
            solicitudesData.tablaSolicitudes.clear();

            compras.forEach(function(compra) {
                const fechaFormateada = formatearFecha(compra.created_at);
                const estadoHTML = generarEstadoHTML(compra.status);
                const montoHTML = generarMontoHTML(compra.monto_total);
                const accionesHTML = generarAccionesSolicitud(compra.numero_carrito);

                solicitudesData.tablaSolicitudes.row.add([
                    fechaFormateada,
                    montoHTML,
                    estadoHTML,
                    accionesHTML.detalle,
                    accionesHTML.estado
                ]);
            });

            solicitudesData.tablaSolicitudes.draw();
        }

        /**
         * Genera HTML para el estado de la solicitud
         */
        function generarEstadoHTML(status) {
            if (status == 1) {
                return '<span class="badge bg-danger"><i class="fas fa-times"></i> Rechazado</span>';
            } else {
                return '<span class="badge bg-success"><i class="fas fa-check"></i> Aceptado</span>';
            }
        }

        /**
         * Genera HTML para el monto
         */
        function generarMontoHTML(monto) {
            if (monto && monto > 0) {
                return `$${formatearNumero(monto)}`;
            } else {
                return '<span class="text-muted">-</span>';
            }
        }

        /**
         * Genera HTML para las acciones de la solicitud
         */
        function generarAccionesSolicitud(numeroCarrito) {
            return {
                detalle: `
                    <button class="btn btn-outline-primary btn-sm"
                            onclick="mostrarDetalleSolicitud(${numeroCarrito})"
                            data-bs-toggle="modal"
                            data-bs-target="#modalDetalleSolicitud"
                            title="Ver detalle">
                        <i class="fas fa-eye"></i> Detalle
                    </button>
                `,
                estado: `
                    <button class="btn btn-outline-info btn-sm"
                            onclick="mostrarEstadoSolicitud(${numeroCarrito})"
                            data-bs-toggle="modal"
                            data-bs-target="#modalEstadoSolicitud"
                            title="Ver estado">
                        <i class="fas fa-shipping-fast"></i> Estado
                    </button>
                `
            };
        }

        // ====================================
        // FUNCIONES DE DETALLE DE SOLICITUD
        // ====================================

        /**
         * Muestra el detalle de una solicitud
         */
        function mostrarDetalleSolicitud(numeroCarrito) {
            const url = `${SOLICITUDES_CONFIG.apiBaseUrl}/${numeroCarrito}/detalle_venta`;

            mostrarEstadoCarga(true, 'Cargando detalle...');

            $.ajax({
                type: 'GET',
                url: url,
                success: function(repuestos) {
                    console.log('Detalle productos:', repuestos);
                    cargarProductosEnTabla(repuestos);
                    $('#titulo_modal_detalle').text(`Detalle de Solicitud #${numeroCarrito}`);
                },
                error: function(xhr) {
                    console.error('Error al cargar detalle:', xhr);
                    manejarErrorAPI(xhr, 'Error al cargar el detalle de la solicitud');
                },
                complete: function() {
                    mostrarEstadoCarga(false);
                }
            });
        }

        /**
         * Carga los productos en la tabla de detalle
         */
        function cargarProductosEnTabla(repuestos) {
            solicitudesData.tablaDetalle.clear();

            let montoTotal = 0;

            repuestos.forEach(function(item) {
                const precio = Number(item.precio_venta).toFixed(0);
                const total = item.cantidad * precio;
                montoTotal += total;

                const imagen = generarImagenProducto(item);
                const precioFormateado = generarPrecioFormateado(precio, item.oferta);
                const acciones = generarAccionesProducto(item.id);

                solicitudesData.tablaDetalle.row.add([
                    imagen,
                    item.codigo_interno || '-',
                    item.marcarepuesto || '-',
                    item.descripcion || '-',
                    precioFormateado,
                    item.cantidad,
                    acciones
                ]);
            });

            solicitudesData.tablaDetalle.draw();

            // Mostrar resumen de totales
            mostrarResumenDetalle(montoTotal, repuestos.length);
        }

        /**
         * Genera HTML para imagen del producto
         */
        function generarImagenProducto(item) {
            return `<img src="${SOLICITUDES_CONFIG.imageBaseUrl}/${item.urlfoto}"
                         width="60" height="60"
                         class="img-thumbnail"
                         onerror="this.src='{{ asset('assets/images/no-image.png') }}'">`;
        }

        /**
         * Genera HTML para precio formateado
         */
        function generarPrecioFormateado(precio, esOferta) {
            const precioBase = '$' + formatearNumero(precio);
            const badgeOferta = esOferta == 1 ? ' <span class="badge bg-danger">Oferta</span>' : '';
            return precioBase + badgeOferta;
        }

        /**
         * Genera HTML para acciones del producto
         */
        function generarAccionesProducto(idProducto) {
            return `
                <button class="btn btn-outline-success btn-sm"
                        onclick="vista_rapida(${idProducto})"
                        data-bs-toggle="modal"
                        data-bs-target="#modalVistaRapida"
                        title="Vista rápida">
                    <i class="fas fa-eye"></i>
                </button>
            `;
        }

        /**
         * Muestra el resumen del detalle
         */
        function mostrarResumenDetalle(montoTotal, cantidadProductos) {
            // La API entrega el precio de venta como valor neto. El IVA se
            // calcula sobre ese monto y luego se suma para obtener el total.
            const neto = Math.round(montoTotal);
            const iva = Math.round(neto * 0.19);
            const total = neto + iva;

            const resumenHTML = `
                <div class="row">
                    <div class="col-md-6">
                        <p class="mb-1"><strong>Productos:</strong> ${cantidadProductos}</p>
                        <p class="mb-1"><strong>Subtotal:</strong> $${formatearNumero(neto)}</p>
                    </div>
                    <div class="col-md-6">
                        <p class="mb-1"><strong>IVA (19%):</strong> $${formatearNumero(iva)}</p>
                        <p class="mb-1"><strong class="text-primary">Total:</strong> <span class="text-primary">$${formatearNumero(total)}</span></p>
                    </div>
                </div>
            `;

            $('#resumen_detalle').html(resumenHTML);
        }

        // ====================================
        // FUNCIONES DE ESTADO DE SOLICITUD
        // ====================================

        /**
         * Muestra el estado de una solicitud
         */
        function mostrarEstadoSolicitud(numeroCarrito) {
            const url = `${SOLICITUDES_CONFIG.apiBaseUrl}/dame_detalle_retiro/${numeroCarrito}`;

            mostrarEstadoCarga(true, 'Cargando estado...');

            $.ajax({
                type: 'GET',
                url: url,
                success: function(response) {
                    console.log('Estado solicitud:', response);
                    mostrarDetalleEstado(response);
                    $('#titulo_modal_estado').text(`Estado de Solicitud #${numeroCarrito}`);
                },
                error: function(xhr) {
                    console.error('Error al cargar estado:', xhr);
                    manejarErrorAPI(xhr, 'Error al cargar el estado de la solicitud');
                },
                complete: function() {
                    mostrarEstadoCarga(false);
                }
            });
        }

        /**
         * Muestra el detalle del estado
         */
        function mostrarDetalleEstado(detalle) {
            let estado = '';
            let tipoEntrega = '';
            let estadoClass = '';

            if (detalle.region) {
                // Envío a domicilio
                tipoEntrega = 'Envío a domicilio';
                switch(detalle.estado) {
                    case 0:
                        estado = 'Esperando procesamiento';
                        estadoClass = 'warning';
                        break;
                    case 1:
                        estado = 'Enviado';
                        estadoClass = 'info';
                        break;
                    case 2:
                        estado = 'Entregado';
                        estadoClass = 'success';
                        break;
                }

                const contenidoHTML = `
                    <div class="row">
                        <div class="col-12 mb-3">
                            <div class="alert alert-${estadoClass}">
                                <h6><i class="fas fa-truck"></i> ${tipoEntrega}</h6>
                                <p class="mb-0"><strong>Estado:</strong> ${estado}</p>
                            </div>
                        </div>
                    </div>
                    <table class="table table-striped">
                        <tbody>
                            <tr>
                                <td><strong>Región:</strong></td>
                                <td>${detalle.region}</td>
                            </tr>
                            <tr>
                                <td><strong>Comuna:</strong></td>
                                <td>${detalle.comuna}</td>
                            </tr>
                            <tr>
                                <td><strong>Dirección:</strong></td>
                                <td>${detalle.direccion_despacho}</td>
                            </tr>
                            <tr>
                                <td><strong>Teléfono:</strong></td>
                                <td>${detalle.telefono_despacho}</td>
                            </tr>
                            <tr>
                                <td><strong>Persona:</strong></td>
                                <td>${detalle.persona}</td>
                            </tr>
                            <tr>
                                <td><strong>Fecha solicitud:</strong></td>
                                <td>${formatearFecha(detalle.created_at)}</td>
                            </tr>
                        </tbody>
                    </table>
                `;

                $('#contenido_estado').html(contenidoHTML);
            } else {
                // Retiro en tienda
                tipoEntrega = 'Retiro en tienda';
                switch(detalle.estado) {
                    case 0:
                        estado = 'Esperando preparación';
                        estadoClass = 'warning';
                        break;
                    case 1:
                        estado = 'Listo para retiro';
                        estadoClass = 'success';
                        break;
                }

                const contenidoHTML = `
                    <div class="row">
                        <div class="col-12 mb-3">
                            <div class="alert alert-${estadoClass}">
                                <h6><i class="fas fa-store"></i> ${tipoEntrega}</h6>
                                <p class="mb-0"><strong>Estado:</strong> ${estado}</p>
                            </div>
                        </div>
                    </div>
                    <table class="table table-striped">
                        <tbody>
                            <tr>
                                <td><strong>Tipo de entrega:</strong></td>
                                <td>Retiro en tienda</td>
                            </tr>
                            <tr>
                                <td><strong>Persona autorizada:</strong></td>
                                <td>${detalle.nombre}</td>
                            </tr>
                            <tr>
                                <td><strong>Fecha emisión:</strong></td>
                                <td>${formatearFecha(detalle.created_at)}</td>
                            </tr>
                            <tr>
                                <td><strong>Dirección tienda:</strong></td>
                                <td>Av. Ejemplo 123, Santiago</td>
                            </tr>
                            <tr>
                                <td><strong>Horario atención:</strong></td>
                                <td>Lunes a Viernes 9:00 - 18:00</td>
                            </tr>
                        </tbody>
                    </table>
                `;

                $('#contenido_estado').html(contenidoHTML);
            }
        }

        // ====================================
        // FUNCIONES DE INTERFAZ
        // ====================================

        /**
         * Muestra el mensaje de solicitudes vacías
         */
        function mostrarSolicitudesVacias() {
            $('#contenedor_principal').html(`
                <div class="text-center py-5">
                    <img src="{{ asset('assets/images/empty-requests.png') }}"
                         alt="Sin solicitudes"
                         class="mb-3"
                         style="max-width: 200px;"
                         onerror="this.style.display='none'">
                    <h4 class="text-muted">No tienes solicitudes de compra</h4>
                    <p class="text-muted">Cuando realices pedidos aparecerán aquí</p>
                    <a href="/" class="btn btn-primary">
                        <i class="fas fa-shopping-cart"></i> Ir a comprar
                    </a>
                </div>
            `);
        }

        /**
         * Muestra el contenedor de solicitudes
         */
        function mostrarContenedorSolicitudes() {
            $('#contenedor_principal').show();
        }

        // ====================================
        // FUNCIONES UTILITARIAS
        // ====================================

        /**
         * Valida respuesta de token
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
         * Muestra estado de carga
         */
        function mostrarEstadoCarga(mostrar, mensaje = 'Procesando...') {
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
         * Maneja errores de API
         */
        function manejarErrorAPI(xhr, mensajeBase = 'Error en la operación') {
            let mensaje = mensajeBase;

            if (xhr.status === 401) {
                mensaje = 'No tiene permisos para realizar esta acción';
            } else if (xhr.status === 404) {
                mensaje = 'Recurso no encontrado';
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
                text: 'Debe iniciar sesión para ver sus solicitudes',
                confirmButtonText: 'Ir al Login'
            }).then(() => {
                window.location.href = "/login";
            });
        }

        /**
         * Formatea números con separadores de miles
         */
        function formatearNumero(num) {
            return parseInt(num).toLocaleString('es-CL');
        }

        /**
         * Formatea fecha
         */
        function formatearFecha(fechaISO) {
            if (!fechaISO) return '-';

            const fecha = new Date(fechaISO);
            const opciones = {
                year: 'numeric',
                month: '2-digit',
                day: '2-digit',
                hour: '2-digit',
                minute: '2-digit'
            };

            return fecha.toLocaleDateString('es-CL', opciones);
        }

        // ====================================
        // FUNCIONES GLOBALES PARA COMPATIBILIDAD
        // ====================================
        window.mostrarDetalleSolicitud = mostrarDetalleSolicitud;
        window.mostrarEstadoSolicitud = mostrarEstadoSolicitud;
    </script>
@endsection

@section('styles')
    <style>
        /* Estilos personalizados para solicitudes */
        .estado-badge {
            font-size: 0.85rem;
            padding: 0.5rem 0.75rem;
        }

        .modal-header img.logo_header {
            height: 40px;
            margin-right: 15px;
        }

        .table-solicitudes th {
            background-color: #f8f9fa;
            font-weight: 600;
            border-bottom: 2px solid #dee2e6;
        }

        .btn-group-actions {
            display: flex;
            gap: 0.25rem;
            justify-content: center;
        }

        .estado-entrega {
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 1rem;
        }

        .estado-entrega h6 {
            margin: 0 0 0.5rem 0;
            font-weight: 600;
        }

        .table-estado td:first-child {
            font-weight: 600;
            color: #495057;
            width: 40%;
        }

        /* Responsive para botones */
        @media (max-width: 768px) {
            .btn-group-actions {
                flex-direction: column;
                gap: 0.5rem;
            }

            .btn-group-actions .btn {
                width: 100%;
            }
        }

        /* Animaciones suaves */
        .table tbody tr {
            transition: background-color 0.2s ease;
        }

        .table tbody tr:hover {
            background-color: #f8f9fa;
        }

        /* Estilos para estados */
        .alert-warning {
            border-left: 4px solid #ffc107;
        }

        .alert-info {
            border-left: 4px solid #17a2b8;
        }

        .alert-success {
            border-left: 4px solid #28a745;
        }
    </style>
@endsection

@section('content')
    <div class="container-fluid">
        @include('fragm.postventa')

        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/">Inicio</a></li>
                <li class="breadcrumb-item active">Mis Solicitudes</li>
            </ol>
        </nav>

        <!-- Contenedor principal -->
        <div id="contenedor_principal">
            <!-- Header -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h2 class="mb-1">
                                <i class="fas fa-clipboard-list text-primary"></i>
                                Mis Solicitudes de Compra
                            </h2>
                            <p class="text-muted mb-0">Revisa el estado y detalle de tus pedidos</p>
                        </div>
                        <div>
                            <button class="btn btn-outline-primary" onclick="cargarSolicitudesCompra()">
                                <i class="fas fa-sync-alt"></i> Actualizar
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabla principal de solicitudes -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-list"></i> Historial de Solicitudes
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="table_solicitudes" class="table table-hover table-solicitudes" style="width:100%">
                            <thead>
                                <tr>
                                    <th><i class="fas fa-calendar"></i> Fecha</th>
                                    <th><i class="fas fa-dollar-sign"></i> Monto</th>
                                    <th><i class="fas fa-info-circle"></i> Estado</th>
                                    <th><i class="fas fa-eye"></i> Detalle</th>
                                    <th><i class="fas fa-shipping-fast"></i> Seguimiento</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Se llena dinámicamente -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Detalle de Solicitud -->
        <div class="modal fade" id="modalDetalleSolicitud" tabindex="-1" aria-labelledby="modalDetalleSolicitudLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <img src="{{ asset('assets/images/logo_ips2025.png') }}" alt="Logo" class="logo_header">
                        <h5 class="modal-title" id="titulo_modal_detalle">Detalle de Solicitud</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <!-- Resumen -->
                        <div class="card mb-3">
                            <div class="card-body">
                                <h6 class="card-title">
                                    <i class="fas fa-calculator"></i> Resumen de la Solicitud
                                </h6>
                                <div id="resumen_detalle">
                                    <!-- Se llena dinámicamente -->
                                </div>
                            </div>
                        </div>

                        <!-- Tabla de productos -->
                        <div class="table-responsive">
                            <table id="tabla_detalle_productos" class="table table-striped" style="width:100%; font-size: 13px;">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Imagen</th>
                                        <th>Código</th>
                                        <th>Marca</th>
                                        <th>Descripción</th>
                                        <th>Precio Unit.</th>
                                        <th>Cantidad</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Se llena dinámicamente -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times"></i> Cerrar
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Estado de Solicitud -->
        <div class="modal fade" id="modalEstadoSolicitud" tabindex="-1" aria-labelledby="modalEstadoSolicitudLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <img src="{{ asset('assets/images/logo_ips2025.png') }}" alt="Logo" class="logo_header">
                        <h5 class="modal-title" id="titulo_modal_estado">Estado de Solicitud</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div id="contenido_estado">
                            <!-- Se llena dinámicamente -->
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times"></i> Cerrar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
