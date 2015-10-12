<?php
/**
 * This is the main GeoDirectory BuddyPress Integration plugin file, here we declare and call the important stuff.
 *
 * @global array $geodir_addon_list GeoDirectory addon list array.
 *
 * @since 1.0.0
 * @package GeoDirectory_BuddyPress_Integration
 */

/*
Plugin Name: GeoDirectory BuddyPress Integration
Plugin URI: http://wpgeodirectory.com/
Description: Integrates GeoDirectory listing activity with the BuddyPress.
Version: 1.0.5
Author: GeoDirectory
Author URI: http://wpgeodirectory.com/
License: GPLv3
 
GeoDirectory BuddyPress Integration is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
any later version.
 
GeoDirectory BuddyPress Integration is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.
 
You should have received a copy of the GNU General Public License
along with GeoDirectory BuddyPress Integration. If not, see license.txt.
*/

// MUST have WordPress.
if ( !defined( 'WPINC' ) )
	exit( 'Do NOT access this file directly: ' . basename( __FILE__ ) );
	
// Define Constants
define( 'GEODIR_BUDDYPRESS_VERSION', '1.0.5' );
define( 'GEODIR_BUDDYPRESS_PLUGIN_PATH', WP_PLUGIN_DIR . '/' . plugin_basename( dirname( __FILE__ ) ) );
define( 'GEODIR_BUDDYPRESS_PLUGIN_URL', WP_PLUGIN_URL . '/' . plugin_basename( dirname( __FILE__ ) ) );
define( 'GDBUDDYPRESS_TEXTDOMAIN', 'gdbuddypress' );

global $geodir_addon_list;

$geodir_addon_list['geodir_buddypress'] = 'yes' ;

if ( is_admin() ) {
	// GEODIRECTORY CORE ALIVE CHECK START
	if ( !function_exists( 'is_plugin_active' ) ) {
		include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
	}
	
	if ( !is_plugin_active( 'geodirectory/geodirectory.php' ) ) {
		return;
	}
	// GEODIRECTORY CORE ALIVE CHECK END
	
	require_once( 'gd_update.php' ); // require update script
}

/**
 * Localisation
 */
add_action('plugins_loaded','geodir_load_translation_gdbuddypress');
function geodir_load_translation_gdbuddypress()
{
    $locale = apply_filters('plugin_locale', get_locale(), GDBUDDYPRESS_TEXTDOMAIN);
    load_textdomain(GDBUDDYPRESS_TEXTDOMAIN, WP_LANG_DIR . '/' . GDBUDDYPRESS_TEXTDOMAIN . '/' . GDBUDDYPRESS_TEXTDOMAIN . '-' . $locale . '.mo');
    load_plugin_textdomain(GDBUDDYPRESS_TEXTDOMAIN, false, dirname(plugin_basename(__FILE__)) . '/gdbuddypress-languages');
}

/**
 * Include core files
 */
if (class_exists('BuddyPress')) {
	require_once( 'includes/gdbuddypress_functions.php' );
	require_once( 'includes/gdbuddypress_template_functions.php' );
	require_once( 'includes/gdbuddypress_hook_actions.php' );
}

/**
 * Admin init + activation hooks
 */
if ( is_admin() ) {
	register_activation_hook( __FILE__ , 'geodir_buddypress_activation' );
	register_deactivation_hook( __FILE__ , 'geodir_buddypress_deactivation' );
	register_uninstall_hook( __FILE__, 'geodir_buddypress_uninstall' );
}

add_action( 'activated_plugin', 'geodir_buddypress_plugin_activated' ) ;
?>