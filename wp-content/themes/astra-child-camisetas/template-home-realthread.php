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
                        <h1>Diseña y pide camisetas<br>personalizadas online</h1>
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
                            <img src="<?php echo get_stylesheet_directory_uri(); ?>/images/icon-choose.svg" alt="Elegir">
                        </div>
                        <h3>Elige tus productos</h3>
                        <p>Selecciona de nuestra amplia gama de ropa de alta calidad</p>
                    </div>
                    <div class="step-card">
                        <div class="step-number">2</div>
                        <div class="step-icon">
                            <img src="<?php echo get_stylesheet_directory_uri(); ?>/images/icon-design.svg" alt="Diseñar">
                        </div>
                        <h3>Sube tu diseño</h3>
                        <p>Añade tu logo, ilustración o usa nuestras herramientas de diseño</p>
                    </div>
                    <div class="step-card">
                        <div class="step-number">3</div>
                        <div class="step-icon">
                            <img src="<?php echo get_stylesheet_directory_uri(); ?>/images/icon-approve.svg" alt="Aprobar">
                        </div>
                        <h3>Aprueba y pide</h3>
                        <p>Revisa tu diseño y realiza tu pedido</p>
                    </div>
                    <div class="step-card">
                        <div class="step-number">4</div>
                        <div class="step-icon">
                            <img src="<?php echo get_stylesheet_directory_uri(); ?>/images/icon-receive.svg" alt="Recibir">
                        </div>
                        <h3>Recibe y disfruta</h3>
                        <p>Te entregamos tus camisetas personalizadas en tu domicilio</p>
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
                <h2 class="section-title-center">Compra por categoría</h2>
                <div class="categories-large-grid">
                    <div class="category-large-card">
                        <div class="category-image-wrapper">
                            <img src="<?php echo get_stylesheet_directory_uri(); ?>/images/category-tshirts.jpg" alt="Camisetas">
                        </div>
                        <div class="category-overlay">
                            <h3>Camisetas</h3>
                            <a href="/categoria/camisetas" class="btn-category">Comprar Ahora</a>
                        </div>
                    </div>
                    <div class="category-large-card">
                        <div class="category-image-wrapper">
                            <img src="<?php echo get_stylesheet_directory_uri(); ?>/images/category-hoodies.jpg" alt="Sudaderas">
                        </div>
                        <div class="category-overlay">
                            <h3>Sudaderas</h3>
                            <a href="/categoria/sudaderas" class="btn-category">Comprar Ahora</a>
                        </div>
                    </div>
                    <div class="category-large-card">
                        <div class="category-image-wrapper">
                            <img src="<?php echo get_stylesheet_directory_uri(); ?>/images/category-caps.jpg" alt="Gorras">
                        </div>
                        <div class="category-overlay">
                            <h3>Gorras</h3>
                            <a href="/categoria/gorras" class="btn-category">Comprar Ahora</a>
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
                <h2 class="section-title-center">Camisetas y productos personalizados de alta calidad</h2>
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
                        <h2>Crea camisetas personalizadas con tu propio diseño</h2>
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
                        <img src="<?php echo get_stylesheet_directory_uri(); ?>/images/customer-1.jpg" alt="Cliente 1">
                    </div>
                    <div class="showcase-item">
                        <img src="<?php echo get_stylesheet_directory_uri(); ?>/images/customer-2.jpg" alt="Cliente 2">
                    </div>
                    <div class="showcase-item">
                        <img src="<?php echo get_stylesheet_directory_uri(); ?>/images/customer-3.jpg" alt="Cliente 3">
                    </div>
                    <div class="showcase-item">
                        <img src="<?php echo get_stylesheet_directory_uri(); ?>/images/customer-4.jpg" alt="Cliente 4">
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
                <div class="testimonials-grid">
                    <div class="testimonial-card">
                        <div class="stars-large">★★★★★</div>
                        <p class="testimonial-text">"La calidad es excepcional y el proceso fue increíblemente fácil. ¡Definitivamente volveré a pedir!"</p>
                        <div class="testimonial-author">
                            <strong>María González</strong>
                            <span>Dueña de Pequeño Negocio</span>
                        </div>
                    </div>
                    <div class="testimonial-card">
                        <div class="stars-large">★★★★★</div>
                        <p class="testimonial-text">"Tiempo de entrega rápido y excelente servicio al cliente. Muy recomendable para ropa personalizada."</p>
                        <div class="testimonial-author">
                            <strong>Carlos Martínez</strong>
                            <span>Organizador de Eventos</span>
                        </div>
                    </div>
                    <div class="testimonial-card">
                        <div class="stars-large">★★★★★</div>
                        <p class="testimonial-text">"La mejor calidad en camisetas personalizadas que he encontrado. Los colores son vibrantes y el ajuste es perfecto."</p>
                        <div class="testimonial-author">
                            <strong>Laura Rodríguez</strong>
                            <span>Manager de Equipo</span>
                        </div>
                    </div>
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

