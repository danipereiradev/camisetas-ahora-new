<?php
/**
 * Template Name: Home - Estilo SpreadShirt
 * Description: Plantilla home minimalista con hero grande y grid de productos
 */

get_header(); ?>

<div id="primary" class="content-area home-spreadshirt">
    <main id="main" class="site-main">
        
        <!-- Hero Section -->
        <section class="hero-spreadshirt">
            <div class="hero-content">
                <div class="hero-text">
                    <span class="hero-label">¡LO PERSONALIZAMOS!</span>
                    <h1 class="hero-title">CAMISETAS<br>PERSONALIZADAS<br><span class="highlight">SUBLIMACIÓN A COLOR</span></h1>
                    <p class="hero-description">Imprimimos tu diseño en camisetas de alta calidad con tecnología de sublimación</p>
                    <div class="hero-buttons">
                        <a href="/shop" class="btn-primary">Diseña tu camiseta</a>
                        <a href="/trabajos" class="btn-secondary">Ver galería</a>
                    </div>
                </div>
                <div class="hero-image">
                    <img src="<?php echo get_stylesheet_directory_uri(); ?>/images/hero-couple.jpg" alt="Pareja con camisetas personalizadas">
                </div>
            </div>
        </section>

        <!-- Categorías Destacadas -->
        <section class="featured-categories">
            <div class="container">
                <h2 class="section-title">LOS MÁS VENDIDOS</h2>
                <div class="categories-grid">
                    <?php
                    $featured_categories = array('hombre', 'mujer', 'nino', 'personalizado');
                    foreach ($featured_categories as $cat_slug) {
                        $category = get_term_by('slug', $cat_slug, 'product_cat');
                        if ($category) {
                            $thumbnail_id = get_term_meta($category->term_id, 'thumbnail_id', true);
                            $image = wp_get_attachment_url($thumbnail_id);
                            ?>
                            <a href="<?php echo get_term_link($category); ?>" class="category-card">
                                <div class="category-image">
                                    <?php if ($image): ?>
                                        <img src="<?php echo esc_url($image); ?>" alt="<?php echo esc_attr($category->name); ?>">
                                    <?php else: ?>
                                        <div class="placeholder-image"></div>
                                    <?php endif; ?>
                                </div>
                                <h3 class="category-name"><?php echo esc_html($category->name); ?></h3>
                            </a>
                            <?php
                        }
                    }
                    ?>
                </div>
            </div>
        </section>

        <!-- Grid de Productos Destacados -->
        <section class="products-grid-section">
            <div class="container">
                <h2 class="section-title">TODOS LOS ESTILOS Y COMPRA EN 11</h2>
                <div class="products-grid-spreadshirt">
                    <?php
                    $args = array(
                        'post_type' => 'product',
                        'posts_per_page' => 9,
                        'orderby' => 'date',
                        'order' => 'DESC',
                    );
                    $products = new WP_Query($args);
                    
                    if ($products->have_posts()) :
                        while ($products->have_posts()) : $products->the_post();
                            global $product;
                            ?>
                            <div class="product-card-spreadshirt">
                                <a href="<?php the_permalink(); ?>">
                                    <div class="product-image">
                                        <?php echo woocommerce_get_product_thumbnail(); ?>
                                    </div>
                                    <div class="product-info">
                                        <h3 class="product-title"><?php the_title(); ?></h3>
                                        <div class="product-colors">
                                            <?php
                                            // Mostrar variaciones de color si existen
                                            if ($product->is_type('variable')) {
                                                $available_variations = $product->get_available_variations();
                                                $color_count = min(count($available_variations), 5);
                                                for ($i = 0; $i < $color_count; $i++) {
                                                    echo '<span class="color-dot"></span>';
                                                }
                                            }
                                            ?>
                                        </div>
                                        <div class="product-price">
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
                <div class="view-all-container">
                    <a href="/shop" class="btn-view-all">Ver todos los productos</a>
                </div>
            </div>
        </section>

        <!-- Sección Camisetas para Mujer -->
        <section class="gender-section women-section">
            <div class="container">
                <h2 class="section-title">CAMISETAS PARA MUJER</h2>
                <div class="products-row">
                    <?php
                    $args_women = array(
                        'post_type' => 'product',
                        'posts_per_page' => 6,
                        'tax_query' => array(
                            array(
                                'taxonomy' => 'product_cat',
                                'field' => 'slug',
                                'terms' => 'mujer',
                            ),
                        ),
                    );
                    $products_women = new WP_Query($args_women);
                    
                    if ($products_women->have_posts()) :
                        while ($products_women->have_posts()) : $products_women->the_post();
                            global $product;
                            ?>
                            <div class="product-card-row">
                                <a href="<?php the_permalink(); ?>">
                                    <?php echo woocommerce_get_product_thumbnail('medium'); ?>
                                    <h4><?php the_title(); ?></h4>
                                    <p class="price"><?php echo $product->get_price_html(); ?></p>
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

        <!-- Banner de Promoción -->
        <section class="promo-banner">
            <div class="promo-content">
                <div class="promo-image">
                    <img src="<?php echo get_stylesheet_directory_uri(); ?>/images/promo-design.jpg" alt="Diseño personalizado">
                </div>
                <div class="promo-text">
                    <h2>PERSONALIZA TU CAMISETA AHORA</h2>
                    <p>Sube tu diseño o elige de nuestra galería</p>
                    <a href="/customizar" class="btn-promo">Empieza a diseñar</a>
                </div>
            </div>
        </section>

        <!-- Reseñas y Confianza -->
        <section class="reviews-section">
            <div class="container">
                <h2 class="section-title">OPINIONES DE NUESTROS CLIENTES</h2>
                <div class="reviews-grid">
                    <div class="review-card">
                        <div class="stars">★★★★★</div>
                        <p class="review-text">"Calidad excepcional y entrega rápida. ¡Muy recomendado!"</p>
                        <p class="review-author">- María G.</p>
                    </div>
                    <div class="review-card">
                        <div class="stars">★★★★★</div>
                        <p class="review-text">"El diseño quedó perfecto, exactamente como lo imaginé."</p>
                        <p class="review-author">- Carlos R.</p>
                    </div>
                    <div class="review-card">
                        <div class="stars">★★★★★</div>
                        <p class="review-text">"Excelente atención al cliente y productos de primera."</p>
                        <p class="review-author">- Laura M.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Footer Info -->
        <section class="info-section">
            <div class="container">
                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-icon">🚚</div>
                        <h3>Envío Gratis</h3>
                        <p>En pedidos superiores a $50</p>
                    </div>
                    <div class="info-item">
                        <div class="info-icon">✨</div>
                        <h3>Calidad Premium</h3>
                        <p>Materiales de primera calidad</p>
                    </div>
                    <div class="info-item">
                        <div class="info-icon">🎨</div>
                        <h3>100% Personalizable</h3>
                        <p>Tu diseño, tu estilo</p>
                    </div>
                    <div class="info-item">
                        <div class="info-icon">🔒</div>
                        <h3>Pago Seguro</h3>
                        <p>Protección garantizada</p>
                    </div>
                </div>
            </div>
        </section>

    </main>
</div>

<?php get_footer(); ?>
