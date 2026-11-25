@extends('layouts.app')

@section('javascript')
<script>
$(document).ready(function() {
    // Obtener ID del repuesto desde la URL
    const urlParts = window.location.pathname.split('/');
    const idRepuesto = urlParts[urlParts.length - 1];

    if (idRepuesto && !isNaN(idRepuesto)) {
        cargarRepuesto(idRepuesto);
    } else {
        mostrarError('ID de repuesto inválido');
        window.location.href = '/repuestos';
    }
});

function cargarRepuesto(repuestoId) {
    const token = localStorage.getItem('token');
    const url = `{{ config("app.api_url", "https://intranet.ipschile.cl") }}/api/${repuestoId}/repuesto`;

    $.ajax({
        type: 'GET',
        url: url,
        beforeSend: function() {
            mostrarCargando();
        },
        headers: {
            "Authorization": 'Bearer ' + token,
            "Accept": "application/json"
        },
        success: function(data) {
            ocultarCargando();

            if (data.status === 'Token is Invalid' || data.status === 'Token is Expired') {
                manejarTokenInvalido(data.status);
                return;
            }

            if (data && data[0]) {
                procesarDatosRepuesto(data, repuestoId);
                $('#contenedor').removeClass('d-none').addClass('d-block');
            } else {
                mostrarError('No se encontraron datos del repuesto');
            }
        },
        error: function(xhr) {
            ocultarCargando();
            console.error('Error al cargar repuesto:', xhr);

            if (xhr.status === 401) {
                manejarTokenInvalido('Token is Invalid');
            } else if (xhr.status === 404) {
                mostrarError('Repuesto no encontrado');
                setTimeout(() => window.location.href = '/repuestos', 2000);
            } else {
                mostrarError('Error al cargar el repuesto. Por favor, intente nuevamente.');
            }
        }
    });
}

function procesarDatosRepuesto(data, repuestoId) {
    const repuesto = data[0];
    const fotos = data[1] || [];
    const flag = data[4]; // Información de ofertas
    const idSiguiente = data[5];
    const idAnterior = data[6];
    const rectificadores = data[7] || [];
    const alternadores = data[8] || [];

    // Configurar información básica
    configurarInformacionBasica(repuesto, flag);

    // Configurar imágenes
    configurarImagenes(fotos, repuesto.descripcion);

    // Configurar navegación
    configurarNavegacion(repuesto, idAnterior, idSiguiente);

    // Configurar país de origen
    configurarPaisOrigen(repuesto);

    // Configurar rectificadores y alternadores si existen
    configurarComponentesAdicionales(rectificadores, alternadores);

    // Configurar validación de stock
    configurarValidacionStock(repuesto.stock_actual);

    // Guardar datos para funciones globales
    window.datosRepuesto = {
        id: repuestoId,
        stock: repuesto.stock_actual,
        precio: flag && flag.precio_actualizado ? flag.precio_actualizado : repuesto.precio_venta
    };
}

function configurarInformacionBasica(repuesto, flag) {
    // Título y códigos
    $('#titulo_repuesto').text(repuesto.descripcion || 'Sin descripción');
    $('#enlace_repuesto').text(repuesto.descripcion || 'Repuesto');
    $('#codigo_interno_repuesto').text(repuesto.codigo_interno || 'N/A');
    $('#cod_fab_repuesto').text(repuesto.codigo_fab || 'N/A');
    $('#marca_repuesto').text(repuesto.marcarepuesto || 'Sin marca');

    // Stock
    const stockHtml = repuesto.stock_actual >= 10
        ? '<span class="badge bg-success"><i class="fas fa-check-circle me-1"></i>Disponible</span>'
        : repuesto.stock_actual > 0
            ? `<span class="badge bg-warning">${repuesto.stock_actual} <small>disponibles</small></span>`
            : '<span class="badge bg-danger"><i class="fas fa-times-circle me-1"></i>Sin stock</span>';

    $('#stock_actual').html(stockHtml);

    // Precio
    const precioFormateado = formatNumber(parseInt(repuesto.precio_venta));
    let precioHtml;

    if (flag && flag.precio_actualizado) {
        const precioOferta = formatNumber(parseInt(flag.precio_actualizado));
        precioHtml = `
            <div class="precio-container">
                <span class="precio-actual">$${precioOferta}</span>
                <span class="precio-original text-muted text-decoration-line-through">$${precioFormateado}</span>
                <span class="badge bg-danger ms-2">OFERTA</span>
            </div>
        `;
    } else {
        precioHtml = `<span class="precio-actual">$${precioFormateado}</span>`;
    }

    $('#precio').html(precioHtml);
}

