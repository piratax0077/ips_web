
@extends('layouts.app_totem')

@section('javascript')
<script>
    
    var modal_relacionado=false;
      $( function() {
        $( "#accordion" ).accordion();
        
    } );

  function dame_marcas(){
                $.ajax({
                    type:'get',
                    url: 'https://panchorepuestos.ddns.net/api/ruta_prueba',
                    beforeSend: function(){
                        $('#contenedor_marcas').empty();
                        $('#contenedor_marcas').append('<div class="text-center"> CARGANDO ... </div>');
                    },
                    success: function(marcas){
                        console.log(marcas);
                        $('#contenedor_marcas').empty();
                        marcas.forEach(marca => {
                            
                            $('#contenedor_marcas').append("<div class='col-xl-6 col-md-12 col-sm-6 col-6'><a href='javascript:void(0)' onclick='dame_repuestos_marca("+marca.idmarcavehiculo+")' > <img src='https://panchorepuestos.ddns.net/storage/"+marca.urlfoto+"' alt='' class='imagen_marca_banner'>  </a></div>");
                
                        });
                        
                    },
                    error: function(error){
                        console.log(error.responseText);
                    }
                });
            }

    function dame_repuestos_marca(idmarca)
    {
        
      if(idmarca==0)
      {
        swal({
            text: 'Elija una marca',
            position: 'top-end',
            icon: 'warning',
            toast: true,
            showConfirmButton: false,
            timer: 3000,
        });
        return false;
      }

      var url_buscar='https://panchorepuestos.ddns.net/api/damemodelos_por_marca_totem/'+idmarca;
      let token = localStorage.getItem('token');
      $.ajax({
       type:'GET',
      url:url_buscar,
      beforeSend: function(){
        Swal.fire({
                    icon:'info',
                    text:'CARGANDO',
                    showConfirmButton: false,
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                });
      },
      success:function(resp){
        console.log(resp);
        Swal.close();
        $('#mensaje').empty();
        let modelos =resp[0];
        $('#mensaje').append('<div class="container">Se han encontrado '+modelos.length+' modelos para la marca '+resp[1].marcanombre+'.</div>');
         $('#mensaje').css('color','gray');
         $('#mensaje').css('font-size','15px');
         $('#mensaje').css('font-style','italic');
        if(modelos.length > 0){
          
        let urlfotomarca = resp[1].urlfoto;
        let container = $('#pagination_marca_modelos');
                        container.pagination({
                            dataSource: modelos,
                            pageSize: 12,
                            
                            beforeRender: function() {
                                $("html, body").animate({ scrollTop: 0 }, "slow");
                                return false;
                            },
                            callback: function (data, pagination) {
                                var dataHtml = '<img src="https://panchorepuestos.ddns.net/storage/'+urlfotomarca+'" class="imagen_marca_banner"> </img><div class="row w-100">';

                                $.each(data, function (index, item) {
                                  let resp;
                                    if(item.cantidad_repuestos == 1){
                                      resp = 'Se ha encontrado '+item.cantidad_repuestos+' repuesto.';
                                    }else{
                                      resp = 'Se han encontrado '+item.cantidad_repuestos+' repuestos.';
                                    }
                                    dataHtml += `
                                    <div class="col-lg-4 col-md-6 col-sm-6 mb-3">
                                    <div class="card">
                                    <img class="card-img-top repuestos_image"  src="https://panchorepuestos.ddns.net/storage/`+item.urlfoto+`" alt="Card image cap">
                                    <div class="card-body">
                                        <h6 class="card-title"> <a href="javascript:void(0)" onclick="buscar_por_modelo(`+item.id+`)" class="a_modelo" >`+item.modelonombre+`</a></h6>
                                        <p class="subtitulo_">`+resp+`</p>
                                    </div>
                                    </div>
                                    </div>
                                    `;
                                   
                                });
                                
                                dataHtml += '</div>';

                                $("#container_marca_modelos").html(dataHtml);
                            }
                        });
        }else{
          $("#container_marca_modelos").html('<div class="text-center">Momentaneamente no existen repuestos. </div>');
        }
         
      },
      error: function(error){
        
        console.log(error.responseText);

      }
      });
    }
        
    function activar_selects(){
        $('#marcas_select').removeAttr('disabled');
        $('#modelos_select').attr('disabled');
        $('#anios_vehiculo_select').attr('disabled');
    }

    function damefamilias(){
    
        $.ajax({
                type:'get',
                url: 'https://panchorepuestos.ddns.net/api/familias',
                beforeSend: function(){
                    $('#familias_select').empty();
                    $('#familias_select').append('<option value="0">Cargando... </option>');
                },
                success: function(familias){
                    

                    $('#familias_select').empty();
                    $('#familias_select').append("<option value='0'>Categor铆as </option>");
                    familias.forEach(familia => $('#familias_select').append(`<option value=`+familia.id+` class="text-uppercase">`+familia.nombrefamilia+` </option>`));
                    
                    
                },
                error: function(error){
                    console.log(error.responseText);
                }
            });
    }

    function dame_marcas_select(){
                $.ajax({
                    type:'get',
                    url: 'https://panchorepuestos.ddns.net/api/ruta_prueba',
                    beforeSend: function(){
                        $('#imagenes_marcas').empty();
                        $('#imagenes_marcas').append('<div class="text-center"> CARGANDO ... </div>');
                    },
                    success: function(marcas){
                        $('#imagenes_marcas').empty();
                        $('#marcas_select').empty();
                        $('#marcas_select').append("<option value='0'>Marcas </option>");
                        marcas.forEach(marca => {
                            
                            $('#imagenes_marcas').append("<div class='col-xl-2 col-md-3 col-sm-6 col-6'><a href='/modelos_marca/"+marca.idmarcavehiculo+"' > <img src='https://panchorepuestos.ddns.net/storage/"+marca.urlfoto+"' alt='' class='imagen_marca_banner'>  </a></div>");
                            
                            $('#marcas_select').append("<option value='"+marca.idmarcavehiculo+"'>"+marca.marcanombre+" </option>");
                        });
                        
                    },
                    error: function(error){
                        console.log(error.responseText);
                    }
                });
            }

    function dame_modelos(){
        var x = document.getElementById("marcas_select").value;
        let url = 'https://panchorepuestos.ddns.net/api/'+x+'/damemodelos';
        
        $.ajax({
            type:'get',
            url: url,
            beforeSend: function(){
                $('#modelos_select').empty();
                $('#anios_vehiculo_select').empty();
                $('#modelos_select').append('<option>Cargando ... </option>');
            },
            success: function(modelos){
    
                $('#modelos_select').removeAttr('disabled');
                $('#modelos_select').empty();
                modelos.forEach(modelo => {
                    
                    $('#modelos_select').append(`<option value=`+modelo.id+` >`+modelo.modelonombre+` </option>`);
                    $('#anios_vehiculo_select').append(`<option value=`+modelo.anios_vehiculo+` >`+modelo.anios_vehiculo+` </option>`);
                });
            },
            error: function(error){
                console.log(error.responseText);
            }
        });
    }

    function dame_modelos_buscador(){
        var x = document.getElementById("marcas_select").value;
        let url = 'https://panchorepuestos.ddns.net/api/'+x+'/damemodelos';
        
        $.ajax({
            type:'get',
            url: url,
            beforeSend: function(){
                $('#modelos_select').empty();
                $('#anios_vehiculo_select').empty();
                $('#modelos_select').append('<option>Cargando ... </option>');
            },
            success: function(modelos){
    
                $('#modelos_select').removeAttr('disabled');
                $('#modelos_select').empty();
                modelos.forEach(modelo => {
                    
                    $('#modelos_select').append(`<option value=`+modelo.id+` >`+modelo.modelonombre+` </option>`);
                    $('#anios_vehiculo_select').append(`<option value=`+modelo.anios_vehiculo+` >`+modelo.anios_vehiculo+` </option>`);
                });
            },
            error: function(error){
                console.log(error.responseText);
            }
        });
    }

    function dameaniosvehiculo(){
        var x = document.getElementById("modelos_select").value;
        let url = 'https://panchorepuestos.ddns.net/api/'+x+'/dameaniosvehiculo';
        
        $.ajax({
            type:'get',
            url: url,
            beforeSend: function(){
                $('#anios_vehiculo_select').empty();
                
                $('#anios_vehiculo_select').append('<option>Cargando ... </option>');
            },
            success: function(modelo){
                $('#anios_vehiculo_select').empty();
                $('#anios_vehiculo_select').append(`<option value=`+modelo.anios_vehiculo+` >`+modelo.anios_vehiculo+` </option>`);
            },
            error: function(error){
                console.log(error.responseText);
            }
        });
    }

    function busqueda_principal_totem(){
        let modelo = $('#modelos_select').val();
        //let familia = $('#familias_select').val();
        // Simulate a mouse click:
        
        if(modelo == 0){
            Swal.fire({
                title: "Error",
                text: "Debe ingresar un Modelo de Vehículo",
                icon: "error"
            });
            return false;
        }else{
            //window.location.href = "/busqueda_por_modelo/"+modelo+"/"+familia;
            buscar_por_modelo_totem(modelo);
        }
    }

    function buscar_por_modelo_totem(idmodelo)
    {

      var url_buscar='https://panchorepuestos.ddns.net/api/damemodelo_por_marca_totem/'+idmodelo;
   
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
      success:function(repuestos){
         
         Swal.close();
        let modelo = repuestos[1];
        let marcas_repuestos = repuestos[2];
        let familias_repuestos = repuestos[3];
        if(repuestos[0].length == 0){
        $('#container_busqueda').empty();
        $('#container_busqueda').addClass('text-center');
        $('#container_busqueda').html(' <p>Lo sentimos, no hemos encontrado coincidencias.</p><img src="{{asset("storage_original/imagenes/sinresultados.jpg")}}" class="image_transbank" />');
        }else{
        let container = $('#pagination_busqueda');
        container.pagination({
            dataSource: repuestos[0],
            pageSize: 4,
            
            afterRender: function() {
                $("html, body").animate({ scrollTop: 0 }, "slow");
                return false;
            },
            callback: function (data, pagination) {
                if(repuestos[0].length > 1)
                var dataHtml = '<div class="row">';
                    $.each(data, function (index, item) {
                        let precio_format = Number(item.precio_venta).toFixed(0);
                        if(item.stock_actual >= 10){
                                var stock_ = 'Disponible';
                            }else{
                                var stock_ = item.stock_actual;
                            }
                        dataHtml += `
                        <div class="col-xl-6 col-md-6 col-sm-6 mb-3">
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
                        <div class="card-footer d-flex">
                            <input type="number" class="form-control form-control-sm" name="cantidad-${item.id}" id="cantidad-${item.id}">
                            <button class="btn btn-outline-success btn-sm" onclick="agregar_repuesto(${item.id})">+</button>
                        </div>
                        </div>
                        </div>
                        `;
                        
                    });

                dataHtml += '</div></div>';

                $("#container_busqueda").html(dataHtml);
            }
        });
        }

                        
      },

      error: function(error){
        console.log(error.responseText);
      }
      });

    }

        function buscar_main(value){
                
                var valor = $('#tag_principal').val();
                if(valor === '' || valor.trim() == 0){
                    Swal.fire({
                    title: "Error",
                    text: "Debe ingresar una descripci贸n",
                    icon: "error"
                });
                return false;
                }
                let url = '/'+valor+'/buscador';
                
                // Simulate a mouse click:
                window.location.href = url;
            }

        function buscar_por_oem(){
            let oem = $('#buscar_por_oem').val();
            if( oem === '' || oem.trim() == 0){
                Swal.fire({
                    title: "Error",
                    text: "Debe ingresar un OEM",
                    icon: "error"
                });
                return false;
            }
            window.location.href = "/busqueda_por_oem/"+oem;
        }

        function buscar_por_medidas(){
            let medida = $('#buscar_por_medida').val();
            if( medida === '' || medida.trim() == 0){
                Swal.fire({
                    title: "Error",
                    text: "Debe ingresar una Medida",
                    icon: "error"
                });
                return false;
            }
         
            window.location.href = "/busqueda_por_medida/"+medida;
        }

        function getUser_ofertas(){
                let url = 'https://panchorepuestos.ddns.net/api/get-user';

                $.ajax({
                    type:'post',
                    url: url,
                    headers: {"Authorization": 'Bearer '+localStorage.getItem('token')},
                    beforeSend: function(){
                        $('#no_autenticado').css('display','none');
                        $('#autenticado').css('display','none');
                    },
                    success: function(resp){
                      if(resp.user){
                        return true;
                      } else return false;                     
                    },
                    error: function(error){
                        console.log(error.responseText);
                    }
                });
            }

        function dame_mensaje(){
            $('#modalMensaje').modal("show");
        }
                
        dame_marcas();
        //damefamilias();
        // dameofertas();

       
        // crear funcion para simular un desplace hacia la derecha del elemento ul con id descuentos
        function moverDerecha(){
            let ul = document.getElementById('descuentos');
            // que el desplazamiento a la derecha sea de 100px
            //ul.scrollLeft += 100;
            // que el desplazamiento a la derecha sea de 100px y que se ejecute en 2 segundo
            ul.scrollLeft += 100;
            ul.style.transition = 'all 2s';
            
        }

        // crear funcion para simular un desplace hacia la izquierda del elemento ul con id descuentos
        function moverIzquierda(){
            let ul = document.getElementById('descuentos');
            
            ul.scrollLeft -= 100;
            
        }

        function enter_press(e,q)
    {

      console.log(q);
        // let formulario = document.querySelector('#buscar_por_descripcion');
        // let resultado = '';
        // $('#zona_grilla').empty();
        // dametodorepuestos();
        // let nombre = formulario.value.toLowerCase();
       
        // for (let repuesto of repuestos[0]) {
        //  let desc = repuesto.descripcion.toLowerCase();
        //  if(desc.indexOf(nombre) !== -1){
        //   $('#zona_grilla').append(`
        //     <li>`+repuesto.descripcion+` </li>
        //   `);
        //  }
        // }
 
        var keycode = e.keyCode;
        if(keycode=='13')
        {
        if(q=='d') buscar_por_descripcion();
        if(q=='p') buscar_por_proveedor();
        if(q=='o') buscar_por_oem();
        if(q=='m') buscar_por_medidas();
        if(q=='c') buscar_por_codigo_interno();
        if(q=='f') buscar_por_codigo_fabricante();
        }
    }

    function clic_en(valor)

  {

    // $("#zona_familia").html("");

    // $("#zona_grilla").html("");

    $("#zona_fotos").html("");

    $("#zona_similares").html("");

    $("#zona_oem").html("");

    $("#zona_fab").html("");

    //$("#mensajes-modal").html("");

    $("#mod_titulo_header").html("BUSCAR REPUESTO");

    switch (valor){

      /*

      case 1: //buscar por descripcion

        document.getElementById("buscar_por_oem").value="";

        document.getElementById("buscar_por_codigo_proveedor").value="";

        document.getElementById("buscar_por_medidas").value="";

        document.getElementById("buscar_por_codint").value="";

        document.getElementById("buscar_por_codigo_fabricante").value="";

        break;

      case 2: //buscar por codigo proveedor

        document.getElementById("buscar_por_descripcion").value="";

        document.getElementById("buscar_por_oem").value="";

        document.getElementById("buscar_por_medidas").value="";

        document.getElementById("buscar_por_codint").value="";

        document.getElementById("buscar_por_codigo_fabricante").value="";

        break;

      case 3: //buscar por oem

        document.getElementById("buscar_por_descripcion").value="";

        document.getElementById("buscar_por_codigo_proveedor").value="";

        document.getElementById("buscar_por_medidas").value="";

        document.getElementById("buscar_por_codint").value="";

        document.getElementById("buscar_por_codigo_fabricante").value="";

        break;

      case 4: //buscar por medidas

        document.getElementById("buscar_por_descripcion").value="";

        document.getElementById("buscar_por_oem").value="";

        document.getElementById("buscar_por_codigo_proveedor").value="";

        document.getElementById("buscar_por_codint").value="";

        document.getElementById("buscar_por_codigo_fabricante").value="";

        break;

        */

      case 5: //buscar por marcas y modelos

        /*

        document.getElementById("buscar_por_descripcion").value="";

        document.getElementById("buscar_por_oem").value="";

        document.getElementById("buscar_por_codigo_proveedor").value="";

        document.getElementById("buscar_por_medidas").value="";

        document.getElementById("buscar_por_codint").value="";

        document.getElementById("buscar_por_codigo_fabricante").value="";

        */

        buscar_por_marca_modelo();

        break;

        /*

      case 6: //buscar por codigo interno pancho repuestos

        document.getElementById("buscar_por_descripcion").value="";

        document.getElementById("buscar_por_oem").value="";

        document.getElementById("buscar_por_codigo_proveedor").value="";

        document.getElementById("buscar_por_medidas").value="";

        document.getElementById("buscar_por_codigo_fabricante").value="";

      case 7: //Buscar por código de fabricante

        document.getElementById("buscar_por_descripcion").value="";

        document.getElementById("buscar_por_oem").value="";

        document.getElementById("buscar_por_codigo_proveedor").value="";

        document.getElementById("buscar_por_medidas").value="";

        document.getElementById("buscar_por_codint").value="";

        */

    }

  }

  function dameofertas(){
            $.ajax({
                    type:'get',
                    url: 'https://panchorepuestos.ddns.net/api/dameofertas',
                    beforeSend: function(){
                        $('#container_busqueda').empty();
                        $('#container_busqueda').append('<div class="text-center"> CARGANDO ... </div>');
                    },
                    success: function(resp){
                        
                       let ofertas = resp[0];
                       let descuentos = resp[1];
                       console.log(ofertas);
                        $('#container_busqueda').empty();
                        $('#descuentos').empty();
                        if(ofertas.length > 0){
                            let container = $('#pagination_busqueda');
                            container.pagination({
                                dataSource: ofertas,
                                pageSize: 6,
                                
                                afterRender: function() {
                                    $("html, body").animate({ scrollTop: 0 }, "slow");
                                    return false;
                                },
                                callback: function (data, pagination) {
                                    
                                    var dataHtml = "<h1 class='titulo_ofertas'>Algunas de nuestras ofertas</h1><div class='row'>";

                                    $.each(data, function (index, item) {
                                        let precio_format = Number(item.precio_venta).toFixed(0);
                                        if(item.stock_actual >= 10){
                                                var stock_ = 'Disponible';
                                            }else{
                                                var stock_ = item.stock_actual;
                                            }
                                        dataHtml += `
                                            <div class="col-lg-6 col-md-6 my-2 text-center card_oferta">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                       <img class="card-img-top" style="height: 220px; width: 100%;"  src="https://panchorepuestos.ddns.net/storage/`+item.urlfoto+`" alt="Card image cap"> 
                                                    </div>
                                                    <div class="col-md-6">
                                                        <p style="height: 100px;"> `+item.descripcion+`</p>
                                                        <p class="text-secondary">`+item.marcarepuesto.marcarepuesto+` </p>
                                                        
                                                        <p><span class="card-price letra_grande">$ `+commaSeparateNumber(item.precio_actualizado)+`</span> </p>
                                                        <button class="btn btn-outline-primary btn-sm" style="border-radius: 10px;" onclick="vista_rapida(${item.id})">Agregar a carrito</button>
                                                    </div>
                                                </div>
                                                
                                                
                                            </div>
                                            `;
                                        
                                    });

                                    dataHtml += '</div>';

                                    $("#container_busqueda").html(dataHtml);
                                }
                            });

                        }else{
                            $('#ofertas').append();
                        }
                        
                        if(descuentos.length > 0){
                            descuentos.forEach( d => {
                                if(d.image_path == null || d.image_path == ''){
                                    d.image_path = 'storage_original/imagenes/logo_negro_200.jpeg';
                                    $('#descuentos').append(`
                                    <li class="list-group-item text-uppercase" style="background: #faee0e;color: gray; font-size:13px;"> <a class="text-decoration-none text-dark" href="`+d.nombrefamilia+`/buscador">
                                        <img src="`+d.image_path+`" style="height: 160px; width: 160px;" class="card-img-top mb-3" alt="..."> `+d.nombrefamilia+` 
                                        </a>
                                    </li>
                                    `);
                                }else{
                                //$('#descuentos').append('<h3 class="p-3">Todos los repuestos de la familia '+d.nombrefamilia+' tienen un <span style="color: red;">'+d.porcentaje+'% </span> de descuento. &#x1F603;🔥 </h3>');
                                $('#descuentos').append(`
                                <li class="list-group-item text-uppercase" style="background: #fff; color: gray; font-size:13px;"> <a class="text-decoration-none text-dark" href="`+d.nombrefamilia+`/buscador">
                                    <img src="https://panchorepuestos.ddns.net/storage/imagenes/familias/`+d.image_path+`" style="height: 160px; width: 160px;" class="card-img-top mb-3" alt="..."> `+d.nombrefamilia+` 
                                    </a>
                                </li>
                                `);
                                }
                                
                            });
                        }
                        
                    },
                    error: function(error){
                        console.log(error.responseText);
                    }
                });
        }

  function buscar_por_descripcion()
    {
      // verificar_carrito_transferido();
      var valor=0;
      //Si medidas y stock estan marcados, valor=3
      var _descripcion=document.getElementById("buscar_por_descripcion");
      var texto=_descripcion.value.trim();
        if(texto.length==0){
            Swal.fire({
                title: 'SI SERÁS SI SERÁS...',
                text: "Escribe algo para buscar poooh!!",
                icon: 'error',
            });
            return false;
        }

      var _donde=_descripcion.placeholder;
      var descripcion=valor+texto;
      if(descripcion.indexOf("/")){
        descripcion=descripcion.replace(/\//g,"_&_");
      }

      $("#mod_titulo_header").html("<p class='d-flex justify-content-center'>BUSCANDO <strong>"+_descripcion.value.toUpperCase()+" EN "+_donde.toUpperCase()+"</strong></p>");

      var url= 'https://panchorepuestos.ddns.net/api/buscardescripcion/'+descripcion;

      $.ajax({
       type:'GET',
       beforeSend: function () {
        if(modal_relacionado)
        {
          $("#zona_fotos_r").html("");
          $("#zona_similares_r").html("");
          $("#zona_oem_r").html("");
        }else{
          $("#zona_fotos").html("");
          $("#zona_similares").html("");
          $("#zona_oem").html("");
          $("#zona_fab").html("");
        }

        $("#container_busqueda").html("<h3>BUSCANDO...</h3>");
        $("#mensajes-modal").html("Buscando...");
      },

      url:url,

      success:function(resp){

        let repuestos = resp;
        console.log(repuestos);
        // return false;
      
        $("#mensajes-modal").html("Listo...");

        let container = $('#pagination_busqueda');
        container.pagination({
            dataSource: repuestos,
            pageSize: 4,
            
            afterRender: function() {
                $("html, body").animate({ scrollTop: 0 }, "slow");
                return false;
            },
            callback: function (data, pagination) {
                
                var dataHtml = "<h1 class='titulo_ofertas'>"+_descripcion.value.toUpperCase()+"</h1><div class='row'>";

                $.each(data, function (index, item) {
                    let precio_format = Number(item.precio_venta).toFixed(0);
                    if(item.stock_actual >= 10){
                            var stock_ = 'Disponible';
                        }else{
                            var stock_ = item.stock_actual;
                        }
                    dataHtml += `
                    <div class="col-xl-4 col-md-6 col-sm-6 mb-3">
                    <div class="card" style="background-image: url('https://t3.ftcdn.net/jpg/00/98/52/26/360_F_98522695_S9vAeY8a3O4AYFUDr2WVlk4eCWrqf7hx.jpg'); background-size: cover; overflow: hidden;">
                    <a href="javascript:void(0)" class="a_titulo_producto"> <img class="card-img-top repuestos_image_catalogo"  src="https://panchorepuestos.ddns.net/storage/`+item.urlfoto+`" alt="Card image cap"></a>
                    <div class="card-body">
                        <h6 class="card-title">`+item.descripcion+`</h6>
                        <p class="text-secondary">`+item.nombrefamilia+` </p>
                        <p class="text-secondary">`+item.marcarepuesto+` </p>
                        <p class="card-text text-center card-price letra_grande">$ `+commaSeparateNumber(precio_format)+`</p>
                        <p class="fst-italic">Stk. `+stock_+`</p>
                        
                        <button class="btn btn-warning w-100 mt-3" onclick="vista_rapida(`+item.id+`)" ><i class="fa-solid fa-eye icon_header"></i>Vista rápida </button>
                    </div>
                    <div class="card-footer d-flex">
                        <input type="number" class="form-control form-control-sm" name="cantidad-${item.id}" id="cantidad-${item.id}">
                        <button class="btn btn-outline-success btn-sm" onclick="agregar_repuesto(${item.id})">+</button>
                    </div>
                    </div>
                    </div>
                    `;
                    
                });

                dataHtml += '</div>';

                $("#container_busqueda").html(dataHtml);
            }
        });



      },

      error: function(error){

        $("#busca-repuesto-modal").modal("hide");

        Swal.fire({

            title: 'ERROR '+error.status,

            text: formatear_error(error.responseText),

            icon: 'error',

        });

      }



      });

    }

    function formatear_error(error){

        let max=300;

        let rpta=error.substring(0,max);

        return rpta;

    }

    function agregar_repuesto(id_repuesto){
        $('#idrepuesto_modal').val(id_repuesto);
        let cantidad = $('#cantidad-'+id_repuesto).val();
        
        if(cantidad == '' || cantidad == 0 || cantidad < 0){
            Swal.fire({
                icon:'error',
                text:'Debe ingresar una cantidad válida',
            });
            return false;
        }

        let url = "https://panchorepuestos.ddns.net/api/agregar_carrito_totem";
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
            success: function(resp){
            console.log(resp);
            if(resp.message == 'OK'){
               Swal.fire({
                title:'Exito',
                text:'Su repuesto fue agregado correctamente.',
                icon:'success',
                position:'top-end',
                showConfirmButton: false,
                toast: true
               });
                $('#modalVistaRapida').modal('hide');
                limpiar_busqueda();
            }
            setTimeout(() => {
                dame_carrito_(); 
            }, 2000);
            },
            error: function(error){
                Swal.fire({
                    title: "Error",
                    text: error.responseText,
                    icon: "error"
                });
                $('#modalVistaRapida').modal('hide');
                
                }
            });
        

        
        }

    function dame_carrito_(){
        
        let url = 'https://panchorepuestos.ddns.net/api/revisar_carrito_totem';
        
        $.ajax({
            type:'get',
            url: url,
            beforeSend: function(){
                $('#container_carrito').empty();
                $("#container_carrito").append('<img src="{{asset("storage_original/imagenes/car-wheel.gif")}}" alt="Cargando ..." class="w-100">');
                $('#modalVistaRapida').modal('hide');
                // Swal.fire({
                //     icon:'info',
                //     text:'CARGANDO',
                //     showConfirmButton: false,
                //     allowOutsideClick: false,
                //     allowEscapeKey: false,
                // });
            },
            success: function(repuestos){
                console.log(repuestos);
                Swal.close();
                if(repuestos.status){
                    
                    $('#container_carrito').empty();
                    $("#container_carrito").append('<div class="w-100 text-center p-5"><img src="{{asset("storage_original/imagenes/empty-car.png")}}" alt="Cargando ..."><br/>  </div>');
                    $('#modalVistaRapida').modal('hide');
                    return false;
                }
                $('#cantidad_items').val(repuestos.repuestos.length);
                $('#cantidad_items_modal').html(repuestos.repuestos.length);
                $('#cantidad_items_carro_header').html(repuestos.repuestos.length);
                if(repuestos.repuestos.length > 0){
                    $('#numero_carrito').val(repuestos.repuestos[0].numero_carrito);
                }else{
                    // $('#zona_grilla').empty();
                }
                
                let monto_total = 0;
                let container = $('#pagination_carrito');
                container.pagination({
                                dataSource: repuestos.repuestos,
                                pageSize: 10,
                                callback: function (data, pagination) {
                                    if(repuestos.repuestos.length == 1){
                                        var dataHtml = '<p style="color: gray; font-size: 15px; font-style: italic;">Usted lleva '+repuestos.repuestos.length+' producto </p><div class="row">';
                                    }else{
                                        var dataHtml = '<p style="color: gray; font-size: 15px; font-style: italic;">Usted lleva '+repuestos.repuestos.length+' productos </p><div class="row">';
                                    }
                                    dataHtml+= '<p id="monto_total_" style="color: gray; font-size: 15px; font-style: italic;"></p>';
                                    
                                    $.each(data, function (index, item) {
                                        if(item.oferta == 1){
                                            let precio_format = Number(item.precio_venta).toFixed(0);
                                            monto_total += (item.cantidad * precio_format);
                                            dataHtml += `
                                            
                                            <div class="col-xl-6 col-md-12 col-lg-12 col-sm-6 mb-3">
                                            <div class="card">
                                            <img class="card-img-top repuestos_image"  src="https://panchorepuestos.ddns.net/storage/`+item.urlfoto+`" alt="Card image cap">
                                            <div class="card-body">
                                                <a href="javascript:void(0)" onclick="vista_rapida(`+item.id+`)" class="a_titulo_producto"> <p style="font-size: 15px; height: 110px;">`+item.descripcion+`</p> </a>
                                                <table class="table">
                                                
                                                <tbody>
                                                    <tr>
                                                        <td style="font-size: 15px;">Cantidad </td>
                                                        <td style="font-size: 15px;">`+item.cantidad+`</td>
                                                    </tr>
                                                    <tr>
                                                        <td style="font-size: 15px;">P.U.</td>
                                                        <td style="font-size: 15px;">$`+commaSeparateNumber(precio_format)+`<span class="badge bg-danger" style="margin-left: 5px;">Oferta</span></td>
                                                    </tr>
                                                    <tr>
                                                        <td style="font-size: 15px;">Total</td>
                                                        <td style="font-size: 15px;">$`+commaSeparateNumber(item.cantidad * precio_format)+`</td>
                                                    </tr>
                                                    </tbody>
                                                </table>
                                                <button class="btn btn-danger btn_eliminar_item_carrito w-100" onclick="eliminar_item_carrito_virtual(`+item.id+`)">X</button>
                                            </div>
                                            </div>
                                            
                                            </div>
                                            
                                            `;
                                        }else{
                                            let precio_format = Number(item.precio_venta).toFixed(0);
                                            monto_total += (item.cantidad * precio_format);
                                            dataHtml += `
                                            
                                            <div class="col-xl-6 col-md-12 col-lg-12 col-sm-6 mb-3">
                                            <div class="card">
                                            <img class="card-img-top repuestos_image"  src="https://panchorepuestos.ddns.net/storage/`+item.urlfoto+`" alt="Card image cap">
                                            <div class="card-body">
                                                <a href="javascript:void(0)" onclick="vista_rapida(`+item.id+`)" class="a_titulo_producto"> <p style="font-size: 15px; height: 110px;">`+item.descripcion+`</p> </a>
                                                <table class="table">
                                                
                                                <tbody>
                                                    <tr>
                                                        <td style="font-size: 15px;">Cantidad </td>
                                                        <td style="font-size: 15px;">`+item.cantidad+`</td>
                                                    </tr>
                                                    <tr>
                                                        <td style="font-size: 15px;">P.U.</td>
                                                        <td style="font-size: 15px;">$`+commaSeparateNumber(precio_format)+`</td>
                                                    </tr>
                                                    <tr>
                                                        <td style="font-size: 15px;">Total</td>
                                                        <td style="font-size: 15px;">$`+commaSeparateNumber(item.cantidad * precio_format)+`</td>
                                                    </tr>
                                                    </tbody>
                                                </table>
                                                <button class="btn btn-danger btn_eliminar_item_carrito w-100" onclick="eliminar_item_carrito_virtual(`+item.id+`)">X</button>
                                            </div>
                                            </div>
                                            </div> 
                                            `;
                                        }
                                    });

                                    dataHtml += '</div>';

                                    dataHtml+= '<button class="btn btn-outline-success btn-sm w-100 my-3" type="button" onclick="enviar_a_cobrar()">Enviar a Cobrar <span id="total_a_cobrar_boton"> </span></button>';
                                    
                                    $("#container_carrito").html(dataHtml);
                                    $('#monto_total_').empty();
                                    $('#monto_total_').append('TOTAL $ '+commaSeparateNumber(monto_total));
                                    $('#total_a_cobrar_boton').html('TOTAL $ '+commaSeparateNumber(monto_total));
                                }
                    });
                    
                },
            error: function(error){
                console.log(error.responseText);
                Swal.fire({
                    icon:'error',
                    text:error.responseText,
                    
                });
            }
        });
        
    }

    function vista_rapida(idrepuesto){
            let url = 'https://panchorepuestos.ddns.net/api/'+idrepuesto+'/repuesto_totem';
         
            $.ajax({
                type:'get',
                url: url,
                beforeSend: function(){
                    $('#stock_repuesto').append(1);
                    $('#modalbody_vistarapida').removeClass('d-block');
                    $('#modalbody_vistarapida').addClass('d-none');
                    $('#cantidad').val(1);
                },
                success: function(data){
                    // abrir modal
                    $('#modalVistaRapida').modal('show');
                console.log(data);
                   let rectificadores = data[7];
                   let alternadores = data[8];
                   // cerrar modal fullscreen
                   $('#modalMarcas').modal('hide');
                    $('#id_repuesto_detalle').attr('href','/repuesto/'+idrepuesto);
                    
                    $('#enlace_facebook_compartir').attr('href','http://www.facebook.com/sharer.php?u=https://panchorepuestos.cl/repuesto/'+idrepuesto);
                    $('#enlace_twitter_compartir').attr('href','/repuesto/'+idrepuesto);
                    $('#enlace_email_compartir').attr('href','/repuesto/'+idrepuesto);
                    let flag = data[4];
                    $('#modalbody_vistarapida').removeClass('d-none');
                    $('#modalbody_vistarapida').addClass('d-block');
                    let repuesto = data[0];
                    $('#idrepuesto_modal').val(repuesto.id);
                    
                    $('#vista_rapida_titulo').empty();
                    $('#vista_rapida_titulo').append(repuesto.descripcion);
                    // crear un array con las medidas separadas por una ,
                    let medidas = repuesto.medidas.split(',');
                    $('#medidas').empty();
                    medidas.forEach(medida => {
                        $('#medidas').append(medida+'<br/>');
                    });

                    if(rectificadores.length > 0){
                        // le quitamos la clase d-none y le asignamos la clase d-blocl para que se muestre
                        $('#tr_rectificadores').removeClass('d-none');
                        
                        $('#rectificadores').empty();
                        rectificadores.forEach(rectificador => {
                            $('#rectificadores').append(rectificador['rectificador']+"</br>");
                        });
                    }else{
                        $('#tr_rectificadores').addClass('d-none');
                    }

                    if(alternadores.length > 0){
                        // le quitamos la clase d-none y le asignamos la clase d-blocl para que se muestre
                        $('#tr_alternadores').removeClass('d-none');
                        $('#alternadores').empty();
                        alternadores.forEach(alternador => {
                            $('#alternadores').append(alternador['alternador']+"</br>");
                        });
                        
                    }else{
                        $('#tr_alternadores').addClass('d-none');
                    }

                    let fotos = data[1];
                    let similares = data[2];
                    let precio_format = Number(repuesto.precio_venta).toFixed(0);
                    precio_total = precio_format;
                    $('#marca_repuesto_').empty();
                    $('#precio').empty();
                    $('#familia_repuesto').empty();
                    $('#codigo_interno_repuesto_').empty();
                    $('#stock_repuesto').val(repuesto.stock_actual);
                    $('#familia_repuesto').append(repuesto.nombrefamilia);
                    $('#codigo_interno_repuesto_').append(repuesto.codigo_interno);
                    $('#marca_repuesto_').append(repuesto.marcarepuesto);
                    //Si flag viene nulo, el repuesto no tiene ofertas
                    if(flag == null || flag == 0){
                        $('#precio').append('<strong>$ '+commaSeparateNumber(precio_format)+'</strong>');
                    }else{
                        $('#precio').append('<strong>$ '+commaSeparateNumber(flag.precio_actualizado)+' </strong>');
                        
                    }
                    
                    if(fotos.length > 0){
                        $('#carousel-inner').empty();
                        $('#preview_photos').empty();
                        $('#carousel-inner').append(`
                        <div class="carousel-item active">
                            <img class="d-block w-100 fotos_repuesto zoom" src="https://panchorepuestos.ddns.net/storage/`+fotos[0].urlfoto+`" alt="First slide">
                            </div>
                        `);
                        fotos.forEach(foto => {
                            $('#carousel-inner').append(`
                                <div class="carousel-item">
                                    <img src="https://panchorepuestos.ddns.net/storage/`+foto.urlfoto+`" class="d-block w-100 fotos_repuesto zoom" alt="`+repuesto.descripcion+`">
                                </div>
                            `);
                            $('#preview_photos').append(`
                            <div class="col-3 col-md-3" >
                                <img src="https://panchorepuestos.ddns.net/storage/`+foto.urlfoto+`" class="d-block w-100" style="width: 100px;" alt="`+repuesto.descripcion+`">
                            </div>
                            `);
                        });
                    }
                else{
                $('#carousel-inner').empty();
                $('#carousel-inner').append(`
                <div class="carousel-item active">
                    <img class="d-block w-100 fotos_repuesto" src="https://panchorepuestos.ddns.net/storage/fotozzz/d42e48078f97443083b70b04a710b31b.png" alt="First slide">
                    </div>
                `);
                fotos.forEach(foto => {
                    $('#carousel-inner').append(`
                        <div class="carousel-item">
                            <img src="https://panchorepuestos.ddns.net/storage/fotozzz/d42e48078f97443083b70b04a710b31b.png" class="d-block w-100 fotos_repuesto zoom" alt="`+repuesto.descripcion+`">
                        </div>
                    `);
                });

                
                }

                let container = $('#pagination_');
                            container.pagination({
                                dataSource: similares,
                                pageSize: 3,
                                callback: function (data, pagination) {
                                    var dataHtml = "<div>";

                                    $.each(data, function (index, similar) {
                                        
                                        dataHtml += `
                                        <tr>
                                        <td><img class="foto_marca" src="https://panchorepuestos.ddns.net/storage/`+similar.urlfoto+`" /> </td>  
                                        <td style="font-size: 15px;">`+similar.modelonombre+` </td>
                                        <td style="font-size: 15px;">`+similar.anios_vehiculo+` </td>
                                        <td></td>
                                        </tr>
                                        `;
                                        
                                    });

                                    dataHtml += '</div>';
                                    dataHtml += `
                                    
                                    `;

                                    $("#container_").html(dataHtml);
                                }
                            });
                },
                error: function(error){
                    Swal.fire({
                        icon:'error',
                        html:error.responseText
                    });
                }
        });
    }
    

    function eliminar_item_carrito_virtual(idrep){
        Swal.fire({
            title: '¿Estás seguro?',
            text: '¿Deseas eliminar el item '+idrep+'?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'No, cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                // Aquí puedes agregar la lógica para enviar a cobrar
                eliminar_item_carrito_virtual_confirmar(idrep);
            } 
        });
    }

    function eliminar_item_carrito_virtual_confirmar(idrep){
        let numero_carrito = $('#numero_carrito').val();
        let url = 'https://panchorepuestos.ddns.net/api/eliminar_item_carrito_totem/'+idrep+'/'+numero_carrito;
       
        $.ajax({
            type:'get',
            url: url,
            beforeSend: function(){
            Swal.fire({
            	icon:'info',
            	text:'Eliminando ...'
            });
            },
            success: function(repuestos){
                console.log(repuestos);
         	    swal.close();
                console.log(repuestos.repuestos);
                if(repuestos.repuestos.length == 0){
                $('#mensaje').empty();
                $('#mensaje').append('<div class="w-100 text-center p-5"><img src="{{asset("storage_original/imagenes/empty-car.png")}}" alt="Cargando ..."><br><a href="/" class="btn btn-warning mt-3">Volver al inicio </a></div>');
                $('#contenido').empty();
                $('#resumen').empty();
                $('#contenedor').addClass('d-none');
                dame_carrito_();
               }else{
                $('#numero_carrito').val(repuestos.repuestos[0].numero_carrito);
              
                $('#cantidad_items').val(repuestos.repuestos.length);
                $('#mensaje').empty();
                dame_carrito_();
               }
                        
            },
            error: function(error){
                Swal.fire({
                    icon:'info',
                    html:error.responseText
                });
                console.log(error);
            }
        });
    }

    function enviar_a_cobrar() {
        Swal.fire({
            title: '¿Estás seguro?',
            text: '¿Deseas enviar a cobrar?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sí, enviar',
            cancelButtonText: 'No, cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                // Aquí puedes agregar la lógica para enviar a cobrar
                enviar_a_cobrar_confirmar();
            } 
        });
    }

    function enviar_a_cobrar_confirmar(){
        let cantidad_items = $('#cantidad_items').val();
        if(cantidad_items == '' || cantidad_items == 0){
            Swal.fire({
                title:'error',
                text:'No hay repuestos agregados',
                icon:'error'
            });
            return false;
        }
        //Se genera un cliente_id para manejar los carritos transferidos
        let cliente_id = Math.floor(Math.random() * 1000);
        let numero_carrito = $('#numero_carrito').val();
        let url = 'https://panchorepuestos.ddns.net/api/transferir_carrito_completo_totem/'+cliente_id+'/'+numero_carrito;

        $.ajax({
            type:'get',
            url: url,
            success: function(resp){
                console.log(resp);
                if(resp == 'OK'){
                    Swal.fire({
                        icon:'success',
                        text:'Carrito enviado correctamente',
                        title:'Felicitaciones'
                    });
                    imprimir_ticket(numero_carrito);
                    borrar_carrito('actual');
                    $('#container_busqueda').empty();
                   
                    dameofertas();
                }
            },
            error: function(error){
                Swal.fire({
                    icon:'info',
                    title:'info',
                    html: error.responseText
                });
            }
        });
    }

    function imprimir_ticket(numero_carrito) {
        let url = 'https://panchorepuestos.ddns.net/api/imprimir_ticket_totem/' + numero_carrito;
        $.ajax({
            type: 'get',
            url: url,
            beforeSend: function() {
                Swal.fire({
                    title: 'ESPERE...',
                    icon: 'info',
                });
            },
            success: function(resp) {
                Swal.close();
                console.log(resp);
                
                let r = resp;
                let repuestos = r.detalle;
                if (r.estado == 'OK') {
                    const { jsPDF } = window.jspdf;
                    const widthInPx = 150;
                    const heightInPx = 200;

                    // Convertir píxeles a milímetros (aproximadamente)
                    const widthInMm = widthInPx / 3.78;
                    const heightInMm = heightInPx / 3.78;
                    const doc = new jsPDF({
                        orientation: 'portrait',
                        unit: 'mm',
                        format: [widthInMm, heightInMm]
                    });

                    // Establecer márgenes y altura de filas
                    const margin = 1; // Márgenes reducidos para formato pequeño
                    const startY = 15; // Posición inicial en Y para el contenido
                    const rowHeight = 3; // Altura de cada fila ajustada para formato pequeño
                    doc.setFontSize(4); // Tamaño de fuente más pequeño para formato compacto

                    // Título del documento
                    doc.setFont("helvetica", "bold");
                    doc.setFontSize(6); // Tamaño del título ajustado
                    doc.text(`Ticket: ${numero_carrito}`, margin, startY - 5); // Ajusta la posición del título

                    // URL de la imagen
                    const imgUrl = 'https://panchorepuestos.cl/storage_original/imagenes/logo_pos.png'; 

                    // Cargar la imagen y agregarla al PDF
                    fetch(imgUrl)
                        .then(response => response.blob())
                        .then(blob => {
                            const reader = new FileReader();
                            reader.onloadend = function() {
                                const imgData = reader.result;

                                // 1. Agregar la imagen en la parte superior
                                const imgYPosition = 5;
                                doc.addImage(imgData, 'PNG', margin, imgYPosition, 30, 8); // Imagen de 30 mm de ancho y 8 mm de alto

                                // 2. Colocar el título solo una vez, debajo de la imagen
                                const titleYPosition = imgYPosition + 10; // Ajusta esta posición para que esté justo debajo de la imagen
                                doc.setFont("helvetica", "bold");
                                doc.setFontSize(5);
                                doc.text(`Ticket: ${numero_carrito}`, margin, titleYPosition); // Asegúrate de que esta línea esté solo una vez

                                // 3. Colocar encabezados de la tabla más abajo
                                const headerY = titleYPosition + 5; // Espacio extra para el título y encabezados
                                const headers = ["Item", "Código", "Cant", "Precio", "Total"];

                                // Dibujar encabezados de la tabla
                                headers.forEach((header, index) => {
                                    doc.text(header, margin + (index * 8), headerY);
                                });

                                // Línea horizontal debajo del encabezado
                                doc.line(margin, headerY + 1, 40 - margin, headerY + 1); // Línea para separar encabezados de contenido

                                let y = headerY + rowHeight;

                                // 4. Dibujar el contenido de la tabla más abajo
                                repuestos.forEach((repuesto, index) => {
                                    let total = repuesto.precio_venta * repuesto.cantidad;
                                    doc.text((index + 1).toString(), margin, y);
                                    doc.text(repuesto.codigo_interno, margin + 8, y);
                                    doc.text(repuesto.cantidad.toString(), margin + 16, y);
                                    doc.text(formatChileanPeso(repuesto.precio_venta), margin + 24, y); // Formato para precio unitario
                                    doc.text(formatChileanPeso(total), margin + 32, y); // Formato para total
                                    y += rowHeight;
                                });


                                // Generar el PDF como blob
                                const pdfBlob = doc.output('blob');

                                // Crear un objeto URL para el blob
                                const pdfUrl = URL.createObjectURL(pdfBlob);

                                // Configuración de la nueva pestaña
                                var config = "location=yes,height=570,width=720,scrollbars=yes,status=yes";
                                var w = window.open(pdfUrl, '_blank', config);
                                w.focus();
                            };
                            reader.readAsDataURL(blob);
                        })
                        .catch(error => {
                            console.error('Error al cargar la imagen:', error);
                            Swal.fire({
                                title: 'ERROR',
                                text: 'No se pudo cargar la imagen.',
                                icon: 'error',
                            });
                        });
                } else {
                    Swal.fire({
                        title: r.estado,
                        text: r.mensaje,
                        icon: 'error',
                    });
                }
            },
            error: function(error) {
                Swal.fire({
                    title: 'ERROR',
                    text: error.responseText,
                    icon: 'error',
                });
            }
        });
    }



    function borrar_carrito(cual) // Borra el carrito activo o guardados
  {
    var numero_carrito = $('#numero_carrito').val();
    var url="https://panchorepuestos.ddns.net/api/borrar_carrito_totem"+"/"+cual+"/"+numero_carrito;
      $.ajax({
       type:'GET',
       beforeSend: function () {
        //$("#carrito").html("Buscando Carrito...");
          },
       url:url,
       success:function(resp){
            if(resp == 'OK'){
                dame_carrito_();
                limpiar_busqueda();
            }
       },
        error: function(error){
        $('#carrito').html(formatear_error(error.responseText));
      }

      });
  }

    function formatChileanPeso(value) {
        return new Intl.NumberFormat('es-CL', {
            style: 'currency',
            currency: 'CLP',
            minimumFractionDigits: 0,
            maximumFractionDigits: 0
        }).format(value).replace('$', ''); // Elimina el símbolo de "$" si no lo quieres
    }

  function limpiar_busqueda(){
    $('#buscar_por_descripcion').val('');
    // $('#zona_grilla').empty();
    $('#buscar_por_descripcion').focus();
  }

  function buscar_por_modelo(idmodelo)
    {

      var url_buscar='https://panchorepuestos.ddns.net/api/damemodelo_por_marca_totem/'+idmodelo;

      $.ajax({
       type:'GET',
       beforeSend: function () {
        $('#container_marca_modelos').removeClass('d-block');
       $('#container_marca_modelos').addClass('d-none');
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
        $('#container_marca_modelos').removeClass('d-none');
        $('#container_marca_modelos').addClass('d-block');
                        $('#mensaje').empty();
                      if(repuestos[0].length == 0){
                        $('#container_marca_modelos').empty();
                        $('#container_marca_modelos').addClass('text-center');
                     	$('#container_marca_modelos').html(' <p>Lo sentimos, no hemos encontrado coincidencias.</p><img src="{{asset("storage_original/imagenes/sinresultados.jpg")}}" class="image_transbank" />');
                      }else{
                        let container = $('#pagination_marca_modelos');
                        container.pagination({
                            dataSource: repuestos[0],
                            pageSize: 4,
                            
                            afterRender: function() {
                                $("html, body").animate({ scrollTop: 0 }, "slow");
                                return false;
                            },
                            callback: function (data, pagination) {
                              
                              var dataHtml = "<div class='row'>";

                                $.each(data, function (index, item) {
                                    let precio_format = Number(item.precio_venta).toFixed(0);
                                    if(item.stock_actual >= 10){
                                            var stock_ = 'Disponible';
                                        }else{
                                            var stock_ = item.stock_actual;
                                        }
                                    dataHtml += `
                                    <div class="col-xl-4 col-md-6 col-sm-6 mb-3">
                                    <div class="card" style="background-image: url('https://t3.ftcdn.net/jpg/00/98/52/26/360_F_98522695_S9vAeY8a3O4AYFUDr2WVlk4eCWrqf7hx.jpg'); background-size: cover; overflow: hidden;">
                                    <a href="javascript:void(0)" class="a_titulo_producto"> <img class="card-img-top repuestos_image_catalogo"  src="https://panchorepuestos.ddns.net/storage/`+item.urlfoto+`" alt="Card image cap"></a>
                                    <div class="card-body">
                                        <h6 class="card-title">`+item.descripcion+`</h6>
                                        <p class="text-secondary">`+item.nombrefamilia+` </p>
                                        <p class="text-secondary">`+item.marcarepuesto+` </p>
                                        <p class="card-text text-center card-price letra_grande">$ `+commaSeparateNumber(precio_format)+`</p>
                                        <p class="fst-italic">Stk. `+stock_+`</p>
                                        
                                        <button class="btn btn-warning w-100 mt-3" onclick="vista_rapida(`+item.id+`)" ><i class="fa-solid fa-eye icon_header"></i>Vista rápida </button>
                                    </div>
                                    <div class="card-footer d-flex">
                                            <input type="number" class="form-control form-control-sm" name="cantidad-${item.id}" id="cantidad-${item.id}">
                                            <button class="btn btn-outline-success btn-sm" onclick="agregar_repuesto(${item.id})">+</button>
                                        </div>
                                    </div>
                                    </div>
                                    `;
                                   
                                });

                                dataHtml += '</div>';

                                $("#container_marca_modelos").html(dataHtml);
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

    function abrir_modal_marcas(){
        $('#modalMarcas').modal('show');
    }

    window.onload = function(){
        dame_carrito_();
        dameofertas();
        dame_marcas_select();
        $('.slider').slick({
            slidesToShow: 4,
            slidesToScroll: 4,
            autoplay: true, // Habilita el autoplay
            autoplaySpeed: 2000, // Establece la velocidad de avance en milisegundos (ejemplo: 2 segundos)
            responsive: [
                {
                    breakpoint: 845, // Tamaño de pantalla en el que se aplicarán las siguientes opciones
                    settings: {
                    slidesToShow: 3,
                    slidesToScroll: 3
                    // Otros ajustes específicos para pantallas más pequeñas
                    }
                },
                {
                    breakpoint: 768, // Tamaño de pantalla en el que se aplicarán las siguientes opciones
                    settings: {
                    slidesToShow: 2,
                    slidesToScroll: 2
                    // Otros ajustes específicos para pantallas más pequeñas
                    }
                },
                {
                    breakpoint: 480, // Tamaño de pantalla en el que se aplicarán las siguientes opciones
                    settings: {
                    slidesToShow: 1,
                    slidesToScroll: 1
                    // Otros ajustes específicos para pantallas aún más pequeñas
                    }
                }
            ],
            prevArrow: '<button class="d-none slick-prev my-2 bg-danger text-warning rounded" aria-label="Anterior"><i class="fa-solid fa-arrow-left"></i></button>',
            nextArrow: '<button class="d-none slick-next my-2 bg-danger text-warning rounded" aria-label="Siguiente"><i class="fa-solid fa-arrow-right"></i></button>'
        });
        // Evita que el modal full screen se cierre al abrir el segundo modal
        document.getElementById('modalVistaRapida').addEventListener('show.bs.modal', function () {
            document.getElementById('modalMarcas').classList.add('pe-none'); // Evitar interacción con el fondo
        });

        document.getElementById('modalVistaRapida').addEventListener('hidden.bs.modal', function () {
            document.getElementById('modalMarcas').classList.remove('pe-none'); // Restablecer interacciones con el fondo
        });

        // que el elemento a que esta dentro del div con clase buscador_box este disabled
        $('.buscador_box a').addClass('disabled');
    }

        
</script>

@endsection

@section('style')
<style>
    #grilla{
        font-size: 13px;
    }
    #modalVistaRapida .modal-dialog {
        z-index: 1056 !important; /* Más alto que el modal fullscreen */
    }

    #modalMarcas.modal-fullscreen {
        background-color: rgba(0, 0, 0, 0.8); /* Fondo más oscuro para dar contraste */
    }
    #container_busqueda .titulo_ofertas{
        background: #eee;
        border-radius: 10px;
        margin-bottom: 10px;
        padding: 20px;
        font-family: sans-serif;
        font-weight: bold;
    }
    #mensajes {
            background-color: #fff;
            color: #000 !important;
            position: relative;
            bottom: 22px;
            left: 0px;
            width: 100%;
            height: 60px;
            color: white;
            margin-top: 40px;
        }

        #titulo_bienvenida {

            margin-top: 10px;
            overflow: hidden;
            animation: example 5s infinite;
        }

        .imagen {
            height: 560px;
            width: 100%;
            margin-top: 10px;
            border-radius: 50px;
        }

        .contenedor_imagen {
            overflow: hidden;
            border-radius: 50px;
        }

        @keyframes example {
            0% {
                background-color: black;
                color: white;
            }

            25% {
                background-color: #e79d6e;
                color: #000;
            }

            50% {
                background-color: black;
                color: white;
                border-radius: 10px;
            }

            75% {
                background: #e79d6e;
                color: #000
            }

            100% {
                background-color: #000;
                color: white;
            }
        }

        .logoInicio{
            width: 85px;
            height: 85px;
            border-radius: 100px;
            -webkit-box-shadow: 5px 5px 15px 5px #000000; 
            box-shadow: 5px 5px 15px 5px #000000;
        }

        .contenedor_titulo_home{
            border: 1px solid gray;
            padding: 10px;
            animation: slidein 8s infinite;
            border-radius: 10px;
        }
        
        @keyframes slidein {
            0% {
            transform: translateX(0%);
            }
        
            50% {
            transform: translateX(300%);
            }

            75%{
                transform: translateX(-300%);
            }

            100%{
                transform: translateX(0%);
            }
        }
</style>
@endsection

@section('content')

      <div class="modal-content">
        <div class="modal-header modal-header-80 mb-3"> <!-- CABECERA -->
         <div class="row w-100" >
             <div class="col-12 d-flex justify-content-start">
                <button class="btn btn-sm mr-2" style="background: #d31718; color: white;" data-bs-target="#modalMarcas" data-bs-toggle="modal"><i class="fas fa-search"></i> Marcas</button>
                {{-- <p class="d-flex justify-content-center" id="buscar-repuesto-modal-titulo" style="color:white;font-weight:bold">BUSCAR REPUESTO</p> --}}
            </div>
         </div>
         <div class="row" style="width:90%">
             <div class="col-2 alin-der" style="padding-right:2px;color:white">Buscar:</div>
             <div class="col-10" style="margin-bottom:5px;padding-left:2px">
                 <input type="search" class="form-control" style="height:25px;" name="txtDescripcion" id="buscar_por_descripcion" placeholder="¿Qué desea encontrar?" onkeyup="enter_press(event,'d')" onclick="clic_en(1)" >
             </div>
         </div>
         <div class="row" style="width: 20%;">
            <button type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasExample" aria-controls="offcanvasExample" class="btn  ml-3" style="background: #877771; color: white;"><i class="fa-solid fa-cart-shopping icon_header"></i> <span id="cantidad_items_carro_header"></span></button>
            <div class="offcanvas offcanvas-start" tabindex="-1" id="offcanvasExample" aria-labelledby="offcanvasExampleLabel">
                <div class="offcanvas-header">
                    <img src="{{asset('storage_original/imagenes/logo_pos.png')}}" class="card-img-top" alt="...">
                  <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                </div>
                <div class="offcanvas-body">
                    <div class="card" >                          
                        <div class="card-body">
                            
                          <div id="container_carrito"></div>
                  
                        </div>
                      </div>
                    
                </div>
              </div> 
        </div>
        </div> <!-- FIN CABECERA -->

        <div class="modal-body modal-body-alto" style="padding-top: 0px;"> <!-- CONTENIDO -->
            @include('fragm.slider')
            <div style="overflow: hidden" class="my-3">
                <div class="d-flex justify-content-between p-2" style="background: #670909; 
                border-radius: 10px;">
                    <img src="https://www.shutterstock.com/image-vector/wrench-gear-vector-icon-lineal-260nw-2402205291.jpg" alt="ola" class="logoInicio">
                    <div style="width: 300px;">
                        <div class="contenedor_titulo_home">
                            <h1 class="text-center text-uppercase text-white"><span>Pancho</span> <span >Repuestos</span></h1>
                            <p class="text-center font-weight-bold font-italic" style="color: #fbf513; background: black; padding: 10px; border-radius: 10px; font-style:italic;">REPUESTOS DE CALIDAD</p>
                        </div>
                    </div>
        
                    <img src="https://w7.pngwing.com/pngs/428/470/png-transparent-spare-part-computer-icons-aftersales-blue-text-logo.png" alt="ola" class="logoInicio">
                </div>
            </div>
            @include('fragm.buscador_totem')
            @include('fragm.galeria')
         <div class="row w-100 mt-5">
         <!-- Columna 1 : elegir -->
                <!-- FIN Columna 1 : elegir -->

         <!-- Columna 2 : grilla y detalles -->
                 <div class="col-sm-12"  style="font-size: 13px;">
                    <div id="container_busqueda" class="text-center container">
                        
                    </div>
                    <div id="pagination_busqueda" class="text-center container"></div>
                 </div>
          </div> <!-- FIN de row principal -->
          {{-- @include('fragm.banner2') --}}
          {{-- <div class="row w-100 mt-5">
            <div class="col-12">
                <a href="javascript:void(0)" onclick="abrir_modal_marcas()">
                    <img src="{{ asset('storage_original/imagenes/oferta_totem.jpeg') }}" alt="oferta" class="w-100">
                </a>
                
            </div>
          </div> --}}
       </div> <!-- FIN DE modal-body CONTENIDO -->

        <div class="modal-footer" style="flex-direction:column; align-content: start; "> <!-- PIE -->
             <div class="row" style="width:90%">
                 <div class="col-sm-8 col-offset-4" id="mensajes-modal"></div>
             </div>
        </div> <!-- FIN MODAL-FOOTER -->

         </div> <!-- modal-content -->

{{-- @include('fragm.buscador') --}}
{{-- @include('fragm.banner4')

@include('fragm.banner')
@include('fragm.galeria')

@include('fragm.ofertas')

@include('fragm.banner2')
@include('fragm.banner3') --}}

<div class="modal fade" id="modalVistaRapida" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
      <div class="modal-content" style="z-index: 1056;">
        <div class="modal-header" style="background: #000; color: white;">
            <img src="https://panchorepuestos.cl/storage_original/imagenes/logo_negro_200.jpeg" alt="" class="logo_header rounded">
          <h5 class="modal-title mx-auto" id="vista_rapida_titulo" ></h5>
          
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body d-none" id="modalbody_vistarapida">
            <div class="row">
                <div class="col-lg-12 col-md-12">
                    <div id="carouselExampleControls" class="carousel slide" data-bs-ride="carousel">
                        <div class="carousel-inner" id="carousel-inner">
                          
                        </div>
                        <button class="carousel-control-prev" style="background: #eed;" type="button" data-bs-target="#carouselExampleControls" data-bs-slide="prev">
                          <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                          <span class="visually-hidden">Anterior</span>
                        </button>
                        <button class="carousel-control-next" style="background: #eed;" type="button" data-bs-target="#carouselExampleControls" data-bs-slide="next">
                          <span class="carousel-control-next-icon" aria-hidden="true"></span>
                          <span class="visually-hidden">Siguiente</span>
                        </button>
                    </div>
                      <hr>
                      <p style="    font-size: 20px;
                      text-align: center;">Imagenes</p>
                      <div class="row w-100" id="preview_photos" style="display: flex;
                      justify-content: space-around;">
                        
                      </div>
                </div>
                
                <div class="col-lg-12 col-md-12">
                    <table class="table">
                        
                        <tbody>
                            <tr>
                                <td>Código Interno</td>
                                <th scope="row" class="letra_pequeña" id="codigo_interno_repuesto_"></th>
                              </tr>
                            <tr>
                                <td>Familia</td>
                                <th scope="row" id="familia_repuesto"></th>
                              </tr>
                          <tr>
                            <td>Marca</td>
                            <th scope="row" id="marca_repuesto_"></th>
                          </tr>
                            <tr>
                              <td scope="col">Precio</td>
                              <td id="precio"></td>
                            </tr>
                            <tr >
                                <td scope="col">Medidas</td>
                                <td id="medidas" style="font-size: 13px;"></td>
                            </tr>
                            <tr id="tr_rectificadores" class="d-none">
                                <td scope="col"col>Rectificadores</td>
                                <td id="rectificadores" style="font-size: 13px"></td>
                            </tr>
                            <tr id="tr_alternadores" class="d-none">
                                <td scope="col"col>Alternadores</td>
                                <td id="alternadores" style="font-size: 13px"></td>
                            </tr>
                            <tr>
                              <td scope="col">Cantidad</td>
                              <td style="display: flex;justify-content: space-between;">
                                <button class="btn btn-warning btn-sm" onclick="disminuir()">-</button>
                                <input type="text" class="text-center" name="cantidad" id="cantidad" min="0" value="1" disabled>
                                <button class="btn btn-warning btn-sm" onclick="aumentar()">+</button>
                              </td>
                            </tr>                   
                        </tbody>
                      </table>
                      {{-- <p style="    font-size: 20px;
                      text-align: center;">Compartir</p>
                      <hr>
                      <div id="botones_rrss" style="display: flex;
                      justify-content: space-around;">
                      
                        <a href="javascript:void(0)" target="_blank" id="enlace_facebook_compartir" class="btn btn-link btn-sm"><i class="fa-brands fa-facebook icon_header w-100" style="color: #1475e7;"></i></a>
                        <a href="javascript:void(0)" target="_blank" id="enlace_twitter_compartir" class="btn btn-link btn-sm"><i class="fa-brands fa-twitter icon_header w-100" style="color: #1d9bf0;"></i></a>
                        <a href="javascript:void(0)" target="_blank" id="enlace_email_compartir" class="btn btn-link btn-sm"><i class="fa-solid fa-envelope icon_header w-100" style="color: #0c7bd4;"></i></a>
                      </div> --}}
                      <hr>
                    <p style="    font-size: 20px;
                    text-align: center;">Aplicaciones</p>
                   
                    <table class="table">
                        <thead>
                          <tr>
                            <th scope="col">Marca</th>
                            <th scope="col">Modelo</th>
                            <th scope="col">Años</th>
                            <th scope="col"></th>
                          </tr>
                        </thead>
                        <tbody id="container_">
                          <div id="pagination_"></div>
                        </tbody>
                      </table>
                      <input type="hidden" name="stock_repuesto" id="stock_repuesto">
                      <input type="hidden" name="idrepuesto_modal" id="idrepuesto_modal">

                </div>
            </div>
            
        </div>
        <div class="modal-footer" style="background: #000; color: white;">
            <button class="btn btn-success btn-sm" onclick="agregar_carrito_()">Agregar al carro</button>
          <button type="button" class="btn btn-danger btn-sm" data-bs-dismiss="modal">Cerrar</button>
        </div>
      </div>
    </div>
</div>
<!-- Modal -->
<div class="modal fade" id="modalMensaje" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
            <img src="{{asset('storage_original/imagenes/logo_pos.png')}}" alt="" class="logo_header">
          <h5 class="modal-title ml-3" id="exampleModalLabel"></h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body text-center">
            <img src="{{asset('storage_original/imagenes/mensaje_navidad.jpeg')}}" alt="logo" class="w-100">
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-danger btn-sm" data-bs-dismiss="modal">Cerrar</button>
        </div>
      </div>
    </div>
</div>
<!-- Modal -->
<div class="modal fade" id="modalMarcas" tabindex="-1" aria-labelledby="modalMarcasLabel" aria-hidden="true">
    <div class="modal-dialog modal-fullscreen">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Marcas</h5>
          <div class="d-flex">
            <p>Usted lleva <span class="badge badge-warning mr-3" id="cantidad_items_modal"></span> Productos.</p>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          
        </div>
        <div class="modal-body">
            <div class="row w-100">
                <div class="col-md-3">
                    <div class="row w-100" id="contenedor_marcas"></div>
                </div>
                <div class="col-md-9">
                    <div id="container_marca_modelos" class="text-center"></div>
                    <div id="pagination_marca_modelos" class="text-center"></div>
                </div>
            </div>
          
            
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
        </div>
      </div>
    </div>
</div>

  <!-- DATOS DE VITAL IMPORTANCIA -->
<input type="hidden" id="idrepuesto_modal" value="">
<input type="hidden" name="numero_carrito" id="numero_carrito">
<input type="hidden" name="cantidad_items" id="cantidad_items" value="">
@endsection

