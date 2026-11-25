@extends('layouts.app')
@section('javascript')
<script>

    //Se recupera la url actual
    var URLactual = window.location;
        //Convertimos la url a string para poder rescatar el id 
        var strurl = String(URLactual);
        //Separamos el string y dejamos solo el id
        var id_array = strurl.split('/');
        var oem = id_array[4];

        $(function() {
            $("#slider-range").slider({
                range: true,
                min: 0,
                max: 600000,
                step: 5000,
                values: [0, 50000],
                slide: function(event, ui) {
                    $("#amount").val("$ "+ui.values[ 0 ] + " - $ " + ui.values[ 1 ] + "");
                }
            });

            $( "#amount" ).val( "$" + $( "#slider-range" ).slider( "values", 0 ) +
            " - $" + $( "#slider-range" ).slider( "values", 1 ) );
        });
        
        window.onload= function(){
            buscar_por_oem(oem);
        }
    

    function buscar_por_oem(oem){
        
        let url = 'https://panchorepuestos.ddns.net/api/buscar_por_oem/'+oem;
        let token = localStorage.getItem('token');
            $.ajax({
                type:'get',
                url: url,
                headers: {"Authorization": 'Bearer '+token},
                beforeSend:function(){
                    $('#mensaje').empty();
                    $('#mensaje').append('<img src="{{asset("storage_original/imagenes/car-wheel.gif")}}" alt="Cargando ...">');
                },
                success: function(resp){
                    console.log(resp);
                    $('#mensaje').empty();
                    if(resp.status == 'Token is Invalid' || resp.status == 'Token is Expired'){
                    Swal.fire({
                            icon:'info',
                            text:'Debe iniciar sesión'
                        });

                        setTimeout(() => {
                        // Simulate a mouse click:
                        window.location.href = "/login";
                        }, 1000);
                    }
                  let repuestos = resp[0];
                  let marcas_repuestos = resp[1];
                        if(repuestos.length == 0){
                            $('#container').empty();
                            $('#container').addClass('text-center');
                            $('#container').html(' <span class="badge rounded-pill bg-danger text-center " style="font-size: 25px;">Lo sentimos, no hemos encontrado coincidencias.</span><img src="{{asset("storage_original/imagenes/sinresultados.jpg")}}" class="image_transbank" />');
                        }else{
                            let container = $('#pagination');
                            container.pagination({
                                dataSource: repuestos,
                                pageSize: 10,
                                
                                beforeRender: function() {
                                    $("html, body").animate({ scrollTop: 0 }, "slow");
                                    return false;
                                },
                                callback: function (data, pagination) {
                                    if(repuestos.length > 1){
                                    var cantidades = `<p class="text-secondary text-center">Mostrando `+repuestos.length+` repuestos. </p>`;
                                }else{
                                    var cantidades = `<p class="text-secondary text-center">Mostrando `+repuestos.length+` repuesto. </p>`;
                                }
                                    var dataHtml = ` 
                                    <div class="modal-content mb-3">
                                    <div class="modal-header" style="background: #eee;">
                                        <h5 class="modal-title">Ordenar por:</h5>
                                    </div>
                                        <div class="modal-body">
                                            <select class="form-control" name="sort_option" id="sort_option" onchange="ordenar()">
                                                <option value="0">Seleccione opción </option>
                                                <option value="1">Precio, Menor a Mayor</option>
                                                <option value="2">Precio, Mayor a Menor</option>
                                                <option value="3">Ordenados alfabéticamente A - Z</option>
                                                <option value="4">Ordenados alfabéticamente Z - A</option>
                                            </select>
                                        </div>
                                    
                                    </div>
                                    `+cantidades+`
                                    <div class="row w-100">`;
                        
                                            $.each(data, function (index, item) {
                                            let precio_format = Number(item.precio_venta).toFixed(0);
                                            if(item.stock_actual >= 10){
                                                var stock_ = 'Disponible';
                                            }else{
                                                var stock_ = item.stock_actual;
                                            }
                                            dataHtml += `
                                            <div class="col-xl-4 col-md-3 mb-3">
                                            <div class="card" style="background-image: url('https://t3.ftcdn.net/jpg/00/98/52/26/360_F_98522695_S9vAeY8a3O4AYFUDr2WVlk4eCWrqf7hx.jpg'); background-size: cover; overflow: hidden;">
                                            <a href="/repuesto/`+item.id+`" class="a_titulo_producto"><img class="card-img-top repuestos_image_catalogo"  src="https://panchorepuestos.ddns.net/storage/`+item.urlfoto+`" alt="Card image cap"></a>
                                            <div class="card-body">
                                                <h5 class="card-title">`+item.descripcion+`</h5>
                                            <p class="text-secondary">`+item.marcarepuesto+` </p>
                                                    <p class="card-text text-center card-price letra_grande">$ `+commaSeparateNumber(precio_format)+`</p>
                                                    <p class="fst-italic">Stk. `+stock_+`</p>
                                                    
                                                    <button class="btn btn-warning w-100 mt-3" onclick="vista_rapida(`+item.id+`)" data-bs-toggle="modal" data-bs-target="#modalVistaRapida"><i class="fa-solid fa-eye icon_header"></i>Vista rápida </button>
                                            </div>
                                            </div>
                                        </div>
                                        `;
                        
                                    });
                            
                                    

                                    dataHtml += '</div>';

                                    $("#container").html(dataHtml);
                                }
                            });
                        }
                        $('#marcas_repuestos').empty();
                        $('#marcas_repuestos').append(`
                        <div class="form-check">
                                        <input class="form-check-input" onchange="buscar_por_oem('`+oem+`');" value='todos' type="radio" name="flexRadioDefault-marcas" id="flexRadioDefault-marcas">
                                        <label class="form-check-label" for="flexRadioDefault-marcas">
                                            TODOS
                                        </label>
                        </div>`);
                      marcas_repuestos.forEach(marca => {
                        $('#marcas_repuestos').append(`<div class="form-check">
                                        <input class="form-check-input" onchange="handleChange_marca(this);" value='`+marca+`' type="radio" name="flexRadioDefault" id="flexRadioDefault-`+marca+`">
                                        <label class="form-check-label" for="flexRadioDefault-`+marca+`">
                                            `+marca+`
                                        </label>
                        </div>`);
                      });
                },
                error: function(error){
                    console.log(error.responseText);
                }
            });
    }

    function handleChange_marca(event){
        
        let marca_repuestos = event.value;
        filtrar_marca(marca_repuestos)
    }

