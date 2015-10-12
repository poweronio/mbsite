<?php
/**
 * Contains functions related to GD captcha plugin.
 *
 * @since 1.0.0
 * @package GeoDirectory_BuddyPress_Integration
 */

// MUST have WordPress.
if ( !defined( 'WPINC' ) )
	exit( 'Do NOT access this file directly: ' . basename( __FILE__ ) );

/**
 * plugin activation hook.
 *
 * @since 1.0.0
 * @package GeoDirectory_BuddyPress_Integration
 */
function geodir_buddypress_activation() {
	if ( get_option( 'geodir_installed' ) ) {
		add_option( 'geodir_buddypress_activation_redirect', 1 );
	}
}

/**
 * plugin deactivation hook.
 *
 * @since 1.0.0
 * @package GeoDirectory_BuddyPress_Integration
 */
function geodir_buddypress_deactivation() {
}

/**
 * plugin uninstall hook.
 *
 * @since 1.0.0
 * @package GeoDirectory_BuddyPress_Integration
 */
function geodir_buddypress_uninstall() {
	delete_option( 'geodir_buddypress_activation_redirect' );
	delete_option( 'geodir_buddypress_tab_listing' );
	delete_option( 'geodir_buddypress_tab_review' );
	delete_option( 'geodir_buddypress_tab_fav' );
	delete_option( 'geodir_buddypress_activity_listing' );
	delete_option( 'geodir_buddypress_activity_review' );
	delete_option( 'geodir_buddypress_link_listing' );
	delete_option( 'geodir_buddypress_link_favorite' );
	delete_option( 'geodir_buddypress_bp_register' );
}

/**
 * plugin activation redirect.
 *
 * @since 1.0.0
 * @package GeoDirectory_BuddyPress_Integration
 */
function geodir_buddypress_activation_redirect() {
	if ( get_option( 'geodir_buddypress_activation_redirect', false ) ) {
		delete_option( 'geodir_buddypress_activation_redirect' );
		
		wp_redirect( admin_url( 'admin.php?page=geodirectory&tab=geodir_buddypress&subtab=gdbuddypress_settings' ) ); 
	}
}

/**
 * check GeoDirectory plugin installed.
 *
 * @since 1.0.0
 * @package GeoDirectory_BuddyPress_Integration
 * @param string $plugin Path to a plugin file or directory, relative to the plugins directory (without the leading and trailing slashes).
 */
function geodir_buddypress_plugin_activated( $plugin ) {
	if ( !get_option( 'geodir_installed' ) )  {
		$file = plugin_basename( __FILE__ );
		
		if ( $file == $plugin ) {
			$all_active_plugins = get_option( 'active_plugins', array() );
			
			if ( !empty( $all_active_plugins ) && is_array( $all_active_plugins ) ) {
				foreach ( $all_active_plugins as $key => $plugin ) {
					if ( $plugin == $file ) {
						unset( $all_active_plugins[$key] );
					}
				}
			}
			update_option( 'active_plugins', $all_active_plugins );
		}
		
		wp_die( __( '<span style="color:#FF0000">There was an issue determining where GeoDirectory Plugin is installed and activated. Please install or activate GeoDirectory Plugin.</span>', GDBUDDYPRESS_TEXTDOMAIN ) );
	}
}

/**
 * Enqueue plugin css and js.
 *
 * @since 1.0.0
 * @package GeoDirectory_BuddyPress_Integration
 *
 * @global string $pagenow Current page name.
 */
function geodir_buddypress_admin_scripts() {
	global $pagenow;
	
	if ( $pagenow == 'admin.php' && $_REQUEST['page'] == 'geodirectory' && isset( $_REQUEST['tab'] ) && $_REQUEST['tab'] == 'geodir_buddypress' ) {
		// Style
		wp_register_style( 'geodir_buddypress_style', GEODIR_BUDDYPRESS_PLUGIN_URL . '/css/gdbuddypress.css' );
		wp_enqueue_style( 'geodir_buddypress_style' );
	}
}

/**
 * This function is used to create GeoDirectory buddypress navigation.
 *
 * @since 1.0.0
 * @package GeoDirectory_BuddyPress_Integration
 *
 * @param array $tabs GeoDirectory Settings Tab list.
 * @return array Modified GeoDirectory Settings Tab list.
 */
function geodir_buddypress_tabs_array( $tabs ) {
	$gd_buddypress_tabs = array();
	$gd_buddypress_tabs['label'] = __('BuddyPress Integration', GDBUDDYPRESS_TEXTDOMAIN);
	$gd_buddypress_tabs['subtabs'] = array(
										array(
											'subtab' => 'gdbuddypress_settings',
											'label' => __( 'GeoDirectory BuddyPress Integration Settings', GDBUDDYPRESS_TEXTDOMAIN ),
											'form_action' => admin_url( 'admin-ajax.php?action=geodir_buddypress_ajax' )
										)
									);
	
	// hook for geodirteory buddypress tabs
	$gd_buddypress_tabs = apply_filters( 'geodir_buddypress_tabs', $gd_buddypress_tabs );
	
	$tabs['geodir_buddypress'] = $gd_buddypress_tabs;
	
	return $tabs;
}

/**
 * BuddyPress Integration setting options.
 *
 * @since 1.0.0
 * @package GeoDirectory_BuddyPress_Integration
 *
 * @param array $options GeoDirectory settings options array.
 * @return array Modified GeoDirectory settings options array.
 */
