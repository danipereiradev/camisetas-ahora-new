<?php
/*
 * Plugin Name: Advanced Product Fields add-on: Image Upload
 * Plugin URI: https://www.studiowombat.com/plugin/apf-image-upload-addon/?utm_source=wapf-aiu&utm_medium=plugin&utm_campaign=plugins
 * Description: add image editing & processing features to your APF upload fields.
 * Version: 1.3.2
 * Author: StudioWombat
 * Author URI: https://studiowombat.com/?utm_source=wapf-aiu&utm_medium=plugin&utm_campaign=plugins
 * Text Domain: wapf-aiu
 * WC requires at least: 3.6.0
 * WC tested up to: 10.1
 */

// Load constants
if ( ! defined( 'WAPFAIU_VERSION' ) ) {
	define( 'WAPFAIU_VERSION', '1.3.2' );
}

if ( ! defined( 'WAPFAIU_STARTFILE' ) ) {
	define( 'WAPFAIU_STARTFILE', __FILE__ );
}

// Load text domain
add_action( 'plugins_loaded', 'wapfaiu_load_textdomain' );
function wapfaiu_load_textdomain() {
	
	load_plugin_textdomain( 'wapf-aiu', FALSE, basename( dirname( __FILE__ ) ) . '/languages/' );

}

if(is_admin() && !wp_doing_ajax()) {

	require_once dirname( __FILE__ ) . '/admin/class-updater.php';
	require_once dirname( __FILE__ ) . '/admin/admin.php';

}

require_once dirname( __FILE__ ) . '/public/public.php';

// Declare HPOS compatibility.
add_action( 'before_woocommerce_init', function() {
    if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
        \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
    }
} );
