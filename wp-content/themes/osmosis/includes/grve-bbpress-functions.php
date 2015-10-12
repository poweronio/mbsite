<?php

/*
*	bbPress helper functions and configuration
*
* 	@version	1.0
* 	@author		Greatives Team
* 	@URI		http://greatives.eu
*/

/**
 * Helper function to check if bbPress is enabled
 */
function grve_bbpress_enabled() {
	if ( class_exists( 'bbPress' ) ) {
		return true;
	}
	return false;
}

//If woocomerce plugin is not enabled return
if ( !grve_bbpress_enabled() ) {
	return false;
}

/**
 * De-register bbPress styles
 */
add_filter( 'bbp_default_styles', 'grve_bbpress_deregister_styles', 10, 1 );
function grve_bbpress_deregister_styles( $styles ) {
	return array();
}

/**
 * Register custom bbPress styles
 */
if( !is_admin() ) {
	add_action('bbp_enqueue_scripts', 'grve_bbpress_register_styles', 15 );
}
function grve_bbpress_register_styles() {
	wp_enqueue_style( 'grve-bbpress-general', get_template_directory_uri() . '/css/bbpress.css', array(), '2.0.0', 'all' );
}

?>