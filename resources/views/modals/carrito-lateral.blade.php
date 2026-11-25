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