function configurarImagenes(fotos, descripcion) {
    const carouselInner = $('#carousel-inner');
    const previewPhotos = $('#preview_photos');

    carouselInner.empty();
    previewPhotos.empty();

    if (fotos && fotos.length > 0) {
        // Carousel principal
        fotos.forEach((foto, index) => {
            const isActive = index === 0 ? 'active' : '';
            const imagenUrl = `https://intranet.ipschile.cl/storage/${foto.urlfoto}`;

            carouselInner.append(`
                <div class="carousel-item ${isActive}">
                    <img class="d-block w-100 fotos_repuesto"
                         src="${imagenUrl}"
                         alt="${descripcion}"
                         onerror="this.src='{{ asset('assets/images/no-image.png') }}'"
                         style="height: 400px; object-fit: contain; background: #f8f9fa;">
                </div>
            `);
        });

        // Miniaturas
        if (fotos.length > 1) {
            fotos.forEach((foto, index) => {
                const imagenUrl = `https://intranet.ipschile.cl/storage/${foto.urlfoto}`;
                previewPhotos.append(`
                    <div class="col-12 mb-2">
                        <img src="${imagenUrl}"
                             class="img-thumbnail w-100 miniatura-foto"
                             alt="${descripcion}"
                             data-slide="${index}"
                             onclick="irASlide(${index})"
                             onerror="this.src='{{ asset('assets/images/no-image.png') }}'"
                             style="height: 80px; object-fit: cover; cursor: pointer;">
                    </div>
                `);
            });
        }
    } else {
        // Imagen por defecto
        const imagenDefault = '{{ asset("assets/images/no-image.png") }}';
        carouselInner.html(`
            <div class="carousel-item active">
                <img class="d-block w-100 fotos_repuesto"
                     src="${imagenDefault}"
                     alt="Sin imagen disponible"
                     style="height: 400px; object-fit: contain; background: #f8f9fa;">
            </div>
        `);

        previewPhotos.html(`
            <div class="col-12 text-center text-muted">
                <i class="fas fa-image fa-2x mb-2"></i>
                <p class="small">Sin imágenes</p>
            </div>
        `);
    }
}

function configurarNavegacion(repuesto, idAnterior, idSiguiente) {
    if (idAnterior) {
        $('#repuesto_anterior').attr('href', `/repuesto/${idAnterior}`);
    } else {
        $('#repuesto_anterior').addClass('disabled').removeAttr('href');
    }

    if (idSiguiente) {
        $('#repuesto_siguiente').attr('href', `/repuesto/${idSiguiente}`);
    } else {
        $('#repuesto_siguiente').addClass('disabled').removeAttr('href');
    }
}

function configurarPaisOrigen(repuesto) {
    const paises = {
        1: { bandera: 'https://flagcdn.com/w80/cn.png', nombre: 'China' },
        2: { bandera: 'https://flagcdn.com/w80/mx.png', nombre: 'México' },
        3: { bandera: 'https://flagcdn.com/w80/tw.png', nombre: 'Taiwan' },
        4: { bandera: 'https://flagcdn.com/w80/cl.png', nombre: 'Chile' },
        5: { bandera: 'https://flagcdn.com/w80/cl.png', nombre: 'Chile' },
        6: { bandera: 'https://flagcdn.com/w80/jp.png', nombre: 'Japón' },
        7: { bandera: 'https://flagcdn.com/w80/id.png', nombre: 'Indonesia' },
        10: { bandera: 'https://flagcdn.com/w80/de.png', nombre: 'Alemania' },
        11: { bandera: 'https://flagcdn.com/w80/in.png', nombre: 'India' },
        13: { bandera: 'https://flagcdn.com/w80/br.png', nombre: 'Brasil' },
        14: { bandera: 'https://flagcdn.com/w80/es.png', nombre: 'España' },
        17: { bandera: 'https://flagcdn.com/w80/ar.png', nombre: 'Argentina' },
        18: { bandera: 'https://flagcdn.com/w80/tr.png', nombre: 'Turquía' },
        20: { bandera: 'https://flagcdn.com/w80/my.png', nombre: 'Malasia' },
        21: { bandera: 'https://flagcdn.com/w80/pe.png', nombre: 'Perú' },
        23: { bandera: 'https://flagcdn.com/w80/fr.png', nombre: 'Francia' },
        24: { bandera: 'https://flagcdn.com/w80/us.png', nombre: 'Estados Unidos' },
        25: { bandera: 'https://flagcdn.com/w80/nl.png', nombre: 'Países Bajos' },
        26: { bandera: 'https://flagcdn.com/w80/gb.png', nombre: 'Reino Unido' },
        29: { bandera: 'https://flagcdn.com/w80/ro.png', nombre: 'Rumania' },
        33: { bandera: 'https://flagcdn.com/w80/bg.png', nombre: 'Bulgaria' }
    };

    const pais = paises[repuesto.id_pais];
    if (pais) {
        $('#pais_origen').html(`
            <img src="${pais.bandera}"
                 alt="${pais.nombre}"
                 style="width: 30px; height: 20px; object-fit: cover; margin-right: 8px;"
                 onerror="this.style.display='none'">
            <span>${pais.nombre}</span>
        `);
    } else {
        $('#pais_origen').text(repuesto.nombre_pais || 'No especificado');
    }
}