function geodir_buddypress_settings( $options = array() ) {
	$options[] = array('name' => __( 'GeoDirectory BuddyPress Integration Settings', GDBUDDYPRESS_TEXTDOMAIN ), 'type' => 'no_tabs', 'desc' => '', 'id' => 'gdbuddypress_subtab_settings' );
	
	// BuddyPress member dashboard tabs settings
	$options[] = array( 'name' => __( 'BuddyPress Member Dashboard Tab Settings', GDBUDDYPRESS_TEXTDOMAIN ), 'type' => 'sectionstart', 'id' => 'gdbuddypress_settings_tabs' );
	$options[] = array(  
					'id' => 'geodir_buddypress_tab_listing',
					'name' => __( 'Show listings in BuddyPress dashboard', GDBUDDYPRESS_TEXTDOMAIN ),
					'desc' => __( 'Choose the post types to show listing type tab under listings tab in BuddyPress dashboard', GDBUDDYPRESS_TEXTDOMAIN ),
					'tip' => '',
					'css' => 'min-width:300px;',
					'std' => array(),
					'type' => 'multiselect',
					'placeholder_text' => __( 'Select post types', GDBUDDYPRESS_TEXTDOMAIN ),
					'class'	=> 'chosen_select',
					'options' => geodir_buddypress_posttypes()
				);
	$options[] = array(  
					'id' => 'geodir_buddypress_tab_review',
					'name' => __( 'Show reviews in BuddyPress dashboard', GDBUDDYPRESS_TEXTDOMAIN ),
					'desc' => __( 'Choose the post types to show listing type tab under reviews tab in BuddyPress dashboard', GDBUDDYPRESS_TEXTDOMAIN ),
					'tip' => '',
					'css' => 'min-width:300px;',
					'std' => array(),
					'type' => 'multiselect',
					'placeholder_text' => __( 'Select post types', GDBUDDYPRESS_TEXTDOMAIN ),
					'class'	=> 'chosen_select',
					'options' => geodir_buddypress_posttypes()
				);
	$options[] = array(  
					'name' => __( 'Use BuddyPress registration form', GDBUDDYPRESS_TEXTDOMAIN ),
					'desc' => __( 'If this option is selected, the GD registration form will points to the BuddyPress registration from.', GDBUDDYPRESS_TEXTDOMAIN ),
					'id' => 'geodir_buddypress_bp_register',
					'type' => 'checkbox',
					'css' => '',
					'std' => ''
				);
	$options[] = array(  
					'name' => __( 'Redirect GD dashboard my listing link to BuddyPress profile', GDBUDDYPRESS_TEXTDOMAIN ),
					'desc' => __( 'If this option is selected, the my listing link from GD dashboard will redirect to listings tab of BuddyPress profile.', GDBUDDYPRESS_TEXTDOMAIN ),
					'id' => 'geodir_buddypress_link_listing',
					'type' => 'checkbox',
					'css' => '',
					'std' => ''
				);
	$options[] = array(  
					'name' => __( 'Redirect GD dashboard favorite link to BuddyPress profile', GDBUDDYPRESS_TEXTDOMAIN ),
					'desc' => __( 'If this option is selected, the favorite link from GD dashboard will redirect to favorites tab of BuddyPress profile.', GDBUDDYPRESS_TEXTDOMAIN ),
					'id' => 'geodir_buddypress_link_favorite',
					'type' => 'checkbox',
					'css' => '',
					'std' => ''
				);
	$options[] = array(  
					'name' => __( 'Show feature image in activity', GDBUDDYPRESS_TEXTDOMAIN ),
					'desc' => __( 'If this option is selected, the feature image displayed in activity for new listing submitted.', GDBUDDYPRESS_TEXTDOMAIN ),
					'id' => 'geodir_buddypress_show_feature_image',
					'type' => 'checkbox',
					'css' => '',
					'std' => ''
				);
	$options[] = array( 'type' => 'sectionend', 'id' => 'gdbuddypress_settings_tabs' );
		
	// post type activity settings
	$options[] = array( 'name' => __( 'Post Type Activity Settings', GDBUDDYPRESS_TEXTDOMAIN ), 'type' => 'sectionstart', 'id' => 'gdbuddypress_settings_activity' );
	$options[] = array(  
					'id' => 'geodir_buddypress_activity_listing',
					'name' => __( 'Track new listing activity in BuddyPress', GDBUDDYPRESS_TEXTDOMAIN ),
					'desc' => __( 'Choose the post types to track new listing submission in BuddyPress activity', GDBUDDYPRESS_TEXTDOMAIN ),
					'tip' => '',
					'css' => 'min-width:300px;',
					'std' => array(),
					'type' => 'multiselect',
					'placeholder_text' => __( 'Select post types', GDBUDDYPRESS_TEXTDOMAIN ),
					'class'	=> 'chosen_select',
					'options' => geodir_buddypress_posttypes()
				);
	$options[] = array(  
					'id' => 'geodir_buddypress_activity_review',
					'name' => __( 'Track new review activity in BuddyPress', GDBUDDYPRESS_TEXTDOMAIN ),
					'desc' => __( 'Choose the post types to track new review submission in BuddyPress activity', GDBUDDYPRESS_TEXTDOMAIN ),
					'tip' => '',
					'css' => 'min-width:300px;',
					'std' => array(),
					'type' => 'multiselect',
					'placeholder_text' => __( 'Select post types', GDBUDDYPRESS_TEXTDOMAIN ),
					'class'	=> 'chosen_select',
					'options' => geodir_buddypress_posttypes()
				);
	$options[] = array( 'type' => 'sectionend', 'id' => 'gdbuddypress_settings_activity' );
	
	// login redirect
	$options[] = array('name' => __( 'Login Redirect Settings', GDBUDDYPRESS_TEXTDOMAIN ), 'type' => 'sectionstart', 'id' => 'gdbuddypress_redirect_tabs' );
	
	$options[] = array(  
		'name' => __( 'Login redirection page:', GDBUDDYPRESS_TEXTDOMAIN ),
		'desc' => __( 'Default', GDBUDDYPRESS_TEXTDOMAIN ),
		'id' => 'gdbuddypress_login_redirect',
		'std' => '0',
		'value' => '0',
		'type' => 'radio',
		'radiogroup' => 'start'
	);
	$options[] = array(  
		'name' => '',
		'desc' => __( 'Home Page', GDBUDDYPRESS_TEXTDOMAIN ),
		'id' => 'gdbuddypress_login_redirect',
		'std' => '0',
		'value' => '1',
		'type' => 'radio',
		'radiogroup'	=> ''
	);
	$options[] = array(  
		'name' => '',
		'desc' => __( 'Profile Page', GDBUDDYPRESS_TEXTDOMAIN ),
		'id' => 'gdbuddypress_login_redirect',
		'std' => '0',
		'value' => '2',
		'type' => 'radio',
		'radiogroup'	=> ''
	);
	$options[] = array(  
		'name' => '',
		'desc' => __( 'Menu Page', GDBUDDYPRESS_TEXTDOMAIN ),
		'id' => 'gdbuddypress_login_redirect',
		'std' => '0',
		'value' => '3',
		'type' => 'radio',
		'radiogroup'	=> 'end'
	);
	$options[] = array(
        'name' => __('Select menu page:', GDBUDDYPRESS_TEXTDOMAIN),
        'desc' => __('Select menu page to redirect after login. "Menu Page" must be enabled for "Login redirection page".', GDBUDDYPRESS_TEXTDOMAIN),
        'id' => 'gdbuddypress_menu_redirect',
        'type' => 'single_select_page',
        'class' => 'chosen_select'
    );
	$options[] = array( 'type' => 'sectionend', 'id' => 'gdbuddypress_redirect_tabs' );
		
	// hook for custom map general options
	$options = apply_filters( 'geodir_buddypress_settings', $options );
	
	return $options;
}

/**
 * Get current buddypress integration page sub tab.
 *
 * @since 1.0.0
 * @package GeoDirectory_BuddyPress_Integration
 *
 * @param string $default The default value to return when subtab empty.
 * @return string Sub tab name.
 */
function geodir_buddypress_current_subtab( $default = '' ) {
	$subtab = isset( $_REQUEST['subtab'] ) ? $_REQUEST['subtab'] : '';
	
	if ($subtab=='' && $default!='') {
		$subtab = $default;
	}
	
	return $subtab;
}

/**
 * Adds buddypress integration settings page content.
 *
 * @since 1.0.0
 * @package GeoDirectory_BuddyPress_Integration
 *
 * @global object $wpdb WordPress Database object.
 */
function geodir_buddypress_tab_content() {
	global $wpdb;
	
	$subtab = geodir_buddypress_current_subtab();
	
	if ( $subtab == 'gdbuddypress_settings' ) {	
		add_action( 'geodir_admin_option_form', 'geodir_buddypress_option_form' );
	}
}

/**
 * Adds buddypress integration settings page setting form.
 *
 * @since 1.0.0
 * @package GeoDirectory_BuddyPress_Integration
 *
 * @param string $current_tab Current Tab name.
 */
function geodir_buddypress_option_form ( $current_tab ) {
	$current_tab = geodir_buddypress_current_subtab();
	
	geodir_buddypress_get_option_form( $current_tab );
}

/**
 * main ajax function.
 *
 * @since 1.0.0
 * @package GeoDirectory_BuddyPress_Integration
 */
function geodir_buddypress_ajax() {
	$subtab = geodir_buddypress_current_subtab();
	
	if ( $subtab == 'gdbuddypress_settings' ) {
		geodir_update_options( geodir_buddypress_settings() );
		
		$msg = urlencode_deep( __( 'Settings saved.', GDBUDDYPRESS_TEXTDOMAIN ) );
		
		wp_redirect( admin_url() . 'admin.php?page=geodirectory&tab=geodir_buddypress&subtab=gdbuddypress_settings&success_msg=' . $msg );
		exit;
	}
}

/**
 * Initialize buddypress integration plugin.
 *
 * @since 1.0.0
 * @package GeoDirectory_BuddyPress_Integration
 */
function geodir_buddypress_init() {
	do_action( 'geodir_buddypress_init' );
}

/**
 * buddypress my listing link.
 *
 * @since 1.0.0
 * @package GeoDirectory_BuddyPress_Integration
 *
 * @param string $link My listing link.
 * @param string $post_type post_type of the listing.
 * @param string|int $user_id User ID.
 * @return string Modified Listing link.
 */
function geodir_buddypress_link_my_listing( $link, $post_type = '', $user_id = '' ) {
	if ( get_option( 'geodir_buddypress_link_listing' ) ) {
		$gd_post_types = geodir_get_posttypes( 'array' );
		
		$listing_post_types = get_option( 'geodir_buddypress_tab_listing' );
		$user_id = (int)$user_id ? $user_id : '';
		if ( !$user_id && is_user_logged_in() ) {
			$user_id = bp_loggedin_user_id();
		}
		
		$user_domain = bp_core_get_user_domain( $user_id );
		
		if ( $post_type != '' && !empty( $gd_post_types ) && array_key_exists( $post_type, $gd_post_types ) && !empty( $listing_post_types ) && in_array( $post_type, $listing_post_types ) && $user_domain ) {
			$parent_slug = 'listings';
			$post_type_slug = $gd_post_types[$post_type]['has_archive'];
			
			$listing_link = trailingslashit( $user_domain . $parent_slug . '/' . $post_type_slug );
			
			$link = $listing_link;
		}
	}
	
	return $link;
}

