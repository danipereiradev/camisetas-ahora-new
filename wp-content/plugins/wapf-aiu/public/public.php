<?php 

function wapf_aiu_get_pdf_page_count( $file ) {
    
    $count = 0;

    if ( extension_loaded( 'imagick' ) ) {
        $pdf = new Imagick( $file );
        $count = $pdf->getNumberImages();
    } else {
        $pdf = file_get_contents( $file );
        $count = preg_match_all("/\/Page\W/", $pdf, $dummy );
    }
    
    return $count;
    
}

function wapf_aiu_is_animated_gif($filename) {
	if(!($fh = @fopen($filename, 'rb'))) return false;
	$count = 0;

	$chunk = false;
	while(!feof($fh) && $count < 2) {
		$chunk = ($chunk ? substr($chunk, -20) : "") . fread($fh, 1024 * 100); //read 100kb at a time
		$count += preg_match_all('#\x00\x21\xF9\x04.{4}\x00(\x2C|\x21)#s', $chunk, $matches);
	}

	fclose($fh);
	return $count > 1;
}

function wapf_aiu_is_image( $str ) {

    $info = pathinfo( $str );

    if( ! empty( $info['extension'] ) ) {

        $ext = strtolower( $info['extension'] );

        return in_array( $ext, [ 'png', 'jpg', 'jpeg', 'gif', 'bmp', 'webp', 'tiff'] );

    }

    return false;

}

function wapf_aiu_is_pdf( $file ) {

    // Simple check first.
    if( strtolower( pathinfo( $file, PATHINFO_EXTENSION ) ) === 'pdf' ) {
        
        // Better check.
        $finfo = finfo_open( FILEINFO_MIME_TYPE );
        $mime = finfo_file( $finfo, $file );
        finfo_close( $finfo );

        // Better check for PDF file.
        return $mime === 'application/pdf';
        
    }
    
    return false;
    
}

// Register assets
add_action( 'wp_enqueue_scripts', function() {

	if( get_option('wapf_aiu_editor', 'yes') === 'yes' ) {

		$url =  trailingslashit(plugin_dir_url(WAPFAIU_STARTFILE));

		wp_enqueue_style('wapf-aiu', $url . 'assets/css/frontend.min.css', [], WAPFAIU_VERSION);
		wp_enqueue_script('wapf-aiu', $url . 'assets/js/frontend.min.js', ['jquery'], WAPFAIU_VERSION, true);

        $script_vars = [
            'imgQuality' => get_option('wapf_aiu_crop', '0.88')
        ];

        wp_localize_script('wapf-aiu', 'aiuConfig', $script_vars);

	}

});

// Always enable the ajax uploader
add_filter( 'wapf_upload_ajax', function($enabled) {
    return true;
} );

add_filter( 'wapf/validate/file', function($validation_result, $file, $field) {

	if( !empty($field->options['minsize']) ) {
		
		$minsize = floatval($field->options['minsize']) * pow(1024,2); // MB To bytes

		if( $minsize > 0 && $minsize > $file['size']) {
			
			$validation_result['error'] = true;
            $validation_result['message'] = apply_filters( 'wapf/message/min_file_size_required', __( 'The filesize is too small. Please upload a bigger file.', 'wapf-aiu' ) );

			return $validation_result;
		}
		
	}
	
	
	$min_width = !empty($field->options['minwidth']) ? intval($field->options['minwidth']) : 0;
	$min_height = !empty($field->options['minheight']) ? intval($field->options['minheight']) : 0;

    $max_width = isset( $field->options['maxwidth'] ) ? intval($field->options['maxwidth']) : 0;
    $max_height = isset( $field->options['maxheight'] ) ? intval($field->options['maxheight']) : 0;

    if( empty( $field->options['auto_resize'] ) && ( $max_width > 0 || $max_height > 0 ) ) {

        $size = wp_getimagesize( $file['tmp_name'] );
        if( ! empty( $size ) && is_array( $size ) && count( $size ) >= 2 ) {

            $width = $size[0];
            $height = $size[1];

            if( $max_width > 0 && $width > $max_width ) {
                $validation_result['error'] = true;
                $validation_result['message'] = sprintf( apply_filters( 'wapf/message/image_max_width_exceeded', __( 'The image width is %spx, but only a maximum width of %spx is allowed.', 'wapf-aiu' )),  $width, $max_width );
            }

            if( $max_height > 0 && $height > $max_height ) {
                $validation_result['error'] = true;
                $validation_result['message'] = sprintf( apply_filters( 'wapf/message/image_max_height_exceeded', __( 'The image height is %spx, but only a maximum height of %spx is allowed.', 'wapf-aiu' )),  $height, $max_height );
            }

            if( $validation_result['error'] ) {
                return $validation_result;
            }

        }
    }

    if( $min_width > 0 || $min_height > 0 ) {
		
		$size = wp_getimagesize( $file['tmp_name'] );

		if( ! empty( $size ) && is_array( $size ) && count( $size ) >= 2 ) {
			
			$width = $size[0];
			$height = $size[1];
					
			if( $min_width > 0 && $min_width > $width ) {
				$validation_result['error'] = true;
                $validation_result['message'] = sprintf( apply_filters( 'wapf/message/image_min_width_required', __( 'The image width is %spx, but a minimum width of %spx is required.', 'wapf-aiu' )),  $width, $min_width );
            }
			
			if( $min_height > 0 && $min_height > $height ) {
				$validation_result['error'] = true;
                $validation_result['message'] = sprintf( apply_filters( 'wapf/message/image_min_height_required', __( 'The image height is %spx, but a minimum height of %spx is required.', 'wapf-aiu' )),  $height, $min_height );
			}
			
			if( $validation_result['error'] ) { 
				return $validation_result;
			}
			
		}

	}

	
	return $validation_result;
		
}, 10, 3);

