<?php
if (!defined('ABSPATH')) exit;

if (!function_exists('chld_thm_cfg_locale_css')):
    function chld_thm_cfg_locale_css($uri){
        if (empty($uri) && is_rtl() && file_exists(get_template_directory() . '/rtl.css'))
            $uri = get_template_directory_uri() . '/rtl.css';
        return $uri;
    }
endif;
add_filter('locale_stylesheet_uri', 'chld_thm_cfg_locale_css');

if (!function_exists('child_theme_configurator_css')):
    function child_theme_configurator_css() {
        wp_enqueue_style('chld_thm_cfg_separate', trailingslashit(get_stylesheet_directory_uri()) . 'ctc-style.css', array());
    }
endif;
add_action('wp_enqueue_scripts', 'child_theme_configurator_css', 10);

function fix_woocommerce_asset_urls() {
    ?>
    <style type="text/css">
    @font-face {
        font-family: 'WooCommerce';
        src: url('<?php echo WC()->plugin_url(); ?>/assets/fonts/WooCommerce.woff') format('woff'),
             url('<?php echo WC()->plugin_url(); ?>/assets/fonts/WooCommerce.ttf') format('truetype');
        font-weight: normal;
        font-style: normal;
    }
    </style>
    <?php
}
add_action('wp_head', 'fix_woocommerce_asset_urls', 999);

// Sobrescribir colores antiguos generados dinámicamente
function override_old_woocommerce_colors() {
    ?>
    <style type="text/css">
    /* Sobrescribir cualquier instancia del color antiguo #96598A */
    
    /* Tiered Pricing específico (con cualquier ID) */
    [id*="tiered-pricing"] .tiered-pricing--active td,
    [class*="tiered-pricing"] .tiered-pricing--active td,
    .tiered-pricing--active td,
    [class*="tiered-pricing"] td.active,
    .woocommerce .tiered-pricing--active td {
        background-color: var(--secondary-color) !important;
    }
    
    /* Sobrescribir TODOS los elementos con inline styles del color antiguo */
    [style*="background-color: #96598A" i],
    [style*="background-color:#96598A" i],
    [style*="background: #96598A" i],
    [style*="background:#96598A" i],
    [style*="background-color: rgb(150, 89, 138)" i] {
        background-color: var(--secondary-color) !important;
        background: var(--secondary-color) !important;
    }
    
    /* Sobrescribir color de texto */
    [style*="color: #96598A" i],
    [style*="color:#96598A" i],
    [style*="color: rgb(150, 89, 138)" i] {
        color: var(--secondary-color) !important;
    }
    
    /* Sobrescribir border-color */
    [style*="border-color: #96598A" i],
    [style*="border-color:#96598A" i],
    [style*="border: 1px solid #96598A" i],
    [style*="border-color: rgb(150, 89, 138)" i] {
        border-color: var(--secondary-color) !important;
    }
    
    /* Sobrescribir cualquier ID aleatorio generado dinámicamente */
    [id^="#"] td,
    [id*="lx"] td.active,
    [id*="tiered"] td.active {
        background-color: var(--secondary-color) !important;
    }
    
    /* WooCommerce botones y elementos específicos */
    .woocommerce a.button.alt,
    .woocommerce button.button.alt,
    .woocommerce input.button.alt,
    .woocommerce #respond input#submit.alt,
    .woocommerce a.button,
    .woocommerce button.button,
    .woocommerce input.button,
    .woocommerce #respond input#submit {
        background-color: var(--secondary-color) !important;
    }
    
    .woocommerce a.button.alt:hover,
    .woocommerce button.button.alt:hover,
    .woocommerce input.button.alt:hover,
    .woocommerce #respond input#submit.alt:hover,
    .woocommerce a.button:hover,
    .woocommerce button.button:hover,
    .woocommerce input.button:hover,
    .woocommerce #respond input#submit:hover {
        background-color: var(--secondary-color-dark) !important;
    }
    
    /* WooCommerce price, sale badge */
    .woocommerce div.product p.price,
    .woocommerce div.product span.price,
    .woocommerce ul.products li.product .price {
        color: var(--secondary-color) !important;
    }
    
    .woocommerce span.onsale {
        background-color: var(--secondary-color) !important;
    }
    
    /* WooCommerce tabs */
    .woocommerce div.product .woocommerce-tabs ul.tabs li.active a,
    .woocommerce div.product .woocommerce-tabs ul.tabs li.active {
        color: var(--secondary-color) !important;
        border-bottom-color: var(--secondary-color) !important;
    }
    
    /* WooCommerce star rating */
    .woocommerce .star-rating span,
    .woocommerce p.stars a:hover::after {
        color: var(--secondary-color) !important;
    }
    
    /* WooCommerce messages */
    .woocommerce-message,
    .woocommerce-info {
        border-top-color: var(--secondary-color) !important;
    }
    
    .woocommerce-message::before,
    .woocommerce-info::before {
        color: var(--secondary-color) !important;
    }
    </style>
    <?php
}
add_action('wp_head', 'override_old_woocommerce_colors', 9999);

// ===== SISTEMA DE IMAGEN DE CATÁLOGO =====
// Permite tener una imagen diferente solo para páginas de catálogo (shop, archivo, categorías)
// NO afecta al carrito, mini-cart, checkout ni single product

// 1. Añadir metabox en el editor de productos
function add_catalog_image_metabox() {
    add_meta_box(
        'catalog_image_metabox',
        'Imagen de Catálogo (Shop/Archivo)',
        'render_catalog_image_metabox',
        'product',
        'side',
        'low'
    );
}
add_action('add_meta_boxes', 'add_catalog_image_metabox');

// 2. Renderizar el metabox
function render_catalog_image_metabox($post) {
    wp_nonce_field('save_catalog_image', 'catalog_image_nonce');
    
    $catalog_image_id = get_post_meta($post->ID, '_catalog_image_id', true);
    $catalog_image_url = $catalog_image_id ? wp_get_attachment_image_url($catalog_image_id, 'thumbnail') : '';
    ?>
    <div class="catalog-image-wrapper">
        <div class="catalog-image-preview" style="margin-bottom: 10px;">
            <?php if ($catalog_image_url): ?>
                <img src="<?php echo esc_url($catalog_image_url); ?>" style="max-width: 100%; height: auto; border: 1px solid #ddd; padding: 5px;" />
            <?php else: ?>
                <p style="color: #999; font-style: italic;">No se ha seleccionado imagen de catálogo</p>
            <?php endif; ?>
        </div>
        
        <input type="hidden" id="catalog_image_id" name="catalog_image_id" value="<?php echo esc_attr($catalog_image_id); ?>" />
        
        <button type="button" class="button button-secondary" id="select_catalog_image_button">
            <?php echo $catalog_image_id ? 'Cambiar imagen' : 'Seleccionar imagen'; ?>
        </button>
        
        <?php if ($catalog_image_id): ?>
            <button type="button" class="button button-link-delete" id="remove_catalog_image_button" style="color: #a00; margin-left: 5px;">
                Eliminar
            </button>
        <?php endif; ?>
        
        <p class="description" style="margin-top: 10px;">
            Esta imagen se mostrará SOLO en páginas de catálogo (shop, categorías).<br>
            <strong>NO afecta al carrito, mini-cart ni checkout.</strong>
        </p>
    </div>
    
    <script type="text/javascript">
    jQuery(document).ready(function($) {
        var frame;
        
        // Abrir media uploader
        $('#select_catalog_image_button').on('click', function(e) {
            e.preventDefault();
            
            if (frame) {
                frame.open();
                return;
            }
            
            frame = wp.media({
                title: 'Seleccionar Imagen de Catálogo',
                button: {
                    text: 'Usar esta imagen'
                },
                multiple: false
            });
            
            frame.on('select', function() {
                var attachment = frame.state().get('selection').first().toJSON();
                $('#catalog_image_id').val(attachment.id);
                $('.catalog-image-preview').html('<img src="' + attachment.url + '" style="max-width: 100%; height: auto; border: 1px solid #ddd; padding: 5px;" />');
                
                // Mostrar botón de eliminar
                if (!$('#remove_catalog_image_button').length) {
                    $('#select_catalog_image_button').after('<button type="button" class="button button-link-delete" id="remove_catalog_image_button" style="color: #a00; margin-left: 5px;">Eliminar</button>');
                    bindRemoveButton();
                }
                
                $('#select_catalog_image_button').text('Cambiar imagen');
            });
            
            frame.open();
        });
        
        // Función para bind el botón de eliminar
        function bindRemoveButton() {
            $('#remove_catalog_image_button').on('click', function(e) {
                e.preventDefault();
                $('#catalog_image_id').val('');
                $('.catalog-image-preview').html('<p style="color: #999; font-style: italic;">No se ha seleccionado imagen de catálogo</p>');
                $('#select_catalog_image_button').text('Seleccionar imagen');
                $(this).remove();
            });
        }
        
        // Bind inicial si existe el botón
        bindRemoveButton();
    });
    </script>
    <?php
}

