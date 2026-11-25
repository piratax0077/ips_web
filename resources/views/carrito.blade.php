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
                    url: '/assets/datatables/es-ES.json', // Usar archivo local para evitar CORS
                },
                paging: false,
                lengthChange: false,
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

            // No mostrar SweetAlert, usar solo el skeleton loader
            // mostrarEstadoCarga(true, 'Cargando carrito...');

            $.ajax({
                type: 'GET',
                url: url,
                headers: {
                    "Authorization": 'Bearer ' + token
                },
                data:{ 'token': token },
                success: function(response) {
                    manejarRespuestaCarrito(response);
                },
                error: function(xhr) {
                    $('#carrito-loading').fadeOut(300);
                    manejarErrorAPI(xhr, 'Error al cargar el carrito');
                },
                complete: function() {
                    // Ya no es necesario cerrar SweetAlert
                    // mostrarEstadoCarga(false);
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
                const precio = Number(item.precio_final || item.precio_venta || 0).toFixed(0);
                const total = item.cantidad * precio;

                const imagen = generarImagenProducto(item);
                const cantidadEditable = generarCantidadEditable(item.id, item.cantidad);
                const precioFormateado = generarPrecioFormateado(item);
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
        function generarPrecioFormateado(item) {
            const precioFinal = Number(item.precio_final || item.precio_venta || 0);
            const precioOriginal = Number(item.precio_original || precioFinal);
            const porcentaje = Number(item.porcentaje_descuento_web || 0);
            const precioAnterior = porcentaje > 0
                ? `<div class="text-muted text-decoration-line-through small">$${formatearNumero(precioOriginal)}</div>`
                : '';
            const badgeDescuento = porcentaje > 0
                ? ` <span class="badge bg-success">-${porcentaje}% web</span>`
                : '';
            const badgeOferta = item.oferta == 1 ? ' <span class="badge bg-danger">Oferta</span>' : '';
            return `${precioAnterior}<strong>$${formatearNumero(precioFinal)}</strong>${badgeDescuento}${badgeOferta}`;
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
                               max="1000"
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
            let total = 0;

            productos.forEach(item => {
                const precio = Number(item.precio_final || item.precio_venta || 0).toFixed(0);
                total += (item.cantidad * precio);
            });

            const montoNeto = total;
            const iva = Math.round(montoNeto * CARRITO_CONFIG.ivaPercent);
            const totalConIva = montoNeto + iva;

            carritoData.montoTotal = totalConIva;
            $('#monto_total').val(totalConIva);

            mostrarResumenTotales(montoNeto, iva, totalConIva);

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
                data: { 'token': token },
                success: function(response) {
                    const repuestos = response.repuestos || [];

                    if (repuestos.length === 0) {
                        mostrarCarritoVacio();
                    } else {
                        manejarRespuestaCarrito(response);
                        mostrarNotificacion('Producto eliminado exitosamente', 'success');
                    }
                    // Actualizar contador del carrito global
                    if (typeof revisar_carrito === 'function') {
                        revisar_carrito();
                    }
                        // Limpiar padding-right del body por modales Bootstrap
                        $('body').css('padding-right', '');
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

            if (nuevaCantidad <= 1000) { // Límite máximo
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

            if (cantidad > 1000) {
                mostrarError('Error', 'La cantidad máxima es 1000');
                $(`#cantidad_${idProducto}`).val(1000);
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

            if (IPSApp.state.quantityUpdates[idProducto]) {
                const itemActual = carritoData.items.find(i => i.id == idProducto);
                if (itemActual) {
                    $(`#cantidad_${idProducto}`).val(itemActual.cantidad);
                }
                return;
            }

            const url = `${CARRITO_CONFIG.apiBaseUrl}/actualizar_cantidad_carrito`;

            // Obtener la cantidad anterior desde los datos guardados, NO del input
            const item = carritoData.items.find(i => i.id == idProducto);
            const cantidadAnterior = item ? item.cantidad : nuevaCantidad;

            const data = {
                id_repuesto: idProducto,
                numero_carrito: carritoData.numeroCarrito,
                nueva_cantidad: nuevaCantidad,
                token: token
            };

            // Deshabilitar input mientras se procesa
            const inputCantidad = $(`#cantidad_${idProducto}`);
            const controlesCantidad = inputCantidad.closest('.input-group').find('button, input');
            IPSApp.state.quantityUpdates[idProducto] = true;
            controlesCantidad.prop('disabled', true);

            console.log('Cantidad anterior guardada:', cantidadAnterior);
            console.log('Nueva cantidad solicitada:', nuevaCantidad);

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
                    console.log(response);
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
                                    ejecutarActualizacionCantidad(idProducto, stockDisponible);
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
                                    eliminarItemCarrito(idProducto);
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
                        mostrarErrorAutenticacion();
                    } else if (xhr.status === 404) {
                        mostrarError('Error', 'Producto no encontrado en el carrito');
                    } else {
                        mostrarError('Error', mensajeError);
                    }
                },
                complete: function() {
                    delete IPSApp.state.quantityUpdates[idProducto];
                    controlesCantidad.prop('disabled', false);
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
                const precio = Number(item.precio_final || item.precio_venta || 0).toFixed(0);
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
                const precio = Number(item.precio_final || item.precio_venta || 0).toFixed(0);
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
            const totalProductos = carritoData.items.length;
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
                data:{ 'token': token },
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
            console.log(user);
            const direcciones = user.direcciones || [];
            // agregar opcion de seleccione una direccion
            direcciones.unshift({ id: '', direccion: 'Seleccione una dirección', nombre_comuna: '', nombre_region: '' });
            // creamos un select con las direcciones
            const contenidoDirecciones = direcciones.map(direccion => `
                <option value="${direccion.id}">
                    ${direccion.direccion || '-'} - ${direccion.nombre_comuna || '-'}, ${direccion.nombre_region || '-'}
                </option>
            `).join('');

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
                    <div class="mb-3">
                        <small class="text-muted">Teléfono:</small>
                        <div>${user.telefono || '-'}</div>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted">Direcciones Registradas:</small>
                        <select class="form-select form-select-sm mt-1" id="select_direcciones">
                            ${contenidoDirecciones || '<option>No hay direcciones registradas</option>'}
                        </select>
                    </div>
                    <a href="{{ url('mis_datos') }}" class="btn btn-outline-success btn-sm w-100">
                        <i class="fas fa-edit"></i> Editar Datos
                    </a>
                </div>
            `;

            $('#detalle_usuario').html(contenidoHtml);
        }

        // ====================================
        // FUNCIONES DE GESTIÓN DE CARRITO
        // ====================================

        /**
         * Limpia completamente el carrito eliminando todos los productos
         */
        function limpiar_carrito() {
            const token = localStorage.getItem('token');
            if (!token) {
                mostrarErrorAutenticacion();
                return;
            }

            if (!carritoData.numeroCarrito) {
                console.log('No hay carrito para limpiar');
                return;
            }

            Swal.fire({
                title: '¿Vaciar carrito?',
                text: 'Se eliminarán todos los productos del carrito. Esta acción no se puede deshacer.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sí, vaciar carrito',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    ejecutarLimpiezaCarrito();
                }
            });
        }

        /**
         * Ejecuta la limpieza completa del carrito
         */
        function ejecutarLimpiezaCarrito() {
            const token = localStorage.getItem('token');
            const url = `${CARRITO_CONFIG.apiBaseUrl}/limpiar_carrito`;

            const data = {
                numero_carrito: carritoData.numeroCarrito,
                token: token
            };

            mostrarEstadoCarga(true, 'Vaciando carrito...');

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
                    console.log('Respuesta limpiar carrito:', response);

                    if (response === 'OK' || response.success || response.message === 'OK') {
                        // Limpiar datos locales
                        carritoData.items = [];
                        carritoData.numeroCarrito = null;
                        carritoData.montoTotal = 0;

                        // Limpiar tabla
                        if (carritoData.tablaDetalle) {
                            carritoData.tablaDetalle.clear().draw();
                        }

                        // Mostrar carrito vacío
                        mostrarCarritoVacio();

                        // Limpiar campos ocultos
                        $('#numero_carrito').val('');
                        $('#monto_total').val(0);

                        // Limpiar observaciones si existen
                        $('#observaciones').val('');

                        mostrarNotificacion('Carrito vaciado exitosamente', 'success');
                        // actualizar contador del carrito global
                        revisar_carrito();
                    } else {
                        throw new Error(response.message || 'Error al limpiar el carrito');
                    }
                },
                error: function(xhr) {
                    console.error('Error al limpiar carrito:', xhr);
                    manejarErrorAPI(xhr, 'Error al vaciar el carrito');
                },
                complete: function() {
                    mostrarEstadoCarga(false);
                }
            });
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
            const id_direccion = $('#select_direcciones').val();
            if(id_direccion === '') {
                mostrarError('Error', 'Debe seleccionar una dirección de envío');
                return;
            }

            // Obtener el archivo del comprobante
            const archivoInput = $('#comprobante_pago')[0];
            const archivo = archivoInput.files[0];

            // Usar FormData para enviar archivos
            const formData = new FormData();
            formData.append('numero_carrito', carritoData.numeroCarrito);
            formData.append('token', token);
            formData.append('id_direccion', id_direccion || '');
            formData.append('observaciones', observaciones || '');

            // Agregar el archivo si existe
            if (archivo) {
                formData.append('comprobante_pago', archivo);
                console.log('Archivo adjunto:', archivo.name, 'Tamaño:', archivo.size, 'bytes');
            } else {
                console.log('No se adjuntó comprobante de pago');
            }

            mostrarEstadoCarga(true, 'Procesando nota de venta...');

            $.ajax({
                type: 'POST',
                url: url,
                data: formData,
                processData: false,  // No procesar los datos
                contentType: false,  // No establecer contentType (se hace automático con FormData)
                headers: {
                    "Authorization": 'Bearer ' + token,
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    console.log(response);

                    // Manejar respuesta como string o JSON
                    let responseData = response;
                    if (typeof response === 'string') {
                        try {
                            responseData = JSON.parse(response);
                        } catch (e) {
                            responseData = { status: response };
                        }
                    }

                    if (response === 'OK' || responseData.status === 'OK') {
                        let mensaje = 'La nota de venta ha sido registrada exitosamente';

                        // Si se generaron múltiples notas de venta
                        if (responseData.message) {
                            mensaje = responseData.message;
                        }

                        Swal.fire({
                            icon: 'success',
                            title: 'Nota de venta procesada',
                            text: mensaje,
                            confirmButtonText: 'Continuar'
                        }).then(() => {
                            // Limpiar observaciones y archivo
                            $('#observaciones').val('');
                            $('#comprobante_pago').val('');
                            // El backend cierra el carrito al procesar la nota de venta.
                            carritoData.items = [];
                            carritoData.numeroCarrito = null;
                            carritoData.montoTotal = 0;
                            $('#numero_carrito').val('');
                            mostrarCarritoVacio();
                            localStorage.setItem('cart_items', 0);
                            // limpiar la cantidad de items en el carrito global
                            if (typeof revisar_carrito === 'function') {
                                revisar_carrito();
                            }
                            // Redirigir a historial de compras
                            // window.location.href = "{{ url('mis_compras') }}";
                        });
                    } else {
                        mostrarError('Error', responseData.message || 'No se pudo procesar la nota de venta');
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
            $('#carrito-loading').fadeOut(300, function() {
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
                `).hide().fadeIn(400);
            });
            $('#contenedor').addClass('d-none');
        }

        /**
         * Muestra el contenedor del carrito
         */
        function mostrarContenedorCarrito() {
            $('#carrito-loading').fadeOut(300, function() {
                $('#contenedor').removeClass('d-none').hide().fadeIn(400);
            });
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
            $('#carrito-loading').fadeOut(300);
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
        // FUNCIONES DE COMPROBANTE DE PAGO
        // ====================================

        /**
         * Previsualiza el archivo del comprobante antes de enviarlo
         */
        function previsualizarComprobante(input) {
            const archivo = input.files[0];

            if (!archivo) {
                return;
            }

            // Validar tamaño (5MB máximo)
            const maxSize = 5 * 1024 * 1024; // 5MB en bytes
            if (archivo.size > maxSize) {
                mostrarError('Error', 'El archivo es demasiado grande. Tamaño máximo: 5MB');
                input.value = '';
                return;
            }

            // Validar tipo de archivo
            const tiposPermitidos = ['image/jpeg', 'image/jpg', 'image/png', 'application/pdf'];
            if (!tiposPermitidos.includes(archivo.type)) {
                mostrarError('Error', 'Formato de archivo no válido. Use JPG, PNG o PDF');
                input.value = '';
                return;
            }

            // Mostrar nombre del archivo
            $('#archivo_nombre').text(archivo.name);
            $('#nombre_archivo').show();

            // Si es una imagen, mostrar vista previa
            if (archivo.type.startsWith('image/')) {
                const reader = new FileReader();

                reader.onload = function(e) {
                    $('#imagen_preview').attr('src', e.target.result);
                    $('#preview_comprobante').show();
                    $('#placeholder_comprobante').hide();
                };

                reader.readAsDataURL(archivo);
            } else {
                // Para PDFs, solo mostrar el nombre
                $('#placeholder_comprobante').hide();
                $('#preview_comprobante').hide();
            }
        }

        /**
         * Limpia el archivo del comprobante seleccionado
         */
        function limpiarComprobante() {
            $('#comprobante_pago').val('');
            $('#imagen_preview').attr('src', '');
            $('#preview_comprobante').hide();
            $('#placeholder_comprobante').show();
            $('#nombre_archivo').hide();
            $('#archivo_nombre').text('');
        }

        // ====================================
        // FUNCIONES DE COMPROBANTE DE PAGO
        // ====================================

        /**
         * Previsualiza el archivo del comprobante antes de enviarlo
         */
        function previsualizarComprobante(input) {
            const archivo = input.files[0];

            if (!archivo) {
                return;
            }

            // Validar tamaño (5MB máximo)
            const maxSize = 5 * 1024 * 1024; // 5MB en bytes
            if (archivo.size > maxSize) {
                mostrarError('Error', 'El archivo es demasiado grande. Tamaño máximo: 5MB');
                input.value = '';
                return;
            }

            // Validar tipo de archivo
            const tiposPermitidos = ['image/jpeg', 'image/jpg', 'image/png', 'application/pdf'];
            if (!tiposPermitidos.includes(archivo.type)) {
                mostrarError('Error', 'Formato de archivo no válido. Use JPG, PNG o PDF');
                input.value = '';
                return;
            }

            // Mostrar nombre del archivo
            $('#archivo_nombre').text(archivo.name);
            $('#nombre_archivo').show();

            // Si es una imagen, mostrar vista previa
            if (archivo.type.startsWith('image/')) {
                const reader = new FileReader();

                reader.onload = function(e) {
                    $('#imagen_preview').attr('src', e.target.result);
                    $('#preview_comprobante').show();
                    $('#placeholder_comprobante').hide();
                };

                reader.readAsDataURL(archivo);
            } else {
                // Para PDFs, solo mostrar el nombre
                $('#placeholder_comprobante').hide();
                $('#preview_comprobante').hide();
            }
        }

        /**
         * Limpia el archivo del comprobante seleccionado
         */
        function limpiarComprobante() {
            $('#comprobante_pago').val('');
            $('#imagen_preview').attr('src', '');
            $('#preview_comprobante').hide();
            $('#placeholder_comprobante').show();
            $('#nombre_archivo').hide();
            $('#archivo_nombre').text('');
        }

        // ====================================
        // FUNCIONES GLOBALES PARA COMPATIBILIDAD
        // ====================================
        window.eliminar_item_carrito_virtual = eliminarItemCarrito;
        window.enviar_nota_venta = procesarNotaVenta;
        window.limpiar_carrito = limpiar_carrito;
        window.handleChange = function(event) { togglePanelesDespacho(event.value); };
        window.handleChange_bf = function(event) { toggleTipoDocumento(event.value === '1' ? 'boleta' : 'factura'); };
        window.quien_retira = function(event) { toggleQuienRetira(event.value); };

        // Funciones para edición de cantidades
        window.cambiarCantidad = cambiarCantidad;
        window.actualizarCantidadProducto = actualizarCantidadProducto;

        // Exponer función para recargar carrito desde otras vistas
        window.recargarVistaCarrito = cargarDatosCarrito;
    </script>
@endsection

@section('styles')
    <link href="{{ asset('css/carrito-moderno.css') }}" rel="stylesheet">
    <style>
        /* Ajuste para header fijo */
        .container-fluid {
            /* padding-top: 20px; */
            position: relative;
            z-index: 1;
        }

        /* Estado de carga inicial */
        #carrito-loading {
            min-height: 300px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        #carrito-loading .spinner-border {
            border-width: 0.3em;
        }

        .breadcrumb {
            background-color: #f8f9fa;
            padding: 12px 20px;
            border-radius: 8px;
            margin-top: 0;
            margin-bottom: 20px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        }

        .breadcrumb-item a {
            color: #315866;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .breadcrumb-item a:hover {
            color: #667eea;
        }

        .breadcrumb-item.active {
            color: #6c757d;
        }

        /* Estilos para cards */
        .card {
            border: none;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            border-radius: 12px;
            overflow: hidden;
            transition: box-shadow 0.3s ease;
        }

        .card:hover {
            box-shadow: 0 4px 12px rgba(0,0,0,0.12);
        }

        .card-header {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-bottom: 2px solid #dee2e6;
            padding: 15px 20px;
        }

        .card-title {
            color: #315866;
            font-weight: 600;
        }

        .card-title i {
            color: #667eea;
            margin-right: 8px;
        }

        /* Espaciado entre filas */
        .row {
            margin-bottom: 20px;
        }

        /* Mensaje de estado */
        #mensaje {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 8px;
        }

        #mensaje.alert {
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

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
            .container-fluid {
                padding-top: 15px;
                padding-left: 10px;
                padding-right: 10px;
            }

            .breadcrumb {
                padding: 8px 12px;
                font-size: 0.875rem;
            }

            .card-header h5, .card-header h6 {
                font-size: 0.95rem;
            }

            .cantidad-control .input-group {
                max-width: 100px;
            }

            .cantidad-control .btn {
                padding: 0.125rem 0.375rem;
                font-size: 0.7rem;
            }
        }

        @media (max-width: 576px) {
            .container-fluid {
                padding-top: 10px;
            }

            .breadcrumb {
                padding: 6px 10px;
                font-size: 0.8rem;
            }

            .card-header {
                padding: 10px 15px;
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

        /* Estilos para botones del header */
        .gap-2 > * + * {
            margin-left: 0.5rem;
        }

        .btn-outline-danger.btn-sm {
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
        }

        /* Estilos para tabla */
        #table_detalle_nv thead th {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            color: #315866;
            font-weight: 600;
            border-bottom: 2px solid #dee2e6;
            padding: 12px 8px;
        }

        #table_detalle_nv tbody tr {
            transition: background-color 0.2s ease;
        }

        #table_detalle_nv tbody tr:hover {
            background-color: #f8f9fa;
        }

        /* Badge de contador */
        .badge.bg-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
            padding: 6px 12px;
            font-weight: 600;
            font-size: 0.85rem;
        }

        /* Botón de vaciar carrito */
        .btn-outline-danger {
            border-color: #dc3545;
            color: #dc3545;
            transition: all 0.3s ease;
        }

        .btn-outline-danger:hover {
            background-color: #dc3545;
            color: white;
            transform: translateY(-1px);
            box-shadow: 0 2px 8px rgba(220, 53, 69, 0.3);
        }
    </style>