// Validate when adding to cart
add_filter( 'wapf/validate' , function( $validation_result, $value, $field, $product_id, $clone_index ) {
	
	if($field->type === 'file') {
		
		$value = sanitize_text_field($value);
		
		if( empty($value) ) {
			return $validation_result;
		}
		
		$multiple_allowed = isset($field->options['multiple']) && $field->options['multiple'] == 1;

		if($multiple_allowed) {

		    $file_names = array_map('trim', explode(',', $value));

			$base_dir = wp_upload_dir();
			$upload_dir = trailingslashit($base_dir['basedir']) . 'wapf/';

			$counted_files = 0;
			
			foreach($file_names as $fn) {
				if( file_exists( $upload_dir . $fn ) ) {
					$counted_files++;
				}
			}

			if(isset($field->options['min_filecount'])) {
				
				$min_filecount = intval($field->options['min_filecount']);
				
				if($min_filecount > 0 && $min_filecount > $counted_files) {
					$validation_result['error'] = true;

                    $validation_result['message'] = sprintf(
						apply_filters('wapf/message/min_filecount_required', _n(
							'The field "%s" requires a minimum of %s files, but %s was uploaded.',
							'The field "%s" requires a minimum of %s files, but %s were uploaded.',
							$counted_files,
							'wapf-aiu'
						) ),
						$field->get_label(),
						$min_filecount,
						$counted_files 
					);
				}
				
			}
			
			if(isset($field->options['max_filecount'])) {
				
				$max_filecount = intval($field->options['max_filecount']);
				
				if($max_filecount > 0 && $max_filecount < $counted_files) {
					$validation_result['error'] = true;


					$validation_result['message'] = sprintf(
                            apply_filters('wapf/message/max_filecount_exceeded', _n(
							'The field "%s" requires a maximum of %s files, but %s was uploaded.',
							'The field "%s" requires a maximum of %s files, but %s were uploaded.',
							$counted_files,
							'wapf-aiu'
						) ),
						$field->get_label(),
						$max_filecount,
						$counted_files 
					);
				}
				
			}
			
			// Remove the files before returning the error.
			if($validation_result['error']) {
				
				foreach($file_names as $fn) {
					if( file_exists( $upload_dir . $fn ) ) {
						unlink($upload_dir . $fn);
					}
				}
				
				return $validation_result;
				
			}
			
			

		}
		
	}
	
	return $validation_result;
	
}, 10, 5);

