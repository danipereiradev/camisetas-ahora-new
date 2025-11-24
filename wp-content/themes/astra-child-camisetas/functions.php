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
 * Cambiar la imagen del carrito para mostrar la imagen personalizada subida
 * en lugar de la imagen base del producto
 */
function wapf_change_cart_item_thumbnail( $product_image, $cart_item, $cart_item_key ) {
    
    // Verificar si el item tiene campos WAPF con archivos subidos
    if ( ! isset( $cart_item['wapf'] ) || ! is_array( $cart_item['wapf'] ) ) {
        return $product_image;
    }
    
    // Buscar el primer campo de tipo archivo con una imagen
    foreach ( $cart_item['wapf'] as $field ) {
        
        // Solo procesar campos de tipo file
        if ( ! isset( $field['type'] ) || $field['type'] !== 'file' ) {
            continue;
        }
        
        // Verificar que tenga valor
        if ( empty( $field['raw'] ) ) {
            continue;
        }
        
        // Obtener las URLs de los archivos
        $upload_dir = wp_upload_dir();
        $files = explode( ',', $field['raw'] );
        
        foreach ( $files as $file ) {
            $file = trim( $file );
            
            if ( empty( $file ) ) {
                continue;
            }
            
            // Construir la URL completa
            // Si ya es una URL completa (order again), usarla directamente
            if ( strpos( $file, 'http://' ) === 0 || strpos( $file, 'https://' ) === 0 ) {
                $file_url = $file;
            } else {
                $file_url = trailingslashit( $upload_dir['baseurl'] ) . 'wapf/' . $file;
            }
            
            // Verificar si es una imagen
            $image_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp'];
            $file_extension = strtolower( pathinfo( $file_url, PATHINFO_EXTENSION ) );
            
            if ( in_array( $file_extension, $image_extensions ) ) {
                // Encontramos una imagen - usarla como thumbnail del carrito
                
                // Obtener el producto para las dimensiones
                $product = $cart_item['data'];
                $thumbnail_size = apply_filters( 'woocommerce_cart_item_thumbnail_size', 'woocommerce_thumbnail' );
                
                // Crear el HTML de la imagen
                $custom_image = sprintf(
                    '<a href="%s"><img src="%s" class="attachment-woocommerce_thumbnail size-woocommerce_thumbnail" alt="%s" loading="lazy" /></a>',
                    esc_url( wc_get_cart_url() ),
                    esc_url( $file_url ),
                    esc_attr( $product->get_name() )
                );
                
                // Retornar la imagen personalizada en lugar de la imagen base
                return $custom_image;
            }
        }
    }
    
    // Si no se encontró ninguna imagen personalizada, retornar la imagen por defecto
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