/**
 * buddypress author link.
 *
 * @since 1.0.0
 * @package GeoDirectory_BuddyPress_Integration
 *
 * @param string $link Author page link.
 * @param string|int $user_id User ID.
 * @param string $post_type post_type of the listing.
 * @param bool $force_type Todo: Explain this.
 * @param bool $force_logged Todo: Explain this.
 * @return string Modified link.
 */
function geodir_buddypress_author_link( $link, $user_id = '', $post_type = '', $force_type = false, $force_logged = false ) {
	if ( get_option( 'geodir_buddypress_link_listing' ) ) {
		
		$user_id = (int)$user_id ? $user_id : '';
		if ( !$user_id && $force_logged && is_user_logged_in() ) {
			$user_id = bp_loggedin_user_id();
		}
		
		if ( $user_id && $user_domain = bp_core_get_user_domain( $user_id ) ) {
			$link = trailingslashit( $user_domain );
		}
		
		$gd_post_types = geodir_get_posttypes( 'array' );
		$listing_post_types = get_option( 'geodir_buddypress_tab_listing' );
		
		if ( $force_type && $post_type != '' && !empty( $gd_post_types ) && array_key_exists( $post_type, $gd_post_types ) && !empty( $listing_post_types ) && in_array( $post_type, $listing_post_types ) && $user_domain ) {
			$parent_slug = 'listings';
			$post_type_slug = $gd_post_types[$post_type]['has_archive'];
			
			$link = trailingslashit( $user_domain . $parent_slug . '/' . $post_type_slug );
		}
	}
	
	return $link;
}

/**
 * buddypress favorite listing link.
 *
 * @since 1.0.0
 * @package GeoDirectory_BuddyPress_Integration
 *
 * @param string $link Favorite listing link.
 * @param string $post_type post_type of the listing.
 * @param string|int $user_id User ID.
 * @return string Modified link.
 */
function geodir_buddypress_link_favorite_listing( $link, $post_type = '', $user_id = '' ) {
	if ( get_option( 'geodir_buddypress_link_favorite' ) ) {
		$gd_post_types = geodir_get_posttypes( 'array' );
		
		$listing_post_types = get_option( 'geodir_buddypress_tab_listing' );
		$user_id = (int)$user_id ? $user_id : '';
		if ( !$user_id && is_user_logged_in() ) {
			$user_id = bp_loggedin_user_id();
		}
		
		$user_domain = bp_core_get_user_domain( $user_id );
		
		if ( $post_type != '' && !empty( $gd_post_types ) && array_key_exists( $post_type, $gd_post_types ) && !empty( $listing_post_types ) && in_array( $post_type, $listing_post_types ) && $user_domain ) {
			$parent_slug = 'favorites';
			$post_type_slug = $gd_post_types[$post_type]['has_archive'];
			
			$listing_link = trailingslashit( $user_domain . $parent_slug . '/' . $post_type_slug );
			
			$link = $listing_link;
		}
	}
	
	return $link;
}

/**
 * get all gd post types.
 *
 * @since 1.0.0
 * @package GeoDirectory_BuddyPress_Integration
 *
 * @return array Post type array.
 */
function geodir_buddypress_posttypes() {
	$post_type_arr = array();
	$post_types = geodir_get_posttypes( 'object' );
	
	foreach ( $post_types as $key => $post_types_obj ) {
		$post_type_arr[$key] = $post_types_obj->labels->singular_name;
	}
	return $post_type_arr;
}

/**
 * Setup navigation.
 *
 * @since 1.0.0
 * @package GeoDirectory_BuddyPress_Integration
 */
function geodir_buddypress_setup_nav() {
	$gd_post_types = geodir_get_posttypes( 'array' );
	
	if ( empty( $gd_post_types ) ) {
		return;
	}
	
	global $bp;
	$user_domain = geodir_buddypress_get_user_domain();

	// listings
	$listing_post_types = get_option( 'geodir_buddypress_tab_listing' );
	
	$position = 70;
	if ( !empty( $listing_post_types ) ) {
		$parent_slug = 'listings';
		$parent_url = trailingslashit( $user_domain . $parent_slug );
		
		$parent_nav = array();
		$sub_nav = array();
		$count = 0;
		$total_count = 0;
		foreach ( $listing_post_types as $post_type ) {
			if ( array_key_exists( $post_type, $gd_post_types ) ) {
				$tab_slug = $gd_post_types[$post_type]['has_archive'];
				
				if ( $count == 0 ) {
					// parent nav
					$parent_nav = array(
						'name' => __( 'Listings', GDBUDDYPRESS_TEXTDOMAIN ),
						'slug' => $parent_slug,
						'parent_slug' => $bp->profile->slug,
						'screen_function' => 'geodir_buddypress_screen_' . $parent_slug,
						'default_subnav_slug' => $tab_slug,
						'position' => $position,
						'item_css_id' => 'gdbuddypress-nav-' . $parent_slug
					);
				}
				
				// get listing count
				$listing_count    = geodir_buddypress_count_total( $post_type );
				$class    = ( 0 === $listing_count ) ? 'no-count' : 'count';
				$total_count += $listing_count;
				
				// sub nav
				$sub_nav[] = array(
					'name' => wp_sprintf( __( '%s <span class="%s">%s</span>', GDBUDDYPRESS_TEXTDOMAIN ), __( $gd_post_types[$post_type]['labels']['name'], GDBUDDYPRESS_TEXTDOMAIN ), esc_attr( $class ), number_format_i18n( $listing_count ) ),
					'slug' => $tab_slug,
					'parent_url' => $parent_url,
					'parent_slug' => $parent_slug,
					'screen_function' => 'geodir_buddypress_screen_' . $parent_slug,
					'position' => $position,
					'item_css_id' => 'gdbuddypress-nav-' . $parent_slug . '-' . $tab_slug
				);
				
				$count++;
			}
		}
		
		if ( !empty( $parent_nav ) ) {
			$class    = ( 0 === $total_count ) ? 'no-count' : 'count';
			$parent_nav['name'] = wp_sprintf( __( 'Listings <span class="%s">%s</span>', GDBUDDYPRESS_TEXTDOMAIN ), esc_attr( $class ), number_format_i18n( $total_count ) );
		}
		
		if ( !empty( $parent_nav ) && !empty( $sub_nav ) ) {
			$parent_nav = apply_filters( 'geodir_buddypress_nav_' . $parent_slug, $parent_nav );
			bp_core_new_nav_item( $parent_nav );
			
			$sub_nav = apply_filters( 'geodir_buddypress_subnav_' . $parent_slug, $sub_nav );
			// Sub nav items are not required
			if ( !empty( $sub_nav ) ) {
				foreach( $sub_nav as $nav ) {
					bp_core_new_subnav_item( $nav );
				}
			}
		}
	}
		
	// favorites
	$listing_post_types = get_option( 'geodir_buddypress_tab_listing' );
	if ( !empty( $listing_post_types ) ) {
		$parent_slug = 'favorites';
		$parent_url = trailingslashit( $user_domain . $parent_slug );
		
		$parent_nav = array();
		$sub_nav = array();
		$count = 0;
		$total_count = 0;
		foreach ( $listing_post_types as $post_type ) {
			if ( array_key_exists( $post_type, $gd_post_types ) ) {
				$tab_slug = $gd_post_types[$post_type]['has_archive'];
				
				if ( $count == 0 ) {
                    $fav_name = __( 'Favorites', GDBUDDYPRESS_TEXTDOMAIN );
                    $favourite_text = apply_filters('gdbuddypress_favourites_text', $fav_name);
					// parent nav
					$parent_nav = array(
						'name' => $favourite_text,
						'slug' => $parent_slug,
						'parent_slug' => $bp->profile->slug,
						'screen_function' => 'geodir_buddypress_screen_' . $parent_slug,
						'default_subnav_slug' => $tab_slug,
						'position' => $position,
						'item_css_id' => 'gdbuddypress-nav-' . $parent_slug
					);
				}
				
				$position = $position + 5;
				
				// get listing count
				$listing_count    = geodir_buddypress_count_favorite( $post_type );
				$class    = ( 0 === $listing_count ) ? 'no-count' : 'count';
				$total_count += $listing_count;
				
				// sub nav
				$sub_nav[] = array(
					'name' => wp_sprintf( __( '%s <span class="%s">%s</span>', GDBUDDYPRESS_TEXTDOMAIN ), __( $gd_post_types[$post_type]['labels']['name'], GDBUDDYPRESS_TEXTDOMAIN ), esc_attr( $class ), number_format_i18n( $listing_count ) ),
					'slug' => $tab_slug,
					'parent_url' => $parent_url,
					'parent_slug' => $parent_slug,
					'screen_function' => 'geodir_buddypress_screen_' . $parent_slug,
					'position' => $position,
					'item_css_id' => 'gdbuddypress-nav-' . $parent_slug . '-' . $tab_slug
				);
				
				$count++;
			}
		}
		
		if ( !empty( $parent_nav ) ) {
			$class    = ( 0 === $total_count ) ? 'no-count' : 'count';
            $fav_name = __( 'Favorites', GDBUDDYPRESS_TEXTDOMAIN );
            $favourite_text = apply_filters('gdbuddypress_favourites_text', $fav_name);
			$parent_nav['name'] = wp_sprintf( __( '%s <span class="%s">%s</span>', GDBUDDYPRESS_TEXTDOMAIN ), $favourite_text, esc_attr( $class ), number_format_i18n( $total_count ) );
		}
		
		if ( !empty( $parent_nav ) && !empty( $sub_nav ) ) {
			$parent_nav = apply_filters( 'geodir_buddypress_nav_' . $parent_slug, $parent_nav );
			bp_core_new_nav_item( $parent_nav );
			
			$sub_nav = apply_filters( 'geodir_buddypress_subnav_' . $parent_slug, $sub_nav );
			// Sub nav items are not required
			if ( !empty( $sub_nav ) ) {
				foreach( $sub_nav as $nav ) {
					bp_core_new_subnav_item( $nav );
				}
			}
		}
	}
	
	// reviews
	$review_post_types = get_option( 'geodir_buddypress_tab_review' );
	if ( !empty( $review_post_types ) ) {
		$parent_slug = 'reviews';
		$parent_url = trailingslashit( $user_domain . $parent_slug );
		
		$parent_nav = array();
		$sub_nav = array();
		$count = 0;
		$total_count = 0;
		foreach ( $review_post_types as $post_type ) {
			if ( array_key_exists( $post_type, $gd_post_types ) ) {
				$tab_slug = $gd_post_types[$post_type]['has_archive'];
				
				if ( $count == 0 ) {
					// parent nav
					$parent_nav = array(
						'name' => __( 'Reviews', GDBUDDYPRESS_TEXTDOMAIN ),
						'slug' => $parent_slug,
						'parent_slug' => $bp->profile->slug,
						'screen_function' => 'geodir_buddypress_screen_' . $parent_slug,
						'default_subnav_slug' => $tab_slug,
						'position' => $position,
						'item_css_id' => 'gdbuddypress-nav-' . $parent_slug
					);
				}
				
				$position = $position + 5;
				
				// get review count
				$review_count    = geodir_buddypress_count_reviews( $post_type );
				$class    = ( 0 === $review_count ) ? 'no-count' : 'count';
				$total_count += $review_count;
				
				// sub nav
				$sub_nav[] = array(
					'name' => wp_sprintf( __( '%s <span class="%s">%s</span>', GDBUDDYPRESS_TEXTDOMAIN ), __( $gd_post_types[$post_type]['labels']['name'], GDBUDDYPRESS_TEXTDOMAIN ), esc_attr( $class ), number_format_i18n( $review_count ) ),
					'slug' => $tab_slug,
					'parent_url' => $parent_url,
					'parent_slug' => $parent_slug,
					'screen_function' => 'geodir_buddypress_screen_' . $parent_slug,
					'position' => $position,
					'item_css_id' => 'gdbuddypress-nav-' . $parent_slug . '-' . $tab_slug
				);
				
				$count++;
			}
		}
		
		if ( !empty( $parent_nav ) ) {
			$class    = ( 0 === $total_count ) ? 'no-count' : 'count';
			$parent_nav['name'] = wp_sprintf( __( 'Reviews <span class="%s">%s</span>', GDBUDDYPRESS_TEXTDOMAIN ), esc_attr( $class ), number_format_i18n( $total_count ) );
		}
		
		if ( !empty( $parent_nav ) && !empty( $sub_nav ) ) {
			$parent_nav = apply_filters( 'geodir_buddypress_nav_' . $parent_slug, $parent_nav );
			bp_core_new_nav_item( $parent_nav );
			
			$sub_nav = apply_filters( 'geodir_buddypress_subnav_' . $parent_slug, $sub_nav );
			// Sub nav items are not required
			if ( !empty( $sub_nav ) ) {
				foreach( $sub_nav as $nav ) {
					bp_core_new_subnav_item( $nav );
				}
			}
		}
	}
}