// 3. Guardar el metabox
function save_catalog_image_metabox($post_id) {
    // Verificar nonce
    if (!isset($_POST['catalog_image_nonce']) || !wp_verify_nonce($_POST['catalog_image_nonce'], 'save_catalog_image')) {
        return;
    }
    
    // Verificar autosave
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    
    // Verificar permisos
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }
    
    // Guardar o eliminar
    if (isset($_POST['catalog_image_id']) && !empty($_POST['catalog_image_id'])) {
        update_post_meta($post_id, '_catalog_image_id', absint($_POST['catalog_image_id']));
    } else {
        delete_post_meta($post_id, '_catalog_image_id');
    }
}
add_action('save_post_product', 'save_catalog_image_metabox');

// 4. Cambiar la imagen SOLO en páginas de archivo (shop, categorías)
// IMPORTANTE: NO afecta a carrito, mini-cart, checkout ni single product
function use_catalog_image_in_archive($image, $product, $size, $attr, $placeholder, $image_context) {
    // EXCLUIR explícitamente: single product, carrito, mini-cart, checkout
    if (is_product() || is_cart() || is_checkout() || is_account_page()) {
        return $image;
    }
    
    // EXCLUIR también si estamos en un contexto de carrito (AJAX)
    if (did_action('woocommerce_before_cart') || did_action('woocommerce_before_mini_cart')) {
        return $image;
    }
    
    // Solo aplicar en páginas de shop/categorías/archivo
    if (!is_shop() && !is_product_category() && !is_product_tag() && !is_archive()) {
        return $image;
    }
    
    $catalog_image_id = get_post_meta($product->get_id(), '_catalog_image_id', true);
    
    if ($catalog_image_id) {
        $catalog_image = wp_get_attachment_image($catalog_image_id, $size, false, $attr);
        if ($catalog_image) {
            return $catalog_image;
        }
    }
    
    return $image;
}
add_filter('woocommerce_product_get_image', 'use_catalog_image_in_archive', 10, 6);

// ===== FIN SISTEMA DE IMAGEN DE CATÁLOGO =====

function wapf_lcp_capture_preview_script() {
    if (!is_product()) return;
    
    global $product;
    if (!$product) return;
    
    ?>
    <script type="text/javascript">
    jQuery(document).ready(function($) {
        var loadHtml2Canvas = function(callback) {
            if (typeof html2canvas !== 'undefined') {
                callback();
                return;
            }
            
            var script = document.createElement('script');
            script.src = '<?php echo get_stylesheet_directory_uri(); ?>/html2canvas.min.js';
            script.onload = callback;
            document.head.appendChild(script);
        };
        
        function captureLCPPreview() {
            var $activeImage = $('.woocommerce-product-gallery__image.flex-active-slide, .woocommerce-product-gallery__image:first');
            if (!$activeImage.length) return null;
            
            var $lcpWrap = $activeImage.find('.lcp-wrap');
            if (!$lcpWrap.length) return null;
            
            var $baseImage = $activeImage.find('img:not(.lcp-wrap img)').first();
            if (!$baseImage.length || !$baseImage[0].complete) return null;
            
            return {
                $container: $activeImage,
                $baseImage: $baseImage,
                $lcpWrap: $lcpWrap
            };
        }
        
        function generateAndSavePreview(callback) {
            var preview = captureLCPPreview();
            if (!preview) {
                if (callback) callback();
                return;
            }
            
            loadHtml2Canvas(function() {
                html2canvas(preview.$container[0], {
                    backgroundColor: null,
                    scale: 2,
                    logging: false,
                    useCORS: true,
                    allowTaint: true
                }).then(function(canvas) {
                    canvas.toBlob(function(blob) {
                        var formData = new FormData();
                        formData.append('action', 'wapf_save_lcp_preview');
                        formData.append('nonce', wapf_lcp_nonce);
                        formData.append('preview_image', blob, 'lcp-preview.png');
                        formData.append('product_id', $('input[name=product_id], input[name=add-to-cart]').val());
                        formData.append('variation_id', $('input[name=variation_id]').val() || '');
                        
                        $.ajax({
                            url: wapf_config.ajax,
                            type: 'POST',
                            data: formData,
                            processData: false,
                            contentType: false,
                            success: function(response) {
                                if (response.success) {
                                    var $input = $('input[name=wapf_lcp_preview_url]');
                                    if (!$input.length) {
                                        $input = $('<input type="hidden" name="wapf_lcp_preview_url">');
                                        $('form.cart').append($input);
                                    }
                                    $input.val(response.data.url);
                                }
                                if (callback) callback();
                            },
                            error: function() {
                                if (callback) callback();
                            }
                        });
                    }, 'image/png', 0.95);
                }).catch(function(error) {
                    if (callback) callback();
                });
            });
        }
        
        document.addEventListener('click', function(e) {
            var button = e.target.closest('.single_add_to_cart_button');
            if (!button) return;
            
            var $lcpCheck = $('.lcp-wrap');
            if (!$lcpCheck.length) return;
            
            var $form = $('form.cart');
            if ($form.data('lcp-captured')) return;
            
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            
            var $button = $(button);
            var originalText = $button.text();
            $button.prop('disabled', true).text('Preparando...');
            
            generateAndSavePreview(function() {
                $form.data('lcp-captured', true);
                $button.prop('disabled', false).text(originalText);
                setTimeout(function() {
                    button.click();
                }, 100);
            });
        }, true);
        
        $('.single_add_to_cart_button').each(function() {
            $(this).data('original-text', $(this).text());
        });
        
        $('form.variations_form').on('found_variation', function() {
            $('form.cart').data('lcp-captured', false);
            $('input[name=wapf_lcp_preview_url]').remove();
        });
    });
    </script>
    <?php
}
add_action('wp_footer', 'wapf_lcp_capture_preview_script', 1000);

function wapf_save_lcp_preview() {
    check_ajax_referer('wapf_lcp_nonce', 'nonce');
    
    if (!isset($_FILES['preview_image'])) {
        wp_send_json_error('No se recibió imagen');
    }
    
    require_once(ABSPATH . 'wp-admin/includes/file.php');
    require_once(ABSPATH . 'wp-admin/includes/image.php');
    
    $upload_dir = wp_upload_dir();
    $wapf_dir = trailingslashit($upload_dir['basedir']) . 'wapf-lcp-previews';
    
    if (!file_exists($wapf_dir)) {
        wp_mkdir_p($wapf_dir);
    }
    
    $filename = 'lcp-preview-' . uniqid() . '.png';
    $filepath = trailingslashit($wapf_dir) . $filename;
    
    if (move_uploaded_file($_FILES['preview_image']['tmp_name'], $filepath)) {
        $file_url = trailingslashit($upload_dir['baseurl']) . 'wapf-lcp-previews/' . $filename;
        wp_send_json_success([
            'url' => $file_url,
            'path' => $filepath
        ]);
    } else {
        wp_send_json_error('Error al guardar archivo');
    }
}
add_action('wp_ajax_wapf_save_lcp_preview', 'wapf_save_lcp_preview');
add_action('wp_ajax_nopriv_wapf_save_lcp_preview', 'wapf_save_lcp_preview');

function wapf_save_product_preview() {
    check_ajax_referer('wapf_lcp_nonce', 'nonce');
    
    if (!isset($_FILES['preview_image'])) {
        wp_send_json_error('No se recibió imagen');
    }
    
    require_once(ABSPATH . 'wp-admin/includes/file.php');
    require_once(ABSPATH . 'wp-admin/includes/image.php');
    
    $upload_dir = wp_upload_dir();
    $wapf_dir = trailingslashit($upload_dir['basedir']) . 'wapf-product-previews';
    
    if (!file_exists($wapf_dir)) {
        wp_mkdir_p($wapf_dir);
    }
    
    $product_id = isset($_POST['product_id']) ? sanitize_text_field($_POST['product_id']) : 'unknown';
    $variation_id = isset($_POST['variation_id']) ? sanitize_text_field($_POST['variation_id']) : '';
    $identifier = $variation_id ? $variation_id : $product_id;
    
    $filename = 'product-preview-' . $identifier . '-' . uniqid() . '.png';
    $filepath = trailingslashit($wapf_dir) . $filename;
    
    if (move_uploaded_file($_FILES['preview_image']['tmp_name'], $filepath)) {
        $file_url = trailingslashit($upload_dir['baseurl']) . 'wapf-product-previews/' . $filename;
        wp_send_json_success([
            'url' => $file_url,
            'path' => $filepath
        ]);
    } else {
        wp_send_json_error('Error al guardar archivo');
    }
}
add_action('wp_ajax_wapf_save_product_preview', 'wapf_save_product_preview');
add_action('wp_ajax_nopriv_wapf_save_product_preview', 'wapf_save_product_preview');

