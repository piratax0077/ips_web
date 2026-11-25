<main class="home-page">
    <section class="home-hero" aria-label="Productos, repuestos y accesorios IPS Chile">
        <img class="home-hero__image" src="{{ asset('assets/images/slider4_web.jpeg') }}" alt="Selección de productos, repuestos y accesorios importados">
        <div class="home-hero__content">
            <div class="container">
                <div class="home-hero__copy">
                    <span class="home-eyebrow">IPS Autopartes</span>
                    <h1>Productos y repuestos para cada necesidad</h1>
                    <p class="home-hero__lead">Encuentra repuestos, accesorios y productos importados por descripción, código o marca. Compra de forma simple y con asesoría cuando la necesites.</p>
                    <form class="home-search" onsubmit="event.preventDefault(); buscar_main();">
                        <input id="tag_principal" name="tag_principal" type="search" placeholder="Ej: cargador USB, ampolleta, herramienta..." aria-label="Buscar productos">
                        <button type="submit"><i class="fa-solid fa-magnifying-glass me-2"></i>Buscar producto</button>
                    </form>
                    <p class="home-search-help"><i class="fa-solid fa-circle-info me-1"></i> También puedes explorar el catálogo por marca de producto.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="home-finder" aria-labelledby="finder-title">
        <div class="container">
            <div class="finder-card">
                <div class="finder-title">
                    <span class="finder-title__icon"><i class="fa-solid fa-tags"></i></span>
                    <div><h2 id="finder-title">Explora por marca</h2><p>Selecciona una marca para ver sus productos y repuestos disponibles.</p></div>
                </div>
                <div class="finder-grid finder-grid--brand">
                    <select id="marcas_select" class="form-select" aria-label="Marca de productos"><option value="">Cargando marcas...</option></select>
                    <button type="button" onclick="busqueda_principal()"><i class="fa-solid fa-boxes-stacked me-2"></i>Ver productos</button>
                </div>
            </div>
        </div>
    </section>

    <section class="home-section home-section--white" aria-labelledby="services-title">
        <div class="container">
            <div class="home-heading">
                <span>Todo en un solo lugar</span>
                <h2 id="services-title">¿Qué necesitas hacer hoy?</h2>
                <p>Explora productos y accesorios, revisa tus solicitudes o conversa con nuestro equipo.</p>
            </div>
            <div class="home-actions">
                <a href="/repuestos" class="home-action">
                    <span class="home-action__icon"><i class="fa-solid fa-gears"></i></span>
                    <h3>Explorar productos</h3><p>Busca en nuestro catálogo por descripción, código, marca o tipo de producto.</p>
                    <span class="home-action__link">Ir al catálogo <i class="fa-solid fa-arrow-right"></i></span>
                </a>
                <a href="/solicitudes" class="home-action">
                    <span class="home-action__icon"><i class="fa-solid fa-box-open"></i></span>
                    <h3>Seguir mi pedido</h3><p>Consulta tus solicitudes y revisa el avance de tu compra en línea.</p>
                    <span class="home-action__link">Ver mis solicitudes <i class="fa-solid fa-arrow-right"></i></span>
                </a>
                <a href="https://wa.me/56975159323?text=Hola,%20necesito%20ayuda%20para%20encontrar%20un%20repuesto" target="_blank" rel="noopener" class="home-action">
                    <span class="home-action__icon"><i class="fa-brands fa-whatsapp"></i></span>
                    <h3>Hablar con un experto</h3><p>¿Tienes dudas sobre un producto? Te ayudamos a encontrar la alternativa adecuada.</p>
                    <span class="home-action__link">Iniciar conversación <i class="fa-solid fa-arrow-right"></i></span>
                </a>
            </div>
        </div>
    </section>

    <section class="home-section">
        <div class="container">
            <div class="home-trust">
                <div class="home-trust__content">
                    <span class="home-eyebrow">Compra con confianza</span>
                    <h2>Variedad de productos, respaldo en cada compra</h2>
                    <p>Importamos y seleccionamos productos confiables, acompañando tu compra desde la elección hasta la entrega.</p>
                    <ul class="trust-list">
                        <li><i class="fa-solid fa-circle-check"></i> Variedad y calidad seleccionada</li>
                        <li><i class="fa-solid fa-truck-fast"></i> Despacho y seguimiento fácil</li>
                        <li><i class="fa-solid fa-headset"></i> Atención de especialistas</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>
</main>
