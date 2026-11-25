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

        function escapeHtml(valor) {
            return String(valor ?? '').replace(/[&<>'"]/g, caracter => ({
                '&': '&amp;', '<': '&lt;', '>': '&gt;', "'": '&#39;', '"': '&quot;'
            })[caracter]);
        }

        // Variables globales
        let solicitudesData = {
            tablaSolicitudes: null,
            tablaDetalle: null,
            solicitudes: [],
            solicitudActual: null
        };
        const usuarioSolicitud = @json(session('external_auth.user'));
        let carritoRepetido = { numero: null, agregados: [], omitidos: [] };

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
                    url: '/assets/datatables/es-ES.json', // Usar archivo local para evitar CORS
                },
                pageLength: 10,
                order: [[1, 'desc']], // Ordenar por fecha descendente
                columnDefs: [
                    { orderable: false, targets: [5, 6, 7] },
                    { className: "text-center", targets: [0, 2, 3, 4, 5, 6, 7] }
                ]
            });

            // Tabla de detalle de productos
            solicitudesData.tablaDetalle = $('#tabla_detalle_productos').DataTable({
                responsive: true,
                language: {
                    url: '/assets/datatables/es-ES.json', // Usar archivo local para evitar CORS
                },
                paging: false,
                lengthChange: false,
                columnDefs: [
                    { orderable: false, targets: [0, 6, 7] }, // Imagen, valoración y acciones no ordenables
                    { className: "text-center", targets: [0, 4, 5, 6, 7] }
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
                data: { 'token': token },
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
                const referenciasHTML = generarReferenciasHTML(compra);
                const estadoHTML = generarEstadoHTML(compra.estado_general);
                const estadoPagoHTML = generarEstadoPagoHTML(compra.estado_pago);
                const montoHTML = generarMontoHTML(compra.monto_total);
                const accionesHTML = generarAccionesSolicitud(compra.numero_carrito);

                solicitudesData.tablaSolicitudes.row.add([
                    referenciasHTML,
                    fechaFormateada,
                    montoHTML,
                    estadoHTML,
                    estadoPagoHTML,
                    accionesHTML.detalle,
                    accionesHTML.estado,
                    accionesHTML.repetir
                ]);
            });

            solicitudesData.tablaSolicitudes.draw();
        }

        /**
         * Relaciona la solicitud web con las notas de venta generadas.
         * Una solicitud puede tener mas de una nota si se divide en lotes.
         */
        function generarReferenciasHTML(compra) {
            const numeroSolicitud = Number(compra.numero_carrito);
            const seguimientos = Array.isArray(compra.seguimientos) ? compra.seguimientos : [];
            const notasVenta = [...new Set(seguimientos
                .map(item => Number(item.numero_nota_venta))
                .filter(Number.isFinite))];
            const notasHTML = notasVenta.length
                ? notasVenta.map(numero => `<span class="badge bg-secondary d-block mt-1">NV #${numero}</span>`).join('')
                : '<small class="text-muted d-block mt-1">NV pendiente de asociar</small>';

            return `<strong>Solicitud #${numeroSolicitud}</strong>${notasHTML}`;
        }

        /**
         * Genera HTML para el estado de la solicitud
         */
        function generarEstadoHTML(estado) {
            const coloresPermitidos = ['primary', 'warning', 'info', 'success', 'danger', 'secondary'];
            const color = estado && coloresPermitidos.includes(estado.color) ? estado.color : 'primary';
            const nombre = estado && estado.nombre ? estado.nombre : 'Solicitud recibida';
            return `<span class="badge bg-${color}">${escapeHtml(nombre)}</span>`;
        }

        function generarEstadoPagoHTML(estado) {
            const pago = estado || {codigo: 'pendiente', nombre: 'Pago pendiente', color: 'warning', icono: 'fa-clock'};
            const coloresPermitidos = ['warning', 'info', 'success', 'danger', 'secondary'];
            const color = coloresPermitidos.includes(pago.color) ? pago.color : 'warning';
            const icono = /^[a-z0-9-]+$/i.test(pago.icono || '') ? pago.icono : 'fa-clock';
            return `<span class="payment-status payment-status--${color}"><i class="fas ${icono}"></i>${escapeHtml(pago.nombre)}</span>`;
        }

        /**
         * Genera HTML para el monto
         */
        function generarMontoHTML(monto) {
            if (monto && monto > 0) {
                const neto = Math.round(Number(monto));
                const iva = Math.round(neto * 0.19);
                const totalConIva = neto + iva;
                return `$${formatearNumero(totalConIva)}`;
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
                `,
                repetir: `<button class="btn btn-repeat btn-sm" onclick="abrirRepetirPedido(${numeroCarrito})" title="Volver a comprar"><i class="fas fa-rotate-right"></i> Repetir</button>`
            };
        }

        async function abrirRepetirPedido(numeroCarrito) {
            const token = localStorage.getItem('token');
            if (!token) return mostrarErrorAutenticacion();

            mostrarEstadoCarga(true, 'Preparando pedido...');
            let errorPreparacion = null;
            try {
                const solicitud = solicitudesData.solicitudes.find(item =>
                    Number(item.numero_carrito) === Number(numeroCarrito)
                );
                const productos = solicitud && Array.isArray(solicitud.detalle)
                    ? solicitud.detalle
                    : [];
                if (!productos.length) {
                    throw new Error('Este pedido no contiene productos que se puedan repetir.');
                }
                solicitudesData.solicitudActual = {numeroCarrito, productos};
                renderizarPedidoARepetir(productos, numeroCarrito);
                await cargarDireccionesRepeticion();
                bootstrap.Modal.getOrCreateInstance(document.getElementById('modalRepetirPedido')).show();
            } catch (error) {
                errorPreparacion = error.name === 'AbortError'
                    ? new Error('La consulta demoró demasiado. Intenta nuevamente.')
                    : error;
            } finally {
                mostrarEstadoCarga(false);
            }

            if (errorPreparacion) {
                await Swal.fire({
                    icon:'error',
                    title:'No pudimos preparar el pedido',
                    text:errorPreparacion.message || 'Ocurrió un error inesperado.'
                });
            }
        }

        function renderizarPedidoARepetir(productos, numeroCarrito) {
            const filas = productos.map(item => {
                const stock = Math.max(0, Number(item.stock_actual) || 0);
                const cantidad = Math.min(Number(item.cantidad) || 1, stock);
                const disponible = stock > 0;
                const precioFinal = Number(item.precio_final || item.precio_venta || 0);
                const precioOriginal = Number(item.precio_original || precioFinal);
                const porcentaje = Number(item.porcentaje_descuento_web || 0);
                const precioHTML = porcentaje > 0
                    ? `<span class="text-muted text-decoration-line-through d-block">$${formatearNumero(precioOriginal)}</span><strong>$${formatearNumero(precioFinal)}</strong> <span class="badge bg-success">-${porcentaje}% web</span>`
                    : `<strong>$${formatearNumero(precioFinal)}</strong>`;
                return `<tr data-repeat-product="${item.id}"><td><input class="form-check-input repeat-product-check" type="checkbox" value="${item.id}" ${disponible?'checked':'disabled'} onchange="actualizarSeleccionRepeticion()"></td><td><strong>${item.descripcion || '-'}</strong><small>${item.codigo_interno || '-'}</small></td><td><input class="form-control form-control-sm repeat-quantity" type="number" min="1" max="${stock}" value="${cantidad || 1}" ${disponible?'':'disabled'}></td><td>${precioHTML}</td><td><span class="repeat-stock ${disponible?'ok':'low'}">${disponible?(stock >= item.cantidad?'Disponible':'Máximo '+stock):'Sin stock'}</span></td></tr>`;
            }).join('');
            $('#repeatOrderNumber').text(`#${numeroCarrito}`);
            $('#repeatProductsBody').html(filas);
            $('#repeatLoadSummary').addClass('d-none').empty();
            $('#repeatCheckout').addClass('d-none');
            $('#btnLoadRepeatedCart').prop('disabled', false).show();
            $('#btnSendRepeatedOrder').prop('disabled', false);
            actualizarSeleccionRepeticion();
        }

        function alternarSeleccionRepeticion(origen) {
            $('.repeat-product-check:not(:disabled)').prop('checked', origen.checked);
            actualizarSeleccionRepeticion();
        }

        function actualizarSeleccionRepeticion() {
            const total = $('.repeat-product-check:not(:disabled)').length;
            const seleccionados = $('.repeat-product-check:checked').length;
            $('#repeatSelectedCount').text(`${seleccionados} de ${total} seleccionados`);
            $('#repeatSelectAll').prop('checked', total > 0 && seleccionados === total).prop('indeterminate', seleccionados > 0 && seleccionados < total);
            $('#btnLoadRepeatedCart').prop('disabled', seleccionados === 0);
        }

        async function leerRespuestaApi(response) {
            const raw = await response.text();
            let data = {};
            try { data = raw ? JSON.parse(raw) : {}; } catch (error) { data = {message: raw}; }
            if (!response.ok) {
                const apiError = new Error(data.message || data.error || `Error HTTP ${response.status}`);
                apiError.status = response.status;
                throw apiError;
            }
            return data;
        }

        async function cargarDireccionesRepeticion() {
            let direcciones = (usuarioSolicitud && usuarioSolicitud.direcciones) || [];
            if (!direcciones.length) {
                try {
                    const token = localStorage.getItem('token');
                    const response = await fetch(`${SOLICITUDES_CONFIG.apiBaseUrl}/get-user`, {method:'POST',headers:{'Accept':'application/json','Authorization':`Bearer ${token}`}});
                    const data = await response.json();
                    direcciones = (data.user && data.user.direcciones) || [];
                } catch (error) {}
            }
            const options = ['<option value="">Selecciona una dirección</option>'].concat(direcciones.map(d => `<option value="${d.id}">${d.direccion || '-'} · ${d.nombre_comuna || ''} ${d.nombre_region || ''}</option>`));
            $('#repeatAddress').html(options.join(''));
        }

        async function cargarPedidoEnCarrito() {
            const {productos} = solicitudesData.solicitudActual || {};
            const token = localStorage.getItem('token');
            if (!productos || !productos.length) return;
            const seleccionados = $('.repeat-product-check:checked').map(function() {
                const row = $(this).closest('tr');
                return {id:Number(this.value), cantidad:Number(row.find('.repeat-quantity').val())};
            }).get();
            if (!seleccionados.length) return Swal.fire({icon:'warning',title:'Selecciona al menos un producto'});
            $('#btnLoadRepeatedCart').prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Cargando...');
            carritoRepetido = {numero:null, agregados:[], omitidos:[]};

            const confirmation = await Swal.fire({
                icon:'question',
                title:'Reemplazar carrito actual',
                text:'Se eliminará el contenido anterior y se creará un carrito nuevo solo con los productos seleccionados.',
                showCancelButton:true,
                confirmButtonText:'Sí, crear carrito nuevo',
                cancelButtonText:'Cancelar'
            });
            if (!confirmation.isConfirmed) {
                $('#btnLoadRepeatedCart').prop('disabled', false).html('<i class="fas fa-cart-plus me-2"></i>Cargar en carrito');
                return;
            }

            try {
                const controller = new AbortController();
                const timeout = setTimeout(() => controller.abort(), 20000);
                const response = await fetch(`${SOLICITUDES_CONFIG.apiBaseUrl}/repetir_carrito`, {
                    method:'POST',
                    headers:{'Accept':'application/json','Content-Type':'application/json','Authorization':`Bearer ${token}`},
                    body:JSON.stringify({token: token, productos: seleccionados}),
                    signal: controller.signal
                });
                clearTimeout(timeout);
                const data = await leerRespuestaApi(response);
                if (data.message !== 'OK' || !data.numero_carrito) {
                    throw new Error(data.message || `Respuesta incompleta del backend: ${JSON.stringify(data)}`);
                }

                carritoRepetido.numero = data.numero_carrito;
                carritoRepetido.agregados = productos.filter(item => (data.agregados || []).some(agregado => Number(agregado.id) === Number(item.id)));
                carritoRepetido.omitidos = (data.omitidos || []).map(omitido => {
                    const item = productos.find(producto => Number(producto.id) === Number(omitido.id));
                    return `${item ? item.descripcion : 'Producto '+omitido.id}: ${omitido.motivo}`;
                });

                const verifiedItems = data.agregados || [];
                if (!verifiedItems.length) throw new Error('El backend creó un carrito sin productos.');

                const warning = carritoRepetido.omitidos.length ? `<div class="repeat-warning"><strong>No se agregaron:</strong><ul>${carritoRepetido.omitidos.map(x=>`<li>${x}</li>`).join('')}</ul></div>` : '';
                $('#repeatLoadSummary').removeClass('d-none').html(`<div class="repeat-success"><i class="fas fa-circle-check"></i><div><strong>${verifiedItems.length} productos en carrito #${data.numero_carrito}</strong><span>El carrito anterior fue reemplazado y verificado.</span></div></div>${warning}`);
                $('#repeatCheckout').removeClass('d-none');
                $('#btnLoadRepeatedCart').hide().html('<i class="fas fa-cart-plus me-2"></i>Cargar en carrito');
                localStorage.setItem('cart_items', verifiedItems.length);
                if (typeof revisar_carrito === 'function') revisar_carrito();
                return;
            } catch (error) {
                $('#btnLoadRepeatedCart').prop('disabled', false).html('<i class="fas fa-cart-plus me-2"></i>Cargar en carrito');
                const mensaje = error.name === 'AbortError'
                    ? 'La operación demoró demasiado. Verifica que el backend tenga disponible la ruta /repetir_carrito.'
                    : error.message;
                return Swal.fire({icon:'error',title:'No se reemplazó el carrito',text:mensaje});
            }

        }

        async function enviarPedidoRepetido() {
            const token = localStorage.getItem('token');
            const direccion = $('#repeatAddress').val();
            const archivo = $('#repeatReceipt')[0].files[0];
            if (!token) return mostrarErrorAutenticacion();
            if (!carritoRepetido.numero) return Swal.fire({icon:'warning',title:'Carrito vacío'});
            if (!direccion) return Swal.fire({icon:'warning',title:'Selecciona una dirección'});
            if (archivo && archivo.size > 5242880) return Swal.fire({icon:'warning',title:'Archivo demasiado grande',text:'El máximo permitido es 5 MB.'});

            const cantidadProductos = carritoRepetido.agregados.length;
            const confirmacion = await Swal.fire({
                icon: 'question',
                title: '¿Procesar nota de venta?',
                html: `Se enviará el pedido con <strong>${cantidadProductos} producto${cantidadProductos === 1 ? '' : 's'}</strong> y se generará una nueva nota de venta.<br><br>¿Confirmas que los datos son correctos?`,
                showCancelButton: true,
                confirmButtonText: '<i class="fas fa-check me-1"></i> Sí, procesar',
                cancelButtonText: 'Volver a revisar',
                confirmButtonColor: '#198754',
                cancelButtonColor: '#6c757d',
                reverseButtons: true,
                focusCancel: true,
                allowOutsideClick: false,
                didOpen: () => {
                    const container = Swal.getContainer();
                    if (container) container.style.zIndex = '20000';
                }
            });
            if (!confirmacion.isConfirmed) return;

            const form = new FormData();
            form.append('numero_carrito', carritoRepetido.numero);
            form.append('token', token);
            form.append('id_direccion', direccion);
            form.append('observaciones', $('#repeatNotes').val().trim() || `Pedido repetido desde solicitud ${solicitudesData.solicitudActual.numeroCarrito}`);
            if (archivo) form.append('comprobante_pago', archivo);
            $('#btnSendRepeatedOrder').prop('disabled',true);
            mostrarEstadoCarga(true,'Enviando nota de venta...');
            let resultadoEnvio = null;
            let errorEnvio = null;
            try {
                const response = await fetch(`${SOLICITUDES_CONFIG.apiBaseUrl}/enviar_nota_venta`, {method:'POST',headers:{'Accept':'application/json','Authorization':`Bearer ${token}`},body:form});
                const raw = await response.text();
                let data; try { data=JSON.parse(raw); } catch(e) { data={status:raw}; }
                if (!response.ok || data.status !== 'OK') throw new Error(data.message || 'No se pudo generar la nota de venta.');
                resultadoEnvio = data;
                carritoRepetido = {numero:null, agregados:[], omitidos:[]};
                localStorage.setItem('cart_items', 0);
                if (typeof revisar_carrito === 'function') revisar_carrito();
                const modalElement = document.getElementById('modalRepetirPedido');
                const modal = bootstrap.Modal.getInstance(modalElement);
                if (modal) modal.hide();
            } catch(error) {
                errorEnvio = error;
            } finally {
                mostrarEstadoCarga(false); $('#btnSendRepeatedOrder').prop('disabled',false);
            }

            if (errorEnvio) {
                await Swal.fire({icon:'error',title:'No pudimos enviar el pedido',text:errorEnvio.message});
                return;
            }

            await Swal.fire({icon:'success',title:'Pedido enviado',text:resultadoEnvio.message || 'La nota de venta fue generada correctamente.'});
            document.body.classList.remove('modal-open');
            document.body.style.removeProperty('padding-right');
            document.querySelectorAll('.modal-backdrop').forEach(element => element.remove());
            window.location.reload();
        }

        // ====================================
        // FUNCIONES DE DETALLE DE SOLICITUD
        // ====================================

        /**
         * Muestra el detalle de una solicitud
         */
        function mostrarDetalleSolicitud(numeroCarrito) {
            const token = localStorage.getItem('token');
            if (!token) return mostrarErrorAutenticacion();
            const url = `${SOLICITUDES_CONFIG.apiBaseUrl}/${numeroCarrito}/detalle_venta`;

            mostrarEstadoCarga(true, 'Cargando detalle...');

            $.ajax({
                type: 'GET',
                url: url,
                headers: {
                    'Authorization': 'Bearer ' + token,
                    'Accept': 'application/json'
                },
                data: {token: token},
                success: function(repuestos) {
                    console.log('Detalle productos:', repuestos);
                    if (validarTokenRespuesta(repuestos)) return;
                    if (!Array.isArray(repuestos)) {
                        manejarErrorAPI({
                            status: 422,
                            responseJSON: {message: repuestos && (repuestos.message || repuestos.error)}
                        }, 'La API no entregó el detalle del pedido');
                        return;
                    }
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

            if (!Array.isArray(repuestos)) {
                solicitudesData.tablaDetalle.draw();
                mostrarResumenDetalle(0, 0);
                return;
            }

            let montoTotal = 0;

            repuestos.forEach(function(item) {
                const precio = Number(item.precio_venta).toFixed(0);
                const total = item.cantidad * precio;
                montoTotal += total;

                const imagen = generarImagenProducto(item);
                const precioFormateado = generarPrecioFormateado(item);
                const valoracion = `<div id="purchased-rating-${Number(item.id)}" class="product-rating product-rating--compact"></div>`;
                const acciones = generarAccionesProducto(item.id);

                solicitudesData.tablaDetalle.row.add([
                    imagen,
                    item.codigo_interno || '-',
                    item.marcarepuesto || '-',
                    item.descripcion || '-',
                    precioFormateado,
                    item.cantidad,
                    valoracion,
                    acciones
                ]);
            });

            solicitudesData.tablaDetalle.draw();
            repuestos.forEach(item => {
                IPSApp.product.renderRating(`#purchased-rating-${Number(item.id)}`, item.valoracion, item.id, true);
            });

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
        function generarPrecioFormateado(item) {
            const precioFinal = Number(item.precio_final || item.precio_venta || 0);
            const precioOriginal = Number(item.precio_original || precioFinal);
            const porcentaje = Number(item.porcentaje_descuento_web || 0);
            if (porcentaje <= 0) return `<strong>$${formatearNumero(precioFinal)}</strong>`;
            return `<span class="text-muted text-decoration-line-through d-block">$${formatearNumero(precioOriginal)}</span><strong>$${formatearNumero(precioFinal)}</strong> <span class="badge bg-success">-${porcentaje}% web</span>`;
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

            $('.resumen-detalle').html(resumenHTML);
        }

        // ====================================
        // FUNCIONES DE ESTADO DE SOLICITUD
        // ====================================

        /**
         * Muestra el estado de una solicitud
         */
        function mostrarEstadoSolicitud(numeroCarrito) {
            const token = localStorage.getItem('token');
            if (!token) return mostrarErrorAutenticacion();
            const url = `${SOLICITUDES_CONFIG.apiBaseUrl}/dame_detalle_retiro/${numeroCarrito}`;

            mostrarEstadoCarga(true, 'Cargando estado...');

            $.ajax({
                type: 'GET',
                url: url,
                headers: {
                    'Authorization': 'Bearer ' + token,
                    'Accept': 'application/json'
                },
                data: {token: token},
                success: function(response) {
                    console.log('Estado solicitud:', response);
                    if (validarTokenRespuesta(response)) return;
                    if (!response || typeof response !== 'object' || !response.cliente) {
                        manejarErrorAPI({
                            status: 422,
                            responseJSON: {message: response && (response.message || response.status)}
                        }, 'La API no entregó el detalle de la solicitud');
                        return;
                    }
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
            const estado = detalle.estado_general || {codigo: 'recibido', nombre: 'Solicitud recibida', porcentaje: 20};
            const seguimientos = Array.isArray(detalle.seguimientos) ? detalle.seguimientos : [];
            const esRetiro = seguimientos.some(item => item.tipo_entrega === 'retiro');
            const pasos = esRetiro
                ? ['Recibida', 'Confirmada', 'Preparando', 'Lista para retiro', 'Entregada']
                : ['Recibida', 'Confirmada', 'Preparando', 'Despachada', 'Entregada'];
            const porcentaje = Number(estado.porcentaje || 0);
            const especial = ['rechazado', 'cancelado', 'incidencia'].includes(estado.codigo);
            const timeline = pasos.map((paso, indice) => {
                const umbral = (indice + 1) * 20;
                const clase = !especial && porcentaje >= umbral ? 'is-complete' : (!especial && porcentaje + 20 >= umbral ? 'is-current' : '');
                return `<div class="tracking-step ${clase}"><span><i class="fas ${clase === 'is-complete' ? 'fa-check' : 'fa-circle'}"></i></span><small>${paso}</small></div>`;
            }).join('');
            const notas = seguimientos.map(item => `
                <div class="tracking-note">
                    <strong>Nota de venta #${Number(item.numero_nota_venta)}</strong>
                    <span>${escapeHtml(item.estado_general?.nombre || estado.nombre)}</span>
                    <div class="tracking-payment"><i class="fas ${escapeHtml(item.estado_pago_detalle?.icono || 'fa-clock')}"></i><small>Pago</small><b>${escapeHtml(item.estado_pago_detalle?.nombre || 'Pago pendiente')}</b></div>
                    ${item.transportista ? `<small><i class="fas fa-truck"></i> ${escapeHtml(item.transportista)}${item.numero_seguimiento ? ` · ${escapeHtml(item.numero_seguimiento)}` : ''}</small>` : ''}
                    ${item.observacion_cliente ? `<p>${escapeHtml(item.observacion_cliente)}</p>` : ''}
                </div>
            `).join('');
            const tipoEntrega = esRetiro ? 'Retiro en tienda' : 'Envío a domicilio';
            const iconoEntrega = esRetiro ? 'fa-store' : 'fa-truck';

            const contenidoHTML = `
                <div class="tracking-summary ${especial ? 'has-alert' : ''}">
                    <div><span class="tracking-summary__icon"><i class="fas ${iconoEntrega}"></i></span><div><small>${tipoEntrega}</small><h5>${escapeHtml(estado.nombre)}</h5></div></div>
                    <span class="tracking-summary__date">Actualizado ${formatearFecha(seguimientos[0]?.updated_at || detalle.updated_at || detalle.created_at)}</span>
                </div>
                <div class="tracking-timeline">${timeline}</div>
                ${notas ? `<div class="tracking-notes">${notas}</div>` : '<div class="tracking-note"><strong>Solicitud recibida</strong><p>Estamos procesando tu pedido. Pronto verás sus próximas actualizaciones aquí.</p></div>'}
                <div class="tracking-address">
                    <h6><i class="fas fa-map-marker-alt"></i> Datos de entrega</h6>
                    <dl>
                        <div><dt>Región</dt><dd>${escapeHtml(detalle.region || '-')}</dd></div>
                        <div><dt>Comuna</dt><dd>${escapeHtml(detalle.comuna || '-')}</dd></div>
                        <div><dt>Dirección</dt><dd>${escapeHtml(detalle.direccion || '-')}</dd></div>
                        <div><dt>Contacto</dt><dd>${escapeHtml(detalle.cliente?.telf1 || detalle.cliente?.telf2 || '-')}</dd></div>
                    </dl>
                </div>`;

            $('#contenido_estado').html(contenidoHTML);
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
            if (response && response.status && (
                response.status === 'Token is Invalid' ||
                response.status === 'Token is Expired' ||
                response.status === 'Authorization Token not found!'
            )) {
                localStorage.removeItem('token');
                sessionStorage.clear();
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
            let mensaje = xhr.responseJSON?.message || mensajeBase;

            if (xhr.status === 401) {
                localStorage.removeItem('token');
                sessionStorage.clear();
                mostrarErrorAutenticacion();
                return;
            }

            if (xhr.status === 403 && xhr.responseJSON?.code === 'CLIENTE_MOROSO') {
                localStorage.removeItem('token');
                sessionStorage.clear();
            } else if (xhr.status === 404) {
                mensaje = 'Recurso no encontrado';
            } else if (xhr.status >= 500) {
                mensaje = 'Error interno del servidor. Intente más tarde.';
            }

            Swal.fire({
                icon: xhr.status === 403 ? 'warning' : 'error',
                title: xhr.status === 403 ? 'Acceso restringido' : 'Error',
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
        window.abrirRepetirPedido = abrirRepetirPedido;
        window.cargarPedidoEnCarrito = cargarPedidoEnCarrito;
        window.enviarPedidoRepetido = enviarPedidoRepetido;
        window.alternarSeleccionRepeticion = alternarSeleccionRepeticion;
        window.actualizarSeleccionRepeticion = actualizarSeleccionRepeticion;
    </script>
@endsection

@section('styles')
    <link href="{{ asset('css/solicitudes.css') }}" rel="stylesheet">
    <link href="{{ asset('css/solicitudes-seleccion.css') }}" rel="stylesheet">
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

        .tracking-summary {display:flex;justify-content:space-between;align-items:center;padding:1.1rem 1.25rem;border-radius:14px;background:linear-gradient(120deg,#123e4c,#286071);color:#fff}
        .tracking-summary>div{display:flex;align-items:center;gap:.85rem}.tracking-summary__icon{display:grid;width:44px;height:44px;place-items:center;border-radius:12px;background:rgba(255,255,255,.14);font-size:1.2rem}.tracking-summary small{color:rgba(255,255,255,.7)}.tracking-summary h5{margin:0;color:#fff;font-weight:800}.tracking-summary__date{font-size:.78rem;color:rgba(255,255,255,.75)}.tracking-summary.has-alert{background:linear-gradient(120deg,#8b302a,#c35342)}
        .tracking-timeline{display:grid;grid-template-columns:repeat(5,1fr);margin:2rem 0 1.5rem;position:relative}.tracking-timeline:before{content:"";position:absolute;top:17px;left:10%;right:10%;height:3px;background:#dbe3e6}.tracking-step{position:relative;z-index:1;text-align:center;color:#98a4a9}.tracking-step span{display:grid;width:36px;height:36px;margin:0 auto .5rem;place-items:center;border:3px solid #dbe3e6;border-radius:50%;background:#fff;font-size:.65rem}.tracking-step small{font-weight:700}.tracking-step.is-complete{color:#168657}.tracking-step.is-complete span{border-color:#168657;background:#168657;color:#fff}.tracking-step.is-current{color:#c36629}.tracking-step.is-current span{border-color:#c36629;color:#c36629}
        .tracking-notes{display:grid;gap:.65rem}.tracking-note{display:grid;gap:.2rem;padding:.9rem 1rem;border:1px solid #e0e7e9;border-radius:11px;background:#f8fafb}.tracking-note strong{color:#173f4d}.tracking-note span{color:#168657;font-weight:700}.tracking-note small{color:#63737a}.tracking-note p{margin:.25rem 0 0;color:#56666c}
        .tracking-address{margin-top:1rem;padding:1rem 1.15rem;border-radius:11px;background:#f3f6f7}.tracking-address h6{color:#173f4d;font-weight:800}.tracking-address dl{display:grid;grid-template-columns:1fr 1fr;gap:.6rem 1.5rem;margin:0}.tracking-address dl div{display:grid;grid-template-columns:90px 1fr}.tracking-address dt{color:#738087}.tracking-address dd{margin:0;color:#253a42}
        .payment-status{display:inline-flex;align-items:center;gap:.4rem;padding:.42rem .65rem;border:1px solid transparent;border-radius:999px;font-size:.76rem;font-weight:800;white-space:nowrap}.payment-status--warning{border-color:#f1d58a;background:#fff8df;color:#8b6508}.payment-status--info{border-color:#acdce8;background:#eaf8fb;color:#176779}.payment-status--success{border-color:#9ed8bd;background:#eaf8f1;color:#137148}.payment-status--danger{border-color:#efb0b5;background:#fff0f1;color:#a62833}.payment-status--secondary{border-color:#ccd3d7;background:#f1f3f4;color:#566168}.tracking-payment{display:flex;align-items:center;gap:.45rem;width:max-content;margin-top:.35rem;padding:.42rem .65rem;border-radius:8px;background:#eef4f6;color:#173f4d}.tracking-payment small{font-size:.7rem;text-transform:uppercase;letter-spacing:.05em}.tracking-payment b{font-size:.78rem}
        .product-rating--compact{display:flex;min-width:185px;flex-wrap:wrap;justify-content:center;gap:.2rem .5rem;padding:.55rem;background:#fff}.product-rating--compact .product-rating__summary{order:2}.product-rating--compact .product-rating__summary strong{font-size:1rem}.product-rating--compact .product-rating__summary span{display:none}.product-rating--compact .product-rating__stars{order:1;width:100%;justify-content:center}.product-rating--compact .product-rating__star{font-size:1.05rem}.product-rating--compact>small{order:3;width:100%;font-size:.66rem;text-align:center}
        @media(max-width:767.98px){.tracking-summary{align-items:flex-start;flex-direction:column;gap:.75rem}.tracking-timeline{overflow-x:auto;grid-template-columns:repeat(5,105px);padding-bottom:.5rem}.tracking-timeline:before{display:none}.tracking-address dl{grid-template-columns:1fr}.tracking-address dl div{grid-template-columns:85px 1fr}}
    </style>
@endsection

@section('content')
    <header class="requests-hero"><div class="container"><span>Centro de pedidos</span><h1>Mis solicitudes</h1><p>Consulta tus compras, revisa el despacho o vuelve a pedir en pocos pasos.</p></div></header>
    <div class="container-fluid requests-container">
        @include('fragm.postventa')

        <!-- Breadcrumb -->
        {{-- <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/">Inicio</a></li>
                <li class="breadcrumb-item active">Mis Solicitudes</li>
            </ol>
        </nav> --}}

        <!-- Contenedor principal -->
        <div id="contenedor_principal">
            <!-- Header -->
            <div class="row mb-4 requests-heading">
                <div class="col-12">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h2 class="mb-1">
                                <i class="fas fa-clipboard-list text-primary"></i>
                                Mis Solicitudes de Compra
                            </h2>
                            <p class="text-muted mb-0">Precios y stock se vuelven a validar cuando repites una compra.</p>
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
            <div class="card requests-card">
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
                                    <th><i class="fas fa-hashtag"></i> Solicitud / Nota de venta</th>
                                    <th><i class="fas fa-calendar"></i> Fecha</th>
                                    <th><i class="fas fa-dollar-sign"></i> Monto con IVA</th>
                                    <th><i class="fas fa-info-circle"></i> Estado</th>
                                    <th><i class="fas fa-credit-card"></i> Estado del pago</th>
                                    <th><i class="fas fa-eye"></i> Detalle</th>
                                    <th><i class="fas fa-shipping-fast"></i> Seguimiento</th>
                                    <th><i class="fas fa-rotate-right"></i> Volver a pedir</th>
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

        <div class="modal fade" id="modalRepetirPedido" tabindex="-1" aria-labelledby="repeatOrderTitle" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable"><div class="modal-content repeat-modal">
                <div class="modal-header repeat-modal__header">
                    <div><span class="repeat-eyebrow">Nueva nota de venta · flujo atomic-v2</span><h5 class="modal-title" id="repeatOrderTitle">Repetir pedido <strong id="repeatOrderNumber"></strong></h5><p>Revisaremos stock y precios antes de cargar los productos.</p></div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body p-0"><div class="repeat-layout">
                    <section class="repeat-products"><div class="repeat-products-title"><h6><span>1</span> Productos del pedido</h6><small id="repeatSelectedCount"></small></div><div class="table-responsive"><table class="table repeat-table"><thead><tr><th><input id="repeatSelectAll" class="form-check-input" type="checkbox" onchange="alternarSeleccionRepeticion(this)" aria-label="Seleccionar todos"></th><th>Producto</th><th>Cantidad</th><th>Precio actual</th><th>Disponibilidad</th></tr></thead><tbody id="repeatProductsBody"></tbody></table></div><div id="repeatLoadSummary" class="d-none"></div></section>
                    <aside class="repeat-checkout"><h6><span>2</span> Entrega y respaldo</h6><div id="repeatCheckout" class="d-none">
                        <label for="repeatAddress" class="form-label">Dirección de entrega *</label><select id="repeatAddress" class="form-select mb-3"></select>
                        <label for="repeatNotes" class="form-label">Observaciones</label><textarea id="repeatNotes" class="form-control mb-3" rows="3" maxlength="500" placeholder="Indicaciones para esta nueva nota de venta"></textarea>
                        <label for="repeatReceipt" class="form-label">Comprobante de pago</label><div class="repeat-upload"><i class="fas fa-cloud-arrow-up"></i><input id="repeatReceipt" class="form-control" type="file" accept="image/jpeg,image/png,application/pdf"><small>JPG, PNG o PDF · máximo 5 MB</small></div>
                        <button id="btnSendRepeatedOrder" class="btn repeat-submit" type="button" onclick="enviarPedidoRepetido()"><i class="fas fa-paper-plane me-2"></i>Enviar nueva nota de venta</button>
                        <a href="{{ url('/carrito') }}" class="btn btn-link repeat-cart-link"><i class="fas fa-cart-shopping me-1"></i>Revisar carrito completo</a>
                    </div><div class="repeat-placeholder"><i class="fas fa-shield-check"></i><p>Primero carga los productos para habilitar los datos de envío.</p></div></aside>
                </div></div>
                <div class="modal-footer"><button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button><button id="btnLoadRepeatedCart" type="button" class="btn btn-repeat-primary" onclick="cargarPedidoEnCarrito()"><i class="fas fa-cart-plus me-2"></i>Cargar en carrito</button></div>
            </div></div>
        </div>

        <!-- Modal Detalle de Solicitud -->
        <div class="modal fade" id="modalDetalleSolicitud" tabindex="-1" aria-labelledby="modalDetalleSolicitudLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <img src="{{ asset('assets/images/logo_ips2025.png') }}" alt="Logo IPS Autopartes" class="logo_header">
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
                                <div class="resumen-detalle">
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
                                        <th>Tu valoración</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Se llena dinámicamente -->
                                </tbody>
                            </table>
                        </div>

                        <!-- Resumen inferior -->
                        <div class="card mt-3 mb-3">
                            <div class="card-body">
                                <h6 class="card-title">
                                    <i class="fas fa-calculator"></i> Resumen de la Solicitud
                                </h6>
                                <div class="resumen-detalle">
                                    <!-- Se llena dinámicamente -->
                                </div>
                            </div>
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
                        <img src="{{ asset('assets/images/logo_ips2025.png') }}" alt="Logo IPS Autopartes" class="logo_header">
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
