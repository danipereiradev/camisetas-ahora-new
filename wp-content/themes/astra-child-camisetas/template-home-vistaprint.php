<?php
/**
 * Template Name: Home - Estilo Vistaprint
 * Description: Plantilla home con grid compacto de productos y banner destacado
 */

get_header(); ?>

<div id="primary" class="content-area home-vistaprint">
    <main id="main" class="site-main">
        
        <!-- Top Banner Strip -->
        <section class="top-banner-strip">
            <div class="container">
                <p><strong>Camisetas personalizadas desde $299</strong> | Envío gratis en pedidos +$800 | <a href="/ofertas">Ver ofertas</a></p>
            </div>
        </section>

        <!-- Hero Banner Large -->
        <section class="hero-vistaprint">
            <div class="hero-slider">
                <div class="hero-slide active">
                    <div class="hero-bg">
                        <img src="<?php echo get_stylesheet_directory_uri(); ?>/images/hero-tshirt-world.jpg" alt="Camiseta personalizada">
                    </div>
                    <div class="hero-overlay">
                        <div class="container">
                            <div class="hero-content-vistaprint">
                                <span class="hero-badge">PERSONALIZACIÓN</span>
                                <h1>Camisetas personalizadas</h1>
                                <p class="hero-tagline">Dale vida a tu diseño con nuestras camisetas de alta calidad</p>
                                <a href="/customizar" class="btn-hero-vistaprint">Personalizar ahora</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Quick Categories -->
        <section class="quick-categories">
            <div class="container">
                <div class="categories-row">
                    <a href="/categoria/camisetas" class="quick-cat-card">
                        <div class="quick-cat-icon">
                            <img src="<?php echo get_stylesheet_directory_uri(); ?>/images/icon-tshirt.svg" alt="Camisetas">
                        </div>
                        <span>Camisetas</span>
                    </a>
                    <a href="/categoria/polos" class="quick-cat-card">
                        <div class="quick-cat-icon">
                            <img src="<?php echo get_stylesheet_directory_uri(); ?>/images/icon-polo.svg" alt="Polos">
                        </div>
                        <span>Polos</span>
                    </a>
                    <a href="/categoria/sudaderas" class="quick-cat-card">
                        <div class="quick-cat-icon">
                            <img src="<?php echo get_stylesheet_directory_uri(); ?>/images/icon-hoodie.svg" alt="Sudaderas">
                        </div>
                        <span>Sudaderas</span>
                    </a>
                </div>
            </div>
        </section>

        <!-- Main Title -->
        <section class="main-title-section">
            <div class="container">
                <h2 class="main-title">Nuestra selección de camisetas personalizadas</h2>
            </div>
        </section>

        <!-- Products Compact Grid -->
        <section class="products-compact-grid">
            <div class="container">
                <div class="products-grid-vistaprint">
                    <?php
                    $args = array(
                        'post_type' => 'product',
                        'posts_per_page' => 24,
                        'orderby' => 'menu_order',
                        'order' => 'ASC',
                    );
                    $products = new WP_Query($args);
                    
                    if ($products->have_posts()) :
                        while ($products->have_posts()) : $products->the_post();
                            global $product;
                            ?>
                            <div class="product-card-vistaprint">
                                <a href="<?php the_permalink(); ?>">
                                    <div class="product-image-compact">
                                        <?php echo woocommerce_get_product_thumbnail('medium'); ?>
                                        <?php if ($product->is_on_sale()) : ?>
                                            <span class="discount-badge">
                                                <?php
                                                $percentage = round((($product->get_regular_price() - $product->get_sale_price()) / $product->get_regular_price()) * 100);
                                                echo '-' . $percentage . '%';
                                                ?>
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="product-info-compact">
                                        <h3 class="product-name"><?php the_title(); ?></h3>
                                        <div class="product-rating-compact">
                                            <?php
                                            $rating = $product->get_average_rating();
                                            if ($rating > 0) {
                                                echo '<span class="stars-small">' . wc_get_rating_html($rating) . '</span>';
                                                echo '<span class="rating-count">(' . $product->get_rating_count() . ')</span>';
                                            }
                                            ?>
                                        </div>
                                        <div class="product-colors-compact">
                                            <?php
                                            if ($product->is_type('variable')) {
                                                echo '<span class="colors-available">+' . count($product->get_available_variations()) . ' colores</span>';
                                            }
                                            ?>
                                        </div>
                                        <div class="product-price-compact">
                                            <?php echo $product->get_price_html(); ?>
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
                
                <div class="load-more-container">
                    <a href="/shop" class="btn-load-more">Ver más productos</a>
                </div>
            </div>
        </section>

        <!-- Banner Destacado de Personalización -->
        <section class="custom-banner-section">
            <div class="container">
                <div class="custom-banner">
                    <div class="custom-banner-image">
                        <img src="<?php echo get_stylesheet_directory_uri(); ?>/images/custom-process.jpg" alt="Proceso de personalización">
                    </div>
                    <div class="custom-banner-content">
                        <h2>¿Cómo hacer camisetas personalizadas?</h2>
                        <div class="custom-steps">
                            <div class="custom-step">
                                <span class="step-num">1</span>
                                <p>Elige tu camiseta favorita</p>
                            </div>
                            <div class="custom-step">
                                <span class="step-num">2</span>
                                <p>Sube tu diseño o crea uno nuevo</p>
                            </div>
                            <div class="custom-step">
                                <span class="step-num">3</span>
                                <p>Personaliza colores y tamaños</p>
                            </div>
                            <div class="custom-step">
                                <span class="step-num">4</span>
                                <p>¡Recíbelo en tu domicilio!</p>
                            </div>
                        </div>
                        <a href="/como-funciona" class="btn-learn-more">Aprende más</a>
                    </div>
                </div>
            </div>
        </section>

        <!-- Inspiración y Galería -->
        <section class="inspiration-section">
            <div class="container">
                <h2 class="section-title-center">Inspírate con nuestros plantillas personalizadas</h2>
                <p class="section-description">Explora nuestras ideas y encuentra la inspiración perfecta para tu próximo proyecto</p>
                
                <div class="inspiration-grid">
                    <?php
                    // Mostrar productos destacados o posts de galería
                    $args_gallery = array(
                        'post_type' => 'product',
                        'posts_per_page' => 10,
                        'meta_key' => '_featured',
                        'meta_value' => 'yes',
                    );
                    $gallery_products = new WP_Query($args_gallery);
                    
                    if ($gallery_products->have_posts()) :
                        $counter = 0;
                        while ($gallery_products->have_posts()) : $gallery_products->the_post();
                            global $product;
                            $counter++;
                            $grid_class = ($counter == 1 || $counter == 6) ? 'grid-large' : 'grid-small';
                            ?>
                            <div class="inspiration-item <?php echo $grid_class; ?>">
                                <a href="<?php the_permalink(); ?>">
                                    <?php echo woocommerce_get_product_thumbnail('large'); ?>
                                    <div class="inspiration-overlay">
                                        <h4><?php the_title(); ?></h4>
                                        <span class="view-design">Ver diseño →</span>
                                    </div>
                                </a>
                            </div>
                            <?php
                            if ($counter >= 10) break;
                        endwhile;
                        wp_reset_postdata();
                    endif;
                    ?>
                </div>
                
                <div class="view-gallery-container">
                    <a href="/galeria" class="btn-view-gallery">Ver toda la galería</a>
                </div>
            </div>
        </section>

        <!-- Beneficios y Garantías -->
        <section class="benefits-section">
            <div class="container">
                <h2 class="section-title-center">¿Por qué elegirnos?</h2>
                <div class="benefits-grid">
                    <div class="benefit-card">
                        <div class="benefit-icon">
                            <img src="<?php echo get_stylesheet_directory_uri(); ?>/images/icon-quality.svg" alt="Calidad">
                        </div>
                        <h3>Calidad garantizada</h3>
                        <p>Utilizamos solo materiales premium y las mejores técnicas de impresión</p>
                    </div>
                    <div class="benefit-card">
                        <div class="benefit-icon">
                            <img src="<?php echo get_stylesheet_directory_uri(); ?>/images/icon-fast.svg" alt="Rápido">
                        </div>
                        <h3>Entrega rápida</h3>
                        <p>Producción en 24-48 horas y envío express disponible</p>
                    </div>
                    <div class="benefit-card">
                        <div class="benefit-icon">
                            <img src="<?php echo get_stylesheet_directory_uri(); ?>/images/icon-design.svg" alt="Diseño">
                        </div>
                        <h3>Diseño fácil</h3>
                        <p>Herramienta intuitiva o servicio de diseño profesional gratuito</p>
                    </div>
                    <div class="benefit-card">
                        <div class="benefit-icon">
                            <img src="<?php echo get_stylesheet_directory_uri(); ?>/images/icon-price.svg" alt="Precio">
                        </div>
                        <h3>Mejor precio</h3>
                        <p>Precios competitivos y descuentos por volumen</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Testimonios Compactos -->
        <section class="testimonials-vistaprint">
            <div class="container">
                <h2 class="section-title-center">Nuestros clientes ya personalizaron sus Sur les Real</h2>
                <div class="testimonials-compact">
                    <div class="testimonial-compact">
                        <div class="testimonial-header">
                            <div class="testimonial-avatar">
                                <img src="<?php echo get_stylesheet_directory_uri(); ?>/images/avatar-1.jpg" alt="Cliente">
                            </div>
                            <div class="testimonial-info">
                                <strong>Ana María González</strong>
                                <div class="stars-compact">★★★★★</div>
                            </div>
                        </div>
                        <p>"Excelente calidad y el diseño quedó perfecto. Muy recomendable para eventos corporativos."</p>
                    </div>
                    
                    <div class="testimonial-compact">
                        <div class="testimonial-header">
                            <div class="testimonial-avatar">
                                <img src="<?php echo get_stylesheet_directory_uri(); ?>/images/avatar-2.jpg" alt="Cliente">
                            </div>
                            <div class="testimonial-info">
                                <strong>Roberto Martínez</strong>
                                <div class="stars-compact">★★★★★</div>
                            </div>
                        </div>
                        <p>"Rápido, económico y con muy buenos acabados. Ya he hecho varios pedidos y siempre perfecto."</p>
                    </div>
                    
                    <div class="testimonial-compact">
                        <div class="testimonial-header">
                            <div class="testimonial-avatar">
                                <img src="<?php echo get_stylesheet_directory_uri(); ?>/images/avatar-3.jpg" alt="Cliente">
                            </div>
                            <div class="testimonial-info">
                                <strong>Carmen Ruiz</strong>
                                <div class="stars-compact">★★★★★</div>
                            </div>
                        </div>
                        <p>"La atención al cliente es increíble. Me ayudaron con mi diseño y el resultado superó mis expectativas."</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- FAQs Compacto -->
        <section class="faq-vistaprint">
            <div class="container">
                <h2 class="section-title-center">Preguntas frecuentes sobre las camisetas personalizadas</h2>
                <div class="faq-columns">
                    <div class="faq-column">
                        <div class="faq-item-vistaprint">
                            <h4>¿Cuál es el pedido mínimo?</h4>
                            <p>No hay pedido mínimo. Puedes pedir desde 1 camiseta.</p>
                        </div>
                        <div class="faq-item-vistaprint">
                            <h4>¿Qué tipo de impresión utilizan?</h4>
                            <p>Utilizamos sublimación, serigrafía y DTG según el diseño y cantidad.</p>
                        </div>
                        <div class="faq-item-vistaprint">
                            <h4>¿Cuánto tarda el envío?</h4>
                            <p>Producción 24-48h + envío 3-5 días laborables.</p>
                        </div>
                    </div>
                    <div class="faq-column">
                        <div class="faq-item-vistaprint">
                            <h4>¿Puedo ver una prueba antes de producir?</h4>
                            <p>Sí, recibirás una prueba digital para aprobar antes de la producción.</p>
                        </div>
                        <div class="faq-item-vistaprint">
                            <h4>¿Ofrecen descuentos por cantidad?</h4>
                            <p>Sí, ofrecemos descuentos progresivos según la cantidad pedida.</p>
                        </div>
                        <div class="faq-item-vistaprint">
                            <h4>¿Qué pasa si no me gusta el resultado?</h4>
                            <p>Garantía de satisfacción 100%. Si no te gusta, te devolvemos el dinero.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Bottom CTA Banner -->
        <section class="bottom-cta">
            <div class="container">
                <div class="cta-content">
                    <h2>¿Listo para crear tu camiseta personalizada?</h2>
                    <p>Comienza ahora y recibe tu pedido en pocos días</p>
                    <a href="/customizar" class="btn-cta-large">Empezar a diseñar</a>
                </div>
            </div>
        </section>

    </main>
</div>

<?php get_footer(); ?>
