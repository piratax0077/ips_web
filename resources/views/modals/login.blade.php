<!-- MODAL Login -->
<div class="modal fade" id="modalLogin" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <img src="{{ asset('assets/images/logo_ips_colores.jpeg') }}" alt=""
                    class="logo_header rounded">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="formulario">
                    <div class="row">
                        <div class="col-md-12">
                            <!-- Email input -->
                            <div class="d-flex p-2 my-3 bd-highlight">
                                <i class="fa-solid fa-user icon_header m-1 p-2 border border-1 rounded "></i>
                                <input id="email_" type="email"
                                    class="form-control form-control-sm @error('email') is-invalid @enderror"
                                    name="email" value="{{ old('email') }}" required autocomplete="email"
                                    autofocus placeholder="Ingrese su email">

                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-12">
                            <!-- Password input -->
                            <div class="d-flex p-2 my-3 bd-highlight">
                                <i class="fa-solid fa-key icon_header m-1 p-2 border border-1 rounded "></i>
                                <input id="password_" type="password"
                                    class="form-control form-control-sm @error('password') is-invalid @enderror"
                                    name="password" required autocomplete="current-password"
                                    placeholder="Ingrese su contraseña">

                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center">
                        <!-- Checkbox -->
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="remember" id="remember"
                                {{ old('remember') ? 'checked' : '' }}>

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

                    <div class="text-lg-end mt-4 pt-2">
                        <button type="button" class="btn btn-outline-dark btn-sm float-right" onclick="login()"
                            style="padding-left: 2.5rem; padding-right: 2.5rem;">
                            {{ __('Login') }}
                        </button>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <figcaption class="blockquote-footer">
                    SysMaFra 2025&copy; <cite title="Source Title">Todos los derechos reservados.</cite>
                </figcaption>
                <p class="text-center"></p>
            </div>
        </div>
    </div>
</div>