function wapf_lcp_add_nonce() {
    if (is_product()) {
        ?>
        <script type="text/javascript">
        var wapf_lcp_nonce = '<?php echo wp_create_nonce('wapf_lcp_nonce'); ?>';
        </script>
        <?php
    }
}
add_action('wp_head', 'wapf_lcp_add_nonce');

function wapf_lcp_save_preview_to_cart($cart_item_data, $product_id, $variation_id, $quantity) {
    // Guardar preview de LCP si existe
    if (isset($_POST['wapf_lcp_preview_url']) && !empty($_POST['wapf_lcp_preview_url'])) {
        $cart_item_data['wapf_lcp_preview'] = esc_url_raw($_POST['wapf_lcp_preview_url']);
    }
    
    // Guardar preview del producto personalizado si existe
    if (isset($_POST['wapf_product_preview_url']) && !empty($_POST['wapf_product_preview_url'])) {
        $cart_item_data['wapf_product_preview'] = esc_url_raw($_POST['wapf_product_preview_url']);
    }
    
    return $cart_item_data;
}
add_filter('woocommerce_add_cart_item_data', 'wapf_lcp_save_preview_to_cart', 10, 4);

// Mostrar el preview personalizado (variación + diseño) en el carrito
function wapf_show_custom_preview_in_cart($product_image, $cart_item, $cart_item_key) {
    // Si existe un preview del producto personalizado, mostrarlo
    if (isset($cart_item['wapf_product_preview']) && !empty($cart_item['wapf_product_preview'])) {
        $preview_url = esc_url($cart_item['wapf_product_preview']);
        $product = $cart_item['data'];
        
        $custom_image = sprintf(
            '<a href="%s"><img src="%s" class="attachment-woocommerce_thumbnail size-woocommerce_thumbnail" alt="%s" loading="lazy" style="width: 100%%; height: auto;" /></a>',
            esc_url(wc_get_cart_url()),
            $preview_url,
            esc_attr($product->get_name())
        );
        
        return $custom_image;
    }
    
    // Si existe un preview LCP, mostrarlo
    if (isset($cart_item['wapf_lcp_preview']) && !empty($cart_item['wapf_lcp_preview'])) {
        $preview_url = esc_url($cart_item['wapf_lcp_preview']);
        $product = $cart_item['data'];
        
        $custom_image = sprintf(
            '<a href="%s"><img src="%s" class="attachment-woocommerce_thumbnail size-woocommerce_thumbnail" alt="%s" loading="lazy" style="width: 100%%; height: auto;" /></a>',
            esc_url(wc_get_cart_url()),
            $preview_url,
            esc_attr($product->get_name())
        );
        
        return $custom_image;
    }
    
    // Si no hay preview personalizado, devolver la imagen por defecto
    return $product_image;
}
add_filter('woocommerce_cart_item_thumbnail', 'wapf_show_custom_preview_in_cart', 10, 3);

// Mostrar la imagen en el checkout dentro del nombre del producto
function wapf_show_preview_in_checkout_name($product_name, $cart_item, $cart_item_key) {
    // Solo aplicar en checkout
    if (!is_checkout()) {
        return $product_name;
    }
    
    $preview_url = '';
    
    // Verificar si existe un preview personalizado
    if (isset($cart_item['wapf_product_preview']) && !empty($cart_item['wapf_product_preview'])) {
        $preview_url = esc_url($cart_item['wapf_product_preview']);
    } elseif (isset($cart_item['wapf_lcp_preview']) && !empty($cart_item['wapf_lcp_preview'])) {
        $preview_url = esc_url($cart_item['wapf_lcp_preview']);
    }
    
    // Si hay preview, agregarlo al nombre
    if ($preview_url) {
        $product = $cart_item['data'];
        $thumbnail = sprintf(
            '<img src="%s" class="attachment-woocommerce_thumbnail" alt="%s" />',
            $preview_url,
            esc_attr($product->get_name())
        );
        
        // Agregar la imagen antes del nombre
        return $thumbnail . ' ' . $product_name;
    }
    
    return $product_name;
}
add_filter('woocommerce_cart_item_name', 'wapf_show_preview_in_checkout_name', 10, 3);

// Eliminar el enlace del nombre del producto en la página del carrito
function remove_cart_product_link($product_name, $cart_item, $cart_item_key) {
    // Solo aplicar en la página del carrito
    if (is_cart()) {
        $product = $cart_item['data'];
        // Devolver solo el nombre sin enlace
        return $product->get_name();
    }
    
    return $product_name;
}
add_filter('woocommerce_cart_item_name', 'remove_cart_product_link', 20, 3);




// Forzar la visualización de campos WAPF en el mini-cart
add_action('init', function() {
    update_option('wapf_settings_show_in_mini_cart', 'yes');
}, 1);

// FORZAR que WAPF muestre TODOS los campos en el mini-cart, sin importar la configuración
add_filter('option_wapf_settings_show_in_mini_cart', function($value) {
    return 'yes';
}, 999);

// También forzar en las otras páginas para asegurar consistencia
add_filter('option_wapf_settings_show_in_cart', function($value) {
    return 'yes';
}, 999);

add_filter('option_wapf_settings_show_in_checkout', function($value) {
    return 'yes';
}, 999);

// CSS para que los campos se vean bien en el mini-cart y carrito
add_action('wp_head', function() {
    ?>
    <style>
        /* Mini-cart: Mostrar variaciones/campos personalizados */
        .woocommerce-mini-cart-item .variation {
            display: block !important;
            margin: 5px 0 0 !important;
            font-size: 0.9em;
        }
        .woocommerce-mini-cart-item .variation dt,
        .woocommerce-mini-cart-item .variation dd {
            display: inline-block !important;
            margin: 0 !important;
            padding: 0 !important;
        }
        .woocommerce-mini-cart-item .variation dt {
            font-weight: 600;
            color: #666;
        }
        .woocommerce-mini-cart-item .variation dt::after {
            content: ' ';
        }
        .woocommerce-mini-cart-item .variation dd {
            color: #00a0d2;
            margin-bottom: 3px !important;
        }
        .woocommerce-mini-cart-item .variation dd p {
            display: inline !important;
            margin: 0 !important;
        }
        .woocommerce-mini-cart-item .variation dd::after {
            content: '';
            display: block;
        }
        
        /* Thumbnails del carrito y mini-cart: Mostrar solo el diseño con buen formato */
        .woocommerce-cart-form .product-thumbnail img,
        .woocommerce-mini-cart-item img.attachment-woocommerce_thumbnail {
            object-fit: contain !important;
            background: #f9f9f9 !important;
            padding: 10px !important;
            border: 1px solid #e0e0e0 !important;
            border-radius: 4px !important;
        }
        
        /* Asegurar que los thumbnails tengan un tamaño consistente */
        .woocommerce-cart-form .product-thumbnail a {
            display: block;
            width: 100%;
            max-width: 120px;
        }
        
        
        /* Eliminar margen inferior del nombre del producto */
        .product-name {
            margin-bottom: 0px !important;
        }
        
        /* Convertir ast-product-name en flex column */
        .ast-product-name {
            display: flex !important;
            flex-direction: column !important;
            margin-bottom:1.5rem;
            font-size:1rem;
            
        }
        
        /* Thumbnails en el checkout dentro de la columna de producto */
        .woocommerce-checkout-review-order-table .product-name a img {
            display: inline-block !important;
            width: 100px !important;
            height: auto !important;
            object-fit: contain !important;
            background: #f9f9f9 !important;
            padding: 5px !important;
            border: 1px solid #e0e0e0 !important;
            border-radius: 4px !important;
            margin-right: 10px !important;
            vertical-align: middle !important;
        }
        
        /* Alinear imágenes a la izquierda en checkout moderno de Astra */
        .ast-modern-checkout .woocommerce #ast-order-review-content .woocommerce-checkout-review-order-table tbody tr td.product-name .ast-product-image,
        .ast-modern-checkout .woocommerce #order_review .woocommerce-checkout-review-order-table tbody tr td.product-name .ast-product-image {
            justify-content: flex-start !important;
        }
        
        /* Ocultar thumbnails duplicados de Astra en checkout */
        .woocommerce-checkout-review-order-table tbody tr td.product-name .ast-product-image .ast-product-thumbnail img {
            display: none !important;
        }
        
        /* Ocultar imágenes duplicadas que WAPF pueda agregar FUERA del link en mini-cart */
        .woocommerce-mini-cart-item .product-name > img:not(.attachment-woocommerce_thumbnail) {
            display: none !important;
        }
    </style>
    <?php
});




