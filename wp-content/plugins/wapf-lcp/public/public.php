<?php

use SW_WAPF_PRO\Includes\Classes\Helper;

add_action( 'wp_enqueue_scripts', 'wapflcp_register_frontend_assets');
add_filter( 'wapf/html/product_totals/data', 'wapflcp_add_customizer_config_to_frontend', 10, 2);

function wapflcp_register_frontend_assets() {
	
	$url =  trailingslashit(plugin_dir_url(WAPFLCP_STARTFILE));
	$version = WAPFLCP_VERSION;
	
	wp_enqueue_style('wapflcp-frontend', $url . 'assets/css/frontend.min.css', [], $version);
    $deps = apply_filters('lcp/script_dependencies', ['jquery','wapf-frontend'] ); // Some themes perform our script before flexslider. We don't want that.
	wp_enqueue_script('wapflcp-frontend', $url . 'assets/js/frontend.min.js', $deps, $version, true);
    wp_script_add_data( 'wapflcp-frontend', 'strategy', 'defer' ); // Woo now defers scripts since WordPress 6.3. This adds defer to our scripts in a pre 6.3 compatible way.
    $script_vars = [ 'customZoom' => apply_filters( 'lcp/enable_custom_zoom', false) ];

    // Woodmart custom zoom integration.
    if( function_exists( 'woodmart_get_opt' ) && woodmart_get_opt( 'image_action' ) === 'zoom' ) {
        $script_vars[ 'customZoom' ] = true;
    }

    wp_localize_script('wapflcp-frontend', 'lcpConfig', $script_vars);

}

function wapflcp_add_customizer_config_to_frontend($data, $product) {
	
	$rows = get_post_meta($product->get_id(), '_wapflcp_rows', true);

	if( ! empty( $rows ) ) {

        $data['data-lcp-scroll'] = get_option('wapflcp_scroll', 'no') === 'yes' ? 1 : 0;
		$data['data-customizer'] = wapflcp_to_html_attribute( wapflcp_prep_rows( $rows ) );

		$custom_fonts = get_option('wapf_lcp_fonts', []);
		if( ! empty( $custom_fonts ) ) {

			for($i = 0; $i < count( $custom_fonts ); $i++) {
				unset($custom_fonts[$i]['path']);
			}

			$data['data-fonts'] = wapflcp_to_html_attribute($custom_fonts);
		}


	}

	return $data;
}

function wapflcp_prep_rows( $rows ) {

    if( empty( $rows ) ) return [];

    foreach ( $rows as &$row) {

        if( ! empty( $row['align'] )  && ! is_array( $row['align'] ) ) { // pre 1.3.0, align was a simple value
            $row['align'] = [
                'default' => $row['align'],
                'rules' => []
            ];
        }

        if( ! empty( $row['size'] )  && ! is_array( $row['size'] ) ) { // pre 1.3.0, this was a simple value
            $row['size'] = [
                'default' => $row['size'],
                'rules' => []
            ];
        }

        if( ! empty( $row['sizeMobile'] )  && ! is_array( $row['sizeMobile'] ) ) { // pre 1.3.0, this was a simple value
            $row['sizeMobile'] = [
                'default' => $row['sizeMobile'],
                'rules' => []
            ];
        }

        if( ! empty( $row['sizeTablet'] )  && ! is_array( $row['sizeTablet'] ) ) { // pre 1.3.0, this was a simple value
            $row['sizeTablet'] = [
                'default' => $row['sizeTablet'],
                'rules' => []
            ];
        }

        if( empty( $row['textMode'] ) ) { // pre 1.4, this didn't exist
            $row['textMode'] = 'default';
        }
        
        if( empty( $row['valign'] ) ) { // pre 1.4, this didn't exist
            $row['valign'] = 'top';
        }

        if( empty( $row['lineheight'] ) ) { // pre 1.5.1, this didn't exist
            $row['lineheight'] = [
                'default' => '',
                'rules' => []
            ];
        }

    }

    return $rows;

}