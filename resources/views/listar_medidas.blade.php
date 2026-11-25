@extends('layouts.app')
@section('javascript')
<script>
    window.onload = function(){
        console.log('cargando ...');
        damefamilias();
    }

    function damefamilias(){
        let token = localStorage.getItem('token');
        let url = 'https://panchorepuestos.ddns.net/api/familias_medidas';

        $.ajax({
            type:'get',
            url:url,
            headers: {'Authorization': 'Bearer' + token},
            beforeSend: function(){
                Swal.fire({
                    title:'Cargando ...',
                    text:'Espere un momento por favor',
                    allowOutsideClick:false,
                    allowEscapeKey:false,
                    didOpen: () => {
                        Swal.showLoading()
                    }
                });
            },
            success:function(data){
                console.log(data);
                Swal.close();
                        if(data.status == 'Token is Invalid'){
                        Swal.fire({
                                icon:'info',
                                text:'Debe iniciar sesión'
                            });

                            setTimeout(() => {
                            // Simulate a mouse click:
                            window.location.href = "/login";
                            }, 1000);
                        }
                        if(data.status == 'Token is Expired'){
                        Swal.fire({
                                icon:'info',
                                text:'Se acabo su sesión. Debe volver a ingresar.'
                            });

                            setTimeout(() => {
                            // Simulate a mouse click:
                            window.location.href = "/login";
                            }, 1000);
                        }

                        let container_familias = $('#pagination_familias');

                        let familias = data[2].sort();
                    
                        container_familias.pagination({
                            dataSource: familias,
                            pageSize: 35,
                            callback: function (data, pagination) {
                                var dataHtml_ = `<ul class="list-group lista_letra_pequeña">
                                    `;

                                $.each(data, function (index, item) {
                                  
                                    dataHtml_ += `
                                    <div class="form-check">
                                        <input class="form-check-input" onchange="handleChange_familia(this);" value='`+item+`' type="radio" name="flexRadioDefault" id="flexRadioDefault-`+item+`">
                                        <label class="form-check-label text-uppercase" for="flexRadioDefault-`+item+`">
                                            `+item+`
                                        </label>
                                    </div>
                                    `;
                                  
                                });

                                dataHtml_ += '</ul>';

                                $("#container_familias").html(dataHtml_);
                               
                            }
                        });
            },
            error:function(data){
                console.log(data);
            }
        });
    }

    function handleChange_familia(event){
        
        let familia_repuestos = event.value;
        $('#familia').val(familia_repuestos);
        filtrar_familia(familia_repuestos)
    }

    function filtrar_familia(familia){
        let url = 'https://panchorepuestos.ddns.net/api/'+familia+'/ordenar_familia';

        $.ajax({
            type:'get',
            url: url,
            beforeSend: function(){
                $('#mensaje').empty();
                $('#mensaje').append('<img src="{{asset("storage_original/imagenes/car-wheel.gif")}}" alt="Cargando ...">');
                $('#container').removeClass('d-block');
                $('#container').addClass('d-none'); 
                Swal.fire({
                    icon:'info',
                    text:'CARGANDO',
                    showConfirmButton: false
                });
            },
            success: function(repuestos){
                if(repuestos.status == 'Token is Invalid'){
                        Swal.fire({
                                icon:'info',
                                text:'Debe iniciar sesión'
                            });

                            setTimeout(() => {
                            // Simulate a mouse click:
                            window.location.href = "/login";
                            }, 1000);
                        }
                        if(repuestos.status == 'Token is Expired'){
                        Swal.fire({
                                icon:'info',
                                text:'Se acabo su sesión. Debe volver a ingresar.'
                            });

                            setTimeout(() => {
                            // Simulate a mouse click:
                            window.location.href = "/login";
                            }, 1000);
                        }
                // limpiar el campo buscador_medida_familia
                $('#buscador_medida_familia').val('');
                    Swal.close();
                    $('#mensaje').empty();
                    $('#container').removeClass('d-none');
                    $('#container').addClass('d-block');
                
                    let container = $('#pagination');
                    container.pagination({
                        dataSource: repuestos,
                        pageSize: 12,
                        beforeRender: function() {
                                $("html, body").animate({ scrollTop: 0 }, "slow");
                                return false;
                        },
                        callback: function (data, pagination) {
                            var buscador_medida = `
                            <div class="form-group mb-3 border p-2 text-center">
                                <label for="medida" class="form-label fst-italic text-secondary w-100" style="background: #eee;">Medida</label>
                                <input type="search" class="form-control" id="buscador_medida_familia" placeholder="Ingrese alguna medida a buscar para la familia `+familia+`" />
                                <button class="btn btn-warning w-100 btn-sm my-2" onclick="buscar_medida_familia()">Buscar</button>
                            </div>
                            
                            `;
                            if(repuestos.length == 0){
                                    var cantidades = `<p class="text-secondary text-center">Sin Stock para venta online. Puede encontrarlo en nuestro local ubicado en Riquelme #831, Arica, Chile.</p>`;
                                }else if(repuestos.length > 1){
                                    var cantidades = `<p class="text-secondary text-center">Mostrando `+repuestos.length+` repuestos. </p>`;
                                }else{
                                    var cantidades = `<p class="text-secondary text-center">Mostrando `+repuestos.length+` repuesto. </p>`;
                                }
                            var dataHtml = ` 
                                `+buscador_medida+`
                                `+cantidades+`
                                <div class="row w-100">`;

                            $.each(data, function (index, item) {
                                    let porciones = item.medidas.split(",");
                                    var htlm_medidas = '';
                                    porciones.forEach(medida => {
                                        htlm_medidas += `<span class="text-dark" style="font-size: 14px;">`+medida+`</span> <br>`;
                                    });

                                    
                                    let precio_format = Number(item.precio_venta).toFixed(0);
                                    if(item.stock_actual >= 10){
                                       var stock_ = 'Disponible';
                                    }else{
                                       var stock_ = item.stock_actual;
                                    }
                                        dataHtml += `
                                        <div class="col-xl-4 col-md-4 col-sm-6 mb-3">
                                        <div class="card" style="background-image: url('https://t3.ftcdn.net/jpg/00/98/52/26/360_F_98522695_S9vAeY8a3O4AYFUDr2WVlk4eCWrqf7hx.jpg'); background-size: cover; overflow: hidden;">
                                        <a href="/repuesto/`+item.id+`" class="a_titulo_producto"> <img class="card-img-top repuestos_image_catalogo"  src="https://panchorepuestos.ddns.net/storage/`+item.urlfoto+`" alt="Card image cap"></a>
                                        <div class="card-body">
                                            <h6 class="card-title">`+item.descripcion+`</h6>
                                            <p class="text-secondary text-uppercase">`+item.nombrefamilia+` </p>
                                            <p class="text-secondary">`+item.marcarepuesto+` </p>
                                            <p class="text-warning">`+htlm_medidas+` </p>
                                            <p class="card-text text-center card-price letra_grande">$ `+commaSeparateNumber(precio_format)+`</p>
                                            <p class="fst-italic">Stk. `+stock_+`</p>
                                            
                                            <button class="btn btn-warning w-100 mt-3" onclick="vista_rapida(`+item.id+`)" data-bs-toggle="modal" data-bs-target="#modalVistaRapida"><i class="fa-solid fa-eye icon_header"></i>Vista rápida</button>
                                        </div>
                                        </div>
                                        </div>
                                        `;
                                    
                                
                                
                            });

                            dataHtml += '</div>';

                            $("#container").html(dataHtml);
                        }
                    });
            },
            error: function(error){
                $('#mensaje').empty();
                console.log(error.responseText);
            }
        });
    }

    function buscar_medida_familia(){
        let medida = $('#buscador_medida_familia').val();
        let familia = $('#familia').val();
        let url = 'https://panchorepuestos.ddns.net/api/'+familia+'/buscar_medida_familia/'+medida;
        let token = localStorage.getItem('token');
        // validar la medida
        if(medida == ''){
            Swal.fire({
                icon:'info',
                text:'Debe ingresar una medida'
            });
            return false;
        }
        $.ajax({
            type:'get',
            url:url,
            headers: {'Authorization': 'Bearer' + token},
            beforeSend: function(){
                $('#mensaje').empty();
                $('#mensaje').append('<img src="{{asset("storage_original/imagenes/car-wheel.gif")}}" alt="Cargando ...">');
                $('#container').removeClass('d-block');
                $('#container').addClass('d-none'); 
                Swal.fire({
                    icon:'info',
                    text:'CARGANDO',
                    showConfirmButton: false
                });
            },
            success: function(repuestos){
                console.log(repuestos);
                Swal.close();
                    $('#mensaje').empty();
                    $('#container').removeClass('d-none');
                    $('#container').addClass('d-block');
                
                    let container = $('#pagination');
                    container.pagination({
                        dataSource: repuestos,
                        pageSize: 12,
                        beforeRender: function() {
                                $("html, body").animate({ scrollTop: 0 }, "slow");
                                return false;
                        },
                        callback: function (data, pagination) {
                            var buscador_medida = `
                            <div class="form-group mb-3 border p-2 text-center">
                                <label for="medida" class="form-label fst-italic text-secondary w-100" style="background: #eee;">Medida</label>
                                <input type="search" class="form-control" id="buscador_medida_familia" placeholder="Ingrese alguna medida a buscar para la familia `+familia+`" />
                                <button class="btn btn-warning w-100 btn-sm my-2" onclick="buscar_medida_familia()">Buscar</button>
                            </div>
                            
                            `;
                            if(repuestos.length > 1){
                                    var cantidades = `<p class="text-secondary text-center">Mostrando `+repuestos.length+` repuestos. </p>`;
                                }else{
                                    var cantidades = `<p class="text-secondary text-center">Mostrando `+repuestos.length+` repuesto. </p>`;
                                }
                            var dataHtml = ` 
                                `+buscador_medida+`
                                `+cantidades+`
                                <div class="row w-100">`;

                            $.each(data, function (index, item) {
                                    let porciones = item.medidas.split(",");
                                    var htlm_medidas = '';
                                    porciones.forEach(medida => {
                                        htlm_medidas += `<span class="text-dark" style="font-size: 14px;">`+medida+`</span> <br>`;
                                    });

                                    
                                    let precio_format = Number(item.precio_venta).toFixed(0);
                                    if(item.stock_actual >= 10){
                                       var stock_ = 'Disponible';
                                    }else{
                                       var stock_ = item.stock_actual;
                                    }
                                        dataHtml += `
                                        <div class="col-xl-4 col-md-4 col-sm-6 mb-3">
                                        <div class="card" style="background-image: url('https://t3.ftcdn.net/jpg/00/98/52/26/360_F_98522695_S9vAeY8a3O4AYFUDr2WVlk4eCWrqf7hx.jpg'); background-size: cover; overflow: hidden;">
                                        <a href="/repuesto/`+item.id+`" class="a_titulo_producto"> <img class="card-img-top repuestos_image_catalogo"  src="https://panchorepuestos.ddns.net/storage/`+item.urlfoto+`" alt="Card image cap"></a>
                                        <div class="card-body">
                                            <h6 class="card-title">`+item.descripcion+`</h6>
                                            <p class="text-secondary text-uppercase">`+item.nombrefamilia+` </p>
                                            <p class="text-secondary">`+item.marcarepuesto+` </p>
                                            <p class="text-warning">`+htlm_medidas+` </p>
                                            <p class="card-text text-center card-price letra_grande">$ `+commaSeparateNumber(precio_format)+`</p>
                                            <p class="fst-italic">Stk. `+stock_+`</p>
                                            
                                            <button class="btn btn-warning w-100 mt-3" onclick="vista_rapida(`+item.id+`)" data-bs-toggle="modal" data-bs-target="#modalVistaRapida"><i class="fa-solid fa-eye icon_header"></i>Vista rápida</button>
                                        </div>
                                        </div>
                                        </div>
                                        `;
                                    
                                
                                
                            });

                            dataHtml += '</div>';

                            $("#container").html(dataHtml);
                        }
                    });
            },
            error: function(error){
                console.log(error.responseText);
            }
        });
    }
