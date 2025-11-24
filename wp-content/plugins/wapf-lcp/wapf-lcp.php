<?php
/**
 * Plugin Name:       Advanced Product Fields add-on: Live Content Preview
 * Plugin URI:        https://studiowombat.com/plugin/advanced-product-fields-for-woocommerce/?utm_source=wapf-lcp&utm_medium=plugin&utm_campaign=plugins
 * Description:       Allows you to preview live text on your product images.
 * Version:           3.1.3
 * Requires at least: 5.0
 * Tested up to:      6.7.2
 * Requires PHP:      5.6
 * WC requires at least: 3.6.0
 * WC tested up to:   9.8
 * Author:            StudioWombat
 * Text Domain: wapf-lcp
 * Author URI:        https://studiowombat.com/?utm_source=wapf-lcp&utm_medium=plugin&utm_campaign=plugins
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 */

use SW_WAPF_PRO\Includes\Classes\Helper;
use SW_WAPF_PRO\Includes\Classes\Util;

// Load constants
if ( ! defined( 'WAPFLCP_VERSION' ) ) {
	define( 'WAPFLCP_VERSION', '3.1.3' );
}

if ( ! defined( 'WAPFLCP_STARTFILE' ) ) {
	define( 'WAPFLCP_STARTFILE', __FILE__ );
}

function wapflcp_to_html_attribute( $thing ) {
    if( method_exists('SW_WAPF_PRO\Includes\Classes\Util', 'to_html_attribute_string') ) {
        return Util::to_html_attribute_string( $thing );
    }
    return Helper::thing_to_html_attribute_string( $thing );
}

// Load text domain
add_action( 'plugins_loaded', 'wapflcp_load_textdomain' );
function wapflcp_load_textdomain() {

	load_plugin_textdomain( 'wapf-lcp', FALSE, basename( dirname( __FILE__ ) ) . '/languages/' );

}

add_action( 'plugins_loaded', function() {

	if(wapflcp_is_wapf_active()) {

        require_once dirname( __FILE__ ) . '/public/public.php'; // Load public before admin for some global functions


        if(is_admin() && !wp_doing_ajax()) {

			require_once dirname( __FILE__ ) . '/admin/class-updater.php';
			require_once dirname( __FILE__ ) . '/admin/admin.php';

		}

	}

});

function wapflcp_is_wapf_active() {

	if (is_multisite()) {
		$plugins = array_merge([],get_site_option('active_sitewide_plugins'),get_option('active_plugins'));
		if (isset($plugins['advanced-product-fields-for-woocommerce-pro/advanced-product-fields-for-woocommerce-pro.php']) || isset($plugins['advanced-product-fields-for-woocommerce-extended/advanced-product-fields-for-woocommerce-extended.php']))
			return true;
	}

	$plugins = get_option('active_plugins');
	if(in_array('advanced-product-fields-for-woocommerce-pro/advanced-product-fields-for-woocommerce-pro.php', $plugins) || in_array('advanced-product-fields-for-woocommerce-extended/advanced-product-fields-for-woocommerce-extended.php', $plugins))
		return true;

	return false;

}

// Declare HPOS compatibility.
add_action( 'before_woocommerce_init', function() {
    if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
        \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
    }
} );