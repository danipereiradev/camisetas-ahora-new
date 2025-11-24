<?php

use SW_WAPFLCP\Admin\Updater;
use SW_WAPF_PRO\Includes\Classes\Helper;

$GLOBALS['wapflcp_updater'] = new Updater();
$GLOBALS['wapflcp_notices'] = [];

// Admin basics
add_action( 'wapf/admin/settings_sections',                 'wapflcp_add_settings_section');
add_action( 'wapf/admin/settings_wapf-lcp', 			    'wapflcp_add_backend_settings');
add_action( 'init',									        'wapflcp_updater');
add_action( 'admin_notices',							    'wapflcp_admin_notices');

// Custom font setting
add_action( 'woocommerce_admin_field_wapflcp_font_setting', 'wapflcp_admin_font_setting');
add_action( 'admin_init',                                   'wapflcp_handle_font_upload');
add_action( 'admin_init',                                   'wapflcp_handle_font_delete');

// Admin functionality
add_action( 'admin_enqueue_scripts',				        'wapflcp_register_backend_assets');
add_filter('plugin_action_links_wapf-lcp/wapf-lcp.php',     'wapflcp_add_plugin_action_links');
add_action( 'wapf/admin/product_tab_content_end',	        'wapflcp_add_section', 20);
add_action( 'woocommerce_process_product_meta', 	        'wapflcp_save_product_meta');
add_action( 'wp_ajax_wapflcp_get_variation_info', 	        'wapflcp_get_variation_info');

add_action( 'wapf/admin/after_product_duplication',         'wapflcp_after_product_duplication', 10, 4 );

function wapflcp_add_plugin_action_links($links) {
    $has_license = Updater::get_license_info() != null;

    $links = array_merge( [
        '<a href="' . esc_url( admin_url( '/admin.php?page=wc-settings&section=wapf-lcp&tab=wapf_settings' ) ) . '">' . __( $has_license ? 'Settings' : 'Activate license', 'sw-wapf' ) . '</a>',
    ], $links );

    return $links;
}

function wapflcp_after_product_duplication($duplicate_product, $original, $field_group, $field_ids_map) {

    $post_id = $duplicate_product->get_id();

    $rows = get_post_meta( $post_id, '_wapflcp_rows', true );
    if( empty( $rows ) ) return;

    foreach ( $field_ids_map as $old_id => $new_id ) {

        for( $i = 0; $i < count($rows); $i++ ) {
            
            if( isset( $rows[$i]['type'] ) && $rows[$i]['type'] === 'img' && isset( $rows[$i]['img'] ) &&  $rows[$i]['img'] === $old_id ) {
                $rows[$i]['img'] = $new_id;
            }

            if( isset( $rows[$i]['field'] ) && $rows[$i]['field'] === $old_id ) {
                $rows[$i]['field'] = $new_id;
            }

            if( isset( $rows[$i]['font'] ) && !empty( $rows[$i]['font']['rules'] ) ) {
                for($j = 0; $j < count( $rows[$i]['font']['rules'] ); $j++ ) {
                    if( $rows[$i]['font']['rules'][$j]['id'] === $old_id ) {
                        $rows[$i]['font']['rules'][$j]['id'] = $new_id;
                    }
                }
            }

            if( isset( $rows[$i]['color'] ) && !empty( $rows[$i]['color']['rules'] ) ) {
                for($j = 0; $j < count( $rows[$i]['color']['rules'] ); $j++ ) {
                    if( $rows[$i]['color']['rules'][$j]['id'] === $old_id ) {
                        $rows[$i]['color']['rules'][$j]['id'] = $new_id;
                    }
                }
            }

            if( isset( $rows[$i]['align'] ) && is_array( $rows[$i]['align'] ) && !empty( $rows[$i]['align']['rules'] ) ) {
                for($j = 0; $j < count( $rows[$i]['align']['rules'] ); $j++ ) {
                    if( $rows[$i]['align']['rules'][$j]['id'] === $old_id ) {
                        $rows[$i]['align']['rules'][$j]['id'] = $new_id;
                    }
                }
            }

            if( isset( $rows[$i]['size'] ) && is_array( $rows[$i]['size'] ) && !empty( $rows[$i]['size']['rules'] ) ) {
                for($j = 0; $j < count( $rows[$i]['size']['rules'] ); $j++ ) {
                    if( $rows[$i]['size']['rules'][$j]['id'] === $old_id ) {
                        $rows[$i]['size']['rules'][$j]['id'] = $new_id;
                    }
                }
            }

            if( isset( $rows[$i]['sizeMobile'] ) && is_array( $rows[$i]['sizeMobile'] ) && !empty( $rows[$i]['sizeMobile']['rules'] ) ) {
                for($j = 0; $j < count( $rows[$i]['sizeMobile']['rules'] ); $j++ ) {
                    if( $rows[$i]['sizeMobile']['rules'][$j]['id'] === $old_id ) {
                        $rows[$i]['sizeMobile']['rules'][$j]['id'] = $new_id;
                    }
                }
            }

            if( isset( $rows[$i]['sizeTablet'] ) && is_array( $rows[$i]['sizeTablet'] ) && !empty( $rows[$i]['sizeTablet']['rules'] ) ) {
                for($j = 0; $j < count( $rows[$i]['sizeTablet']['rules'] ); $j++ ) {
                    if( $rows[$i]['sizeTablet']['rules'][$j]['id'] === $old_id ) {
                        $rows[$i]['sizeTablet']['rules'][$j]['id'] = $new_id;
                    }
                }
            }

            if( isset( $rows[$i]['sizeXs'] ) && is_array( $rows[$i]['sizeXs'] ) && !empty( $rows[$i]['sizeXs']['rules'] ) ) {
                for($j = 0; $j < count( $rows[$i]['sizeXs']['rules'] ); $j++ ) {
                    if( $rows[$i]['sizeXs']['rules'][$j]['id'] === $old_id ) {
                        $rows[$i]['sizeXs']['rules'][$j]['id'] = $new_id;
                    }
                }
            }

            if( isset( $rows[$i]['lineheight'] ) && is_array( $rows[$i]['lineheight'] ) && !empty( $rows[$i]['lineheight']['rules'] ) ) {
                for($j = 0; $j < count( $rows[$i]['lineheight']['rules'] ); $j++ ) {
                    if( $rows[$i]['lineheight']['rules'][$j]['id'] === $old_id ) {
                        $rows[$i]['lineheight']['rules'][$j]['id'] = $new_id;
                    }
                }
            }

        }
    }

    update_post_meta( $post_id, '_wapflcp_rows', $rows);

}

function wapflcp_is_screen( $id = '' ) {

    if(!function_exists('get_current_screen'))
        return false;

    $current_screen = get_current_screen();
    if( !$current_screen )
        return false;

    if($id !== $current_screen->id)
        return false;

    return true;
}

function wapflcp_add_settings_section($sections) {

    $sections['wapf-lcp'] = __('Live Content Preview','wapf-lcp');
    return $sections;

}