/**
 * Add listing tabs to buddypress profile page.
 *
 * @since 1.0.0
 * @package GeoDirectory_BuddyPress_Integration
 */
function geodir_buddypress_screen_listings() {
	$gd_post_types = geodir_get_posttypes( 'array' );
	$post_type = geodir_buddypress_action_post_type();
	$listing_post_types = get_option( 'geodir_buddypress_tab_listing' );

	if ( !empty( $gd_post_types ) && !empty( $post_type ) && !empty( $listing_post_types ) && array_key_exists( $post_type, $gd_post_types ) && in_array( $post_type, $listing_post_types ) ) {
		add_action( 'bp_template_title', 'geodir_buddypress_listings_title' );
		add_action( 'bp_template_content', 'geodir_buddypress_listings_content' );
	
		$template = apply_filters( 'bp_core_template_plugin', 'members/single/plugins' );
		
		bp_core_load_template( apply_filters( 'geodir_buddypress_bp_core_template_plugin', $template ) );
	}
}

/**
 * Add review tabs to buddypress profile page.
 *
 * @since 1.0.0
 * @package GeoDirectory_BuddyPress_Integration
 */
function geodir_buddypress_screen_reviews() {
	$gd_post_types = geodir_get_posttypes( 'array' );
	$post_type = geodir_buddypress_action_post_type();
	$review_post_types = get_option( 'geodir_buddypress_tab_review' );
	
	if ( !empty( $gd_post_types ) && !empty( $post_type ) && !empty( $review_post_types ) && array_key_exists( $post_type, $gd_post_types ) && in_array( $post_type, $review_post_types ) ) {
		add_action( 'bp_template_title', 'geodir_buddypress_reviews_title' );
		add_action( 'bp_template_content', 'geodir_buddypress_reviews_content' );
	
		$template = apply_filters( 'bp_core_template_plugin', 'members/single/plugins' );
		
		bp_core_load_template( apply_filters( 'geodir_buddypress_bp_core_template_reviews_plugin', $template ) );
	}
}

/**
 * Adds listings on member profile favorites tab.
 *
 * @since 1.0.0
 * @package GeoDirectory_BuddyPress_Integration
 */
function geodir_buddypress_screen_favorites() {
	geodir_buddypress_screen_listings();
}

/**
 * Add listing tab title to buddypress profile page.
 *
 * @since 1.0.0
 * @package GeoDirectory_BuddyPress_Integration
 */
function geodir_buddypress_listings_title() {
	$action = bp_current_action();
	
	$post_type_name = geodir_buddypress_post_type_name();
	
	echo apply_filters( 'geodir_buddypress_listings_before_title', '' );
	echo apply_filters( 'geodir_buddypress_listings_title', '<div class="gdbp-content-title">' . $post_type_name . '</div>' );
	echo apply_filters( 'geodir_buddypress_listings_after_title', '' );
}

/**
 * Add review tab title to buddypress profile page.
 *
 * @since 1.0.0
 * @package GeoDirectory_BuddyPress_Integration
 */
function geodir_buddypress_reviews_title() {
	$action = bp_current_action();
	
	$post_type_name = geodir_buddypress_post_type_name();
	
	echo apply_filters( 'geodir_buddypress_reviews_title_' . $action, '<div class="gdbp-content-title">' . wp_sprintf( __( 'Reviews on %s' ), $post_type_name ) . '</div>' );
}

/**
 * Add listing tabs content to buddypress profile page.
 *
 * @since 1.0.0
 * @package GeoDirectory_BuddyPress_Integration
 */
function geodir_buddypress_listings_content() {
	$action = bp_current_action();
	$post_type = geodir_buddypress_action_post_type();
	$gd_post_types = geodir_get_posttypes( 'array' );
	
	$post_type_name = geodir_buddypress_post_type_name();
	
	$args = array();
	$args['post_type'] = $post_type;
	$args['post_type_name'] = $post_type_name;
	
	$content = geodir_buddypress_listings_html( $args );
	
	echo apply_filters( 'geodir_buddypress_listings_before_content', '' );
	echo apply_filters( 'geodir_buddypress_listings_content', $content );
	echo apply_filters( 'geodir_buddypress_listings_after_content', '' );
}

