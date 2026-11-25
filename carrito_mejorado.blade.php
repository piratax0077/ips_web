@extends('layouts.app')

@section('javascript')
    <script>
        // ====================================
        // CONFIGURACIÓN Y CONSTANTES
        // ====================================
        const CARRITO_CONFIG = {
            apiBaseUrl: 'https://intranet.ipschile.cl/api',
            imageBaseUrl: 'https://intranet.ipschile.cl/storage',
            pageSize: 30,
            clienteSII: 4,
            ivaPercent: 0.19
        };

        // Variables globales del carrito
        let carritoData = {
            tablaDetalle: null,
            numeroCarrito: null,
            montoTotal: 0,
            items: []
        };

        // ====================================
        // INICIALIZACIÓN
        // ====================================
        $(document).ready(function() {
            inicializarCarrito();
            inicializarRegionesYComunas();
            cargarDatosCarrito();
            cargarDatosUsuario();
        });

        /**
         * Inicializa los componentes principales del carrito
         */
        function inicializarCarrito() {
            // Inicializar DataTable
            carritoData.tablaDetalle = $('#table_detalle_nv').DataTable({
                responsive: true,
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
                },
                pageLength: 10,
                order: [[2, 'asc']], // Ordenar por descripción
                columnDefs: [
                    { orderable: false, targets: [0, 6] }, // Imagen y acciones no ordenables
                    { className: "text-center", targets: [0, 3, 4, 5, 6] }
                ]
            });

            // Configurar eventos
            configurarEventosCarrito();
        }

        /**
         * Configura eventos específicos del carrito
         */
        function configurarEventosCarrito() {
            // Eventos para tipos de despacho
            $('input[name="flexRadioDefault"]').on('change', function() {
                const tipoDespacho = $(this).val();
                togglePanelesDespacho(tipoDespacho);
            });

            // Eventos para tipo de documento
            $('input[name="tipo_documento_radio"]').on('change', function() {
                const tipoDoc = $(this).val();
                toggleTipoDocumento(tipoDoc);
            });

            // Eventos para quien retira
            $('input[name="flexRadioPersona"]').on('change', function() {
                const quienRetira = $(this).val();
                toggleQuienRetira(quienRetira);
            });
        }

        // ====================================
        // FUNCIONES DE CARRITO
        // ====================================

        /**
         * Carga los datos del carrito desde la API
         */
        function cargarDatosCarrito() {
            const token = localStorage.getItem('token');
            if (!token) {
                mostrarErrorAutenticacion();
                return;
            }

            const url = `${CARRITO_CONFIG.apiBaseUrl}/revisar_carrito`;

            mostrarEstadoCarga(true, 'Cargando carrito...');

            $.ajax({
                type: 'GET',
                url: url,
                headers: {
                    "Authorization": 'Bearer ' + token
                },
                success: function(response) {
                    manejarRespuestaCarrito(response);
                },
                error: function(xhr) {
                    manejarErrorAPI(xhr, 'Error al cargar el carrito');
                },
                complete: function() {
                    mostrarEstadoCarga(false);
                }
            });
        }

        /**
         * Maneja la respuesta de la API del carrito
         */
        function manejarRespuestaCarrito(response) {
            if (validarTokenRespuesta(response)) {
                return;
            }

            const repuestos = response.repuestos || [];
            carritoData.items = repuestos;

            if (repuestos.length === 0) {
                mostrarCarritoVacio();
                return;
            }

            // Cargar datos en la interfaz
            carritoData.numeroCarrito = repuestos[0].numero_carrito;
            $('#numero_carrito').val(carritoData.numeroCarrito);

            mostrarProductosEnTabla(repuestos);
            calcularYMostrarTotales(repuestos);
            mostrarContenedorCarrito();
        }

        /**
         * Muestra los productos en la tabla DataTable
         */
        function mostrarProductosEnTabla(productos) {
            carritoData.tablaDetalle.clear();

            productos.forEach(function(item) {
                const precio = Number(item.precio_venta).toFixed(0);
                const total = item.cantidad * precio;

                const imagen = generarImagenProducto(item);
                const cantidadEditable = generarCantidadEditable(item.id, item.cantidad);
                const precioFormateado = generarPrecioFormateado(precio, item.oferta);
                const acciones = generarAccionesProducto(item.id);

                carritoData.tablaDetalle.row.add([
                    imagen,
                    item.codigo_interno || '-',
                    item.descripcion || '-',
                    cantidadEditable,
                    precioFormateado,
                    `<span id="total_${item.id}">$${formatearNumero(total)}</span>`,
                    acciones
                ]);
            });

            carritoData.tablaDetalle.draw();

            // Actualizar contador de productos
            actualizarContadorProductos();
        }

        /**
         * Genera HTML para imagen del producto
         */
        function generarImagenProducto(item) {
            return `<img src="${CARRITO_CONFIG.imageBaseUrl}/${item.urlfoto}"
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
         * Genera HTML para cantidad editable
         */
        function generarCantidadEditable(idProducto, cantidadActual) {
            return `
                <div class="cantidad-control" data-producto-id="${idProducto}">
                    <div class="input-group input-group-sm" style="max-width: 120px;">
                        <button class="btn btn-outline-secondary btn-sm"
                                type="button"
                                onclick="cambiarCantidad(${idProducto}, -1)"
                                title="Disminuir cantidad">
                            <i class="fas fa-minus"></i>
                        </button>
                        <input type="number"
                               class="form-control text-center cantidad-input"
                               id="cantidad_${idProducto}"
                               value="${cantidadActual}"
                               min="1"
                               max="99"
                               onchange="actualizarCantidadProducto(${idProducto}, this.value)"
                               onkeypress="if(event.key === 'Enter') actualizarCantidadProducto(${idProducto}, this.value)">
                        <button class="btn btn-outline-secondary btn-sm"
                                type="button"
                                onclick="cambiarCantidad(${idProducto}, 1)"
                                title="Aumentar cantidad">
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>
                </div>
            `;
        }

        /**
         * Genera HTML para acciones del producto
         */
        function generarAccionesProducto(idProducto) {
            return `
                <div class="btn-group" role="group">
                    <button class="btn btn-outline-danger btn-sm"
                            onclick="eliminarItemCarrito(${idProducto})"
                            title="Eliminar">
                        <i class="fas fa-trash"></i>
                    </button>
                    <button class="btn btn-outline-primary btn-sm"
                            onclick="vista_rapida(${idProducto})"
                            data-bs-target="#modalVistaRapida"
                            data-bs-toggle="modal"
                            title="Vista rápida">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
            `;
        }

        /**
         * Calcula y muestra los totales del carrito
         */
        function calcularYMostrarTotales(productos) {
            let montoNeto = 0;

            productos.forEach(item => {
                const precio = Number(item.precio_venta).toFixed(0);
                montoNeto += (item.cantidad * precio);
            });

            const iva = Math.round(montoNeto * CARRITO_CONFIG.ivaPercent);
            const total = montoNeto + iva;

            carritoData.montoTotal = total;
            $('#monto_total').val(total);

            mostrarResumenTotales(montoNeto, iva, total);

            // Actualizar contador de productos
            actualizarContadorProductos();
        }

        /**
         * Muestra el resumen de totales
         */
        function mostrarResumenTotales(neto, iva, total) {
            const resumenHtml = `
                <div class="table-responsive">
                    <table class="table table-sm table-borderless">
                        <tbody>
                            <tr>
                                <td><strong class="text-muted">Subtotal:</strong></td>
                                <td class="text-end">$${formatearNumero(neto)}</td>
                            </tr>
                            <tr>
                                <td><strong class="text-muted">IVA (19%):</strong></td>
                                <td class="text-end">$${formatearNumero(iva)}</td>
                            </tr>
                            <tr class="border-top">
                                <td><strong>Total:</strong></td>
                                <td class="text-end"><strong class="text-primary fs-5">$${formatearNumero(total)}</strong></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="text-center mt-3">
                    <img src="{{ asset('assets/images/webpay.png') }}"
                         alt="WebPay"
                         class="img-fluid"
                         style="max-height: 40px;">
                </div>
            `;

            $('#resumen').html(resumenHtml);
        }

        /**
         * Elimina un item del carrito
         */
        function eliminarItemCarrito(idRepuesto) {
            if (!carritoData.numeroCarrito) {
                mostrarError('Error', 'No se encontró el carrito');
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
                    ejecutarEliminacionItem(idRepuesto);
                }
            });
        }

        /**
         * Ejecuta la eliminación del item
         */
        function ejecutarEliminacionItem(idRepuesto) {
            const token = localStorage.getItem('token');
            const url = `${CARRITO_CONFIG.apiBaseUrl}/eliminar_item_carrito/${idRepuesto}/${carritoData.numeroCarrito}`;

            mostrarEstadoCarga(true, 'Eliminando producto...');

            $.ajax({
                type: 'GET',
                url: url,
                headers: {
                    "Authorization": 'Bearer ' + token
                },
                success: function(response) {
                    const repuestos = response.repuestos || [];

                    if (repuestos.length === 0) {
                        mostrarCarritoVacio();
                    } else {
                        manejarRespuestaCarrito(response);
                        mostrarNotificacion('Producto eliminado exitosamente', 'success');
                    }
                },
                error: function(xhr) {
                    manejarErrorAPI(xhr, 'Error al eliminar el producto');
                },
                complete: function() {
                    mostrarEstadoCarga(false);
                }
            });
        }

        // ====================================
        // FUNCIONES DE EDICIÓN DE CANTIDAD
        // ====================================

        /**
         * Cambia la cantidad de un producto (incremento/decremento)
         */
        function cambiarCantidad(idProducto, incremento) {
            const inputCantidad = $(`#cantidad_${idProducto}`);
            const cantidadActual = parseInt(inputCantidad.val()) || 1;
            const nuevaCantidad = Math.max(1, cantidadActual + incremento);

            if (nuevaCantidad <= 99) { // Límite máximo
                inputCantidad.val(nuevaCantidad);
                actualizarCantidadProducto(idProducto, nuevaCantidad);
            }
        }

        /**
         * Actualiza la cantidad de un producto en el carrito
         */
        function actualizarCantidadProducto(idProducto, nuevaCantidad) {
            const cantidad = parseInt(nuevaCantidad);

            // Validar cantidad
            if (isNaN(cantidad) || cantidad < 1) {
                mostrarError('Error', 'La cantidad debe ser mayor a 0');
                // Restaurar cantidad anterior
                const item = carritoData.items.find(item => item.id == idProducto);
                if (item) {
                    $(`#cantidad_${idProducto}`).val(item.cantidad);
                }
                return;
            }

            if (cantidad > 99) {
                mostrarError('Error', 'La cantidad máxima es 99');
                $(`#cantidad_${idProducto}`).val(99);
                return;
            }

            // Confirmar cambio si es una disminución significativa
            const item = carritoData.items.find(item => item.id == idProducto);
            if (item && cantidad < item.cantidad && (item.cantidad - cantidad) > 5) {
                Swal.fire({
                    title: 'Confirmar cambio de cantidad',
                    text: `¿Desea cambiar la cantidad de ${item.cantidad} a ${cantidad} unidades?`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#28a745',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Sí, cambiar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        ejecutarActualizacionCantidad(idProducto, cantidad);
                    } else {
                        // Restaurar cantidad anterior
                        $(`#cantidad_${idProducto}`).val(item.cantidad);
                    }
                });
            } else {
                ejecutarActualizacionCantidad(idProducto, cantidad);
            }
        }

        /**
         * Ejecuta la actualización de cantidad en el servidor
         */
        function ejecutarActualizacionCantidad(idProducto, nuevaCantidad) {
            const token = localStorage.getItem('token');
            if (!token) {
                mostrarErrorAutenticacion();
                return;
            }

            const url = `${CARRITO_CONFIG.apiBaseUrl}/actualizar_cantidad_carrito`;

            const data = {
                id_repuesto: idProducto,
                numero_carrito: carritoData.numeroCarrito,
                nueva_cantidad: nuevaCantidad
            };

            // Deshabilitar input mientras se procesa
            const inputCantidad = $(`#cantidad_${idProducto}`);
            const cantidadAnterior = inputCantidad.val();
            inputCantidad.prop('disabled', true);

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
                success: function(response) {
                    if (response.success || response.message === 'OK') {
                        // Actualizar los datos locales
                        actualizarDatosLocalesCantidad(idProducto, nuevaCantidad);

                        // Actualizar el total del producto en la tabla
                        actualizarTotalProductoEnTabla(idProducto, nuevaCantidad);

                        // Recalcular totales generales
                        recalcularTotalesGenerales();

                        mostrarNotificacion('Cantidad actualizada exitosamente', 'success');
                    } else {
                        throw new Error(response.message || 'Error al actualizar cantidad');
                    }
                },
                error: function(xhr) {
                    // Restaurar cantidad anterior en caso de error
                    inputCantidad.val(cantidadAnterior);
                    manejarErrorAPI(xhr, 'Error al actualizar la cantidad');
                },
                complete: function() {
                    inputCantidad.prop('disabled', false);
                }
            });
        }

        /**
         * Actualiza los datos locales del carrito
         */
        function actualizarDatosLocalesCantidad(idProducto, nuevaCantidad) {
            const itemIndex = carritoData.items.findIndex(item => item.id == idProducto);
            if (itemIndex !== -1) {
                carritoData.items[itemIndex].cantidad = parseInt(nuevaCantidad);
            }
        }

        /**
         * Actualiza el total del producto en la tabla
         */
        function actualizarTotalProductoEnTabla(idProducto, nuevaCantidad) {
            const item = carritoData.items.find(item => item.id == idProducto);
            if (item) {
                const precio = Number(item.precio_venta).toFixed(0);
                const nuevoTotal = nuevaCantidad * precio;
                $(`#total_${idProducto}`).html(`$${formatearNumero(nuevoTotal)}`);
            }
        }

        /**
         * Recalcula los totales generales del carrito
         */
        function recalcularTotalesGenerales() {
            let montoNeto = 0;

            carritoData.items.forEach(item => {
                const precio = Number(item.precio_venta).toFixed(0);
                montoNeto += (item.cantidad * precio);
            });

            const iva = Math.round(montoNeto * CARRITO_CONFIG.ivaPercent);
            const total = montoNeto + iva;

            carritoData.montoTotal = total;
            $('#monto_total').val(total);

            mostrarResumenTotales(montoNeto, iva, total);

            // Actualizar contador de productos
            actualizarContadorProductos();
        }

        /**
         * Actualiza el contador de productos en la interfaz
         */
        function actualizarContadorProductos() {
            const totalProductos = carritoData.items.reduce((sum, item) => sum + item.cantidad, 0);
            const textoProductos = totalProductos === 1 ? 'producto' : 'productos';
            $('#contador_productos').text(`${totalProductos} ${textoProductos}`);
        }

        // ====================================
        // FUNCIONES DE USUARIO
        // ====================================

        /**
         * Carga los datos del usuario autenticado
         */
        function cargarDatosUsuario() {
            const token = localStorage.getItem('token');
            if (!token) return;

            const url = `${CARRITO_CONFIG.apiBaseUrl}/get-user`;

            $.ajax({
                type: 'POST',
                url: url,
                headers: {
                    "Authorization": 'Bearer ' + token
                },
                success: function(response) {
                    mostrarDatosUsuario(response.user);
                },
                error: function(xhr) {
                    $('#detalle_usuario').html(`
                        <div class="text-center text-muted">
                            <i class="fas fa-user-slash mb-2"></i>
                            <p>No se pudo cargar la información del usuario</p>
                            <a href="/login" class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-sign-in-alt"></i> Iniciar Sesión
                            </a>
                        </div>
                    `);
                }
            });
        }

        /**
         * Muestra los datos del usuario en la interfaz
         */
        function mostrarDatosUsuario(user) {
            const contenidoHtml = `
                <div class="user-info">
                    <div class="mb-2">
                        <small class="text-muted">Nombre:</small>
                        <div class="fw-bold">${user.name || '-'}</div>
                    </div>
                    <div class="mb-2">
                        <small class="text-muted">Email:</small>
                        <div>${user.email || '-'}</div>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted">RUT:</small>
                        <div>${user.rut || '-'}</div>
                    </div>
                    <a href="{{ url('mis_datos') }}" class="btn btn-outline-success btn-sm w-100">
                        <i class="fas fa-edit"></i> Editar Datos
                    </a>
                </div>
            `;

            $('#detalle_usuario').html(contenidoHtml);
        }

        // ====================================
        // FUNCIONES DE PROCESO DE COMPRA
        // ====================================

        /**
         * Procesa la nota de venta
         */
        function procesarNotaVenta() {
            const token = localStorage.getItem('token');
            if (!token) {
                mostrarErrorAutenticacion();
                return;
            }

            if (!carritoData.numeroCarrito) {
                mostrarError('Error', 'No se encontró información del carrito');
                return;
            }

            Swal.fire({
                title: 'Confirmar Nota de Venta',
                text: '¿Desea procesar la nota de venta con los productos seleccionados?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sí, procesar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    enviarNotaVenta();
                }
            });
        }

        /**
         * Envía la nota de venta al servidor
         */
        function enviarNotaVenta() {
            const token = localStorage.getItem('token');
            const url = `${CARRITO_CONFIG.apiBaseUrl}/enviar_nota_venta`;

            // Obtener observaciones del textarea
            const observaciones = $('#observaciones').val().trim();

            const data = {
                numero_carrito: carritoData.numeroCarrito,
                token: token,
                observaciones: observaciones || null
            };

            mostrarEstadoCarga(true, 'Procesando nota de venta...');

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
                success: function(response) {
                    if (response === 'OK') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Nota de venta procesada',
                            text: 'La nota de venta ha sido registrada exitosamente',
                            confirmButtonText: 'Continuar'
                        }).then(() => {
                            // Limpiar observaciones
                            $('#observaciones').val('');
                            // Redirigir o actualizar vista
                            window.location.reload();
                        });
                    } else {
                        mostrarError('Error', 'No se pudo procesar la nota de venta');
                    }
                },
                error: function(xhr) {
                    manejarErrorAPI(xhr, 'Error al procesar la nota de venta');
                },
                complete: function() {
                    mostrarEstadoCarga(false);
                }
            });
        }

        // ====================================
        // FUNCIONES DE REGIONES Y COMUNAS
        // ====================================

        /**
         * Inicializa el sistema de regiones y comunas
         */
        function inicializarRegionesYComunas() {
            // Esta función puede cargar datos desde un archivo JSON externo
            // Por ahora mantengo una versión simplificada
            cargarRegionesChile();
        }

        /**
         * Carga las regiones de Chile (versión simplificada)
         */
        function cargarRegionesChile() {
            // Aquí idealmente cargarías desde un archivo JSON externo
            // Por simplicidad, incluyo algunas regiones principales
            const regionesSimplificadas = [
                {
                    nombre: "Región Metropolitana de Santiago",
                    comunas: ["Santiago", "Las Condes", "Providencia", "Ñuñoa", "La Florida", "Maipú", "Puente Alto", "San Bernardo"]
                },
                {
                    nombre: "Valparaíso",
                    comunas: ["Valparaíso", "Viña del Mar", "Quilpué", "Villa Alemana", "San Antonio"]
                },
                {
                    nombre: "Biobío",
                    comunas: ["Concepción", "Talcahuano", "Los Ángeles", "Chillán", "Coronel"]
                }
            ];

            poblarSelectRegiones(regionesSimplificadas);
        }

        /**
         * Puebla el select de regiones
         */
        function poblarSelectRegiones(regiones) {
            const selectRegion = $('#regiones');
            selectRegion.empty().append('<option value="">Seleccione región</option>');

            regiones.forEach(region => {
                selectRegion.append(`<option value="${region.nombre}">${region.nombre}</option>`);
            });

            // Configurar evento change
            selectRegion.on('change', function() {
                const regionSeleccionada = $(this).val();
                const region = regiones.find(r => r.nombre === regionSeleccionada);

                if (region) {
                    poblarSelectComunas(region.comunas);
                } else {
                    $('#comunas').empty().append('<option value="">Seleccione comuna</option>');
                }
            });
        }

        /**
         * Puebla el select de comunas
         */
        function poblarSelectComunas(comunas) {
            const selectComuna = $('#comunas');
            selectComuna.empty().append('<option value="">Seleccione comuna</option>');

            comunas.forEach(comuna => {
                selectComuna.append(`<option value="${comuna}">${comuna}</option>`);
            });
        }

        // ====================================
        // FUNCIONES DE INTERFAZ
        // ====================================

        /**
         * Muestra/oculta paneles según tipo de despacho
         */
        function togglePanelesDespacho(tipoDespacho) {
            if (tipoDespacho == '1') {
                $('#panel_retiro_tienda').show();
                $('#panel_envio_domicilio').hide();
            } else if (tipoDespacho == '2') {
                $('#panel_retiro_tienda').hide();
                $('#panel_envio_domicilio').show();
            }
        }

        /**
         * Muestra/oculta paneles según tipo de documento
         */
        function toggleTipoDocumento(tipoDoc) {
            $('#tipo_documento').val(tipoDoc);

            if (tipoDoc === 'boleta') {
                $('#panel_boleta').show();
                $('#panel_factura').hide();
            } else if (tipoDoc === 'factura') {
                $('#panel_boleta').hide();
                $('#panel_factura').show();
            }
        }

        /**
         * Muestra/oculta campos según quien retira
         */
        function toggleQuienRetira(quienRetira) {
            if (quienRetira == '1') {
                $('#retira_otra_persona').hide();
            } else if (quienRetira == '2') {
                $('#retira_otra_persona').show();
            }
        }

        /**
         * Muestra el carrito vacío
         */
        function mostrarCarritoVacio() {
            $('#mensaje').html(`
                <div class="text-center py-5">
                    <img src="{{ asset('assets/images/empty-car.png') }}"
                         alt="Carrito vacío"
                         class="mb-3"
                         style="max-width: 200px;">
                    <h4 class="text-muted">Tu carrito está vacío</h4>
                    <p class="text-muted">Agrega productos para comenzar tu compra</p>
                    <a href="/" class="btn btn-primary">
                        <i class="fas fa-shopping-cart"></i> Ir a comprar
                    </a>
                </div>
            `);
            $('#contenedor').addClass('d-none');
        }

        /**
         * Muestra el contenedor del carrito
         */
        function mostrarContenedorCarrito() {
            $('#contenedor').removeClass('d-none').addClass('d-block');
            $('#mensaje').empty();
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

            mostrarError('Error', mensaje);
        }

        /**
         * Muestra error de autenticación
         */
        function mostrarErrorAutenticacion() {
            Swal.fire({
                icon: 'warning',
                title: 'Acceso Requerido',
                text: 'Debe iniciar sesión para acceder al carrito',
                confirmButtonText: 'Ir al Login'
            }).then(() => {
                window.location.href = "/login";
            });
        }

        /**
         * Muestra error genérico
         */
        function mostrarError(titulo, mensaje) {
            Swal.fire({
                icon: 'error',
                title: titulo,
                text: mensaje
            });
        }

        /**
         * Muestra notificación
         */
        function mostrarNotificacion(mensaje, tipo = 'info') {
            Swal.fire({
                icon: tipo,
                text: mensaje,
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000
            });
        }

        /**
         * Formatea números con separadores de miles
         */
        function formatearNumero(num) {
            return parseInt(num).toLocaleString('es-CL');
        }

        // ====================================
        // FUNCIONES GLOBALES PARA COMPATIBILIDAD
        // ====================================
        window.eliminar_item_carrito_virtual = eliminarItemCarrito;
        window.enviar_nota_venta = procesarNotaVenta;
        window.handleChange = function(event) { togglePanelesDespacho(event.value); };
        window.handleChange_bf = function(event) { toggleTipoDocumento(event.value === '1' ? 'boleta' : 'factura'); };
        window.quien_retira = function(event) { toggleQuienRetira(event.value); };

        // Funciones para edición de cantidades
        window.cambiarCantidad = cambiarCantidad;
        window.actualizarCantidadProducto = actualizarCantidadProducto;
    </script>