add_filter( 'wapf/ajax_file_upload_config', function($options, $field) {
    
	if( isset( $field->options['img_editor'] ) && $field->options['img_editor'] ) {
		
		$cropper_enabled = isset($field->options['img_cropper']) && $field->options['img_cropper'];
		$rotation_enabled = isset($field->options['img_rotate']) && $field->options['img_rotate'];
		$flip_enabled = isset($field->options['img_flip']) && $field->options['img_flip'];
        $open_type = empty( $field->options['editor_open'] ) ? 'button' : ( sanitize_text_field( $field->options['editor_open'] ) );
        $min_width = empty( $field->options['minwidth'] ) ? 0 : intval( $field->options['minwidth'] );
        $min_height = empty( $field->options['minheight'] ) ? 0 : intval( $field->options['minheight'] );

		$editor_enabled = $cropper_enabled || $rotation_enabled || $flip_enabled;
        
        $editor_config = [
            'cropper' 	=> false,
            'rotate' 	=> false,
            'flip'		=> false,
        ];

		if( $editor_enabled ) {

			$editor_config = [
				'cropper' 	=> $cropper_enabled,
				'rotate' 	=> $rotation_enabled,
				'flip'		=> $flip_enabled,
                'onlyCrop'  => $cropper_enabled && ! $rotation_enabled && ! $flip_enabled,
                'openType'  => $open_type
			];
			
			if($cropper_enabled && !empty($field->options['cropper_ratio']) ) {
				
				$editor_config['ratio'] = floatval($field->options['cropper_ratio']);
				
			}

            if( $min_width > 0 ) {
                $editor_config['minWidth'] = $min_width;
            }

            if( $min_height > 0 ) {
                $editor_config['minHeight'] = $min_height;
            }
				
		}
        
        if( !empty( $field->options['maxsize'] ) ) {
            $max_size = intval($field->options['maxsize']);
            if($max_size >= 10)
                $options['maxThumbnailFilesize'] = $max_size;
        }
	
		ob_start();
        
        $options['previewTemplate'] = str_replace( '<div class="dz-preview">', '<div class="dz-preview aiu-preview" data-editor-config="' . _wp_specialchars( wp_json_encode($editor_config), ENT_QUOTES, 'UTF-8', true ) . '">',  $options['previewTemplate'] );

        if( $editor_enabled ) {
            $options[ 'previewTemplate' ] = str_replace( '<div class="dz-remove"', '<div class="start-crop wapf-tt-wrap" data-dir="t"  data-tip="' . __('Edit image', 'wapf-aiu') . '" style="display:none"><svg fill="currentColor" xmlns="http://www.w3.org/2000/svg" width=".8rem" height=".8rem" viewBox="0 0 512 512"><path d="M497.9 142.1l-46.1 46.1c-4.7 4.7-12.3 4.7-17 0l-111-111c-4.7-4.7-4.7-12.3 0-17l46.1-46.1c18.7-18.7 49.1-18.7 67.9 0l60.1 60.1c18.8 18.7 18.8 49.1 0 67.9zM284.2 99.8L21.6 362.4.4 483.9c-2.9 16.4 11.4 30.6 27.8 27.8l121.5-21.3 262.6-262.6c4.7-4.7 4.7-12.3 0-17l-111-111c-4.8-4.7-12.4-4.7-17.1 0zM88 424h48v36.3l-64.5 11.3-31.1-31.1L51.7 376H88v48z"/></svg></div><div class="dz-remove"',  $options['previewTemplate'] );
        }
		
		$model = $editor_config;
		
		include dirname( __FILE__ ) . '/image-editor.php';
		
		$template =  ob_get_clean();
		
		$options['editorTemplate'] = $template;
		$options['disablePreviews'] = false;

	}
	
	return $options;
	
},10, 2);