/**
 * Add review tabs content to buddypress profile page.
 *
 * @since 1.0.0
 * @package GeoDirectory_BuddyPress_Integration
 */
function geodir_buddypress_reviews_content() {
	$action = bp_current_action();
	$post_type = geodir_buddypress_action_post_type();
	$gd_post_types = geodir_get_posttypes( 'array' );
	
	$post_type_name = geodir_buddypress_post_type_name();
	
	$args = array();
	$args['post_type'] = $post_type;
	$args['post_type_name'] = $post_type_name;
	
	$content = geodir_buddypress_reviews_html( $args );
	
	echo apply_filters( 'geodir_buddypress_reviews_before_content', '' );
	echo apply_filters( 'geodir_buddypress_reviews_content', $content );
	echo apply_filters( 'geodir_buddypress_reviews_after_content', '' );
}

/**
 * Checks post type has archive and returns the post type if true.
 *
 * @since 1.0.0
 * @package GeoDirectory_BuddyPress_Integration
 *
 * @return int|string
 */
function geodir_buddypress_action_post_type() {
	$action = bp_current_action();
	$gd_post_types = geodir_get_posttypes( 'array' );

	$post_type = '';
	foreach ( $gd_post_types as $gd_post_type => $post_info ) {
		if ( $post_info['has_archive'] == $action ) {
			$post_type = $gd_post_type;
			break;
		}
	}
	
	return $post_type;
}

/**
 * Check post type has archive or not.
 *
 * @since 1.0.0
 * @package GeoDirectory_BuddyPress_Integration
 *
 * @param string $post_type post_type of the listing.
 * @return null|string
 */
function geodir_buddypress_post_type_archive( $post_type ) {
	$gd_post_types = geodir_get_posttypes( 'array' );
	
	$has_archive = $post_type != '' && isset( $gd_post_types[$post_type]['has_archive'] ) ?  $gd_post_types[$post_type]['has_archive'] : NULL;
	
	return $has_archive;
}

/**
 * Get post type name to display in member profile listings Tab.
 *
 * @since 1.0.0
 * @package GeoDirectory_BuddyPress_Integration
 *
 * @param string $post_type post_type of the listing.
 * @return string|void
 */
function geodir_buddypress_post_type_name( $post_type = '' ) {
	$action_post_type = geodir_buddypress_action_post_type();
	$post_type = $post_type != '' ? $post_type : $action_post_type;
	$gd_post_types = geodir_get_posttypes( 'array' );
	
	$return = !empty( $gd_post_types ) && isset( $gd_post_types[$post_type]['labels']['name'] ) ? __( $gd_post_types[$post_type]['labels']['name'], GDBUDDYPRESS_TEXTDOMAIN ) : '';
	
	return $return;
}

/**
 * Get user profile link.
 *
 * @since 1.0.0
 * @package GeoDirectory_BuddyPress_Integration
 *
 * @return string|void
 */
function geodir_buddypress_get_user_domain() {
	// Stop if there is no user displayed or logged in
	if ( !is_user_logged_in() && !bp_displayed_user_id() )
		return;
	
	// Determine user to use
	if ( bp_displayed_user_domain() ) {
		$user_domain = bp_displayed_user_domain();
	} elseif ( bp_loggedin_user_domain() ) {
		$user_domain = bp_loggedin_user_domain();
	} else {
		return;
	}
	
	return $user_domain;
}

/**
 * register activity post type.
 *
 * @since 1.0.0
 * @package GeoDirectory_BuddyPress_Integration
 *
 * @param array $post_types post_types of the listing.
 * @return array Post types array.
 */
function geodir_buddypress_record_geodir_post_types( $post_types = array() ) {
	$post_types = is_array( $post_types ) && !empty( $post_types ) ? $post_types : array();
	
	$listing_post_types = get_option( 'geodir_buddypress_activity_listing' );
	if ( !empty( $listing_post_types ) ) {
		$gd_post_types = geodir_get_posttypes( 'array' );
		
		foreach ( $listing_post_types as $post_type ) {
			if ( array_key_exists( $post_type, $gd_post_types ) ) {
				$post_types[] = $post_type;
			}
		}
	}
	
	return $post_types;
}

/**
 * register activity comment post type.
 *
 * @since 1.0.0
 * @package GeoDirectory_BuddyPress_Integration
 *
 * @param array $post_types post_types of the listing.
 * @return array Post types array.
 */
function geodir_buddypress_record_comment_post_types( $post_types = array() ) {
	$post_types = is_array( $post_types ) && !empty( $post_types ) ? $post_types : array();
	
	$listing_post_types = get_option( 'geodir_buddypress_activity_review' );
	if ( !empty( $listing_post_types ) ) {
		$gd_post_types = geodir_get_posttypes( 'array' );
		
		foreach ( $listing_post_types as $post_type ) {
			if ( array_key_exists( $post_type, $gd_post_types ) ) {
				$post_types[] = $post_type;
			}
		}
	}
	
	return $post_types;
}

/**
 * action for listing post type.
 *
 * @since 1.0.0
 * @package GeoDirectory_BuddyPress_Integration
 *
 * @param string $action BuddyPress Constructed activity action.
 * @param object $activity BuddyPress Activity data object.
 * @return mixed|void Modified Action.
 */
function geodir_buddypress_new_listing_activity( $action, $activity ) {
    global $post;
	$posy_info = NULL;
	switch_to_blog( $activity->item_id );
	$posy_info = get_post( $activity->secondary_item_id );
	$post_type = !empty( $posy_info ) ? $posy_info->post_type : '';
	restore_current_blog();
	
	$gd_post_types = geodir_get_posttypes( 'array' );
	
	if ( !empty( $post_type ) && array_key_exists( $post_type, $gd_post_types ) ) {
		$blog_url  = bp_blogs_get_blogmeta( $activity->item_id, 'url' );
		$blog_name = bp_blogs_get_blogmeta( $activity->item_id, 'name' );
	
		if ( empty( $blog_url ) || empty( $blog_name ) ) {
			$blog_url  = get_home_url( $activity->item_id );
			$blog_name = get_blog_option( $activity->item_id, 'blogname' );
	
			bp_blogs_update_blogmeta( $activity->item_id, 'url', $blog_url );
			bp_blogs_update_blogmeta( $activity->item_id, 'name', $blog_name );
		}
	
		$post_url = esc_url( add_query_arg( 'p', $activity->secondary_item_id, trailingslashit( $blog_url )) );
	
		$post_title = isset($activity->id) ? bp_activity_get_meta( $activity->id, 'post_title' ) : "";
	
		// Should only be empty at the time of post creation
		if ( empty( $post_title ) ) {
			if ( is_a( $posy_info, 'WP_Post' ) ) {
				$post_title = $posy_info->post_title;
                if ( ! empty( $activity->id ) ) {bp_activity_update_meta( $activity->id, 'post_title', $post_title );}
			}
		}
	
		$post_link  = '<a href="' . $post_url . '">' . $post_title . '</a>';
	
		$user_link = bp_core_get_userlink( $activity->user_id );
		
		$post_type_name = __( strtolower( $gd_post_types[$post_type]['labels']['singular_name'] ), GDBUDDYPRESS_TEXTDOMAIN );
	
		if ( is_multisite() ) {
			$action  = sprintf( __( '%1$s listed a new %2$s, %3$s, on the site %4$s', GDBUDDYPRESS_TEXTDOMAIN ), $user_link, $post_type_name, $post_link, '<a href="' . esc_url( $blog_url ) . '">' . esc_html( $blog_name ) . '</a>' );
		} else {
			$action  = sprintf( __( '%1$s listed a new %2$s, %3$s', GDBUDDYPRESS_TEXTDOMAIN ), $user_link, $post_type_name, $post_link );
		}
	}

	return apply_filters( 'geodir_buddypress_new_listing_activity', $action, $activity, $post_type );
}

/**
 * action for listing post type comment.
 *
 * @since 1.0.0
 * @package GeoDirectory_BuddyPress_Integration
 *
 * @param string $action BuddyPress Constructed activity action.
 * @param object $activity BuddyPress Activity data object.
 * @return mixed|void Modified Action.
 */
