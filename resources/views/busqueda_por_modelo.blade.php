@extends('layouts.app')

@section('javascript')
<script>

        //Se recupera la url actual
        var URLactual = window.location;
        //Convertimos la url a string para poder rescatar el id del repuesto
        var strurl = String(URLactual);
        //Separamos el string y dejamos solo el id
        var id_array = strurl.split('/');
        var modelo = id_array[4];
        var familia = id_array[5];

        $(function() {
            $("#slider-range").slider({
                range: true,
                min: 0,
                max: 500000,
                step: 5000,
                values: [0, 200000],
                slide: function(event, ui) {
                    $("#amount").val("$ "+ui.values[ 0 ] + " - $ " + ui.values[ 1 ] + "");
                }
            });

            $( "#amount" ).val( "$" + $( "#slider-range" ).slider( "values", 0 ) +
            " - $" + $( "#slider-range" ).slider( "values", 1 ) );
        });

        window.onload = function(){
            busqueda_principal(modelo);
        }
    
        

        function busqueda_principal(modelo){

            let url = 'https://panchoserver.ddns.net/api/buscar_modelo/'+modelo+'/'+familia;
            
            $.ajax({
                type:'get',
                url: url,
                beforeSend: function(){
                    $('#mensaje').empty();
                    $('#mensaje').append('<img src="{{asset("storage_original/imagenes/loading6.gif")}}" alt="Cargando ...">');
                    $('#contenedor').addClass('d-none');
                },
                success: function(resp){
                    let repuestos = resp[0];
                    let modelo_ = resp[1];
                    let marcas = resp[2];
                    let familia = resp[3];
                    $('#enlace_marca').text(modelo_.marcanombre);
                    $('#enlace_modelo').text(modelo_.modelonombre);
                    $('#datos_modelo').empty();
                    $('#datos_modelo').append(`
                    <div class="modal-content">
                        <div class="modal-header" style="background: #eee;">
                            <h5 class="modal-title">Resumen</h5>
                        </div>
                            <div class="modal-body">
                            <img class="w-75" src="https://panchoserver.ddns.net/storage/`+modelo_.urlfoto+`" alt="Card image cap">
                            
                            </div>
                        <div class="modal-footer">
                        <table class="table">
                            <tbody>
                                <tr>
                                    <td scope="row" class="fw-bold">Marca</td>
                                    <td><a href="/modelos_marca/`+modelo_.marcavehiculos_idmarcavehiculo+`" class="a_titulo_producto"> `+modelo_.marcanombre+` </a></td>
                                </tr>
                                <tr>
                                    <td scope="row" class="fw-bold">Modelo</td>
                                    <td>`+modelo_.modelonombre+` </td>
                                </tr>
                                <tr>
                                    <td scope="row" class="fw-bold">Años</td>
                                    <td>`+modelo_.anios_vehiculo+` </td>
                                </tr>
                                <tr>
                                    <td scope="row" class="fw-bold">Familia </td>
                                    <td>`+familia.nombrefamilia+` </td>
                                </tr>
                            </tbody>
                            </table>
                        </div>
                    </div>
                    
                
                    `);
                      $('#mensaje').empty();
                        $('#contenedor').removeClass('d-none');
                        $('#contenedor').addClass('d-block');
                        
                        if(repuestos.length == 0){
                            
                            $('#container').empty();
                            $('#container').append(' <h1 class="alert-danger text-center">Lo sentimos,no se han encontrado coincidencias </h1><img src="{{asset("storage_original/imagenes/sinresultados.jpg")}}" />');
                        }else{
                            let container = $('#pagination');
             
                        container.pagination({
                            dataSource: repuestos,
                            pageSize: 10,
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
                                    <div class="col-xl-4 col-md-3 mb-3">
                                    <div class="card" style="background-image: url('https://t3.ftcdn.net/jpg/00/98/52/26/360_F_98522695_S9vAeY8a3O4AYFUDr2WVlk4eCWrqf7hx.jpg'); background-size: cover;">
                                      <a href="/repuesto/`+item.id+`" class="a_titulo_producto"><img class="card-img-top repuestos_image_catalogo"  src="https://panchoserver.ddns.net/storage/`+item.urlfoto+`" alt="Card image cap"></a>
                                    <div class="card-body">
                                        <h5 class="card-title">`+item.descripcion+`</h5>
                                        <p class="card-text text-secondary">`+item.marcarepuesto+`</p>
                                        <p class="card-price">$ `+commaSeparateNumber(precio_format)+`</p>
                                        
                                        <p class="fst-italic">Stk. `+item.stock_actual+`</p>
                                        
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
                        }
                        $('#marcas_repuestos').empty();
                        $('#marcas_repuestos').append(`
                        <div class="form-check">
                                        <input class="form-check-input" onchange="busqueda_principal('`+modelo+`');" value='todos' type="radio" name="flexRadioDefault-marcas" id="flexRadioDefault-marcas">
                                        <label class="form-check-label" for="flexRadioDefault-marcas">
                                            TODOS
                                        </label>
                        </div>
                        `);
                      marcas.forEach(marca => {
                        $('#marcas_repuestos').append(`
                        <div class="form-check">
                                        <input class="form-check-input" onchange="handleChange_marca(this);" value='`+marca+`' type="radio" name="flexRadioDefault" id="flexRadioDefault-`+marca+`">
                                        <label class="form-check-label" for="flexRadioDefault-`+marca+`">
                                            `+marca+`
                                        </label>
                        </div>
                        `);
                      });
                },
                error: function(error){
                    console.log(error.responseText);
                }
            })
        }

        function handleChange_marca(event){
        
            let marca_repuestos = event.value;
            filtrar_busqueda_modelo_marca(marca_repuestos)
        }

function agregar_carrito(id_repuesto){
  let cantidad = 1;
  let url = "{{ env('URL_AGREGAR_CARRITO') }}";
  let token = localStorage.getItem('token');

  let data = {
    idrep: id_repuesto,
    cantidad: cantidad
  }

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
}

function filtrar_rango_precio_modelo(){
        let minimo = $('#slider-range').slider("values")[0]; // for slider with single knob or lower value of range
        let maximo = $('#slider-range').slider("values")[1]; // for highest value of range 
        let url = 'https://panchoserver.ddns.net/api/ordenar_busquedamodelo_rango_precio/'+minimo+'/'+maximo+'/'+modelo+'/'+familia;
        

        $.ajax({
            type:'get',
            url: url,
            beforeSend: function(){
                    $('#mensaje').empty();
                    $('#mensaje').append('<img src="{{asset("storage_original/imagenes/loading6.gif")}}" alt="Cargando ...">');
                    $('#contenedor').addClass('d-none');
            },
            success: function(repuestos){
                        console.log(repuestos);
                        $('#mensaje').empty();
                        $('#contenedor').removeClass('d-none');
                        $('#contenedor').addClass('d-block');
                      
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
                                            <div class="card" style="background-image: url('https://t3.ftcdn.net/jpg/00/98/52/26/360_F_98522695_S9vAeY8a3O4AYFUDr2WVlk4eCWrqf7hx.jpg'); background-size: cover;">
                                            <a href="/repuesto/`+item.id+`" class="a_titulo_producto"><img class="card-img-top repuestos_image_catalogo"  src="https://panchoserver.ddns.net/storage/`+item.urlfoto+`" alt="Card image cap"></a>
                                            <div class="card-body">
                                                <h6 class="card-title">`+item.descripcion+`</h6>
                                                <p class="text-secondary text-uppercase">`+item.nombrefamilia+` </p>
                                                <p class="text-secondary">`+item.marcarepuesto+` </p>
                                                <p class="card-text text-center card-price letra_grande">$ `+commaSeparateNumber(precio_format)+`</p>
                                                <p class="fst-italic">Stk. `+item.stock_actual+`</p>
                                                
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

function filtrar_busqueda_modelo_marca(marca){
  let url = 'https://panchoserver.ddns.net/api/ordenar_busqueda_modelo_marca/'+marca+'/'+modelo+'/'+familia;
  
  $.ajax({
      type:'get',
      url: url,
      beforeSend: function(){
          $('#mensaje').empty();
          $('#mensaje').append('<img src="{{asset("storage_original/imagenes/loading6.gif")}}" alt="Cargando ...">');
          $('#contenedor').removeClass('d-block');
          $('#contenedor').addClass('d-none'); 
      },
      success: function(repuestos){
        
              console.log(repuestos);
              
              $('#mensaje').empty();
              $('#contenedor').removeClass('d-none');
              $('#contenedor').addClass('d-block');
            
              let container = $('#pagination');
              container.pagination({
                  dataSource: repuestos,
                  pageSize: 12,
                  
                              afterRender: function() {
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
                                  <div class="card" style="background-image: url('https://t3.ftcdn.net/jpg/00/98/52/26/360_F_98522695_S9vAeY8a3O4AYFUDr2WVlk4eCWrqf7hx.jpg'); background-size: cover;">
                                  <a href="/repuesto/`+item.id+`" class="a_titulo_producto"> <img class="card-img-top repuestos_image_catalogo"  src="https://panchoserver.ddns.net/storage/`+item.urlfoto+`" alt="Card image cap"></a>
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

function ordenar(){
          let value = $('#sort_option').val();
            if(value == 0){
                Swal.fire({
                    icon:'error',
                    text:'Debe seleccionar una opción'
                });
                return false;
            }
            
            let url = 'https://panchoserver.ddns.net/api/ordenar_buscador_modelo/'+familia+'/'+modelo+'/'+value;
            
            $.ajax({
                type:'get',
                url: url,
                beforeSend: function(){
                    $('#mensaje').empty();
                    $('#mensaje').append('<img src="{{asset("storage_original/imagenes/loading6.gif")}}" alt="Cargando ...">');
                    $('#contenedor').addClass('d-none');
                },
                success: function(repuestos){
                  console.log(repuestos);
                
                    $('#contenedor').removeClass('d-none');
                        $('#contenedor').addClass('d-block');
                        $('#mensaje').empty();
                        
                      
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
                                            <div class="col-xl-4 col-md-3 mb-3">
                                            <div class="card" style="background-image: url('https://t3.ftcdn.net/jpg/00/98/52/26/360_F_98522695_S9vAeY8a3O4AYFUDr2WVlk4eCWrqf7hx.jpg'); background-size: cover;">
                                            <a href="/repuesto/`+item.id+`" class="a_titulo_producto"><img class="card-img-top repuestos_image_catalogo"  src="https://panchoserver.ddns.net/storage/`+item.urlfoto+`" alt="Card image cap"></a>
                                            <div class="card-body">
                                                <h6 class="card-title">`+item.descripcion+`</h6>
                                                <p class="text-secondary text-uppercase">`+item.nombrefamilia+` </p>
                                                <p class="text-secondary">`+item.marcarepuesto+` </p>
                                                <p class="card-text text-center card-price letra_grande">$ `+commaSeparateNumber(precio_format)+`</p>
                                                <p class="fst-italic">Stk. `+item.stock_actual+`</p>
                                                
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
</script>
@endsection

@section('content')
@include('fragm.slider_pequeño')
<p id="mensaje" class="text-center"></p>
<section class="container-fluid" id="contenedor">
  <div style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='currentColor'/%3E%3C/svg%3E&#34;);" aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="/">Inicio</a></li>
        <li class="breadcrumb-item active" aria-current="page"><a href="/marcas-repuestos">Marcas</a></li>
        <li class="breadcrumb-item active" aria-current="page"><a href="" id="enlace_marca">Marca</a></li>
        <li class="breadcrumb-item active" aria-current="page"><a href="" id="enlace_modelo">Modelo</a></li>
        <li class="breadcrumb-item active" aria-current="page">Repuestos</li>
      </ol>
  </div>
  <section class="row">
    <div class="col-md-3">
        <div id="datos_modelo" class="mb-2">

        </div>
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
                  <p><input type="button" onclick="filtrar_rango_precio_modelo()" class="btn btn-success btn-sm my-3" name="send" value="Buscar" /></p>
              </div>
          
         </div>
         <div class="modal-content">
          <div class="modal-header" style="background: #eee;">
              <h5 class="modal-title">Marcas</h5>
          </div>
              <div class="modal-body">
                <ul id="marcas_repuestos" class="list-group">
  
                </ul>
              </div>
          
         </div>
      </div>
    <div class="col-md-9">
        <div id="container"></div>
        <div id="pagination"></div>
    </div>
    
</section>
    
</section>
@endsection