add_action( 'wp_footer', function() {
    
    ?>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            (function($) {

                $(document).on( 'wapf/file_uploaded', function(e,data) {
                    var pc = $('.input-'+data.fieldId).data('pageCount') || 0;
                    $('.input-' + data.fieldId).data( 'pageCount', pc + ( data.response[0].page_count || 0  ) );
                } );
                
                $(document).on( 'wapf/file_deleted', function(e,data) {
                    $('.input-' + data.fieldId).data( 'pageCount', Object.values(data.uploads || {}).reduce((sum, { fileCount = 0 }) => sum + fileCount, 0) );
                });

                if(WAPF) {
                    WAPF.Util.formulas.pdfPages = function(args, $parent ) {
                        return $('.input-' + args[0]).data('pageCount') || 0;
                    }
                }
                
            })(jQuery);
        });
    </script>

    <?php 
    if( get_option('wapf_aiu_editor', 'yes') !== 'yes' ) return;

    ?>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            (function($) {
                if( window.Dropzone && window.Dropzone.instances) {
                    window.Dropzone.instances.forEach(function(i){
                        i.options.transformFile = function (file, done) {
    
                            var openEditor = function( dataURL ) {
                                window.wapfaiu.initCropper( i, file, dataURL, function (f) {
                                    i.createThumbnail(f, i.options.thumbnailWidth, i.options.thumbnailHeight, i.options.thumbnailMethod, false, function (dataURL) {
                                        i.emit('thumbnail', file, dataURL);
                                        file.transformedFile = f;
                                        done(f);
                                    });
    
                                }, function () {
                                    i.removeFile(file);
                                }, true, editorConfig.openType, $preview);
                            };
    
                            if( file.openedWithEdit ) return done(file);
                            var $preview = $(file.previewElement);
                            if (!$preview.length) return done(file);
                            var editorConfig = $preview.data('editor-config');
    
                            if ( ! editorConfig || ! editorConfig.openType || editorConfig.openType === 'button' || file.type.indexOf('image') == -1 || file.type === 'image/heic' )
                                return done(file);
    
                            var fr = new FileReader();
                            fr.onload = function( f ) {
                                var img = new Image();
                                img.onload = function() {
    
                                    if( editorConfig.openType === 'crop_only' && editorConfig.ratio &&  wapfaiu.compareRatio(img.width,img.height, editorConfig.ratio) )
                                        return done(file);
                                    openEditor(f.target.result);
                                };
                                img.src = f.target.result;
                            };
                            fr.readAsDataURL(file);
    
                        }
    
                    });
                }
    
                $(document).on( 'wapf/file_uploaded', function(e,data) {
                    var $preview = $( data.file.previewElement );
                    if(!$preview.length) return;
                    var editorConfig = $preview.data('editor-config');
                    if( !editorConfig || data.file.type.indexOf( 'image' ) == -1 || data.file.type === 'image/heic' ) return;
                    var dropzone = $('#wapf-dz-'+ data.fieldId)[0].dropzone;
                    if ( ! dropzone ) return;
                    if( editorConfig.openType === 'crop_only' && ! editorConfig.ratio ) return;
    
                    window.wapfaiu.initCropper( dropzone, data.file, data.file.transformedFile ? data.file.transformedFile.dataURL : data.file.dataURL, null, null, false, editorConfig.openType, $preview);
                });
                
            })(jQuery);
        });
    </script>
    <?php
} );

add_filter('wapf/file/upload_result', function($upload_result, $field) {
	
	// Bail early when upload failed.
	if( !empty( $upload_result['error'] ) ) 
		return $upload_result;
	
	// Bail early when no resize needed, or possible
	if( empty( $field->options['auto_resize'] ) )
		return $upload_result;
		
	$file_type = $upload_result['type'];
	$valid_types = array('image/gif','image/png','image/jpeg','image/jpg');
	
	// Bail when filetype is different
	if( ! in_array( $file_type, $valid_types ) )
		return $upload_result;
	
	$resize_width = isset( $field->options['maxwidth'] ) ? intval($field->options['maxwidth']) : 0;
	$resize_height = isset( $field->options['maxheight'] ) ? intval($field->options['maxheight']) : 0;
	
	if($resize_width > 0 || $resize_height > 0) {
		
		$file_path = $upload_result['file'];
		
		// We can't resize animated gifs
		if( $file_type === 'image/gif' && wapf_aiu_is_animated_gif($file_path) ) {
			return $upload_result;
		}
		
		$image_editor = wp_get_image_editor($file_path);
		
		if( is_wp_error($image_editor) ) {
			return $upload_result;
		}
		
		$sizes = $image_editor->get_size();

		if( ( isset($sizes['width']) && $sizes['width'] > $resize_width ) || ( isset($sizes['height']) && $sizes['height'] > $resize_height ) ) {
			
			$image_editor->resize(
				$resize_width > 0 ? $resize_width : null,
				$resize_height > 0 ? $resize_height : null, 
				$resize_width > 0 && $resize_height > 0 // Crop when both width/height have been entered, otherwise resize.
			);
			
			$image_editor->save($file_path);
			
		}
		
		
	}

	return $upload_result;
	
} , 10, 2);

add_filter('wapf/cart/cart_item_field', function($cart_item_field, $field, $clone_idx) {

    $cart_item_field['type'] = $field->type;
    return $cart_item_field;

}, 10, 3);

