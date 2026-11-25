
@extends('layouts.app')

@section('styles')
<link href="{{ asset('css/home-prueba.css') }}" rel="stylesheet">
@endsection

@section('javascript')
<script>
      $( function() {
        $( "#accordion" ).accordion();

    } );

  function dame_marcas(){
        $.ajax({
            type: 'get',
            url: 'https://intranet.ipschile.cl/api/marcas-repuestos-catalogo',
            beforeSend: function(){
                $('#marcas_select').prop('disabled', true).html('<option value="">Cargando marcas...</option>');
            },
            success: function(marcas){
                $('#marcas_select').empty().append('<option value="">Todas las marcas de productos</option>');
                marcas.forEach(function(marca) {
                    $('#marcas_select').append(new Option(marca.marcarepuesto, marca.marcarepuesto));
                });
                $('#marcas_select').prop('disabled', false);
            },
            error: function(error){
                $('#marcas_select').html('<option value="">No fue posible cargar las marcas</option>');
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
                        $('#familias_select').append("<option value='0'>Categorías </option>");
                        familias.forEach(familia => $('#familias_select').append(`<option value=`+familia.id+` class="text-uppercase">`+familia.nombrefamilia+` </option>`));


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

        function busqueda_principal(){
            let marca = $('#marcas_select').val();

            if(!marca){
                Swal.fire({
                    title: "Selecciona una marca",
                    text: "Elige una marca de productos para explorar el catálogo.",
                    icon: "info"
                });
                return false;
            }else{
                window.location.href = "/"+encodeURIComponent(marca)+"/buscador";
            }

        }

        function buscar_main(value){

                var valor = $('#tag_principal').val();
                if(valor === '' || valor.trim() == 0){
                    Swal.fire({
                    title: "Error",
                    text: "Debe ingresar una descripción",
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
                let url = 'https://intranet.ipschile.cl/api/get-user';

                $.ajax({
                    type:'post',
                    url: url,
                    headers: {"Authorization": 'Bearer '+localStorage.getItem('token')},
                    beforeSend: function(){
                        $('#no_autenticado').css('display','none');
                        $('#autenticado').css('display','none');
                    },
                    success: function(resp){
                        console.log(resp);
                      if(resp.user){
                        return true;
                      } else return false;
                    },
                    error: function(error){
                        console.log(error.responseText);
                    }
                });
            }

        function dameofertas(){

            $.ajax({
                    type:'get',
                    url: 'https://panchorepuestos.ddns.net/api/dameofertas',
                    beforeSend: function(){
                        $('#ofertas').empty();
                        $('#ofertas').append('<div class="text-center"> CARGANDO ... </div>');
                    },
                    success: function(resp){
                       let ofertas = resp[0];
                       let descuentos = resp[1];

                        $('#ofertas').empty();
                        $('#descuentos').empty();
                        if(ofertas.length > 0){
                            ofertas.forEach(oferta => {
                                let precio_format = Number(oferta.precio_venta).toFixed(0);
                                $('#ofertas').append(`
                                <div class="col-lg-4 col-md-6 my-2 text-center card_oferta">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <a href="/repuesto/`+oferta.id+`" class="a_titulo_producto"> <img class="card-img-top" style="height: 220px; width: 100%;"  src="https://panchorepuestos.ddns.net/storage/`+oferta.urlfoto+`" alt="Card image cap"> </a>
                                        </div>
                                        <div class="col-md-6">
                                            <p style="height: 100px;"><a href="/repuesto/`+oferta.id+`" class="a_titulo_producto"> `+oferta.descripcion+`</a></p>
                                            <p class="text-secondary">`+oferta.marcarepuesto.marcarepuesto+` </p>

                                            <p><span class="card-price letra_grande">$ `+commaSeparateNumber(oferta.precio_actualizado)+`</span> </p>
                                        </div>
                                    </div>


                                </div>
                                `);
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

        function dame_mensaje(){
            $('#modalMensaje').modal("show");
        }

        dame_marcas();
        //damefamilias();
        dameofertas();
        window.onload = function(){
            //dame_mensaje();
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
            prevArrow: '<button class="slick-prev my-2 bg-danger text-warning rounded" aria-label="Anterior"><i class="fa-solid fa-arrow-left"></i></button>',
            nextArrow: '<button class="slick-next my-2 bg-danger text-warning rounded" aria-label="Siguiente"><i class="fa-solid fa-arrow-right"></i></button>'
        });
        // dame_mensaje();
        }


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



</script>

@endsection

@section('content')
@include('fragm.home_prueba')
@if(false)
@include('fragm.slider')
 <!-- Services Section Begin -->
    <section class="services spad">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="section-title">
                        <span>Nuestros servicios</span>
                        <h2>¿Qué ofrecemos?</h2>
                        <p>En IPS Chile somos expertos en la venta de accesorios para vehículos, ofreciendo productos de alta calidad para que tu auto siempre esté en las mejores condiciones. En IPS Chile nos comprometemos con la calidad, el buen servicio y la satisfacción de nuestros clientes.</p>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-6 col-md-6 col-sm-6">
                    <div class="services__item">
                        <img src="{{ asset('assets/images/services-1.png') }}" alt="">
                        <h5>Sigue tu pedido</h5>
                        <p>¿Ya realizaste una compra y quieres saber el estado de tu pedido? Ahora puedes rastrear el progreso de tu compra en tiempo real directamente en nuestro sitio web. Solo necesitas ingresar a tu cuenta y revisar la sección mis solicitudes.</p>
                        <a href="/solicitudes"><i class="fa fa-long-arrow-right"></i></a>
                    </div>
                </div>
                {{-- <div class="col-lg-4 col-md-6 col-sm-6">
                    <div class="services__item">
                        <img src="{{ asset('assets/images/services-2.png') }}" alt="">
                        <h5>Tiendas</h5>
                        <p>Consectetur adipiscing elit incididunt ut labore et dolore magna aliqua. Risus commodo
                            viverra maecenas.</p>
                        <a href="#"><i class="fa fa-long-arrow-right"></i></a>
                    </div>
                </div> --}}
                <div class="col-lg-6 col-md-6 col-sm-6">
                    <div class="services__item">
                        <img src="{{ asset('assets/images/services-3.png') }}" alt="">
                        <h5>Contactanos</h5>
                        <p>¿Tienes alguna pregunta, comentario o necesitas asistencia con tu compra? Estamos aquí para ayudarte.</p>
                        <a href="/quienes-somos"><i class="fa fa-long-arrow-right"></i></a>
                    </div>
                </div>
                {{-- <div class="col-lg-3 col-md-6 col-sm-6">
                    <div class="services__item">
                        <img src="img/services/services-4.png" alt="">
                        <h5>Support 24/7</h5>
                        <p>Consectetur adipiscing elit incididunt ut labore et dolore magna aliqua. Risus commodo
                            viverra maecenas.</p>
                        <a href="#"><i class="fa fa-long-arrow-right"></i></a>
                    </div>
                </div> --}}
            </div>
        </div>
    </section>

    <!-- Chooseus Section Begin -->
    <section class="chooseus spad">
        <div class="container">
            <div class="row">
                <div class="col-lg-5">
                    <div class="chooseus__text">
                        <div class="section-title">
                            <h2>¿Porqué la gente nos prefiere?</h2>
                            <p>En IPS Chile nos mueve el impulso de mantener cada vehículo en su mejor versión.</p>
                        </div>
                        <ul>
                            <li><i class="fa fa-check-circle"></i> Productos seleccionados para asegurar calidad, durabilidad y compatibilidad real.</li>
                            {{-- <li><i class="fa fa-check-circle"></i> Precios justos y ofertas constantes para mantener tu vehículo en su mejor estado.
                            </li> --}}
                            <li><i class="fa fa-check-circle"></i> Envíos rápidos y seguimiento fácil para que sepas siempre dónde va tu pedido.
                            </li>
                            <li><i class="fa fa-check-circle"></i> Atención personalizada por expertos que conocen el mundo automotriz.</li>
                        </ul>
                        <a href="/quienes-somos" class="primary-btn">Sobre Nosotros</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="chooseus__video set-bg">
            <img src="img/chooseus-video.png" alt="">
            <a href="https://www.youtube.com/watch?v=Xd0Ok-MkqoE"
                class="play-btn video-popup"><i class="fa fa-play"></i></a>
        </div>
    </section>
    <!-- Services Section End -->
{{-- @include('fragm.buscador') --}}
{{-- @include('fragm.banner4') --}}

{{-- @include('fragm.banner') --}}
{{-- @include('fragm.galeria') --}}

{{-- @include('fragm.ofertas')

@include('fragm.banner2')
@include('fragm.banner3') --}}
@endif
@include('fragm.footer')

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

  {{-- <script>
    // Calcula la duración de la animación en función del ancho total de los elementos li
    const ul = document.getElementById("descuentos");
    const liList = ul.getElementsByTagName("li");
    let totalWidth = 0;

    for (const li of liList) {
      totalWidth += li.offsetWidth + parseFloat(getComputedStyle(li).marginRight);
    }

    // Calcula la duración en segundos en función de la velocidad deseada (por ejemplo, 100px por segundo)
    const velocidadDeseada = 200; // Píxeles por segundo
    const duracion = totalWidth / velocidadDeseada;

    // Establece la duración de la animación en segundos
    ul.style.animationDuration = duracion + "s";

    // Establece la variable CSS --li-width con el ancho total de los elementos li
    ul.style.setProperty("--li-width", totalWidth + "px");
  </script> --}}
@endsection

