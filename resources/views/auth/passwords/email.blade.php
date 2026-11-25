@extends('layouts.app')

@section('javascript')
    <script>
       function enviar(){
        let email = $('#email').val();
        $.ajaxSetup({
                headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            if(email.trim() == 0 || email == ''){
                Swal.fire({
                    icon:'error',
                    text: 'Debe ingresar una dirección de correo',
                    toast: true,
                    timer: 3000,
                    position:'center',
                    showConfirmButton: false
                });
                return false;
            }
        // Hacer una solicitud POST al backend para enviar el correo de restablecimiento de contraseña
        $.ajax({
            url: 'https://panchoserver.ddns.net/api/forgot-password',
            method: 'POST',
            data: {email: email},
            beforeSend: function(){
                Swal.fire({
                    icon:'info',
                    text:'Solicitando ...'
                });
            },
            success: function(response) {
                console.log(response);
              Swal.close();
                // Si la solicitud es exitosa, mostrar un mensaje de éxito
                Swal.fire({
                    icon:'success',
                    text: 'Se ha enviado un email para restablecer su contraseña.'
                });
            },
            error: function(response) {
                // Si hay un error, mostrar un mensaje de error
                console.log(response.responseText);
                
                Swal.fire({
                    icon:'error',
                    text: 'Correo electrónico no encontrado. No se puede reestablecer la contraseña.'
                });
            }
        });
       }
        

    </script>
@endsection

@section('content')
<div class="container">
    <div class="row justify-content-center p-5">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header text-light"><img src="{{asset('storage_original/imagenes/logo_negro_200.jpeg')}}" alt="" class="logo_header"> {{ __('Reset Password') }}</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <form id="reset-password-form">

                        <div class="row mb-3">
                            <label for="email" class="col-md-4 col-form-label text-md-end">{{ __('Email Address') }}</label>

                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email">

                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="button" class="btn btn-dark" onclick="enviar()">
                                    {{ __('Enviar Password Reset Link') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
