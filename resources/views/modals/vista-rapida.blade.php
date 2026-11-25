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