function wapflcp_add_backend_settings($settings) {

    $license_info = Updater::get_license_info();
    $has_license = $license_info != null;

    $settings[] = [
        'name'  => __( 'License key', 'wapf-lcp' ),
        'type'	=> 'wapf_license_key',
        'license_id' => 'wapflcp_license',
        'has_license' => $has_license,
    ];

    if($has_license) {

        $settings[] = [
            'name' => __( 'Live Content Preview settings', 'wapf-lcp' ),
            'type' => 'title',
        ];

        $settings[] = [
            'name'      => __( 'Auto scroll', 'wapf-lcp' ),
            'id'        => 'wapflcp_scroll',
            'type'      => 'checkbox',
            'default'   => 'no',
            'desc'      => __( "Auto scroll to live preview", 'wapf-lcp' ),
            'desc_tip'  => __( 'If your product gallery contains multiple images, auto scroll to the slide containing the live preview when the user makes a change.', 'wapf-lcp' ),
        ];

        $settings[] = [
            'name'  => __( 'Custom fonts', 'wapf-lcp' ),
            'type'	=> 'wapflcp_font_setting',
            'id'	=> 'wapflcp_font_setting',
        ];

    }

    $settings[] = [
        'type' => 'sectionend',
    ];

    return $settings;

}

function wapflcp_admin_font_setting($data) {

    $all_fonts = get_option( 'wapf_lcp_fonts', [] );

    ?>
    <table class="form-table">
        <tr valign="top">
            <th scope="row" class="titledesc">
                <label><?php esc_html_e($data['name']) ?></label>
            </th>
            <td class="forminp">
                <div style="padding-bottom:15px;">
                    <?php _e( 'Add custom fonts by uploading woff or woff2 files here. Fonts will only be loaded when necessary so it does not slow down your site.', 'wapf-lcp' ) ?>
                </div>

                <div>
                    <div style="display: flex; align-items: center;">
                        <label style="padding-right: 10px" for="wapflcp_font_name"><?php _e('Font name','wapf-lcp')?>: </label>
                        <input style="width: 120px" type="text" id="wapflcp_font_name" name="wapflcp_font_name" />
                    </div>
                    <div style="padding-top:10px;display: flex; align-items: center;;">
                        <label style="padding-right: 10px" for="wapflcp_font_file"><?php _e('Font file','wapf-lcp')?>: </label>
                        <input type="file" id="wapflcp_font_file" name="wapflcp_font_file" />
                    </div>
                </div>

                <?php if( ! empty( $all_fonts ) ) { ?>
                    <div style="padding-top:15px;margin-top:15px;border-top:1px dotted #000;">
                        <strong><?php _e('Uploaded fonts:', 'wapf-lcp') ?></strong>
                        <ol>
                            <?php foreach($all_fonts as $font ) { ?>
                                <li>
                                    <span style="display: flex;align-items: center">
                                        <span><a href="<?php echo esc_url($font['url']) ?>"><?php esc_html_e($font['name']) ?></a></span>
                                        <button type="submit" name="wapflcp_font_delete" value="<?php echo esc_attr($font['name']) ?>" class="button button-small button-secondary" style="margin-left:10px"><span class="dashicons dashicons-trash"></span></button>
                                    </span>
                                </li>
                            <?php } ?>
                        </ol>
                    </div>
                <?php } ?>

            </td>
        </tr>
    </table>
    <?php
}

function wapflcp_handle_font_upload() {

    if( ! isset( $_GET['section'] ) || $_GET['section'] !== 'wapf-lcp' ) return;

    if( ! isset( $_POST['save'] ) || ! isset( $_FILES['wapflcp_font_file'] ) ) return;

    if( ! isset( $_POST['_wpnonce'] ) || ! wp_verify_nonce( $_POST['_wpnonce'], 'woocommerce-settings' ) ) return;

    if( ! current_user_can( 'manage_options' ) ) return;

    if( isset($_FILES['wapflcp_font_file']) && is_uploaded_file( $_FILES['wapflcp_font_file']['tmp_name'] ) ) {

        if( empty($_POST['wapflcp_font_name']) ) {

            $GLOBALS['wapflcp_notices'][] = [
                'class' => 'error',
                'message' => __('Please enter a font name before uploading a font file.', 'wapf-lcp')
            ];
            return;

        }

        $skip_file_checks = apply_filters( 'lcp/skip_file_checks', false );

        if( ! $skip_file_checks ) {

            $allowed_file_types = [
                'woff' => 'application/octet-stream',
                'woff2' => 'application/octet-stream'
            ];

            $info = wp_check_filetype_and_ext($_FILES['wapflcp_font_file']['tmp_name'], $_FILES['wapflcp_font_file']['name'], $allowed_file_types);

            if (!$info['ext'] && !$info['type']) {

                $allowed_file_types = [
                    'woff' => 'font/woff',
                    'woff2' => 'font/woff2'
                ];

                add_filter('mime_types', function ($types) {
                    $types['woff'] = 'font/woff';
                    $types['woff2'] = 'font/woff2';
                    return $types;
                });

                $info = wp_check_filetype_and_ext($_FILES['wapflcp_font_file']['tmp_name'], $_FILES['wapflcp_font_file']['name'], $allowed_file_types);
            }

            if (!$info['ext'] || !$info['type']) {

                // Check with application/octet-stream
                $allowed_file_types = [
                    'woff' => 'application/octet-stream',
                    'woff2' => 'application/octet-stream'
                ];

                add_filter('mime_types', function ($types) {
                    $types['woff'] = 'application/octet-stream';
                    $types['woff2'] = 'application/octet-stream';
                    return $types;
                });

                $info = wp_check_filetype_and_ext($_FILES['wapflcp_font_file']['tmp_name'], $_FILES['wapflcp_font_file']['name'], $allowed_file_types);

                if (!$info['ext'] || !$info['type']) {
                    $GLOBALS['wapflcp_notices'][] = [
                        'class' => 'error',
                        'message' => __('Wrong file type uploaded. Only these file types are allowed: woff, woff2.', 'wapf-lcp')
                    ];

                    return;

                }

            }

        } else {
            $old = defined( 'ALLOW_UNFILTERED_UPLOADS' ) && ALLOW_UNFILTERED_UPLOADS;
            define( 'ALLOW_UNFILTERED_UPLOADS', true );
        }

        if (!function_exists('wp_handle_upload')) require_once(ABSPATH . 'wp-admin/includes/file.php');
        include_once(ABSPATH . 'wp-admin/includes/media.php');

        add_filter( 'upload_dir', 'wapflcp_set_upload_dir' );

        $upload = wp_handle_upload(
            $_FILES['wapflcp_font_file'],
            [
                'test_form' => false,
                'mimes'		=> $allowed_file_types
            ]
        );

        remove_filter( 'upload_dir', 'wapflcp_set_upload_dir' );

        if( isset($upload['error']) ) {

            $GLOBALS['wapflcp_notices'][] = [
                'class' => 'error',
                'message' => $upload['error']
            ];

            return;

        }

        $all_fonts = get_option( 'wapf_lcp_fonts', [] );

        $unwanted_array = array('Š'=>'S', 'š'=>'s', 'Ž'=>'Z', 'ž'=>'z',
            'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'A', 'Å'=>'A', 'Æ'=>'A', 'Ç'=>'C', 'È'=>'E', 'É'=>'E',
            'Ê'=>'E', 'Ë'=>'E', 'Ì'=>'I', 'Í'=>'I', 'Î'=>'I', 'Ï'=>'I', 'Ñ'=>'N', 'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O', 'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O', 'Ù'=>'U',
            'Ú'=>'U', 'Û'=>'U', 'Ü'=>'U', 'Ý'=>'Y', 'Þ'=>'B', 'ß'=>'Ss', 'à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a', 'å'=>'a', 'æ'=>'a', 'ç'=>'c',
            'è'=>'e', 'é'=>'e', 'ê'=>'e', 'ë'=>'e', 'ì'=>'i', 'í'=>'i', 'î'=>'i', 'ï'=>'i', 'ð'=>'o', 'ñ'=>'n', 'ò'=>'o', 'ó'=>'o', 'ô'=>'o', 'õ'=>'o',
            'ö'=>'o', 'ø'=>'o', 'ù'=>'u', 'ú'=>'u', 'û'=>'u', 'ý'=>'y', 'þ'=>'b', 'ÿ'=>'y',
            'Ğ'=>'G', 'İ'=>'I', 'Ş'=>'S', 'ğ'=>'g', 'ı'=>'i', 'ş'=>'s', 'ü'=>'u',
            'ă'=>'a', 'Ă'=>'A', 'ș'=>'s', 'Ș'=>'S', 'ț'=>'t', 'Ț'=>'T',
            'ű'=>'u', 'Ű'=>'U', 'ő'=>'o', 'Ő'=>'O', 'ü'=>'u'
        );

        $font_name = str_replace( ' ', '-', strtr( trim(sanitize_text_field($_POST['wapflcp_font_name'])), $unwanted_array ));

        $font_url = $upload['url'];
        if( is_ssl() && strpos( $font_url, 'http://') !== false ) { // Somne plugins mess up upload and return a http url when it should be https. We fix that here.
            $font_url = str_replace('http://', 'https://', $font_url );
        }

        $all_fonts[] = [
            'url'   => $font_url,
            'path'  => $upload['file'],
            'name'  => $font_name
        ];

        update_option( 'wapf_lcp_fonts', $all_fonts, false );

        $GLOBALS['wapflcp_notices'][] = [
            'class' => 'success',
            'message' => __('Font file uploaded successfully.', 'wapf-lcp')
        ];

        if( $skip_file_checks ) {
            define( 'ALLOW_UNFILTERED_UPLOADS', $old || false );
        }

    }

}

