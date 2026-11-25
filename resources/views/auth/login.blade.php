@extends('layouts.app')

@section('styles')
<style>
    .login-page{position:relative;display:grid;min-height:calc(100vh - 132px);place-items:center;padding:56px 20px;background:#eef2f3;overflow:hidden}
    .login-page:before{content:"";position:absolute;inset:0;background:linear-gradient(90deg,rgba(8,31,39,.95),rgba(8,31,39,.78) 48%,rgba(8,31,39,.25)),url('{{ asset('assets/images/slider4_web.jpeg') }}') center/cover no-repeat}
    .login-shell{position:relative;z-index:1;display:grid;grid-template-columns:1.05fr .95fr;width:min(1040px,100%);min-height:610px;overflow:hidden;border:1px solid rgba(255,255,255,.12);border-radius:26px;background:#fff;box-shadow:0 30px 80px rgba(3,20,27,.35)}
    .login-story{position:relative;display:flex;flex-direction:column;justify-content:space-between;padding:54px;background:linear-gradient(145deg,#0c2a35,#1e4c5c);color:#fff;overflow:hidden}
    .login-story:after{content:"";position:absolute;right:-110px;bottom:-120px;width:340px;height:340px;border:55px solid rgba(198,106,43,.17);border-radius:50%}
    .login-story__brand,.login-story__content,.login-benefits{position:relative;z-index:1}.login-story__brand{display:flex;align-items:center;gap:13px}.login-story__brand img{width:62px;height:62px;object-fit:cover;border-radius:50%;box-shadow:0 7px 20px rgba(0,0,0,.28)}.login-story__brand strong{display:block;font-size:1.08rem}.login-story__brand span{color:rgba(255,255,255,.62);font-size:.78rem}
    .login-story__content span{color:#f4a771;font-size:.76rem;font-weight:800;letter-spacing:.14em;text-transform:uppercase}.login-story__content h1{max-width:430px;margin:.65rem 0 1rem;color:#fff;font-size:clamp(2.2rem,4vw,3.5rem);font-weight:800;line-height:1.02;letter-spacing:-.04em}.login-story__content p{max-width:430px;margin:0;color:rgba(255,255,255,.7);line-height:1.7}
    .login-benefits{display:flex;gap:20px;margin:0;padding:0;list-style:none;color:rgba(255,255,255,.72);font-size:.75rem}.login-benefits i{margin-right:5px;color:#f4a771}
    .login-panel{display:flex;flex-direction:column;justify-content:center;padding:60px 58px}.login-panel__heading span{color:#b55823;font-size:.75rem;font-weight:800;letter-spacing:.11em;text-transform:uppercase}.login-panel__heading h2{margin:.35rem 0 .55rem;color:#162d36;font-size:2rem;font-weight:800}.login-panel__heading p{margin:0 0 30px;color:#718087;font-size:.9rem}
    .page-login-field{margin-bottom:18px}.page-login-field label{display:block;margin-bottom:7px;color:#344a53;font-size:.78rem;font-weight:800}.page-login-control{position:relative}.page-login-control>i{position:absolute;top:50%;left:16px;transform:translateY(-50%);color:#78909a}.page-login-control input{width:100%;height:52px;padding:0 48px;border:1px solid #d7e0e3;border-radius:12px;background:#f8fafb;outline:0;transition:.2s}.page-login-control input:focus{border-color:#c66a2b;background:#fff;box-shadow:0 0 0 3px rgba(198,106,43,.12)}
    .page-password-toggle{position:absolute;top:50%;right:9px;transform:translateY(-50%);width:36px;height:36px;border:0;background:transparent;color:#70858d}.page-login-options{display:flex;justify-content:space-between;align-items:center;gap:12px;margin:2px 0 25px;font-size:.82rem}.page-login-options a{color:#a8511f;font-weight:700;text-decoration:none}.page-login-submit{display:flex;align-items:center;justify-content:center;width:100%;height:52px;border:0;border-radius:12px;background:linear-gradient(135deg,#c66a2b,#984817);color:#fff;font-weight:800;box-shadow:0 10px 22px rgba(152,72,23,.24);transition:.2s}.page-login-submit:hover{transform:translateY(-1px);box-shadow:0 14px 28px rgba(152,72,23,.3)}.page-login-submit:disabled{opacity:.7;transform:none}
    .login-back{display:inline-flex;align-items:center;gap:7px;margin-top:22px;color:#6e7d83;font-size:.8rem;text-decoration:none}.login-back:hover{color:#a8511f}.login-secure{margin:24px 0 0;text-align:center;color:#8a989e;font-size:.7rem}
    @media(max-width:991.98px){.login-shell{grid-template-columns:1fr;max-width:560px}.login-story{min-height:250px;padding:34px}.login-story__content{margin-top:35px}.login-story__content h1{font-size:2.25rem}.login-benefits{display:none}.login-panel{padding:42px 38px}}
    @media(max-width:575.98px){.login-page{padding:18px 12px}.login-shell{border-radius:18px}.login-story{min-height:220px;padding:25px}.login-story__content h1{font-size:1.85rem}.login-story__content p{font-size:.84rem}.login-panel{padding:34px 22px}.page-login-options{align-items:flex-start;flex-direction:column}}
</style>
@endsection

@section('javascript')
<script>
    function togglePagePassword(button) {
        const input = document.getElementById('password');
        const visible = input.type === 'text';
        input.type = visible ? 'password' : 'text';
        button.querySelector('i').className = visible ? 'fas fa-eye' : 'fas fa-eye-slash';
        button.setAttribute('aria-label', visible ? 'Mostrar contraseña' : 'Ocultar contraseña');
    }

    function login_() {
        const email = $('#email').val().trim();
        const password = $('#password').val();
        const button = $('#pageLoginButton');

        if (!email || !password) {
            Swal.fire({icon: 'warning', title: 'Completa tus datos', text: 'Ingresa tu correo electrónico y contraseña.'});
            return;
        }

        button.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Ingresando...');

        $.ajax({
            type: 'POST',
            url: '{{ config('services.ips_auth.url', 'https://intranet.ipschile.cl') }}/api/login',
            data: {email: email, password: password},
            headers: {'Accept': 'application/json'}
        }).done(function(response) {
            if (!response.token) throw new Error('La respuesta no contiene un token.');
            localStorage.setItem('token', response.token);

            $.ajax({
                type: 'POST',
                url: '/auth/token/exchange',
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                data: {token: response.token}
            }).done(function() {
                sessionStorage.setItem('external_session_ready', '1');
                window.location.href = '/';
            }).fail(function() {
                localStorage.removeItem('token');
                Swal.fire({icon: 'error', title: 'No pudimos iniciar la sesión', text: 'El token recibido no pudo validarse en el sitio.'});
            });
        }).fail(function(error) {
            const message = error.responseJSON && error.responseJSON.message ? error.responseJSON.message : 'Revisa tus credenciales e inténtalo nuevamente.';
            const isMoroso = error.responseJSON && error.responseJSON.code === 'CLIENTE_MOROSO';
            Swal.fire({
                icon: isMoroso ? 'warning' : 'error',
                title: isMoroso ? 'Acceso restringido' : 'No pudimos ingresar',
                text: message
            });
        }).always(function() {
            button.prop('disabled', false).html('<i class="fas fa-right-to-bracket me-2"></i>Ingresar a mi cuenta');
        });
    }
</script>
@endsection

@section('content')
<main class="login-page">
    <section class="login-shell" aria-labelledby="pageLoginTitle">
        <div class="login-story">
            <div class="login-story__brand"><img src="{{ asset('assets/images/logo_ips2025.png') }}" alt="IPS Autopartes"><div><strong>IPS Autopartes</strong><span>Repuestos y accesorios</span></div></div>
            <div class="login-story__content"><span>Tu cuenta IPS</span><h1>Todo sobre tu compra, en un solo lugar</h1><p>Accede para revisar pedidos, administrar tus datos y comprar repuestos de forma más rápida.</p></div>
            <ul class="login-benefits"><li><i class="fas fa-shield-halved"></i>Acceso seguro</li><li><i class="fas fa-box-open"></i>Seguimiento fácil</li><li><i class="fas fa-headset"></i>Soporte IPS</li></ul>
        </div>
        <div class="login-panel">
            <div class="login-panel__heading"><span>Inicio de sesión</span><h2 id="pageLoginTitle">Bienvenido nuevamente</h2><p>Ingresa con los datos asociados a tu cuenta.</p></div>
            <form onsubmit="event.preventDefault(); login_();" novalidate>
                @csrf
                <div class="page-login-field"><label for="email">Correo electrónico</label><div class="page-login-control"><i class="fas fa-envelope"></i><input id="email" type="email" name="email" value="{{ old('email') }}" autocomplete="email" placeholder="nombre@correo.cl" required autofocus></div></div>
                <div class="page-login-field"><label for="password">Contraseña</label><div class="page-login-control"><i class="fas fa-lock"></i><input id="password" type="password" name="password" autocomplete="current-password" placeholder="Ingresa tu contraseña" required><button type="button" class="page-password-toggle" onclick="togglePagePassword(this)" aria-label="Mostrar contraseña"><i class="fas fa-eye"></i></button></div></div>
                {{-- <div class="page-login-options"><label class="form-check mb-0"><input class="form-check-input" type="checkbox" id="remember_" name="remember"><span class="form-check-label">Recordarme</span></label>@if(Route::has('password.request'))<a href="{{ route('password.request') }}">¿Olvidaste tu contraseña?</a>@endif</div> --}}
                <button id="pageLoginButton" type="submit" class="page-login-submit"><i class="fas fa-right-to-bracket me-2"></i>Ingresar a mi cuenta</button>
            </form>
            <a href="{{ url('/') }}" class="login-back"><i class="fas fa-arrow-left"></i>Volver al inicio</a>
            <p class="login-secure"><i class="fas fa-lock me-1"></i>Conexión segura · IPS Autopartes</p>
        </div>
    </section>
</main>
@endsection
