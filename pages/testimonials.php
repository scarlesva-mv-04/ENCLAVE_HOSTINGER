<!DOCTYPE html>
<html lang="en">
<body>
    <main class="testimonials">
        <div class="header-dummy"></div>
        <div class="cover">
            <div class="content">
                <p class="subtitles"> Solo Enclave puede lograr el equilibrio perfecto entre confort y seguridad.</p>
            </div>
        </div>
        <section id="testimonials">
            <h2 class="subtitles">Las opiniones de nuestros clientes <span class="resaltar">nos respaldan</span></h2>
            <!-- Slider main container -->
            <div class="swiper testimonials_opinion">
                <!-- Additional required wrapper -->
                <div class="swiper-wrapper">
                    <!-- Slides -->
                    <div class="swiper-slide">
                        <div class="testimonial-container fondo-4">
                            <div class="testimonial-content">
                                <div class="testimonial-header">
                                    <img src="images/photos/User_pic.webp" alt="Foto de Adeline"
                                        class="testimonial-photo">
                                    <span class="testimonial-author subtitles">Adeline ha dicho:</span>
                                </div>
                                <blockquote class="testimonial-quote">
                                    “Estar en casa jamás había sido tan cómodo”
                                </blockquote>
                            </div>
                        </div>
                    </div>
                    <div class="swiper-slide">
                        <div class="testimonial-container fondo-2">
                            <div class="testimonial-content">
                                <div class="testimonial-header">
                                    <img src="images/photos/jeff.webp" alt="Foto de Jeff"
                                        class="testimonial-photo">
                                    <span class="testimonial-author">Jeff ha dicho:</span>
                                </div>
                                <blockquote class="testimonial-quote">
                                    “Enclave simplemente esta a otro nivel”
                                </blockquote>
                            </div>
                        </div>
                    </div>
                    <div class="swiper-slide">
                        <div class="testimonial-container fondo-3">
                            <div class="testimonial-content">
                                <div class="testimonial-header">
                                    <img src="images/photos/jessica.webp" alt="Foto de Jessica"
                                        class="testimonial-photo">
                                    <span class="testimonial-author">Jessica ha dicho:</span>
                                </div>
                                <blockquote class="testimonial-quote">
                                    “Estar en casa jamás había sido tan cómodo”
                                </blockquote>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="swiper-scrollbar"></div>
            </div>
        </section>
        <script>
            const testimonials_opinion = new Swiper('.swiper', {
                // Optional parameters
                direction: 'horizontal',

                // And if we need scrollbar
                scrollbar: {
                    el: '.swiper-scrollbar',
                },
                breakpoints: {
                    0: {   // Para pantallas menores a 768px
                        slidesPerView: 1
                    },
                    768: { // Para pantallas de 768px hasta 1023px
                        slidesPerView: 2,
                        spaceBetween:10
                    },
                    1024: { // Para pantallas de 1024px en adelante
                        slidesPerView: 3,
                        spaceBetween:20
                    }
                }
            });
        </script>
        <section id="testionials-security">
            <h2 class="subtitles">Tu seguridad siempre será <span class="resaltar">nuestra prioridad</span></h2>
            <div class="swiper testimonials_opinion">
                <!-- Additional required wrapper -->
                <div class="swiper-wrapper">
                    <!-- Slides -->
                    <div class="swiper-slide">
                        <div class="testimonial-container fondo-4">
                            <div class="testimonial-content">
                                <div class="testimonial-header">
                                    <img src="images/photos/User_pic.webp" alt="Foto de Adeline"
                                        class="testimonial-photo">
                                    <span class="testimonial-author subtitles">Adeline ha dicho:</span>
                                </div>
                                <blockquote class="testimonial-quote">
                                    “Estar en casa jamás había sido tan cómodo”
                                </blockquote>
                            </div>
                        </div>
                    </div>
                    <div class="swiper-slide">
                        <div class="testimonial-container fondo-2">
                            <div class="testimonial-content">
                                <div class="testimonial-header">
                                    <img src="images/photos/jeff.webp" alt="Foto de Jeff"
                                        class="testimonial-photo">
                                    <span class="testimonial-author">Jeff ha dicho:</span>
                                </div>
                                <blockquote class="testimonial-quote">
                                    “Enclave simplemente esta a otro nivel”
                                </blockquote>
                            </div>
                        </div>
                    </div>
                    <div class="swiper-slide">
                        <div class="testimonial-container fondo-3">
                            <div class="testimonial-content">
                                <div class="testimonial-header">
                                    <img src="images/photos/jessica.webp" alt="Foto de Jessica"
                                        class="testimonial-photo">
                                    <span class="testimonial-author">Jessica ha dicho:</span>
                                </div>
                                <blockquote class="testimonial-quote">
                                    “Estar en casa jamás había sido tan cómodo”
                                </blockquote>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="swiper-scrollbar"></div>
            </div>
        </section>
        <script>
            const testimonials_security = new Swiper('.swiper', {
                // Optional parameters
                direction: 'horizontal',

                // And if we need scrollbar
                scrollbar: {
                    el: '.swiper-scrollbar',
                },
                breakpoints: {
                    0: {   // Para pantallas menores a 768px
                        slidesPerView: 1
                    },
                    768: { // Para pantallas de 768px hasta 1023px
                        slidesPerView: 2,
                        spaceBetween:10
                    },
                    1024: { // Para pantallas de 1024px en adelante
                        slidesPerView: 3,
                        spaceBetween:20
                    }
                }
            });
        </script>
        <section id="agenciarCita">
            <h2 class="subtitles">Únete a un nuevo <span class="resaltar">estilo de vida.</span></h2>
            <div>
                <p class="text">
                    Disfruta de el control total de tu hogar en cualquier lugar, sitio o dispositivo, de forma
                    inmediata,
                    cómoda y sencilla.
                </p>
                <button class="normal txt-botones" onclick="window.location.href = 'cita.html'">Agencia una
                    cita</button>
                </div>
        </section>
    </main>
</body>