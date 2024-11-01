<?php
/*
Plugin Name: Super Hero Slider
Plugin URI: http://catapultthemes.com/super-hero-slider/
Description: A simple way to make great sliders
Version: 1.6.4
Author: Catapult Themes
Author URI: http://catapultthemes.com/
Text Domain: super-hero-slider
Domain Path: /languages
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function ctshs_load_plugin_textdomain() {
    load_plugin_textdomain( 'super-hero-slider', FALSE, basename( dirname( __FILE__ ) ) . '/languages/' );
}
add_action( 'plugins_loaded', 'ctshs_load_plugin_textdomain' );

/**
 * Define constants
 **/
if ( ! defined( 'SHS_PLUGIN_URL' ) ) {
	define( 'SHS_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
}
if ( function_exists ( 'ctshs_pro_load_plugin_textdomain' ) ) {
	define( 'SHS_PRO_ENABLED', true );
}

if( is_admin() ) {
	require_once dirname( __FILE__ ) . '/admin/class-ctshs-admin.php';
	require_once dirname( __FILE__ ) . '/admin/admin-ajax.php';
	// Admin
	$CT_SHS_Admin = new CT_SHS_Admin();
	$CT_SHS_Admin -> init();
}
function ctshs_admin() {
	require_once dirname( __FILE__ ) . '/admin/class-ctshs-metaboxes.php';
	require_once dirname( __FILE__ ) . '/admin/metaboxes.php';
	// Our metaboxes
	$metaboxes = ctshs_metaboxes();
	$CT_SHS_Metaboxes = new CT_SHS_Metaboxes ( $metaboxes );
	$CT_SHS_Metaboxes -> init();
}
// We call this after the init hook in order to ensure post types have been registered
if ( is_admin() ) {
	add_action ( 'admin_init', 'ctshs_admin', 10 );
}

require_once dirname( __FILE__ ) . '/functions/functions.php';
require_once dirname( __FILE__ ) . '/functions/functions-featured-image.php';
require_once dirname( __FILE__ ) . '/functions/functions-themes.php';
require_once dirname( __FILE__ ) . '/functions/shortcodes.php';
require_once dirname( __FILE__ ) . '/public/class-ctshs-public.php';

$CT_SHS_Public = new CT_SHS_Public();
$CT_SHS_Public -> init();

/**
 * This function allows you to track usage of your plugin
 * Place in your main plugin file
 * Refer to https://wisdomplugin.com/support for help
 */
if( ! class_exists( 'Plugin_Usage_Tracker') ) {
	require_once dirname( __FILE__ ) . '/tracking/class-plugin-usage-tracker.php';
}
if( ! function_exists( 'ctshs_start_plugin_tracking' ) ) {
	function ctshs_start_plugin_tracking() {
		$wisdom = new Plugin_Usage_Tracker(
			__FILE__,
			'https://wisdomplugin.com',
			array( 'ctshs_media_settings' ),
			true,
			true,
			1
		);
	}
	ctshs_start_plugin_tracking();
}