@endsection

@section('styles')
    <style>
        /* Estilos para controles de cantidad */
        .cantidad-control {
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .cantidad-control .input-group {
            max-width: 120px;
            margin: 0 auto;
        }

        .cantidad-control .cantidad-input {
            text-align: center;
            font-weight: bold;
            border-left: 0;
            border-right: 0;
            background-color: #f8f9fa;
        }

        .cantidad-control .cantidad-input:focus {
            box-shadow: none;
            border-color: #dee2e6;
            background-color: #fff;
        }

        .cantidad-control .btn {
            border-color: #dee2e6;
            background-color: #fff;
            color: #6c757d;
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
        }

        .cantidad-control .btn:hover {
            background-color: #e9ecef;
            border-color: #adb5bd;
            color: #495057;
        }

        .cantidad-control .btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        /* Responsive para tabla de carrito */
        @media (max-width: 768px) {
            .cantidad-control .input-group {
                max-width: 100px;
            }

            .cantidad-control .btn {
                padding: 0.125rem 0.375rem;
                font-size: 0.7rem;
            }
        }

        /* Animación para totales que cambian */
        .total-actualizado {
            animation: highlight 0.5s ease-in-out;
        }

        @keyframes highlight {
            0% { background-color: #fff3cd; }
            100% { background-color: transparent; }
        }

        /* Estilos para el card de observaciones */
        #observaciones {
            resize: vertical;
            min-height: 100px;
        }

        #observaciones:focus {
            border-color: #86b7fe;
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
        }

        .card-footer .btn-lg {
            padding: 0.75rem 2rem;
            font-weight: 600;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }

        .card-footer .btn-lg:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        }
    </style>
