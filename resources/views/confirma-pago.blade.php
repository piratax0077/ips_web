@extends('layouts.app')
@section('javascript')
    <script>
        window.onload = function(){
            
            confirmar_pago_virtual();
            
        }

        function confirmar_pago_virtual(){
            let compra_id = $('#compra_id').val();
            let url = "https://panchoserver.ddns.net/api/confirmar_pago/"+compra_id;
            console.log(url);
            $.ajax({
                type:'get',
                url:url,
                async: false,
                success: function(resp){
                    console.log(resp);
                },
                error: function(error){
                    console.log(error.responseText);
                }
            })
        }
    </script>
@endsection

@section('content')
<div class="container text-center">
    <h1>Gracias por su compra.</h1>
    <p>Ha completado su pago con éxito</p>
    <p>Haga click en el numero de pédido {{$compra_id}} para ver más detalles</p>
    <p>Usted tambien recibirá un resumen de la información de su pedido por email</p>
    <p>Si tienes alguna duda respecto al pedido, no dude en contactarnos en nuestra página contacto</p>
    <a class="btn btn-warning" href="{{url('/')}}">Seguir comprando</a>
</div>
<input type="hidden" name="compra_id" id="compra_id" value="{{$compra_id}}">
@endsection
