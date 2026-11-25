@extends('layouts.app')

@section('javascript')
<script>
        //Se recupera la url actual
        var URLactual = window.location;
        //Convertimos la url a string para poder rescatar el id del repuesto
        var strurl = String(URLactual);
        //Separamos el string y dejamos solo el id
        var id_array = strurl.split('/');
        var idmodelo = id_array[4];

        $(function() {
          $("#slider-range").slider({
              range: true,
              min: 0,
              max: 250000,
              step: 5000,
              values: [0, 70000],
              slide: function(event, ui) {
                  $("#amount").val("$ "+ui.values[ 0 ] + " - $ " + ui.values[ 1 ] + "");
              }
          });
          $( "#amount" ).val( "$" + $( "#slider-range" ).slider( "values", 0 ) +
            " - $" + $( "#slider-range" ).slider( "values", 1 ) );
        });

    window.onload = function(){
        
        buscar_por_modelo(idmodelo);
    }
    function buscar_por_modelo(idmodelo)
    {

      var url_buscar='https://panchorepuestos.ddns.net/api/damemodelo_por_marca/'+idmodelo;
      let token = localStorage.getItem('token');
      $.ajax({
       type:'GET',
       beforeSend: function () {
        $('#container').removeClass('d-block');
       $('#container').addClass('d-none');
       $('#mensaje').empty();
        $('#mensaje').append('<img src="{{asset("storage_original/imagenes/car-wheel.gif")}}" alt="Cargando ...">');
        Swal.fire({
                            icon:'info',
                            text:'CARGANDO',
                            showConfirmButton: false,
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                        });
      },

      url:url_buscar,
      headers: {"Authorization": 'Bearer '+token},
      success:function(repuestos){
        console.log(repuestos);
         if(repuestos.status == 'Token is Invalid' || repuestos.status == 'Token is Expired'){
          Swal.fire({
            	icon:'info',
            	text:'Debe iniciar sesión'
            });

            setTimeout(() => {
              // Simulate a mouse click:
              window.location.href = "/login";
            }, 1000);
         }
         Swal.close();
        let modelo = repuestos[1];
        let marcas_repuestos = repuestos[2];
        let familias_repuestos = repuestos[3];
        
        $('#enlace_marca').attr('href','/modelos_marca/'+modelo.marcavehiculos_idmarcavehiculo);
        $('#enlace_marca').text(modelo.marcanombre);
        $('#enlace_modelo').text(modelo.modelonombre);
        $('#titulo_modelo').text(modelo.marcanombre);
        $('#datos_modelo').empty();
        $('#datos_modelo').append(`
        <div class="modal-content">
            <div class="modal-header" style="background: linear-gradient(to right, #d81012, #808080); color: #fff;">
                <h5 class="modal-title">Resumen</h5>
            </div>
                <div class="modal-body">
                  <img class="w-75" src="https://panchorepuestos.ddns.net/storage/`+modelo.urlfoto+`" alt="Card image cap">
                  
                </div>
            <div class="modal-footer">
              <table class="table">
                  <tbody>
                    <tr>
                      <td scope="row" class="fw-bold">Marca</td>
                      <td><a href="/modelos_marca/`+modelo.marcavehiculos_idmarcavehiculo+`" class="a_titulo_producto"> `+modelo.marcanombre+` </a></td>
                    </tr>
                    <tr>
                      <td scope="row" class="fw-bold">Modelo</td>
                      <td>`+modelo.modelonombre+` </td>
                    </tr>
                    <tr>
                      <td scope="row" class="fw-bold">Años</td>
                      <td>`+modelo.anios_vehiculo+` </td>
                    </tr>
                    </tr>
                  </tbody>
                </table>
            </div>
           </div>
        
       
        `);
        $('#container').removeClass('d-none');
        $('#container').addClass('d-block');
                        $('#mensaje').empty();
                      if(repuestos[0].length == 0){
                        $('#container').empty();
                        $('#container').addClass('text-center');
                     	$('#container').html(' <p>Lo sentimos, no hemos encontrado coincidencias.</p><img src="{{asset("storage_original/imagenes/sinresultados.jpg")}}" class="image_transbank" />');
                      }else{
                        let container = $('#pagination');
                        container.pagination({
                            dataSource: repuestos[0],
                            pageSize: 12,
                            
                            afterRender: function() {
                                $("html, body").animate({ scrollTop: 0 }, "slow");
                                return false;
                            },
                            callback: function (data, pagination) {
                              if(repuestos[0].length > 1){
                                var cantidades = `<p class="text-secondary text-center">Mostrando `+repuestos[0].length+` repuestos. </p>`;
                              }else{
                                var cantidades = `<p class="text-secondary text-center">Mostrando `+repuestos[0].length+` repuesto. </p>`;
                              }
                              var dataHtml = ` 
                                <div class="modal-content mb-3">
                                <div class="modal-header" style="background: linear-gradient(to right, #d81012, #808080); color: #fff;">
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
                                    <div class="col-xl-4 col-md-4 col-sm-6 mb-3">
                                    <div class="card" style="background-image: url('https://t3.ftcdn.net/jpg/00/98/52/26/360_F_98522695_S9vAeY8a3O4AYFUDr2WVlk4eCWrqf7hx.jpg'); background-size: cover; overflow: hidden;">
                                    <a href="/repuesto/`+item.id+`" class="a_titulo_producto"> <img class="card-img-top repuestos_image_catalogo"  src="https://panchorepuestos.ddns.net/storage/`+item.urlfoto+`" alt="Card image cap"></a>
                                    <div class="card-body">
                                        <h6 class="card-title">`+item.descripcion+`</h6>
                                        <p class="text-secondary">`+item.nombrefamilia+` </p>
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
                      marcas_repuestos.forEach(marca => {
                        $('#marcas_repuestos').append(`
                        <div class="form-check">
                                        <input class="form-check-input" onchange="handleChange_marca(this);" value='`+marca+`' type="radio" name="flexRadioDefault" id="flexRadioDefault-`+marca+`">
                                        <label class="form-check-label" for="flexRadioDefault-`+marca+`">
                                            `+marca+`
                                        </label>
                        </div>`);
                      });

                      $('#familias_repuestos').empty();
                      $('#familias_repuestos').append(`<div class="form-check">
                                        <input class="form-check-input" onchange="buscar_por_modelo(`+idmodelo+`)" value='todos' type="radio" name="flexRadioDefault-todos" id="flexRadioDefault-todos">
                                        <label class="form-check-label text-uppercase" for="flexRadioDefault-todos">
                                            Todos
                                        </label>
                                    </div>`);
                      familias_repuestos.forEach(familia => {
                        $('#familias_repuestos').append(`
                        <div class="form-check">
                                        <input class="form-check-input" onchange="handleChange_familia(this);" value='`+familia+`' type="radio" name="flexRadioDefault" id="flexRadioDefault-`+familia+`">
                                        <label class="form-check-label text-uppercase" for="flexRadioDefault-`+familia+`">
                                            `+familia+`
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
        filtrar_modelo_marca(marca_repuestos)
    }

    function handleChange_familia(event){
      let familia_repuestos = event.value;
      $('#familia').val(familia_repuestos);
      filtrar_modelo_familia(familia_repuestos);
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

            if ($('input[name=flexRadioDefault]:checked').length > 0) {
                var familia = $('#familia').val();
                var url = 'https://panchorepuestos.ddns.net/api/'+value+'/ordenar_modelo_con_familia/'+idmodelo+'/'+familia;
                console.log('Si hay familia');
            }else{
                var url = 'https://panchorepuestos.ddns.net/api/'+value+'/ordenar_modelo/'+idmodelo;
                console.log('NO hay familia');
            }
            
            $.ajax({
                type:'get',
                url: url,
                beforeSend: function(){
                    $('#mensaje').empty();
                    $('#mensaje').append('<img src="{{asset("storage_original/imagenes/car-wheel.gif")}}" alt="Cargando ...">');
                    $('#container').removeClass('d-block');
                    $('#container').addClass('d-none'); 
                    console.log('vamos a ordenar');
                },
                success: function(repuestos){
                  
                        
                        $('#mensaje').empty();
                        $('#container').removeClass('d-none');
                        $('#container').addClass('d-block');
                      
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
                                <div class="modal-header" style="background: linear-gradient(to right, #d81012, #808080); color: #fff;">
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
                                            <div class="col-xl-4 col-md-4 col-sm-6 mb-3">
                                            <div class="card" style="background-image: url('https://t3.ftcdn.net/jpg/00/98/52/26/360_F_98522695_S9vAeY8a3O4AYFUDr2WVlk4eCWrqf7hx.jpg'); background-size: cover; overflow: hidden;">
                                            <a href="/repuesto/`+item.id+`" class="a_titulo_producto"> <img class="card-img-top repuestos_image_catalogo"  src="https://panchorepuestos.ddns.net/storage/`+item.urlfoto+`" alt="Card image cap"></a>
                                            <div class="card-body">
                                                <h6 class="card-title">`+item.descripcion+`</h6>
                                                <p class="text-secondary">`+item.nombrefamilia+` </p>
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
                },
                error: function(error){
                    $('#mensaje').empty();
                    console.log(error.responseText);
                }
            });
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

    headers: {"Authorization": 'Bearer '+token},
    success: function(resp){
      console.log(resp);
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
      }else{
        Swal.fire({
          icon:'error',
          text:resp.message
        });
        return false;
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
          icon:'error',
          text:'Debe registrarse para poder comprar.'
        });
        return false;
  }

 
}

function filtrar_modelo_familia(familia){
  let url = 'https://panchorepuestos.ddns.net/api/'+familia+'/ordenar_modelo_familia/'+idmodelo;

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
        console.log(repuestos);
            
            $('#mensaje').empty();
            $('#container').removeClass('d-none');
            $('#container').addClass('d-block');
          
            let container = $('#pagination');
            container.pagination({
                dataSource: repuestos,
                pageSize: 12,
                
                            afterRender: function() {
                                $("html, body").animate({ scrollTop: 200 }, "slow");
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
                                <div class="modal-header" style="background: linear-gradient(to right, #d81012, #808080); color: #fff;">
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
                                <div class="col-xl-4 col-md-4 col-sm-6 mb-3">
                                <div class="card" style="background-image: url('https://t3.ftcdn.net/jpg/00/98/52/26/360_F_98522695_S9vAeY8a3O4AYFUDr2WVlk4eCWrqf7hx.jpg'); background-size: cover; overflow: hidden;">
                                <a href="/repuesto/`+item.id+`" class="a_titulo_producto"> <img class="card-img-top repuestos_image_catalogo"  src="https://panchorepuestos.ddns.net/storage/`+item.urlfoto+`" alt="Card image cap"></a>
                                <div class="card-body">
                                    <h6 class="card-title">`+item.descripcion+`</h6>
                                    <p class="text-secondary">`+item.nombrefamilia+` </p>
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
    },
    error: function(error){
        $('#mensaje').empty();
        console.log(error.responseText);
    }
});
}

function filtrar_modelo_marca(marca){
  let url = 'https://panchorepuestos.ddns.net/api/'+marca+'/ordenar_modelo_marca/'+idmodelo;

$.ajax({
    type:'get',
    url: url,
    beforeSend: function(){
        $('#mensaje').empty();
        $('#mensaje').append('<img src="{{asset("storage_original/imagenes/car-wheel.gif")}}" alt="Cargando ...">');
        $('#contenedor').removeClass('d-block');
        $('#contenedor').addClass('d-none'); 
    },
    success: function(repuestos){
      
            
            $('#mensaje').empty();
            $('#contenedor').removeClass('d-none');
            $('#contenedor').addClass('d-block');
          
            let container = $('#pagination');
            container.pagination({
                dataSource: repuestos,
                pageSize: 12,
                
                            afterRender: function() {
                                $("html, body").animate({ scrollTop: 200 }, "slow");
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
                                <div class="modal-header" style="background: linear-gradient(to right, #d81012, #808080); color: #fff;">
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
                                <div class="col-xl-4 col-md-4 col-sm-6 mb-3">
                                <div class="card" style="background-image: url('https://t3.ftcdn.net/jpg/00/98/52/26/360_F_98522695_S9vAeY8a3O4AYFUDr2WVlk4eCWrqf7hx.jpg'); background-size: cover; overflow: hidden;">
                                <a href="/repuesto/`+item.id+`" class="a_titulo_producto"> <img class="card-img-top repuestos_image_catalogo"  src="https://panchorepuestos.ddns.net/storage/`+item.urlfoto+`" alt="Card image cap"></a>
                                <div class="card-body">
                                    <h6 class="card-title">`+item.descripcion+`</h6>
                                    <p class="text-secondary">`+item.nombrefamilia+` </p>
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
    },
    error: function(error){
        $('#mensaje').empty();
        console.log(error.responseText);
    }
});
}

function filtrar_rango_precio_modelo(){
      let minimo = $('#slider-range').slider("values")[0]; // for slider with single knob or lower value of range
        let maximo = $('#slider-range').slider("values")[1]; // for highest value of range 
        if ($('input[name=flexRadioDefault]:checked').length > 0) {
                var familia = $('#familia').val();
                var url = 'https://panchorepuestos.ddns.net/api/ordenar_modelo_rango_precio_con_familia/'+minimo+'/'+maximo+'/'+idmodelo+'/'+familia;
                console.log('Si hay familia '+familia);
            }else{
                var url = 'https://panchorepuestos.ddns.net/api/ordenar_modelo_rango_precio/'+minimo+'/'+maximo+'/'+idmodelo;
                console.log('NO hay familia');
            }
     

        $.ajax({
            type:'get',
            url: url,
            beforeSend: function(){
                    $('#mensaje').empty();
                    $('#mensaje').append('<img src="{{asset("storage_original/imagenes/car-wheel.gif")}}" alt="Cargando ...">');
                    $('#contenedor').addClass('d-none');
            },
            success: function(repuestos){
                        console.log(repuestos);
                        
                        $('#mensaje').empty();
                        $('#contenedor').removeClass('d-none');
                        $('#contenedor').addClass('d-block');
                        if(repuestos.length == 0){
                        $('#container').empty();
                     	  $('#container').html(' <p>Lo sentimos,no se han encontrado coincidencias </p><img src="{{asset("storage_original/imagenes/sinresultados.jpg")}}" class="image_transbank" />');
                      }else{
                        let container = $('#pagination');
                        container.pagination({
                            dataSource: repuestos,
                            pageSize: 12,
                            
                            afterRender: function() {
                                $("html, body").animate({ scrollTop: 200 }, "slow");
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
                                <div class="modal-header" style="background: linear-gradient(to right, #d81012, #808080); color: #fff;">
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
                                            <div class="col-xl-4 col-md-4 col-sm-6 mb-3">
                                            <div class="card" style="background-image: url('https://t3.ftcdn.net/jpg/00/98/52/26/360_F_98522695_S9vAeY8a3O4AYFUDr2WVlk4eCWrqf7hx.jpg'); background-size: cover; overflow: hidden;">
                                            <a href="/repuesto/`+item.id+`" class="a_titulo_producto"><img class="card-img-top repuestos_image_catalogo"  src="https://panchorepuestos.ddns.net/storage/`+item.urlfoto+`" alt="Card image cap"></a>
                                            <div class="card-body">
                                                <h6 class="card-title">`+item.descripcion+`</h6>
                                                <p class="text-secondary text-uppercase">`+item.nombrefamilia+` </p>
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
                        
            },
            error: function(error){
                console.log(error.responseText);
            }
        });
}
</script>
@endsection

@section('content')
@include('fragm.slider_pequeño')



<div class="container" id="contenedor" data-aos="zoom-in">
  <div style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='currentColor'/%3E%3C/svg%3E&#34;);" aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="/">Inicio</a></li>
      <li class="breadcrumb-item active" aria-current="page"><a href="/marcas-repuestos">Marcas</a></li>
      <li class="breadcrumb-item active" aria-current="page"><a href="" id="enlace_marca">Marca</a></li>
      <li class="breadcrumb-item active" aria-current="page"><a href="" id="enlace_modelo">Modelo</a></li>
      <li class="breadcrumb-item active" aria-current="page">Repuestos</li>
    </ol>
  </div>
    
    <div class="row">
      <div class="col-lg-3">
        <div class="row">
          <div class="col-xl-12 col-lg-8 col-md-8">
            <div id="datos_modelo" class="mb-2">

            </div>
          </div>
          <div class="col-xl-12 col-lg-4 col-md-4">
            <div class="modal-content mb-2" >
              <div class="modal-header" style="background: linear-gradient(to right, #d81012, #808080); color: #fff;">
                  <h5 class="modal-title">Precios</h5>
              </div>
                  <div class="modal-body text-center">
                    <p>
                      <label for="amount">Rango de precios:</label>
                      <input type="text" id="amount" name="range" style="border: 0; color: #cd3a43; font-weight: bold;" />
                      </p>
                      <div id="slider-range" style="width:100%;"></div>
                      <p><input type="button" onclick="filtrar_rango_precio_modelo()" class="btn btn-light btn-sm my-3 w-100" name="send" value="Buscar" /></p>
                  </div>
              
             </div>
          </div>
          <div class="col-lg-12 col-md-12">
            <div class="row">
              <div class="col-xl-12 col-lg-6 col-md-6">
                <div class="modal-content mb-2">
                  <div class="modal-header" style="background: linear-gradient(to right, #d81012, #808080); color: #fff;">
                      <h5 class="modal-title">Familias</h5>
                  </div>
                      <div class="modal-body">
                        <p class="fst-italic text-center text-secondary">Seleccione una familia:</p>
                        <ul id="familias_repuestos" class="list-group lista_letra_pequeña">
      
                        </ul>
                      </div>
                  
                 </div>
              </div>
              <div class="col-xl-12 col-lg-6 col-md-6">
                <div class="modal-content ">
                  <div class="modal-header" style="background: linear-gradient(to right, #d81012, #808080); color: #fff;">
                      <h5 class="modal-title">Marcas</h5>
                  </div>
                      <div class="modal-body">
                        <p class="fst-italic text-center text-secondary">Seleccione una marca:</p>
                        <ul id="marcas_repuestos" class="list-group lista_letra_pequeña">
      
                        </ul>
                      </div>
                  
                 </div>
              </div>
            </div>
            
             
          </div>
        </div>
          
          
           
           
      </div>
      <div class="col-lg-9">
        
          <div id="container"></div>
          <p id="mensaje" class="text-center"></p>
          <div id="pagination"></div>
      </div>
  </div>
    
</div>
<input type="hidden" name="" id="familia" value="">
@endsection
