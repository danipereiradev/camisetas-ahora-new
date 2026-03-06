<?php
/**
 * Template Name: Home - Estilo RealThread
 * Description: Plantilla home limpia con proceso de personalización paso a paso
 */

get_header(); ?>

<div id="primary" class="content-area home-realthread">
    <main id="main" class="site-main">
        
        <!-- Hero Section Clean -->
        <?php
        // Obtener imagen de fondo personalizada desde campos personalizados o usar default
        $hero_bg_image = get_post_meta(get_the_ID(), 'hero_background_image', true);
        $hero_bg_style = $hero_bg_image ? 'style="background-image: url(' . esc_url($hero_bg_image) . ');"' : '';
        ?>
        <section class="hero-realthread hero-with-bg" <?php echo $hero_bg_style; ?>>
            <div class="hero-overlay"></div>
            <div class="container">
                <div class="hero-inner">
                    <div class="hero-text-realthread">
                        <h1>Diseña y pide artículos<br>personalizados online</h1>
                        <p class="hero-subtitle">Crea ropa personalizada de alta calidad con tu logo o diseño</p>
                        <div class="hero-features">
                            <div class="feature-item">
                                <span class="feature-icon">✓</span>
                                <span>Sin Mínimos</span>
                            </div>
                            <div class="feature-item">
                                <span class="feature-icon">✓</span>
                                <span>Entrega Rápida</span>
                            </div>
                            <div class="feature-item">
                                <span class="feature-icon">✓</span>
                                <span>Envío a toda la península</span>
                            </div>
                        </div>
                        <a href="/shop" class="btn-hero-realthread">Empieza a Diseñar</a>
                    </div>
                    <div class="hero-products-realthread">
                        <div class="product-showcase product-showcase-single">
                            <div class="product-carousel">
                                <?php
                                // Mostrar productos solo si están configurados
                                $has_products = false;
                                
                                for ($i = 1; $i <= 4; $i++) {
                                    $product_image = get_post_meta(get_the_ID(), "carousel_product_{$i}_image", true);
                                    $product_title = get_post_meta(get_the_ID(), "carousel_product_{$i}_title", true);
                                    $product_link = get_post_meta(get_the_ID(), "carousel_product_{$i}_link", true);
                                    
                                    // Solo mostrar si hay imagen configurada
                                    if (!empty($product_image)) {
                                        $has_products = true;
                                        $title = !empty($product_title) ? $product_title : 'Producto Personalizado';
                                        $link = !empty($product_link) ? $product_link : '#';
                                        
                                        $active_class = ($i === 1) ? 'active' : '';
                                        ?>
                                        <a href="<?php echo esc_url($link); ?>" class="product-image-container carousel-item <?php echo $active_class; ?>">
                                            <img src="<?php echo esc_url($product_image); ?>" alt="<?php echo esc_attr($title); ?>">
                                            <h3 class="product-title-hero"><?php echo esc_html($title); ?></h3>
                                        </a>
                                        <?php
                                    }
                                }
                                
                                // Mensaje si no hay productos configurados
                                if (!$has_products) {
                                    ?>
                                    <div class="carousel-placeholder" style="text-align: center; padding: 3rem; color: rgba(255,255,255,0.5);">
                                        <p style="margin: 0;">Configura los productos del carousel desde el editor de página</p>
                                    </div>
                                    <?php
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- How It Works Steps -->
        <?php
        $steps_bg_image = get_post_meta(get_the_ID(), 'steps_background_image', true);
        $steps_bg_style = $steps_bg_image ? 'style="background-image: url(' . esc_url($steps_bg_image) . ');"' : '';
        ?>
        <section class="steps-section section-with-bg" <?php echo $steps_bg_style; ?>>
            <div class="section-overlay"></div>
            <div class="container">
                <h2 class="section-title-center">Cómo funciona</h2>
                <div class="steps-grid">
                    <div class="step-card">
                        <div class="step-number">1</div>
                        <div class="step-icon">
                            <img src="<?php echo home_url('/horultoo/2026/03/camisetas-1.jpg'); ?>" alt="Elegir">
                        </div>
                        <h3>Elige tus productos</h3>
                        <p>Selecciona de nuestra amplia gama de ropa de alta calidad</p>
                    </div>
                    <div class="step-card">
                        <div class="step-number">2</div>
                        <div class="step-icon">
                            <img src="<?php echo home_url('/horultoo/2026/03/camisetas-2.png'); ?>" alt="Diseñar">
                        </div>
                        <h3>Sube tu diseño</h3>
                        <p>Añade tu logo, ilustración o usa nuestras herramientas de diseño</p>
                    </div>
                    <div class="step-card">
                        <div class="step-number">3</div>
                        <div class="step-icon">
                            <img src="<?php echo home_url('/horultoo/2026/03/camisetas-3.png'); ?>" alt="Aprobar">
                        </div>
                        <h3>Aprueba y pide</h3>
                        <p>Revisa tu diseño y realiza tu pedido</p>
                    </div>
                    <div class="step-card">
                        <div class="step-number">4</div>
                        <div class="step-icon">
                            <img src="<?php echo home_url('/horultoo/2026/03/camisetas-4.png'); ?>" alt="Recibir">
                        </div>
                        <h3>Recibe y disfruta</h3>
                        <p>Te entregamos tus artículos personalizados en tu domicilio</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Product Categories with Images -->
        <?php
        $categories_bg_image = get_post_meta(get_the_ID(), 'categories_background_image', true);
        $categories_bg_style = $categories_bg_image ? 'style="background-image: url(' . esc_url($categories_bg_image) . ');"' : '';
        ?>
        <section class="categories-showcase section-with-bg" <?php echo $categories_bg_style; ?>>
            <div class="section-overlay"></div>
            <div class="container">
                <h2 class="section-title-center">Elige entre nuestras categorias</h2>
                <?php
                $category_colors = [
                    '#e63232', '#2a52be', '#ffffff', '#f5c000', '#1a2744',
                    '#00aacc', '#6b1020', '#a8a8a8', '#7b2d8b', '#e87722',
                    '#f4a7b9', '#e8004d', '#2e8b2e', '#1a5c2a', '#111111'
                ];
                $color_borders = [
                    '', '', '1px solid #ccc', '', '', '', '', '', '', '', '', '', '', '', ''
                ];
                ?>
                <div class="categories-large-grid">
                    <div class="category-card-wrapper">
                        <div class="category-large-card">
                            <div class="category-image-wrapper">
                                <img src="<?php echo home_url('/horultoo/2026/03/categoria-camisetas-chica-skatepark.png'); ?>" alt="Camisetas">
                            </div>
                            <div class="category-overlay">
                                <h3>Camisetas</h3>
                                <a href="/categoria/camisetas" class="btn-category">Comprar Ahora</a>
                            </div>
                        </div>
                        
                    </div>
                    <div class="category-card-wrapper">
                        <div class="category-large-card">
                            <div class="category-image-wrapper">
                                <img src="<?php echo home_url('horultoo/2026/03/categoria-sudaderas-pareja-bici.png'); ?>" alt="Sudaderas">
                            </div>
                            <div class="category-overlay">
                                <h3>Sudaderas</h3>
                                <a href="/categoria/sudaderas" class="btn-category">Comprar Ahora</a>
                            </div>
                        </div>
                        
                    </div>
                    <div class="category-card-wrapper">
                        <div class="category-large-card">
                            <div class="category-image-wrapper">
                                <img src="<?php echo home_url('/horultoo/2026/03/categoria-tote.png'); ?>" alt="Gorras">
                            </div>
                            <div class="category-overlay">
                                <h3>Totebags</h3>
                                <a href="/categoria/gorras" class="btn-category">Comprar Ahora</a>
                            </div>
                        </div>
                       
                    </div>
                </div>
            </div>
        </section>

        <!-- Featured Products Grid -->
        <?php
        $products_bg_image = get_post_meta(get_the_ID(), 'products_background_image', true);
        $products_bg_style = $products_bg_image ? 'style="background-image: url(' . esc_url($products_bg_image) . ');"' : '';
        ?>
        <section class="featured-products-realthread section-with-bg" <?php echo $products_bg_style; ?>>
            <div class="section-overlay"></div>
            <div class="container">
                <h2 class="section-title-center">Nuestros productos mas solicitados</h2>
                <div class="products-grid-realthread">
                    <?php
                    $args = array(
                        'post_type' => 'product',
                        'posts_per_page' => 8,
                        'meta_key' => 'total_sales',
                        'orderby' => 'meta_value_num',
                        'order' => 'DESC',
                    );
                    $products = new WP_Query($args);
                    
                    if ($products->have_posts()) :
                        while ($products->have_posts()) : $products->the_post();
                            global $product;
                            ?>
                            <div class="product-card-realthread">
                                <a href="<?php the_permalink(); ?>">
                                    <div class="product-image-wrapper">
                                        <?php echo woocommerce_get_product_thumbnail('medium'); ?>
                                        <?php if ($product->is_on_sale()) : ?>
                                            <span class="sale-badge">Oferta</span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="product-details">
                                        <h3><?php the_title(); ?></h3>
                                        <div class="product-rating">
                                            <?php echo wc_get_rating_html($product->get_average_rating()); ?>
                                        </div>
                                        <div class="product-price-info">
                                            <span class="price"><?php echo $product->get_price_html(); ?></span>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            <?php
                        endwhile;
                        wp_reset_postdata();
                    endif;
                    ?>
                </div>
            </div>
        </section>

        <!-- Design Tools Section -->
        <?php
        $design_bg_image = get_post_meta(get_the_ID(), 'design_background_image', true);
        $design_bg_style = $design_bg_image ? 'style="background-image: url(' . esc_url($design_bg_image) . ');"' : '';
        ?>
        <section class="design-tools-section section-with-bg" <?php echo $design_bg_style; ?>>
            <div class="section-overlay"></div>
            <div class="container">
                <div class="design-content">
                    <div class="design-image">
                        <img src="<?php echo get_stylesheet_directory_uri(); ?>/images/design-tool-preview.jpg" alt="Herramienta de diseño">
                    </div>
                    <div class="design-text">
                        <h2>Crea artículos personalizados con tu propio diseño</h2>
                        <p>Usa nuestra herramienta de diseño fácil de usar para dar vida a tus ideas. Sube tu logo, añade texto o elige entre miles de elementos de diseño.</p>
                        <ul class="design-features">
                            <li>Interfaz de diseño fácil de usar</li>
                            <li>Precios y mockups instantáneos</li>
                            <li>Impresión de calidad profesional</li>
                            <li>No necesitas experiencia en diseño</li>
                        </ul>
                        <a href="/design-tool" class="btn-design">Prueba la Herramienta</a>
                    </div>
                </div>
            </div>
        </section>

        <!-- Customer Showcase -->
        <?php
        $customer_bg_image = get_post_meta(get_the_ID(), 'customer_background_image', true);
        $customer_bg_style = $customer_bg_image ? 'style="background-image: url(' . esc_url($customer_bg_image) . ');"' : '';
        ?>
        <section class="customer-showcase section-with-bg" <?php echo $customer_bg_style; ?>>
            <div class="section-overlay"></div>
            <div class="container">
                <h2 class="section-title-center">Creaciones de nuestros clientes</h2>
                <p class="section-subtitle">Mira lo que nuestros clientes han creado</p>
                <div class="showcase-grid">
                    <div class="showcase-item">
                        <img src="<?php echo home_url('/horultoo/2026/03/clientes-disenos-1.png'); ?>" alt="Cliente 1">
                    </div>
                    <div class="showcase-item">
                        <img src="<?php echo home_url('/horultoo/2026/03/clientes-disenos-2.png'); ?>" alt="Cliente 2">
                    </div>
                    <div class="showcase-item">
                        <img src="<?php echo home_url('/horultoo/2026/03/clientes-disenos-3.png'); ?>" alt="Cliente 3">
                    </div>
                </div>
            </div>
        </section>

        <!-- Testimonials -->
        <?php
        $testimonials_bg_image = get_post_meta(get_the_ID(), 'testimonials_background_image', true);
        $testimonials_bg_style = $testimonials_bg_image ? 'style="background-image: url(' . esc_url($testimonials_bg_image) . ');"' : '';
        ?>
        <section class="testimonials-realthread section-with-bg" <?php echo $testimonials_bg_style; ?>>
            <div class="section-overlay"></div>
            <div class="container">
                <h2 class="section-title-center">Lo que dicen nuestros clientes</h2>
                
                <?php
                require_once get_stylesheet_directory() . '/data/resenas.php';
                $resenas_json = json_encode($resenas, JSON_UNESCAPED_UNICODE);
                ?>
                <?php
                $per_page = 3;
                $pages    = array_chunk($resenas, $per_page);
                ?>
                <div class="testimonials-carousel-wrapper">
                    <?php foreach ($pages as $p => $grupo): ?>
                    <div class="testimonials-page<?php echo $p === 0 ? ' active' : ''; ?>">
                        <div class="testimonials-grid">
                            <?php foreach ($grupo as $resena): ?>
                            <div class="testimonial-card">
                                <div class="testimonial-header">
                                    <div class="testimonial-avatar"><?php echo esc_html(mb_substr($resena['nombre'], 0, 1)); ?></div>
                                    <div class="testimonial-meta">
                                        <strong><?php echo esc_html($resena['nombre']); ?></strong>
                                        <span class="testimonial-source">Reseña de Google</span>
                                    </div>
                                    <div class="testimonial-google-icon">
                                        <svg width="18" height="18" viewBox="0 0 24 24"><path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/><path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/><path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l3.66-2.84z"/><path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/></svg>
                                    </div>
                                </div>
                                <div class="stars-large">★★★★★</div>
                                <p class="testimonial-text"><?php echo esc_html($resena['texto']); ?></p>
                                <span class="testimonial-time"><?php echo esc_html($resena['tiempo']); ?></span>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <div class="testimonials-dots">
                    <?php foreach ($pages as $p => $grupo): ?>
                        <span class="testimonials-dot<?php echo $p === 0 ? ' active' : ''; ?>" data-index="<?php echo $p; ?>"></span>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

        <!-- Trust Badges -->
        <?php
        $trust_bg_image = get_post_meta(get_the_ID(), 'trust_background_image', true);
        $trust_bg_style = $trust_bg_image ? 'style="background-image: url(' . esc_url($trust_bg_image) . ');"' : '';
        ?>
        <section class="trust-section section-with-bg" <?php echo $trust_bg_style; ?>>
            <div class="section-overlay"></div>
            <div class="container">
                <div class="trust-grid">
                    <div class="trust-item">
                        <div class="trust-icon">🏆</div>
                        <div class="trust-content">
                            <h4>Calidad Premium</h4>
                            <p>Materiales de primera</p>
                        </div>
                    </div>
                    <div class="trust-item">
                        <div class="trust-icon">⚡</div>
                        <div class="trust-content">
                            <h4>Producción Rápida</h4>
                            <p>Entrega en 3-5 días</p>
                        </div>
                    </div>
                    <div class="trust-item">
                        <div class="trust-icon">💯</div>
                        <div class="trust-content">
                            <h4>Satisfacción Garantizada</h4>
                            <p>Devolución del 100%</p>
                        </div>
                    </div>
                    <div class="trust-item">
                        <div class="trust-icon">🌍</div>
                        <div class="trust-content">
                            <h4>Eco-Friendly</h4>
                            <p>Impresión sostenible</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- FAQ Section -->
        <?php
        $faq_bg_image = get_post_meta(get_the_ID(), 'faq_background_image', true);
        $faq_bg_style = $faq_bg_image ? 'style="background-image: url(' . esc_url($faq_bg_image) . ');"' : '';
        ?>
        <section class="faq-section section-with-bg" <?php echo $faq_bg_style; ?>>
            <div class="section-overlay"></div>
            <div class="container">
                <h2 class="section-title-center">Preguntas Frecuentes</h2>
                <div class="faq-grid">
                    <div class="faq-item">
                        <h4>¿Cuál es la cantidad mínima de pedido?</h4>
                        <p>¡Sin mínimos! Pide desde 1 camiseta o las que necesites.</p>
                    </div>
                    <div class="faq-item">
                        <h4>¿Cuánto tarda la producción?</h4>
                        <p>La mayoría de pedidos se envían en 3-5 días laborables tras la aprobación.</p>
                    </div>
                    <div class="faq-item">
                        <h4>¿Ofrecéis envío gratis?</h4>
                        <p>¡Sí! Envío gratis en pedidos superiores a 50€ en toda España.</p>
                    </div>
                    <div class="faq-item">
                        <h4>¿Y si necesito ayuda con mi diseño?</h4>
                        <p>¡Nuestro equipo de diseño está aquí para ayudarte! Contáctanos para asistencia gratuita.</p>
                    </div>
                </div>
            </div>
        </section>

    </main>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {

    // Carousel de reseñas (páginas de 3)
    const pages   = document.querySelectorAll('.testimonials-page');
    const dots    = document.querySelectorAll('.testimonials-dot');
    let current   = 0;
    let autoplay;

    function showPage(index) {
        pages[current].classList.remove('active');
        dots[current].classList.remove('active');
        current = (index + pages.length) % pages.length;
        pages[current].classList.add('active');
        dots[current].classList.add('active');
    }

    function startAutoplay() {
        autoplay = setInterval(() => showPage(current + 1), 6000);
    }

    dots.forEach(function(dot, i) {
        dot.addEventListener('click', function() {
            clearInterval(autoplay);
            showPage(i);
            startAutoplay();
        });
    });

    if (pages.length > 1) startAutoplay();

    // Carousel de productos
    const carousel = document.querySelector('.product-carousel');
    if (!carousel) return;
    
    const items = carousel.querySelectorAll('.carousel-item');
    if (items.length <= 1) return;
    
    // Buscar el H1 del hero
    const heroH1 = document.querySelector('.hero-text-realthread h1');
    const heroButton = document.querySelector('.btn-hero-realthread');
    
    let currentIndex = 0;
    let isTransitioning = false;
    let intervalId = null;
    let isPaused = false;
    
    function showNextItem() {
        if (isTransitioning || isPaused) return;
        
        isTransitioning = true;
        
        // Remover active del item actual y forzar reflow
        const currentItem = items[currentIndex];
        currentItem.classList.remove('active');
        
        // Añadir efecto pulse al H1 y botón
        if (heroH1) {
            heroH1.classList.add('pulse');
            setTimeout(() => {
                heroH1.classList.remove('pulse');
            }, 500);
        }
        
        if (heroButton) {
            heroButton.classList.add('pulse');
            setTimeout(() => {
                heroButton.classList.remove('pulse');
            }, 500);
        }
        
        // Pequeño delay para asegurar que la animación se reinicia
        setTimeout(() => {
            // Incrementar índice
            currentIndex = (currentIndex + 1) % items.length;
            
            // Añadir active al nuevo item
            items[currentIndex].classList.add('active');
            
            // Reset del flag después de la animación
            setTimeout(() => {
                isTransitioning = false;
            }, 300);
        }, 50);
    }
    
    function startCarousel() {
        if (intervalId) return;
        intervalId = setInterval(showNextItem, 4000);
    }
    
    function stopCarousel() {
        if (intervalId) {
            clearInterval(intervalId);
            intervalId = null;
        }
    }
    
    // Pausar cuando hover
    carousel.addEventListener('mouseenter', function() {
        isPaused = true;
        stopCarousel();
    });
    
    // Reanudar cuando sale el hover
    carousel.addEventListener('mouseleave', function() {
        isPaused = false;
        startCarousel();
    });
    
    // Iniciar carousel
    startCarousel();
});
</script>

<?php get_footer(); ?>

