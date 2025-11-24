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
    if (isset($_POST['wapf_lcp_preview_url']) && !empty($_POST['wapf_lcp_preview_url'])) {
        $cart_item_data['wapf_lcp_preview'] = esc_url_raw($_POST['wapf_lcp_preview_url']);
    }
    return $cart_item_data;
}
add_filter('woocommerce_add_cart_item_data', 'wapf_lcp_save_preview_to_cart', 10, 4);

function wapf_change_cart_item_thumbnail($product_image, $cart_item, $cart_item_key) {
    if (isset($cart_item['wapf_lcp_preview']) && !empty($cart_item['wapf_lcp_preview'])) {
        $product = $cart_item['data'];
        $custom_image = sprintf(
            '<a href="%s"><img src="%s" class="attachment-woocommerce_thumbnail size-woocommerce_thumbnail" alt="%s" loading="lazy" /></a>',
            esc_url(wc_get_cart_url()),
            esc_url($cart_item['wapf_lcp_preview']),
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
