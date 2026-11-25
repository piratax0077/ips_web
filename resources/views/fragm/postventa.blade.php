<style>
    /* Estilos para la barra de progreso de pasos */
    .steps-progress {
        background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
        color: white;
        padding: 20px 15px;
        margin: 0 0 25px 0;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }

    .step-item {
        text-align: center;
        position: relative;
        padding: 10px;
    }

    .step-item p:first-child {
        font-weight: 700;
        font-size: 1rem;
        margin-bottom: 15px;
        color: #fff;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .step-item .icon_header {
        font-size: 2.5rem;
        /* color: #4caf50; */
        margin: 10px 0;
        filter: drop-shadow(0 2px 4px rgba(0,0,0,0.3));
    }

    .step-item p:last-child {
        font-size: 0.9rem;
        margin-top: 10px;
        color: #ecf0f1;
        font-weight: 500;
    }

    /* Conectores entre pasos */
    .step-item:not(:last-child)::after {
        content: '';
        position: absolute;
        top: 50%;
        right: -10%;
        width: 20%;
        height: 2px;
        background: linear-gradient(90deg, #4caf50 0%, rgba(76, 175, 80, 0.3) 100%);
        display: none;
    }

    @media (min-width: 768px) {
        .step-item:not(:last-child)::after {
            display: block;
        }
    }

    /* Responsive */
    @media (max-width: 768px) {
        .steps-progress {
            padding: 15px 10px;
            margin-bottom: 20px;
        }

        .step-item p:first-child {
            font-size: 0.85rem;
            margin-bottom: 10px;
        }

        .step-item .icon_header {
            font-size: 2rem;
        }

        .step-item p:last-child {
            font-size: 0.8rem;
        }
    }

    @media (max-width: 576px) {
        .steps-progress {
            padding: 10px 5px;
            margin-bottom: 15px;
        }

        .step-item {
            padding: 8px 5px;
        }

        .step-item p:first-child {
            font-size: 0.75rem;
            margin-bottom: 8px;
        }

        .step-item .icon_header {
            font-size: 1.5rem;
            margin: 5px 0;
        }

        .step-item p:last-child {
            font-size: 0.7rem;
            margin-top: 5px;
        }
    }
</style>

<div class="row steps-progress mx-0">
    <div class="col-md-3 col-6 step-item">
        <p>Paso 1</p>
        <p><i class="fa-solid fa-check icon_header"></i></p>
        <p>Validamos tu compra</p>
    </div>
    <div class="col-md-3 col-6 step-item">
        <p>Paso 2</p>
        <p><i class="fa-solid fa-money-bill icon_header"></i></p>
        <p>Compra confirmada</p>
    </div>
    <div class="col-md-3 col-6 step-item">
        <p>Paso 3</p>
        <p><i class="fa-solid fa-truck icon_header"></i></p>
        <p>En camino a destino</p>
    </div>
    <div class="col-md-3 col-6 step-item">
        <p>Paso 4</p>
        <p><i class="fa-solid fa-check-double icon_header"></i></p>
        <p>Entregado en domicilio</p>
    </div>
</div>
