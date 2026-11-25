<div class="container-fluid">
    <div class="twelve">
        <h1>¿Qué necesitas?</h1>
      </div>
    <div class="row bg-light">
        
        <div class="col-md-12 p-3">
            <p>Busqueda por vehículo</p>
            <div class="row">
                
                <div class="col-md-12">
                    <select name="marcas_select" id="marcas_select" class="form-control" onchange="dame_modelos()" >
                        <option value="0">Marca</option>
                    </select>
                </div>
                <div class="col-md-12">
                    <select name="modelos_select" id="modelos_select" class="form-control" onchange="dameaniosvehiculo()" >
                        <option value="0">Modelo</option>
                    </select>
                </div>
                <div class="col-md-12">
                    <select name="anios_vehiculo_select" id="anios_vehiculo_select"  class="form-control" > 
                        <option value="0">Año</option>
                    </select>
                </div>
                <div class="col-md-12">
                    <button class="btn btn-dark w-100" onclick="busqueda_principal()">Buscar</button>
                </div>
            </div>
        </div>
    </div>
    
</div>
<div class="container-fluid mt-4">
    <div class="row bg-light">
        <div class="col-md-6">
            <p>Descripción</p>
            <input type="text" name="tag_principal" id="tag_principal" class="form-control">
            <input type="button" class="btn btn-dark w-100 mt-3" value="Buscar" onclick="buscar_main()">
        </div>
        
        <div class="col-md-6">
            <p>OEM</p>
            <input type="text" name="buscar_por_oem" id="buscar_por_oem" class="form-control">
            <input type="button" class="btn btn-dark w-100 mt-3"  onclick="buscar_por_oem()" value="Buscar">
        </div>
    </div>
</div>

    