function preserve_variation_selections() {
    if (!is_product()) return;
    
    global $product;
    if (!$product || !$product->is_type('variable')) return;
    
    ?>
    <script type="text/javascript">
    jQuery(document).ready(function($) {
        var variationCache = {};
        var isRestoring = false;
        
        function saveVariationSelections() {
            if (isRestoring) return;
            $('form.variations_form select[name^="attribute_"]').each(function() {
                var $select = $(this);
                var attrName = $select.attr('name');
                var value = $select.val();
                if (value && value !== '') {
                    variationCache[attrName] = value;
                }
            });
        }
        
        function restoreVariationSelections(changedAttr) {
            if (Object.keys(variationCache).length === 0) return;
            isRestoring = true;
            $.each(variationCache, function(attrName, value) {
                if (changedAttr && attrName === changedAttr) return;
                var $select = $('select[name="' + attrName + '"]');
                if ($select.length && $select.val() !== value) {
                    var optionExists = $select.find('option[value="' + value + '"]').length > 0;
                    if (optionExists) {
                        $select.val(value);
                        var $swatchContainer = $select.closest('td').find('.cfvsw-swatches-container');
                        if ($swatchContainer.length) {
                            $swatchContainer.find('.cfvsw-swatches-option').removeClass('cfvsw-selected-swatch');
                            $swatchContainer.find('.cfvsw-swatches-option[data-slug="' + value + '"]').addClass('cfvsw-selected-swatch');
                        }
                    }
                }
            });
            setTimeout(function() { isRestoring = false; }, 100);
        }
        
        $('form.variations_form').on('change', 'select[name^="attribute_"]', function() {
            var changedAttr = $(this).attr('name');
            var newValue = $(this).val();
            if (newValue && newValue !== '') {
                variationCache[changedAttr] = newValue;
            }
            setTimeout(function() { restoreVariationSelections(changedAttr); }, 50);
        });
        
        $(document).on('click', '.cfvsw-swatches-option', function() {
            if (isRestoring) return;
            var $container = $(this).closest('.cfvsw-swatches-container');
            var attrName = $container.attr('swatches-attr');
            if (attrName) {
                var fullAttrName = 'attribute_' + attrName;
                saveVariationSelections();
                setTimeout(function() { restoreVariationSelections(fullAttrName); }, 100);
            }
        });
        
        $('form.variations_form').on('found_variation', function() {
            if (!isRestoring) {
                setTimeout(function() { saveVariationSelections(); }, 100);
            }
        });
        
        $(window).on('load', function() {
            setTimeout(function() { saveVariationSelections(); }, 500);
        });
        
        $('form.variations_form').on('change', 'input.qty, input[name="quantity"]', function(e) {
            saveVariationSelections();
            e.stopPropagation();
            setTimeout(function() {
                restoreVariationSelections();
                $('form.variations_form').trigger('check_variations');
            }, 50);
        });
        
        $(document).on('click', '.quantity .plus, .quantity .minus, .qty-plus, .qty-minus', function(e) {
            setTimeout(function() {
                saveVariationSelections();
                setTimeout(function() { restoreVariationSelections(); }, 100);
            }, 50);
        });
    });
    </script>
    <?php
}
add_action('wp_footer', 'preserve_variation_selections', 998);

function wapf_preserve_uploads_on_variation_change() {
    if (!is_product()) return;
    
    global $product;
    if (!$product || !$product->is_type('variable')) return;
    
    ?>
    <script type="text/javascript">
    jQuery(document).ready(function($) {
        var wapfUploadCache = {};
        var isRestoringFiles = false;
        var filesCache = {};
        
        function saveUploadedFiles() {
            if (isRestoringFiles) return;
            $('input[data-is-file="1"]').each(function() {
                var $input = $(this);
                var fieldId = $input.attr('name').replace('wapf[field_', '').replace(']', '');
                var currentValue = $input.val();
                if (currentValue && currentValue.trim() !== '') {
                    wapfUploadCache[fieldId] = {
                        value: currentValue,
                        files: []
                    };
                    var dropzoneId = 'wapf-dz-' + fieldId;
                    if ($('#' + dropzoneId).length && $('#' + dropzoneId)[0].dropzone) {
                        var dz = $('#' + dropzoneId)[0].dropzone;
                        wapfUploadCache[fieldId].files = dz.files.map(function(file) {
                            return {
                                name: file.name,
                                size: file.size,
                                type: file.type,
                                dataURL: file.dataURL || file.transformedFile?.dataURL,
                                status: file.status,
                                uuid: file.upload?.uuid,
                                isEditCart: file.isEditCart
                            };
                        });
                    }
                }
            });
        }
        
        function restoreUploadedFiles() {
            if (Object.keys(wapfUploadCache).length === 0) return;
            isRestoringFiles = true;
            $.each(wapfUploadCache, function(fieldId, data) {
                var $input = $('input[name="wapf[field_' + fieldId + ']"]');
                if ($input.length) {
                    $input.val(data.value);
                    var dropzoneId = 'wapf-dz-' + fieldId;
                    if ($('#' + dropzoneId).length && $('#' + dropzoneId)[0].dropzone && data.files.length > 0) {
                        var dz = $('#' + dropzoneId)[0].dropzone;
                        dz.removeAllFiles(true);
                        setTimeout(function() {
                            data.files.forEach(function(fileData) {
                                if (fileData.dataURL) {
                                    var file = {
                                        isEditCart: true,
                                        processing: false,
                                        accepted: true,
                                        name: fileData.name,
                                        size: fileData.size,
                                        type: fileData.type,
                                        status: Dropzone.SUCCESS,
                                        upload: { uuid: fileData.uuid || Date.now() },
                                        dataURL: fileData.dataURL
                                    };
                                    dz.files.push(file);
                                    dz.emit("addedfile", file);
                                    if (file.type && file.type.indexOf('image') !== -1) {
                                        dz.createThumbnailFromUrl(file, dz.options.thumbnailWidth, dz.options.thumbnailHeight, dz.options.thumbnailMethod, true, function(thumbnail) {
                                            dz.emit('thumbnail', file, thumbnail);
                                        });
                                    }
                                    dz.emit("complete", file);
                                }
                            });
                            if (data.files.length > 0) {
                                $('#' + dropzoneId + ' .dz-message').hide();
                            }
                        }, 100);
                    }
                    $input.trigger('change');
                }
            });
            setTimeout(function() { isRestoringFiles = false; }, 500);
        }
        
        var preventImageClear = function() {
            if (typeof Dropzone !== 'undefined' && Dropzone.instances.length > 0) {
                Dropzone.instances.forEach(function(dz) {
                    var dzId = dz.element.id;
                    if (dz.files && dz.files.length > 0) {
                        filesCache[dzId] = dz.files.map(function(file) {
                            return {
                                name: file.name,
                                size: file.size,
                                type: file.type,
                                dataURL: file.dataURL,
                                status: file.status,
                                upload: file.upload
                            };
                        });
                    }
                    dz.removeAllFiles = function() { return; };
                    var originalRemoveFile = dz.removeFile.bind(dz);
                    dz.removeFile = function(file) {
                        if (file.manualRemove) {
                            originalRemoveFile(file);
                        }
                    };
                });
            }
            
            $(document.body).off('added_to_cart.wapf_maintain');
            $(document.body).on('added_to_cart.wapf_maintain', function(event, fragments, cart_hash, button) {
                setTimeout(function() {
                    if (typeof Dropzone !== 'undefined' && Dropzone.instances.length > 0) {
                        Dropzone.instances.forEach(function(dz) {
                            var dzId = dz.element.id;
                            var $dz = $('#' + dzId);
                            if (filesCache[dzId] && filesCache[dzId].length > 0 && dz.files.length === 0) {
                                filesCache[dzId].forEach(function(fileData) {
                                    var mockFile = {
                                        name: fileData.name,
                                        size: fileData.size,
                                        type: fileData.type,
                                        status: Dropzone.SUCCESS,
                                        dataURL: fileData.dataURL,
                                        upload: fileData.upload
                                    };
                                    dz.files.push(mockFile);
                                    dz.emit("addedfile", mockFile);
                                    if (fileData.dataURL && fileData.type.indexOf('image') !== -1) {
                                        dz.emit("thumbnail", mockFile, fileData.dataURL);
                                    }
                                    dz.emit("complete", mockFile);
                                });
                            }
                            if (dz.files.length > 0) {
                                $dz.find('.dz-message').hide();
                            }
                        });
                    }
                }, 300);
            });
        };
        
        $('form.variations_form').on('woocommerce_variation_select_change', function() { saveUploadedFiles(); });
        $('form.variations_form').on('found_variation', function() { setTimeout(function() { restoreUploadedFiles(); }, 300); });
        $('form.variations_form').on('reset_data', function() {});
        $(window).on('load', function() { setTimeout(function() { saveUploadedFiles(); }, 1500); });
        $(document).on('wapf/file_uploaded', function(e, data) {
            if (!isRestoringFiles) {
                // El usuario subió un archivo, limpiar el flag de "manualmente borrado"
                if (data && data.fieldId) {
                    var dzId = 'wapf-dz-' + data.fieldId;
                    delete manuallyCleared[dzId];
                }
                setTimeout(function() { saveUploadedFiles(); }, 200);
            }
        });
        $(document).on('wapf/file_deleted', function(e, data) {
            if (!isRestoringFiles) {
                // El usuario eliminó el archivo manualmente
                if (data && data.fieldId) {
                    var dzId = 'wapf-dz-' + data.fieldId;
                    manuallyCleared[dzId] = true;
                    
                    // Limpiar los archivos persistentes para este dropzone
                    delete persistentFiles[dzId];
                    delete persistentInputs[data.fieldId];
                    
                    console.log('Archivo eliminado manualmente:', dzId);
                }
                setTimeout(function() { saveUploadedFiles(); }, 200);
            }
        });
        
        setTimeout(preventImageClear, 100);
        setTimeout(preventImageClear, 1000);
        setTimeout(preventImageClear, 2000);
        $(window).on('load', function() { setTimeout(preventImageClear, 500); });
        $(document).on('wapf/init_dropzone', function() { setTimeout(preventImageClear, 100); });
    });
    </script>
    <?php
}
add_action('wp_footer', 'wapf_preserve_uploads_on_variation_change', 999);