function agregar_carrito(id_repuesto){
  let cantidad = 1;
  let url = "{{ env('URL_AGREGAR_CARRITO') }}";
  let token = localStorage.getItem('token');
  let data = {
    idrep: id_repuesto,
    cantidad: cantidad
  }

  if(token){
    $.ajaxSetup({
                headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
      
  $.ajax({
    type:'post',
    url: url,
    data: data,

    // Fetch the stored token from localStorage and set in the header
    headers: {"Authorization": 'Bearer '+token},
    success: function(resp){
      
      if(resp.message == 'OK'){
	      Swal.fire({
		    icon:'success',
		  title: 'Repuesto agregado al carrito',
		  showDenyButton: true,
		  confirmButtonText: 'Ir al carrito',
		  denyButtonText: `Seguir comprando`,
		}).then((result) => {
		  /* Read more about isConfirmed, isDenied below */
		  if (result.isConfirmed) {
		    // Simulate a mouse click:
		    window.location.href = "/carrito";
		  } else if (result.isDenied) {
		    
		  }
		});
      }
    },
    error: function(error){
    console.log(error);
      Swal.fire({
                    title: "Error",
                    text: 'Repuesto ya existe en el carrito',
                    icon: "error"
                });
    }
  });
  }else{
    Swal.fire({
                    title: "Error",
                    text: 'Debe registrarse para poder comprar.',
                    icon: "error"
                });
  }

  
}

function ordenar(){
  let value = $('#sort_option').val();
            if(value == 0){
                Swal.fire({
                    icon:'error',
                    text:'Debe seleccionar una opción'
                });
                return false;
            }
            
            let url = 'https://panchorepuestos.ddns.net/api/'+value+'/ordenar_oem/'+oem;

            $.ajax({
                type:'get',
                url: url,
                beforeSend: function(){
                    $('#mensaje').empty();
                    $('#mensaje').append('<img src="{{asset("storage_original/imagenes/car-wheel.gif")}}" alt="Cargando ...">');
                    $('#container').removeClass('d-block');
                    $('#container').addClass('d-none'); 
                },
                success: function(repuestos){
                  if(repuestos.length == 0){
                    $('#container').html(' <h1 class="alert-danger text-center">Lo sentimos,no se han encontrado coincidencias </h1><img src="{{asset("storage_original/imagenes/sinresultados.jpg")}}" class="image_transbank" />');
                  }else{
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
                                if(repuestos.length > 1){
                                var cantidades = `<p class="text-secondary text-center">Mostrando `+repuestos.length+` repuestos. </p>`;
                              }else{
                                var cantidades = `<p class="text-secondary text-center">Mostrando `+repuestos.length+` repuesto. </p>`;
                              }
                                var dataHtml = ` 
                                <div class="modal-content mb-3">
                                <div class="modal-header" style="background: #eee;">
                                    <h5 class="modal-title">Ordenar por:</h5>
                                </div>
                                    <div class="modal-body">
                                        <select class="form-control" name="sort_option" id="sort_option" onchange="ordenar()">
                                            <option value="0">Seleccione opción </option>
                                            <option value="1">Precio, Menor a Mayor</option>
                                            <option value="2">Precio, Mayor a Menor</option>
                                            <option value="3">Ordenados alfabéticamente A - Z</option>
                                            <option value="4">Ordenados alfabéticamente Z - A</option>
                                        </select>
                                    </div>
                                
                                </div>
                               `+cantidades+`
                                <div class="row w-100">`;

                                $.each(data, function (index, item) {
                                    
                                        let precio_format = Number(item.precio_venta).toFixed(0);
                                            dataHtml += `
                                            <div class="col-xl-4 col-md-4 col-sm-6 mb-3">
                                            <div class="card" style="background-image: url('https://t3.ftcdn.net/jpg/00/98/52/26/360_F_98522695_S9vAeY8a3O4AYFUDr2WVlk4eCWrqf7hx.jpg'); background-size: cover; overflow: hidden;">
                                            <a href="/repuesto/`+item.id+`" class="a_titulo_producto"> <img class="card-img-top repuestos_image_catalogo"  src="https://panchorepuestos.ddns.net/storage/`+item.urlfoto+`" alt="Card image cap"></a>
                                            <div class="card-body">
                                                <h6 class="card-title">`+item.descripcion+`</h6>
                                                <p class="text-secondary">`+item.marcarepuesto+` </p>
                                                <p class="card-text text-center card-price letra_grande">$ `+commaSeparateNumber(precio_format)+`</p>
                                                <p class="fst-italic">Stk. `+item.stock_actual+`</p>
                                                
                                                <button class="btn btn-warning w-100 mt-3" onclick="vista_rapida(`+item.id+`)" data-bs-toggle="modal" data-bs-target="#modalVistaRapida"><i class="fa-solid fa-eye icon_header"></i>Vista rápida </button>
                                            </div>
                                            </div>
                                            </div>
                                            `;
                                           
                                    
                                    
                                });

                                dataHtml += '</div>';

                                $("#container").html(dataHtml);
                            }
                        });
                  }
                        
                        
                },
                error: function(error){
                    $('#mensaje').empty();
                    console.log(error.responseText);
                }
            });
}

