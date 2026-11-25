@extends('layouts.app')
@section('javascript')
    <script>
        function reestablecer_password(){
            let token = $('#token').val();
            let pass1 = $('#password').val();
            let pass2 = $('#password-confirm').val();

            let url = 'https://panchoserver.ddns.net/api/restablecer_password';

            let data = {token: token, password: pass1, confirm_password: pass2};

            $.ajaxSetup({
                headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
           
            $.ajax({
                type:'post',
                url: url,
                data: data,
                beforeSend: function(){
                    console.log('cargando');
                },
                success: function(data){
                    console.log(data);
                    if(data == 'OK'){
                        Swal.fire({
                            icon:'success',
                            text:'Su contraseña ha sido reestablecida con éxito.',
                        });
                    }

                    setTimeout(() => {
                        // Simulate a mouse click:
                        window.location.href = "/login";
                    }, 2000);
                },
                error: function(xhr, textStatus, errorThrown){
                    
                    // Error en la petición AJAX, manejar los mensajes de error de validación
                    var errors = xhr.responseJSON.errors;
                    console.log(errors);
                    if (errors) {
                        // Recorrer los mensajes de error y mostrarlos en la interfaz de usuario
                        $('#mensajes').empty();
                        $.each(errors, function (field, messages) {
                            
                            messages.forEach(m => {
                                
                                // En este ejemplo, asumimos que los mensajes de error se muestran en un elemento HTML con el ID correspondiente al nombre del campo de formulario
                                $('#mensajes').append(m+'<br>');
                            });
                            
                        });
                    }
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
                    <div id="mensajes" class="mensajes"></div>
                    <form method="POST" action="{{ route('password.update') }}">
                        @csrf

                        <input type="hidden" name="token" id="token" value="{{ $token }}">

                        <div class="row mb-3">
                            <label for="password" class="col-md-4 col-form-label text-md-end">{{ __('Password') }}</label>

                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">

                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="password-confirm" class="col-md-4 col-form-label text-md-end">{{ __('Confirm Password') }}</label>

                            <div class="col-md-6">
                                <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password">
                            </div>
                        </div>

                        <div class="row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="button" class="btn btn-dark" onclick="reestablecer_password()">
                                    {{ __('Reset Password') }}
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