add_filter('wapf/cart/item_data', function($item_data, $cart_item) {

	if(empty($cart_item['wapf']) || ! is_array($cart_item['wapf']) || !is_array($item_data) )
		return $item_data;

	if( get_option('wapf_aiu_show_thumbs','no') === 'no' )
	    return $item_data;

	foreach($cart_item['wapf'] as $field) {

	    if(isset($field['type']) && $field['type'] === 'file') {

	        $thumb_width = intval(get_option('wapf_aiu_thumb_size', 100));
	        $thumb_width = $thumb_width < 0 ? 100 : $thumb_width;

	        $upload_dir = wp_upload_dir();

	        foreach($item_data as &$data) {

                if($data['key'] === $field['label'] ) {
                    //$display = $data['display'];
                    $display ='<div class="wapf-upload-cart-thumb wapf-upload-thumbs">';
                    $urls = [];
                    
                    $raw_split = explode( ',', $field['raw'] );
                    $pricing_hint = isset( $field['values'][0]['pricing_hint'] ) ? $field['values'][0]['pricing_hint'] : '';
                    
                    foreach( $raw_split as $i => $file ) {

                        // When "order again", file will already be a URL, otherwise it is just the path.
                        $file_url = strpos( $file, is_ssl() ? 'https://' : 'http://') === 0 ? trim( $file ) : trailingslashit( $upload_dir['baseurl'] ) . 'wapf/' . trim( $file );

                        $split = explode( '/', $file_url );
                        $urls[] = '<a href="' . esc_url( $file_url ) . '" target="_blank">' . esc_html( $split[ count( $split ) - 1 ] ) . '</a>';

                        // Not all files are images (maybe a PDF or something can also be uploaded)
                        if ( wapf_aiu_is_image( $file_url ) ) {
                            $display .= sprintf( '<img style="max-width:'.$thumb_width.'px" src="%s" />', esc_url($file_url ) );
                        }
                    }

                    $display .='</div>';

                    $data['display'] = join(', ', $urls) . $pricing_hint . '<div>' . $display . '</div>';

                }
            }

        }
    }

	return $item_data;

}, 10, 2);

add_filter( 'wapf/order_item/meta_display_value', function( $display_value, $meta_field ) {

    if(isset($meta_field['type']) && $meta_field['type'] === 'file' && get_option('wapf_aiu_show_thumbs','no') === 'yes' ) {

        $thumb_width = intval( get_option( 'wapf_aiu_thumb_size', 100 ) );
        $thumb_width = $thumb_width < 0 ? 100 : $thumb_width;
        $imgs = [];
        $display = '';
        
        foreach( explode( ',', $display_value ) as $link ) {
            
            // Extract link out the <a> element.
            preg_match( '/href="([^"]+)"/', $link, $matches );
            
            if ( isset( $matches[1] ) ) {
                $the_link = $matches[1];
                if ( wapf_aiu_is_image( $the_link ) ) {
                    $imgs[] = sprintf( '<img width="'.$thumb_width.'" src="%s" />', esc_url( $the_link ) );
                }
            }
            
        }
        
        if( ! empty( $imgs ) ) {
            $display ='<div class="wapf-upload-cart-thumb wapf-upload-thumbs">' . join( '', $imgs ) . '</div>';
        }

        return $display_value . $display;

    }


    return $display_value;

}, 10, 2);


if( function_exists('wapf_add_formula_function' ) ) {
// PDF count
// Send PDF count from backend to frontend.
    add_filter( 'wapf/file/ajax_upload_success_file_result', function( $result, $file_result ) {

        if ( get_option( 'wapf_aiu_pdf_count', 'no' ) === 'yes' ) {

            $file = $file_result[ 'uploaded_file_path' ];

            if ( wapf_aiu_is_pdf( $file ) ) {
                $result[ 'page_count' ] = wapf_aiu_get_pdf_page_count( $file );
            }

        }

        return $result;

    }, 11, 2 );

// Create formula function.
    wapf_add_formula_function( 'pdfPages', function( $args, $data ) {

        if ( empty( $args ) ) return 0;

        if ( get_option( 'wapf_aiu_pdf_count', 'no' ) === 'yes' ) {

            foreach ( $data[ 'fields' ] as $cf ) {

                if ( $cf[ 'id' ] !== $args[ 0 ] )
                    continue;

                if ( empty( $cf[ 'values' ] ) || ! is_array( $cf[ 'values' ] ) || empty( $cf[ 'raw' ] ) )
                    return 0;

                $count = 0;
                $files = explode( ',', $cf[ 'raw' ] );
                $wp_upload_dir = wp_upload_dir();
                $path = trailingslashit( $wp_upload_dir[ 'basedir' ] ) . 'wapf' . DIRECTORY_SEPARATOR;

                foreach ( $files as $file ) {
                    if ( wapf_aiu_is_pdf( $path . trim( $file ) ) ) {
                        $count = $count + wapf_aiu_get_pdf_page_count( $path . trim( $file ) );
                    }
                }

                return $count;

            }

        }

        return 0;

    } );

}