function geodir_buddypress_new_listing_comment_activity( $action, $activity ) {
	$posy_info = NULL;
	switch_to_blog( $activity->item_id );
	$comment = get_comment( $activity->secondary_item_id );
	$posy_info = !empty( $comment->comment_post_ID ) ? get_post( $comment->comment_post_ID ) : NULL;
	$post_type = !empty( $posy_info ) ? $posy_info->post_type : '';
	restore_current_blog();
	
	$gd_post_types = geodir_get_posttypes( 'array' );
	
	if ( !empty( $post_type ) && array_key_exists( $post_type, $gd_post_types ) ) {
		$blog_url  = bp_blogs_get_blogmeta( $activity->item_id, 'url' );
		$blog_name = bp_blogs_get_blogmeta( $activity->item_id, 'name' );
	
		if ( empty( $blog_url ) || empty( $blog_name ) ) {
			$blog_url  = get_home_url( $activity->item_id );
			$blog_name = get_blog_option( $activity->item_id, 'blogname' );
	
			bp_blogs_update_blogmeta( $activity->item_id, 'url', $blog_url );
			bp_blogs_update_blogmeta( $activity->item_id, 'name', $blog_name );
		}
	
		$post_url   = bp_activity_get_meta( $activity->id, 'post_url' );
		$post_title = bp_activity_get_meta( $activity->id, 'post_title' );
	
		// Should only be empty at the time of post creation
		if ( empty( $post_url ) || empty( $post_title ) ) {		
			if ( ! empty( $comment->comment_post_ID ) ) {
				$post_url = esc_url( add_query_arg( 'p', $comment->comment_post_ID, trailingslashit( $blog_url ) ));
				bp_activity_update_meta( $activity->id, 'post_url', $post_url );
	
				if ( is_a( $posy_info, 'WP_Post' ) ) {
					$post_title = $post->post_title;
					bp_activity_update_meta( $activity->id, 'post_title', $post_title );
				}
			}
		}
	
		$post_link = '<a href="' . $post_url . '">' . $post_title . '</a>';
		$user_link = bp_core_get_userlink( $activity->user_id );
		
		$post_type_name = __( strtolower( $gd_post_types[$post_type]['labels']['singular_name'] ), GDBUDDYPRESS_TEXTDOMAIN );
	
		if ( is_multisite() ) {
			$action  = sprintf( __( '%1$s commented on the %2$s, %3$s, on the site %4$s', GDBUDDYPRESS_TEXTDOMAIN ), $user_link, $post_type_name, $post_link, '<a href="' . esc_url( $blog_url ) . '">' . esc_html( $blog_name ) . '</a>' );
		} else {
			$action  = sprintf( __( '%1$s commented on the %2$s, %3$s', GDBUDDYPRESS_TEXTDOMAIN ), $user_link, $post_type_name, $post_link );
		}
	}

	return apply_filters( 'geodir_buddypress_new_listing_comment_activity', $action, $activity, $post_type );
}

/**
 * Query listings to display on member profile.
 *
 * @since 1.0.0
 * @package GeoDirectory_BuddyPress_Integration
 *
 * @global object $wpdb WordPress Database object.
 * @global string $plugin_prefix Geodirectory plugin table prefix.
 * @global string $table_prefix WordPress Database Table prefix.
 * @global string $geodir_post_type Post type.
 * @global string $table Listing table name.
 * @global int $paged Global variable contains the page number of a listing of posts.
 *
 * @param array $query_args Query args.
 * @return int|mixed Query results.
 */
function geodir_buddypress_get_bp_listings( $query_args = array() ) {
	global $wpdb, $plugin_prefix, $table_prefix, $geodir_post_type, $table, $paged;
	$current_geodir_post_type = $geodir_post_type;
	$current_table = $table;
	
	$GLOBALS['gd_query_args_bp'] = $query_args;
	$gd_query_args_widgets = $query_args;
	
	$post_type = $query_args['post_type'];
	$geodir_post_type = $post_type;
	$table = $plugin_prefix . $post_type . '_detail';
	
	$fields = $wpdb->posts . ".*, " . $table . ".*";
	$fields = apply_filters( 'geodir_filter_bp_listings_fields', $fields, $table, $post_type );
	
	$join = "INNER JOIN " . $table ." ON (" . $table .".post_id = " . $wpdb->posts . ".ID)";
	
	if ( $post_type == 'gd_event' && defined( 'EVENT_SCHEDULE' ) ) {
		$fields .= ", " . EVENT_SCHEDULE . ".*";
		$join .= " INNER JOIN " . EVENT_SCHEDULE ." ON (" . EVENT_SCHEDULE .".event_id = " . $wpdb->posts . ".ID)";
	}
	
	########### WPML ###########
	if ( function_exists( 'icl_object_id' ) ) {
		global $sitepress;
		$lang_code = ICL_LANGUAGE_CODE;
		$default_lang_code = $sitepress->get_default_language();
		if( $lang_code ) {
			$join .= " JOIN ".$table_prefix."icl_translations icl_t ON icl_t.element_id = ".$table_prefix."posts.ID";
		}
	}
	########### WPML ###########
	
	$join = apply_filters( 'geodir_filter_bp_listings_join', $join, $post_type  );
	
	$post_status = is_super_admin() ? " OR " . $wpdb->posts . ".post_status = 'private'" : '';
	if ( bp_loggedin_user_id() && bp_displayed_user_id() == bp_loggedin_user_id() ) {
		$post_status .= " OR " . $wpdb->posts . ".post_status = 'draft' OR " . $wpdb->posts . ".post_status = 'private'";
	}
		
	$where = " AND ( " . $wpdb->posts . ".post_status = 'publish' " . $post_status . " ) AND " . $wpdb->posts . ".post_type = '" . $post_type . "'";
	
	// filter favorites
	if ( isset( $query_args['filter_favorite'] ) && $query_args['filter_favorite'] == 1 ) {	
		$user_fav_posts = get_user_meta( (int)bp_displayed_user_id(), 'gd_user_favourite_post', true );
		$user_fav_posts = !empty( $user_fav_posts ) ? implode( "','", $user_fav_posts ) : "-1";
		$where .= " AND " . $wpdb->posts . ".ID IN ('" . $user_fav_posts . "')";
	} else {
		$where .= " AND " . $wpdb->posts . ".post_author = " . (int)bp_displayed_user_id();
	}
	
	
	########### WPML ###########
	if ( function_exists( 'icl_object_id' ) ) {
		if ( $lang_code ) {
			$where .= " AND icl_t.language_code = '$lang_code' ";
		}
	}
	########### WPML ###########
	
	$where = apply_filters( 'geodir_filter_bp_listings_where', $where, $post_type );
	$where = $where != '' ? " WHERE 1=1 " . $where : '';
	
	$groupby = " GROUP BY $wpdb->posts.ID ";
	$groupby = apply_filters( 'geodir_filter_bp_listings_groupby', $groupby, $post_type );
	
	$orderby = apply_filters( 'geodir_buddypress_posts_orderby', '' );
	$orderby = apply_filters( 'geodir_filter_bp_listings_orderby', $orderby, $table, $post_type );
	$orderby = $orderby != '' ? " ORDER BY " . $orderby : '';
	
	$posts_per_page = !empty( $query_args['posts_per_page'] ) ? $query_args['posts_per_page'] : 5;
	
	// Paging
	$limit = '';
	if ( $posts_per_page > 0 ) {
		$page = absint( $paged );
		if ( !$page ) {
			$page = 1;
		}

		$pgstrt = absint( ( $page - 1 ) * $posts_per_page ) . ', ';
		$limit = " LIMIT " . $pgstrt . $posts_per_page;
	}
	$limit = apply_filters( 'geodir_filter_widget_listings_limit', $limit, $posts_per_page, $post_type );
	
	if ( isset( $query_args['count_only'] ) && !empty( $query_args['count_only'] ) ) {
		$fields = $wpdb->posts . ".ID";
		$orderby = "";
		$limit = "";
	}
	
	$sql =  "SELECT SQL_CALC_FOUND_ROWS " . $fields . " FROM " . $wpdb->posts . "
		" . $join . "
		" . $where . "
		" . $groupby . "
		" . $orderby . "
		" . $limit;
	//echo '<pre>sql : '; print_r($sql); echo '</pre>';// exit;
	
	$rows = $wpdb->get_results($sql);
	if ( isset( $query_args['count_only'] ) && !empty( $query_args['count_only'] ) ) {
		$rows = !empty( $rows ) ? count( $rows ) : 0;
	}
	
	unset( $GLOBALS['gd_query_args_bp'] );
	unset( $gd_query_args_bp );
	
	global $geodir_post_type, $table;
	$geodir_post_type = $current_geodir_post_type;
	$table = $current_table;
	
	return $rows;
}