function wapflcp_handle_font_delete() {

    if( ! isset( $_GET['section'] ) || $_GET['section'] !== 'wapf-lcp' ) return;

    if( ! isset( $_POST['wapflcp_font_delete'] ) ) return;

    if( ! isset( $_POST['_wpnonce'] ) || ! wp_verify_nonce( $_POST['_wpnonce'], 'woocommerce-settings' ) ) return;

    if( ! current_user_can( 'manage_options' ) ) return;

    $to_delete = sanitize_text_field(trim($_POST['wapflcp_font_delete']));

    $all_fonts = get_option( 'wapf_lcp_fonts', [] );
    $new_fonts = [];

    foreach($all_fonts as $font) {
        if($font['name'] !== $to_delete) {
            $new_fonts[] = $font;
        } else {
            wp_delete_file( $font['path'] );
        }
    }

    update_option( 'wapf_lcp_fonts', $new_fonts, false );

}

function wapflcp_set_upload_dir($upload) {

    $upload['subdir'] = '/wapf-lcp';

    $upload['path']         = $upload['basedir'] . $upload['subdir'];
    $upload['url']          = $upload['baseurl'] . $upload['subdir'];

    if(!file_exists($upload['path'])) {
        wp_mkdir_p($upload['path']);
        @file_put_contents($upload['path'] . '/index.php', '<?php' . PHP_EOL . '// Silence is golden.');
        @file_put_contents($upload['path'] . '/.htaccess', 'Options -Indexes');
    }

    return $upload;
}

function wapflcp_updater() {

    $wapflcp_updater = $GLOBALS['wapflcp_updater'];

    $nonce = isset($_POST['_wapflcp_license_nonce']) ? $_POST['_wapflcp_license_nonce'] : false;
    if(!$nonce) return;

    if(isset($_REQUEST['wapflcp_license_activate']) && wp_verify_nonce($nonce,'wapflcp_license_activate')  ) {
        $activated = $wapflcp_updater->activate_license();

        $notice = $activated === true ? __('License activated.','wapf-lcp') : $activated;
        $GLOBALS['wapflcp_notices'][] = [
            'class' => $activated === true ? 'success' : 'error',
            'message' => __($notice, 'wapf-lcp')
        ];
    }

    if(isset($_REQUEST['wapflcp_license_activate']) && wp_verify_nonce($nonce,'wapflcp_license_deactivate')){
        $deactivated = $wapflcp_updater->deactivate_license();
        $GLOBALS['wapflcp_notices'][] = [
            'class' => 'success',
            'message' => __('License deactivated','wapf-lcp')
        ];
    }

}

function wapflcp_admin_notices() {

    foreach( $GLOBALS['wapflcp_notices'] as $notice ) {
        echo '<div class="notice is-dismissible notice-' . esc_html($notice['class']) . '"><p>' . esc_html($notice['message']) . '</p></div>';
    }

}

function wapflcp_register_backend_assets() {

    if(wapflcp_is_screen('product')) {

        $url =  trailingslashit(plugin_dir_url(WAPFLCP_STARTFILE));
        $version = WAPFLCP_VERSION;

        wp_enqueue_style('wapflcp-admin', $url . 'assets/css/admin.min.css', [], $version);
        wp_enqueue_script('wapflcp-admin', $url . 'assets/js/admin.min.js', ['jquery'], $version, true);
        wp_localize_script('wapflcp-admin', 'wapflcp_config', [
            'ajaxUrl' => admin_url('admin-ajax.php')
        ]);
    }

}

