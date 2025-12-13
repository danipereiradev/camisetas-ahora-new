<?php
/**
 * Template Name: Trabajos
 * Template Post Type: page
 *
 * @package Astra Child
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

get_header(); ?>

<div id="primary" class="content-area primary">
    <main id="main" class="site-main">
        
        <?php while ( have_posts() ) : the_post(); ?>
            
            <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                
                <header class="entry-header">
                    <?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
                </header>

                <div class="entry-content">
                    <?php the_content(); ?>
                    
                    <!-- Grid de Servicios -->
                    <div class="servicios-grid-container">
                        
                        <!-- Servicio 1: Serigrafía -->
                        <a href="#" class="servicio-card">
                            <div class="servicio-icon">
                                <img src="<?php echo content_url('/uploads/2024/08/SERIGRAFIA.png'); ?>" alt="Serigrafía" loading="lazy">
                            </div>
                            <h3 class="servicio-title">SERIGRAFÍA</h3>
                        </a>

                        <!-- Servicio 2: Diseño Gráfico -->
                        <a href="#" class="servicio-card">
                            <div class="servicio-icon">
                                <img src="<?php echo content_url('/uploads/2024/08/DISE令-GRAFICO.png'); ?>" alt="Diseño Gráfico" loading="lazy">
                            </div>
                            <h3 class="servicio-title">GRÁFICO</h3>
                        </a>

                        <!-- Servicio 3: Impresión Digital -->
                        <a href="#" class="servicio-card">
                            <div class="servicio-icon">
                                <img src="<?php echo content_url('/uploads/2024/08/IMPRESION-DIGITAL.png'); ?>" alt="Impresión Digital" loading="lazy">
                            </div>
                            <h3 class="servicio-title">IMPRESIÓN DIGITAL</h3>
                        </a>

                        <!-- Servicio 4: Promociones -->
                        <a href="#" class="servicio-card">
                            <div class="servicio-icon">
                                <img src="<?php echo content_url('/uploads/2024/08/REGALO-PROMOCIONAL.png'); ?>" alt="Promociones" loading="lazy">
                            </div>
                            <h3 class="servicio-title">PROMOCIONES</h3>
                        </a>

                        <!-- Servicio 5: Ropa Laboral -->
                        <a href="#" class="servicio-card">
                            <div class="servicio-icon">
                                <img src="<?php echo content_url('/uploads/2024/08/ROPA-LABORAL.png'); ?>" alt="Ropa Laboral" loading="lazy">
                            </div>
                            <h3 class="servicio-title">ROPA LABORAL</h3>
                        </a>

                        <!-- Servicio 6: Regalos -->
                        <a href="#" class="servicio-card">
                            <div class="servicio-icon">
                                <img src="<?php echo content_url('/uploads/2024/08/REGALOS.png'); ?>" alt="Regalos" loading="lazy">
                            </div>
                            <h3 class="servicio-title">REGALOS</h3>
                        </a>

                    </div>
                    
                </div>

            </article>

        <?php endwhile; ?>

    </main>
</div>

<?php get_footer(); ?>