function configurarComponentesAdicionales(rectificadores, alternadores) {
    if (rectificadores && rectificadores.length > 0) {
        $('#tr_rectificadores').removeClass('d-none');
        const rectificadoresText = rectificadores.map(r => r.rectificador).join('<br>');
        $('#rectificadores').html(rectificadoresText);
    } else {
        $('#tr_rectificadores').addClass('d-none');
    }

    if (alternadores && alternadores.length > 0) {
        $('#tr_alternadores').removeClass('d-none');
        const alternadoresText = alternadores.map(a => a.alternador).join('<br>');
        $('#alternadores').html(alternadoresText);
    } else {
        $('#tr_alternadores').addClass('d-none');
    }
}

function configurarValidacionStock(stock) {
    if (stock <= 0) {
        $('#btn_agregar_carrito').prop('disabled', true).text('Sin stock');
        $('#cantidad').prop('disabled', true);
    } else {
        $('#btn_agregar_carrito').prop('disabled', false).text('Agregar al carrito');
        $('#cantidad').prop('disabled', false);
    }
}

function irASlide(slideIndex) {
    if (typeof bootstrap !== 'undefined') {
        const carousel = new bootstrap.Carousel(document.getElementById('carouselExampleControls'));
        carousel.to(slideIndex);
    }
}

function aumentar() {
    const cantidadInput = $('#cantidad');
    const stockMaximo = window.datosRepuesto?.stock || 1;
    let cantidad = parseInt(cantidadInput.val()) || 1;

    if (cantidad < stockMaximo) {
        cantidad++;
        cantidadInput.val(cantidad);
        actualizarPrecioCalculado(cantidad);
    } else {
        mostrarAdvertencia('No puede agregar más de ' + stockMaximo + ' unidades');
    }
}

function disminuir() {
    const cantidadInput = $('#cantidad');
    let cantidad = parseInt(cantidadInput.val()) || 1;

    if (cantidad > 1) {
        cantidad--;
        cantidadInput.val(cantidad);
        actualizarPrecioCalculado(cantidad);
    }
}

function actualizarPrecioCalculado(cantidad) {
    const precio = window.datosRepuesto?.precio || 0;
    const total = cantidad * precio;

    // Actualizar visualización del total si existe un elemento para ello
    if ($('#precio_calculado').length) {
        $('#precio_calculado').text('Total: $' + formatNumber(total));
    }
}

function agregarCarrito() {
    const token = localStorage.getItem('token');

    if (!token) {
        mostrarError('Debe iniciar sesión para agregar productos al carrito');
        return;
    }

    const cantidad = parseInt($('#cantidad').val()) || 1;
    const idRepuesto = window.datosRepuesto?.id;
    const stockDisponible = window.datosRepuesto?.stock || 0;

    if (cantidad > stockDisponible) {
        mostrarError('La cantidad solicitada supera el stock disponible');
        return;
    }

    if (!idRepuesto) {
        mostrarError('Error: ID de repuesto no encontrado');
        return;
    }

    const url = "{{ env('URL_AGREGAR_CARRITO') }}";
    const data = {
        idrep: idRepuesto,
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
        beforeSend: function() {
            $('#btn_agregar_carrito').prop('disabled', true).text('Agregando...');
        },
        success: function(response) {
            $('#btn_agregar_carrito').prop('disabled', false).text('Agregar al carrito');

            if (response.message === 'OK') {
                Swal.fire({
                    icon: 'success',
                    title: '¡Perfecto!',
                    text: 'Repuesto agregado al carrito exitosamente',
                    timer: 2000,
                    showConfirmButton: false
                });

                // Actualizar carrito si la función existe
                if (typeof dame_carrito_ === 'function') {
                    dame_carrito_();
                }
            }
        },
        error: function(xhr) {
            $('#btn_agregar_carrito').prop('disabled', false).text('Agregar al carrito');

            let mensaje = 'Error al agregar el producto al carrito';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                mensaje = xhr.responseJSON.message;
            }

            mostrarError(mensaje);

            // Actualizar carrito si la función existe
            if (typeof dame_carrito_ === 'function') {
                dame_carrito_();
            }
        }
    });
}