/**
 * post status on author listing page.
 *
 * @since 1.0.0
 * @package GeoDirectory_BuddyPress_Integration
 *
 * @global object $wpdb WordPress Database object.
 * @global object $post The current post object.
 */
function geodir_buddypress_post_status_on_listing() {
	global $wpdb, $post;
		
	$html = '';
	if ( get_current_user_id() && !empty( $post ) && isset( $post->post_author ) && $post->post_author == get_current_user_id() && !geodir_is_page('author') ) {
			
		// we need to query real status direct as we dynamically change the status for author on author page so even non author status can view them.
		$real_status = $wpdb->get_var("SELECT post_status from $wpdb->posts WHERE ID=$post->ID");
		$status = "<strong>(";
		$status_icon = '<i class="fa fa-play"></i>';
		if ( $real_status == 'publish' ) {
			$status .= __( 'Published', GDBUDDYPRESS_TEXTDOMAIN );
		} else {
			$status .= __( 'Not published', GDBUDDYPRESS_TEXTDOMAIN );
			$status_icon = '<i class="fa fa-pause"></i>';
		}
		$status .= ")</strong>";
		
		$html = '<span class="geodir-post-status">'.$status_icon.' <font class="geodir-status-label">' . __( 'Status: ', GDBUDDYPRESS_TEXTDOMAIN ) . '</font>'.$status.'</span>';
	}
	echo $html;
}

/**
 * Get the listing count of a given user.
 *
 * @since 1.0.0
 * @package GeoDirectory_BuddyPress_Integration
 *
 * @global object $wpdb WordPress Database object.
 *
 * @param string $post_type post_type of the listing.
 * @param int $user_id ID of the user whose listings are being counted.
 * @return int Listing count of the user.
 */
function geodir_buddypress_count_total( $post_type, $user_id = 0 ) {
	global $wpdb;
	if ( empty( $user_id ) ) {
		$user_id = ( bp_displayed_user_id() ) ? bp_displayed_user_id() : bp_loggedin_user_id();
	}
	
	$post_status = is_super_admin() ? " OR " . $wpdb->posts . ".post_status = 'private'" : '';
	if ( $user_id && $user_id == bp_loggedin_user_id() ) {
		$post_status .= " OR " . $wpdb->posts . ".post_status = 'draft' OR " . $wpdb->posts . ".post_status = 'private'";
	}
	
	$count = (int)$wpdb->get_var( "SELECT count( ID ) FROM ".$wpdb->prefix."posts WHERE post_author=" . (int)$user_id . " AND post_type='" . $post_type . "' AND ( post_status = 'publish' " . $post_status . " )" );

	return apply_filters( 'geodir_buddypress_count_total', $count, $user_id );
}

/**
 * Get the favorite listing count of a given user.
 *
 * @since 1.0.0
 * @package GeoDirectory_BuddyPress_Integration
 *
 * @global object $wpdb WordPress Database object.
 *
 * @param string $post_type post_type of the listing.
 * @param int $user_id ID of the user whose listings are being counted.
 * @return int favorite listing count of the user.
 */
function geodir_buddypress_count_favorite( $post_type, $user_id = 0 ) {
	global $wpdb;
	if ( empty( $user_id ) ) {
		$user_id = ( bp_displayed_user_id() ) ? bp_displayed_user_id() : bp_loggedin_user_id();
	}
	
	$post_status = is_super_admin() ? " OR " . $wpdb->posts . ".post_status = 'private'" : '';
	if ( $user_id && $user_id == bp_loggedin_user_id() ) {
		$post_status .= " OR " . $wpdb->posts . ".post_status = 'draft' OR " . $wpdb->posts . ".post_status = 'private'";
	}
	
	$user_fav_posts = get_user_meta( (int)$user_id, 'gd_user_favourite_post', true );
	$user_fav_posts = !empty( $user_fav_posts ) ? implode( "','", $user_fav_posts ) : "-1";
	
	$count = (int)$wpdb->get_var( "SELECT count( ID ) FROM ".$wpdb->posts." WHERE " . $wpdb->posts . ".ID IN ('" . $user_fav_posts . "') AND post_type='" . $post_type . "' AND ( post_status = 'publish' " . $post_status . " )" );

	return apply_filters( 'geodir_buddypress_count_favorite', $count, $user_id );
}

/**
 * Get the favorite reviews count of a given user.
 *
 * @since 1.0.0
 * @package GeoDirectory_BuddyPress_Integration
 *
 * @global object $wpdb WordPress Database object.
 *
 * @param string $post_type post_type of the listing.
 * @param int $user_id ID of the user whose reviews are being counted.
 * @return int favorite listing count of the user.
 */
function geodir_buddypress_count_reviews( $post_type, $user_id = 0 ) {
	global $wpdb;
	if ( empty( $user_id ) ) {
		$user_id = ( bp_displayed_user_id() ) ? bp_displayed_user_id() : bp_loggedin_user_id();
	}
	$logged_id = bp_loggedin_user_id();
	
	$post_status = is_super_admin() ? " OR " . $wpdb->posts . ".post_status = 'private'" : '';
	if ( $user_id && $user_id == bp_loggedin_user_id() ) {
		$post_status .= " OR " . $wpdb->posts . ".post_status = 'draft' OR " . $wpdb->posts . ".post_status = 'private'";
	}
	$comment_status = " AND ( " . $wpdb->comments . ".comment_approved='1'";
	if ( $logged_id > 0 && $logged_id == $user_id ) {
		$comment_status .= " OR " . $wpdb->comments . ".comment_approved='0'";
	}
	$comment_status .= " )";
		
	$query = $wpdb->prepare( "SELECT count( " . $wpdb->comments . ".comment_post_ID ) FROM " . $wpdb->comments . " JOIN " . $wpdb->posts . " ON " . $wpdb->posts . ".ID = " . $wpdb->comments . ".comment_post_ID WHERE user_id = %d  AND " . $wpdb->posts . ".post_type = %s " . $comment_status, array( (int)$user_id, $post_type ) );
	$count = (int)$wpdb->get_var( $query );

	return apply_filters( 'geodir_buddypress_count_reviews', $count, $user_id );
}

/**
 * Redirect away from gd signup if BP registration templates are present.
 *
 * @since 1.0.0
 * @package GeoDirectory_BuddyPress_Integration
 */
function geodir_buddypress_gdsignup_redirect() {
	if ( !get_option( 'geodir_buddypress_bp_register' ) ) {
		return;
	}
	
	// Bail in admin or logged in
	if ( is_admin() || !bp_has_custom_signup_page() || is_user_logged_in() ) {
		return;
	}

	$geodir_signup = !empty( $_GET['geodir_signup'] ) ? true : false;
	$sign_up = !empty( $_GET['page1'] ) && trim( $_GET['page1'] ) == 'sign_up' ? true : false;
	
	// Not at the WP core signup page and action is not register
	if ( ( !empty( $_SERVER['SCRIPT_NAME'] ) && false !== strpos( $_SERVER['SCRIPT_NAME'], 'index.php' ) && $geodir_signup ) ) {
		// adds class to gd signup page
		add_filter( 'body_class', 'geodir_buddypress_body_class', 100 );
		add_action( 'wp_head', 'geodir_buddypress_custom_style' );
		add_action( 'login_form', 'geodir_buddypress_login_form' );
		
		if ( !$sign_up ) {
			return;
		}
	} else {
		return;
	}

	bp_core_redirect( bp_get_signup_page() );
}

/**
 * Redirect away from gd dashboard to BP registration profile page.
 *
 * @since 1.0.0
 * @package GeoDirectory_BuddyPress_Integration
 * @param string $body_class HTML body class.
 */
