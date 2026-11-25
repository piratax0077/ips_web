<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <!-- CSRF Token -->
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>PanchoRepuestos</title>
        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">


        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.2/css/all.min.css" integrity="sha512-1sCRPdkRXhBV2PBLUdRb4tMg1w2YPf37qatUFeS7zlBy7jJI8Lf4VHwWfZZfpXtYSLy85pkm9GaYVYMfw5BC1A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
        <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <link href="{{ asset('dist/web.css') }}" rel="stylesheet">
        <script src="{{asset('dist/main.js')}}"></script>
        <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>

        <!-- Agrega el archivo CSS de Slick -->
        <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css">

        <!-- JS PDF -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

        <!-- Data AOS Css -->
        <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

        <!-- Agrega el archivo JavaScript de Slick -->
        <script src="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js"></script>

        @yield('javascript')
        <link rel="stylesheet" href="//code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
        <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>

        <!-- SweetAlert2 CSS -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@10.16.6/dist/sweetalert2.min.css">
        <!-- SweetAlert2 JS -->
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10.16.6/dist/sweetalert2.min.js"></script>

        <script src="https://cdnjs.cloudflare.com/ajax/libs/paginationjs/2.1.4/pagination.min.js"></script>

        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/paginationjs/2.1.4/pagination.css"/>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />

        <script src='{{asset('nprogress-master/nprogress.js')}}'></script>
        <link rel='stylesheet' href='{{asset('nprogress-master/nprogress.css')}}'/>

        <!-- Data AOS JS -->
        <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
        @yield('style')
    </head>
    <body>

        <div class="main">
            @yield('content')

        </div>
        {{-- @include('fragm.footer') --}}
        <script>

            $(document).ajaxStart(function () {
                NProgress.start();
            }).ajaxStop(function () {
                NProgress.done();
            });

            NProgress.configure({ easing: 'ease', speed: 500 });
            //NProgress.configure({ parent: '#contenedor' });

            function getUser(){
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
                      let url = window.location.href;
                      //Si la página es mis_datos, que te pase los valores del usuario a los campos del dom.
                      if(url =='https://panchorepuestos.cl/mis_datos'){

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
                        let user = resp.user;

                        document.getElementById('name').value = user.name;
                        document.getElementById('email').value = user.email;
                        document.getElementById('telefono').value = user.telefono;
                        document.getElementById('id_usuario').value = user.id;
                      }
                        if(typeof resp.user === 'undefined'){

                            $('#no_autenticado').css('display','flex');
                            $('#autenticado').css('display','none');
                            $('#contenedor_descuentos').css('display','none');
                            $('#contenedor_ofertas').css('display','none');
                        }else{

                            $('#no_autenticado').css('display','none');
                            $('#autenticado').css('display','flex');
                            // separar el user.name en un array con los espacio
                            let nombre = resp.user.name.split(' ');

                            $('#navbarDropdown').text(nombre[0]);
                            $('#contenedor_descuentos').css('display','block');
                            $('#contenedor_ofertas').css('display','block');
                        }
                    },
                    error: function(error){
                        console.log(error.responseText);
                    }
                });
            }

            function revisar_carrito(){

                var url = 'https://panchorepuestos.ddns.net/api/revisar_carrito';

                $.ajax({
                    type:'get',
                    url: url,
                    headers: {"Authorization": 'Bearer '+localStorage.getItem('token')},
                    beforeSend:function(){
                        $('#cantidad_items').empty();
                    },
                    success: function(cantidad){
                        if(cantidad.status == 'Token is Invalid'){
                            $('#cantidad_items').empty();
                        }else if(cantidad.status == 'Token is Expired'){

                            $('#cantidad_items').empty();

                        }else{

                            if(cantidad.repuestos.length > 0){
                                $('#cantidad_items').empty();
                                $('#cantidad_items').append(cantidad.repuestos.length);
                                if(window.location.href == 'https://panchorepuestos.cl/mis_datos'){
                                    $('#cantidad_items_misdatos').empty();

                                    if(cantidad.repuestos.length == 1) {
                                        $('#cantidad_items_misdatos').append('Tiene '+cantidad.repuestos.length+' item en el carrito.');
                                    }
                                    else {
                                        $('#cantidad_items_misdatos').append('Tiene '+cantidad.repuestos.length+' items en el carrito.');
                                    }
                                    $('#btn_pagar').html(`

                                    <img src="{{asset("storage_original/imagenes/WEBCHECKOUT_GETNET+ENDO_RGB_COLOR_W300.png")}}" alt="" class="image_transbank_carrito mt-5 mb-5">
                                    <a href="/carrito" class="btn btn-warning btn-sm w-100">Ir a pagar </a>
                                    `);
                                }

                            }else{
                                $('#cantidad_items').empty();
                                $('#cantidad_items').append();
                                if(window.location.href == 'https://panchorepuestos.cl/mis_datos'){
                                    $('#cantidad_items_misdatos').append('No tiene items en el carrito.');
                                }

                            }

                        }

                    },
                    error: function(error){
                        console.log(error.responseText);
                    }
                });
            }

            function enter_buscar(){
                var keycode = event.keyCode;
                if(keycode=='13')
                {
                    var valor = $('#tags').val();
                    if(valor == "" || valor.trim() == 0){
                        Swal.fire({
                            icon:'error',
                            text: 'Debe ingresar algo para buscar'
                        });
                    return false;
                    }else{
                        buscar(valor);
                    }

                }
            }

            function buscar(value){

                var valor = $('#tags').val();
                if(valor == "" || valor.trim() == 0){
                        Swal.fire({
                            icon:'error',
                            text: 'Debe ingresar algo para buscar'
                        });

                        return false;
                  }
                let url = '/'+valor+'/buscador';

                // Simulate a mouse click:
                window.location.href = url;
            }

            function aumentar(){
        let stock_maximo = $('#stock_repuesto').val();
        let cantidad = $('#cantidad').val();
        let precio_repuesto = $('#precio_repuesto').val();
        let precio_format = Number(precio_repuesto).toFixed(0);
        $('#precio_calculado').empty();

        if(parseInt(cantidad) < parseInt(stock_maximo)){
            let aumentado = parseInt(cantidad) + 1;
            let precio_calculado = aumentado * precio_repuesto;
            $('#cantidad').val(aumentado);
            $('#precio_calculado').append(`$ `+commaSeparateNumber(Number(precio_calculado).toFixed(0)));


        }else{
            $('#cantidad').val(stock_maximo);
            $('#precio_calculado').append(`$ `+commaSeparateNumber(Number(stock_maximo * precio_repuesto).toFixed(0)));
        }

        }

        function disminuir(){
            let cantidad = $('#cantidad').val();
            let precio_repuesto = $('#precio_repuesto').val();
            $('#precio_calculado').empty();
            if(cantidad > 1){
                let disminuido = parseInt(cantidad)- 1;
                let precio_calculado = disminuido * precio_repuesto;
                $('#cantidad').val(disminuido);
                $('#precio_calculado').append(`$ `+commaSeparateNumber(Number(precio_calculado).toFixed(0)));
            }else{
                $('#cantidad').val(1);
                $('#precio_calculado').append(`$ `+commaSeparateNumber(Number(precio_repuesto).toFixed(0)));
            }

        }

    // function vista_rapida(idrepuesto){
    //         let url = 'https://panchorepuestos.ddns.net/api/'+idrepuesto+'/repuesto';
    //         let token = localStorage.getItem('token');
    //         $.ajax({
    //             type:'get',
    //             url: url,
    //             beforeSend: function(){
    //                 $('#stock_repuesto').append(1);
    //                 $('#modalbody_vistarapida').removeClass('d-block');
    //                 $('#modalbody_vistarapida').addClass('d-none');
    //                 $('#cantidad').val(1);
    //             },
    //             headers: {"Authorization": 'Bearer '+token},
    //             success: function(data){
    //                let rectificadores = data[7];
    //                let alternadores = data[8];

    //                 $('#id_repuesto_detalle').attr('href','/repuesto/'+idrepuesto);

    //                 $('#enlace_facebook_compartir').attr('href','http://www.facebook.com/sharer.php?u=https://panchorepuestos.cl/repuesto/'+idrepuesto);
    //                 $('#enlace_twitter_compartir').attr('href','/repuesto/'+idrepuesto);
    //                 $('#enlace_email_compartir').attr('href','/repuesto/'+idrepuesto);
    //                 let flag = data[4];
    //                 $('#modalbody_vistarapida').removeClass('d-none');
    //                 $('#modalbody_vistarapida').addClass('d-block');
    //                 let repuesto = data[0];
    //                 $('#idrepuesto_modal').val(repuesto.id);

    //                 $('#vista_rapida_titulo').empty();
    //                 $('#vista_rapida_titulo').append(repuesto.descripcion);
    //                 // crear un array con las medidas separadas por una ,
    //                 let medidas = repuesto.medidas.split(',');
    //                 $('#medidas').empty();
    //                 medidas.forEach(medida => {
    //                     $('#medidas').append(medida+'<br/>');
    //                 });

    //                 if(rectificadores.length > 0){
    //                     // le quitamos la clase d-none y le asignamos la clase d-blocl para que se muestre
    //                     $('#tr_rectificadores').removeClass('d-none');

    //                     $('#rectificadores').empty();
    //                     rectificadores.forEach(rectificador => {
    //                         $('#rectificadores').append(rectificador['rectificador']+"</br>");
    //                     });
    //                 }else{
    //                     $('#tr_rectificadores').addClass('d-none');
    //                 }

    //                 if(alternadores.length > 0){
    //                     // le quitamos la clase d-none y le asignamos la clase d-blocl para que se muestre
    //                     $('#tr_alternadores').removeClass('d-none');
    //                     $('#alternadores').empty();
    //                     alternadores.forEach(alternador => {
    //                         $('#alternadores').append(alternador['alternador']+"</br>");
    //                     });

    //                 }else{
    //                     $('#tr_alternadores').addClass('d-none');
    //                 }

    //                 let fotos = data[1];
    //                 let similares = data[2];
    //                 let precio_format = Number(repuesto.precio_venta).toFixed(0);
    //                 precio_total = precio_format;
    //                 $('#marca_repuesto_').empty();
    //                 $('#precio').empty();
    //                 $('#familia_repuesto').empty();
    //                 $('#codigo_interno_repuesto_').empty();
    //                 $('#stock_repuesto').val(repuesto.stock_actual);
    //                 $('#familia_repuesto').append(repuesto.nombrefamilia);
    //                 $('#codigo_interno_repuesto_').append(repuesto.codigo_interno);
    //                 $('#marca_repuesto_').append(repuesto.marcarepuesto);
    //                 //Si flag viene nulo, el repuesto no tiene ofertas
    //                 if(flag == null || flag == 0){
    //                     $('#precio').append('<strong>$ '+commaSeparateNumber(precio_format)+'</strong>');
    //                 }else{
    //                     $('#precio').append('<strong>$ '+commaSeparateNumber(flag.precio_actualizado)+' </strong>');

    //                 }

    //                 if(fotos.length > 0){
    //                     $('#carousel-inner').empty();
    //                     $('#preview_photos').empty();
    //                     $('#carousel-inner').append(`
    //                     <div class="carousel-item active">
    //                         <img class="d-block w-100 fotos_repuesto zoom" src="https://panchorepuestos.ddns.net/storage/`+fotos[0].urlfoto+`" alt="First slide">
    //                         </div>
    //                     `);
    //                     fotos.forEach(foto => {
    //                         $('#carousel-inner').append(`
    //                             <div class="carousel-item">
    //                                 <img src="https://panchorepuestos.ddns.net/storage/`+foto.urlfoto+`" class="d-block w-100 fotos_repuesto zoom" alt="`+repuesto.descripcion+`">
    //                             </div>
    //                         `);
    //                         $('#preview_photos').append(`
    //                         <div class="col-3 col-md-3" >
    //                             <img src="https://panchorepuestos.ddns.net/storage/`+foto.urlfoto+`" class="d-block w-100" style="width: 100px;" alt="`+repuesto.descripcion+`">
    //                         </div>
    //                         `);
    //                     });
    //                 }
    //             else{
    //             $('#carousel-inner').empty();
    //             $('#carousel-inner').append(`
    //             <div class="carousel-item active">
    //                 <img class="d-block w-100 fotos_repuesto" src="https://panchorepuestos.ddns.net/storage/fotozzz/d42e48078f97443083b70b04a710b31b.png" alt="First slide">
    //                 </div>
    //             `);
    //             fotos.forEach(foto => {
    //                 $('#carousel-inner').append(`
    //                     <div class="carousel-item">
    //                         <img src="https://panchorepuestos.ddns.net/storage/fotozzz/d42e48078f97443083b70b04a710b31b.png" class="d-block w-100 fotos_repuesto zoom" alt="`+repuesto.descripcion+`">
    //                     </div>
    //                 `);
    //             });


    //             }

    //             let container = $('#pagination_');
    //                         container.pagination({
    //                             dataSource: similares,
    //                             pageSize: 3,
    //                             callback: function (data, pagination) {
    //                                 var dataHtml = "<div>";

    //                                 $.each(data, function (index, similar) {

    //                                     dataHtml += `
    //                                     <tr>
    //                                     <td><img class="foto_marca" src="https://panchorepuestos.ddns.net/storage/`+similar.urlfoto+`" /> </td>
    //                                     <td style="font-size: 15px;">`+similar.modelonombre+` </td>
    //                                     <td style="font-size: 15px;">`+similar.anios_vehiculo+` </td>
    //                                     <td></td>
    //                                     </tr>
    //                                     `;

    //                                 });

    //                                 dataHtml += '</div>';
    //                                 dataHtml += `

    //                                 `;

    //                                 $("#container_").html(dataHtml);
    //                             }
    //                         });
    //             },
    //             error: function(error){
    //                 console.log(error.responseText);
    //             }
    //     });
    // }

    function agregar_carrito_(){
        let id_repuesto = $('#idrepuesto_modal').val();
        let cantidad = $('#cantidad').val();
        let token = localStorage.getItem('token');

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
                                text: 'Repuesto ya existe en el carrito!!',
                                icon: "error"
                            });
                $('#modalVistaRapida').modal('hide');

            }
        });



    }

    function aumentar(){
        let stock_maximo = $('#stock_repuesto').val();
        let cantidad = $('#cantidad').val();
        let precio_repuesto = $('#precio_repuesto').val();
        let precio_format = Number(precio_repuesto).toFixed(0);
        $('#precio_calculado').empty();

        if(parseInt(cantidad) < parseInt(stock_maximo)){
            let aumentado = parseInt(cantidad) + 1;
            let precio_calculado = aumentado * precio_repuesto;
            $('#cantidad').val(aumentado);
            $('#precio_calculado').append(`$ `+commaSeparateNumber(Number(precio_calculado).toFixed(0)));


        }else{
            $('#cantidad').val(stock_maximo);
            $('#precio_calculado').append(`$ `+commaSeparateNumber(Number(stock_maximo * precio_repuesto).toFixed(0)));
        }

        }

        function disminuir(){
            let cantidad = $('#cantidad').val();
            let precio_repuesto = $('#precio_repuesto').val();
            $('#precio_calculado').empty();
            if(cantidad > 1){
                let disminuido = parseInt(cantidad)- 1;
                let precio_calculado = disminuido * precio_repuesto;
                $('#cantidad').val(disminuido);
                $('#precio_calculado').append(`$ `+commaSeparateNumber(Number(precio_calculado).toFixed(0)));
            }else{
                $('#cantidad').val(1);
                $('#precio_calculado').append(`$ `+commaSeparateNumber(Number(precio_repuesto).toFixed(0)));
            }

        }

            function logout(){
                localStorage.clear();
                let url = '/';

                // Simulate a mouse click:
                window.location.href = url;
            }

    function login(){
    let email = $('#email_').val();
    let password = $('#password_').val();

    let url = 'https://panchorepuestos.ddns.net/api/login';

    if(email == '' || password == ''){
      return false;
    }

      $.ajaxSetup({
             headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                 }
             });

             let vars = {email: email, password: password};

            $.ajax({
                type:'post',
                url: url,
                data: vars,
                success: function(resp){
                    localStorage.setItem('token', resp.token);
                    // Simulate a mouse click:
                    window.location.href = "/";
                },
                error: function(error){

                    let msg = JSON.parse(error.responseText);

                    Swal.fire({
                      icon:'error',
                      text: msg.message
                    })
                }
            });
  }
            getUser();
            revisar_carrito();
        </script>
        <!-- Modal -->