@endsection

@section('content')
    <div class="container-fluid">
        @include('fragm.postventa')

        <!-- Mensaje de estado -->
        <div id="mensaje" class="text-center"></div>

        <!-- Contenedor principal del carrito -->
        <div class="d-none" id="contenedor">
            <!-- Breadcrumb -->
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/">Inicio</a></li>
                    <li class="breadcrumb-item active">Carrito de compras</li>
                </ol>
            </nav>

            <div class="row">
                <!-- Sidebar: Resumen y Usuario -->
                <div class="col-xl-3 col-lg-4 col-md-12 mb-4">
                    <!-- Resumen del carrito -->
                    <div class="card mb-3">
                        <div class="card-header">
                            <h6 class="card-title mb-0">
                                <i class="fas fa-calculator"></i> Resumen de Compra
                            </h6>
                        </div>
                        <div class="card-body" id="resumen">
                            <div class="text-center text-muted">
                                <div class="spinner-border spinner-border-sm" role="status"></div>
                                <p class="mt-2 mb-0">Calculando totales...</p>
                            </div>
                        </div>
                    </div>

                    <!-- Información del usuario -->
                    <div class="card">
                        <div class="card-header">
                            <h6 class="card-title mb-0">
                                <i class="fas fa-user"></i> Información Personal
                            </h6>
                        </div>
                        <div class="card-body" id="detalle_usuario">
                            <div class="text-center text-muted">
                                <div class="spinner-border spinner-border-sm" role="status"></div>
                                <p class="mt-2 mb-0">Cargando datos...</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Contenido principal: Productos -->
                <div class="col-xl-9 col-lg-8 col-md-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-shopping-cart"></i> Productos en tu Carrito
                            </h5>
                            <span class="badge bg-primary" id="contador_productos">0 productos</span>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="table_detalle_nv" class="table table-hover" style="width:100%">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Imagen</th>
                                            <th>Código</th>
                                            <th>Descripción</th>
                                            <th>Cantidad</th>
                                            <th>Precio Unit.</th>
                                            <th>Total</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Card para observaciones y proceso de nota de venta -->
                    <div class="card mt-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-clipboard-list"></i> Finalizar Pedido
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="observaciones" class="form-label">
                                    <i class="fas fa-comment"></i> Observaciones (Opcional)
                                </label>
                                <textarea class="form-control"
                                          id="observaciones"
                                          name="observaciones"
                                          rows="4"
                                          placeholder="Escriba aquí cualquier observación especial para su pedido (ejemplo: horario de entrega, instrucciones de envío, etc.)"></textarea>
                                <div class="form-text">
                                    <i class="fas fa-info-circle"></i>
                                    Las observaciones serán incluidas en su nota de venta y consideradas por nuestro equipo.
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <div class="d-flex justify-content-between align-items-center">
                                <small class="text-muted">
                                    <i class="fas fa-shield-alt"></i>
                                    Transacción segura protegida
                                </small>
                                <button type="button"
                                        class="btn btn-success btn-lg"
                                        onclick="enviar_nota_venta()">
                                    <i class="fas fa-check-circle"></i> Procesar Nota de Venta
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <hr>
            @include('fragm.info_sitioweb')
        </div>

        <!-- Campos ocultos -->
        <input type="hidden" id="monto_total">
        <input type="hidden" id="numero_carrito">
        <input type="hidden" id="id_cliente">
        <input type="hidden" id="tipo_documento" value="">
    </div>
@endsection
