 <!-- Footer Section Begin -->
    <footer class="footer set-bg" data-setbg="img/footer-bg.jpg">
        <div class="container">
            <div class="footer__contact">
                <div class="row">
                    <div class="col-lg-6 col-md-6">
                        <div class="footer__contact__title">
                            <span class="footer__eyebrow">Atención personalizada</span>
                            <h2>¿Buscas un repuesto o producto?</h2>
                            <p>Cuéntanos qué necesitas y te ayudamos a encontrarlo.</p>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-6">
                        <div class="footer__contact__option">
                            <a class="option__item" href="tel:+56975159323"><i class="fa fa-phone"></i> (+569) 7515 9323</a>
                            <a class="option__item email" href="mailto:contacto@ipschile.cl"><i class="fa fa-envelope-o"></i> contacto@ipschile.cl</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-4 col-md-5">
                    <div class="footer__about">
                        <div class="footer__logo">
                            <a href="/"><img src="{{ asset('assets/images/logo_ips2025.png') }}" alt="IPS Chile" style="max-height: 60px;"></a>
                        </div>
                        <p>Importadora y comercializadora de repuestos, accesorios y productos para vehículos, hogar y uso diario. Encuentra desde componentes automotrices hasta ampolletas, radios, USB, cargadores y mucho más.</p>
                        <p class="footer__address"><i class="fa fa-map-marker"></i> Río Refugio #9641, Santiago, Chile</p>
                        <div class="footer__social">
                            <a href="https://www.facebook.com/ipschile" class="facebook" target="_blank"><i class="fa fa-facebook"></i></a>
                            <a href="https://www.instagram.com/ipschile" class="twitter" target="_blank"><i class="fa fa-instagram"></i></a>
                            <a href="https://wa.me/56975159323" class="google" target="_blank"><i class="fa fa-whatsapp"></i></a>
                            <a href="mailto:contacto@ipschile.cl" class="skype"><i class="fa fa-envelope"></i></a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 offset-lg-1 col-md-3">
                    <div class="footer__widget">
                        <h5>Servicios</h5>
                        <ul>
                            <li><a href="{{ url('/repuestos') }}"><i class="fa fa-angle-right"></i> Ver catálogo</a></li>
                            <li><a href="{{ url('/carrito') }}"><i class="fa fa-angle-right"></i> Mi carrito</a></li>
                            <li><a href="{{ url('/solicitudes') }}"><i class="fa fa-angle-right"></i> Mis pedidos</a></li>
                            <li><a href="{{ url('/quienes-somos') }}"><i class="fa fa-angle-right"></i> Conoce IPS Chile</a></li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-4 col-md-4">
                    <div class="footer__widget">
                        <h5>Productos populares</h5>
                        <ul class="footer__category-list">
                            <li><a href="{{ url('/ampolleta/buscador') }}"><i class="fa fa-angle-right"></i> Ampolletas</a></li>
                            <li><a href="{{ url('/usb/buscador') }}"><i class="fa fa-angle-right"></i> USB y cargadores</a></li>
                            <li><a href="{{ url('/radio/buscador') }}"><i class="fa fa-angle-right"></i> Radios y audio</a></li>
                            <li><a href="{{ url('/filtro/buscador') }}"><i class="fa fa-angle-right"></i> Filtros</a></li>
                            <li><a href="{{ url('/aceite/buscador') }}"><i class="fa fa-angle-right"></i> Aceites</a></li>
                            <li><a href="{{ url('/accesorio/buscador') }}"><i class="fa fa-angle-right"></i> Accesorios</a></li>
                        </ul>
                    </div>
                </div>
                {{-- <div class="col-lg-3 col-md-6">
                    <div class="footer__brand">
                        <h5>Marcas Destacadas</h5>
                        <ul>
                            <li><a href="/marca/bosch"><i class="fa fa-angle-right"></i> Bosch</a></li>
                            <li><a href="/marca/gates"><i class="fa fa-angle-right"></i> Gates</a></li>
                            <li><a href="/marca/continental"><i class="fa fa-angle-right"></i> Continental</a></li>
                            <li><a href="/marca/mann"><i class="fa fa-angle-right"></i> Mann Filter</a></li>
                        </ul>
                        <ul>
                            <li><a href="/marca/ngk"><i class="fa fa-angle-right"></i> NGK</a></li>
                            <li><a href="/marca/sachs"><i class="fa fa-angle-right"></i> Sachs</a></li>
                            <li><a href="/marca/brembo"><i class="fa fa-angle-right"></i> Brembo</a></li>
                            <li><a href="/marca/mahle"><i class="fa fa-angle-right"></i> Mahle</a></li>
                        </ul>
                    </div>
                </div> --}}
            </div>
            <!-- Link back to Colorlib can't be removed. Template is licensed under CC BY 3.0. -->
            <div class="footer__copyright__text">
                <p>Copyright &copy;<script>document.write(new Date().getFullYear());</script> Todos los derechos reservados | IPS Chile - Importadora de repuestos y productos | Desarrollado con <i class="fa fa-heart" aria-hidden="true"></i> por <a href="https://sysmafra.cl" target="_blank" rel="noopener noreferrer">Sysmafra</a></p>
            </div>
            <!-- Link back to Colorlib can't be removed. Template is licensed under CC BY 3.0. -->
        </div>
    </footer>
    <!-- Footer Section End -->
