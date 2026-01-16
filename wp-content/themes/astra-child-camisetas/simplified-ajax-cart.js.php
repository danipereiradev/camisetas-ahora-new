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
        
        // Habilitar botón de añadir al carrito
        var forceEnableButton = function() {
            if (isAddingToCart) return;
            var $button = $('.single_add_to_cart_button');
            if ($button.length > 0) {
                $button.prop('disabled', false).removeAttr('disabled').removeClass('disabled wc-variation-selection-needed');
            }
        };
        setInterval(forceEnableButton, 200);
        
        // Variables para protección de Dropzone (solo en memoria)
        var persistentFiles = {};
        
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
                }
            });
        };
        
        // Restaurar archivos de Dropzone
        var restoreDropzoneFiles = function() {
            if (typeof Dropzone === 'undefined' || !Dropzone.instances.length) return;
            
            Dropzone.instances.forEach(function(dz) {
                var dzId = dz.element.id;
                
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
                    
                    if (dz.files.length > 0) {
                        $('#' + dzId).find('.dz-message').hide();
                    }
                }
            });
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
                
                dz.removeAllFiles = function(cancelIfNecessary) {
                    if (persistentFiles[dzId] && persistentFiles[dzId].length > 0) {
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
        
        // Cuando se sube un archivo, capturar preview UNA VEZ
        $(document).on('wapf/file_uploaded', function() {
            setTimeout(saveDropzoneFiles, 100);
            setTimeout(forceEnableButton, 150);
            
            // Capturar preview solo la primera vez
            if (!$('input[name=wapf_product_preview_url]').val()) {
                setTimeout(function() {
                    captureProductPreview();
                }, 800);
            }
        });
        
        // AJAX add-to-cart
        $(document).on('click', '.single_add_to_cart_button', function(e) {
            var now = Date.now();
            
            if (now - lastAddToCartTime < 1000) {
                e.preventDefault();
                e.stopImmediatePropagation();
                return false;
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
        });
        
        setupDropzoneProtection();
        
        // Monitor para restaurar archivos si desaparecen
        setInterval(function() {
            if (typeof Dropzone !== 'undefined' && Dropzone.instances.length) {
                Dropzone.instances.forEach(function(dz) {
                    var dzId = dz.element.id;
                    if (persistentFiles[dzId] && persistentFiles[dzId].length > 0 && dz.files.length === 0) {
                        restoreDropzoneFiles();
                    }
                });
            }
        }, 500);
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