// Funciones de utilidad
function formatNumber(number) {
    return new Intl.NumberFormat('es-CL').format(number);
}

function mostrarCargando() {
    $('#mensaje').html('<div class="text-center"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Cargando...</span></div><p class="mt-2">Cargando información del repuesto...</p></div>');
}

function ocultarCargando() {
    $('#mensaje').empty();
}

function mostrarError(mensaje) {
    Swal.fire({
        icon: 'error',
        title: 'Error',
        text: mensaje
    });
}

function mostrarAdvertencia(mensaje) {
    Swal.fire({
        icon: 'warning',
        title: 'Atención',
        text: mensaje,
        timer: 3000
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
        setTimeout(() => window.location.href = "/login", 1000);
    });
}

// Event listeners para cantidad
$(document).on('change', '#cantidad', function() {
    const cantidad = parseInt($(this).val()) || 1;
    const stockMaximo = window.datosRepuesto?.stock || 1;

    if (cantidad > stockMaximo) {
        $(this).val(stockMaximo);
        mostrarAdvertencia('Cantidad ajustada al stock disponible');
    } else if (cantidad < 1) {
        $(this).val(1);
    }

    actualizarPrecioCalculado(parseInt($(this).val()));
});
</script>
@endsection

@section('content')
<p id="mensaje" class="text-center"></p>

<div class="container d-none" id="contenedor">
    <!-- Breadcrumb y navegación -->
    <div class="row mb-4">
        <div class="col-md-9">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('/') }}">Inicio</a></li>
                    <li class="breadcrumb-item"><a href="{{ url('repuestos') }}">Repuestos</a></li>
                    <li class="breadcrumb-item active" aria-current="page">
                        <span id="enlace_repuesto">Repuesto</span>
                    </li>
                </ol>
            </nav>
        </div>
        <div class="col-md-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb justify-content-end">
                    <li class="breadcrumb-item">
                        <a href="#" id="repuesto_anterior" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-chevron-left me-1"></i>Anterior
                        </a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="#" id="repuesto_siguiente" class="btn btn-outline-secondary btn-sm">
                            Siguiente<i class="fas fa-chevron-right ms-1"></i>
                        </a>
                    </li>
                </ol>
            </nav>
        </div>
    </div>

    <!-- Contenido principal del repuesto -->
    <div class="repuesto_tienda" data-aos="fade-up">
        <div class="row">
            <!-- Columna de imágenes -->
            <div class="col-lg-6 col-md-12 mb-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-light">
                        <h5 class="card-title mb-0 text-center" id="titulo_repuesto">
                            <i class="fas fa-cog me-2"></i>Cargando...
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="row g-0">
                            <!-- Carousel principal -->
                            <div class="col-md-10">
                                <div id="carouselExampleControls" class="carousel slide" data-bs-ride="carousel">
                                    <div class="carousel-inner" id="carousel-inner">
                                        <!-- Las imágenes se cargan dinámicamente -->
                                    </div>
                                    <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleControls" data-bs-slide="prev">
                                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                        <span class="visually-hidden">Anterior</span>
                                    </button>
                                    <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleControls" data-bs-slide="next">
                                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                        <span class="visually-hidden">Siguiente</span>
                                    </button>
                                </div>
                            </div>

                            <!-- Miniaturas -->
                            <div class="col-md-2">
                                <div class="card h-100 border-0">
                                    <div class="card-body p-2">
                                        <div class="row" id="preview_photos">
                                            <!-- Las miniaturas se cargan dinámicamente -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Columna de información -->
            <div class="col-lg-6 col-md-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-light">
                        <h5 class="card-title mb-0 text-center">
                            <i class="fas fa-info-circle me-2"></i>Información del Producto
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-borderless">
                                <tbody>
                                    <tr>
                                        <td class="fw-bold text-muted" style="width: 40%;">
                                            <i class="fas fa-barcode me-2"></i>Código Interno
                                        </td>
                                        <td id="codigo_interno_repuesto">-</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold text-muted">
                                            <i class="fas fa-industry me-2"></i>Código de Fábrica
                                        </td>
                                        <td id="cod_fab_repuesto">-</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold text-muted">
                                            <i class="fas fa-tag me-2"></i>Marca
                                        </td>
                                        <td id="marca_repuesto">-</td>
                                    </tr>
                                    <tr class="d-none" id="tr_rectificadores">
                                        <td class="fw-bold text-muted">
                                            <i class="fas fa-cog me-2"></i>Rectificadores
                                        </td>
                                        <td id="rectificadores" class="small">-</td>
                                    </tr>
                                    <tr class="d-none" id="tr_alternadores">
                                        <td class="fw-bold text-muted">
                                            <i class="fas fa-bolt me-2"></i>Alternadores
                                        </td>
                                        <td id="alternadores" class="small">-</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold text-muted">
                                            <i class="fas fa-boxes me-2"></i>Stock
                                        </td>
                                        <td id="stock_actual">-</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold text-muted">
                                            <i class="fas fa-dollar-sign me-2"></i>Precio
                                        </td>
                                        <td id="precio" class="fs-4 fw-bold text-success">-</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold text-muted">
                                            <i class="fas fa-shopping-cart me-2"></i>Cantidad
                                        </td>
                                        <td>
                                            <div class="input-group" style="max-width: 150px;">
                                                <button class="btn btn-outline-secondary" type="button" onclick="disminuir()">
                                                    <i class="fas fa-minus"></i>
                                                </button>
                                                <input type="number" class="form-control text-center"
                                                       name="cantidad" id="cantidad" min="1" value="1">
                                                <button class="btn btn-outline-secondary" type="button" onclick="aumentar()">
                                                    <i class="fas fa-plus"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold text-muted">
                                            <i class="fas fa-globe me-2"></i>País de Origen
                                        </td>
                                        <td id="pais_origen">-</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Botón de agregar al carrito -->
                        <div class="d-grid mt-3">
                            <button class="btn btn-success btn-lg" id="btn_agregar_carrito"
                                    onclick="agregarCarrito()"
                                    data-bs-toggle="offcanvas"
                                    data-bs-target="#offcanvasRight"
                                    aria-controls="offcanvasRight">
                                <i class="fas fa-cart-plus me-2"></i>Agregar al carrito
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Servicios disponibles -->
        <div class="card mt-4 border-0 shadow-sm">
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-md-6 mb-2">
                        <i class="fas fa-truck text-success me-2"></i>
                        <span class="fw-bold">Despacho a domicilio</span>
                        <small class="text-muted d-block">Envío seguro a todo Chile</small>
                    </div>
                    <div class="col-md-6 mb-2">
                        <i class="fas fa-store text-success me-2"></i>
                        <span class="fw-bold">Retiro en tienda</span>
                        <small class="text-muted d-block">Sin costo adicional</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <hr class="my-5">
    @include('fragm.info_sitioweb')