/**
 * AJAX add-to-cart simplificado
 * Miniatura del carrito = imagen base + diseño (sin variaciones de color)
 */
function prevent_wapf_upload_clear_on_add_to_cart() {
    if (!is_product()) return;
    ?>
    <script type="text/javascript">
    jQuery(document).ready(function($) {
        var isAddingToCart = false;
        var lastAddToCartTime = 0;
        var isRestoringFiles = false;
        
        // Habilitar botón de añadir al carrito (excepto cuando se está capturando preview)
        var forceEnableButton = function() {
            if (isAddingToCart) return;
            var $button = $('.single_add_to_cart_button');
            if ($button.length > 0) {
                // NO habilitar si se está capturando el preview
                if (!$button.hasClass('wapf-capturing-preview')) {
                    $button.prop('disabled', false).removeAttr('disabled').removeClass('disabled wc-variation-selection-needed');
                }
            }
        };
        setInterval(forceEnableButton, 200);
        
        // Variables para protección de Dropzone (solo en memoria)
        var persistentFiles = {};
        var persistentInputs = {};
        var manuallyCleared = {}; // Flag para saber si el usuario borró manualmente
        
        // Guardar archivos de Dropzone
        var saveDropzoneFiles = function() {
            if (typeof Dropzone === 'undefined' || !Dropzone.instances.length) return;
            
            Dropzone.instances.forEach(function(dz) {
                var dzId = dz.element.id;
                if (dz.files && dz.files.length > 0) {
                    persistentFiles[dzId] = dz.files.map(function(file) {
                        return {
                            name: file.name,
                            size: file.size,
                            type: file.type,
                            dataURL: file.dataURL || file.transformedFile?.dataURL,
                            status: file.status,
                            upload: file.upload ? {
                                uuid: file.upload.uuid,
                                filename: file.upload.filename
                            } : null
                        };
                    });
                    
                    // Guardar también el valor del input asociado
                    var fieldId = dzId.replace('wapf-dz-', '');
                    var $input = $('input[data-field-id="' + fieldId + '"]');
                    if ($input.length && $input.val()) {
                        persistentInputs[fieldId] = $input.val();
                    }
                }
            });
        };
        
        // Restaurar archivos de Dropzone
        var restoreDropzoneFiles = function() {
            if (typeof Dropzone === 'undefined' || !Dropzone.instances.length) return;
            
            isRestoringFiles = true;
            
            Dropzone.instances.forEach(function(dz) {
                var dzId = dz.element.id;
                
                // NO restaurar si el usuario borró manualmente
                if (manuallyCleared[dzId]) {
                    return;
                }
                
                if (persistentFiles[dzId] && persistentFiles[dzId].length > 0 && dz.files.length === 0) {
                    persistentFiles[dzId].forEach(function(fileData) {
                        var mockFile = {
                            name: fileData.name,
                            size: fileData.size,
                            type: fileData.type,
                            status: Dropzone.SUCCESS,
                            accepted: true,
                            dataURL: fileData.dataURL,
                            upload: fileData.upload
                        };
                        
                        dz.files.push(mockFile);
                        dz.emit("addedfile", mockFile);
                        
                        if (fileData.dataURL && fileData.type && fileData.type.indexOf('image') !== -1) {
                            dz.emit("thumbnail", mockFile, fileData.dataURL);
                        }
                        
                        dz.emit("complete", mockFile);
                    });
                    
                    // Restaurar el valor del input asociado
                    var fieldId = dzId.replace('wapf-dz-', '');
                    if (persistentInputs[fieldId]) {
                        var $input = $('input[data-field-id="' + fieldId + '"]');
                        if ($input.length) {
                            $input.val(persistentInputs[fieldId]).trigger('change');
                        }
                    }
                    
                    if (dz.files.length > 0) {
                        $('#' + dzId).find('.dz-message').hide();
                    }
                }
            });
            
            setTimeout(function() {
                isRestoringFiles = false;
            }, 100);
        };
        
        // Proteger Dropzone de limpieza
        var setupDropzoneProtection = function() {
            if (typeof Dropzone === 'undefined' || !Dropzone.instances.length) {
                setTimeout(setupDropzoneProtection, 500);
                return;
            }
            
            Dropzone.instances.forEach(function(dz) {
                var dzId = dz.element.id;
                var originalRemoveAll = dz.removeAllFiles.bind(dz);
                
                // Detectar cuando el usuario hace clic en la X para borrar un archivo
                dz.on('removedfile', function(file) {
                    if (!isRestoringFiles) {
                        console.log('Usuario eliminó archivo de:', dzId);
                        manuallyCleared[dzId] = true;
                        
                        // Limpiar los archivos persistentes
                        delete persistentFiles[dzId];
                        var fieldId = dzId.replace('wapf-dz-', '');
                        delete persistentInputs[fieldId];
                    }
                });
                
                dz.removeAllFiles = function(cancelIfNecessary) {
                    // Si el usuario borró manualmente, permitir la eliminación
                    if (manuallyCleared[dzId]) {
                        return originalRemoveAll(cancelIfNecessary);
                    }
                    
                    if (persistentFiles[dzId] && persistentFiles[dzId].length > 0) {
                        // Asegurar que el input mantenga su valor antes de restaurar
                        var fieldId = dzId.replace('wapf-dz-', '');
                        if (persistentInputs[fieldId]) {
                            var $input = $('input[data-field-id="' + fieldId + '"]');
                            if ($input.length) {
                                $input.val(persistentInputs[fieldId]);
                            }
                        }
                        setTimeout(restoreDropzoneFiles, 50);
                        return;
                    }
                    return originalRemoveAll(cancelIfNecessary);
                };
            });
        };
        
        // Notificación visual
        var showNotification = function(message, type) {
            type = type || 'success';
            var $notification = $('<div class="wapf-notification ' + type + '">' +
                '<span>' + message + '</span>' +
                '<button class="close">&times;</button>' +
            '</div>');
            
            $('body').append($notification);
            setTimeout(function() { $notification.addClass('show'); }, 10);
            setTimeout(function() {
                $notification.removeClass('show');
                setTimeout(function() { $notification.remove(); }, 300);
            }, 5000);
            
            $notification.find('.close').on('click', function() {
                $notification.removeClass('show');
                setTimeout(function() { $notification.remove(); }, 300);
            });
        };
        
        // Cargar html2canvas
        var loadHtml2Canvas = function(callback) {
            if (typeof html2canvas !== 'undefined') {
                callback();
                return;
            }
            
            var script = document.createElement('script');
            script.src = '<?php echo get_stylesheet_directory_uri(); ?>/html2canvas.min.js';
            script.onload = callback;
            script.onerror = function() {
                console.error('Error cargando html2canvas');
                callback();
            };
            document.head.appendChild(script);
        };
        
        // Capturar preview UNA VEZ (imagen base + diseño)
        var captureProductPreview = function(callback) {
            var $activeImage = $('.woocommerce-product-gallery__image.flex-active-slide img').first();
            if (!$activeImage.length) {
                $activeImage = $('.woocommerce-product-gallery img').first();
            }
            
            if (!$activeImage.length) {
                if (callback) callback();
                return;
            }
            
            var $container = $activeImage.closest('.woocommerce-product-gallery__image');
            var $lcpWrap = $container.find('.lcp-wrap');
            
            // Si hay overlay LCP, capturar con html2canvas
            if ($lcpWrap.length > 0) {
                loadHtml2Canvas(function() {
                    if (typeof html2canvas === 'undefined') {
                        if (callback) callback();
                        return;
                    }
                    
                    html2canvas($container[0], {
                        backgroundColor: null,
                        scale: 2,
                        logging: false,
                        useCORS: true,
                        allowTaint: true
                    }).then(function(canvas) {
                        canvas.toBlob(function(blob) {
                            var formData = new FormData();
                            formData.append('action', 'wapf_save_product_preview');
                            formData.append('nonce', wapf_lcp_nonce);
                            formData.append('preview_image', blob, 'product-preview.png');
                            
                            var $form = $('form.cart');
                            var productId = $form.find('button[name="add-to-cart"]').val();
                            
                            formData.append('product_id', productId || '');
                            formData.append('variation_id', '0');
                            
                            $.ajax({
                                url: wapf_config.ajax,
                                type: 'POST',
                                data: formData,
                                processData: false,
                                contentType: false,
                                success: function(response) {
                                    if (response.success) {
                                        var $input = $('input[name=wapf_product_preview_url]');
                                        if (!$input.length) {
                                            $input = $('<input type="hidden" name="wapf_product_preview_url">');
                                            $('form.cart').append($input);
                                        }
                                        $input.val(response.data.url);
                                    }
                                    if (callback) callback();
                                },
                                error: function() {
                                    if (callback) callback();
                                }
                            });
                        }, 'image/png', 0.95);
                    }).catch(function(error) {
                        if (callback) callback();
                    });
                });
            } else {
                // Sin LCP, solo proceder
                if (callback) callback();
            }
        };
        
        // Cuando se sube un archivo, capturar preview
        $(document).on('wapf/file_uploaded', function(e, data) {
            setTimeout(saveDropzoneFiles, 100);
            setTimeout(forceEnableButton, 150);
            
            // Capturar preview después de subir archivo
            setTimeout(function() {
                captureProductPreview();
            }, 800);
        });
        
        // IMPORTANTE: Recapturar preview cuando cambia la variación (color/talla)
        $('form.variations_form').on('found_variation', function(event, variation) {
            // Deshabilitar el botón mientras se captura el preview
            var $button = $('.single_add_to_cart_button');
            $button.prop('disabled', true).addClass('wapf-capturing-preview').text('Preparando imagen...');
            
            // Esperar a que la imagen de la variación se cargue completamente
            setTimeout(function() {
                // Solo recapturar si ya hay un diseño subido
                if (typeof Dropzone !== 'undefined' && Dropzone.instances.length > 0) {
                    var hasFiles = false;
                    Dropzone.instances.forEach(function(dz) {
                        if (dz.files && dz.files.length > 0) {
                            hasFiles = true;
                        }
                    });
                    
                    if (hasFiles) {
                        // Limpiar el preview anterior y capturar uno nuevo con la nueva variación
                        $('input[name=wapf_product_preview_url]').remove();
                        captureProductPreview(function() {
                            // Callback: habilitar el botón cuando termine la captura
                            $button.prop('disabled', false).removeClass('wapf-capturing-preview').text($button.data('original-text') || 'Añadir al carrito');
                        });
                    } else {
                        // No hay archivos, habilitar el botón inmediatamente
                        $button.prop('disabled', false).removeClass('wapf-capturing-preview').text($button.data('original-text') || 'Añadir al carrito');
                    }
                } else {
                    // No hay Dropzone, habilitar el botón inmediatamente
                    $button.prop('disabled', false).removeClass('wapf-capturing-preview').text($button.data('original-text') || 'Añadir al carrito');
                }
            }, 600); // Dar tiempo a que la imagen de variación se cargue
        });
        
        // También recapturar cuando se cambia un campo WAPF (color/talla)
        $(document).on('change', 'input[name^="wapf[field_"]', function() {
            // Deshabilitar el botón mientras se captura el preview
            var $button = $('.single_add_to_cart_button');
            $button.prop('disabled', true).addClass('wapf-capturing-preview').text('Preparando imagen...');
            
            setTimeout(function() {
                // Solo recapturar si ya hay un diseño subido
                if (typeof Dropzone !== 'undefined' && Dropzone.instances.length > 0) {
                    var hasFiles = false;
                    Dropzone.instances.forEach(function(dz) {
                        if (dz.files && dz.files.length > 0) {
                            hasFiles = true;
                        }
                    });
                    
                    if (hasFiles) {
                        // Limpiar el preview anterior y capturar uno nuevo
                        $('input[name=wapf_product_preview_url]').remove();
                        captureProductPreview(function() {
                            // Callback: habilitar el botón cuando termine la captura
                            $button.prop('disabled', false).removeClass('wapf-capturing-preview').text($button.data('original-text') || 'Añadir al carrito');
                        });
                    } else {
                        // No hay archivos, habilitar el botón inmediatamente
                        $button.prop('disabled', false).removeClass('wapf-capturing-preview').text($button.data('original-text') || 'Añadir al carrito');
                    }
                } else {
                    // No hay Dropzone, habilitar el botón inmediatamente
                    $button.prop('disabled', false).removeClass('wapf-capturing-preview').text($button.data('original-text') || 'Añadir al carrito');
                }
            }, 600);
        });
        
        // Guardar cuando cambia el input de archivo WAPF
        $(document).on('change', 'input[data-is-file="1"]', function() {
            setTimeout(saveDropzoneFiles, 100);
        });
        
        // AJAX add-to-cart
        $(document).on('click', '.single_add_to_cart_button', function(e) {
            var now = Date.now();
            
            if (now - lastAddToCartTime < 1000) {
                e.preventDefault();
                e.stopImmediatePropagation();
                return false;
            }
            
            // IMPORTANTE: Restaurar valores de inputs ANTES de validar el formulario
            if (typeof Dropzone !== 'undefined' && Dropzone.instances.length > 0) {
                Dropzone.instances.forEach(function(dz) {
                    var dzId = dz.element.id;
                    var fieldId = dzId.replace('wapf-dz-', '');
                    
                    // Limpiar el flag de "manualmente borrado" al añadir al carrito
                    // para que el archivo se mantenga para el siguiente producto
                    delete manuallyCleared[dzId];
                    
                    if (persistentInputs[fieldId]) {
                        var $input = $('input[data-field-id="' + fieldId + '"]');
                        if ($input.length && !$input.val()) {
                            $input.val(persistentInputs[fieldId]);
                        }
                    }
                });
            }
            
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            
            var $btn = $(this);
            var $form = $btn.closest('form.cart');
            
            var originalText = $btn.text();
            $btn.prop('disabled', true).addClass('loading').text('Añadiendo...');
            
            lastAddToCartTime = now;
            isAddingToCart = true;
            
            var formData = new FormData($form[0]);
            formData.append('action', 'woocommerce_ajax_add_to_cart');
            
            $.ajax({
                type: 'POST',
                url: wc_add_to_cart_params.wc_ajax_url.toString().replace('%%endpoint%%', 'add_to_cart'),
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.error && response.product_url) {
                        window.location = response.product_url;
                        return;
                    }
                    
                    if (response.fragments) {
                        $.each(response.fragments, function(key, value) {
                            $(key).replaceWith(value);
                        });
                    }
                    
                    $(document.body).trigger('added_to_cart', [response.fragments, response.cart_hash, $btn]);
                    
                    showNotification('Producto añadido al carrito', 'success');
                    
                    // Guardar archivos para mantener el input sincronizado
                    setTimeout(saveDropzoneFiles, 100);
                    
                    $btn.prop('disabled', false).removeClass('loading').text(originalText);
                    isAddingToCart = false;
                },
                error: function() {
                    showNotification('Error al añadir el producto', 'error');
                    $btn.prop('disabled', false).removeClass('loading').text(originalText);
                    isAddingToCart = false;
                }
            });
            
            return false;
        });
        
        // Setup inicial
        $(window).on('load', function() {
            setTimeout(saveDropzoneFiles, 1000);
            
            // Guardar el texto original del botón
            var $button = $('.single_add_to_cart_button');
            if ($button.length && !$button.data('original-text')) {
                $button.data('original-text', $button.text());
            }
        });
        
        setupDropzoneProtection();
        
        // Monitor para restaurar archivos si desaparecen Y mantener inputs sincronizados
        setInterval(function() {
            if (typeof Dropzone !== 'undefined' && Dropzone.instances.length) {
                Dropzone.instances.forEach(function(dz) {
                    var dzId = dz.element.id;
                    
                    // NO restaurar si el usuario borró manualmente
                    if (manuallyCleared[dzId]) {
                        return;
                    }
                    
                    // Restaurar archivos si desaparecieron
                    if (persistentFiles[dzId] && persistentFiles[dzId].length > 0 && dz.files.length === 0) {
                        restoreDropzoneFiles();
                    }
                    
                    // Asegurar que el input tenga valor si hay archivos
                    if (dz.files.length > 0) {
                        var fieldId = dzId.replace('wapf-dz-', '');
                        var $input = $('input[data-field-id="' + fieldId + '"]');
                        
                        if ($input.length && !$input.val() && persistentInputs[fieldId]) {
                            $input.val(persistentInputs[fieldId]);
                        }
                    }
                });
            }
        }, 200);
    });
    </script>
    
    <style>
    .wapf-notification {
        position: fixed;
        top: 20px;
        right: 20px;
        background: #fff;
        padding: 15px 45px 15px 20px;
        border-radius: 4px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        z-index: 999999;
        min-width: 300px;
        opacity: 0;
        transform: translateX(400px);
        transition: all 0.3s ease;
    }
    
    .wapf-notification.show {
        opacity: 1;
        transform: translateX(0);
    }
    
    .wapf-notification.success {
        border-left: 4px solid #46b450;
    }
    
    .wapf-notification.success span::before {
        content: '✓ ';
        color: #46b450;
        font-weight: bold;
    }
    
    .wapf-notification.error {
        border-left: 4px solid #dc3232;
    }
    
    .wapf-notification.error span::before {
        content: '✕ ';
        color: #dc3232;
        font-weight: bold;
    }
    
    .wapf-notification .close {
        position: absolute;
        top: 10px;
        right: 10px;
        background: none;
        border: none;
        font-size: 20px;
        color: #999;
        cursor: pointer;
        padding: 0;
        width: 20px;
        height: 20px;
    }
    
    .wapf-notification .close:hover {
        color: #333;
    }
    </style>
    <?php
}
add_action('wp_footer', 'prevent_wapf_upload_clear_on_add_to_cart', 1001);

