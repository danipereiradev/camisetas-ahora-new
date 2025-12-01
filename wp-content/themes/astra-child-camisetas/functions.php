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

function wapf_change_cart_item_thumbnail($product_image, $cart_item, $cart_item_key) {
    $product = $cart_item['data'];
    $preview_url = '';
    
    // Prioridad 1: Preview del producto personalizado
    if (isset($cart_item['wapf_product_preview']) && !empty($cart_item['wapf_product_preview'])) {
        $preview_url = $cart_item['wapf_product_preview'];
    }
    // Prioridad 2: Preview de LCP
    elseif (isset($cart_item['wapf_lcp_preview']) && !empty($cart_item['wapf_lcp_preview'])) {
        $preview_url = $cart_item['wapf_lcp_preview'];
    }
    
    if ($preview_url) {
        $custom_image = sprintf(
            '<a href="%s"><img src="%s" class="attachment-woocommerce_thumbnail size-woocommerce_thumbnail" alt="%s" loading="lazy" style="object-fit: cover;" /></a>',
            esc_url(wc_get_cart_url()),
            esc_url($preview_url),
            esc_attr($product->get_name())
        );
        return $custom_image;
    }
    
    return $product_image;
}
add_filter('woocommerce_cart_item_thumbnail', 'wapf_change_cart_item_thumbnail', 10, 3);

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
            if (!isRestoringFiles) { setTimeout(function() { saveUploadedFiles(); }, 200); }
        });
        $(document).on('wapf/file_deleted', function(e, data) {
            if (!isRestoringFiles) { setTimeout(function() { saveUploadedFiles(); }, 200); }
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
 * Prevenir que el uploader se vacíe cuando se añade al carrito
 * Permite añadir múltiples variaciones con el mismo diseño
 */
function prevent_wapf_upload_clear_on_add_to_cart() {
    if (!is_product()) return;
    ?>
    <script type="text/javascript">
    jQuery(document).ready(function($) {
        // Variables de control global
        var isAddingToCart = false;
        var isCapturingPreview = false;
        var lastAddToCartTime = 0;
        
        // Función optimizada para habilitar el botón
        var forceEnableButton = function() {
            // No interferir si estamos añadiendo al carrito
            if (isAddingToCart) return;
            
            var $button = $('form.cart .single_add_to_cart_button, .single_add_to_cart_button');
            
            if ($button.length > 0) {
                $button.prop('disabled', false);
                $button.removeAttr('disabled');
                $button.removeClass('disabled wc-variation-selection-needed');
            }
        };
        
        // Ejecutar solo cada 200ms para evitar sobrecarga
        setInterval(forceEnableButton, 200);
        // Variables para almacenar archivos de todos los dropzones
        var persistentUploads = {};
        var persistentFiles = {};
        
        // Función para guardar todos los archivos de todos los dropzones
        var saveAllDropzoneFiles = function() {
            if (typeof Dropzone === 'undefined' || !Dropzone.instances.length) return;
            
            console.log('💾 Guardando archivos de ' + Dropzone.instances.length + ' dropzones...');
            
            Dropzone.instances.forEach(function(dz) {
                var dzId = dz.element.id;
                
                if (dz.files && dz.files.length > 0) {
                    console.log('Guardando ' + dz.files.length + ' archivos del dropzone: ' + dzId);
                    
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
                            } : null,
                            accepted: file.accepted,
                            processing: false,
                            previewElement: file.previewElement ? file.previewElement.outerHTML : null
                        };
                    });
                    
                    // Guardar también el input value
                    var fieldId = dzId.replace('wapf-dz-', '');
                    var $input = $('input[data-field-id="' + fieldId + '"]');
                    if ($input.length) {
                        persistentUploads[fieldId] = $input.val();
                        console.log('Valor guardado del campo ' + fieldId + ': ' + $input.val());
                    }
                }
            });
        };
        
        // Función para restaurar todos los archivos
        var restoreAllDropzoneFiles = function() {
            if (typeof Dropzone === 'undefined' || !Dropzone.instances.length) {
                console.log('⚠️ No hay dropzones disponibles para restaurar');
                return;
            }
            
            console.log('📂 Restaurando archivos en ' + Dropzone.instances.length + ' dropzones...');
            var filesRestored = 0;
            
            Dropzone.instances.forEach(function(dz) {
                var dzId = dz.element.id;
                
                if (persistentFiles[dzId] && persistentFiles[dzId].length > 0) {
                    console.log('Restaurando ' + persistentFiles[dzId].length + ' archivos en: ' + dzId);
                    
                    var $dzElement = $('#' + dzId);
                    
                    // Restaurar archivos
                    persistentFiles[dzId].forEach(function(fileData) {
                        // Verificar si el archivo ya existe
                        var exists = dz.files.some(function(f) {
                            return f.upload && fileData.upload && 
                                   f.upload.uuid === fileData.upload.uuid;
                        });
                        
                        if (!exists) {
                            var mockFile = {
                                name: fileData.name,
                                size: fileData.size,
                                type: fileData.type,
                                status: Dropzone.SUCCESS,
                                accepted: true,
                                processing: false,
                                dataURL: fileData.dataURL,
                                upload: fileData.upload
                            };
                            
                            dz.files.push(mockFile);
                            dz.emit("addedfile", mockFile);
                            
                            if (fileData.dataURL && fileData.type && fileData.type.indexOf('image') !== -1) {
                                dz.emit("thumbnail", mockFile, fileData.dataURL);
                            }
                            
                            dz.emit("complete", mockFile);
                            filesRestored++;
                        }
                    });
                    
                    // Restaurar el valor del input
                    var fieldId = dzId.replace('wapf-dz-', '');
                    if (persistentUploads[fieldId]) {
                        var $input = $('input[data-field-id="' + fieldId + '"]');
                        if ($input.length) {
                            $input.val(persistentUploads[fieldId]).trigger('change');
                            console.log('✅ Input restaurado: ' + persistentUploads[fieldId]);
                        }
                    }
                    
                    // Ocultar el mensaje de "arrastrar archivos"
                    if (dz.files.length > 0) {
                        $dzElement.find('.dz-message').hide();
                    }
                } else {
                    console.log('⚠️ No hay archivos guardados para: ' + dzId);
                }
            });
            
            console.log('✅ Restauración completa: ' + filesRestored + ' archivos restaurados');
        };
        
        // Guardar archivos cuando se suben
        $(document).on('wapf/file_uploaded', function(e, data) {
            setTimeout(saveAllDropzoneFiles, 100);
            setTimeout(forceEnableButton, 150);
        });
        
        // Cargar html2canvas si no está disponible
        var loadHtml2Canvas = function(callback) {
            if (typeof html2canvas !== 'undefined') {
                callback();
                return;
            }
            
            var script = document.createElement('script');
            script.src = '<?php echo get_stylesheet_directory_uri(); ?>/html2canvas.min.js';
            script.onload = callback;
            script.onerror = function() {
                console.log('Error cargando html2canvas');
                callback();
            };
            document.head.appendChild(script);
        };
        
        // Capturar preview del producto antes de añadir al carrito
        var captureProductPreview = function(callback) {
            var $activeImage = $('.woocommerce-product-gallery__image.flex-active-slide img, .woocommerce-product-gallery__image:first img').first();
            
            if (!$activeImage.length) {
                console.log('No se encontró imagen del producto para capturar');
                if (callback) callback();
                return;
            }
            
            // Verificar si hay overlay LCP
            var $container = $activeImage.closest('.woocommerce-product-gallery__image');
            var $lcpWrap = $container.find('.lcp-wrap');
            
            if ($lcpWrap.length > 0) {
                console.log('Capturando preview con LCP...');
                // Cargar y usar html2canvas para capturar el contenedor completo con LCP
                loadHtml2Canvas(function() {
                    if (typeof html2canvas === 'undefined') {
                        console.log('html2canvas no disponible después de cargar');
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
                            formData.append('product_id', $('input[name=product_id], input[name=add-to-cart]').val());
                            formData.append('variation_id', $('input[name=variation_id]').val() || '');
                            
                            $.ajax({
                                url: wapf_config.ajax,
                                type: 'POST',
                                data: formData,
                                processData: false,
                                contentType: false,
                                success: function(response) {
                                    console.log('Preview guardada:', response);
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
                                    console.log('Error guardando preview');
                                    if (callback) callback();
                                }
                            });
                        }, 'image/png', 0.95);
                    }).catch(function(error) {
                        console.log('Error en html2canvas:', error);
                        if (callback) callback();
                    });
                });
            } else {
                // No hay LCP, usar la imagen del producto directamente
                console.log('No hay LCP, usando imagen del producto');
                var imageUrl = $activeImage.attr('src') || $activeImage.attr('data-large_image') || $activeImage.attr('data-src');
                if (imageUrl) {
                    var $input = $('input[name=wapf_product_preview_url]');
                    if (!$input.length) {
                        $input = $('<input type="hidden" name="wapf_product_preview_url">');
                        $('form.cart').append($input);
                    }
                    $input.val(imageUrl);
                }
                if (callback) callback();
            }
        };
        
        // Guardar antes de añadir al carrito y capturar preview
        $(document).on('click', '.single_add_to_cart_button', function(e) {
            var now = Date.now();
            
            // Prevenir clicks múltiples en menos de 1 segundo
            if (now - lastAddToCartTime < 1000) {
                console.log('Click ignorado - demasiado rápido');
                e.preventDefault();
                e.stopImmediatePropagation();
                return false;
            }
            
            console.log('Click en añadir al carrito - guardando archivos');
            
            // CRÍTICO: Marcar INMEDIATAMENTE que debe preservar
            window.wapf_preserve_files = true;
            
            // IMPORTANTE: Guardar archivos ANTES de cualquier otra cosa
            saveAllDropzoneFiles();
            
            // Si hay archivos subidos, capturar preview
            if (typeof Dropzone !== 'undefined') {
                var hasFiles = Dropzone.instances.some(function(dz) {
                    return dz.files && dz.files.length > 0;
                });
                
                if (hasFiles && !isCapturingPreview && !$(this).data('preview-captured')) {
                    e.preventDefault();
                    e.stopPropagation();
                    e.stopImmediatePropagation();
                    
                    isCapturingPreview = true;
                    isAddingToCart = true;
                    lastAddToCartTime = now;
                    
                    var $btn = $(this);
                    var originalText = $btn.text();
                    $btn.text('Preparando...');
                    $btn.data('preview-captured', true);
                    
                    console.log('Capturando preview antes de añadir...');
                    captureProductPreview(function() {
                        isCapturingPreview = false;
                        $btn.text(originalText);
                        
                        console.log('Preview capturada, enviando formulario...');
                        
                        // Guardar una última vez antes de enviar
                        saveAllDropzoneFiles();
                        
                        // Permitir que el formulario se envíe normalmente
                        $btn.closest('form.cart').trigger('submit');
                        
                        // Resetear después de 2 segundos
                        setTimeout(function() {
                            isAddingToCart = false;
                            $btn.removeData('preview-captured');
                        }, 2000);
                    });
                    
                    return false;
                } else {
                    // No hay archivos o ya se capturó
                    lastAddToCartTime = now;
                    isAddingToCart = true;
                    
                    setTimeout(function() {
                        isAddingToCart = false;
                    }, 2000);
                }
            }
        });
        
        // Cuando se encuentra una variación, forzar botón habilitado
        $('form.variations_form').on('found_variation', function() {
            setTimeout(forceEnableButton, 300);
        });
        
        // Cuando cambia una variación
        $('form.variations_form').on('woocommerce_variation_select_change', function() {
            setTimeout(forceEnableButton, 300);
        });
        
        // IMPORTANTE: Interceptar el evento added_to_cart ANTES que el plugin WAPF
        // Usar captura de eventos (useCapture = true) para ejecutar primero
        document.body.addEventListener('added_to_cart', function(event) {
            console.log('Producto añadido al carrito - PRESERVANDO archivos');
            
            // Marcar globalmente que debe preservar
            window.wapf_preserve_files = true;
            
            // Guardar una última vez antes de que el plugin intente limpiar
            saveAllDropzoneFiles();
            
        }, true); // useCapture = true para ejecutar ANTES que otros listeners
        
        // Nuestro event listener principal que restaura
        $(document.body).on('added_to_cart', function(event, fragments, cart_hash, button) {
            console.log('Restaurando archivos después de añadir al carrito');
            
            // Resetear flags para permitir nueva captura
            $('.single_add_to_cart_button').removeData('preview-captured');
            $('input[name=wapf_product_preview_url]').remove();
            
            // Resetear control de tiempo
            isAddingToCart = false;
            lastAddToCartTime = Date.now();
            
            // Restaurar archivos inmediatamente y múltiples veces
            restoreAllDropzoneFiles();
            
            setTimeout(function() {
                console.log('Segunda restauración de archivos...');
                restoreAllDropzoneFiles();
                forceEnableButton();
                
                // Asegurar que se mantengan visibles
                if (typeof Dropzone !== 'undefined') {
                    Dropzone.instances.forEach(function(dz) {
                        if (dz.files.length > 0) {
                            $('#' + dz.element.id).find('.dz-message').hide();
                        }
                    });
                }
            }, 300);
            
            setTimeout(function() {
                console.log('Tercera restauración de archivos...');
                restoreAllDropzoneFiles();
                
                // Resetear flag después de completar todas las restauraciones
                setTimeout(function() {
                    window.wapf_preserve_files = false;
                    console.log('✅ Flag de preservación reseteado - limpieza manual permitida');
                }, 2000); // Esperar 2 segundos adicionales
            }, 1000); // Cambiar a 1 segundo
            
            // Restauración adicional después de más tiempo por si acaso
            setTimeout(function() {
                if (persistentFiles && Object.keys(persistentFiles).length > 0) {
                    console.log('Cuarta restauración de seguridad...');
                    restoreAllDropzoneFiles();
                }
            }, 1500);
        });
        
        // Guardar archivos periódicamente
        setInterval(saveAllDropzoneFiles, 2000);
        
        // Guardar al cargar
        $(window).on('load', function() {
            setTimeout(saveAllDropzoneFiles, 1000);
        });
        
        // Prevenir que se limpien los archivos - VERSIÓN SUPER AGRESIVA
        var setupDropzoneProtection = function() {
            if (typeof Dropzone === 'undefined' || !Dropzone.instances.length) {
                setTimeout(setupDropzoneProtection, 500);
                return;
            }
            
            Dropzone.instances.forEach(function(dz) {
                var dzId = dz.element.id;
                
                // Interceptar la función removeAllFiles - BLOQUEAR SIEMPRE si hay archivos guardados
                var originalRemoveAll = dz.removeAllFiles.bind(dz);
                dz.removeAllFiles = function(cancelIfNecessary) {
                    // Si hay archivos guardados, NUNCA permitir limpiar
                    if (persistentFiles[dzId] && persistentFiles[dzId].length > 0) {
                        console.log('🛡️ BLOQUEADO removeAllFiles() en ' + dzId + ' - hay archivos guardados');
                        // Restaurar inmediatamente
                        setTimeout(function() {
                            console.log('Restaurando inmediatamente después de intento de limpieza...');
                            restoreAllDropzoneFiles();
                        }, 50);
                        return; // NO ejecutar la limpieza
                    }
                    // Solo si NO hay archivos guardados, permitir
                    console.log('Limpieza permitida en ' + dzId + ' - no hay archivos guardados');
                    return originalRemoveAll(cancelIfNecessary);
                };
                
                // Permitir eliminar archivos individuales solo si no está preservando
                dz.on('removedfile', function(file) {
                    if (!window.wapf_preserve_files) {
                        saveAllDropzoneFiles();
                    } else {
                        // Si está preservando y alguien eliminó un archivo, restaurar
                        console.log('Archivo eliminado durante preservación - restaurando...');
                        setTimeout(restoreAllDropzoneFiles, 100);
                    }
                });
                
                // Sobrescribir eventos que deshabilitan el botón
                dz.off('sending');
                dz.off('complete');
                
                // Asegurar que el botón esté habilitado después de subir
                dz.on('complete', function() {
                    setTimeout(forceEnableButton, 200);
                });
            });
            
            console.log('✅ Protección de Dropzone activada');
        };
        
        setupDropzoneProtection();
        
        // MONITOR CONTINUO: Detectar si los archivos desaparecen y restaurarlos inmediatamente
        var monitorFiles = function() {
            if (typeof Dropzone === 'undefined' || !Dropzone.instances.length) return;
            
            Dropzone.instances.forEach(function(dz) {
                var dzId = dz.element.id;
                
                // Si tenemos archivos guardados pero el dropzone está vacío, restaurar
                if (persistentFiles[dzId] && persistentFiles[dzId].length > 0 && dz.files.length === 0) {
                    console.log('⚠️ ALERTA: Archivos desaparecidos de ' + dzId + ' - RESTAURANDO...');
                    restoreAllDropzoneFiles();
                }
            });
        };
        
        // Monitorear cada 500ms
        setInterval(monitorFiles, 500);
        
        // Observador simplificado para mantener el botón habilitado
        var observeButton = function() {
            var $button = $('form.cart .single_add_to_cart_button, .single_add_to_cart_button');
            
            if ($button.length > 0 && typeof MutationObserver !== 'undefined') {
                var observer = new MutationObserver(function(mutations) {
                    if (isAddingToCart) return; // No interferir durante el proceso
                    
                    mutations.forEach(function(mutation) {
                        if (mutation.type === 'attributes' && mutation.attributeName === 'disabled') {
                            // Solo revertir si hay archivos subidos
                            if (typeof Dropzone !== 'undefined') {
                                var hasFiles = Dropzone.instances.some(function(dz) {
                                    return dz.files && dz.files.length > 0;
                                });
                                
                                if (hasFiles) {
                                    setTimeout(forceEnableButton, 100);
                                }
                            }
                        }
                    });
                });
                
                $button.each(function() {
                    observer.observe(this, {
                        attributes: true,
                        attributeFilter: ['disabled']
                    });
                });
            }
        };
        
        // Iniciar observador una sola vez
        setTimeout(observeButton, 1000);
    });
    </script>
    <?php
}
add_action('wp_footer', 'prevent_wapf_upload_clear_on_add_to_cart', 1001);