</div>

<!-- Campos ocultos para compatibilidad -->
<input type="hidden" name="stock_repuesto" id="stock_repuesto">
<input type="hidden" name="precio_repuesto" id="precio_repuesto">
@endsection

@push('styles')
<style>
/* Estilos específicos para la vista de detalle del repuesto */
.fotos_repuesto {
    border-radius: 8px;
    transition: transform 0.3s ease;
}

.miniatura-foto {
    transition: all 0.3s ease;
    border: 2px solid transparent;
}

.miniatura-foto:hover {
    border-color: #007bff;
    transform: scale(1.05);
}

.precio-container {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
}

.precio-actual {
    font-size: 1.5rem;
    font-weight: bold;
    color: #28a745;
}

.precio-original {
    font-size: 1rem;
    margin-left: 0;
}

.card {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    border: 1px solid rgba(0, 0, 0, 0.125);
}

.table td {
    padding: 0.75rem 0.5rem;
    border: none;
    vertical-align: middle;
}

.carousel-control-prev,
.carousel-control-next {
    background: rgba(0, 0, 0, 0.1);
    border-radius: 50%;
    width: 40px;
    height: 40px;
    top: 50%;
    transform: translateY(-50%);
}

.carousel-control-prev {
    left: 10px;
}

.carousel-control-next {
    right: 10px;
}

@media (max-width: 768px) {
    .precio-container {
        align-items: center;
        text-align: center;
    }

    .table-responsive {
        font-size: 0.9rem;
    }

    .carousel-inner {
        height: 300px;
    }

    .fotos_repuesto {
        height: 300px !important;
    }
}

@media (max-width: 576px) {
    .card-body {
        padding: 1rem;
    }

    .carousel-inner {
        height: 250px;
    }

    .fotos_repuesto {
        height: 250px !important;
    }
}

/* Animaciones */
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

.repuesto_tienda {
    animation: fadeIn 0.6s ease-out;
}
</style>
@endpush