function filtrar_rango_precio_oem(){
        let minimo = $('#slider-range').slider("values")[0]; // for slider with single knob or lower value of range
        let maximo = $('#slider-range').slider("values")[1]; // for highest value of range 
        let url = 'https://panchorepuestos.ddns.net/api/ordenar_rango_precio_oem/'+minimo+'/'+maximo+'/'+oem;

        $.ajax({
            type:'get',
            url: url,
            beforeSend: function(){
                    $('#mensaje').empty();
                    $('#mensaje').append('<img src="{{asset("storage_original/imagenes/car-wheel.gif")}}" alt="Cargando ...">');
                    $('#container').addClass('d-none');
            },
            success: function(repuestos){
                        console.log(repuestos);
                        
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
                                if(repuestos.length > 1){
                                var cantidades = `<p class="text-secondary text-center">Mostrando `+repuestos.length+` repuestos. </p>`;
                              }else{
                                var cantidades = `<p class="text-secondary text-center">Mostrando `+repuestos.length+` repuesto. </p>`;
                              }
                                var dataHtml = ` 
                                <div class="modal-content mb-3">
                                <div class="modal-header" style="background: #eee;">
                                    <h5 class="modal-title">Ordenar por:</h5>
                                </div>
                                    <div class="modal-body">
                                        <select class="form-control" name="sort_option" id="sort_option" onchange="ordenar()">
                                            <option value="0">Seleccione opción </option>
                                            <option value="1">Precio, Menor a Mayor</option>
                                            <option value="2">Precio, Mayor a Menor</option>
                                            <option value="3">Ordenados alfabéticamente A - Z</option>
                                            <option value="4">Ordenados alfabéticamente Z - A</option>
                                        </select>
                                    </div>
                                
                                </div>
                                `+cantidades+`
                                <div class="row w-100">`;

                                $.each(data, function (index, item) {
                                    
                                        let precio_format = Number(item.precio_venta).toFixed(0);
                                            dataHtml += `
                                            <div class="col-xl-4 col-md-4 col-sm-6 mb-3">
                                            <div class="card" style="background-image: url('https://t3.ftcdn.net/jpg/00/98/52/26/360_F_98522695_S9vAeY8a3O4AYFUDr2WVlk4eCWrqf7hx.jpg'); background-size: cover; overflow: hidden;">
                                            <a href="/repuesto/`+item.id+`" class="a_titulo_producto"><img class="card-img-top repuestos_image_catalogo"  src="https://panchorepuestos.ddns.net/storage/`+item.urlfoto+`" alt="Card image cap"></a>
                                            <div class="card-body">
                                                <h6 class="card-title">`+item.descripcion+`</h6>
                                                <p class="text-secondary">`+item.marcarepuesto+` </p>
                                                <p class="card-text text-center card-price letra_grande">$ `+commaSeparateNumber(precio_format)+`</p>
                                                <p class="fst-italic">Stk. `+item.stock_actual+`</p>
                                                
                                                <button class="btn btn-warning w-100 mt-3" onclick="vista_rapida(`+item.id+`)" data-bs-toggle="modal" data-bs-target="#modalVistaRapida"><i class="fa-solid fa-eye icon_header"></i>Vista rápida </button>
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

function filtrar_marca(marca){
        let url = 'https://panchorepuestos.ddns.net/api/ordenar_marca_oem/'+marca+'/'+oem;

        $.ajax({
            type:'get',
            url: url,
            beforeSend: function(){
                $('#mensaje').empty();
                $('#mensaje').append('<img src="{{asset("storage_original/imagenes/car-wheel.gif")}}" alt="Cargando ...">');
                $('#container').removeClass('d-block');
                $('#container').addClass('d-none'); 
            },
            success: function(repuestos){
            
                    
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
                            if(repuestos.length > 1){
                                var cantidades = `<p class="text-secondary text-center">Mostrando `+repuestos.length+` repuestos. </p>`;
                              }else{
                                var cantidades = `<p class="text-secondary text-center">Mostrando `+repuestos.length+` repuesto. </p>`;
                              }
                            var dataHtml = ` 
                                <div class="modal-content mb-3">
                                <div class="modal-header" style="background: #eee;">
                                    <h5 class="modal-title">Ordenar por:</h5>
                                </div>
                                    <div class="modal-body">
                                        <select class="form-control" name="sort_option" id="sort_option" onchange="ordenar()">
                                            <option value="0">Seleccione opción </option>
                                            <option value="1">Precio, Menor a Mayor</option>
                                            <option value="2">Precio, Mayor a Menor</option>
                                            <option value="3">Ordenados alfabéticamente A - Z</option>
                                            <option value="4">Ordenados alfabéticamente Z - A</option>
                                        </select>
                                    </div>
                                
                                </div>
                                `+cantidades+`
                                <div class="row w-100">`;

                            $.each(data, function (index, item) {
                                
                                    let precio_format = Number(item.precio_venta).toFixed(0);
                                        dataHtml += `
                                        <div class="col-xl-4 col-md-4 col-sm-6 mb-3">
                                        <div class="card" style="background-image: url('https://t3.ftcdn.net/jpg/00/98/52/26/360_F_98522695_S9vAeY8a3O4AYFUDr2WVlk4eCWrqf7hx.jpg'); background-size: cover; overflow: hidden;">
                                        <a href="/repuesto/`+item.id+`" class="a_titulo_producto"> <img class="card-img-top repuestos_image_catalogo"  src="https://panchorepuestos.ddns.net/storage/`+item.urlfoto+`" alt="Card image cap"></a>
                                        <div class="card-body">
                                            <h6 class="card-title">`+item.descripcion+`</h6>
                                            <p class="text-secondary">`+item.marcarepuesto+` </p>
                                            <p class="card-text text-center card-price letra_grande">$ `+commaSeparateNumber(precio_format)+`</p>
                                            <p class="fst-italic">Stk. `+item.stock_actual+`</p>
                                            
                                            <button class="btn btn-warning w-100 mt-3" onclick="vista_rapida(`+item.id+`)" data-bs-toggle="modal" data-bs-target="#modalVistaRapida"><i class="fa-solid fa-eye icon_header"></i>Vista rápida </button>
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
</script>
@endsection
@section('content')
@include('fragm.slider_pequeño_busqueda')
<div class="container" id="contenedor">
  
  <div style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='currentColor'/%3E%3C/svg%3E&#34;);" aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="/">Inicio</a></li>
      <li class="breadcrumb-item active" aria-current="page"><a href="/repuestos">Productos</a></li>
      <li class="breadcrumb-item active" aria-current="page">Busqueda por OEM</li>
    </ol>
  </div>
  <section class="row">
    <div class="col-md-3">
        <div class="modal-content mb-3">
            <div class="modal-header" style="background: #eee;">
                <h5 class="modal-title">Precios</h5>
            </div>
                <div class="modal-body">
                    <p>
                        <label for="amount">Rango de precios:</label>
                        <input type="text" id="amount" name="range" style="border: 0; color: #cd3a43; font-weight: bold;" />
                    </p>
                    <div id="slider-range" style="width:100%;"></div>
                    <p><input type="button" onclick="filtrar_rango_precio_oem()" class="btn btn-light btn-sm my-3 w-100" name="send" value="Buscar" /></p>
                </div>
            
           </div>
           <div class="modal-content">
            <div class="modal-header" style="background: #eee;">
                <h5 class="modal-title">Marcas</h5>
            </div>
                <div class="modal-body">
                    <ul id="marcas_repuestos" class="list-group lista_letra_pequeña">

                    </ul>
                </div>
            
           </div>
          
    </div>
    <div class="col-md-9">
        <div id="container"></div>
        <p id="mensaje" class="text-center"></p>
        <div id="pagination"></div>
    </div>
    
</section>
</div>
@endsection
