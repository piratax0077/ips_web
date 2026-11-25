@extends('layouts.app')

@section('javascript')
    <script>
        let token = localStorage.getItem('token');
        //Si no tiene token 
        if(token){
            // Simulate a mouse click:
            localStorage.removeItem('token');
        }
        function registrar_usuario_backend(){
            let name = $('#name').val();
            let apellidos = $('#apellidos').val();
            let email = $('#email').val();
            let telefono = $('#telefono').val();
            let pass = $('#password').val();
            let pass_confirm = $('#password-confirm').val();

            let url = 'https://panchorepuestos.ddns.net/api/register';
            $.ajaxSetup({
             headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                 }
             });

             let vars = {
                name: name, 
                apellidos: apellidos,
                email: email, 
                telefono: telefono,
                password: pass, 
                confirm_password: pass_confirm,
                role: 20
            };

            $.ajax({
                type:'post',
                url: url,
                data: vars,
                success: function(resp){
                    console.log(resp);
                    
                        localStorage.setItem('token', resp.token);
                        Swal.fire({
                            icon:'success',
                            text:'Usuario registrado con éxito.',
                            toast: true,
                            timer:2000,
                            position:'center'
                        });
                        // Simulate a mouse click:
                        window.location.href = "/";
                    
                    
                },
                error: function(xhr, textStatus, errorThrown){
                    // Error en la petición AJAX, manejar los mensajes de error de validación
                    var errors = xhr.responseJSON.errors;
                    if (errors) {
                        // Recorrer los mensajes de error y mostrarlos en la interfaz de usuario
                        $('#mensajes').empty();
                        $.each(errors, function (field, messages) {
                            
                            messages.forEach(m => {
                                
                                // En este ejemplo, asumimos que los mensajes de error se muestran en un elemento HTML con el ID correspondiente al nombre del campo de formulario
                                $('#mensajes').append(`
                                <div class="alert alert-danger d-flex align-items-center alert-dismissible fade show" role="alert">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-exclamation-triangle-fill flex-shrink-0 me-2" viewBox="0 0 16 16" role="img" aria-label="Warning:">
                                        <path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"/>
                                    </svg>
                                    <div>
                                        `+m+`
                                    </div>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                    </div>
                                `);
                            });
                            
                        });
                    }
                }
            });
        }

        function verPassword(){
            let pass = $('#password').attr('type');
            let pass_confirm = $('#password-confirm').attr('type');
            if(pass == 'password'){
                $('#password').attr('type', 'text');
                $('#password-confirm').attr('type', 'text');
            }else{
                $('#password').attr('type', 'password');
                $('#password-confirm').attr('type', 'password');
            }
        }
    </script>
@endsection

@section('content')
@php
    $entorno = App::environment();
@endphp
<div class="container" data-aos="zoom-in">
    
    <div class="row justify-content-center">
        <div class="col-md-4">
            <img src="{{asset('storage_original/imagenes/image_login.jpg')}}" alt="" class="w-100">
            <img src="{{asset('storage_original/imagenes/logo_negro_200.jpeg')}}" alt="" class="w-100">
        </div>
        <div class="col-md-8">
            <div class="formulario">
                <h2 class="text-center">Registro</h2>
                <hr>
                <div id="mensajes" class="mensajes"></div>
                <form method="POST" action="{{ route('register') }}">
                    @csrf
                    <div class="row my-2">
                        <div class="col-md-6">
                            <div class="d-inline-flex w-100">
                                <i class="fa-solid fa-user icon_header m-1 p-2 border border-1 rounded bg-warning text-dark"></i>
                                <input id="name" type="text" placeholder="Ingrese su nombre" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus>
    
                                    @error('name')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                            </div>
                            
                        </div>
                        <div class="col-md-6">
                            <div class="d-inline-flex w-100">
                                <i class="fa-solid fa-user icon_header m-1 p-2 border border-1 rounded bg-warning text-dark"></i>
                                <input id="apellidos" placeholder="Ingrese sus apellidos" type="text" class="form-control @error('apellidos') is-invalid @enderror" name="apellidos" value="{{ old('apellidos') }}" required autocomplete="apellidos" autofocus>
    
                                    @error('apellidos')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                            </div>
                            
                        </div>
                    </div>
                    <div class="row my-2">
                        <div class="col-md-6 ">
                            <div class="d-inline-flex w-100">
                                <i class="fa-solid fa-envelope icon_header m-1 p-2 border border-1 rounded bg-warning text-dark"></i>
                                <input id="email" placeholder="Ingrese su email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email">

                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                                
                            </div>
                            
                        </div>
                        <div class="col-md-6 ">
                            <div class="d-inline-flex w-100">
                                <i class="fa-solid fa-phone icon_header m-1 p-2 border border-1 rounded bg-warning text-dark"></i>
                                <input type="text" placeholder="Ingrese su telefono" id="telefono" name="telefono" class="form-control @error('telefono') is-invalid @enderror" value="{{old('telefono')}}" required>

                                @error('telefono')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                            
                        </div>
                    </div>
                    <div class="row my-2">
                        <div class="col-md-6 ">
                            <div class="d-inline-flex w-100">
                                <i class="fa-solid fa-key icon_header m-1 p-2 border border-1 rounded bg-warning text-dark"></i>
                                <input id="password" type="password" placeholder="Ingrese su password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">

                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                                <button class="btn btn-warning btn-sm" onclick="verPassword()" role="button"><i class="fa fa-eye"></i></button>
                            </div>
                            
                        </div>
                        <div class="col-md-6 ">
                            <div class="d-inline-flex w-100">
                                <i class="fa-solid fa-key icon_header m-1 p-2 border border-1 rounded bg-warning text-dark"></i>
                                <input id="password-confirm" type="password" placeholder="Confirmar password" class="form-control" name="password_confirmation" required autocomplete="new-password">
                                <button class="btn btn-warning btn-sm" onclick="verPassword()" role="button"><i class="fa fa-eye"></i></button>
                            </div>
                            
                        </div>
                    </div>
                    
                    <div class="text-center bg-light border border-1 rounded py-3">
                        <button type="button" class="btn btn-dark" onclick="registrar_usuario_backend()">
                            {{ __('Registrar') }}
                        </button>
                    </div>
                    
                        
                </form>
                
            </div>
            
        </div>
        
    </div>
</div>
@endsection
