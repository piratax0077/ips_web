@extends('layouts.app')

@section('javascript')
<script>

    

    window.onload = function(){
        //Se recupera la url actual
        var URLactual = window.location;
        //Convertimos la url a string para poder rescatar el id del repuesto
        var strurl = String(URLactual);
        //Separamos el string y dejamos solo el id
        var id_array = strurl.split('/');
        var idmarca = id_array[4];
        cargar_Modelos(idmarca);
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

      var url_buscar='https://panchorepuestos.ddns.net/api/damemodelos_por_marca/'+idmarca;
      let token = localStorage.getItem('token');
      $.ajax({
       type:'GET',
       beforeSend: function () {
        $('#mensaje').empty();
        $('#mensaje').append('<img src="{{asset("storage_original/imagenes/loading6.gif")}}" alt="Cargando ...">');
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
      success:function(resp){
        console.log(resp);
        Swal.close();
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
        $('#mensaje').empty();
        let modelos =resp[0];
        $('#mensaje').append('<div class="container">Se han encontrado '+modelos.length+' modelos para la marca '+resp[1].marcanombre+'.</div>');
         $('#mensaje').css('color','gray');
         $('#mensaje').css('font-size','15px');
         $('#mensaje').css('font-style','italic');
        if(modelos.length > 0){
          
        let urlfotomarca = resp[1].urlfoto;
        let container = $('#pagination');
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
                                    <div class="col-lg-3 col-md-4 col-sm-6 mb-3">
                                    <div class="card">
                                    <img class="card-img-top repuestos_image"  src="https://panchorepuestos.ddns.net/storage/`+item.urlfoto+`" alt="Card image cap">
                                    <div class="card-body">
                                        <h6 class="card-title"> <a href="/repuestos_modelo/`+item.id+`" class="a_modelo" >`+item.modelonombre+`</a></h6>
                                        <p class="subtitulo_">`+resp+`</p>
                                    </div>
                                    </div>
                                    </div>
                                    `;
                                   
                                });
                                
                                dataHtml += '</div>';

                                $("#container").html(dataHtml);
                            }
                        });
        }else{
          $("#container").html('<div class="text-center">Momentaneamente no existen repuestos. </div>');
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
<div class="slider_propio">
  <h1 style="line-height: 120px; font-size:30px; letter-spacing: 1px;   color: #f9ef26;
  font-weight: bold;">Modelos</h1>
</div>
<div class="container" id="contenedor">
  <div style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='currentColor'/%3E%3C/svg%3E&#34;);" aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="/">Inicio</a></li>
      <li class="breadcrumb-item active" aria-current="page"><a href="/marcas-repuestos">Marcas</a></li>
      <li class="breadcrumb-item active" aria-current="page">Modelos</li>
    </ol>
  </div>
    <p id="mensaje"></p>
    <div id="container" class="text-center"></div>
    <div id="pagination" class="text-center"></div>
</div>

@endsection
