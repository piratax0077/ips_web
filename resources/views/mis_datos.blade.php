@extends('layouts.app')

@section('styles')
    <style>
        :root {
            --profile-navy: #173f4d;
            --profile-dark: #0d2933;
            --profile-orange: #c66a2b;
            --profile-muted: #68777d;
            --profile-line: #e3e8ea;
        }

        body { margin: 0 !important; }
        .main { margin-top: 0 !important; }
        .header-fixed-wrapper { top: 0 !important; }
        .header-fixed-wrapper nav.navbar { position: relative !important; top: 0 !important; }
        #globalLoadingOverlay { display: none !important; }

        .profile-hero {
            padding: 36px 0 76px;
            color: #fff;
            background: linear-gradient(120deg, var(--profile-dark), var(--profile-navy));
        }

        .profile-breadcrumb {
            display: flex;
            align-items: center;
            gap: .65rem;
            margin-bottom: 1.25rem;
            color: rgba(255, 255, 255, .62);
            font-size: .82rem;
        }

        .profile-breadcrumb a { color: #ffc49a; text-decoration: none; }
        .profile-breadcrumb i { font-size: .55rem; }
        .profile-eyebrow {
            color: #ffc49a;
            font-size: .76rem;
            font-weight: 800;
            letter-spacing: .13em;
            text-transform: uppercase;
        }

        .profile-hero__row {
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            gap: 2rem;
        }

        .profile-hero h1 {
            margin: .35rem 0 .55rem;
            color: #fff;
            font-size: clamp(1.8rem, 3.2vw, 2.65rem);
            font-weight: 800;
            line-height: 1.08;
            letter-spacing: -.035em;
        }

        .profile-hero p {
            max-width: 720px;
            margin: 0;
            color: rgba(255, 255, 255, .72);
            font-size: .92rem;
            line-height: 1.5;
        }

        .profile-hero__icon {
            display: grid;
            flex: 0 0 78px;
            width: 78px;
            height: 78px;
            place-items: center;
            color: #fff;
            background: rgba(255, 255, 255, .09);
            border: 1px solid rgba(255, 255, 255, .16);
            border-radius: 18px;
            font-size: 1.7rem;
            backdrop-filter: blur(8px);
        }

        .profile-container { position: relative; margin-top: -54px; padding-bottom: 80px; }
        .profile-card {
            overflow: hidden;
            max-width: 1080px;
            margin: 0 auto;
            border: 0 !important;
            border-radius: 20px !important;
            background: #fff;
            box-shadow: 0 18px 45px rgba(19, 48, 59, .13) !important;
        }

        .profile-card__header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            padding: 1.35rem 1.6rem;
            border-bottom: 1px solid var(--profile-line);
        }

        .profile-card__title { display: flex; align-items: center; gap: .8rem; }
        .profile-card__title-icon {
            display: grid;
            width: 42px;
            height: 42px;
            place-items: center;
            color: var(--profile-orange);
            background: #fff4ec;
            border-radius: 11px;
        }

        .profile-card__title h2 { margin: 0; color: #162c35; font-size: 1.1rem; font-weight: 800; }
        .profile-card__title p { margin: .2rem 0 0; color: var(--profile-muted); font-size: .78rem; }
        .profile-readonly-badge {
            display: inline-flex;
            align-items: center;
            gap: .4rem;
            padding: .5rem .7rem;
            color: var(--profile-navy);
            background: #eef4f6;
            border-radius: 8px;
            font-size: .75rem;
            font-weight: 800;
            white-space: nowrap;
        }

        .profile-card__body { padding: 1.6rem; }
        .profile-data-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 1rem; }
        .profile-field { min-width: 0; }
        .profile-field--wide { grid-column: 1 / -1; }
        .profile-field label {
            display: flex;
            align-items: center;
            gap: .5rem;
            margin-bottom: .5rem;
            color: #35474e;
            font-size: .8rem;
            font-weight: 800;
        }

        .profile-field label i { width: 18px; color: var(--profile-orange); text-align: center; }
        .profile-readonly {
            width: 100%;
            height: 54px;
            padding: .75rem 1rem;
            overflow: hidden;
            color: #20353d;
            background: #f6f8f9;
            border: 1px solid #dce3e6;
            border-radius: 11px;
            font-size: .95rem;
            font-weight: 600;
            text-overflow: ellipsis;
            cursor: default;
        }

        .profile-readonly:focus { border-color: #dce3e6; box-shadow: none; outline: 0; }
        .profile-note {
            display: flex;
            align-items: flex-start;
            gap: .75rem;
            margin-top: 1.4rem;
            padding: 1rem 1.1rem;
            color: #52646b;
            background: #f4f7f8;
            border-left: 3px solid var(--profile-orange);
            border-radius: 0 10px 10px 0;
            font-size: .82rem;
            line-height: 1.5;
        }

        .profile-note i { margin-top: .15rem; color: var(--profile-orange); }

        @media (max-width: 767.98px) {
            .profile-hero { padding: 28px 0 64px; }
            .profile-hero__row { align-items: flex-start; }
            .profile-hero h1 { font-size: 1.75rem; }
            .profile-hero__icon { width: 58px; height: 58px; flex-basis: 58px; border-radius: 14px; }
            .profile-container { padding-right: 12px; padding-left: 12px; }
            .profile-card__header { align-items: flex-start; padding: 1rem; flex-direction: column; }
            .profile-card__body { padding: 1rem; }
            .profile-data-grid { grid-template-columns: 1fr; }
            .profile-field--wide { grid-column: auto; }
        }
    </style>
@endsection

@section('content')
    <header class="profile-hero">
        <div class="container-fluid px-3 px-lg-5">
            <nav class="profile-breadcrumb" aria-label="Migas de pan">
                <a href="{{ url('/') }}">Inicio</a>
                <i class="fas fa-chevron-right" aria-hidden="true"></i>
                <span>Mis datos</span>
            </nav>

            <div class="profile-hero__row">
                <div>
                    <span class="profile-eyebrow">Cuenta de cliente</span>
                    <h1>Mis datos</h1>
                    <p>Consulta la información asociada a tu cuenta de cliente.</p>
                </div>
                <div class="profile-hero__icon" aria-hidden="true">
                    <i class="fas fa-user"></i>
                </div>
            </div>
        </div>
    </header>

    <main class="container-fluid profile-container px-3 px-lg-5">
        <section class="card profile-card" aria-labelledby="profile-data-title">
            <div class="profile-card__header">
                <div class="profile-card__title">
                    <span class="profile-card__title-icon" aria-hidden="true"><i class="fas fa-address-card"></i></span>
                    <div>
                        <h2 id="profile-data-title">Información del cliente</h2>
                        <p>Datos registrados en nuestros sistemas</p>
                    </div>
                </div>
                <span class="profile-readonly-badge"><i class="fas fa-lock"></i> Solo lectura</span>
            </div>

            <div class="profile-card__body">
                <input type="hidden" id="id_usuario" value="{{ auth()->user()->id ?? '' }}">

                <div class="profile-data-grid">
                    <div class="profile-field">
                        <label for="name"><i class="fas fa-user"></i> Nombre completo</label>
                        <input id="name" class="profile-readonly" type="text"
                               value="{{ auth()->user()->name ?? '' }}" readonly aria-readonly="true">
                    </div>

                    <div class="profile-field">
                        <label for="telefono"><i class="fas fa-phone"></i> Teléfono</label>
                        <input id="telefono" class="profile-readonly" type="text"
                               value="{{ auth()->user()->telefono ?? '' }}" readonly aria-readonly="true">
                    </div>

                    <div class="profile-field profile-field--wide">
                        <label for="email"><i class="fas fa-envelope"></i> Correo electrónico</label>
                        <input id="email" class="profile-readonly" type="email"
                               value="{{ auth()->user()->email ?? '' }}" readonly aria-readonly="true">
                    </div>
                </div>

                <div class="profile-note" role="note">
                    <i class="fas fa-circle-info" aria-hidden="true"></i>
                    <span>Esta información es administrada desde nuestros sistemas internos. Si necesitas actualizar algún dato, comunícate con nuestro equipo de soporte o ventas.</span>
                </div>
            </div>
        </section>
    </main>
@endsection