function geodir_buddypress_author_redirect($body_class) {
	$gd_dashboard = isset( $_REQUEST['geodir_dashbord'] ) ? true : false;
	$favourite = isset( $_REQUEST['list'] ) && $_REQUEST['list'] == 'favourite' ? true : false;
	$post_type = isset( $_REQUEST['stype'] ) ? $_REQUEST['stype'] : NULL;
	
	// gd dashboard page
	if ( $gd_dashboard && get_option( 'geodir_buddypress_link_listing' ) ) {
		$author = get_query_var( 'author_name' ) ? get_user_by( 'slug', get_query_var( 'author_name' ) ) : get_userdata( get_query_var( 'author' ) );
		
		if ( $favourite && !get_option( 'geodir_buddypress_link_favorite' ) ) {
			return;
		}
		
		if ( !empty( $author ) && isset( $author->ID ) && $author_id = $author->ID ) {
			if ( $author_id && $user_domain = bp_core_get_user_domain( $author_id ) ) {
				$author_link = trailingslashit( $user_domain );
				
				if ( $post_type != '' ) {
					$gd_post_types = geodir_get_posttypes( 'array' );
					$listing_post_types = get_option( 'geodir_buddypress_tab_listing' );
					
					if ( !empty( $gd_post_types ) && array_key_exists( $post_type, $gd_post_types ) && !empty( $listing_post_types ) && in_array( $post_type, $listing_post_types ) && $user_domain ) {
						$parent_slug = 'listings';
						$post_type_slug = $gd_post_types[$post_type]['has_archive'];
						
						$author_link = trailingslashit( $user_domain . $parent_slug . '/' . $post_type_slug );
					}
				}
				
				wp_redirect( $author_link );
				exit;
			}
		}
	}
	return;
}

/**
 * adds class to gd signup page.
 *
 * @since 1.0.0
 * @package GeoDirectory_BuddyPress_Integration
 * @param array $classes HTML classes array.
 * @return array Modified HTML classes array.
 */
function geodir_buddypress_body_class( $classes = array() ) {
	$classes[] = 'gdbp-signup';
	
	return $classes;
}

/**
 * adds style to hide registration from.
 *
 * @since 1.0.0
 * @package GeoDirectory_BuddyPress_Integration
 */
function geodir_buddypress_custom_style() {
	$custom_css = "@media only screen and (min-width: 661px){.login_form_l{margin-left:33%;width:auto}}body.gdbp-signup .registration_form_r,body.gdbp-signup .registration_form{display:none} .gdbp-hidden-reg{display:none} #gdbp-reg-link{margin-top:12px;text-align:right}";
    echo '<style type="text/css">' . $custom_css . '</style>';
	echo '<script type="text/javascript">jQuery(function(){var gdbO=jQuery(\'.login_form_l #cus_loginform .gdbp-hidden-reg\');var gdbHtml = jQuery(gdbO).html();jQuery(gdbO).remove();jQuery(\'.login_form_l #cus_loginform\').append(\'<div id="gdbp-reg-link">\'+gdbHtml+\'</div>\');});</script>';
}

/**
 * GD registration to BuddyPress registration.
 *
 * @since 1.0.0
 * @package GeoDirectory_BuddyPress_Integration
 *
 * @param string $link Signup page link.
 * @return string Modified Signup page link.
 */
function geodir_buddypress_signup_reg_form_link( $link ) {
	if ( get_option( 'geodir_buddypress_bp_register' ) ) {
		$link = bp_get_signup_page();
	}
	
	return $link;
}

/**
 * set parent activity_id to 1 if listing comment has not parent activity_id.
 *
 * @since 1.0.0
 * @package GeoDirectory_BuddyPress_Integration
 *
 * @param int $activity_id BP activity ID.
 * @return int BP activity ID.
 */
function geodir_buddypress_get_activity_id( $activity_id ) {
	if ( !$activity_id ) {
		$gd_post_types = geodir_get_posttypes( 'array' );
		
		$comment_post_ID = isset( $_POST['comment_post_ID'] ) ? $_POST['comment_post_ID'] : 0;
		$comment_post = get_post( $comment_post_ID );
		
		if ( !empty( $comment_post ) && isset( $comment_post->post_type ) && $comment_post->post_type && !empty( $gd_post_types ) && array_key_exists( $comment_post->post_type, $gd_post_types ) ) {
			$activity_id = 1;
		}
	}
	return $activity_id;
}

/**
 * Add link to buddypress registration.
 *
 * @since 1.0.0
 * @package GeoDirectory_BuddyPress_Integration
 */
function geodir_buddypress_login_form() {
	?>
	<div class="gdbp-hidden-reg" style="display:none"><a href="<?php echo apply_filters( 'geodir_signup_reg_form_link', home_url() . '/?geodir_signup=true&page1=sign_up' ); ?>" class="goedir-newuser-link"><?php echo NEW_USER_TEXT;?></a></div>
	<?php
}

/**
 * Filter the login redirect URL.
 *
 * @since 1.0.2
 * @package GeoDirectory_BuddyPress_Integration
 *
 * @global object $bp BuddyPress object.
 *
 * @param string $redirect_to The redirect destination URL.
 * @param string $requested_redirect_to The requested redirect destination URL passed as a parameter.
 * @param WP_User|WP_Error $user WP_User object if login was successful, WP_Error object otherwise.
 * @return string The redirect URL.
 */
function geodir_buddypress_login_redirect( $redirect_to, $requested_redirect_to, $user ) {					
	// Only modify the redirect if we're on the main BP blog
	if ( !bp_is_root_blog() ) {
		return $redirect_to;
	}

	// Only modify the redirect once the user is logged in
	if ( !is_a( $user, 'WP_User' ) ) {
		return $redirect_to;
	}

	// If a 'redirect_to' parameter has been passed that contains 'wp-admin'
	if ( !empty( $redirect_to ) && false !== strpos( $redirect_to, 'wp-admin' ) && user_can( $user, 'edit_posts' ) ) {
		return $redirect_to;
	}
	
	$login_redirect = (int)get_option( 'gdbuddypress_login_redirect' );
	
	switch ( $login_redirect ) {
		case 1: // Home page
			$redirect_to = trailingslashit( home_url() );
		break;
		case 2: // Profile page
			$members_slug = bp_get_members_root_slug();
			
			if ( $members_slug ) {
				$redirect_to = trailingslashit( bp_core_get_user_domain( $user->ID ) );
			} else {
				$username = bp_core_get_username( $user->ID, $user->data->user_nicename, $user->data->user_login );
				
				if ( bp_core_enable_root_profiles() ) {
					$redirect_to = trailingslashit( bp_get_root_domain() . '/' . $username );
				} else {
					$bp_pages = bp_core_get_directory_pages();
					
					if ( isset( $bp_pages->members->slug ) ) {
						$members_slug = $bp_pages->members->slug;
					} else {
						global $bp;
						
						$members_slug = defined( 'BP_MEMBERS_SLUG' ) ? BP_MEMBERS_SLUG : $bp->members->id;
					}
					
					$redirect_to = trailingslashit( bp_get_root_domain() . '/' . $members_slug . '/' . $username );
				}
			}
		break;
		case 3: // Menu page
			$menu_redirect = (int)get_option( 'gdbuddypress_menu_redirect' );
			
			if ( $menu_redirect > 0 ) {
				$redirect_to = get_permalink( $menu_redirect );
			}
		break;
	}
	
	return $redirect_to;
}

/**
 * Append the featured image for the activity excerpt.
 *
 * @since 1.0.5
 * @package GeoDirectory_BuddyPress_Integration
 *
 * @param string $excerpt The appended text for the activity excerpt.
 * @return string The activity excerpt.
 */
function geodir_buddypress_bp_activity_featured_image( $excerpt = '' ) {
	$activity_name = bp_get_activity_object_name();
	$activity_type = bp_get_activity_type();
	$item_id = bp_get_activity_secondary_item_id();
	
	if ( $activity_name == 'activity' && $item_id > 0 && $activity_type == 'new_' . get_post_type( $item_id ) && get_option( 'geodir_buddypress_show_feature_image' ) ) {		
        $image = wp_get_attachment_image_src(  get_post_thumbnail_id( $item_id ) );
        
		if (!empty($image) && !empty($image[0])) {
			$listing_title = geodir_get_post_meta( $item_id, 'post_title', true );
			
			$featured_image = '<a class="gdbp-feature-image" href="' . get_permalink( $item_id ) . '" title="' . esc_attr( $listing_title ) . '"><img alt="' . esc_attr( $listing_title ) . '" src="' . $image[0] . '" /></a>';
			
			/**
			 * Filter the new listing featured image in activity.
			 *
			 * @since 1.0.5
			 *
			 * @param string $featured_image Featured image content.
			 * @param int $item_id Activity item id.
			 * @param string $activity_name Current activity name.
			 * @param string $activity_type Current activity type.
			 */
			$featured_image = apply_filters( 'geodir_buddypress_bp_activity_featured_image', $featured_image, $item_id, $activity_name, $activity_type );
			
			echo $featured_image;
		}
	}
	
	return $excerpt;
}
?>