<div class="modal fade" id="modalVistaRapida" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
      <div class="modal-content">
        <div class="modal-header" style="background: #000; color: white;">
            <img src="{{asset('storage_original/imagenes/logo_negro_200.jpeg')}}" alt="" class="logo_header rounded">
          <h5 class="modal-title mx-auto" id="vista_rapida_titulo" ></h5>

          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body d-none" id="modalbody_vistarapida">
            <div class="row">
                <div class="col-md-6">
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

                <div class="col-md-6">
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
                                <a href="#" id="id_repuesto_detalle" class="fst-italic ml-5" style="font-size: 15px;">Ver detalle</a>
                              </td>
                            </tr>
                        </tbody>
                      </table>
                      <p style="    font-size: 20px;
                      text-align: center;">Compartir</p>
                      <hr>
                      <div id="botones_rrss" style="display: flex;
                      justify-content: space-around;">

                        <a href="javascript:void(0)" target="_blank" id="enlace_facebook_compartir" class="btn btn-link btn-sm"><i class="fa-brands fa-facebook icon_header w-100" style="color: #1475e7;"></i></a>
                        <a href="javascript:void(0)" target="_blank" id="enlace_twitter_compartir" class="btn btn-link btn-sm"><i class="fa-brands fa-twitter icon_header w-100" style="color: #1d9bf0;"></i></a>
                        <a href="javascript:void(0)" target="_blank" id="enlace_email_compartir" class="btn btn-link btn-sm"><i class="fa-solid fa-envelope icon_header w-100" style="color: #0c7bd4;"></i></a>
                      </div>
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

  <!-- MODAL Login -->
  <div class="modal fade" id="modalLogin" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
            <img src="{{asset('storage_original/imagenes/logo_negro_200.jpeg')}}" alt="" class="logo_header rounded">
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <div class="formulario">

                <form method="POST" >
                  @csrf

                  <!-- Email input -->
                  <div  class="d-flex p-2 my-3 bd-highlight">
                    <i class="fa-solid fa-user icon_header m-1 p-2 border border-1 rounded bg-warning text-dark"></i>
                      <input id="email_" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>

                      @error('email')
                          <span class="invalid-feedback" role="alert">
                              <strong>{{ $message }}</strong>
                          </span>
                      @enderror
                  </div>

                  <!-- Password input -->
                  <div class="d-flex p-2 my-3 bd-highlight">
                    <i class="fa-solid fa-key icon_header m-1 p-2 border border-1 rounded bg-warning text-dark"></i>
                      <input id="password_" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">

                      @error('password')
                          <span class="invalid-feedback" role="alert">
                              <strong>{{ $message }}</strong>
                          </span>
                      @enderror
                  </div>

                  <div class="d-flex justify-content-between align-items-center">
                    <!-- Checkbox -->
                    <div class="form-check">
                      <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>

                      <label class="form-check-label" for="remember">
                          {{ __('Recordarme') }}
                      </label>
                  </div>
                    @if (Route::has('password.request'))
                                      <a class="btn btn-link" href="{{ route('password.request') }}">
                                          {{ __('¿Olvidaste tu contraseña?') }}
                                      </a>
                    @endif
                  </div>

                  <div class="text-center text-lg-start mt-4 pt-2">
                      <button type="button" class="btn btn-dark btn-lg w-100" onclick="login()" style="padding-left: 2.5rem; padding-right: 2.5rem;">
                          {{ __('Login') }}
                      </button>
                  </div>

                </form>
              </div>
        </div>
        <div class="modal-footer">
            <figcaption class="blockquote-footer">
                SysMaFra 2023&copy; <cite title="Source Title">Todos los derechos reservados.</cite>
              </figcaption>
            <p class="text-center"></p>
        </div>
      </div>
    </div>
  </div>


  <!-- Modal Lateral Carrito -->

  <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasRight" aria-labelledby="offcanvasRightLabel">
    <div class="offcanvas-header">
        <img src="https://panchorepuestos.ddns.net/storage/imagenes/logo_pos.png" alt="" class="logo_header">
      <h5 id="offcanvasRightLabel">Carrito Compras</h5>
      <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>

    </div>
    <div class="offcanvas-body">
      <section id="contenido_carrito">
        <div id="container_carrito"></div>
        <div id="pagination_carrito"></div>
    </section>
    </div>
  </div>

<!-- FIN Modal Lateral Carrito -->
        {{-- <a href="https://api.whatsapp.com/send?phone=56990980457" class="btn_contacto_whatsapp" style="text-decoration: none;" title="Contactanos via whastapp para cualquier duda o consulta."><span class="badge bg-light text-dark">Cotiza con nosotros</span><img src="{{asset("storage_original/imagenes/wzap2.jpeg")}}" alt="wzap" style="width: 80px; border-radius: 100px;" class="boton_whastapp"></a> --}}

        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous"></script>
    <!-- DATO DE VITAL IMPORTANCIA -->
    <input type="hidden" id="id_usuario" value="">
    <script>
        AOS.init({
            duration: 650,
            once: true
        });
      </script>
    </body>
</html>
