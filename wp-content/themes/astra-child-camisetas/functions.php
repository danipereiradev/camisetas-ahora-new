<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

// BEGIN ENQUEUE PARENT ACTION
// AUTO GENERATED - Do not modify or remove comment markers above or below:

if ( !function_exists( 'chld_thm_cfg_locale_css' ) ):
    function chld_thm_cfg_locale_css( $uri ){
        if ( empty( $uri ) && is_rtl() && file_exists( get_template_directory() . '/rtl.css' ) )
            $uri = get_template_directory_uri() . '/rtl.css';
        return $uri;
    }
endif;
add_filter( 'locale_stylesheet_uri', 'chld_thm_cfg_locale_css' );
         
if ( !function_exists( 'child_theme_configurator_css' ) ):
    function child_theme_configurator_css() {
        wp_enqueue_style( 'chld_thm_cfg_separate', trailingslashit( get_stylesheet_directory_uri() ) . 'ctc-style.css', array(  ) );
    }
endif;
add_action( 'wp_enqueue_scripts', 'child_theme_configurator_css', 10 );

// END ENQUEUE PARENT ACTION

/**
 * Corregir rutas incorrectas de WooCommerce assets
 */
function fix_woocommerce_asset_urls() {
    ?>
    <style type="text/css">
    /* Corregir rutas de iconos y fuentes de WooCommerce */
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

/**
 * Capturar y guardar la vista previa del Live Content Preview (LCP)
 * cuando se añade al carrito
 */
function wapf_lcp_capture_preview_script() {
    // Solo cargar en páginas de producto
    if ( ! is_product() ) {
        return;
    }
    
    // DEBUG: Verificar producto
    global $product;
    if ( ! $product ) {
        echo '<!-- LCP DEBUG: No hay producto global -->';
        return;
    }
    
    echo '<!-- LCP DEBUG: Producto encontrado, tipo: ' . $product->get_type() . ' -->';
    
    ?>
    <script type="text/javascript">
    jQuery(document).ready(function($) {
        
        console.log('=== DIAGNÓSTICO LCP CAPTURA ===');
        console.log('jQuery cargado:', typeof jQuery !== 'undefined');
        console.log('wapf_config existe:', typeof wapf_config !== 'undefined');
        console.log('wapf_lcp_nonce existe:', typeof wapf_lcp_nonce !== 'undefined');
        
        // Verificar si existe el LCP en la página
        var $lcpElements = $('.lcp-wrap');
        console.log('Elementos LCP encontrados:', $lcpElements.length);
        
        if ($lcpElements.length > 0) {
            console.log('✅ LCP existe en la página');
            console.log('Estructura:', $lcpElements.first().html().substring(0, 100));
        } else {
            console.warn('⚠️ No se encontró .lcp-wrap en la página');
        }
        
        // Verificar galería de producto
        var $gallery = $('.woocommerce-product-gallery__image');
        console.log('Imágenes de galería encontradas:', $gallery.length);
        
        // Verificar formulario
        var $form = $('form.cart');
        console.log('Formulario cart encontrado:', $form.length);
        
        console.log('=== FIN DIAGNÓSTICO ===');
        
        // Verificar el botón añadir al carrito
        var $addToCartButton = $('.single_add_to_cart_button');
        console.log('Botón añadir al carrito encontrado:', $addToCartButton.length);
        if ($addToCartButton.length) {
            console.log('Texto del botón:', $addToCartButton.text());
            console.log('Clase del botón:', $addToCartButton.attr('class'));
        }
        
        // Librería para capturar HTML como imagen (LOCAL - no CDN por CSP)
        var loadHtml2Canvas = function(callback) {
            if (typeof html2canvas !== 'undefined') {
                console.log('html2canvas ya cargado');
                callback();
                return;
            }
            
            console.log('Cargando html2canvas desde archivo local...');
            var script = document.createElement('script');
            script.src = '<?php echo get_stylesheet_directory_uri(); ?>/html2canvas.min.js';
            script.onload = function() {
                console.log('✅ html2canvas cargado correctamente');
                callback();
            };
            script.onerror = function() {
                console.error('❌ Error al cargar html2canvas');
            };
            document.head.appendChild(script);
        };
        
        /**
         * Capturar la vista previa del LCP como imagen
         */
        function captureLCPPreview() {
            console.log('LCP: Intentando capturar vista previa...');
            
            // Buscar la imagen activa de la galería con el overlay LCP
            var $activeImage = $('.woocommerce-product-gallery__image.flex-active-slide, .woocommerce-product-gallery__image:first');
            
            if (!$activeImage.length) {
                console.log('LCP: No se encontró imagen activa');
                return null;
            }
            
            // Buscar el wrapper del LCP
            var $lcpWrap = $activeImage.find('.lcp-wrap');
            
            if (!$lcpWrap.length) {
                console.log('LCP: No se encontró vista previa LCP');
                return null;
            }
            
            // Obtener la imagen base y el overlay
            var $baseImage = $activeImage.find('img:not(.lcp-wrap img)').first();
            
            if (!$baseImage.length || !$baseImage[0].complete) {
                console.log('LCP: Imagen base no cargada');
                return null;
            }
            
            return {
                $container: $activeImage,
                $baseImage: $baseImage,
                $lcpWrap: $lcpWrap
            };
        }
        
        /**
         * Generar imagen compuesta y guardarla
         */
        function generateAndSavePreview(callback) {
            var preview = captureLCPPreview();
            
            if (!preview) {
                console.log('LCP: No hay vista previa para capturar');
                if (callback) callback();
                return;
            }
            
            loadHtml2Canvas(function() {
                console.log('LCP: html2canvas cargado, capturando...');
                
                // Capturar el contenedor completo (imagen + overlay)
                html2canvas(preview.$container[0], {
                    backgroundColor: null,
                    scale: 2, // Mayor calidad
                    logging: false,
                    useCORS: true,
                    allowTaint: true
                }).then(function(canvas) {
                    
                    // Convertir a blob
                    canvas.toBlob(function(blob) {
                        
                        // Crear FormData para enviar
                        var formData = new FormData();
                        formData.append('action', 'wapf_save_lcp_preview');
                        formData.append('nonce', wapf_lcp_nonce);
                        formData.append('preview_image', blob, 'lcp-preview.png');
                        formData.append('product_id', $('input[name=product_id], input[name=add-to-cart]').val());
                        formData.append('variation_id', $('input[name=variation_id]').val() || '');
                        
                        // Guardar via AJAX
                        $.ajax({
                            url: wapf_config.ajax,
                            type: 'POST',
                            data: formData,
                            processData: false,
                            contentType: false,
                            success: function(response) {
                                if (response.success) {
                                    console.log('LCP: Vista previa guardada:', response.data.url);
                                    
                                    // Guardar la URL en un campo oculto para enviarla con el formulario
                                    var $input = $('input[name=wapf_lcp_preview_url]');
                                    if (!$input.length) {
                                        $input = $('<input type="hidden" name="wapf_lcp_preview_url">');
                                        $('form.cart').append($input);
                                    }
                                    $input.val(response.data.url);
                                    
                                    if (callback) callback();
                                } else {
                                    console.error('LCP: Error al guardar:', response.data);
                                    if (callback) callback();
                                }
                            },
                            error: function() {
                                console.error('LCP: Error en AJAX');
                                if (callback) callback();
                            }
                        });
                        
                    }, 'image/png', 0.95);
                    
                }).catch(function(error) {
                    console.error('LCP: Error al capturar canvas:', error);
                    if (callback) callback();
                });
            });
        }
        
        // NUEVA ESTRATEGIA: Interceptar CLIC del botón directamente
        // Usar capture: true para interceptar ANTES que otros handlers
        document.addEventListener('click', function(e) {
            // Buscar si el elemento clickeado es o está dentro del botón
            var button = e.target.closest('.single_add_to_cart_button');
            
            if (!button) {
                return; // No es el botón, ignorar
            }
            
            console.log('🔵 CLIC en botón añadir al carrito interceptado');
            
            // Verificar si hay LCP
            var $lcpCheck = $('.lcp-wrap');
            console.log('LCP presente:', $lcpCheck.length);
            
            if (!$lcpCheck.length) {
                console.log('LCP: No hay vista previa, añadiendo normalmente');
                return; // Dejar que el clic continúe normalmente
            }
            
            // Verificar si ya se capturó
            var $form = $('form.cart');
            if ($form.data('lcp-captured')) {
                console.log('LCP: Ya capturado, permitiendo clic');
                return; // Dejar que el clic continúe
            }
            
            // PREVENIR el clic para capturar primero
            console.log('🔴 Previniendo clic para capturar LCP');
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            
            // Deshabilitar botón
            var $button = $(button);
            var originalText = $button.text();
            $button.prop('disabled', true).text('Preparando vista previa...');
            console.log('Botón deshabilitado, iniciando captura...');
            
            // Capturar vista previa
            generateAndSavePreview(function() {
                console.log('✅ Captura completada, re-habilitando botón');
                
                // Marcar como capturado
                $form.data('lcp-captured', true);
                
                // Re-habilitar botón
                $button.prop('disabled', false).text(originalText);
                
                // Simular clic de nuevo (ahora sí se ejecutará normalmente)
                console.log('🟢 Re-ejecutando clic del botón');
                setTimeout(function() {
                    button.click();
                }, 100);
            });
            
        }, true); // true = capture phase (se ejecuta ANTES que otros handlers)
        
        // Guardar texto original del botón
        $('.single_add_to_cart_button').each(function() {
            $(this).data('original-text', $(this).text());
        });
        
        // Limpiar flag cuando cambian variaciones
        $('form.variations_form').on('found_variation', function() {
            $('form.cart').data('lcp-captured', false);
            $('input[name=wapf_lcp_preview_url]').remove();
        });
        
    });
    </script>
    <?php
}
add_action( 'wp_footer', 'wapf_lcp_capture_preview_script', 1000 );

/**
 * AJAX handler para guardar la imagen de vista previa
 */
function wapf_save_lcp_preview() {
    check_ajax_referer( 'wapf_lcp_nonce', 'nonce' );
    
    if ( ! isset( $_FILES['preview_image'] ) ) {
        wp_send_json_error( 'No se recibió imagen' );
    }
    
    require_once( ABSPATH . 'wp-admin/includes/file.php' );
    require_once( ABSPATH . 'wp-admin/includes/image.php' );
    
    $upload_dir = wp_upload_dir();
    $wapf_dir = trailingslashit( $upload_dir['basedir'] ) . 'wapf-lcp-previews';
    
    // Crear directorio si no existe
    if ( ! file_exists( $wapf_dir ) ) {
        wp_mkdir_p( $wapf_dir );
    }
    
    // Nombre único para el archivo
    $filename = 'lcp-preview-' . uniqid() . '.png';
    $filepath = trailingslashit( $wapf_dir ) . $filename;
    
    // Mover archivo subido
    if ( move_uploaded_file( $_FILES['preview_image']['tmp_name'], $filepath ) ) {
        $file_url = trailingslashit( $upload_dir['baseurl'] ) . 'wapf-lcp-previews/' . $filename;
        
        wp_send_json_success( [
            'url' => $file_url,
            'path' => $filepath
        ] );
    } else {
        wp_send_json_error( 'Error al guardar archivo' );
    }
}
add_action( 'wp_ajax_wapf_save_lcp_preview', 'wapf_save_lcp_preview' );
add_action( 'wp_ajax_nopriv_wapf_save_lcp_preview', 'wapf_save_lcp_preview' );

/**
 * Añadir nonce al frontend
 */
function wapf_lcp_add_nonce() {
    if ( is_product() ) {
        ?>
        <script type="text/javascript">
        var wapf_lcp_nonce = '<?php echo wp_create_nonce( 'wapf_lcp_nonce' ); ?>';
        </script>
        <?php
    }
}
add_action( 'wp_head', 'wapf_lcp_add_nonce' );

/**
 * Guardar la URL de la vista previa en los datos del carrito
 */
function wapf_lcp_save_preview_to_cart( $cart_item_data, $product_id, $variation_id, $quantity ) {
    
    if ( isset( $_POST['wapf_lcp_preview_url'] ) && ! empty( $_POST['wapf_lcp_preview_url'] ) ) {
        $cart_item_data['wapf_lcp_preview'] = esc_url_raw( $_POST['wapf_lcp_preview_url'] );
    }
    
    return $cart_item_data;
}
add_filter( 'woocommerce_add_cart_item_data', 'wapf_lcp_save_preview_to_cart', 10, 4 );

/**
 * Cambiar la imagen del carrito para mostrar la vista previa del LCP
 */
function wapf_change_cart_item_thumbnail( $product_image, $cart_item, $cart_item_key ) {
    
    // Prioridad 1: Usar la vista previa LCP capturada
    if ( isset( $cart_item['wapf_lcp_preview'] ) && ! empty( $cart_item['wapf_lcp_preview'] ) ) {
        $product = $cart_item['data'];
        
        $custom_image = sprintf(
            '<a href="%s"><img src="%s" class="attachment-woocommerce_thumbnail size-woocommerce_thumbnail" alt="%s" loading="lazy" /></a>',
            esc_url( wc_get_cart_url() ),
            esc_url( $cart_item['wapf_lcp_preview'] ),
            esc_attr( $product->get_name() . ' - Vista Previa Personalizada' )
        );
        
        return $custom_image;
    }
    
    // Si no hay vista previa LCP, retornar imagen original
    return $product_image;
}
add_filter( 'woocommerce_cart_item_thumbnail', 'wapf_change_cart_item_thumbnail', 10, 3 );

/**
 * Preservar variaciones seleccionadas (color, talla) al cambiar entre ellas
 * Solución para que no se quite el color al cambiar talla (y viceversa)
 */
function preserve_variation_selections() {
    // Solo cargar en páginas de producto
    if ( ! is_product() ) {
        return;
    }
    
    global $product;
    
    // Solo para productos variables
    if ( ! $product || ! $product->is_type( 'variable' ) ) {
        return;
    }
    
    ?>
    <script type="text/javascript">
    jQuery(document).ready(function($) {
        
        var variationCache = {};
        var isRestoring = false;
        
        console.log('Sistema de preservación de variaciones inicializado');
        
        /**
         * Guarda todas las variaciones actualmente seleccionadas
         */
        function saveVariationSelections() {
            if (isRestoring) return;
            
            $('form.variations_form select[name^="attribute_"]').each(function() {
                var $select = $(this);
                var attrName = $select.attr('name');
                var value = $select.val();
                
                if (value && value !== '') {
                    variationCache[attrName] = value;
                    console.log('Guardando variación:', attrName, '=', value);
                }
            });
        }
        
        /**
         * Restaura las variaciones guardadas
         */
        function restoreVariationSelections(changedAttr) {
            if (Object.keys(variationCache).length === 0) return;
            
            isRestoring = true;
            console.log('Restaurando variaciones...');
            
            // Restaurar cada variación guardada, excepto la que cambió
            $.each(variationCache, function(attrName, value) {
                if (changedAttr && attrName === changedAttr) {
                    return; // Skip la que acabamos de cambiar
                }
                
                var $select = $('select[name="' + attrName + '"]');
                if ($select.length && $select.val() !== value) {
                    // Verificar que el valor existe en las opciones
                    var optionExists = $select.find('option[value="' + value + '"]').length > 0;
                    if (optionExists) {
                        $select.val(value);
                        
                        // Actualizar swatch visual si existe
                        var $swatchContainer = $select.closest('td').find('.cfvsw-swatches-container');
                        if ($swatchContainer.length) {
                            $swatchContainer.find('.cfvsw-swatches-option').removeClass('cfvsw-selected-swatch');
                            $swatchContainer.find('.cfvsw-swatches-option[data-slug="' + value + '"]').addClass('cfvsw-selected-swatch');
                        }
                        
                        console.log('Restaurada variación:', attrName, '=', value);
                    }
                }
            });
            
            setTimeout(function() {
                isRestoring = false;
            }, 100);
        }
        
        // Guardar selección cuando cambia cualquier variación
        $('form.variations_form').on('change', 'select[name^="attribute_"]', function() {
            var $changedSelect = $(this);
            var changedAttr = $changedSelect.attr('name');
            var newValue = $changedSelect.val();
            
            console.log('Variación cambiada:', changedAttr, '=', newValue);
            
            // Guardar el nuevo valor
            if (newValue && newValue !== '') {
                variationCache[changedAttr] = newValue;
            }
            
            // Después de un breve delay, restaurar las otras variaciones
            setTimeout(function() {
                restoreVariationSelections(changedAttr);
            }, 50);
        });
        
        // Interceptar clicks en swatches (si se usa plugin de variation swatches)
        $(document).on('click', '.cfvsw-swatches-option', function() {
            if (isRestoring) return;
            
            var $swatch = $(this);
            var $container = $swatch.closest('.cfvsw-swatches-container');
            var attrName = $container.attr('swatches-attr');
            
            if (attrName) {
                var fullAttrName = 'attribute_' + attrName;
                saveVariationSelections();
                
                setTimeout(function() {
                    restoreVariationSelections(fullAttrName);
                }, 100);
            }
        });
        
        // Guardar estado después de encontrar variación
        $('form.variations_form').on('found_variation', function() {
            if (!isRestoring) {
                setTimeout(function() {
                    saveVariationSelections();
                }, 100);
            }
        });
        
        // Guardar estado inicial
        $(window).on('load', function() {
            setTimeout(function() {
                saveVariationSelections();
            }, 500);
        });
        
        // SOLUCIÓN PROBLEMA 2: Preservar variaciones al cambiar cantidad
        $('form.variations_form').on('change', 'input.qty, input[name="quantity"]', function(e) {
            console.log('Cantidad cambiada - preservando variaciones...');
            
            // Guardar estado actual
            saveVariationSelections();
            
            // Prevenir que se reseteen las variaciones
            e.stopPropagation();
            
            // Restaurar después de un breve momento
            setTimeout(function() {
                restoreVariationSelections();
                
                // Forzar actualización de la variación con la cantidad nueva
                $('form.variations_form').trigger('check_variations');
            }, 50);
        });
        
        // También interceptar clicks en botones +/- de cantidad (si existen)
        $(document).on('click', '.quantity .plus, .quantity .minus, .qty-plus, .qty-minus', function(e) {
            console.log('Botón cantidad clickeado - preservando variaciones...');
            
            setTimeout(function() {
                saveVariationSelections();
                setTimeout(function() {
                    restoreVariationSelections();
                }, 100);
            }, 50);
        });
        
    });
    </script>
    <?php
}
add_action( 'wp_footer', 'preserve_variation_selections', 998 );

/**
 * Preservar imágenes subidas de WAPF cuando se cambian variaciones
 * Solución para mantener los diseños cuando se seleccionan diferentes colores y tallas
 */
function wapf_preserve_uploads_on_variation_change() {
    // Solo cargar en páginas de producto
    if ( ! is_product() ) {
        return;
    }
    
    global $product;
    
    // Solo para productos variables
    if ( ! $product || ! $product->is_type( 'variable' ) ) {
        return;
    }
    
    ?>
    <script type="text/javascript">
    jQuery(document).ready(function($) {
        
        // Almacenamiento temporal para los archivos subidos
        var wapfUploadCache = {};
        var isRestoringFiles = false;
        
        /**
         * Guarda el estado actual de todos los campos de subida de archivos
         */
        function saveUploadedFiles() {
            if (isRestoringFiles) return;
            
            console.log('WAPF: Guardando estado de imágenes subidas...');
            
            // Guardar cada campo de archivo
            $('input[data-is-file="1"]').each(function() {
                var $input = $(this);
                var fieldId = $input.attr('name').replace('wapf[field_', '').replace(']', '');
                var currentValue = $input.val();
                
                if (currentValue && currentValue.trim() !== '') {
                    console.log('WAPF: Guardando campo', fieldId, ':', currentValue);
                    
                    // Guardar el valor del input
                    wapfUploadCache[fieldId] = {
                        value: currentValue,
                        files: []
                    };
                    
                    // Guardar información de Dropzone si existe
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
                        console.log('WAPF: Archivos Dropzone guardados:', wapfUploadCache[fieldId].files.length);
                    }
                }
            });
        }
        
        /**
         * Restaura los archivos guardados a los campos de subida
         */
        function restoreUploadedFiles() {
            if (Object.keys(wapfUploadCache).length === 0) {
                console.log('WAPF: No hay archivos para restaurar');
                return;
            }
            
            console.log('WAPF: Restaurando imágenes subidas...');
            isRestoringFiles = true;
            
            $.each(wapfUploadCache, function(fieldId, data) {
                var $input = $('input[name="wapf[field_' + fieldId + ']"]');
                
                if ($input.length) {
                    console.log('WAPF: Restaurando campo', fieldId);
                    
                    // Restaurar el valor del input
                    $input.val(data.value);
                    
                    // Restaurar archivos en Dropzone si existe
                    var dropzoneId = 'wapf-dz-' + fieldId;
                    if ($('#' + dropzoneId).length && $('#' + dropzoneId)[0].dropzone && data.files.length > 0) {
                        var dz = $('#' + dropzoneId)[0].dropzone;
                        
                        // Limpiar dropzone sin disparar eventos
                        dz.removeAllFiles(true);
                        
                        // Restaurar cada archivo
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
                                    
                                    // Si es una imagen, crear la vista previa
                                    if (file.type && file.type.indexOf('image') !== -1) {
                                        dz.createThumbnailFromUrl(
                                            file,
                                            dz.options.thumbnailWidth,
                                            dz.options.thumbnailHeight,
                                            dz.options.thumbnailMethod,
                                            true,
                                            function(thumbnail) {
                                                dz.emit('thumbnail', file, thumbnail);
                                            }
                                        );
                                    }
                                    
                                    dz.emit("complete", file);
                                }
                            });
                            
                            // Ocultar mensaje de drag & drop si hay archivos
                            if (data.files.length > 0) {
                                $('#' + dropzoneId + ' .dz-message').hide();
                            }
                            
                            console.log('WAPF: Archivos restaurados en Dropzone:', data.files.length);
                        }, 100);
                    }
                    
                    // Disparar evento de cambio
                    $input.trigger('change');
                }
            });
            
            setTimeout(function() {
                isRestoringFiles = false;
            }, 500);
            
            console.log('WAPF: Restauración completada');
        }
        
        // Guardar archivos antes de que se cambie la variación
        $('form.variations_form').on('woocommerce_variation_select_change', function() {
            saveUploadedFiles();
        });
        
        // Restaurar archivos después de que se encuentre una variación
        $('form.variations_form').on('found_variation', function(event, variation) {
            console.log('WAPF: Variación encontrada, restaurando archivos...');
            setTimeout(function() {
                restoreUploadedFiles();
            }, 300);
        });
        
        // También escuchar el evento de reset de variación
        $('form.variations_form').on('reset_data', function() {
            console.log('WAPF: Reset de variación detectado');
            // No limpiar el cache, solo esperar a que se seleccione otra variación
        });
        
        // Guardar estado inicial después de que la página cargue completamente
        $(window).on('load', function() {
            setTimeout(function() {
                saveUploadedFiles();
            }, 1500);
        });
        
        // Actualizar cache cuando se suba un nuevo archivo
        $(document).on('wapf/file_uploaded', function(e, data) {
            if (!isRestoringFiles) {
                console.log('WAPF: Nuevo archivo subido, actualizando cache...');
                setTimeout(function() {
                    saveUploadedFiles();
                }, 200);
            }
        });
        
        // Actualizar cache cuando se elimine un archivo
        $(document).on('wapf/file_deleted', function(e, data) {
            if (!isRestoringFiles) {
                console.log('WAPF: Archivo eliminado, actualizando cache...');
                setTimeout(function() {
                    saveUploadedFiles();
                }, 200);
            }
        });
        
        // SOLUCIÓN PROBLEMA 1: Prevenir que se limpien las imágenes al añadir al carrito
        // Interceptar el evento ANTES de que el plugin lo procese
        var preventClearOnAddToCart = function() {
            // Desactivar el handler original del plugin
            $(document.body).off('added_to_cart');
            
            // Añadir nuestro propio handler que NO limpia los archivos
            $(document.body).on('added_to_cart', function(event, fragments, cart_hash, button) {
                console.log('WAPF: Producto añadido al carrito - Manteniendo imágenes');
                // NO limpiar archivos - permitir que persistan para añadir más productos
                // Los archivos se mantendrán hasta que el usuario los elimine manualmente
                // o recargue la página
            });
        };
        
        // Ejecutar después de que el plugin WAPF cargue su código
        setTimeout(preventClearOnAddToCart, 2000);
        
        console.log('WAPF: Sistema de preservación de imágenes inicializado');
    });
    </script>
    <?php
}
add_action( 'wp_footer', 'wapf_preserve_uploads_on_variation_change', 999 );