@endsection

@section('content')
    <header class="cart-hero">
        <div class="container">
            <nav class="cart-breadcrumb" aria-label="Migas de pan"><a href="{{ url('/') }}">Inicio</a><i class="fas fa-chevron-right"></i><span>Carrito</span></nav>
            <div class="cart-hero__content"><div><span class="cart-eyebrow">Tu compra IPS Autopartes</span><h1>Revisa y confirma tu pedido</h1><p>Actualiza cantidades, verifica tus datos y envía tu nota de venta de forma segura.</p></div><div class="cart-hero__icon"><i class="fas fa-cart-shopping"></i></div></div>
        </div>
    </header>
    <div class="container cart-page">
        <!-- Estado de carga inicial -->
        <div id="carrito-loading" class="text-center py-5">
            <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
                <span class="visually-hidden">Cargando...</span>
            </div>
            <p class="mt-3 text-muted">Cargando carrito...</p>
        </div>

        <!-- Mensaje de estado -->
        <div id="mensaje" class="text-center"></div>

        <!-- Contenedor principal del carrito -->
        <div class="d-none" id="contenedor">
            @include('fragm.postventa')

            {{-- <!-- Breadcrumb -->
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/">Inicio</a></li>
                    <li class="breadcrumb-item active">Carrito de compras</li>
                </ol>
            </nav> --}}

            <!-- Primera fila: Productos (col-12) -->
            <div class="row">
                <div class="col-12">
                    <div class="card cart-products-card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-shopping-cart"></i> Productos en tu Carrito
                            </h5>
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-primary" id="contador_productos">0 productos</span>
                                <button type="button"
                                        class="btn btn-outline-danger btn-sm"
                                        onclick="limpiar_carrito()"
                                        title="Vaciar carrito">
                                    <i class="fas fa-trash"></i> Vaciar
                                </button>
                            </div>
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
                </div>
            </div>

            <!-- Segunda fila: Resumen, Información Personal y Comprobante de Pago (3 columnas) -->
            <div class="row mt-4 cart-info-grid">
                <!-- Resumen de Compra -->
                <div class="col-lg-4 col-md-12 mb-4">
                    <div class="card h-100 cart-summary-card">
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
                </div>

                <!-- Información Personal -->
                <div class="col-lg-4 col-md-12 mb-4">
                    <div class="card h-100 cart-profile-card">
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

                <!-- Comprobante de Pago -->
                <div class="col-lg-4 col-md-12 mb-4">
                    <div class="card h-100 cart-upload-card">
                        <div class="card-header">
                            <h6 class="card-title mb-0">
                                <i class="fas fa-file-upload"></i> Comprobante de Pago
                            </h6>
                        </div>
                        <div class="card-body" id="adjunto_comprobante">
                            <div class="text-center">
                                <div id="preview_comprobante" class="mb-3" style="display:none;">
                                    <img id="imagen_preview" src="" alt="Vista previa" class="img-thumbnail" style="max-width: 100%; max-height: 200px;">
                                    <button type="button" class="btn btn-sm btn-outline-danger mt-2" onclick="limpiarComprobante()">
                                        <i class="fas fa-times"></i> Eliminar
                                    </button>
                                </div>
                                <div id="placeholder_comprobante">
                                    <img src="{{ asset('assets/images/upload-file.png') }}"
                                         alt="Adjuntar comprobante"
                                         class="mb-3"
                                         style="max-width: 100px;">
                                    <p class="text-muted mb-2">Adjuntar comprobante de pago (opcional)</p>
                                </div>
                                <input type="file" class="form-control" id="comprobante_pago"
                                       accept="image/jpeg,image/jpg,image/png,application/pdf"
                                       onchange="previsualizarComprobante(this)">
                                <small class="text-muted d-block mt-2">
                                    <i class="fas fa-info-circle"></i> Formatos: JPG, PNG, PDF (Máx. 5MB)
                                </small>
                                <small id="nombre_archivo" class="text-primary d-block mt-1" style="display:none;">
                                    <i class="fas fa-file"></i> <span id="archivo_nombre"></span>
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tercera fila: Finalizar Pedido (col-12) -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card cart-finish-card">
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
                                        class="btn btn-success btn-lg cart-submit"
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