function wapflcp_save_product_meta($post_id) {

    $product = wc_get_product($post_id);
    if(!$product) return;

    $rows = [];

    if(isset($_REQUEST['wapflcp_rows'])) {
        $raw = json_decode(wp_unslash($_POST['wapflcp_rows']), true);

        foreach($raw as $row) {

            if( ! isset($row['img']) || ! isset($row['field']) ) continue;

            $type = isset($row['type']) && $row['type'] === 'img' ? 'img' : 'gallery';

            $r = [
                'type'		    => $type,
                'img'		    => $type === 'gallery' ? intval($row['img']) : sanitize_text_field($row['img']),
                'field' 	    => sanitize_text_field($row['field']),
                'x'			    => Helper::normalize_string_decimal(''.$row['x']),
                'y'			    => Helper::normalize_string_decimal(''.$row['y']),
                'w'			    => Helper::normalize_string_decimal(''.$row['w']),
                'h'			    =>  Helper::normalize_string_decimal(''.$row['h']),
                'rotate'	    => isset($row['rotate']) ? intval($row['rotate']) : 0,
                'size'		    => [
                    'default'	=> isset($row['size']) && !empty($row['size']['default']) ? intval($row['size']['default']) : 16,
                    'rules'		=> []
                ],
                'sizeMobile'	=> [
                    'default'	=> isset($row['sizeMobile']) && !empty($row['sizeMobile']['default']) ? intval($row['sizeMobile']['default']) : 16,
                    'rules'		=> []
                ],
                'sizeTablet'	=> [
                    'default'	=> isset($row['sizeTablet']) && !empty($row['sizeTablet']['default']) ? intval($row['sizeTablet']['default']) : 16,
                    'rules'		=> []
                ],
                'sizeXs'	=> [
                    'default'	=> isset($row['sizeXs']) && !empty($row['sizeXs']['default']) ? intval($row['sizeXs']['default']) : 16,
                    'rules'		=> []
                ],
                'color'		    => [
                    'default'	=> isset($row['color']) && !empty($row['color']['default']) ? sanitize_text_field($row['color']['default']) : '#000',
                    'rules'		=> []
                ],
                'weight'	    => isset($row['weight']) ? sanitize_text_field($row['weight']) : 'normal',
                'caps'		    => isset($row['caps']) && $row['caps'],
                'italic'	    => isset($row['italic']) && $row['italic'],
                'align'		    => [
                    'default'	=> isset($row['align']) && !empty($row['align']['default']) ? sanitize_text_field($row['align']['default']) : 'left',
                    'rules'		=> []
                ],
                'font'		    => [
                    'default'	=> isset($row['font']) && !empty($row['font']['default']) ? sanitize_text_field($row['font']['default']) : 'Arial',
                    'rules'		=> []
                ],
                'textMode'      => empty($row['textMode']) ? 'default' : sanitize_text_field( $row['textMode'] ),
                'valign'        => empty( $row['valign'] ) ? 'top' : $row['valign'],
                'imgFit'        => empty( $row['imgFit'] ) ? '' : $row['imgFit'],
                'shape'         => isset($row['shape']) && $row['shape'] === 'oval' ? 'oval' : '',
                'lineheight'	=> [
                    'default'	=> isset($row['lineheight']) && !empty($row['lineheight']['default']) ? Helper::normalize_string_decimal( '' . $row['lineheight']['default'] ) : '',
                    'unit'      => isset( $row['lineheight'] ) && isset( $row['lineheight']['unit'] ) && $row['lineheight']['unit'] === 'ul' ? 'ul' : 'px',
                    'rules'		=> []
                ],
            ];

            $dynamics = ['color','font','align','size', 'sizeMobile', 'sizeTablet', 'sizeXs', 'lineheight'];

            foreach($dynamics as $key) {
                if( isset( $row[$key] ) && ! empty( $row[$key]['rules'] ) ) {
                    foreach($row[$key]['rules'] as $rule) {
                        $value = isset($rule['value']) ? trim(sanitize_text_field(''.$rule['value'])) : '';
                        if( !empty( $value ) && $key === 'lineheight' ) $value = Helper::normalize_string_decimal( $value ); 
                        $r[$key]['rules'][] = [
                            'type' => sanitize_text_field($rule['type']),
                            'value' => $value,
                            'meta' => isset($rule['meta']) ? sanitize_text_field($rule['meta']) : '',
                            'id' => sanitize_text_field($rule['id'])
                        ];
                    }
                }
            }

            $rows[] = $r;
        }
    }

    if(empty($rows))
        delete_post_meta($post_id, '_wapflcp_rows');
    else
        update_post_meta( $post_id, '_wapflcp_rows', $rows);

}

function wapflcp_get_variation_info() {

    if(!isset($_REQUEST['id']))  wp_send_json_error();
    $product = wc_get_product(intval($_REQUEST['id']));
    if(!$product) wp_send_json_error();

    $attributes = $product->get_variation_attributes();
    $atts = [];
    foreach($attributes as $key => $values) {
        foreach($values as $v) {
            $atts[] = ['id' => $key . '|' . $v, 'label' => str_replace('pa_','',$key) . ': ' . $v ];
        }
    }

    $variations = $product->get_available_variations();
    $vars = [];
    foreach($variations as $variation) {
        $vars[] = ['id' => $variation['variation_id'], 'label' => '#'.$variation['variation_id'] ];
    }

    wp_send_json_success([
        'attributes' => $atts,
        'variations' => $vars
    ]);

}

