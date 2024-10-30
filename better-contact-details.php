<?php
/*
Plugin Name: Better Contact Details
Plugin URI: http://catapultthemes.com/better-contact-details/
Description: Maintain contact details - even when you switch themes
Version: 1.0.1
Author: Catapult Themes
Author URI: http://catapultthemes.com/
Text Domain: better-contact-details
Domain Path: /languages
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function bcd_load_plugin_textdomain() {
    load_plugin_textdomain( 'better-contact-details', FALSE, basename( dirname( __FILE__ ) ) . '/languages/' );
}
add_action( 'plugins_loaded', 'bcd_load_plugin_textdomain' );

/**
 * Define constants
 **/
if ( ! defined( 'BCD_PLUGIN_URL' ) ) {
	define( 'BCD_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
}

require_once dirname( __FILE__ ) . '/public/functions.php';

// Admin
if ( is_admin() ) {
	require_once dirname( __FILE__ ) . '/admin/class-bcd-admin.php';
	$BCD_Admin = new BCD_Admin();
	$BCD_Admin -> init();
}

// Public
require_once dirname( __FILE__ ) . '/public/class-bcd-public.php';
$BCD_Public = new BCD_Public();
$BCD_Public -> init();

// Widget
require_once dirname( __FILE__ ) . '/public/widget-better-contact-details.php';
function bcd_init_widget() {
	register_widget ( 'Better_Contact_Details_Widget' );
}
add_action ( 'widgets_init', 'bcd_init_widget' );