/**
 * Handler AJAX para añadir al carrito sin recargar la página
 * Compatible con Advanced Product Fields
 */
function wapf_ajax_add_to_cart_handler() {
    // Verificar que sea una petición AJAX
    if (!defined('DOING_AJAX') || !DOING_AJAX) {
        return;
    }
    
    // Obtener datos del producto
    $product_id = apply_filters('woocommerce_add_to_cart_product_id', absint($_POST['product_id'] ?? $_POST['add-to-cart'] ?? 0));
    $quantity = empty($_POST['quantity']) ? 1 : wc_stock_amount(wp_unslash($_POST['quantity']));
    $variation_id = absint($_POST['variation_id'] ?? 0);
    $variation = array();
    
    // Si es un producto variable, obtener los atributos de variación
    if ($variation_id) {
        $product = wc_get_product($variation_id);
        
        // Obtener atributos de variación del POST
        foreach ($_POST as $key => $value) {
            if (strpos($key, 'attribute_') === 0) {
                $variation[sanitize_title($key)] = sanitize_text_field($value);
            }
        }
    } else {
        $product = wc_get_product($product_id);
    }
    
    // Verificar que el producto existe
    if (!$product) {
        wp_send_json_error(array(
            'error' => true,
            'message' => 'Producto no encontrado'
        ));
        return;
    }
    
    // Preparar cart_item_data para incluir campos personalizados de WAPF
    $cart_item_data = array();
    
    // Guardar preview del producto personalizado si existe en el POST
    if (isset($_POST['wapf_product_preview_url']) && !empty($_POST['wapf_product_preview_url'])) {
        $cart_item_data['wapf_product_preview'] = esc_url_raw($_POST['wapf_product_preview_url']);
    }
    
    // Guardar preview de LCP si existe en el POST
    if (isset($_POST['wapf_lcp_preview_url']) && !empty($_POST['wapf_lcp_preview_url'])) {
        $cart_item_data['wapf_lcp_preview'] = esc_url_raw($_POST['wapf_lcp_preview_url']);
    }
    
    // WAPF guarda sus datos en el POST, dejar que WAPF los procese
    // Los hooks de WAPF capturarán automáticamente los datos de campos personalizados
    
    // Añadir al carrito
    $passed_validation = apply_filters('woocommerce_add_to_cart_validation', true, $product_id, $quantity, $variation_id, $variation);
    
    if ($passed_validation) {
        $cart_item_key = WC()->cart->add_to_cart($product_id, $quantity, $variation_id, $variation, $cart_item_data);
        
        if ($cart_item_key) {
            do_action('woocommerce_ajax_added_to_cart', $product_id);
            
            // Obtener fragmentos actualizados del carrito
            WC_AJAX::get_refreshed_fragments();
        } else {
            wp_send_json_error(array(
                'error' => true,
                'message' => 'No se pudo añadir el producto al carrito'
            ));
        }
    } else {
        wp_send_json_error(array(
            'error' => true,
            'message' => 'La validación del producto falló'
        ));
    }
}

