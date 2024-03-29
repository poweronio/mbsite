<?php

/**
 * Include the TGM_Plugin_Activation class.
 */
require_once dirname( __FILE__ ) . '/class-tgm-plugin-activation.php';

add_action( 'tgmpa_register', 'grve_theme_register_required_plugins' );

/**
 * Register the required plugins for this theme.
 *
 * This function is hooked into tgmpa_init, which is fired within the
 * TGM_Plugin_Activation class constructor.
 */
function grve_theme_register_required_plugins() {

	/**
	 * Array of plugin arrays. Required keys are name and slug.
	 * If the source is NOT from the .org repo, then source is also required.
	 */
	$plugins = array(

		array(
			'name'					=> 'WPBakery Visual Composer', // The plugin name
			'slug'					=> 'js_composer',
			'source'				=> get_template_directory() . '/includes/plugins/js_composer.zip',
			'required'				=> true,
			'version'				=> '4.5.1',
			'force_activation' 		=> false,
			'force_deactivation' 	=> false,
			'external_url'			=> '',
		),

		array(
			'name'					=> 'Osmosis Visual Composer Extension', // The plugin name
			'slug'					=> 'grve-osmosis-vc-extension',
			'source'				=> get_template_directory() . '/includes/plugins/grve-osmosis-vc-extension.zip',
			'required'				=> true,
			'version'				=> '2.0.0',
			'force_activation' 		=> false,
			'force_deactivation' 	=> false,
			'external_url'			=> '',
		),

 		array(
			'name'					=> 'Osmosis Demo Importer', // The plugin name
			'slug'					=> 'grve-osmosis-dummy-importer',
			'source'				=> get_template_directory() . '/includes/plugins/grve-osmosis-dummy-importer.zip',
			'required'				=> false,
			'version'				=> '2.1.0',
			'force_activation' 		=> false,
			'force_deactivation' 	=> false,
			'external_url'			=> '',
		),

		array(
			'name'					=> 'Revolution Slider', // The plugin name
			'slug'					=> 'revslider',
			'source'				=> get_template_directory() . '/includes/plugins/revslider.zip',
			'required'				=> false,
			'version'				=> '4.6.92',
			'force_activation' 		=> false,
			'force_deactivation' 	=> false,
			'external_url'			=> '',
		),

		array(
			'name'					=> 'Go - Responsive Pricing & Compare Tables', // The plugin name
			'slug'					=> 'go_pricing',
			'source'				=> get_template_directory() . '/includes/plugins/go_pricing.zip',
			'required'				=> false,
			'version'				=> '2.4.5',
			'force_activation' 		=> false,
			'force_deactivation' 	=> false,
			'external_url'			=> '',
		),

		array(
			'name'					=> 'MailChimp for WordPress Lite', // The plugin name
			'slug'					=> 'mailchimp-for-wp',
			'required'				=> false,
		),

		array(
			'name'				=> 'Contact Form 7',
			'slug'				=> 'contact-form-7',
			'required'			=> false,
		),

		array(
			'name'				=> 'WooCommerce',
			'slug'				=> 'woocommerce',
			'required'			=> false,
			'force_activation'	=> false,
		),

	);

	/**
	* Array of configuration settings. Amend each line as needed.
	* If you want the default strings to be available under your own theme domain,
	* leave the strings uncommented.
	* Some of the strings are added into a sprintf, so see the comments at the
	* end of each line for what each argument will be.
	*/
	$config = array(
		'id'           => 'tgmpa',                 // Unique ID for hashing notices for multiple instances of TGMPA.
		'default_path' => '',                      // Default absolute path to pre-packaged plugins.
		'menu'         => 'tgmpa-install-plugins', // Menu slug.
		'has_notices'  => true,                    // Show admin notices or not.
		'dismissable'  => true,                    // If false, a user cannot dismiss the nag message.
		'dismiss_msg'  => '',                      // If 'dismissable' is false, this message will be output at top of nag.
		'is_automatic' => false,                   // Automatically activate plugins after installation or not.
		'message'      => '',                      // Message to output right before the plugins table.
		'strings'      => array(
			'page_title'                      => __( 'Install Required Plugins', 'tgmpa' ),
			'menu_title'                      => __( 'Install Plugins', 'tgmpa' ),
			'installing'                      => __( 'Installing Plugin: %s', 'tgmpa' ), // %s = plugin name.
			'oops'                            => __( 'Something went wrong with the plugin API.', 'tgmpa' ),
			'notice_can_install_required'     => _n_noop( 'This theme requires the following plugin: %1$s.', 'This theme requires the following plugins: %1$s.', 'tgmpa' ), // %1$s = plugin name(s).
			'notice_can_install_recommended'  => _n_noop( 'This theme recommends the following plugin: %1$s.', 'This theme recommends the following plugins: %1$s.', 'tgmpa' ), // %1$s = plugin name(s).
			'notice_cannot_install'           => _n_noop( 'Sorry, but you do not have the correct permissions to install the %s plugin. Contact the administrator of this site for help on getting the plugin installed.', 'Sorry, but you do not have the correct permissions to install the %s plugins. Contact the administrator of this site for help on getting the plugins installed.', 'tgmpa' ), // %1$s = plugin name(s).
			'notice_can_activate_required'    => _n_noop( 'The following required plugin is currently inactive: %1$s.', 'The following required plugins are currently inactive: %1$s.', 'tgmpa' ), // %1$s = plugin name(s).
			'notice_can_activate_recommended' => _n_noop( 'The following recommended plugin is currently inactive: %1$s.', 'The following recommended plugins are currently inactive: %1$s.', 'tgmpa' ), // %1$s = plugin name(s).
			'notice_cannot_activate'          => _n_noop( 'Sorry, but you do not have the correct permissions to activate the %s plugin. Contact the administrator of this site for help on getting the plugin activated.', 'Sorry, but you do not have the correct permissions to activate the %s plugins. Contact the administrator of this site for help on getting the plugins activated.', 'tgmpa' ), // %1$s = plugin name(s).
			'notice_ask_to_update'            => _n_noop( 'The following plugin needs to be updated to its latest version to ensure maximum compatibility with this theme: %1$s.', 'The following plugins need to be updated to their latest version to ensure maximum compatibility with this theme: %1$s.', 'tgmpa' ), // %1$s = plugin name(s).
			'notice_cannot_update'            => _n_noop( 'Sorry, but you do not have the correct permissions to update the %s plugin. Contact the administrator of this site for help on getting the plugin updated.', 'Sorry, but you do not have the correct permissions to update the %s plugins. Contact the administrator of this site for help on getting the plugins updated.', 'tgmpa' ), // %1$s = plugin name(s).
			'install_link'                    => _n_noop( 'Begin installing plugin', 'Begin installing plugins', 'tgmpa' ),
			'activate_link'                   => _n_noop( 'Begin activating plugin', 'Begin activating plugins', 'tgmpa' ),
			'return'                          => __( 'Return to Required Plugins Installer', 'tgmpa' ),
			'plugin_activated'                => __( 'Plugin activated successfully.', 'tgmpa' ),
			'complete'                        => __( 'All plugins installed and activated successfully. %s', 'tgmpa' ), // %s = dashboard link.
			'nag_type'                        => 'updated' // Determines admin notice type - can only be 'updated', 'update-nag' or 'error'.
		)
	);

	tgmpa( $plugins, $config );

}

/**
 * Force Visual Composer to initialize as "built into the theme". This will hide certain tabs under the Settings->Visual Composer page
 */
if ( function_exists( 'vc_set_as_theme' ) ) {
	vc_set_as_theme( true );
}