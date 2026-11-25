@extends('layouts.app')

@section('javascript')
<script>
    window.onload = function(){
            dame_marcas();
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
    }

    function cargar_Modelos(idmarca)
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

      var url_buscar='https://intranet.ipschile.cl/api/damemodelos_por_marca/'+idmarca;

      $.ajax({
       type:'GET',
       beforeSend: function () {
      },
      url:url_buscar,
      success:function(resp){
        console.log(resp);
      },
      error: function(error){

        console.log(error.responseText);

      }
      });
    }

    function dame_marcas(){
                $.ajax({
                    type:'get',
                    url: 'https://intranet.ipschile.cl/api/ruta_prueba',
                    beforeSend: function(){
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
                    success: function(marcas){
                      Swal.close();
                        $('#mensaje').empty();
                        $('#imagenes_marcas').empty();

                        marcas.forEach(marca => {

                            $('#imagenes_marcas').append("<div class='col-md-3 col-lg-2 col-sm-6 col-6 '><a href='/modelos_marca/"+marca.idmarcavehiculo+"' ><img src='https://intranet.ipschile.cl/storage/"+marca.urlfoto+"' alt='' class='imagen_marca_banner'> </a></div>");


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
{{-- @include('fragm.slider_pequeño_marcas') --}}

<div class="container-fluid" id="contenedor" data-aos="fade-down">
    <div style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='currentColor'/%3E%3C/svg%3E&#34;);" aria-label="breadcrumb">
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="/">Inicio</a></li>
          <li class="breadcrumb-item active" aria-current="page">Marcas</li>
        </ol>
      </div>
      {{-- @include('fragm.galeria') --}}
<h1 style="font-size: 23px;
color: gray;
margin-left: 30px;
border-bottom: 1px solid;
margin-right: 30px;">Haz click en alguna de nuestras marcas: </h1>
<p id="mensaje" class="text-center"></p>
    <div id="imagenes_marcas" class="row"  style="margin: 30px 0px 40px 0px; display: flex; justify-content: space-evenly;">

    </div>

</div>

@endsection