</script>
@endsection

@section('content')
<div class="container" id="contenedor" data-aos="flip-up">
    <div style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='currentColor'/%3E%3C/svg%3E&#34;);" aria-label="breadcrumb">
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="/">Inicio</a></li>
          <li class="breadcrumb-item active" aria-current="page">Listar por Medidas</li>
        </ol>
      </div>
    <section class="row">
        <div class="col-md-12 col-lg-3">
                
               
               <div class="modal-content mb-3">
                <div class="modal-header" style="background: linear-gradient(to right, #d81012, #808080);color: #fff;">
                    <h5 class="modal-title">Familias</h5>
                </div>
                    <div class="modal-body">
                        <p class="fst-italic text-center text-secondary">Seleccione una familia:</p>
                        <div id="container_familias"></div>
                        <div id="pagination_familias"></div>
                    </div>
                
               </div>

               <!--<div class="modal-content ">
                <div class="modal-header" style="background: linear-gradient(to right, #d81012, #808080);color: #fff;">
                    <h5 class="modal-title">Marcas</h5>
                </div>
                    <div class="modal-body">
                        <div id="container_marcas"></div>
                        <div id="pagination_marcas"></div>
                    </div>
                
               </div>-->
        </div>
        <div class="col-md-12 col-lg-9">
            <div id="container">Seleccione alguna familia</div>
            <p id="mensaje" class="text-center"></p>
            <div id="pagination"></div>
        </div>
        
    </section>
</div>
<!-- DATOS DE VITAL IMPORTANCIA -->
<input type="hidden" id="familia">
@endsection