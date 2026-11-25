<div class="container my-5" data-aos="fade-up">
    <div class="twelve">
        <h1>¿Qué necesitas?</h1>
      </div>
    <div class="row bg-light buscador_box">
        
        <div class="col-md-12 p-3">
            
            <p class="fw-bolder">Búsqueda por vehículo</p>
            <div class="row">
                
                <div class="col-md-3">
                    <span class="subtitulo_">Seleccione una marca</span>
                    <select name="marcas_select" id="marcas_select" class="form-control" onchange="dame_modelos_buscador()">
                        <option value="0">Marcas</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <span class="subtitulo_">Seleccione un modelo</span>
                    <select name="modelos_select" id="modelos_select" class="form-control" onchange="dameaniosvehiculo()">
                        <option value="0">Modelo</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <span class="subtitulo_">Seleccione rango de año</span>
                    <select name="anios_vehiculo_select" id="anios_vehiculo_select"  class="form-control">
                        <option value="0">Año</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <br>
                    <button class="btn btn-dark w-100" onclick="busqueda_principal_totem()">Buscar</button>
                </div>
            </div>
        </div>
    </div>
    
</div>
<div class="container mt-4" data-aos="fade-up">
    <div class="row bg-light buscador_box">
        <div class="col-md-6">
            <p class="fw-bolder">Búsqueda por Medidas</p>
            <input type="text" name="buscar_por_medida" id="buscar_por_medida" class="form-control" disabled>
            <a href="/listar-medidas" type="button" class="btn btn-dark w-100 mt-3" >Listar por Medidas </a>
        </div>
        <div class="col-md-6">
            <p class="fw-bolder">Búsqueda por OEM</p>
            <input type="text" name="buscar_por_oem" id="buscar_por_oem" class="form-control">
            <input type="button" class="btn btn-dark w-100 mt-3"  onclick="buscar_por_oem()" value="Buscar">
        </div>
    </div>
</div>

    