// Registrar el handler AJAX para usuarios logueados y no logueados
add_action('wp_ajax_woocommerce_ajax_add_to_cart', 'wapf_ajax_add_to_cart_handler');
add_action('wp_ajax_nopriv_woocommerce_ajax_add_to_cart', 'wapf_ajax_add_to_cart_handler');

/**
 * Habilitar soporte para añadir al carrito via AJAX en productos simples y variables
 */
function wapf_enable_ajax_add_to_cart_params() {
    if (!is_product()) {
        return;
    }
    
    // Asegurar que los parámetros de WooCommerce AJAX estén disponibles
    if (!wp_script_is('wc-add-to-cart', 'enqueued')) {
        wp_enqueue_script('wc-add-to-cart');
    }
}
add_action('wp_enqueue_scripts', 'wapf_enable_ajax_add_to_cart_params');

/**
 * Añadir meta boxes para imágenes de fondo de secciones en plantilla RealThread
 */
function realthread_add_background_metaboxes() {
    add_meta_box(
        'realthread_backgrounds',
        'Imágenes de Fondo - Plantilla RealThread',
        'realthread_backgrounds_callback',
        'page',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'realthread_add_background_metaboxes');

function realthread_backgrounds_callback($post) {
    wp_nonce_field('realthread_backgrounds_nonce', 'realthread_backgrounds_nonce_field');
    
    $sections = array(
        'hero_background_image' => 'Hero Principal',
        'steps_background_image' => 'Cómo Funciona',
        'categories_background_image' => 'Categorías',
        'products_background_image' => 'Productos Destacados',
        'design_background_image' => 'Herramienta de Diseño',
        'customer_background_image' => 'Creaciones de Clientes',
        'testimonials_background_image' => 'Testimonios',
        'trust_background_image' => 'Badges de Confianza',
        'faq_background_image' => 'Preguntas Frecuentes'
    );
    
    echo '<div class="realthread-backgrounds-container">';
    echo '<p><strong>Sube imágenes de fondo para cada sección de la plantilla RealThread</strong></p>';
    echo '<p><em>Las imágenes aparecerán como fondo con un overlay semitransparente para mantener la legibilidad del texto.</em></p>';
    echo '<hr style="margin: 20px 0;">';
    
    foreach ($sections as $meta_key => $label) {
        $image_url = get_post_meta($post->ID, $meta_key, true);
        
        echo '<div class="realthread-bg-section" style="margin-bottom: 30px; padding: 15px; background: #f5f5f5; border-radius: 5px;">';
        echo '<h4 style="margin-top: 0;">' . esc_html($label) . '</h4>';
        
        echo '<div class="bg-image-container">';
        if ($image_url) {
            echo '<img src="' . esc_url($image_url) . '" style="max-width: 300px; height: auto; display: block; margin-bottom: 10px; border: 2px solid #ddd; border-radius: 4px;" />';
        }
        echo '</div>';
        
        echo '<input type="hidden" name="' . esc_attr($meta_key) . '" id="' . esc_attr($meta_key) . '" value="' . esc_attr($image_url) . '" />';
        echo '<button type="button" class="button realthread-upload-btn" data-target="' . esc_attr($meta_key) . '">Seleccionar Imagen</button> ';
        if ($image_url) {
            echo '<button type="button" class="button realthread-remove-btn" data-target="' . esc_attr($meta_key) . '">Eliminar Imagen</button>';
        }
        echo '</div>';
    }
    
    echo '</div>';
    
    // Sección de Carousel de Productos
    echo '<hr style="margin: 30px 0;"><h3>Carousel de Productos en Hero</h3>';
    echo '<p><strong>Configura los 4 productos que aparecerán en el carousel del hero banner</strong></p>';
    
    for ($i = 1; $i <= 4; $i++) {
        $product_image = get_post_meta($post->ID, "carousel_product_{$i}_image", true);
        $product_title = get_post_meta($post->ID, "carousel_product_{$i}_title", true);
        $product_link = get_post_meta($post->ID, "carousel_product_{$i}_link", true);
        
        echo '<div class="carousel-product-section" style="margin-bottom: 30px; padding: 20px; background: #f9f9f9; border-radius: 5px; border: 1px solid #ddd;">';
        echo '<h4 style="margin-top: 0; color: var(--secondary-color);">Producto ' . $i . '</h4>';
        
        // Imagen
        echo '<div style="margin-bottom: 15px;">';
        echo '<label style="display: block; font-weight: 600; margin-bottom: 5px;">Imagen del Producto:</label>';
        echo '<div class="carousel-image-container">';
        if ($product_image) {
            echo '<img src="' . esc_url($product_image) . '" style="max-width: 200px; height: auto; display: block; margin-bottom: 10px; border: 2px solid #ddd; border-radius: 4px;" />';
        }
        echo '</div>';
        echo '<input type="hidden" name="carousel_product_' . $i . '_image" id="carousel_product_' . $i . '_image" value="' . esc_attr($product_image) . '" />';
        echo '<button type="button" class="button carousel-upload-btn" data-target="carousel_product_' . $i . '_image" data-container="carousel">Seleccionar Imagen</button> ';
        if ($product_image) {
            echo '<button type="button" class="button carousel-remove-btn" data-target="carousel_product_' . $i . '_image">Eliminar</button>';
        }
        echo '</div>';
        
        // Título
        echo '<div style="margin-bottom: 15px;">';
        echo '<label style="display: block; font-weight: 600; margin-bottom: 5px;">Título del Producto:</label>';
        echo '<input type="text" name="carousel_product_' . $i . '_title" value="' . esc_attr($product_title) . '" style="width: 100%; padding: 8px;" placeholder="Ej: Camiseta Una Impresión" />';
        echo '</div>';
        
        // Enlace
        echo '<div style="margin-bottom: 15px;">';
        echo '<label style="display: block; font-weight: 600; margin-bottom: 5px;">Enlace del Producto:</label>';
        echo '<input type="text" name="carousel_product_' . $i . '_link" value="' . esc_attr($product_link) . '" style="width: 100%; padding: 8px;" placeholder="/producto/camiseta-personalizada/" />';
        echo '</div>';
        
        echo '</div>';
    }
    
    // JavaScript para el media uploader
    ?>
    <script>
    jQuery(document).ready(function($) {
        var mediaUploader;
        
        // Upload para backgrounds
        $('.realthread-upload-btn').on('click', function(e) {
            e.preventDefault();
            var button = $(this);
            var targetField = button.data('target');
            var container = button.closest('.realthread-bg-section');
            
            if (mediaUploader) {
                mediaUploader.open();
                return;
            }
            
            mediaUploader = wp.media({
                title: 'Seleccionar Imagen de Fondo',
                button: {
                    text: 'Usar esta imagen'
                },
                multiple: false
            });
            
            mediaUploader.on('select', function() {
                var attachment = mediaUploader.state().get('selection').first().toJSON();
                $('#' + targetField).val(attachment.url);
                
                var imgContainer = container.find('.bg-image-container');
                imgContainer.html('<img src="' + attachment.url + '" style="max-width: 300px; height: auto; display: block; margin-bottom: 10px; border: 2px solid #ddd; border-radius: 4px;" />');
                
                if (container.find('.realthread-remove-btn').length === 0) {
                    button.after(' <button type="button" class="button realthread-remove-btn" data-target="' + targetField + '">Eliminar Imagen</button>');
                }
            });
            
            mediaUploader.open();
        });
        
        // Upload para carousel
        $('.carousel-upload-btn').on('click', function(e) {
            e.preventDefault();
            var button = $(this);
            var targetField = button.data('target');
            var container = button.closest('.carousel-product-section');
            
            var carouselUploader = wp.media({
                title: 'Seleccionar Imagen del Producto',
                button: {
                    text: 'Usar esta imagen'
                },
                multiple: false
            });
            
            carouselUploader.on('select', function() {
                var attachment = carouselUploader.state().get('selection').first().toJSON();
                $('#' + targetField).val(attachment.url);
                
                var imgContainer = container.find('.carousel-image-container');
                imgContainer.html('<img src="' + attachment.url + '" style="max-width: 200px; height: auto; display: block; margin-bottom: 10px; border: 2px solid #ddd; border-radius: 4px;" />');
                
                if (container.find('.carousel-remove-btn[data-target="' + targetField + '"]').length === 0) {
                    button.after(' <button type="button" class="button carousel-remove-btn" data-target="' + targetField + '">Eliminar</button>');
                }
            });
            
            carouselUploader.open();
        });
        
        // Remove background
        $(document).on('click', '.realthread-remove-btn', function(e) {
            e.preventDefault();
            var button = $(this);
            var targetField = button.data('target');
            var container = button.closest('.realthread-bg-section');
            
            $('#' + targetField).val('');
            container.find('.bg-image-container').html('');
            button.remove();
        });
        
        // Remove carousel
        $(document).on('click', '.carousel-remove-btn', function(e) {
            e.preventDefault();
            var button = $(this);
            var targetField = button.data('target');
            var container = button.closest('.carousel-product-section');
            
            $('#' + targetField).val('');
            container.find('.carousel-image-container').html('');
            button.remove();
        });
    });
    </script>
    <?php
}

function realthread_save_backgrounds($post_id) {
    if (!isset($_POST['realthread_backgrounds_nonce_field']) || 
        !wp_verify_nonce($_POST['realthread_backgrounds_nonce_field'], 'realthread_backgrounds_nonce')) {
        return;
    }
    
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }
    
    $sections = array(
        'hero_background_image',
        'steps_background_image',
        'categories_background_image',
        'products_background_image',
        'design_background_image',
        'customer_background_image',
        'testimonials_background_image',
        'trust_background_image',
        'faq_background_image'
    );
    
    foreach ($sections as $meta_key) {
        if (isset($_POST[$meta_key])) {
            update_post_meta($post_id, $meta_key, esc_url_raw($_POST[$meta_key]));
        } else {
            delete_post_meta($post_id, $meta_key);
        }
    }
    
    // Guardar productos del carousel
    for ($i = 1; $i <= 4; $i++) {
        $image_key = "carousel_product_{$i}_image";
        $title_key = "carousel_product_{$i}_title";
        $link_key = "carousel_product_{$i}_link";
        
        if (isset($_POST[$image_key])) {
            update_post_meta($post_id, $image_key, esc_url_raw($_POST[$image_key]));
        } else {
            delete_post_meta($post_id, $image_key);
        }
        
        if (isset($_POST[$title_key])) {
            update_post_meta($post_id, $title_key, sanitize_text_field($_POST[$title_key]));
        } else {
            delete_post_meta($post_id, $title_key);
        }
        
        if (isset($_POST[$link_key])) {
            update_post_meta($post_id, $link_key, esc_url_raw($_POST[$link_key]));
        } else {
            delete_post_meta($post_id, $link_key);
        }
    }
}
add_action('save_post', 'realthread_save_backgrounds');

