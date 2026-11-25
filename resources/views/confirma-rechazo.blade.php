@extends('layouts.app')

@section('content')
<div class="container text-center">
    <h1 class="alert-danger">NO SE HA PODIDO PROCESAR LA COMPRA.</h1>
    <p>Ha completado el proceso con algún error</p>
    <p>Haga click en el numero de pédido {{$compra_id}} para ver más detalles</p>
    <p>Usted tambien recibirá un resumen de la información de su pedido por email</p>
    <p>Si tienes alguna duda respecto al pedido, no dude en contactarnos en nuestra página contacto</p>
    <a class="btn btn-warning" href="{{url('/')}}">Seguir comprando</a>
</div>

@endsection