function wapflcp_add_section() {

    global $post;

    $rows = get_post_meta($post->ID, '_wapflcp_rows', true);
    $rows = wapflcp_prep_rows( empty($rows) ? [] : $rows );

    ?>
    <div class="wapf_modal_overlay wapf--customizer-help">
        <div class="wapf_modal">
            <a class="wapf_close" href="#" onclick="event.preventDefault();jQuery('.wapf--customizer-help').hide();">×</a>
            <h3><?php _e('Help with Live Content Preview','wapf-lcp')?></h3>
            <div style="line-height: 1.5;">
                <p>
                    <?php _e("Do you want customers to see a live preview of their selection? Example: you're selling custom posters and customers can add custom text to their poster. You could show that text live on the product image so your customer sees a live example of what they will purchase.",'wapf-lcp');?>
                </p>
                <p>
                    <?php _e("Check out <a href=\"https://www.studiowombat.com/knowledge-base/everything-you-need-to-know-about-the-live-content-preview-add-on/?ref=wapf_admin\" target=\"_blank\">our step-by-step tutorial</a> on how to use the Live Content Preview addon.", 'wapf-lcp'); ?>
                </p>
            </div>
        </div>
    </div>
    <h4 class="wapf-product-admin-title"><a class="modal_help_icon" style="padding:5px;" href="#" onclick="event.preventDefault();jQuery('.wapf--customizer-help').show();"><i class="dashicons-before dashicons-editor-help"></i></a><?php _e('Live Content Preview','wapf-lcp');?> &mdash; <span style="opacity:.5;"><?php _e('Display content on gallery images','wapf-lcp');?></span></h4>

    <div rv-controller="CICtrl" data-rows="<?php echo wapflcp_to_html_attribute($rows);?>">
        <input type="hidden" name="wapflcp_rows" rv-value="json" />

        <div class="wapf_modal_overlay wapfc-customizer">
            <div class="wapf_modal modal--xlarge">
                <a class="wapf_close" href="#" onclick="event.preventDefault();jQuery('.wapfc-customizer').hide()">×</a>
                <div rv-show="step | eq 1">
                    <h3><?php _e('Step 1: Select image','wapf-lcp');?></h3>
                    <p style="padding:0;">
                        <?php _e('Select an image to display content on. This can be a product gallery image or an image field added by our plugin.','wapf-lcp'); ?>
                    </p>
                    <div><strong><?php _e('Select gallery image','wapf-lcp');?></strong></div>
                    <div rv-if="galleryImages | isNotEmpty" class="wapfc-select-img">
                        <div rv-on-click="setSelectedGalleryImage" rv-each-img="galleryImages" rv-class-active="activeCustomization.img|eq img.id">
                            <img rv-src="img.url" />
                        </div>
                    </div>
                    <div rv-if="galleryImages | empty" style="padding-bottom:20px;padding-top:5px">
                        <i> <?php _e('You currently have no gallery images','wapf-lcp');?> </i>
                    </div>
                    <div rv-if="imageFieldIds | isNotEmpty">
                        <div style="padding-bottom:10px;"><strong><?php _e('Or select an image field:','wapf-lcp');?></strong></div>
                        <select class="select-wapfc-img" rv-on-change="setSelectedGalleryImage" style="float:none;">
                            <option value=""><?php _e('Select a field','wapf-lcp');?></option>
                            <option rv-each-img="imageFieldIds" rv-value="$index">{img.label}</option>
                        </select>
                    </div>
                    <div style="padding-top:15px;">
                        <button rv-disabled="activeCustomization.img|isEmpty" class="button button-primary button-large" rv-on-click="nextStep">Next step</button>
                    </div>
                </div>
                <div rv-show="step | eq 2">
                    <h3><?php _e('Step 2: select field','wapf-lcp');?></h3>
                    <p style="padding:0">
                        <?php _e('Select the field which content should appear on the image.','wapf-lcp'); ?>
                    </p>
                    <div style="overflow:hidden">
                        <select rv-value="activeCustomization.field" rv-on-change="onChange">
                            <option value=""><?php _e('Select a field','wapf-lcp');?></option>
                            <option rv-each-choice="fieldIds" rv-value="choice.id" rv-text="choice.label"></option>
                        </select>
                    </div>
                    <div style="padding-top:15px">
                        <button rv-disabled="activeCustomization.field|isEmpty" class="button button-primary button-large" rv-on-click="nextStep">Next step</button>
                    </div>
                </div>
                <div rv-show="step | eq 3">
                    <h3><?php _e('Step 3: customize','wapf-lcp');?></h3>
                    <p style="padding: 0">
                        <?php _e('Draw the bounding box where this field should appear on the image.','wapf-lcp'); ?>
                    </p>
                    <div class="wapfc-customizer-wrapper">
                        <div class="wapfc-customizer-loader"></div>

                        <div class="wapf-customizer-left">
                            <div class="wapfc-canvas-wrapper">
                                <canvas rv-canvas="activeCustomization" class="wapfc-canvas"></canvas>
                            </div>
                            <div class="wapfc-customizer-row">
                                <div class="wapfc-customizer-inner">
                                    <div class="wapf-input-with-prepend">
                                        <div class="wapf-input-prepend" style="width: 46%"><?php _e('Rotate','wapf-lcp');?></div>
                                        <input type="number" min="-360" max="360" rv-value="activeCustomization.rotate" rv-on-change="redraw" />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- for text fields -->
                        <div rv-if="activeCustomization.field | isFileField 'not'" class="wapfc-customizer-options">

                            <div class="wapfc-customizer-row">
                                <div class="wapfc-customizer-row-label">
                                    <span><?php _e('Font details','wapf-lcp') ?></span>
                                </div>
                                <div class="wapfc-customizer-inner">
                                    <div style="width: 48%">
                                        <div class="wapf-input-with-prepend">
                                            <div class="wapf-input-prepend"><?php _e('Font', 'wapf-lcp') ?></div>
                                            <input type="text" rv-value="activeCustomization.font.default" rv-on-change="onChange"/>
                                        </div>
                                        <div class="link-below">
                                            <?php _e('or','wapf-lcp');?>&nbsp;<span><a rv-on-click="openDynamicValue" data-for="font" href="#"><?php _e('set dynamic value','wapf-lcp');?> <span rv-if="activeCustomization.font.rules | isNotEmpty">({activeCustomization.font.rules.length})</span></a></span>
                                        </div>
                                    </div>
                                    <div style="width: 48%">
                                        <div class="wapf-input-with-prepend">
                                            <div class="wapf-input-prepend"><?php _e('Color', 'wapf-lcp') ?></div>
                                            <input type="text" rv-value="activeCustomization.color.default" rv-on-change="onChange" placeholder="<?php _e('color value','wapf-lcp');?>"/>
                                        </div>
                                        <div class="link-below">
                                            <?php _e('or','wapf-lcp');?>&nbsp;<span><a rv-on-click="openDynamicValue" data-for="color" href="#"><?php _e('set dynamic value','wapf-lcp');?> <span rv-if="activeCustomization.color.rules | isNotEmpty">({activeCustomization.color.rules.length})</span></a></span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="wapfc-customizer-row">
                                <div class="wapfc-customizer-row-label">
                                    <span><?php _e('Font sizes','wapf-lcp') ?></span>
                                </div>
                                <div class="wapfc-customizer-inner">
                                    <div class="wapf-input-with-prepend">
                                        <div title="<?php _e( 'Desktop & large screens', 'wapf-lcp' ) ?>" class="wapf-input-prepend">
                                            <svg style="width:14px;height:14px" viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg"><path d="M28 4H4a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h8v4H8v2h16v-2h-4v-4h8a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2ZM18 28h-4v-4h4Zm10-6H4V6h24Z"/></svg>
                                        </div>
                                        <input style="border-right:0;border-top-right-radius: 0;border-bottom-right-radius: 0" type="number" min="1" max="4000" rv-value="activeCustomization.size.default"  rv-on-change="onChange"/>
                                    </div>
                                    <div class="wapf-input-with-prepend">
                                        <div title="<?php _e( 'Tablet screens', 'wapf-lcp' ) ?>" style="border-top-left-radius: 0;border-bottom-left-radius: 0" class="wapf-input-prepend">
                                            <svg style="width:14px;height:14px" viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg"><path d="M19 24v2h-6v-2z"/><path d="M25 30H7a2.002 2.002 0 0 1-2-2V4a2.002 2.002 0 0 1 2-2h18a2.002 2.002 0 0 1 2 2v24a2.003 2.003 0 0 1-2 2ZM7 4v24h18V4Z"/></svg>
                                        </div>
                                        <input style="border-right:0;border-top-right-radius: 0;border-bottom-right-radius: 0" type="number" min="1" max="4000" rv-value="activeCustomization.sizeTablet.default"  rv-on-change="onChange"/>
                                    </div>
                                    <div class="wapf-input-with-prepend">
                                        <div title="<?php _e( 'Mobile (landscape)', 'wapf-lcp' ) ?>" style="border-top-left-radius: 0;border-bottom-left-radius: 0" class="wapf-input-prepend">
                                            <svg style="width:14px;height:14px" viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg"><path d="M3 10v12a2.002 2.002 0 0 0 2 2h22a2.002 2.002 0 0 0 2-2V10a2.003 2.003 0 0 0-2-2H5a2.002 2.002 0 0 0-2 2Zm2 0h2v12H5Zm22 12H9V10h18Z"/></svg>
                                        </div>
                                        <input style="border-right:0;border-top-right-radius: 0;border-bottom-right-radius: 0" type="number" min="1" max="4000" rv-value="activeCustomization.sizeMobile.default"  rv-on-change="onChange" />
                                    </div>
                                    <div class="wapf-input-with-prepend">
                                        <div title="<?php _e( 'Mobile (portrait)', 'wapf-lcp' ) ?>" style="border-top-left-radius: 0;border-bottom-left-radius: 0" class="wapf-input-prepend">
                                            <svg style="width:14px;height:14px;transform:rotate(180deg)" viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg"><path d="M22 4H10a2.002 2.002 0 0 0-2 2v22a2.002 2.002 0 0 0 2 2h12a2.003 2.003 0 0 0 2-2V6a2.002 2.002 0 0 0-2-2Zm0 2v2H10V6ZM10 28V10h12v18Z"/></svg>
                                        </div>
                                        <input type="number" min="1" max="4000" rv-value="activeCustomization.sizeXs.default"  rv-on-change="onChange" />
                                    </div>
                                </div>
                                <div class="link-below">
                                    <?php _e('or','wapf-lcp');?>&nbsp;<span><a rv-on-click="openDynamicValue" data-for="size" href="#"><?php _e('set dynamic value','wapf-lcp');?> <span rv-if="activeCustomization.size.rules | isNotEmpty">({activeCustomization.size.rules.length})</span></a></span>
                                </div>
                            </div>

                            <div class="wapfc-customizer-row">
                                <div class="wapfc-customizer-row-label">
                                    <span><?php _e('Alignment','wapf-lcp') ?></span>
                                </div>
                                <div class="wapfc-customizer-inner">
                                    <div style="width: 48%">
                                        <div class="wapf-input-with-prepend">
                                            <div class="wapf-input-prepend" style="width: 46%"><?php _e('Horizontal', 'wapf-lcp') ?></div>
                                            <select rv-value="activeCustomization.align.default" rv-on-change="onChange">
                                                <option value="left"><?php _e('Left','wapf-lcp');?></option>
                                                <option value="center"><?php _e('Center','wapf-lcp');?></option>
                                                <option value="right"><?php _e('Right','wapf-lcp');?></option>
                                            </select>
                                        </div>
                                        <div class="link-below">
                                            <?php _e('or','wapf-lcp');?>&nbsp;<span><a rv-on-click="openDynamicValue" data-for="align" href="#"><?php _e('set dynamic value','wapf-lcp');?> <span rv-if="activeCustomization.align.rules | isNotEmpty">({activeCustomization.align.rules.length})</span></a></span>
                                        </div>
                                    </div>
                                    <div style="width: 48%">
                                    <div class="wapf-input-with-prepend">
                                        <div class="wapf-input-prepend" style="width: 46%"><?php _e('Vertical', 'wapf-lcp') ?></div>
                                        <select rv-value="activeCustomization.valign" rv-on-change="onChange">
                                            <option value="top"><?php _e('Top','wapf-lcp');?></option>
                                            <option value="center"><?php _e('Center','wapf-lcp');?></option>
                                            <option value="bottom"><?php _e('Bottom','wapf-lcp');?></option>
                                        </select>
                                    </div>
                                    </div>

                                </div>
                            </div>
                            <div class="wapfc-customizer-row">
                                <div class="wapfc-customizer-row-label">
                                    <span><?php _e('Characteristics','wapf-lcp') ?></span>
                                </div>
                                <div class="wapfc-customizer-inner">
                                    <div class="wapf-input-with-prepend">
                                        <div class="wapf-input-prepend" style="width: 46%"><?php _e('Weight', 'wapf-lcp') ?></div>
                                        <select rv-value="activeCustomization.weight" rv-on-change="onChange">
                                            <option value="normal"><?php _e('Normal','wapf-lcp');?></option>
                                            <option value="bold"><?php _e('Bold','wapf-lcp');?></option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="wapfc-customizer-row">
                                <div class="wapfc-customizer-inner">
                                    <div>
                                        <input type="checkbox" rv-checked="activeCustomization.italic" id="chk_wapfc_italic" rv-on-change="onChange"/>
                                        <label style="margin:0;" for="chk_wapfc_italic"><?php _e('Italic','wapf-lcp');?></label>
                                    </div>
                                    <div style="padding-left: 10px">
                                        <input type="checkbox" rv-checked="activeCustomization.caps" id="chk_wapfc_caps" rv-on-change="onChange"/>
                                        <label style="margin:0;" for="chk_wapfc_caps"><?php _e('Capitalize','wapf-lcp');?></label>
                                    </div>
                                </div>
                            </div>

                            <div class="wapfc-customizer-row" style="margin-top: 15px">
                                <div class="wapfc-customizer-inner">
                                    <a href="#" class="button" onclick="javascript:event.preventDefault();jQuery('.lcp-advanced').show();"><?php _e('Show advanced settings','sw-wapf'); ?></a>
                                </div>
                            </div>
                        </div>

                        <!-- for images -->
                        <div rv-if="activeCustomization.field | isFileField" class="wapfc-customizer-options">
                            <div class="wapfc-customizer-row">
                                <div class="wapfc-customizer-inner" style="flex-flow: column">
                                    <div class="wapf-input-with-prepend">
                                        <div class="wapf-input-prepend" style="width: 46%"><?php _e('Fitting mode', 'wapf-lcp') ?></div>
                                        <select rv-value="activeCustomization.imgFit" rv-on-change="onChange">
                                            <option value=""><?php _e('Standard fill','wapf-lcp');?></option>
                                            <option value="scale"><?php _e('Scale to fit','wapf-lcp');?></option>
                                            <option value="cover"><?php _e('Cover','wapf-lcp');?></option>
                                        </select>
                                    </div>
                                    <div class="link-below" style="display: inline-block;margin-top:-15px;width:100px;">
                                        <?php SW_WAPF_PRO\Includes\Classes\Html::help_modal( [ 'button' => __('What is this?', 'wapf-lcp'), 'title' => __('Image fitting mode', 'wapf-lcp'), 'content' => __( 'This setting determines how the image should be displayed within the bounds. <ul><li><strong>Standard fill: </strong>The image will be filled within the bounds. If the uploaded image is not the same aspect ratio as the bounds, the image will be stretched or squeezed to fit within the bounds.</li><li><strong>Scale to fit: </strong>If the image is larger than the bounds, the image will be resized to fit within the bounds while maintaining aspect ratio. If the image is smaller than the bounds, it will be centered within the bounds (and whitespace around).</li><li><strong>Cover: </strong>The image is sized to maintain its aspect ratio while filling the entire bounding box. If the image\'s aspect ratio does not match the aspect ratio of its box, then the image will be clipped to fit (no whitespace around).</li></ul>','wapf-lcp') ] ) ?>
                                    </div>
                                </div>
                            </div>
                            <div class="wapfc-customizer-row">
                                <div class="wapfc-customizer-inner" style="flex-flow: column">
                                    <div class="wapf-input-with-prepend">
                                        <div class="wapf-input-prepend" style="width: 46%"><?php _e('Shape', 'wapf-lcp') ?></div>
                                        <select rv-value="activeCustomization.shape" rv-on-change="onShapeChange">
                                            <option value=""><?php _e('Rectangle','wapf-lcp');?></option>
                                            <option value="oval"><?php _e('Oval','wapf-lcp');?></option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div style="padding-top:15px;display: flex;align-items: center">
                        <a class="button button-primary button-large" href="#" onclick="event.preventDefault();jQuery('.wapfc-customizer').hide();"><?php _e('Done','wapf-lcp');?></a>
                        <span class="wapfc-customizer-info">
                            <ul class="customizer-bounding-list">
                                <li>
                                    <div>
                                        X: &nbsp;<input type="number" rv-on-change="redrawFromNumber" step="any" rv-value="activeCustomization.px" />
                                    </div>
                                </li>
                                <li>
                                    <div>
                                        Y: &nbsp;<input type="number" rv-on-change="redrawFromNumber" step="any" rv-value="activeCustomization.py" />
                                    </div>
                                </li>
                                <li>
                                    <div>
                                        Width: &nbsp;
                                        <div class="wapf-input-with-append" style="width: auto"><input step="any" rv-on-change="redrawFromNumber" rv-value="activeCustomization.pw" type="number" /><div class="wapf-input-append" style="height:26px">px</div></div>
                                    </div>
                                </li>
                                <li>
                                    <div>
                                        Height: &nbsp;
                                        <div class="wapf-input-with-append" style="width: auto"><input step="any" rv-on-change="redrawFromNumber" type="number" rv-value="activeCustomization.ph" /><div class="wapf-input-append" style="height:26px">px</div></div>
                                    </div>
                                </li>
                            </ul>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="wapf_modal_overlay lcp-advanced">
            <div class="wapf_modal">
                <a class="wapf_close" href="#" onclick="javascript:event.preventDefault();jQuery('.lcp-advanced').hide();">&times;</a>
                <h3><?php _e('Advanced settings', 'wapf-lcp') ?></h3>
                <div style="line-height: 1.5;">
                    <div class="wapfc-customizer-option">
                        <div><?php _e('Text mode','wapf-lcp');?></div>
                        <div>
                            <select style="position: relative;z-index:2" rv-value="activeCustomization.textMode" rv-on-change="onChange">
                                <option value="default"><?php _e('Multi-line + overflow','wapf-lcp');?></option>
                                <option value="multiscalex"><?php _e('Multi-line + scale horizontally','wapf-lcp');?></option>
                                <option value="multiscaley"><?php _e('Multi-line + scale vertically','wapf-lcp');?></option>
                                <!--<option value="multiscale"><?php _e('Multi-line + auto size','wapf-lcp');?></option>-->
                                <option value="single"><?php _e('Single-line + overflow','wapf-lcp');?></option>
                                <option value="scaledown"><?php _e('Single-line + scale horizontally','wapf-lcp');?></option>
                            </select>
                            <div class="link-below" style="display: inline-block;margin-top:-15px">
                                <?php SW_WAPF_PRO\Includes\Classes\Html::help_modal( [ 'button' => __('What is this?', 'wapf-lcp'), 'title' => __('Text mode', 'wapf-lcp'), 'content' => __( 'This setting determines how text is rendered in the container you defined. <ul><li><strong>Multi-line + overflow: </strong>the text wraps onto multiple lines. If there\'s too much text, it will extend beyond the container\'s height, overflowing vertically.</li><li><strong>Single-line + overflow: </strong>the text stays on one line. If it\'s longer than the container, it overflows horizontally.</li><li><strong>Single-line + scale horizontally: </strong>The text stays on one line, and if it\'s too long, the font size automatically shrinks to fit within the container.</li><li><strong>Multi-line + scale horizontally/vertically: </strong>text can display on multiple lines, and if it\'s too long, the font size decreases automatically to fit within the container (either horizontally or vertically).</li></ul>','wapf-lcp') ] ) ?>
                            </div>
                        </div>
                    </div>
                    <div class="wapfc-customizer-option">
                        <div><?php _e('Line height','wapf-lcp');?></div>
                        <div>
                            <div style="display: flex">
                                <input type="number" min="0" max="200" step="any" rv-value="activeCustomization.lineheight.default" rv-on-change="onChange" />
                                <select  rv-value="activeCustomization.lineheight.unit" rv-on-change="onChange" >
                                    <option value="ul">Unitless (preferred)</option>
                                    <option value="px">Pixels</option>
                                </select>
                            </div>
                            <div style="display: inline-block;margin-top:8px">
                                <small><?php _e('Leave blank to auto calculate line height (recommended).', 'wapf-lcp'); ?></small>
                            </div>
                            <div class="link-below">
                                <?php _e('or','wapf-lcp');?>&nbsp;<span><a rv-on-click="openDynamicValue" data-for="lineheight" href="#"><?php _e('set dynamic value','wapf-lcp');?> <span rv-if="activeCustomization.lineheight.rules | isNotEmpty">({activeCustomization.lineheight.rules.length} rules)</span></a></span>
                            </div>
                        </div>
                    </div>

                </div>
                <div style="padding-top:15px;">
                    <a class="button button-primary button-large" href="#" onclick="event.preventDefault();jQuery('.lcp-advanced').hide();"><?php _e('Done','wapf-lcp');?></a>
                </div>
            </div>
        </div>

        <div class="wapf_modal_overlay wapfc-selector">
            <div class="wapf_modal modal--xlarge">
                <a class="wapf_close" href="#" onclick="event.preventDefault();jQuery('.wapfc-selector').hide()">×</a>
                <h3><?php _e('Set a value for "{activeValueSelector.for}"','wapf-lcp');?></h3>

                <div class="wapfc-selector-content">
                    <div class="wapfc-selector-input">

                        <input rv-if="activeValueSelector.renderAs | eq 'text'" type="text" rv-value="activeValueSelector.customize.default" rv-on-change="onChange" rv-placeholder="<?php _e('Default value','wapf-lcp');?>"/>

                        <select rv-if="activeValueSelector.renderAs | eq 'select'" rv-value="activeValueSelector.customize.default" rv-on-change="onChange">
                            <option rv-each-choice="activeValueSelector.options" rv-value="choice.value" rv-text="choice.label"></option>
                        </select>

                        <div rv-if="activeValueSelector.renderAs | eq 'size'" class="wapf-flex" style="width:100%;max-width: 600px">
                            <input placeholder="Desktop" type="number" min="1" max="4000" rv-value="activeValueSelector.customize.default"  rv-on-change="onChange"/>
                            <input placeholder="Tablet" type="number" min="1" max="4000" rv-value="activeValueSelectorTablet.default"  rv-on-change="onChange" style="margin-left:1%!important;"/>
                            <input placeholder="Mobile (landscape)" type="number" min="1" max="4000" rv-value="activeValueSelectorMobile.default"  rv-on-change="onChange" style="margin-left:1%!important;"/>
                            <input placeholder="Mobile (portrait)" type="number" min="1" max="4000" rv-value="activeValueSelectorXs.default"  rv-on-change="onChange" style="margin-left:1%!important;"/>
                        </div>

                    </div>
                    <p>
                        <?php _e("The default value can dynamically change when a product field value changes. In the case of a variable product, the value can also change when a variation or attribute is selected. You can define those rules by adding conditions below.",'wapf-lcp'); ?>
                    </p>
                    <div class="wapf-collapsible__holder">
                        <div rv-on-click="maybeSetSizeRules" rv-each-rule="activeValueSelector.customize.rules" class="wapf-collapsible__wrapper" rv-data-variable-id="variable.name">
                            <div class="wapf-collapsible__header">
                                <div class="wapf-collapsible__icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="-53 0 512 512"><path d="M385.89 353c11.044 0 20-8.953 20-20V130c0-44.113-35.89-80-80-80h-63V20c0-11.047-8.956-20-20-20h-80c-11.046 0-20 8.953-20 20v30H80C35.887 50 0 85.887 0 130v302c0 44.113 35.887 80 80 80h245.89c44.11 0 80-35.887 80-80 0-11.047-8.956-20-20-20-11.046 0-20 8.953-20 20 0 22.055-17.945 40-40 40H80c-22.055 0-40-17.945-40-40V130c0-22.055 17.945-40 40-40h62.89v10c0 33.086 26.915 60 60 60 33.083 0 60-26.914 60-60V90h63c22.055 0 40 17.945 40 40v203c0 11.047 8.954 20 20 20zm-163-253c0 11.027-8.972 20-20 20-11.03 0-20-8.973-20-20V40h40zm87.403 127.625c9.074 6.297 11.324 18.762 5.027 27.836l-88.984 128.203c-.266.383-.547.758-.84 1.125-8.984 11.184-22.348 18.145-36.66 19.098-1.133.074-2.27.113-3.399.113-13.136 0-25.91-5.066-35.476-14.176-.094-.09-.188-.176-.277-.265l-52.86-52.282c-7.851-7.77-7.922-20.433-.156-28.285 7.77-7.855 20.434-7.922 28.285-.156l52.692 52.113c3.132 2.914 6.675 3.149 8.527 3.024 1.793-.118 5.121-.782 7.8-3.832l88.485-127.489c6.3-9.074 18.762-11.328 27.836-5.027zm0 0"/></svg>
                                </div>
                                <div class="wapf-collapsible__name">
                                    Condition type: {rule.type}
                                </div>
                                <div class="wapf-collapsible__actions">
                                    <a href="#" style="color: #a00 !important" title="<?php _e('Delete rule','wapf-lcp');?>" rv-on-click="deleteValueRule">Delete</a>
                                </div>
                            </div>
                            <div class="wapf-collapsible__body">
                                <div class="wapf-field__setting">
                                    <div class="wapf-setting__label">
                                        <label>
                                            <?php _e('Condition type','wapf-lcp');?>
                                        </label>
                                        <p class="wapf-description">
                                            <?php _e('When should the value change?','wapf-lcp'); ?>
                                        </p>
                                    </div>
                                    <div class="wapf-setting__input">
                                        <div>
                                            <select rv-on-change="onChange" rv-value="rule.type">
                                                <option value="field"><?php _e('If product field value changes','wapf-lcp');?></option>
                                                <option rv-if="isVariation" value="variation"><?php _e('If a variation is selected','wapf-lcp');?></option>
                                                <option rv-if="isVariation" value="attribute"><?php _e('If a variation attribute is selected','wapf-lcp');?></option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="wapf-field__setting" rv-if="rule.type |eq 'field'">
                                    <div class="wapf-setting__label">
                                        <label>
                                            <?php _e('Field','wapf-lcp');?>
                                        </label>
                                        <p class="wapf-description">
                                            <?php _e('Select a field','wapf-lcp'); ?>
                                        </p>
                                    </div>
                                    <div class="wapf-setting__input">
                                        <div>
                                            <select rv-value="rule.id" rv-on-change="onFieldChange" >
                                                <option value=""><?php _e('Select a field ID','wapf-lcp')?></option>
                                                <option rv-each-f="selectFieldIds" rv-value="f.id">{f.label}</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="wapf-field__setting" rv-if="rule.type |eq 'field'">
                                    <div class="wapf-setting__label">
                                        <label>
                                            <?php _e('Field value','wapf-lcp');?>
                                        </label>
                                    </div>
                                    <div class="wapf-setting__input">
                                        <div>
                                            <select rv-value="rule.meta" rv-on-change="onMetaChange" >
                                                <option value=""><?php _e('Select a value','wapf-lcp')?></option>
                                                <option rv-each-f="selectFieldIds | query 'first' 'id' '==' rule.id 'get' 'options'" rv-value="f.id">{f.label}</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="wapf-field__setting" rv-if="rule.type |eq 'variation'">
                                    <div class="wapf-setting__label">
                                        <label>
                                            <?php _e('Variation','wapf-lcp');?>
                                        </label>
                                        <p class="wapf-description">
                                            <?php _e('Select a variation','wapf-lcp'); ?>
                                        </p>
                                    </div>
                                    <div class="wapf-setting__input">
                                        <div>
                                            <select rv-value="rule.id" rv-on-change="onChange" >
                                                <option value=""><?php _e('Select a variation ID','wapf-lcp')?></option>
                                                <option rv-each-v="variationIds" rv-value="v.id">{v.label}</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="wapf-field__setting" rv-if="rule.type |eq 'attribute'">
                                    <div class="wapf-setting__label">
                                        <label>
                                            <?php _e('Attribute','wapf-lcp');?>
                                        </label>
                                        <p class="wapf-description">
                                            <?php _e('Select an attribute','wapf-lcp'); ?>
                                        </p>
                                    </div>
                                    <div class="wapf-setting__input">
                                        <div>
                                            <select rv-value="rule.id" rv-on-change="onChange" >
                                                <option value=""><?php _e('Select an attribute','wapf-lcp')?></option>
                                                <option rv-each-a="attributeIds" rv-value="a.id">{a.label}</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="wapf-field__setting">
                                    <div class="wapf-setting__label">
                                        <label>
                                            <?php _e('New value','wapf-lcp');?>
                                        </label>
                                        <p class="wapf-description">
                                            <?php _e('When the above condition is met, what should the new value be?','wapf-lcp'); ?>
                                        </p>
                                    </div>
                                    <div class="wapf-setting__input">
                                        <div>

                                            <input rv-if="activeValueSelector.renderAs | eq 'text'" type="text" rv-value="rule.value" rv-on-change="onChange" rv-placeholder="<?php _e('New value','wapf-lcp');?>"/>

                                            <select rv-if="activeValueSelector.renderAs | eq 'select'" rv-value="rule.value" rv-on-change="onChange">
                                                <option rv-each-choice="activeValueSelector.options" rv-value="choice.value" rv-text="choice.label"></option>
                                            </select>

                                            <div rv-if="activeValueSelector.renderAs | eq 'size'" class="wapf-flex" style="width:100%;max-width: 600px">
                                                <input placeholder="Desktop" type="number" min="1" max="4000" rv-value="rule.value" rv-on-change="onChange" />
                                                <input placeholder="Tablet" type="number" min="1" max="4000" rv-value="tabletRule.value" rv-on-change="onChange" style="margin-left:1%!important;"/>
                                                <input placeholder="Mobile (landscape)" type="number" min="1" max="4000" rv-value="mobileRule.value" rv-on-change="onChange" style="margin-left:1%!important;"/>
                                                <input placeholder="Mobile (portrait)" type="number" min="1" max="4000" rv-value="xsRule.value" rv-on-change="onChange" style="margin-left:1%!important;"/>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div style="margin-top:25px">
                        <a class="button button-primary button-large" href="#" onclick="event.preventDefault();jQuery('.wapfc-selector').hide()">Done</a>
                        <a href="#" class="button button-secondary button-large" rv-on-click="addValueRule"><?php _e('Add new condition','wapf-lcp'); ?></a>
                    </div>
                </div>
            </div>
        </div>


        <div style="padding:15px;" rv-show="rows|isNotEmpty" class="wapfc-rows wapf-panel">
            <table class="wapf-table">
                <thead>
                    <tr>
                        <th><?php _e('Image','wapf-lcp'); ?></th>
                        <th><?php _e('Field','wapf-lcp'); ?></th>
                        <th>&nbsp;</th>
                        <th>&nbsp;</th>
                    </tr>
                </thead>
                <tbody>
                    <tr rv-each-row="rows">
                        <td>
                            <img style="max-width:55px" rv-src="row.url" />
                        </td>
                        <td>
                            <select rv-value="row.field" rv-on-change="onChange">
                                <option rv-each-choice="fieldIds" rv-value="choice.id" rv-text="choice.label"></option>
                            </select>
                        </td>
                        <td>
                            <a href="#" rv-on-click="openCustomizerOnFinalStep" class="button button-secondary"><?php _e('Configure','wapf-lcp'); ?></a>
                        </td>
                        <td style="vertical-align: middle;text-align: right">
                            <a title="<?php _e('Delete this row','wapf-lcp');?>" href="#" rv-on-click="deleteCustomizerRow" class="wapf-button--tiny-rounded wapf-del"></a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="wapf-list--empty" rv-show="hasImages" rv-if="fieldIds|isNotEmpty">
            <a href="#" rv-on-click="addNew" class="button button-primary button-large"><?php _e('Add new','wapf-lcp'); ?></a>
        </div>
        <div class="wapf-list--empty" rv-show="hasImages|eq false">
            <?php _e("Make sure your product has at least one product or gallery image or you've added image fields with our plugin. Also make sure to create some fields of type 'text', 'textarea' or 'file' first.",'wapf-lcp'); ?>
        </div>
    </div>
    <?php

}