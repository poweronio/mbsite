<?php
/**
 * Contains hook related to Location Manager plugin.
 *
 * @since 1.0.0
 * @package GeoDirectory_Location_Manager
 */

add_filter('geodir_diagnose_multisite_conversion' , 'geodir_diagnose_multisite_conversion_location_manager', 10,1);
/**
 * Diagnose Location Manager tables.
 *
 * @since 1.0.0
 * @package GeoDirectory_Location_Manager
 *
 * @param array $table_arr Diagnose table array.
 * @return array Modified diagnose table array.
 */
function geodir_diagnose_multisite_conversion_location_manager($table_arr){
	
	$table_arr['geodir_post_neighbourhood'] = __('Neighbourhood',GEODIRLOCATION_TEXTDOMAIN);
	$table_arr['geodir_post_locations'] = __('Locations',GEODIRLOCATION_TEXTDOMAIN);
	$table_arr['geodir_location_seo'] = __('Location SEO',GEODIRLOCATION_TEXTDOMAIN);
	return $table_arr;
}

/**************************
/* ACTIVATION/DEACTIVATION
***************************/

/**
 * Plugin Activation Function
 *
 * @since 1.0.0
 * @package GeoDirectory_Location_Manager
 */
function geodir_location_activation()
{
		
		if (get_option('geodir_installed')) {
		
			geodir_location_activation_script();
			
			geodir_update_options(geodir_location_default_options(), true);
			
			add_option('geodir_location_manager_activation_redirect', 1);
			
		}
	
}


/**
 * Function to install all location manager related data and options.
 *
 * @since 1.0.0
 * @package GeoDirectory_Location_Manager
 *
 * @global object $wpdb WordPress Database object.
 * @global string $plugin_prefix Geodirectory plugin table prefix.
 */
function geodir_location_activation_script() {
	global $wpdb,$plugin_prefix;
	
	/**
	 * Include any functions needed for upgrades.
	 *
	 * @since 1.3.6
	 */
	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
	
	$is_set_default_location = geodir_get_default_location();
	$wpdb->hide_errors();
	
	// rename tables if we need to
	if($wpdb->query("SHOW TABLES LIKE 'geodir_post_locations'")>0){$wpdb->query("RENAME TABLE geodir_post_locations TO ".$wpdb->prefix."geodir_post_locations");}
	if($wpdb->query("SHOW TABLES LIKE 'geodir_post_neighbourhood'")>0){$wpdb->query("RENAME TABLE geodir_post_neighbourhood TO ".$wpdb->prefix."geodir_post_neighbourhood");}
	if($wpdb->query("SHOW TABLES LIKE 'geodir_countries'")>0){$wpdb->query("RENAME TABLE geodir_countries TO ".$wpdb->prefix."geodir_countries");}
	if($wpdb->query("SHOW TABLES LIKE 'geodir_location_seo'")>0){$wpdb->query("RENAME TABLE geodir_location_seo TO ".$wpdb->prefix."geodir_location_seo");}
	
	
	$collate = '';
	if($wpdb->has_cap( 'collation' )) {
		if(!empty($wpdb->charset)) $collate = "DEFAULT CHARACTER SET $wpdb->charset";
		if(!empty($wpdb->collate)) $collate .= " COLLATE $wpdb->collate";
	}
	
	if($wpdb->get_var("SHOW TABLES LIKE '".POST_LOCATION_TABLE."'") != POST_LOCATION_TABLE)
	{
		
		$location_table = "CREATE TABLE IF NOT EXISTS ".POST_LOCATION_TABLE." (
					`location_id` int(11) NOT NULL AUTO_INCREMENT,
					`country` varchar(254) NOT NULL,
					`region` varchar(254) NOT NULL,
					`city` varchar(254) NOT NULL, 
					`country_slug` varchar(254) NOT NULL,
					`region_slug` varchar(254) NOT NULL,
					`city_slug` varchar(254) NOT NULL,
					`city_latitude` varchar(254) NOT NULL,
					`city_longitude` varchar(254) NOT NULL,
					`is_default` ENUM( '0', '1' ) NOT NULL DEFAULT '0',
					`city_meta` VARCHAR( 254 ) NOT NULL,
					`city_desc` TEXT NOT NULL,
					PRIMARY KEY (`location_id`)) $collate ";
		
		if ( $wpdb->query( "SHOW TABLES LIKE 'geodir_post_locations'" ) > 0 ) {
			$wpdb->query( "RENAME TABLE geodir_post_locations TO " . $wpdb->prefix . "geodir_post_locations" );
		} else {
			dbDelta( $location_table );
		}
	}
	
	
		
	$location_result = (array)geodir_get_default_location(); // this function is there in core plugin location_functions.php file.
	$post_types = geodir_get_posttypes(); // Fuction in core geodirectory plugin  
	$location_info = geodir_add_new_location_via_adon($location_result);
	geodir_location_set_default($location_info->location_id);
	
	if(!empty($post_types)){
		foreach($post_types as $post_type){
			$table = $plugin_prefix.$post_type.'_detail';
			$wpdb->query(
				$wpdb->prepare(
				"UPDATE ".$table." SET post_location_id=%d WHERE post_location_id=0",
				array($location_info->location_id)
				)
			);
		}
	}
		
	if($wpdb->get_var("SHOW TABLES LIKE '".POST_NEIGHBOURHOOD_TABLE."'") != POST_NEIGHBOURHOOD_TABLE)
	{
		
		$neighbourhood_table = "CREATE TABLE IF NOT EXISTS ".POST_NEIGHBOURHOOD_TABLE." (
					`hood_id` int(11) NOT NULL AUTO_INCREMENT,
					`hood_location_id` int(11) NOT NULL,
					`hood_name` varchar(254) NOT NULL,
					`hood_latitude` varchar(254) NOT NULL,
					`hood_longitude` varchar(254) NOT NULL,
					`hood_slug` varchar(254) NOT NULL,
					PRIMARY KEY (`hood_id`)) $collate ";
		
		if ( $wpdb->query( "SHOW TABLES LIKE 'geodir_post_neighbourhood'" ) > 0 ) {
			$wpdb->query( "RENAME TABLE geodir_post_neighbourhood TO " . $wpdb->prefix . "geodir_post_neighbourhood" );
		} else {
			dbDelta( $neighbourhood_table );
		}
	}	
	
	$address_extra_info = $wpdb->get_results("select id, extra_fields from ".GEODIR_CUSTOM_FIELDS_TABLE." where field_type = 'address'");

	if(!empty($address_extra_info)){
		foreach($address_extra_info as $extra){
			$fields = array();
			if($extra->extra_fields != ''){
				$fields = unserialize($extra->extra_fields);
				if(!isset($fields['show_city'])){       $fields['show_city'] = 1;}
				if(!isset($fields['city_lable'])){      $fields['city_lable'] = __('City', GEODIRLOCATION_TEXTDOMAIN);}
				if(!isset($fields['show_region'])){     $fields['show_region'] = 1;}
				if(!isset($fields['region_lable'])){    $fields['region_lable'] = __('Region', GEODIRLOCATION_TEXTDOMAIN);}
				if(!isset($fields['show_country'])){    $fields['show_country'] = 1;}
				if(!isset($fields['country_lable'])){   $fields['country_lable'] = __('Country', GEODIRLOCATION_TEXTDOMAIN);}
					
					$wpdb->query(
						$wpdb->prepare(
							"UPDATE ".GEODIR_CUSTOM_FIELDS_TABLE." SET extra_fields=%s WHERE id=%d",
							array(serialize($fields),$extra->id)
						)
					);
			}
		}
	}
	
	//$post_types = geodir_get_posttypes();
	if(!empty($post_types)){
		foreach($post_types as $post_type){
			$detail_table = $plugin_prefix.$post_type.'_detail';
			$meta_field_add = "VARCHAR( 30 ) NULL";						
			geodir_add_column_if_not_exist( $detail_table, "post_neighbourhood", $meta_field_add );
		}
	}
	
	// location seo table
	if($wpdb->get_var("SHOW TABLES LIKE '".LOCATION_SEO_TABLE."'") != LOCATION_SEO_TABLE)
	{
		$location_table = "CREATE TABLE IF NOT EXISTS ".LOCATION_SEO_TABLE." (
					`seo_id` int(11) NOT NULL AUTO_INCREMENT,
					  `location_type` varchar(255) NOT NULL,
					  `country_slug` varchar(254) NOT NULL,
					  `region_slug` varchar(254) NOT NULL,
					  `city_slug` varchar(254) NOT NULL,
					  `seo_title` varchar(254) NOT NULL,
					  `seo_desc` text NOT NULL,
					  `date_created` datetime NOT NULL,
					  `date_updated` datetime NOT NULL,
					  PRIMARY KEY (`seo_id`)
					) $collate ";
		
		if ( $wpdb->query( "SHOW TABLES LIKE 'geodir_location_seo'" ) > 0 ) {
			$wpdb->query( "RENAME TABLE geodir_location_seo TO " . $wpdb->prefix . "geodir_location_seo" );
		} else {
			dbDelta( $location_table );
		}
	}

    // location term count table
    if($wpdb->get_var("SHOW TABLES LIKE '".GEODIR_TERM_META."'") != GEODIR_TERM_META)
    {
        $term_meta_table = "CREATE TABLE IF NOT EXISTS ".GEODIR_TERM_META." (
						id int NOT NULL AUTO_INCREMENT,
						location_type varchar( 100 ) NULL DEFAULT NULL,
						location_name varchar( 100 ) NULL DEFAULT NULL,
						term_count varchar( 5000 ) NULL DEFAULT NULL,
						review_count varchar( 5000 ) NULL DEFAULT NULL,
						PRIMARY KEY  (id)
						) $collate ";
		
		dbDelta( $term_meta_table );
    }
}

/**
 * Plugin deactivation Function.
 *
 * @since 1.0.0
 * @package GeoDirectory_Location_Manager
 *
 * @global object $wpdb WordPress Database object.
 * @global string $plugin_prefix Geodirectory plugin table prefix.
 */
function geodir_location_deactivation()
{
	global $wpdb,$plugin_prefix;
	$default_location = geodir_get_default_location(); 
	$post_types = geodir_get_posttypes();
	if(!empty($post_types)){
		foreach($post_types as $post_type)
		{
			$table = $plugin_prefix.$post_type.'_detail';
			$wpdb->query($wpdb->prepare("UPDATE ".$table." SET post_location_id='0' WHERE post_location_id=%d",array($default_location->location_id)));
		}	
		
	}
	$default_location->location_id = 0;
	update_option('geodir_default_location', $default_location); 
}

/**
 * Function to delete all the location adons related data and option on plugin deletion.
 *
 * @since 1.0.0
 * @package GeoDirectory_Location_Manager
 *
 * @global object $wpdb WordPress Database object.
 * @global string $plugin_prefix Geodirectory plugin table prefix.
 */
function geodir_location_uninstall()
{
	if ( ! isset($_REQUEST['verify-delete-adon']) ) 
	{
		$plugins = isset( $_REQUEST['checked'] ) ? (array) $_REQUEST['checked'] : array();
			//$_POST = from the plugin form; $_GET = from the FTP details screen.
		wp_enqueue_script('jquery');
		require_once(ABSPATH . 'wp-admin/admin-header.php');
		printf( '<h2>%s</h2>' ,__( 'Warning!!' , GEODIRLOCATION_TEXTDOMAIN) );
		printf( '%s<br/><strong>%s</strong><br /><br />%s <a href="http://wpgeodirectory.com">%s</a>.' , __('You are about to delete a Geodirectory Adon which has important option and custom data associated to it.' ,GEODIRLOCATION_TEXTDOMAIN) ,__('Deleting this and activating another version, will be treated as a new installation of plugin, so all the data will be lost.', GEODIRLOCATION_TEXTDOMAIN), __('If you have any problem in upgrading the plugin please contact Geodirectroy', GEODIRLOCATION_TEXTDOMAIN) , __('support' ,GEODIRLOCATION_TEXTDOMAIN) ) ;
					
	?><br /><br />
		<form method="post" action="<?php echo esc_url($_SERVER['REQUEST_URI']); ?>" style="display:inline;">
						<input type="hidden" name="verify-delete" value="1" />
						<input type="hidden" name="action" value="delete-selected" />
						<input type="hidden" name="verify-delete-adon" value="1" />
						<?php
							foreach ( (array) $plugins as $plugin )
								echo '<input type="hidden" name="checked[]" value="' . esc_attr($plugin) . '" />';
						?>
						<?php wp_nonce_field('bulk-plugins') ?>
						<?php submit_button(  __( 'Delete plugin files only' , GEODIRLOCATION_TEXTDOMAIN ), 'button', 'submit', false ); ?>
					</form>
					<form method="post" action="<?php echo esc_url($_SERVER['REQUEST_URI']); ?>" style="display:inline;">
						<input type="hidden" name="verify-delete" value="1" />
						<input type="hidden" name="action" value="delete-selected" />
                        <input type="hidden" name="verify-delete-adon" value="1" />
						<input type="hidden" name="verify-delete-adon-data" value="1" />
						<?php
							foreach ( (array) $plugins as $plugin )
								echo '<input type="hidden" name="checked[]" value="' . esc_attr($plugin) . '" />';
						?>
						<?php wp_nonce_field('bulk-plugins') ?>
						<?php submit_button(  __( 'Delete both plugin files and data' , GEODIRLOCATION_TEXTDOMAIN) , 'button', 'submit', false ); ?>
					</form>
					
	<?php
		require_once(ABSPATH . 'wp-admin/admin-footer.php');
		exit;
	}
	
	
	if ( isset($_REQUEST['verify-delete-adon-data']) ) 
	{	
		global $wpdb,$plugin_prefix;
		
		/*delete_option('geodir_show_changelocation_nave', '');
		delete_option('location_multicity', '');
		delete_option('location_everywhere', '');
		delete_option('location_neighbourhoods', '');*/
		
		/* --- delete location options --- */
		
		$location_option = geodir_location_default_options();
		
		if(!empty($location_option)){
			foreach($location_option as $value){
				if(isset($value['id']) && $value['id'] != '')
					delete_option($value['id'], '');
			}
		}
		
		unset(	$_SESSION['gd_multi_location'],
					$_SESSION['gd_city'],
					$_SESSION['gd_region'],
					$_SESSION['gd_country'] );
		
		$default_location = geodir_get_default_location();
		
		$post_types = geodir_get_posttypes();
		
		if(!empty($post_types)){
			
			foreach($post_types as $post_type){
				
				$table = $plugin_prefix.$post_type.'_detail';
				
				$del_post_sql = $wpdb->get_results(
					$wpdb->prepare(
					"SELECT post_id from ".$table." where post_location_id != %d",
					array($default_location->location_id)
					)
				);
					
				if(!empty($del_post_sql)){
					foreach($del_post_sql as $del_post_info)
					{					
						$postid = $del_post_info->post_id;
						wp_delete_post($postid);
					}
				}
				
				$wpdb->query("UPDATE ".$table." SET post_location_id='0'");
				
				if($wpdb->get_var("SHOW COLUMNS FROM ".$table." WHERE field = 'post_neighbourhood'"))
					$wpdb->query("ALTER TABLE ".$table." DROP post_neighbourhood");
			}	
		}
		
		$wpdb->query("DROP TABLE ".$plugin_prefix."post_locations");
		$wpdb->query("DROP TABLE ".$plugin_prefix."post_neighbourhood");
		$wpdb->query("DROP TABLE IF EXISTS ".$plugin_prefix."location_seo");
		
		$default_location->location_id = 0;
		update_option('geodir_default_location', $default_location); 
	}
}

/**************************
/* INIT HOOKS
***************************/
add_action('admin_init', 'geodir_admin_location_init');
/**
 * Initialize admin functions.
 *
 * @since 1.0.0
 * @package GeoDirectory_Location_Manager
 */
function geodir_admin_location_init()
{
	if(is_admin()):
		geodir_location_form_submit_handler();
		add_filter('geodir_settings_tabs_array','geodir_admin_location_tabs' , 4); 
		add_action('geodir_admin_option_form', 'geodir_get_admin_location_option_form',2);
	endif;	
}

/**
 * Function to add tabs and subtabs in GeoDirectory backend.
 *
 * @since 1.0.0
 * @package GeoDirectory_Location_Manager
 *
 * @param array $tabs Geodirectory settings page tab list.
 * @return array Modified Tabs list
 */
function geodir_admin_location_tabs($tabs)
{
	$tabs['managelocation_fields'] = array( 
											'label' =>__( 'MultiLocations', GEODIRLOCATION_TEXTDOMAIN ),
											'subtabs' => array(
																array(	'subtab' => 'geodir_location_setting',
																		'label' =>__( 'Location Settings', GEODIRLOCATION_TEXTDOMAIN),	
																		'form_action' => admin_url('admin-ajax.php?action=geodir_locationajax_action')
																),
																array(	'subtab' => 'geodir_location_manager',
																		'label' =>__( 'Manage Location', GEODIRLOCATION_TEXTDOMAIN),
																		'form_action' => ''
																),
																array(	'subtab' => 'geodir_location_seo',
																		'label' =>__( 'SEO Settings', GEODIRLOCATION_TEXTDOMAIN),
																		'form_action' => ''
																),
																array('subtab' => 'geodir_location_addedit',
																		'label' =>__( 'Add/Edit Location', GEODIRLOCATION_TEXTDOMAIN),
																		'form_action' => admin_url('admin-ajax.php?action=geodir_locationajax_action')
																),
																array( 
																	'subtab' => 'geodir_location_translate',
																	'label' =>__( 'Translate Countries', GEODIRLOCATION_TEXTDOMAIN ),
																	'form_action' => ''
																)
															)// end of sub tab array
											);// end of main array
		
	return $tabs; 
}

/**
 * Function to show backend form based on selected sub tab.
 *
 * @since 1.0.0
 * @package GeoDirectory_Location_Manager
 *
 * @global object $wpdb WordPress Database object.
 *
 * @param string $current_tab The current settings tab name.
 */
function geodir_get_admin_location_option_form( $current_tab ) {
	global $wpdb;
	
	$subtab = isset( $_REQUEST['subtab'] ) ? $_REQUEST['subtab'] : '';
	
	switch( $subtab ) {
		case 'geodir_location_setting': {
			add_action( 'geodir_admin_option_form', 'geodir_get_location_default_options_form' );// function is in geodir_location_template_tags.php file
		}
		break;
		case 'geodir_location_manager': {
			include_once( 'geodir_location_list.php' );
		}
		break;
		case 'geodir_location_seo': {
			include_once( 'geodir_location_seo.php' );
		}
		break;
		case 'geodir_location_addedit': {
			include_once( 'geodir_add_location.php' );
		}
		break;
		case 'geodir_location_translate': {
			include_once( 'geodir_location_translate.php' );
		}
		break;
	}
}

/**
 *
 * @since 1.0.0
 * @package GeoDirectory_Location_Manager
 *
 * @param string $current_tab The current settings tab name.
 */
function geodir_get_location_default_options_form($current_tab){
	
	$current_tab = $_REQUEST['subtab'];
	geodir_location_default_option_form($current_tab); // this function is in template tags 
}

add_action('admin_init', 'geodir_location_activation_redirect');
/**
 * Hook to redirect user to Location related setting page in backend on plugin installation.
 *
 * @since 1.0.0
 * @package GeoDirectory_Location_Manager
 */
function geodir_location_activation_redirect()
{
	if (get_option('geodir_location_manager_activation_redirect', false))
	{
		delete_option('geodir_location_manager_activation_redirect');
		wp_redirect(admin_url('admin.php?page=geodirectory&tab=managelocation_fields&subtab=geodir_location_setting')); 
	}
}

add_action('geodir_before_admin_panel' , 'geodir_display_location_messages');
/**
 * Function for display GeoDirectory location error and success messages.
 *
 * @since 1.0.0
 * @package GeoDirectory_Location_Manager
 */
function geodir_display_location_messages()
{
	if(isset($_REQUEST['location_success']) && $_REQUEST['location_success'] != '')
	{
			echo '<div id="message" class="updated fade"><p><strong>' . $_REQUEST['location_success'] . '</strong></p></div>';			
				
	}
	
	if(isset($_REQUEST['location_error']) && $_REQUEST['location_error'] != '')
	{
			echo '<div id="payment_message_error" class="updated fade"><p><strong>' . $_REQUEST['location_error'] . '</strong></p></div>';			
				
	}
}




/**************************
/* SCRIPT AND STYLE RELATED
***************************/

add_action('wp_enqueue_scripts', 'geodir_add_location_style_sheet');
/**
 * Adds location manager plugin css.
 *
 * @since 1.0.0
 * @package GeoDirectory_Location_Manager
 */
function geodir_add_location_style_sheet()
{	
	wp_enqueue_style( 'location_manager_css',plugins_url('/css/geodir-location.css',__FILE__)  );
}

add_action('admin_enqueue_scripts', 'geodir_add_location_admin_style_sheet');
/**
 * Adds location manager plugin admin css.
 *
 * @since 1.0.0
 * @package GeoDirectory_Location_Manager
 */
function geodir_add_location_admin_style_sheet()
{	
	wp_enqueue_style( 'location_manager_admin_css',plugins_url('/css/location-admin.css',__FILE__)  );
}

	
add_action('wp_enqueue_scripts', 'geodir_add_location_scripts');
add_action('admin_enqueue_scripts', 'geodir_add_location_scripts');
/**
 * Adds location manager plugin js.
 *
 * @since 1.0.0
 * @package GeoDirectory_Location_Manager
 */
function geodir_add_location_scripts()
{	
	if(is_admin() && ( isset( $_REQUEST['page']) && $_REQUEST['page'] == 'geodirectory') && ( isset($_REQUEST['tab']) && $_REQUEST['tab'] == 'managelocation_fields'))
		wp_enqueue_script( 'geodirectory-location-admin', plugins_url('/js/location-admin.js',__FILE__));
	
	// Include script only on front end.
	
		wp_enqueue_script( 'geodirectory-location-front' ,plugins_url('/js/location-front.min.js#asyncload',__FILE__),'', '', true);
	
	
}

add_action('wp_footer','geodir_location_localize_all_js_msg');
add_action('admin_footer','geodir_location_localize_all_js_msg');
/**
 * Outputs translated JS text strings.
 *
 * @since 1.0.0
 * @package GeoDirectory_Location_Manager
 */
function geodir_location_localize_all_js_msg()
{
	global $path_location_url;
	
	$arr_alert_msg = array(
							'geodir_location_admin_url' => admin_url('admin.php'),
							'geodir_location_plugin_url' => $path_location_url,
							'geodir_location_admin_ajax_url' => admin_url('admin-ajax.php'),
							'select_merge_city_msg' => MSG_LOCATION_JS_SELECT_CITY,
							'set_location_default_city_confirmation' => MSG_LOCATION_SET_DEFAULT_CITY,
							'LISTING_URL_PREFIX' =>  __('Please enter listing url prefix', GEODIRLOCATION_TEXTDOMAIN),
							'LISTING_URL_PREFIX_INVALID_CHAR' =>__('Invalid character in listing url prefix', GEODIRLOCATION_TEXTDOMAIN),
							'LOCATION_URL_PREFIX'=> __('Please enter location url prefix', GEODIRLOCATION_TEXTDOMAIN),
							'LOCATOIN_PREFIX_INVALID_CHAR' =>__('Invalid character in location url prefix', GEODIRLOCATION_TEXTDOMAIN),
							'LOCATION_CAT_URL_SEP' =>__('Please enter location and category url separator', GEODIRLOCATION_TEXTDOMAIN),
							'LOCATION_CAT_URL_SEP_INVALID_CHAR' =>__('Invalid character in location and category url separator', GEODIRLOCATION_TEXTDOMAIN),
							'LISTING_DETAIL_URL_SEP'=>__('Please enter listing detail url separator', GEODIRLOCATION_TEXTDOMAIN),
							'LISTING_DETAIL_URL_SEP_INVALID_CHAR' =>__('Invalid character in listing detail url separator', GEODIRLOCATION_TEXTDOMAIN),
							
							'LOCATION_PLEASE_WAIT' =>__('Please wait...', GEODIRLOCATION_TEXTDOMAIN),
							'LOCATION_CHOSEN_NO_RESULT_TEXT' =>__('Sorry, nothing found!', GEODIRLOCATION_TEXTDOMAIN),
							'LOCATION_CHOSEN_KEEP_TYPE_TEXT' =>__('Please wait...', GEODIRLOCATION_TEXTDOMAIN),
							'LOCATION_CHOSEN_LOOKING_FOR_TEXT' =>__('We are searching for', GEODIRLOCATION_TEXTDOMAIN),
							'select_location_translate_msg' => MSG_LOCATION_JS_SELECT_COUNTRY,
							'select_location_translate_confirm_msg' => MSG_LOCATION_JS_SELECT_COUNTRY_CONFIRM,
							'gd_text_search_city' => __( 'Search City', GEODIRLOCATION_TEXTDOMAIN ),
							'gd_text_search_region' => __( 'Search Region', GEODIRLOCATION_TEXTDOMAIN ),
							'gd_text_search_country' => __( 'Search Country', GEODIRLOCATION_TEXTDOMAIN ),
							'gd_text_search_location' => __( 'Search location', GEODIRLOCATION_TEXTDOMAIN ),
							'gd_base_location' => geodir_get_location_link('base'),
							'UNKNOWN_ERROR' => __( 'Unable to find your location.',GEODIRLOCATION_TEXTDOMAIN ),
							'PERMISSION_DENINED' => __( 'Permission denied in finding your location.',GEODIRLOCATION_TEXTDOMAIN ),
							'POSITION_UNAVAILABLE' => __( 'Your location is currently unknown.',GEODIRLOCATION_TEXTDOMAIN ),	
							'BREAK' => __( 'Attempt to find location took too long.',GEODIRLOCATION_TEXTDOMAIN ),
							'DEFAUTL_ERROR' => __( 'Browser unable to find your location.',GEODIRLOCATION_TEXTDOMAIN ),
							'msg_Near' => __( "Near:", GEODIRLOCATION_TEXTDOMAIN ),
							'msg_Me' => __( "Me", GEODIRLOCATION_TEXTDOMAIN ),
							'msg_User_defined' => __( "User defined", GEODIRLOCATION_TEXTDOMAIN ),
							'delete_location_msg' => __( 'Are you sure want to delete this location?', GEODIRLOCATION_TEXTDOMAIN ),
							'delete_bulk_location_select_msg' => __( 'Please select atleast one location.', GEODIRLOCATION_TEXTDOMAIN ),
						);

	foreach ( $arr_alert_msg as $key => $value )
	{
		if ( !is_scalar($value) )
			continue;
		$arr_alert_msg[$key] = html_entity_decode( (string) $value, ENT_QUOTES, 'UTF-8');
	}

	$script = "var geodir_location_all_js_msg = " . json_encode($arr_alert_msg) . ';';
	echo '<script>';
	echo $script ;
	echo '</script>';
}
	
/**************************
/* WIDGETS RELATED
***************************/
// All these functions are in geodir_location_widgets.php file
add_action('widgets_init', 'register_geodir_location_widgets'); 

add_action('widgets_init', 'register_geodir_neighbourhood_widgets');

add_action('widgets_init', 'register_geodir_neighbourhood_posts_widgets');

add_action('widgets_init', 'register_geodir_location_description_widgets');
	
	
/**************************
/* LOCATION ADONS ADMIN PANEL RELATED 
***************************/
add_filter('geodir_settings_tabs_array', 'geodir_hide_set_location_default',3);	
/**
 * Function to hide geodirectory core manage default location tab.
 *
 * @since 1.0.0
 * @package GeoDirectory_Location_Manager
 *
 * @param array $tabs Geodirectory settings page tab list.
 * @return array Modified Tabs list
 */
function geodir_hide_set_location_default($tabs)
{
	if(!empty($tabs)):
		unset($tabs['default_location_settings']);
	endif;
	return $tabs;
}

add_filter('geodir_search_near_addition', 'geodir_search_near_additions',3);	
/**
 * Adds any extra info to the near search box query when trying to geolocate it via google api.
 *
 * @since 1.0.0
 * @package GeoDirectory_Location_Manager
 *
 * @global object $wpdb WordPress Database object.
 *
 * @param string $additions Extra info string.
 * @return string
 */
function geodir_search_near_additions($additions)
{
	global $wpdb;
	$loc = '';
	if($default = get_option('geodir_default_location')){
		if(get_option('geodir_enable_region')=='default' && $default->region){$loc .= '+", '.$default->region.'"';}
		if(get_option('geodir_enable_country')=='default' && $default->country){$loc .= '+", '.$default->country.'"';}
	}
	return $loc;
}

add_filter('geodir_design_settings', 'geodir_detail_page_related_post_add_location_filter_checkbox', 1);
/**
 * This add a new filed in Geodirectory > Design > Detail > Related Post Settings.
 *
 * @since 1.0.0
 * @package GeoDirectory_Location_Manager
 *
 * @param array $arr GD design settings array.
 * @return array Filtered GD design settings array.
 */
function geodir_detail_page_related_post_add_location_filter_checkbox($arr)
{
	$location_design_array = array();
	foreach($arr as $key => $val)
	{
		$location_design_array[] = $val;
		if($val['id'] == 'geodir_related_post_excerpt')
		{
			$location_design_array[] = array(  
											'name' => __( 'Enable Location Filter:', GEODIRLOCATION_TEXTDOMAIN ),
											'desc' 		=> __( 'Enable location filter on related post.', GEODIRLOCATION_TEXTDOMAIN ),
											'id' 		=> 'geodir_related_post_location_filter',
											'type' 		=> 'checkbox',
											'std' 		=> '1' // Default value to show home top section
										);
		}
	}
	return $location_design_array;
}
	

/**************************
/* LOCATION ADONS QUERY FILTERS
**************************	*/

/**
 * Sets user location.
 *
 * @since 1.0.0
 * @package GeoDirectory_Location_Manager
 */
function geodir_set_user_location_near_me(){?>
<script type="text/javascript">
(function() {
	// Try HTML5 geolocation
	if(navigator.geolocation) {
		navigator.geolocation.getCurrentPosition(function(position) {
			lat = position.coords.latitude;
			lon = position.coords.longitude;
			my_location = 1;
			if(typeof gdSetupUserLoc === 'function') {
				gdSetupUserLoc();
			} else {
				gdLocationSetupUserLoc();
			}
			jQuery.ajax({
				// url: url,
				url: "<?php echo admin_url('admin-ajax.php'); ?>",
				type: 'POST',
				dataType: 'html',
				data: {
					action: 'gd_location_manager_set_user_location',
					lat: lat,
					lon: lon,
					myloc: 1
				},
				beforeSend: function() {},
				success: function(data, textStatus, xhr) {
					<?php if(!isset($_SESSION['my_location']) || $_SESSION['my_location']==0){?>window.location.href = "<?php echo geodir_get_location_link('base').'me/';?>";<?php }?>
				},
				error: function(xhr, textStatus, errorThrown) {
					alert(textStatus);
				}
			});
		});
	} else {
		// Browser doesn't support Geolocation
		alert(geodir_location_all_js_msg.DEFAUTL_ERROR);
	}
}());	  
</script>
	<?php
}





add_filter('query_vars', 'add_location_var');
/**
 *
 * @since 1.0.0
 * @package GeoDirectory_Location_Manager
 *
 * @param array $public_query_vars The array of whitelisted query variables.
 * @return array Filtered query variables.
 */
function add_location_var($public_query_vars) {

	$public_query_vars[] = 'gd_neighbourhood';
	
	return $public_query_vars;
}
if ( get_option('permalink_structure') != '' )
	add_filter('parse_request', 'geodir_set_location_var_in_session',100);
/**
 * Set location data in session.
 *
 * @since 1.0.0
 * @package GeoDirectory_Location_Manager
 *
 * @global object $wpdb WordPress Database object.
 *
 * @param object $wp WordPress object.
 */
function geodir_set_location_var_in_session($wp)
{
//unset($_SESSION['gd_location_shared']);
/*$point1=array('latitude'=>'-22.5260060699' ,'longitude'=> '-43.7334400235' ) ;
$point1=array('latitude'=>'28.635308' ,'longitude'=> '77.22496' ) ;
$point2=array('latitude'=>'-22.7024218' ,'longitude'=> '-43.33662349999997' ) ;
$point2=array('latitude'=>'-22.7356363' ,'longitude'=> '-43.44001100000003' ) ;

echo geodir_calculateDistanceFromLatLong($point1, $point2);
*/
// Avoide all the changes made by core, restore original queryvars ;
//	$wp->query_vars=$wp->geodir_query_vars ;


// this code will determine when a user wants to switch location 
// A location can be switched using 3 ways 
//1. usign location switcher, in this case the url will always have location prefix
// Query Vars will have page_id parameter
// check if query var has page_id and that page id is location page 


//print_r($_SESSION);
// my location set start

        //Fix for WPML removing page_id query var:
        if(isset($wp->query_vars['page']) && !isset($wp->query_vars['page_id']) && isset($wp->query_vars['pagename'])){
            global $wpdb;
            $real_page_id = $wpdb->get_var($wpdb->prepare("SELECT ID FROM $wpdb->posts WHERE post_type='page' AND post_name=%s",$wp->query_vars['pagename']));
            if($real_page_id){
                $wp->query_vars['page_id'] = $real_page_id;
            }
        }

		if((isset($wp->query_vars['gd_country']) &&  $wp->query_vars['gd_country'] == 'me' && isset($_SESSION['user_lat']) && $_SESSION['user_lat'] && isset($_SESSION['user_lon']) && $_SESSION['user_lon']) || (isset($_SESSION['all_near_me']) && is_admin()) ){
			
		if(isset($_REQUEST['user_lat']) && $_REQUEST['user_lat']){$_SESSION['user_lat']=$_REQUEST['user_lat'];}
		if(isset($_REQUEST['user_lon']) && $_REQUEST['user_lon']){$_SESSION['user_lon']=$_REQUEST['user_lon'];}
		if(isset($_SESSION['near_me_range']) && $_SESSION['near_me_range']){$_REQUEST['sdist']=$_SESSION['near_me_range'];}
			
		$_SESSION['all_near_me']=1;
		$_REQUEST['sgeo_lat'] = $_SESSION['user_lat'];
		$_REQUEST['sgeo_lon'] = $_SESSION['user_lon'];
		$_REQUEST['snear'] = 1;
		$_SESSION['gd_multi_location'] = 0;
		
			//unset any locations
			unset($_SESSION['gd_city'],$_SESSION['gd_region'],$_SESSION['gd_country'] );



	return;
	
		}
		elseif(isset($wp->query_vars['gd_country']) &&  $wp->query_vars['gd_country'] == 'me'){
		// at the near me page but with no location
		add_action('wp_head','geodir_set_user_location_near_me');
		return;
		
		}else{
		if(isset($_SESSION['all_near_me'])){unset($_SESSION['all_near_me']);}
		}
		// my location set end

	geodir_set_is_geodir_page($wp) ;
	if(!get_option('geodir_set_as_home'))
	{
		
		if ( empty($wp->query_vars) || !array_diff( array_keys($wp->query_vars), array('preview', 'page', 'paged', 'cpage') ) )
		{
			if( 'page' == get_option('show_on_front'))
				$wp->query_vars['page_id'] = get_option('page_on_front');
		}
			
	}



	if(isset($wp->query_vars['page_id']) && $wp->query_vars['page_id'] == geodir_location_page_id() || (isset($_REQUEST['set_location_type']) && $_REQUEST['set_location_type'] && isset($_REQUEST['set_location_val']) && $_REQUEST['set_location_val']))
	{


		$gd_country = '' ;
		$gd_region = '' ;
		$gd_city = '' ;
		if(isset($wp->query_vars['gd_country']) &&  $wp->query_vars['gd_country'] != '')
			$gd_country =urldecode($wp->query_vars['gd_country']) ;
		
		if(isset($wp->query_vars['gd_region']) &&  $wp->query_vars['gd_region'] != '')
			$gd_region =urldecode($wp->query_vars['gd_region']) ; 
		
		if(isset($wp->query_vars['gd_city']) &&  $wp->query_vars['gd_city'] != '')
			$gd_city =urldecode($wp->query_vars['gd_city']) ;

		if(!($gd_country=='' && $gd_region == '' && $gd_city == '' ))
		{
			$default_location = geodir_get_default_location();
			
			if( get_option('geodir_add_location_url'))
			{
				if(get_option('geodir_show_location_url')!='all')
				{
                    /*
                     * @todo i don't see the point in this code so i am removing it. (stiofan)
                     */
                    /*
					if($gd_region=='' )
					{
						if(isset($_SESSION['gd_region']))
							$gd_region = $_SESSION['gd_region'];
						else
							$gd_region =$default_location->region_slug;
					}
					
					if($gd_city=='' )
					{
						if(isset($_SESSION['gd_city']))
							$gd_city = $_SESSION['gd_city'];
						else
							$gd_city =$default_location->city_slug;
						
						$base_location_link = geodir_get_location_link('base') ;
						wp_redirect($base_location_link . '/' .$gd_country . '/' . $gd_region . '/' . $gd_city )	;
						exit();
					}*/

				}
			}
			
			$args  = 	array(
									'what'=> 'city' , 
									'city_val' => $gd_city,
									'region_val' => $gd_region,
									'country_val' => $gd_country,
									'country_column_name' => 'country_slug' ,
									'region_column_name' => 'region_slug' ,
									'city_column_name' => 'city_slug' ,
									'location_link_part' => false,
									'compare_operator' =>'' 
								);
			$location_array= geodir_get_location_array($args) ;
			if(!empty($location_array))
			{
				$_SESSION['gd_multi_location']=1 ;
				$_SESSION['gd_country'] = $gd_country ;
				$_SESSION['gd_region'] = $gd_region ;
				$_SESSION['gd_city'] = $gd_city ;
				$wp->query_vars['gd_country'] =  $gd_country ;
				$wp->query_vars['gd_region'] =  $gd_region;
				$wp->query_vars['gd_city'] =  $gd_city ;
			}
			else
			{
			
				unset(	$_SESSION['gd_multi_location'],
					$_SESSION['gd_city'],
					$_SESSION['gd_region'],
					$_SESSION['gd_country'] );
			}
			
		}
		else
		{
			unset(	$_SESSION['gd_multi_location'],
					$_SESSION['gd_city'],
					$_SESSION['gd_region'],
					$_SESSION['gd_country'] );	
		}
		
	}
	else if(isset($wp->query_vars['post_type']) && $wp->query_vars['post_type']!= '')
	{
	
		if(!is_admin())
		{
			$requested_post_type = $wp->query_vars['post_type'] ;
			// check if this post type is geodirectory post types 
			$post_type_array = geodir_get_posttypes()  ;
			if(in_array($requested_post_type  , $post_type_array))
			{
				// now u can apply geodirectory related manipulation.
				
				//echo "good: it is geodirectory post type<br />" ;
				//print_r($wp->query_vars) ;
			}
		}
	}
	else
	{
		// check if a geodirectory taxonomy is set
		$gd_country = '' ;
		$gd_region = '' ;
		$gd_city = '' ;
		$is_geodir_taxonomy = false;
		$is_geodir_taxonomy_term = false ; // the last term is real geodirectory taxonomy term or not
		$is_geodir_location_found = false ;
		$geodir_taxonomy = '' ; 
		$geodir_post_type = '' ;
		$geodir_term = '';
		$geodir_set_location_session = true;
		$geodir_taxonomis = geodir_get_taxonomies('',true ); 
		foreach($geodir_taxonomis as $taxonomy)
		{
			if(array_key_exists($taxonomy ,$wp->query_vars ))
			{
				$is_geodir_taxonomy = true ;
				$geodir_taxonomy =$taxonomy ;
				$geodir_post_type = str_replace('category' , '' , $taxonomy);
				$geodir_post_type = str_replace('_tags' , '' , $geodir_post_type);
				$geodir_term = $wp->query_vars[$geodir_taxonomy] ;
				
				break ;
			}
		}
		// now get an array of all terms seperated by '/'
		$geodir_terms  = explode('/' , $geodir_term);
		$geodir_last_term = end($geodir_terms);
		
		if($is_geodir_taxonomy) // do all these only when it is a geodirectory taxonomy
		{
			$wp->query_vars['post_type'] = $geodir_post_type ;
			
			// now check if last term is a post of geodirectory post types
			$geodir_post = get_posts(array(
						'name' => $geodir_last_term ,
						'posts_per_page' => 1,
						'post_type' => $geodir_post_type,
						
			));
			
			if(empty($geodir_post))
			{
				$geodir_post = get_posts(array(
						'name' => $geodir_last_term ,
						'posts_per_page' => 1,
						'post_type' => $geodir_post_type,
						'post_status'=>'draft',
						'suppress_filters'=>false,
						
				));
			}
			
			if(!empty($geodir_post) )
			{
				
				if($geodir_post[0]->post_status != 'publish') 
				{
					foreach($wp->query_vars as $key => $vars)
					{
						unset($wp->query_vars[$key]);
					}
					$wp->query_vars['error'] = '404' ;
					// set it as 404 if post exists but its status is not published yet
					
				}
				else
				{
					//$wp->query_vars[$geodir_taxonomy] = str_replace( '/'.$geodir_last_term , ''  , $geodir_term);				
					$wp->query_vars[$geodir_post_type]  = $geodir_last_term;
					$wp->query_vars['name']  = $geodir_last_term;
					
				}
				
				$geodir_term =  str_replace('/' . $geodir_last_term , ''  , $geodir_term,$post_title_replace_count);	
				if(!$post_title_replace_count)
					$geodir_term =  str_replace( $geodir_last_term , ''  , $geodir_term,$post_title_replace_count);	
				$geodir_terms  = explode('/' , $geodir_term);
				$geodir_last_term = end($geodir_terms);
				
				$geodir_set_location_session =false;
				//return ;
			}
			
			$geodir_location_terms = '';
			// if last term is not a post then check if last term is a term of the specific texonomy or not 
			if(geodir_term_exists($geodir_last_term, $geodir_taxonomy ))
			{
				$is_geodir_taxonomy_term = true ;
				
				$geodir_set_location_session =false;
			}
		
			
			// now check if there is location parts in the url or not
			if( get_option('geodir_add_location_url'))
			{
				
				if(get_option('geodir_show_location_url')=='all')
				{
					if(count($geodir_terms) >=3)
					{
						$gd_country= urldecode($geodir_terms[0]);
						$gd_region = urldecode($geodir_terms[1]) ;
						$gd_city =  urldecode($geodir_terms[2]) ;
					}
					else if(count($geodir_terms) >=2)
					{
						$gd_country= urldecode($geodir_terms[0]);
						$gd_region = urldecode($geodir_terms[1]) ;
					}
					else if(count($geodir_terms) >=1)
					{
						$gd_country= urldecode($geodir_terms[0]);
					}
						
					$args  = array(
										'what'=> 'city' , 
										'city_val' => $gd_city,
										'region_val' => $gd_region,
										'country_val' => $gd_country,
										'country_column_name' => 'country_slug' ,
										'region_column_name' => 'region_slug' ,
										'city_column_name' => 'city_slug' ,
										'location_link_part' => false,
										'compare_operator' =>'',
										'format'=> array('type'=>'array') 
									);
					
					$location_array= geodir_get_location_array($args) ;
					
					if(!empty($location_array) )
						$is_geodir_location_found = true ;
					
					
					// if location has not been found for country , region and city then search for country and region only 
					
					if(!$is_geodir_location_found )
					{
						$gd_city='';
						$args  = 	array(
										'what'=> 'city' , 
										'city_val' => $gd_city,
										'region_val' => $gd_region,
										'country_val' => $gd_country,
										'country_column_name' => 'country_slug' ,
										'region_column_name' => 'region_slug' ,
										'city_column_name' => 'city_slug' ,
										'location_link_part' => false,
											'compare_operator' =>'',
										'format'=> array('type'=>'array')
									);
							
						$location_array= geodir_get_location_array($args) ;
						
						if(!empty($location_array))
							$is_geodir_location_found = true ;
									
					}
					
					// if location has not been found for country , region  then search for country only 
					if(!$is_geodir_location_found )
					{
						$gd_city='';
						$gd_region='';
						$args  = 	array(
										'what'=> 'city' , 
										'city_val' => $gd_city,
										'region_val' => $gd_region,
										'country_val' => $gd_country,
										'country_column_name' => 'country_slug' ,
										'region_column_name' => 'region_slug' ,
										'city_column_name' => 'city_slug' ,
										'location_link_part' => false,
											'compare_operator' =>'',
										'format'=> array('type'=>'array')
									);
									
						$location_array= geodir_get_location_array($args) ;
						
						if(!empty($location_array) )
							$is_geodir_location_found = true ;
									
					}
				}
				else
				{
					$gd_city= urldecode($geodir_terms[0]);
					$args  = array(
										'what'=> 'city' , 
										'city_val' => $gd_city,
										'region_val' => $gd_region,
										'country_val' => $gd_country,
										'country_column_name' => 'country_slug' ,
										'region_column_name' => 'region_slug' ,
										'city_column_name' => 'city_slug' ,
										'location_link_part' => false,
											'compare_operator' =>'',
										'format'=> array('type'=>'array')
											
									);
					$location_array= geodir_get_location_array($args) ;
					if(!empty($location_array) )
						$is_geodir_location_found = true ;
						
					$args  = array(
										'what'=> 'region_slug' , 
										'city_val' => $gd_city,
										'region_val' => '',
										'country_val' => '',
										'country_column_name' => 'country_slug' ,
										'region_column_name' => 'region_slug' ,
										'city_column_name' => 'city_slug' ,
										'location_link_part' => false,
											'compare_operator' =>'',
										'format'=> array('type'=>'array')
									);
					
					$location_array= geodir_get_location_array($args) ;
					if(!empty($location_array) )
					{
						$gd_region=$location_array[0]->region_slug;
					}
					
					
					$args  = array(
										'what'=> 'country_slug' , 
										'city_val' => $gd_city,
										'region_val' => '',
										'country_val' => '',
										'country_column_name' => 'country_slug' ,
										'region_column_name' => 'region_slug' ,
										'city_column_name' => 'city_slug' ,
										'location_link_part' => false,
											'compare_operator' =>'',
										'format'=> array('type'=>'array')
									);
					
					$location_array= geodir_get_location_array($args) ;
					if(!empty($location_array) )
					{
						$gd_country=$location_array[0]->country_slug;
					}
				}
				// if locaton still not found then clear location related session variables
				if($is_geodir_location_found && $geodir_set_location_session )
				{
					$_SESSION['gd_multi_location']=1 ;
					$_SESSION['gd_country'] = $gd_country ;
					$_SESSION['gd_region'] = $gd_region ;
					$_SESSION['gd_city'] = $gd_city ;	
				}
				
				if(get_option('geodir_show_location_url')!='all')
				{
					$gd_country='' ;
					$gd_region='';
				}
				
				if($is_geodir_location_found)
				{
					$wp->query_vars['gd_country'] = $gd_country ;
					$wp->query_vars['gd_region'] =  $gd_region;
					$wp->query_vars['gd_city'] =  $gd_city; 
				}
				else
				{
					$gd_country='' ;
					$gd_region='';
					$gd_city='';
				}
			}
			
			
			$wp->query_vars[$geodir_taxonomy] = $geodir_term ;
			// eliminate location related terms from taxonomy term 
			if($gd_country !='')
				$wp->query_vars[$geodir_taxonomy] = preg_replace( '/' .urlencode($gd_country) .'/', ''  , $wp->query_vars[$geodir_taxonomy],1) ;
			
			if($gd_region !='')
				$wp->query_vars[$geodir_taxonomy] = preg_replace('/' . urlencode($gd_region).'/' , ''  , $wp->query_vars[$geodir_taxonomy],1)	;
				
			if($gd_city !='')
				$wp->query_vars[$geodir_taxonomy] = preg_replace( '/' .urlencode($gd_city)  .'/' , ''  , $wp->query_vars[$geodir_taxonomy],1)	;
				
			
			
			$wp->query_vars[$geodir_taxonomy] = str_replace( '///' , ''  , $wp->query_vars[$geodir_taxonomy])	;
			$wp->query_vars[$geodir_taxonomy] = str_replace( '//' , ''  , $wp->query_vars[$geodir_taxonomy])	;
			
			$wp->query_vars[$geodir_taxonomy] = trim($wp->query_vars[$geodir_taxonomy], '/');
			if($wp->query_vars[$geodir_taxonomy] == '' )
			{
				unset($wp->query_vars[$geodir_taxonomy]) ;
			}
			else
			{
				if(!$is_geodir_taxonomy_term)
				{
					foreach($wp->query_vars as $key => $vars)
					{
						unset($wp->query_vars[$key]);
					}
					$wp->query_vars['error'] = '404' ;
				}
				
			}
			
		}
	}

	if(isset($wp->query_vars['gd_is_geodir_page']) && is_array($wp->query_vars) &&  count($wp->query_vars)=='1' )
	{
		if(!isset($_SESSION['gd_location_filter_on_site_load']))
		{
			$_SESSION['gd_location_filter_on_site_load']=1 ;
			if(get_option('geodir_result_by_location')=='default')
			{
				
				$location_default = geodir_get_default_location();
				$_SESSION['gd_multi_location'] = 1 ; 
				$_SESSION['gd_country'] = isset($location_default->country_slug) ? $location_default->country_slug : '';
				$_SESSION['gd_region'] = isset($location_default->region_slug) ? $location_default->region_slug : '';
				$_SESSION['gd_city'] = isset($location_default->city_slug) ? $location_default->city_slug : '';
				
				$wp->query_vars['gd_country'] = isset($location_default->country_slug) ? $location_default->country_slug : '' ;
				$wp->query_vars['gd_region'] =  isset($location_default->region_slug) ? $location_default->region_slug : '';
				$wp->query_vars['gd_city'] =  isset($location_default->city_slug) ? $location_default->city_slug : ''; 
			}
		}	
		
	}
	else
	{
		$_SESSION['gd_location_filter_on_site_load']=1 ;
	}
	
		
					
	if(isset($_SESSION['gd_multi_location']) && $_SESSION['gd_multi_location']==1)
	{
		$wp->query_vars['gd_country'] =  $_SESSION['gd_country'] ;
		$wp->query_vars['gd_region'] =  $_SESSION['gd_region'] ;
		$wp->query_vars['gd_city'] =  $_SESSION['gd_city'] ;
	}/**/
	
	// now check if there is location parts in the url or not
	if( get_option('geodir_add_location_url'))
	{
		if(get_option('geodir_show_location_url')!='all')
		{
			if(isset($wp->query_vars['gd_country']))
				$wp->query_vars['gd_country']='' ;
		
			if(isset($wp->query_vars['gd_region']))
				$wp->query_vars['gd_region']='' ;
		}
	}
	else
	{
		if(isset($wp->query_vars['gd_country']))
			$wp->query_vars['gd_country']='' ;
		
		if(isset($wp->query_vars['gd_region']))
			$wp->query_vars['gd_region']='' ;
		
		if(isset($wp->query_vars['gd_city']))
			$wp->query_vars['gd_city']='' ;
				
	}
	
	/**/
	//print_r($_SESSION);
	/*
echo "<pre>" ;
	print_r($wp) ;
	echo "</pre>" ;
	exit();
		*/
}	

add_action('pre_get_posts','geodir_listing_loop_location_filter' , 2);
/**
 * Adds location filter to the query.
 *
 * @since 1.0.0
 * @package GeoDirectory_Location_Manager
 *
 * @global object $wp_query WordPress Query object.
 * @global string $plugin_prefix Geodirectory plugin table prefix.
 *
 * @param object $query The WP_Query instance.
 */
function geodir_listing_loop_location_filter( $query ) {
	global $wp_query, $geodir_post_type, $table, $plugin_prefix, $table, $term;
	
	// fix wp_reset_query for popular post view widget
	if ( !geodir_is_geodir_page() ) {
		return;
	}
	
	$apply_location_filter = true ;
	if( isset( $query->query_vars['gd_location'] ) ) {
		$apply_location_filter = $query->query_vars['gd_location'] ? true : false ;
	}
	
	if ( isset( $query->query_vars['is_geodir_loop'] ) && $query->query_vars['is_geodir_loop']  && !is_admin() && !geodir_is_page( 'add-listing' ) && !isset( $_REQUEST['geodir_dashbord'] ) && $apply_location_filter ) {
		geodir_post_location_where(); // this function is in geodir_location_functions.php
	}	
}

/**
 * Filters the where clause.
 *
 * @since 1.0.0
 * @package GeoDirectory_Location_Manager
 */
function geodir_post_location_where()
{
	global $snear;
	if( ( is_search() && $_REQUEST['geodir_search'] )  )
	{
		add_filter('posts_where', 'searching_filter_location_where', 2);		
	
		if($snear!='')
			add_filter('posts_where', 'searching_filter_location_where', 2);		
	}
	
	if( !geodir_is_page('detail') )
		add_filter('posts_where', 'geodir_default_location_where', 2);/**/	

}

/**
 * Adds the location filter to the where clause.
 *
 * @since 1.0.0
 * @package GeoDirectory_Location_Manager
 *
 * @param string $where The WHERE clause of the query.
 * @return string Filtered WHERE clause.
 */
function searching_filter_location_where($where)
{
	global $table;
	$city_where = '';
	// Filter-Location-Manager // City search ..
	if(isset($_REQUEST['scity']))
	{	
		if(is_array($_REQUEST['scity']) && !empty($_REQUEST['scity']))
		{
			$awhere = array();
			foreach($_REQUEST['scity'] as $city)
			{
				//$city_where .= "'".$city."',";
				//$where .= " FIND_IN_SET(".$_REQUEST['scity'].", post_locations), ";
				$awhere[] = " post_locations LIKE '[".$_REQUEST['scity']."],%' ";
			}
			$where .= " ( " . implode( " OR ", $awhere ) ." ) ";
		}
		elseif($_REQUEST['scity'] != ''){
			//$city_where = "'".$_REQUEST['scity']."'";
			//$where .= " FIND_IN_SET(".$_REQUEST['scity'].", post_locations) ";
			$where .= " post_locations LIKE '[".$_REQUEST['scity']."],%' ";
		}
		
		/*if(!empty($city_where))
			$where .= " AND ".POST_LOCATION_TABLE.".city IN ( ". trim($city_where,',') ." ) ";*/
			
	}	
	return $where ;
}



add_action('geodir_filter_widget_listings_fields','geodir_filter_widget_listings_fields_set',10,2);
/**
 * Filters the Field clause of the query.
 *
 * @since 1.0.0
 * @package GeoDirectory_Location_Manager
 *
 * @global object $wpdb WordPress Database object.
 *
 * @param string $fields Fields string.
 * @param string $table Table name.
 * @return string Filtered field clause.
 */
function geodir_filter_widget_listings_fields_set($fields,$table){
	// my location set start
	if(isset($_SESSION['all_near_me'])){
		global $wpdb;
	$mylat = $_SESSION['user_lat'];
	$mylon = $_SESSION['user_lon'];
	$DistanceRadius = geodir_getDistanceRadius(get_option('geodir_search_dist_1'));
	$fields .= $wpdb->prepare(" , (".$DistanceRadius." * 2 * ASIN(SQRT( POWER(SIN((ABS(%s) - ABS(".$table.".post_latitude)) * pi()/180 / 2), 2) +COS(ABS(%s) * pi()/180) * COS( ABS(".$table.".post_latitude) * pi()/180) *POWER(SIN((%s - ".$table.".post_longitude) * pi()/180 / 2), 2) )))as distance ",$mylat,$mylat,$mylon);
	}
	return $fields;	
}


add_action('geodir_filter_widget_listings_orderby','geodir_filter_widget_listings_orderby_set',10,2);
/**
 * Adds the location filter to the orderby clause.
 *
 * @since 1.0.0
 * @package GeoDirectory_Location_Manager
 *
 * @param string $orderby Order by clause string.
 * @param string $table Table name.
 * @return string Filtered Orderby Clause.
 */
function geodir_filter_widget_listings_orderby_set($orderby,$table){
	// my location set start
	if(isset($_SESSION['all_near_me'])){
	$orderby = " distance, ".$orderby;
	}
	return $orderby;	
}


/**
 * Adds the default location filter to the where clause.
 *
 * @since 1.0.0
 * @package GeoDirectory_Location_Manager
 *
 * @global object $wp_query WordPress Query object.
 * @global object $wpdb WordPress Database object.
 * @global string $plugin_prefix Geodirectory plugin table prefix.
 * @global object $wp WordPress object.
 *
 * @param string $where The WHERE clause of the query.
 * @param string $p_table Post table.
 * @return mixed|string Filterd where clause.
 */
function geodir_default_location_where( $where, $p_table = '' ) {
	global $wp_query, $wpdb, $table, $wp, $plugin_prefix;
	
	$allowed_location = apply_filters( 'geodir_location_allowed_location_where', true, $wp->query_vars, $table, $wp_query, $p_table );
	if ( !$allowed_location ) {
		return $where;
	}
	
	// my location set start
	if(isset($_SESSION['all_near_me'])){
		
	$mylat = $_SESSION['user_lat'];
	$mylon = $_SESSION['user_lon'];
	
	if(isset($_SESSION['near_me_range']) && is_numeric($_SESSION['near_me_range'])){$dist =$_SESSION['near_me_range']; }
	elseif(get_option('geodir_near_me_dist')!=''){$dist = get_option('geodir_near_me_dist');}
	else{ $dist = '200';  }
	
	$lon1 = $mylon- $dist/abs(cos(deg2rad($mylat))*69); 
	$lon2 = $mylon+$dist/abs(cos(deg2rad($mylat))*69);
	$lat1 = $mylat-($dist/69);
	$lat2 = $mylat+($dist/69);	
	
	$rlon1 = is_numeric(min($lon1,$lon2)) ? min($lon1,$lon2) : '';
	$rlon2 = is_numeric(max($lon1,$lon2)) ? max($lon1,$lon2) : '';
	$rlat1 = is_numeric(min($lat1,$lat2)) ? min($lat1,$lat2) : '';
	$rlat2 = is_numeric(max($lat1,$lat2)) ? max($lat1,$lat2) : '';
	
	$where .= " AND post_latitude between $rlat1 and $rlat2 
	AND post_longitude between $rlon1 and $rlon2 ";
	return $where;
	}	
	
	$where = str_replace( "0 = 1", "1=1", $where );
	$country = '';
	$region = '';
	$city = '';
	$neighbourhood = '';
	
	if ( isset( $_SESSION['gd_country'] ) && $_SESSION['gd_country'] != '' ) {
		$country = $_SESSION['gd_country'];
	}
	
	if ( $country == '' ) {
		// check if we have country  in query vars
		if ( isset( $wp->query_vars['gd_country'] ) && $wp->query_vars['gd_country'] != '' ) {
			$country = $wp->query_vars['gd_country'];
		}
	}
	
	if ( isset( $_SESSION['gd_region'] ) && $_SESSION['gd_region'] != '' ) {
		$region = $_SESSION['gd_region'];
	}
	
	if ( $region == '' ) {
		// check if we have region in query vars
		if ( isset( $wp->query_vars['gd_region'] ) && $wp->query_vars['gd_region'] != '' ) {
			$region = $wp->query_vars['gd_region'];
		}
	}
		
	if ( isset( $_SESSION['gd_city'] ) && $_SESSION['gd_city'] != '' ) {
		$city = $_SESSION['gd_city'];
	}
	
	if ( $city == '' ) {
		// check if we have city in query vars
		if ( isset($wp->query_vars['gd_city'] ) && $wp->query_vars['gd_city'] != '' ) {
			$city = $wp->query_vars['gd_city'];
		}
	}
	
	
	$neighbourhood = get_query_var( 'gd_neighbourhood' );
	if ( empty( $neighbourhood ) ) {	
		if ( isset( $wp->query_vars['gd_neighbourhood'] ) && $wp->query_vars['gd_neighbourhood'] != '' ) {
			$neighbourhood = $wp->query_vars['gd_neighbourhood'];
		}
	}	
	
	// added for map calls
	if ( empty( $neighbourhood ) ) {	
		if ( isset( $_REQUEST['gd_neighbourhood'] ) && $_REQUEST['gd_neighbourhood'] != '' ) {
			$neighbourhood = $_REQUEST['gd_neighbourhood'];
					if ( isset( $_REQUEST['gd_posttype'] ) && $_REQUEST['gd_posttype'] != '' ) {
						$p_table = "pd";
					}
		}
	}
	
	$format = "''";
	if ( is_array( $neighbourhood ) && !empty( $neighbourhood ) ) {
		$neighbourhood_length = count( $neighbourhood );
		$format = array_fill( 0, $neighbourhood_length, '%s' );
		$format = implode( ',', $format );
		
	} else if( !is_array( $neighbourhood ) && !empty( $neighbourhood ) ) {
		$format = "%s";
		$neighbourhood = array( $neighbourhood );
	}
		
	if ( $country != '' ) {
		//$where .= " AND FIND_IN_SET('[".$country."]', post_locations) ";
		$where .= " AND post_locations LIKE '%,[".$country."]' ";
	}
	
	if ( $region != '' ) {
		//$where .= " AND FIND_IN_SET('[".$region."]', post_locations) ";
		$where .= " AND post_locations LIKE '%,[".$region."],%' ";
	}
	
	if ( $city != '' ) {
		//$where .= " AND FIND_IN_SET('[".$city."]', post_locations) ";
		$where .= " AND post_locations LIKE '[".$city."],%' ";
	}

	if ( $neighbourhood != '' ) {
		$post_table = $table != '' ? $table . '.' : ''; /* fixed db error when $table is not set */
		if(!empty($p_table)){$post_table = $p_table. '.';}
		$where .= $wpdb->prepare( " AND " . $post_table . "post_neighbourhood IN ($format) " , $neighbourhood );
	}

	return $where;
}

/**************************
/* LOCATION AJAX Handler
***************************/		
add_action('wp_ajax_geodir_location_ajax' , 'geodir_location_ajax_handler') ; // it in geodir_location_functions.php 
add_action('wp_ajax_nopriv_geodir_location_ajax' , 'geodir_location_ajax_handler') ;
// AJAX Handler //
/**
 * Handles ajax request.
 *
 * @since 1.0.0
 * @package GeoDirectory_Location_Manager
 */
function geodir_location_ajax_handler()
{
	
 if(isset($_REQUEST['gd_loc_ajax_action']) &&  $_REQUEST['gd_loc_ajax_action'] != '')
 {
 	switch($_REQUEST['gd_loc_ajax_action'])
	{
		case 'get_location' :
			if(isset($_REQUEST['gd_which_location']) && $_REQUEST['gd_which_location'] != '')
			{
				$city_val = '';
				$region_val = '';
				$country_val = '';
				
				if(isset($_REQUEST['gd_city_val']) && $_REQUEST['gd_city_val'] != '')
					$city_val =  $_REQUEST['gd_city_val'];
					
				if(isset($_REQUEST['gd_region_val']) && $_REQUEST['gd_region_val'] != '')
					$region_val =  $_REQUEST['gd_region_val'];
				
				if(isset($_REQUEST['gd_country_val']) && $_REQUEST['gd_country_val'] != '')
					$country_val =  $_REQUEST['gd_country_val'];
					
				if(isset($_REQUEST['spage']) && $_REQUEST['spage'] != ''){
					$spage =  $_REQUEST['spage'];}
					else{$spage = '';}
					
				if(isset($_REQUEST['lscroll']) && $_REQUEST['lscroll'] != ''){
					$no_of_records = '5';  
				}else{$no_of_records = '';}
						
				$location_args =array(  'what' => $_REQUEST['gd_which_location'] ,
										'city_val' => $city_val, 
										'region_val' => $region_val ,
										'country_val' => $country_val,
										'compare_operator' =>'like' ,
										'country_column_name' => 'country' ,
										'region_column_name' => 'region' ,
										'city_column_name' => 'city' ,
										'location_link_part' => true , 
										'order_by' => ' asc ',
										'no_of_records' => $no_of_records,
										'format' => array('type' => 'array'),
										'spage' => $spage
									);
				$location_array =  geodir_get_location_array($location_args);
				if(isset($_REQUEST['gd_formated_for']) && $_REQUEST['gd_formated_for'] == 'location_switcher')
				{
					$base_location_link = geodir_get_location_link('base');
					if(!empty($location_array))
					{
						if($_REQUEST['gd_which_location']=='city')
							$arrow_html = '' ;
						else
							$arrow_html = '<span class="geodir_loc_arrow"><a href="javascript:void(0);">&nbsp;</a></span>' ;
						foreach($location_array as $location_item)
						{
							$location_name = $_REQUEST['gd_which_location'] == 'country' ? __( $location_item->$_REQUEST['gd_which_location'], GEODIRECTORY_TEXTDOMAIN ) : $location_item->$_REQUEST['gd_which_location'];
							
							echo "<li class=\"geodir_loc_clearfix\"><a href='" . geodir_location_permalink_url( $base_location_link . $location_item->location_link ) . "' >" . $location_name . "</a>$arrow_html</li>"  ;
						}
					}
					else
						echo _e("No Results" , GEODIRLOCATION_TEXTDOMAIN);
				}
				else
					print_r($location_array);
				exit();
			}
			break;
		case 'fill_location': {
			$gd_which_location = isset( $_REQUEST['gd_which_location'] ) ? trim( $_REQUEST['gd_which_location'] ) : '';
			$term = isset( $_REQUEST['term'] ) ? trim( $_REQUEST['term'] ) : '';
			
			$base_location = geodir_get_location_link( 'base' );
			$current_location_array;
			$selected = ''; 
			$country_val = '';
			$region_val = '';
			$city_val = '';
			$country_val = geodir_get_current_location( array( 'what' => 'country', 'echo' => false ) );
			$region_val = geodir_get_current_location( array( 'what' => 'region', 'echo' => false ) );
			$city_val = geodir_get_current_location( array( 'what' => 'city', 'echo' => false ) );
			$item_set_selected = false;
			
			if(isset($_REQUEST['spage']) && $_REQUEST['spage'] != ''){
					$spage =  $_REQUEST['spage'];}
					else{$spage = '';}
					
			if(isset($_REQUEST['lscroll']) && $_REQUEST['lscroll'] != ''){
				$no_of_records = '5';  
			}else{$no_of_records = '';}
			
			$location_switcher_list_mode = get_option( 'geodir_location_switcher_list_mode' );
			if( empty( $location_switcher_list_mode ) ) {
				$location_switcher_list_mode = 'drill';
			}
			
			if( $location_switcher_list_mode == 'drill' ) {
				$args = array(
							'what' => $gd_which_location, 
							'country_val' => ( strtolower( $gd_which_location ) == 'region' || strtolower( $gd_which_location ) =='city' ) ? $country_val : '',
							'region_val' => ( strtolower( $gd_which_location ) == 'city' ) ? $region_val : '',
							'echo' => false,
							'no_of_records' => $no_of_records,
							'format' => array('type' => 'array'),
							'spage' => $spage
						);
			} else {
				$args = array(
							'what' => $gd_which_location , 
							'echo' => false,
							'no_of_records' => $no_of_records,
							'format' => array('type' => 'array'),
							'spage' => $spage
						);
			}
			
			if( $term != '' ) {
				if( $gd_which_location == 'city' ) {
					$args['city_val'] = $term;
				}
				
				if( $gd_which_location == 'region' ) {
					$args['region_val'] = $term;
				}
				
				if( $gd_which_location == 'country' ) {
					$args['country_val'] = $term;
				}
			} else {
				if( $gd_which_location == 'country' && $country_val != '' ) {
					$args_current_location = array(
												'what' => $gd_which_location, 
												'country_val' => $country_val,
												'compare_operator' => '=',
												'no_of_records' => '1',
												'echo' => false,
												'format' => array( 'type' => 'array' )
											);
					$current_location_array = geodir_get_location_array( $args_current_location, true );
				}
				
				if( $gd_which_location == 'region' && $region_val != '' ) {
					$args_current_location = array(
												'what' => $gd_which_location, 
												'country_val' => $country_val,
												'region_val' => $region_val,
												'compare_operator' => '=',
												'no_of_records' => '1',
												'echo' => false,
												'format' => array( 'type' => 'array' )
											);
					$current_location_array = geodir_get_location_array( $args_current_location, true );
				}
				
				if( $gd_which_location == 'city' && $city_val != '' ) {
					$args_current_location = array(
												'what' => $gd_which_location, 
												'country_val' => $country_val,
												'region_val' =>$region_val,
												'city_val' => $city_val,
												'compare_operator' => '=',
												'no_of_records' => '1',
												'echo' => false,
												'format'=> array( 'type' => 'array' )
											);
					$current_location_array = geodir_get_location_array( $args_current_location, true );
				}
				// if not searching then set to get exact matches
				$args['compare_operator']='in';
			}


			$location_array = geodir_get_location_array( $args, true );
			// get country val in case of country search to get selected option
			
			if( get_option( 'geodir_everywhere_in_' . $gd_which_location . '_dropdown' ) && !isset($_REQUEST['lscroll']) ) {
				echo  '<option value="' . $base_location . '">' . __( 'Everywhere', GEODIRLOCATION_TEXTDOMAIN ) . '</option>';
			}	
				
			$selected = '' ; 
			$loc_echo = '';
			if( !empty( $location_array ) ) {
				foreach( $location_array as $locations ) {
					$selected = '' ; 
					$with_parent = isset( $locations->label ) ? true : false;
					switch( $gd_which_location ) {
						case 'country':
							if( strtolower( $country_val ) == strtolower( $locations->country ) ) {
								$selected = 'selected="selected"';
							}
							$locations->country = __( $locations->country, GEODIRECTORY_TEXTDOMAIN );
						break;
						case 'region':
							$country_iso2 = geodir_location_get_iso2( $country_val );
							$country_iso2 = $country_iso2 != '' ? $country_iso2 : $country_val;
							$with_parent = $with_parent && strtolower( $region_val . ', ' . $country_iso2 ) == strtolower( $locations->label ) ? true : false;
							if( strtolower( $region_val ) == strtolower( $locations->region ) || $with_parent ) {
								$selected = 'selected="selected"';
							}
						break;
						case 'city':
							$with_parent = $with_parent && strtolower( $city_val . ', ' . $region_val ) == strtolower( $locations->label ) ? true : false;
							if( strtolower( $city_val ) == strtolower( $locations->city ) || $with_parent ) {
								$selected = 'selected="selected"';
							}
						break;		
					}
					
					echo '<option value="' . geodir_location_permalink_url( $base_location . $locations->location_link ) . '" ' . $selected . '>' . ucwords( $locations->$gd_which_location ) . '</option>';
					
					if( !$item_set_selected && $selected != '' ) {
						$item_set_selected = true;
					}
				}
			}
			
			if( !empty( $current_location_array ) && !$item_set_selected && !isset($_REQUEST['lscroll'])) {
				foreach( $current_location_array as $current_location ) {
					$selected = '' ; 
					$with_parent = isset( $current_location->label ) ? true : false;
					switch( $gd_which_location ) {
						case 'country':
							if( strtolower( $country_val ) == strtolower( $current_location->country ) ) {
								$selected = 'selected="selected"';
							}
							$current_location->country = __( $current_location->country, GEODIRECTORY_TEXTDOMAIN );
						break;
						case 'region':
							$country_iso2 = geodir_location_get_iso2( $country_val );
							$country_iso2 = $country_iso2 != '' ? $country_iso2 : $country_val;
							$with_parent = $with_parent && strtolower( $region_val . ', ' . $country_iso2 ) == strtolower( $current_location->label ) ? true : false;
							if( strtolower( $region_val ) == strtolower( $current_location->region ) || $with_parent ) {
								$selected = 'selected="selected"';
							}
						break;
						case 'city':
							$with_parent = $with_parent && strtolower( $city_val . ', ' . $region_val ) == strtolower( $current_location->label ) ? true : false;
							if( strtolower( $city_val ) == strtolower( $current_location->city ) || $with_parent ) {
								$selected = 'selected="selected"';
							}
						break;			
					}
					
					echo '<option value="' . geodir_location_permalink_url( $base_location . $current_location->location_link ) . '" ' . $selected . '>' . ucwords( $current_location->$gd_which_location ) . '</option>';
				}
			}
			exit;
		}
		break;
		case 'fill_location_on_add_listing' :
			$selected = '' ; 
			$country_val=(isset($_REQUEST['country_val'])  ) ?  $_REQUEST['country_val'] : '';
			$region_val=(isset($_REQUEST['region_val'])  ) ?  $_REQUEST['region_val'] : '';
			$city_val =(isset($_REQUEST['city_val'])  ) ?  $_REQUEST['city_val'] : '';
			$compare_operator =(isset($_REQUEST['compare_operator'])  ) ?  $_REQUEST['compare_operator'] : '=';
			
			if(isset($_REQUEST['term']) && $_REQUEST['term']!='')
			{
				if($_REQUEST['gd_which_location'] =='region')
				{
					$region_val = $_REQUEST['term'];
					$city_val = '';
				}
				else if($_REQUEST['gd_which_location'] =='city')
				{
					$city_val = $_REQUEST['term'];
				}
			}
			
			if($_REQUEST['gd_which_location'] !='neighbourhood')
			{
				$args=array(
							'what'=>$_REQUEST['gd_which_location'] , 
							'country_val' => (strtolower($_REQUEST['gd_which_location'])=='region' || strtolower($_REQUEST['gd_which_location'])=='city') ? $country_val : '',
							'region_val' =>$region_val ,
							'city_val' =>$city_val ,
							'echo' => false,
							'compare_operator' => $compare_operator,
							'format'=> array('type'=>'array')
							);
				$location_array= geodir_get_location_array($args);
			}
			else
			{
			
				geodir_get_neighbourhoods_dl($city_val) ;
				exit();
			}	
			
			// get country val in case of country search to get selected option
			
			if($_REQUEST['gd_which_location']=='region')
				echo  '<option  value="" >'.__('Select Region', GEODIRLOCATION_TEXTDOMAIN).'</option>';	
			else
				echo  '<option  value="" >'.__('Select City', GEODIRLOCATION_TEXTDOMAIN).'</option>';	
			
			if(!empty($location_array))
			{
				foreach( $location_array as $locations)
				{
					$selected = '' ; 
					switch($_REQUEST['gd_which_location'] )
					{
						case 'country':
							if(strtolower($country_val)== strtolower($locations->country))
								$selected=" selected='selected' ";
							break;
						case 'region':
							if(strtolower($region_val)== strtolower($locations->region))
								$selected=" selected='selected' ";
							break;
						case 'city':
							if(strtolower($city_val)== strtolower($locations->city))
								$selected=" selected='selected' ";	
							break;
									
					}
					echo '<option '.$selected .' value="'.ucwords($locations->$_REQUEST['gd_which_location']).'" >'.ucwords($locations->$_REQUEST['gd_which_location']).'</option>' ;
				}
				
			}
			else
			{
				if(isset($_REQUEST['term']) && $_REQUEST['term']!='')
					echo '<option  value="'.$_REQUEST['term'].'" >'.$_REQUEST['term'].'</option>' ;
			}
			exit();
			break;
	}
 }	
}
// AJAX Handler ends//

/**************************
/* LOCATION SWITCHER IN NAV 
***************************/
add_filter('wp_page_menu','geodir_location_pagemenu_items',110,2);
add_filter('wp_nav_menu_items','geodir_location_menu_items', 110, 2);
/**
 * Filters the HTML output of a page-based menu.
 *
 * @since 1.0.0
 * @package GeoDirectory_Location_Manager
 *
 * @param string $menu The HTML output.
 * @param array  $args An array of arguments.
 * @return string Filtered HTML.
 */
function geodir_location_pagemenu_items($menu, $args)
{

	$locations = get_nav_menu_locations();
	$geodir_theme_location = get_option('geodir_theme_location_nav');
	$geodir_theme_location_nav = array();
	if ( empty( $locations) &&  empty($geodir_theme_location))
	{
		$menu = str_replace("</ul></div>",add_nav_location_menu_items()."</ul></div>",$menu);
		$geodir_theme_location_nav[] = $args['theme_location'] ;
		update_option('geodir_theme_location_nav' , $geodir_theme_location_nav);
	}
	//else if(empty($geodir_theme_location)) // It means 'Show geodirectory navigation in selected menu locations' is not set yet.
//		$menu = str_replace("</ul></div>",geodir_add_nav_menu_items()."</ul></div>",$menu);
	else  if (  is_array($geodir_theme_location) && in_array($args['theme_location'],$geodir_theme_location) )
		$menu = str_replace("</ul></div>",add_nav_location_menu_items()."</ul></div>",$menu);
	
	return $menu;
	
}

/**
 * Filter the HTML output of navigation menus.
 *
 * @since 1.0.0
 * @package GeoDirectory_Location_Manager
 *
 * @param string $items The HTML list content for the menu items.
 * @param object $args  An object containing wp_nav_menu() arguments.
 * @return string Filtered HTML.
 */
function geodir_location_menu_items($items, $args)
{
	
	$location = $args->theme_location;
	
	$geodir_theme_location = get_option('geodir_theme_location_nav');
	
	if ( has_nav_menu( $location )=='1' && is_array($geodir_theme_location) && in_array($location,$geodir_theme_location) ) {
		
		$items = $items.add_nav_location_menu_items();
		
	}
	
	return $items;
}

/**
 * Adds location items to the menu.
 *
 * @since 1.0.0
 * @package GeoDirectory_Location_Manager
 *
 * @return string HTML.
 */
function add_nav_location_menu_items()
{
	$items = '';
	
	if(get_option('geodir_show_changelocation_nave')){
		
		$current_location = geodir_get_current_location(array('echo'=> false));
		if(empty($current_location)){
			$current_location = CHANGE_LOCATION;
		}
		$current_location_link = geodir_get_location_link();
		
		$li_class = apply_filters('geodir_location_switcher_menu_li_class' ,'menu-item menu-item-type-social menu-item-type-social gd-location-switcher' );
		$a_class = apply_filters('geodir_location_switcher_menu_a_class' ,'' );
		$sub_ul_class = apply_filters('geodir_location_switcher_menu_sub_ul_class' ,'sub-menu' );
		$sub_li_class = apply_filters('geodir_location_switcher_menu_sub_li_class' ,'menu-item gd-location-switcher-menu-item' );
		
		$items .= '<li id="menu-item-gd-location-switcher" class="'.$li_class.'">';
		$items .= 	'<a href="#" class="'.$a_class.'"><i class="fa fa-map-marker"></i> '.__( $current_location, GEODIRECTORY_TEXTDOMAIN ).'</a>'; // link replaced with # for better mobile support
						
		$items .= 	'<ul class="'.$sub_ul_class.'">';
		$items .= 	'<li class="'.$sub_li_class.'">';
				$args = array('echo' => false, 'addSearchTermOnNorecord' => 0, 'autoredirect'=> true);
				$items .= geodir_location_tab_switcher($args);	
			
		$items .= 	'</li>';	
		$items .= 	'</ul>';
		$items .= '</li>';				
	}
	
	return $items;
}	



/**************************
/* Filters and Actions for other adons 
***************************/	
add_filter('geodir_breadcrumb','geodir_location_breadcrumb', 1, 2);
	
	
add_filter('geodir_add_listing_map_restrict', 'geodir_remove_listing_map_restrict', 1, 1);
/**
 * Allow marker to be dragged beyond the range of default city when Multilocation is enabled.
 *
 * @since 1.0.0
 * @package GeoDirectory_Location_Manager
 *
 * @param bool $restrict Whether to restrict the map?
 * @return bool
 */
function geodir_remove_listing_map_restrict($restrict)
{
	return $restrict = false;
}

add_filter('geodir_home_map_enable_location_filters', 'geodir_home_map_enable_location_filters', 1);
/**
 * Enable location filter on home page.
 *
 * @since 1.0.0
 * @package GeoDirectory_Location_Manager
 *
 * @param bool $enable True if location filters should be used, false if not.
 * @return bool
 */
function geodir_home_map_enable_location_filters($enable)
{
	return $enable = true;
}

add_filter('geodir_home_map_listing_where', 'geodir_default_location_where', 1);
add_filter('geodir_cat_post_count_where', 'geodir_default_location_where', 1, 2);

add_action( 'geodir_create_new_post_type', 'geodir_after_custom_detail_table_create', 1, 2 );
/**
 * Add nightbourhood column in custom post detail table.
 *
 * @since 1.0.0
 * @package GeoDirectory_Location_Manager
 *
 * @global object $wpdb WordPress Database object.
 * @global string $plugin_prefix Geodirectory plugin table prefix.
 *
 * @param string $post_type The post type.
 * @param string $detail_table The deatil table name.
 */
function geodir_after_custom_detail_table_create($post_type, $detail_table='')
{
	global $wpdb,$plugin_prefix;
	$post_types = geodir_get_posttypes();
	if($detail_table == '')
		$detail_table = $plugin_prefix . $post_type . '_detail';
	
	if(in_array($post_type, $post_types)){
		$meta_field_add = "VARCHAR( 30 ) NULL";
		geodir_add_column_if_not_exist( $detail_table, "post_neighbourhood", $meta_field_add );
	}
}


add_action('geodir_address_extra_listing_fields', 'geodir_location_address_extra_listing_fields', 1, 1);
/**
 * This is used to put country , region , city and neighbour dropdown on add/edit listing page.
 *
 * @since 1.0.0
 * @package GeoDirectory_Location_Manager
 *
 * @global object $wpdb WordPress Database object.
 * @global string $plugin_prefix Geodirectory plugin table prefix.
 *
 * @param array $val The array of setting for the custom field.
 */
function geodir_location_address_extra_listing_fields($val)
{
	$name = $val['name'];
	$site_title = $val['site_title'];
	$type = $val['type'];
	$admin_desc = $val['desc'];
	$option_values = $val['option_values'];
	$is_required = $val['is_required'];
	$is_default =  $val['is_default'];
	$is_admin =  $val['is_admin'];
	$required_msg = $val['required_msg'];
	$extra_fields = unserialize($val['extra_fields']);
	
	$prefix = $name.'_';
	
	($extra_fields['city_lable'] != '') ? $city_title = $extra_fields['city_lable'] : $city_title = ucwords($prefix.' city');
	($extra_fields['region_lable'] != '') ? $region_title = $extra_fields['region_lable'] : $region_title = ucwords($prefix.' region');
	($extra_fields['country_lable'] != '') ? $country_title = $extra_fields['country_lable'] : $country_title = ucwords($prefix.' country');
	
	$city = '';
	$region = '';
	$country = '';
	$neighbourhood = '';
	
	if(isset($_REQUEST['backandedit']) &&  $_REQUEST['backandedit'] && isset($_SESSION['listing']) )
	{ 
	
		$post = unserialize($_SESSION['listing']);
		$city = $post[$prefix.'city'];
		$region = $post[$prefix.'region'];
		$country = $post[$prefix.'country'];
		$neighbourhood = isset($post[$prefix.'neighbourhood']) ? $post[$prefix.'neighbourhood'] : '';
		
	}
	elseif( isset($_REQUEST['pid']) && $_REQUEST['pid']!='' && $post_info = geodir_get_post_info($_REQUEST['pid']) )
	{ 
		
		$post_info = (array)$post_info;
		$city = $post_info[$prefix.'city'];
		$region = $post_info[$prefix.'region'];
		$country = $post_info[$prefix.'country'];
		
		if(isset($post_info[$prefix.'neighbourhood']))
			$neighbourhood = $post_info[$prefix.'neighbourhood'];
		
	}
	elseif(isset($_SESSION['gd_multi_location']))
	{
	
		if(isset($_SESSION['gd_city']) && $_SESSION['gd_city'] != '' )
			$location = geodir_get_locations('city',$_SESSION['gd_city']);
		elseif(isset($_SESSION['gd_region']) && $_SESSION['gd_region'] != '' )
			$location = geodir_get_locations('region',$_SESSION['gd_region']);
		elseif(isset($_SESSION['gd_country']) && $_SESSION['gd_country'] != '' )
			$location = geodir_get_locations('country',$_SESSION['gd_country']);		
		
		if(isset($location) && $location)
			$location = end($location);
		
		$city = isset($location->city) ? $location->city : '';
		$region = isset($location->region) ? $location->region : '';
		$country = isset($location->country) ? $location->country : '';
		
	}
		
	$location = geodir_get_default_location();
	if(empty($city)) $city = isset($location->city) ? $location->city : '';
	if(empty($region)) $region = isset($location->region) ? $location->region : '';
	if(empty($country)) $country = isset($location->country) ? $location->country : '';
	
	
	?>
    <div id="geodir_add_listing_all_chosen_container_row" class="geodir_location_add_listing_all_chosen_container" >
    <?php
		
	
	if(get_option('geodir_enable_country') == 'default'){
		global $wpdb;
		$countries_ISO2 =	$wpdb->get_var($wpdb->prepare("SELECT ISO2 FROM ".GEODIR_COUNTRIES_TABLE." WHERE Country=%s",$country));
		?><input type="hidden" name="geodir_location_add_listing_country_val" value="<?php echo $country ;?>" />
		<input type="hidden" id="<?php echo $prefix ?>country" data-country_code="<?php echo $countries_ISO2;?>" name="<?php echo $prefix ?>country" value="<?php echo $country;?>" />
		<?php
	
	}else{
	
		if($extra_fields['show_country']) 
		{
			
		?>   
			<div id="geodir_<?php echo $prefix.'country';?>_row" class="<?php if($is_required) echo 'required_field';?> geodir_form_row  geodir_location_add_listing_country_chosen_container clearfix">
				<label>

					<?php _e($country_title , GEODIRLOCATION_TEXTDOMAIN);?>
					<?php if($is_required) echo '<span>*</span>';?>
				</label>
				 <div class="geodir_location_add_listing_country_chosen_div" style="width:57%; float:left;">
							 <input type="hidden" name="geodir_location_add_listing_country_val" value="<?php echo $country ;?>" />
							 
				<select id="<?php echo $prefix ?>country" class="geodir_location_add_listing_chosen" data-location_type="country" name="<?php echo $prefix ?>country"  data-placeholder="<?php _e('Choose a country.', GEODIRLOCATION_TEXTDOMAIN) ;?>" data-addsearchtermonnorecord="1" data-ajaxchosen="0" data-autoredirect="0" data-showeverywhere="0" >
				<?php if(get_option('geodir_enable_country') == 'multi')
							geodir_get_country_dl($country,$prefix); 
					  else if(get_option('geodir_enable_country') == 'selected')
					  {
					  		geodir_get_limited_country_dl($country,$prefix); 	
					  }
				?>
				</select>
		
				</div>
							<span class="geodir_message_note"><?php _e('Click on above field and type to filter list' , GEODIRLOCATION_TEXTDOMAIN)?></span>
				<?php if($is_required) 
				{?>
					<span class="geodir_message_error"><?php echo $required_msg?></span> 
			<?php } ?>
			</div>
		<?php 
		} 
	
	}  // end of show country if
	
	
	if(get_option('geodir_enable_region') == 'default'){
		?><input type="hidden" name="geodir_location_add_listing_region_val" value="<?php echo $region ;?>" />
			<input type="hidden" id="<?php echo $prefix ?>region" name="<?php echo $prefix ?>region" value="<?php echo $region;?>" />
		<?php
	}else{
		
		if($extra_fields['show_region']) 
		{?>   
		<div id="geodir_<?php echo $prefix.'region';?>_row" class="<?php if($is_required) echo 'required_field';?> geodir_form_row  geodir_location_add_listing_region_chosen_container clearfix">
			<label>
				<?php _e($region_title , GEODIRLOCATION_TEXTDOMAIN);?>
				<?php if($is_required) echo '<span>*</span>';?>
			</label>
			<div class="geodir_location_add_listing_region_chosen_div" style="width:57%; float:left;">
					<input type="hidden" name="geodir_location_add_listing_region_val" value="<?php echo $region ;?>" />
				<select id="<?php echo $prefix ?>region" class="geodir_location_add_listing_chosen"  data-location_type="region" name="<?php echo $prefix ?>region" data-placeholder="<?php _e('Please wait..&hellip;', GEODIRLOCATION_TEXTDOMAIN) ;?>" <?php if(get_option('geodir_enable_region') == 'selected') {?>
				data-ajaxchosen="0" <?php } else { ?> data-ajaxchosen="1" <?php } ?> data-addsearchtermonnorecord="1" data-autoredirect="0"  >
		<?php 	
				$selected = '' ; 
				
				$args=array(
								'what'=>'region' , 
								'country_val' => $country,
								'region_val' => '',
								'echo' => false,
								'format'=> array('type'=>'array')
							);
				
				if(get_option('location_dropdown_all')){$args['no_of_records']='10000';} // set limit to 10 thouseand as this is most browsers limit
				$location_array= geodir_get_location_array($args);
				// get country val in case of country search to get selected option
				?>
							<option  value='' ><?php _e('Select State', GEODIRLOCATION_TEXTDOMAIN);?></option>	
				<?php
				if(!empty($location_array))
				{
					foreach( $location_array as $locations)
					{
						$selected = '' ; 
						if(strtolower($region)== strtolower($locations->region))
							$selected=" selected='selected' ";
							
				?>
						<option <?php echo $selected ;?> value="<?php echo $locations->region ;?>" ><?php echo ucwords($locations->region);?></option>
				<?php
							}
				}				
		?>				
			</select>
	
			</div>
						<span class="geodir_message_note"><?php _e('Click on above field and type to filter list or add a new region' , GEODIRLOCATION_TEXTDOMAIN)?></span>
			<?php if($is_required) {?>
				<span class="geodir_message_error"><?php echo $required_msg?></span> 
			<?php } ?>
		</div>
		<?php 
		} 
	
	} //end of show region 
	
	
	if(get_option('geodir_enable_city') == 'default'){
		 ?><input type="hidden" name="geodir_location_add_listing_city_val" value="<?php echo $city;?>" />
		 <input type="hidden" id="<?php echo $prefix ?>city" name="<?php echo $prefix ?>city" value="<?php echo $city;?>" />
		 <?php
	}else{
		
		if($extra_fields['show_city']) 
		{?>   
		<div id="geodir_<?php echo $prefix.'city';?>_row" class="<?php if($is_required) echo 'required_field';?> geodir_form_row  geodir_location_add_listing_city_chosen_container clearfix">
			<label> 
				<?php _e($city_title , GEODIRLOCATION_TEXTDOMAIN);?>
				<?php if($is_required) echo '<span>*</span>';?>
			</label>
			
			<div  class="geodir_location_add_listing_city_chosen_div" style="width:57%; float:left;">
				 <input type="hidden" name="geodir_location_add_listing_city_val" value="<?php echo $city;?>" />
				<select id="<?php echo $prefix ?>city" class="geodir_location_add_listing_chosen" data-location_type="city" name="<?php echo $prefix ?>city" data-placeholder="<?php _e('Please wait..&hellip;', GEODIRLOCATION_TEXTDOMAIN) ;?>" <?php if(get_option('geodir_enable_city') == 'selected') {?>
				data-ajaxchosen="0" <?php } else { ?> data-ajaxchosen="1" <?php } ?>  data-addsearchtermonnorecord="1" data-autoredirect="0"  >
		<?php 	
				$selected = '' ; 
				
				$args=array(
								'what'=>'city' , 
								'country_val' => $country,
								'region_val' => $region,
								'echo' => false,
								'format'=> array('type'=>'array')
							);
				
				if(get_option('location_dropdown_all')){$args['no_of_records']='10000';} // set limit to 10 thouseand as this is most browsers limit
				$location_array= geodir_get_location_array($args);
				
				// get country val in case of country search to get selected option
				?>
				<option  value='' ><?php _e('Select City', GEODIRLOCATION_TEXTDOMAIN);?></option>	
				<?php 
				if(!empty($location_array))
				{
					foreach( $location_array as $locations)
					{
						$selected = '' ; 
						if(strtolower($city)== strtolower($locations->city))
							$selected=" selected='selected' ";
							
				?>
						<option <?php echo $selected ;?> value="<?php echo $locations->city ;?>" ><?php echo ucwords($locations->city);?></option>
				<?php
							}
				}				
		?>				
			</select>
			</div>
					<span class="geodir_message_note"><?php _e('Click on above field and type to filter list or add a new city' , GEODIRLOCATION_TEXTDOMAIN)?></span>
			<?php if($is_required)
			{?>
				<span  class="geodir_message_error"><?php echo $required_msg?></span> 
			<?php
				} ?>
		</div>
		<?php 
    }
	 
	} // end of show city if
			 
	if(get_option( 'location_neighbourhoods' ) && $is_admin == '1')
	{ 
		global $plugin_prefix;
		
		$neighbourhood_options = geodir_get_neighbourhoods_dl(esc_attr(stripslashes($city)), $neighbourhood, false);
		
		$neighbourhood_display = '';
		if(trim($neighbourhood_options) == '')
			$neighbourhood_display = 'style="display:none;"';

	?>
	<div id="geodir_<?php echo $prefix.'neighbourhood';?>_row" class="geodir_form_row  geodir_location_add_listing_neighbourhood_chosen_container clearfix" <?php echo $neighbourhood_display;?>   >
		<label><?php _e('Neighbourhood',GEODIRLOCATION_TEXTDOMAIN);?></label>
        
		<div  class="geodir_location_add_listing_neighbourhood_chosen_div" style="width:57%; float:left;">
			<select name="<?php echo $prefix.'neighbourhood';?>" class="chosen_select" option-ajaxChosen="false" >
			 <?php echo $neighbourhood_options; ?>
			</select>
		</div>
        <span class="geodir_message_note"><?php _e('Click on above field and type to filter list' , GEODIRLOCATION_TEXTDOMAIN)?></span>
	</div>
	<?php 
	
	} 
	?>
    </div  ><!-- end of geodir_location_add_listing_all_chosen_container -->
    <?php
			
}


/**************************
/* DATABASE OPERATION RELATED FILTERS AND ACTIONS 
***************************/
add_filter('geodir_get_location_by_id' , 'geodir_get_location_by_id', 1, 2); // this function is in geodir_location_functions.php
add_filter('geodir_default_latitude', 'geodir_location_default_latitude',1,2);
add_filter('geodir_default_longitude', 'geodir_location_default_longitude',1,2);

add_action('geodir_after_save_listing', 'geodir_save_listing_location', 2, 3);
/**
 * Action to save location related information in post type detail table on add/edit new listing action.
 *
 * @since 1.0.0
 * @package GeoDirectory_Location_Manager
 *
 * @global object $wpdb WordPress Database object.
 *
 * @param int $last_post_id The saved post ID.
 * @param array $request_info The post details in an array.
 */
function geodir_save_listing_location($last_post_id,$request_info)
{
	global $wpdb;
	$location_info = array();
	if(isset($request_info['post_neighbourhood']))
	{
		$location_info['post_neighbourhood'] = $request_info['post_neighbourhood'];
	}
	
	if(isset($request_info['post_city']) && isset($request_info['post_region']))
	{

		$post_location_id = geodir_get_post_meta($last_post_id, 'post_location_id', true);
		
		$post_location = geodir_get_location_by_id('', $post_location_id);
		
		$location_info['post_locations'] =  '['.$post_location->city_slug.'],['.$post_location->region_slug.'],['.$post_location->country_slug.']'; // set all overall post location
		
	}

	if(!empty($location_info))
		geodir_save_post_info($last_post_id, $location_info);
}



add_action('geodir_add_new_location', 'geodir_add_new_location_via_adon', 1, 1); 
// this action is defined in geodirectory core plugin and geodir_add_new_location_via_adon geodir_location_functions.php
	
add_action('geodir_get_new_location_link', 'geodir_get_new_location_link', 1, 3);	

	
add_action('geodir_address_extra_admin_fields', 'geodir_location_address_extra_admin_fields', 1, 2);

add_filter('geodir_auto_change_map_fields', 'geodir_location_auto_change_map_fields', 1, 1);
	
add_action('geodir_update_marker_address', 'geodir_location_update_marker_address', 1, 1);

add_action('geodir_add_listing_js_start', 'geodir_location_autofill_address', 1, 1);
	
add_filter('geodir_codeaddress', 'geodir_location_codeaddress', 1, 1);


/**
 * Change the address code when add neighbourhood request.
 *
 * @since 1.0.0
 * @package GeoDirectory_Location_Manager
 *
 * @param string $codeAddress Row of address to use in google map.
 * @return string Filtered codeAddress.
 */
function geodir_location_codeaddress($codeAddress)
{
	
	if(isset($_REQUEST['add_hood']) && $_REQUEST['add_hood'] != ''){ 
		
		ob_start();?>
		address = jQuery("#hood_name").val();
		<?php $codeAddress = ob_get_clean();
		
	}
	return $codeAddress;
}

/**
 * Set auto change map fields to false when add neighbourhood request.
 *
 * @since 1.0.0
 * @package GeoDirectory_Location_Manager
 *
 * @param bool $change Whether to auto fill country, state, city values in fields.
 * @return bool
 */
function geodir_location_auto_change_map_fields($change)
{
	
	if(isset($_REQUEST['add_hood']) && $_REQUEST['add_hood'] != ''){
		$change = false;
	}
	return $change;
}

add_filter('geodir_googlemap_script_extra' , 'geodir_location_map_extra',1,1);
/**
 * Add map extras.
 *
 * @since 1.0.0
 * @package GeoDirectory_Location_Manager
 *
 * @param string $prefix The string to filter, default is empty string.
 * @return string
 */
function geodir_location_map_extra($prefix='')
{
	global $pagenow;
	
	if(((is_page() && get_query_var('page_id') == get_option( 'geodir_add_listing_page' ) )) || (is_admin() && ( $pagenow == 'post.php'  || isset($_REQUEST['post_type'])))){
		return "&libraries=places";
	}
}

/**
 * Adds js to autofill the address.
 *
 * @since 1.0.0
 * @package GeoDirectory_Location_Manager
 *
 * @global object $post The current post object.
 *
 * @param string $prefix The prefix for all elements.
 */
function geodir_location_autofill_address($prefix='')
{
	
	global $pagenow,$post;
	$add_google_places_api = false;
	if(isset($post->post_content) && has_shortcode( $post->post_content, 'gd_add_listing' ) ) {$add_google_places_api = true;}
	
	if(((is_page() && get_query_var('page_id') == get_option( 'geodir_add_listing_page' ) ))|| ($add_google_places_api) || (is_admin() && ( $pagenow == 'post.php'  || isset($_REQUEST['post_type'])))){
	
	if(get_option('location_address_fill')){}else{?>
jQuery(function(){
initialize_autofill_address();
});
<?php }?>
        
var placeSearch, autocomplete;
var componentForm = {
  street_number: 'short_name',
  route: 'long_name',
  locality: 'long_name',
  administrative_area_level_1: 'short_name',
  country: 'long_name',
  postal_code: 'short_name'
};

function initialize_autofill_address() {
  // Create the autocomplete object, restricting the search
  // to geographical location types.
  autocomplete = new google.maps.places.Autocomplete(
      /** @type {HTMLInputElement} */(document.getElementById('<?php echo $prefix.'address';?>')),
      { types: ['geocode'] });
  // When the user selects an address from the dropdown,
  // populate the address fields in the form.
  google.maps.event.addListener(autocomplete, 'place_changed', function() {
    fillInAddress();
  });
}

// [START region_fillform]
function fillInAddress() {
// Get the place details from the autocomplete object.
var place = autocomplete.getPlace();

//blank feilds
jQuery('#<?php echo $prefix.'country';?> option[value=""]').attr("selected",true);
jQuery("#<?php echo $prefix.'country';?>").trigger("chosen:updated");

jQuery("#<?php echo $prefix.'region';?>").append('<option value=""><?php _e('Select Region', GEODIRLOCATION_TEXTDOMAIN);?></option>');
jQuery('#<?php echo $prefix.'region';?> option[value=""]').attr("selected",true);
jQuery("#<?php echo $prefix.'region';?>").trigger("chosen:updated");
					
jQuery("#<?php echo $prefix.'city';?>").append('<option value=""><?php _e('Select City', GEODIRLOCATION_TEXTDOMAIN);?></option>');
jQuery('#<?php echo $prefix.'city';?> option[value=""]').attr("selected",true);
jQuery("#<?php echo $prefix.'city';?>").trigger("chosen:updated");

jQuery('#<?php echo $prefix.'zip';?>').val('');

var newArr = new Array();
newArr[0] = place;
geocodeResponse(newArr);
geodir_codeAddress(true);

if(place.name){jQuery('#<?php echo $prefix.'address';?>').val(place.name);}
  // Get each component of the address from the place details
  // and fill the corresponding field on the form.
 /* for (var i = 0; i < place.address_components.length; i++) {
    var addressType = place.address_components[i].types[0];
    if (componentForm[addressType]) {
      var val = place.address_components[i][componentForm[addressType]];
      document.getElementById(addressType).value = val;
    }
  }*/
}
// [END region_fillform]

// [START region_geolocation]
// Bias the autocomplete object to the user's geographical location,
// as supplied by the browser's 'navigator.geolocation' object.
/*function geolocate() {
  if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(function(position) {
      var geolocation = new google.maps.LatLng(
          position.coords.latitude, position.coords.longitude);
      autocomplete.setBounds(new google.maps.LatLngBounds(geolocation,
          geolocation));
    });
  }
}*/
// [END region_geolocation]

<?php
		
		
	}
	
}


/**
 * Updates marker address.
 *
 * @since 1.0.0
 * @package GeoDirectory_Location_Manager
 *
 * @global object $wpdb WordPress Database object.
 *
 * @param string $prefix Identifier used as a prefix for field name
 */
function geodir_location_update_marker_address($prefix='')
{
	
	global $pagenow, $wpdb;
	
	if(((is_page() && geodir_is_page('add-listing') )) || (is_admin() && ( $pagenow == 'post.php'  || isset($_REQUEST['post_type'])))){
	
		$country_option = get_option('geodir_enable_country');
		$region_option = get_option('geodir_enable_region');
		$city_option = get_option('geodir_enable_city');
		
		$default_country = '';
		$default_region = '';
		$default_city = '';
		
		if(isset($_SESSION['gd_multi_location']))
		{
		
			if(isset($_SESSION['gd_city']) && $_SESSION['gd_city'] != '' )
				$location = geodir_get_locations('city',$_SESSION['gd_city']);
			elseif(isset($_SESSION['gd_region']) && $_SESSION['gd_region'] != '' )
				$location = geodir_get_locations('region',$_SESSION['gd_region']);
			elseif(isset($_SESSION['gd_country']) && $_SESSION['gd_country'] != '' )
				$location = geodir_get_locations('country',$_SESSION['gd_country']);		
			
			if(isset($location) && $location)
				$location = end($location);
			
			$default_city = isset($location->city) ? $location->city : '';
			$default_region = isset($location->region) ? $location->region : '';
			$default_country = isset($location->country) ? $location->country : '';
			
		}
		
		$location = geodir_get_default_location();
		
		if(empty($default_city)) $default_city = isset($location->city) ? $location->city : '';
		if(empty($default_region)) $default_region = isset($location->region) ? $location->region : '';
		if(empty($default_country)) $default_country = isset($location->country) ? $location->country : '';
		
		$default_lat = apply_filters('geodir_default_latitude', $location->city_latitude, true);
		$default_lng = apply_filters('geodir_default_longitude', $location->city_longitude, true);
		
		
		$selected_countries = array();
		if(get_option('geodir_selected_countries'))
			$selected_countries = get_option('geodir_selected_countries');
		
		$selected_regions = array();
		if(get_option('geodir_selected_regions'))
			$selected_regions = get_option('geodir_selected_regions');
		
		$selected_cities = array();
		if(get_option('geodir_selected_cities'))
			$selected_cities = get_option('geodir_selected_cities');
		?>
		
		var error = false;
		
		<?php 
		if($country_option == 'default'){
			  $countries_ISO2 =	$wpdb->get_var($wpdb->prepare("SELECT ISO2 FROM ".GEODIR_COUNTRIES_TABLE." WHERE Country=%s",$default_country));

			?>
			            
			if('<?php echo $countries_ISO2;?>' != getCountryISO && error == false){
				
				alert('<?php printf(__('Please choose any address of the (%s) country only.',GEODIRECTORY_TEXTDOMAIN), $default_country);?>');

				error = true;
			} <?php 
		
		}elseif($country_option == 'selected'){ 
		
			if(is_array($selected_countries) && !empty($selected_countries)){
				$selected_countries_string = implode(',',$selected_countries);
				if(count($selected_countries) > 1){
					$selected_countries_string = sprintf(__('Please choose any address of the (%s) countries only.',GEODIRECTORY_TEXTDOMAIN), implode(',',$selected_countries));
				}else{
					$selected_countries_string = sprintf(__('Please choose any address of the (%s) country only.',GEODIRECTORY_TEXTDOMAIN), implode(',',$selected_countries));
				}
			
			}else{
				$selected_countries_string = __('No countries available.',GEODIRECTORY_TEXTDOMAIN);
			}

			$countriesP = implode(',', array_fill(0, count($selected_countries), '%s'));
			$countries_ISO2 =	$wpdb->get_results($wpdb->prepare("SELECT ISO2 FROM ".GEODIR_COUNTRIES_TABLE." WHERE Country IN ($countriesP)",$selected_countries));
			$cISO_arr = array();
			foreach($countries_ISO2 as $cIOS2){
				$cISO_arr[] = $cIOS2->ISO2; 
			}
			?>
			
			var country_array = <?php echo json_encode($cISO_arr); ?>;
			
			//country_array = jQuery.map(country_array, String.toLowerCase);
			
			if(jQuery.inArray( getCountryISO, country_array ) == -1 && error == false){
				
				alert('<?php echo $selected_countries_string;?>');
				
				error = true;
				
			}	<?php
			
		}
			
		?>
		if(getCountry && getCity && error == false){
		
		jQuery.post( "<?php echo admin_url().'admin-ajax.php?action=geodir_locationajax_action&location_ajax_action=set_region_on_map'; ?>", { country: getCountry, state: getState, city: getCity })
		.done(function( data ) {
			
			if(jQuery.trim(data) != '')
				getState = data;
			
			<?php
			if($region_option == 'default'){?>
			
			if('<?php echo mb_strtolower(esc_attr($default_region));?>' != getState.toLowerCase() && error == false){
				
				alert('<?php printf(__('Please choose any address of the (%s) region only.',GEODIRECTORY_TEXTDOMAIN), $default_region);?>');
				
				error = true;
			}<?php 
		
		}elseif($region_option == 'selected'){ 
			
			$selected_regions_string = '';
			if(is_array($selected_regions) && !empty($selected_regions)){
				$selected_regions_string = implode(',',$selected_regions);
				if(count($selected_regions) > 1){
					$selected_regions_string = sprintf(__('Please choose any address of the (%s) regions only.',GEODIRECTORY_TEXTDOMAIN), implode(',',$selected_regions));
				}else{
					$selected_regions_string = sprintf(__('Please choose any address of the (%s) region only.',GEODIRECTORY_TEXTDOMAIN), implode(',',$selected_regions));
				}
					
			} ?>
			
			var region_array = <?php echo json_encode($selected_regions); ?>;
			
			region_array = jQuery.map(region_array, String.toLowerCase);
			
			if(jQuery.inArray( getState.toLowerCase(), region_array ) == -1 && error == false && region_array.length > 0){
				
				alert('<?php echo $selected_regions_string;?>');
				
				error = true;
				
			}	<?php
			
		}
			
			if($city_option == 'default'){?>
			
			if('<?php echo mb_strtolower(esc_attr($default_city));?>' != getCity.toLowerCase() && error == false){
				
				alert('<?php printf(__('Please choose any address of the (%s) city only.',GEODIRECTORY_TEXTDOMAIN), $default_city);?>');
				
				error = true;
			}<?php 
		
		}elseif($city_option == 'selected'){ 
			
			$selected_cities_string = '';
			if(is_array($selected_cities) && !empty($selected_cities)){
				$selected_cities_string = implode(',',$selected_cities);
				if(count($selected_cities) > 1){
					$selected_cities_string = sprintf(__('Please choose any address of the (%s) cities only.',GEODIRECTORY_TEXTDOMAIN), implode(',',$selected_cities));
				}else{
					$selected_cities_string = sprintf(__('Please choose any address of the (%s) city only.',GEODIRECTORY_TEXTDOMAIN), implode(',',$selected_cities));
				}
					
			} ?>
			
			var city_array = <?php echo json_encode($selected_cities); ?>;
			
			city_array = jQuery.map(city_array, String.toLowerCase);
			
			if(jQuery.inArray( getCity.toLowerCase(), city_array ) == -1 && error == false && city_array.length > 0){
				
				alert('<?php echo $selected_cities_string;?>');
				
				error = true;
				
			}	<?php
			
		} ?>
			});
			
	}
			
			if(error == false){
				if((jQuery.trim(old_country) != jQuery.trim(getCountry))){
					
					jQuery('#<?php echo $prefix.'region';?> option').remove();
					
					if(jQuery("#<?php echo $prefix.'region';?> option:contains('"+getState+"')").length == 0){
							jQuery("#<?php echo $prefix.'region';?>").append('<option value="'+getState+'">'+getState+'</option>');
					}
					
					jQuery('#<?php echo $prefix.'region';?> option[value="'+getState+'"]').attr("selected",true);
					jQuery("#<?php echo $prefix.'region';?>").trigger("chosen:updated");
					
					jQuery('#<?php echo $prefix.'city';?> option').remove();
					
					if(jQuery("#<?php echo $prefix.'city';?> option:contains('"+getCity+"')").length == 0){
						jQuery("#<?php echo $prefix.'city';?>").append('<option value="'+getCity+'">'+getCity+'</option>');
					}
					
					jQuery('#<?php echo $prefix.'city';?> option[value="'+getCity+'"]').attr("selected",true);
					jQuery("#<?php echo $prefix.'city';?>").trigger("chosen:updated");
					
				}
				
				
				if(jQuery.trim(old_region) != jQuery.trim(getState)){
					
					jQuery('#<?php echo $prefix.'city';?> option').remove();
					
					if(jQuery("#<?php echo $prefix.'city';?> option:contains('"+getCity+"')").length == 0){
						jQuery("#<?php echo $prefix.'city';?>").append('<option value="'+getCity+'">'+getCity+'</option>');
					}
					
					jQuery('#<?php echo $prefix.'city';?> option[value="'+getCity+'"]').attr("selected",true);
					jQuery("#<?php echo $prefix.'city';?>").trigger("chosen:updated");
					
				}
				
				
				if (getCountry){
				
					jQuery('#<?php echo $prefix.'country';?> option[value="'+getCountry+'"]').attr("selected",true);
					jQuery("#<?php echo $prefix.'country';?>").trigger("chosen:updated");
		
				}
				
                
			   if(getZip){
			   		if(getCountryISO=='SK'){geodir_region_fix(getCountryISO,getZip,'<?php echo $prefix;?>');
					} 
			   }
			   
				if (getState){
				
					if(jQuery("#<?php echo $prefix.'region';?> option:contains('"+getState+"')").length == 0){
						jQuery("#<?php echo $prefix.'region';?>").append('<option value="'+getState+'">'+getState+'</option>');
					}
				
					jQuery('#<?php echo $prefix.'region';?> option[value="'+getState+'"]').attr("selected",true);
					jQuery("#<?php echo $prefix.'region';?>").trigger("chosen:updated");
				
				}
				
				if (getCity){
				
				if(jQuery("#<?php echo $prefix.'city';?> option:contains('"+getCity+"')").length == 0){
					jQuery("#<?php echo $prefix.'city';?>").append('<option value="'+getCity+'">'+getCity+'</option>');
				}
				
				jQuery('#<?php echo $prefix.'city';?> option[value="'+getCity+'"]').attr("selected",true);
				jQuery("#<?php echo $prefix.'city';?>").trigger("chosen:updated");
				
				jQuery('select.geodir_location_add_listing_chosen').each(function(){
					
					if(jQuery(this).attr('id') == '<?php echo $prefix.'city';?>'){
						jQuery(this).change();	
					}
					
				});
					
			}	
			
			}else{
		
				geodir_set_map_default_location('<?php echo $prefix.'map';?>', '<?php echo $default_lat; ?>', '<?php echo $default_lng; ?>');
			
				return false;
				
			}
			
		
		
		if(error){
		
		geodir_set_map_default_location('<?php echo $prefix.'map';?>', '<?php echo $default_lat; ?>', '<?php echo $default_lng; ?>');
	
		return false;
		
	}
	
	
	<?php
	}
	
	if(isset($_REQUEST['add_hood']) && $_REQUEST['add_hood'] != ''){
	?>
		if (getCity){
			if(jQuery('input[id="hood_name"]').attr('id')){
				
				//jQuery("#hood_name").val(getCity);
			
			}
		}
	<?php	
	}elseif(get_option( 'location_neighbourhoods' ) && ((is_page() && get_query_var('page_id') == get_option( 'geodir_add_listing_page' ) )) || (is_admin() && ( $pagenow == 'post.php'  || isset($_REQUEST['post_type'])))){
		?>
		//geodir_get_neighbourhood_dl(getCity);
		<?php
	}
	
}



add_filter('geodir_add_listing_js_start' , 'geodir_add_fix_region_code');
/**
 *
 *
 * @since 1.0.0
 * @package GeoDirectory_Location_Manager
 */
function geodir_add_fix_region_code()
{?>
function geodir_region_fix(ISO2,ZIP,prefix){
	var _wpnonce = jQuery('#gd_location').closest('form').find('#_wpnonce').val();
		jQuery.post(
		"<?php echo plugins_url( '', __FILE__ );?>/zip_arrays/"+ISO2+".php", {
		ISO2: ISO2, ZIP:ZIP
		}
	)
	.done(function(data) {
				 if(data) { 
				getState =  data;  
				   
				   if (getState){
				
					if(jQuery("#"+prefix+"<?php echo 'region';?> option:contains('"+getState+"')").length == 0){
						jQuery("#"+prefix+"<?php echo 'region';?>").append('<option value="'+getState+'">'+getState+'</option>');
					}
				
					jQuery('#'+prefix+'<?php echo 'region';?> option[value="'+getState+'"]').attr("selected",true);
					jQuery("#"+prefix+"<?php echo 'region';?>").trigger("chosen:updated");
				
				}
				 }
	});
}
<?php
} 

add_filter('geodir_show_city_in_address' , 'geodir_show_city_in_address');
/**
 * Sets "show city in address" value to true.
 *
 * @since 1.0.0
 * @package GeoDirectory_Location_Manager
 *
 * @param bool $show_city show city in address? Default false.
 * @return bool
 */
function geodir_show_city_in_address($show_city)
{
	return true ;
} 	
	
	
/*========================*/
/* ENALBE SHARE LOCATION */
/*========================*/
add_filter('geodir_ask_for_share_location' , 'geodir_ask_for_share_location');
/**
 * Ask user confirmation to share location.
 *
 * @since 1.0.0
 * @package GeoDirectory_Location_Manager
 *
 * @param bool $mode Ask the user? Default: false.
 * @return bool Filtered value.
 */
function geodir_ask_for_share_location($mode)
{
	
	if((isset($_SESSION['gd_location_shared']) && $_SESSION['gd_location_shared']=='1') ||(isset($_SESSION['gd_multi_location']) && $_SESSION['gd_multi_location']) ) 
	{
		$_SESSION['gd_location_shared']=1;
		return false;
	}
	else if(!geodir_is_geodir_page())
		return false;
	else{
		if(!defined('DONOTCACHEPAGE')) {
  			define('DONOTCACHEPAGE', TRUE);// do not cache if we are asking for location
		}
		return true ;
	}
	
}

	
add_filter('geodir_share_location' , 'geodir_location_manager_share_location');

/**
 * Redirect url after sharing location.
 *
 * @since 1.0.0
 * @package GeoDirectory_Location_Manager
 *
 * @global object $wp_query WordPress Query object.
 * @global object $wpdb WordPress Database object.
 * @global string $plugin_prefix Geodirectory plugin table prefix.
 *
 * @param string $redirect_url Old redirect url
 * @return bool|null|string Filtered redirect url.
 */
function geodir_location_manager_share_location($redirect_url)
{
	global $wp_query,$plugin_prefix;

	
	if(isset($_REQUEST['geodir_ajax']) && $_REQUEST['geodir_ajax']=='share_location')
	{
		if(isset($_REQUEST['error']) && $_REQUEST['error'])
		{
			$_SESSION['gd_location_shared'] = true ;
			return ;
		}
		global $wpdb;
		
		// ask user to share his location only one time.
		$_SESSION['gd_location_shared'] = true ;
		
		$DistanceRadius = geodir_getDistanceRadius(get_option('geodir_search_dist_1'));
		
		if(get_option('geodir_search_dist')!=''){$dist = get_option('geodir_search_dist');}else{ $dist = '25000';  }
		if(get_option('geodir_near_me_dist')!=''){$dist2 = get_option('geodir_near_me_dist');}else{ $dist2 = '200';  }
		
		if(isset($_REQUEST['lat']) && isset($_REQUEST['long']))
		{
			$mylat = (float)stripslashes(ucfirst($_REQUEST['lat']));
			$mylon = (float)stripslashes(ucfirst($_REQUEST['long']));
		}else
		{
			$ip = $_SERVER['REMOTE_ADDR'];
			$addr_details = unserialize(file_get_contents('http://www.geoplugin.net/php.gp?ip='.$ip));
			$mylat = stripslashes(ucfirst($addr_details['geoplugin_latitude']));
			$mylon = stripslashes(ucfirst($addr_details['geoplugin_longitude']));
		}
		
	$_SESSION['user_lat'] = $mylat;
	$_SESSION['user_lon'] = $mylon;
	$lon1 = $mylon- $dist2/abs(cos(deg2rad($mylat))*69); 
	$lon2 = $mylon+$dist2/abs(cos(deg2rad($mylat))*69);
	$lat1 = $mylat-($dist2/69);
	$lat2 = $mylat+($dist2/69);
	
	$rlon1 = is_numeric(min($lon1,$lon2)) ? min($lon1,$lon2) : '';
	$rlon2 = is_numeric(max($lon1,$lon2)) ? max($lon1,$lon2) : '';
	$rlat1 = is_numeric(min($lat1,$lat2)) ? min($lat1,$lat2) : '';
	$rlat2 = is_numeric(max($lat1,$lat2)) ? max($lat1,$lat2) : '';
		
		$near_location_info = $wpdb->get_results($wpdb->prepare("SELECT *,CONVERT((%s * 2 * ASIN(SQRT( POWER(SIN((%s - (".$plugin_prefix."gd_place_detail.post_latitude)) * pi()/180 / 2), 2) +COS(%s * pi()/180) * COS( (".$plugin_prefix."gd_place_detail.post_latitude) * pi()/180) *POWER(SIN((%s - ".$plugin_prefix."gd_place_detail.post_longitude) * pi()/180 / 2), 2) ))),UNSIGNED INTEGER) as distance FROM ".$plugin_prefix."gd_place_detail WHERE (".$plugin_prefix."gd_place_detail.post_latitude IS NOT NULL AND ".$plugin_prefix."gd_place_detail.post_latitude!='') AND ".$plugin_prefix."gd_place_detail.post_latitude between $rlat1 and $rlat2  AND ".$plugin_prefix."gd_place_detail.post_longitude between $rlon1 and $rlon2 ORDER BY distance ASC LIMIT 1",$DistanceRadius,$mylat,$mylat,$mylon));
	 
		if(!empty($near_location_info)){
			$redirect_url = geodir_get_location_link('base').'me';
			return ($redirect_url);die();
		}
		
		
		$location_info = $wpdb->get_results($wpdb->prepare("SELECT *,CONVERT((%s * 2 * ASIN(SQRT( POWER(SIN((%s - (".POST_LOCATION_TABLE.".city_latitude)) * pi()/180 / 2), 2) +COS(%s * pi()/180) * COS( (".POST_LOCATION_TABLE.".city_latitude) * pi()/180) *POWER(SIN((%s - ".POST_LOCATION_TABLE.".city_longitude) * pi()/180 / 2), 2) ))),UNSIGNED INTEGER) as distance FROM ".POST_LOCATION_TABLE." ORDER BY distance ASC LIMIT 1",$DistanceRadius,$mylat,$mylat,$mylon));
		
		if(!empty($location_info))
		{
			$location_info = end($location_info);
			$location_array = array();
			$location_array['gd_country'] = $location_info->country_slug;
			$location_array['gd_region'] = $location_info->region_slug;
			$location_array['gd_city'] = $location_info->city_slug;
			$base = rtrim(geodir_get_location_link('base') , '/');
			$redirect_url = $base .'/' .$location_info->country_slug. '/' . $location_info->region_slug. '/' .  $location_info->city_slug ;
			$redirect_url = geodir_location_permalink_url( $redirect_url );
		}
		else
		{
			$redirect_url = geodir_get_location_link('base');
		}
		
		return ($redirect_url);
		
		die;
	}
	
}


add_filter('geodir_term_slug_is_exists', 'geodir_location_term_slug_is_exists', 1, 3);
/**
 * Check term slug exists or not.
 *
 * @since 1.0.0
 * @package GeoDirectory_Location_Manager
 *
 * @global object $wpdb WordPress Database object.
 * @global string $plugin_prefix Geodirectory plugin table prefix.
 *
 * @param bool $slug_exists Default: false.
 * @param string $slug The term slug.
 * @param int|string $term_id The term ID.
 * @return bool Filtered $slug_exists value.
 */
function geodir_location_term_slug_is_exists($slug_exists, $slug, $term_id){

	global $plugin_prefix,$wpdb,$table_prefix;
	$slug = urldecode($slug);
	$get_slug = $wpdb->get_var($wpdb->prepare("SELECT location_id FROM ".$plugin_prefix."post_locations WHERE country_slug=%s ||	region_slug=%s ||	city_slug=%s ", array($slug, $slug, $slug)));
	
	if($get_slug)
		return $slug_exists = true;
	
	if($wpdb->get_var($wpdb->prepare("SELECT slug FROM ".$table_prefix."terms WHERE slug=%s AND term_id != %d", array($slug, $term_id))))
		return $slug_exists = true;
		
	
	return $slug_exists;
}


add_action('init', 'geodir_update_locations_default_options');
/**
 * Update the default settings of location manager.
 *
 * @since 1.0.0
 * @package GeoDirectory_Location_Manager
 */
function geodir_update_locations_default_options(){

	if(!get_option('geodir_update_locations_default_options')){
		
		if(!get_option('geodir_enable_country'))
			update_option('geodir_enable_country', 'multi');
		
		if(!get_option('geodir_enable_region'))
			update_option('geodir_enable_region', 'multi');
			
		if(!get_option('geodir_enable_city'))
			update_option('geodir_enable_city', 'multi');
			
		if(!get_option('geodir_result_by_location'))
			update_option('geodir_result_by_location', 'everywhere');
				
		if(!get_option('geodir_everywhere_in_country_dropdown'))
			update_option('geodir_everywhere_in_country_dropdown', '1');
		
		if(!get_option('geodir_everywhere_in_region_dropdown'))
			update_option('geodir_everywhere_in_region_dropdown', '1');
		
		if(!get_option('geodir_everywhere_in_city_dropdown'))
			update_option('geodir_everywhere_in_city_dropdown', '1');
		
		update_option('geodir_update_locations_default_options', '1');
		
	}
	
}

add_action('wp', 'geodir_location_temple_redirect') ;
/**
 * Manage canonical link on location pages.
 *
 * @since 1.0.0
 * @package GeoDirectory_Location_Manager
 *
 * @global object $wp WordPress object.
 */
function geodir_location_temple_redirect()
{
	global $wp ;
	
	if(isset($wp->query_vars['page_id']) && $wp->query_vars['page_id'] == geodir_location_page_id())
	{
		add_action( 'template_redirect', 'geodir_set_location_canonical_urls',1);
	}
}

/**
 * Modify canonical links.
 *
 * @since 1.0.0
 * @package GeoDirectory_Location_Manager
 */
function geodir_set_location_canonical_urls()
{
	remove_action( 'wp_head', 'rel_canonical' );
	add_action( 'wp_head', 'geodir_location_rel_canonical' ,9 );
}

/**
 * Adds rel='canonical' tag to links.
 *
 * @since 1.0.0
 * @package GeoDirectory_Location_Manager
 *
 * @global object $wp WordPress object.
 */
function geodir_location_rel_canonical() {
	global $wp ;
	
	if( isset( $wp->query_vars['page_id'] ) && $wp->query_vars['page_id'] == geodir_location_page_id() ) {
		$link = geodir_get_location_link();
		
		if( get_option( 'geodir_set_as_home' ) && $link == geodir_get_location_link( 'base' ) ) {
			$link = get_bloginfo( 'url' );
		} else {
			if ( get_option( 'permalink_structure') != '' ) {
				$link = trim( $link );
				$link = rtrim( $link, "/" ) . "/";
			}
		}
		
		echo "<link rel='canonical' href='$link' />\n";
	}
}


add_action('init' , 'geodir_remove_parse_request_core') ;
/**
 * Removes {@see geodir_set_location_var_in_session_in_core} function from parse_request.
 *
 * @since 1.0.0
 * @package GeoDirectory_Location_Manager
 */
function geodir_remove_parse_request_core()
{
	remove_filter('parse_request' , 'geodir_set_location_var_in_session_in_core');
}

/* category + location description */
if( is_admin() ) {
	add_action( 'edit_tag_form_fields', 'geodir_location_cat_loc_desc');
}
/**
 * Adds additional description form fields to the listing category.
 *
 * @since 1.0.0
 * @package GeoDirectory_Location_Manager
 *
 * @global object $wpdb WordPress Database object.
 *
 * @param $row
 */
function geodir_location_cat_loc_desc($row) {
	global $wpdb, $wp_version;
	
	if( !is_admin() ) {
		return;
	}
	
	$taxonomy = !empty($row) && !empty($row->taxonomy) ? $row->taxonomy : '';
	if (!$taxonomy) {
		return;
	}
	$taxObject = get_taxonomy($taxonomy);

	$post_type = isset($taxObject->object_type[0]) ? $taxObject->object_type[0] : '';

	if (!$post_type) {
		return;
	}
	
	if ( $taxonomy != $post_type . 'category' ) {
		return;
	}
	$sql = "SELECT loc.location_id, loc.country, loc.region, loc.city, loc.city_meta FROM ".POST_LOCATION_TABLE." AS loc ORDER BY loc.city ASC";
	$locations = $wpdb->get_results($sql);
	if (empty($locations)) {
		return;
	}
	
	$term_id = $row->term_id;
	$term_name = $row->name;
	$term_slug = $row->slug;
	
	$gd_cat_loc_default = 1;
	
	$option_name = 'geodir_cat_loc_'.$post_type.'_'.$term_id;
	$cat_loc_option = get_option($option_name);
	
	$gd_cat_loc_default = !empty($cat_loc_option) && isset($cat_loc_option['gd_cat_loc_default']) ? (int)$cat_loc_option['gd_cat_loc_default'] : $gd_cat_loc_default;
	if (isset($_REQUEST['topdesc_type'])) {
		$gd_cat_loc_default = true;
	}
	
	$is_default = true;
	if ($gd_cat_loc_default==0) {
		$is_default = false;
	}
	
	$location_options_arr = array();
	$location_options = '';
	$count = 0;
	if (isset($_REQUEST['gd_location'])) {
		$gd_location = (int)$_REQUEST['gd_location'];
	}
	foreach ($locations as $location) {
		$count++;
		$location_id = (int)$location->location_id;
		if ($count==1 && !isset($gd_location)) {
			$gd_location = $location_id;
		}
		$country = $location->country;
		$region = $location->region;
		$city = $location->city;
		$location_name = $city.', '.$region.', '. __( $country, GEODIRECTORY_TEXTDOMAIN );
		$location_options_arr[] = array('value' => $location_id, 'label' => $location_name);
		$selected = $gd_location == $location_id ? 'selected="selected"' : '';
		if ($gd_location == $location_id) {
			$gd_location_name = $location_name;
		}
		$location_options .= '<option value="'.$location_id.'" '.$selected.'>'.$location_name.'</option>';
	}
	
	$option_name = 'geodir_cat_loc_'.$post_type.'_'.$term_id.'_'.$gd_location;
	$option = get_option($option_name);
	$gd_cat_loc_desc = !empty($option) && isset($option['gd_cat_loc_desc']) ? $option['gd_cat_loc_desc'] : '';
	if (isset($_REQUEST['gd_cat_loc'])) {
		$gd_cat_loc_desc = $_REQUEST['gd_cat_loc'];
	}
	$description = esc_attr( $gd_cat_loc_desc );
	?>
	<tr class="form-field topdesc_type">
		<th scope="row"><label for="topdesc_type"><?php echo __('Category Top Description', GEODIRECTORY_TEXTDOMAIN); ?></label></th>
		<td><input type="checkbox" id="topdesc_type" name="topdesc_type" class="rw-checkbox" value="1" <?php echo checked($is_default, true, false);?> /> Use description of default for all locations<br />
		<span class="description"><?php echo __('%location% tag available here', GEODIRECTORY_TEXTDOMAIN); ?></span>
		</td>
	</tr>
	<tr class="form-field location-top-desc" <?php if($is_default) { echo 'style="display:none"'; };?>>
		<th scope="row"><label for=""><?php echo __('Category + Location Top Description', GEODIRECTORY_TEXTDOMAIN); ?></label><br /><span class="description"><?php echo __('(Leave blank to display default description of category for location)', GEODIRECTORY_TEXTDOMAIN); ?></span></th>
		<td class="all-locations"><select name="gd_location" id="gd_location" class="gd-location-list" onchange="javascript:changeCatLocation('<?php echo $term_id;?>', '<?php echo $post_type;?>', this);"><?php echo $location_options;?></select><table class="form-table"><tbody>
		<?php 
		$count = 1;
		$field_id = 'gd_cat_loc';
		$field_name = 'gd_cat_loc';
		
		echo '<tr><td class="cat-loc-editor cat-loc-row-'.$count.'">';
		echo '<label for="'.$field_id.'">&raquo; '.sprintf(__('Category description for location %s', GEODIRECTORY_TEXTDOMAIN), '<b id="lbl-location-name">'.$gd_location_name.'</b>').'</label>';
		if ( version_compare( $wp_version, '3.2.1' ) < 1 ) {
				echo '<textarea class="at-wysiwyg theEditor large-text cat-loc-desc" name="'.$field_name.'" id="'.$field_id.'" cols="40" rows="10">'.$description.'</textarea>';
		} else {
			$settings = array( 'textarea_name' => $field_name,'media_buttons' => true, 'editor_class' => 'at-wysiwyg cat-loc-desc', 'textarea_rows' => 10 );
			// Use new wp_editor() since WP 3.3
			wp_editor( stripslashes(html_entity_decode($description)), $field_id, $settings );
		}
		?>
		<div id="<?php echo $field_id;?>-values" style="display:none!important"><input type="hidden" id="gd_locid" name="gd_locid" value="<?php echo $gd_location;?>" /><input type="hidden" id="gd_posttype" name="gd_posttype" value="<?php echo $post_type;?>" /><input type="hidden" id="gd_catid" name="gd_catid" value="<?php echo $term_id;?>" /></div>
		<script type="text/javascript">jQuery('textarea#<?php echo $field_id;?>').attr('onchange', "javascript:saveCatLocation(this);");</script>
		<?php
		echo '</td></tr>';
		?></tbody></table>
		</td>
	</tr>
	<?php
}

add_action('admin_head', 'geodir_location_cat_loc_add_css');
/**
 * Adds category location styles to head.
 *
 * @since 1.0.0
 * @package GeoDirectory_Location_Manager
 */
function geodir_location_cat_loc_add_css () {
	$taxonomy = isset($_REQUEST['taxonomy']) ? $_REQUEST['taxonomy'] : '';
	$action = isset($_REQUEST['action']) && $_REQUEST['action']=='edit' ? true : false;
	if( is_admin() && $taxonomy && $action && strpos( $taxonomy, 'category' ) !== false ) {
	?>
<style>td.cat-loc-editor{padding-top:10px;padding-bottom:12px;border:1px solid #dedede}.all-locations>table{margin-top:0}.cat-loc-editor>label{padding-bottom:10px;display:block}textarea.cat-loc-desc{width:100%!important}.default-top-desc iframe, .default-top-desc textarea{min-height:400px!important}.cat-loc-editor iframe{min-height:234px!important}.cat-loc-editor textarea{min-height:256px!important}.location-top-desc .description{font-weight:normal}#ct_cat_top_desc{width:100%!important}select.gd-location-list{margin-bottom:5px;margin-left:0;}</style>
	<?php
	}
}

add_filter( 'tiny_mce_before_init', 'add_idle_function_to_tinymce' );
/**
 *
 * @since 1.0.0
 * @package GeoDirectory_Location_Manager
 *
 * @param $initArray
 * @return mixed
 */
function add_idle_function_to_tinymce( $initArray ) {
	if (isset($initArray['selector']) && $initArray['selector']=='#gd_cat_loc') {
		$initArray['setup'] = 'function(ed) { ed.onChange.add(function(ob, e) { var content = ob.getContent(); if (ob.id=="gd_cat_loc") { saveCatLocation(ob, content); } }); }';
	}
	return $initArray;
}

add_action('admin_footer', 'geodir_location_cat_loc_add_script', 99);
/**
 * Adds category location javascript to footer.
 *
 * @since 1.0.0
 * @package GeoDirectory_Location_Manager
 */
function geodir_location_cat_loc_add_script () {
	$taxonomy = isset($_REQUEST['taxonomy']) ? $_REQUEST['taxonomy'] : '';
	$action = isset($_REQUEST['action']) && $_REQUEST['action']=='edit' ? true : false;
	if( is_admin() && $taxonomy && $action && strpos( $taxonomy, 'category' ) !== false ) {
	?>
<script type="text/javascript">
jQuery(document).ready(function(){
	jQuery('#wp-ct_cat_top_desc-wrap').closest('tr').addClass('default-top-desc');
	jQuery('.default-top-desc > th > label').hide();
	jQuery("#topdesc_type").change(function(e) {
		e.preventDefault();
		var $input = jQuery(this);
		if ($input.is(":checked")) {
			jQuery('.default-top-desc').show();
			jQuery('.location-top-desc').hide();
		} else {
			jQuery('.default-top-desc').hide();
			jQuery('.location-top-desc').show();
		}
	});
	jQuery("#topdesc_type").trigger('change');
});
function saveCatLocation(obj, content) {
	var locid = $('#gd_locid').val();
	var catid = $('#gd_catid').val();
	var posttype = $('#gd_posttype').val();
	if (!locid && !catid || !posttype) {
		return;
	}
	if (typeof content=='undefined') {
		content = $(obj).val();
	}
	var _wpnonce = $('#gd_location').closest('form').find('#_wpnonce').val();
	var loc_default = $('#topdesc_type').is(':checked')==true ? 1 : 0;
	
	jQuery.post(
		geodir_location_all_js_msg.geodir_location_admin_ajax_url+"?action=geodir_locationajax_action", {
			locid: locid, catid: catid, posttype: posttype, wpnonce: _wpnonce, location_ajax_action: 'geodir_save_cat_location', content: content, loc_default: loc_default
		}
	)
	.done(function(data) {
		//console.log(data);
	});
}
function changeCatLocation(catid, posttype, obj) {
	var locid = $(obj).val();
	if (!locid && !catid || !posttype) {
		return;
	}
	if (!locid && !catid || !posttype) {
		return;
	}
	jQuery("#gd_locid").val(locid);
	var _wpnonce = $('#gd_location').closest('form').find('#_wpnonce').val();
	var is_tinymce = typeof tinymce!='undefined' && typeof tinymce.editors!='undefined' && typeof tinymce.editors['gd_cat_loc']!='undefined' ? true : false;
	var loc_name = $("#gd_location option:selected").text();
	$('#lbl-location-name').text(loc_name);
	if (is_tinymce) {
		tinymce.editors['gd_cat_loc'].setProgressState(true);
	}
	jQuery.post(
		geodir_location_all_js_msg.geodir_location_admin_ajax_url+"?action=geodir_locationajax_action", {
			locid: locid, catid: catid, posttype: posttype, wpnonce: _wpnonce, location_ajax_action: 'geodir_change_cat_location'
		}
	)
	.done(function(data) {
		if (data!='FAIL') {
			$('#gd_cat_loc').val(data);
			if (is_tinymce) {
				tinymce.editors['gd_cat_loc'].setContent(data);
			}
		}
		if (is_tinymce) {
			tinymce.editors['gd_cat_loc'].setProgressState(false);
		}
	});
}
</script>
	<?php
	}
}

if( is_admin() ) {
	add_action( 'edited_term', 'geodir_location_save_cat_loc_desc', 10, 2 );
}
/**
 * Save category and location description.
 *
 * @since 1.0.0
 * @package GeoDirectory_Location_Manager
 *
 * @param int|string $term_id The term ID.
 * @param int $tt_id The term taxonomy ID.
 */
function geodir_location_save_cat_loc_desc($term_id, $tt_id) {
	if( !is_admin() ) {
		return;
	}
	$taxonomy = isset($_REQUEST['taxonomy']) ? $_REQUEST['taxonomy'] : '';
	$topdesc_type = isset($_REQUEST['topdesc_type']) ? $_REQUEST['topdesc_type'] : '';
	$gd_locid = isset($_REQUEST['gd_locid']) ? $_REQUEST['gd_locid'] : '';
	if (!$gd_locid || !$taxonomy) {
		return;
	}
	$taxObject = get_taxonomy($taxonomy);
	$post_type = $taxObject->object_type[0];
	if (!$post_type) {
		return;
	}
	if ( $taxonomy != $post_type . 'category' ) {
		return;
	}
	$option = array();
	$option['gd_cat_loc_default'] = (int)$topdesc_type;
	$option['gd_cat_loc_cat_id'] = (int)$term_id;
	$option['gd_cat_loc_post_type'] = $post_type;
	$option['gd_cat_loc_taxonomy'] = $taxonomy;
	$option_name = 'geodir_cat_loc_'.$post_type.'_'.$term_id;
	
	update_option($option_name, $option);
}

if( !is_admin() ) {
	add_action( 'wp_print_scripts', 'geodir_location_remove_action_listings_description', 100 );
}
/**
 * Remove listing description and add the new description.
 *
 * @since 1.0.0
 * @package GeoDirectory_Location_Manager
 */
function geodir_location_remove_action_listings_description() {
	// remove default description
	remove_action('geodir_listings_page_description', 'geodir_action_listings_description');
	
	// add action to display description
	add_action( 'geodir_listings_page_description', 'geodir_location_action_listings_description', 100 );
}

/**
 * Adds listing description to the page.
 *
 * @since 1.0.0
 * @package GeoDirectory_Location_Manager
 *
 * @global object $wpdb WordPress Database object.
 * @global object $wp_query WordPress Query object.
 */
function geodir_location_action_listings_description() {
	global $wpdb, $wp_query;
    $current_term = $wp_query->get_queried_object();
	$gd_post_type = geodir_get_current_posttype();
	if (isset($current_term->term_id) && $current_term->term_id != '') {
		$term_desc = term_description( $current_term->term_id, $gd_post_type.'_tags' ) ;
		$saved_data = stripslashes(get_tax_meta($current_term->term_id,'ct_cat_top_desc', false, $gd_post_type));
		if($term_desc && !$saved_data) { 
			$saved_data = $term_desc;
		}
		$default_location = geodir_get_default_location();
		$location_type = geodir_what_is_current_location();
		$replace_location = __('Everywhere', GEODIRLOCATION_TEXTDOMAIN);
		
		$gd_country = get_query_var( 'gd_country' );
		$gd_region = get_query_var( 'gd_region' );
		$gd_city = get_query_var( 'gd_city' );
		$current_location = '';
		if ( $gd_country != '' ) {
			$location_type = 'country';
			$current_location = get_actual_location_name( 'country', $gd_country, true );
		}
		if ( $gd_region != '' ) {
			$location_type = 'region';
			$current_location = get_actual_location_name( 'region', $gd_region );
		}
		if ( $gd_city != '' ) {
			$location_type = 'city';
			$current_location = get_actual_location_name( 'city', $gd_city );
		}
		
		if ($location_type == 'city') {
			$replace_location = geodir_get_current_location(array('what' => 'city' , 'echo'=>false));
			
			$option_name = 'geodir_cat_loc_'.$gd_post_type.'_'.$current_term->term_id;
			$cat_loc_option = get_option($option_name);
			
			$gd_cat_loc_default = !empty( $cat_loc_option ) && isset($cat_loc_option['gd_cat_loc_default']) && $cat_loc_option['gd_cat_loc_default']>0 ? true : false;
			
			if ( !$gd_cat_loc_default && $gd_city != '' ) {
				$post_type = $gd_post_type;
				$term_id = $current_term->term_id; 
				$sql = $wpdb->prepare("SELECT location_id FROM ".POST_LOCATION_TABLE." WHERE city_slug=%s ORDER BY location_id ASC LIMIT 1",array($gd_city));
				$location_id = $wpdb->get_var($sql);
				if ($location_id>0) {
					$option_name = 'geodir_cat_loc_'.$post_type.'_'.$term_id.'_'.$location_id;
					$option = get_option($option_name);
					$gd_cat_loc_desc = !empty($option) && isset($option['gd_cat_loc_desc']) ? trim($option['gd_cat_loc_desc']) : '';
					if ($gd_cat_loc_desc!='') {
						$saved_data = stripslashes_deep($gd_cat_loc_desc);
					}
				}
			}
		} else if($location_type == 'region') {
			$replace_location = geodir_get_current_location(array('what' => 'region' , 'echo'=>false));
		} else if($location_type == 'country') {
			$replace_location = geodir_get_current_location(array('what' => 'country' , 'echo'=>false));
			$replace_location = __( $replace_location, GEODIRECTORY_TEXTDOMAIN );
		}
		$replace_location = $current_location != '' ? $current_location : $replace_location;
		
		$saved_data = str_replace('%location%', $replace_location, $saved_data);
		
		$cat_description =  apply_filters( 'the_content', $saved_data );
		if($cat_description) {
			echo '<div class="term_description">'.$cat_description.'</div>';
		}
	}
}


add_filter('next_post_link', 'geodir_single_next_previous_fix',10,4);
add_filter('previous_post_link', 'geodir_single_next_previous_fix',10,4);

if (!function_exists('geodir_single_next_previous_fix')) { // we add this in location manager and CPT 
    /**
     * Filters the adjacent post url.
     *
     * @since 1.0.0
     * @package GeoDirectory_Location_Manager
     *
     * @global object $wpdb WordPress Database object.
     * @global string $plugin_prefix Geodirectory plugin table prefix.
     * @global object $post The current post object.
     *
     * @param string $url The adjacent post url.
     * @param string $link Link permalink format.
     * @param string $direction Direction. Ex: Next or Previous.
     * @param object $post The Post object.
     * @return mixed Post url.
     */
    function geodir_single_next_previous_fix($url,$link,$direction,$post) {
    global $wpdb,$plugin_prefix,$post;
    $post_type_array = geodir_get_posttypes();
    if(isset($post->post_type) && in_array($post->post_type , $post_type_array))
    {

        $post_date = $timestamp = strtotime($post->post_date);

        $where ='';
        $prep_arr = array($post_date);
        if(isset($post->country_slug) && $post->country_slug != '') {
            $where .= " AND post_locations LIKE %s ";
            $prep_arr[] = "%,[".$post->country_slug."]";
        }

        if(isset($post->region_slug) && $post->region_slug != '') {
            $where .= " AND post_locations LIKE %s ";
            $prep_arr[] = "%,[".$post->region_slug."],%";
        }

        if(isset($post->city_slug) && $post->city_slug != '') {
            $where .= " AND post_locations LIKE %s ";
            $prep_arr[] = "[".$post->city_slug."],%";
        }
        $prep_arr[] = $post->ID;



        if($direction==__('Next', GEODIRLOCATION_TEXTDOMAIN)){$op = '>';}else{$op = '<';}
        $table = $plugin_prefix.$post->post_type.'_detail';

        $pid = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT  post_id FROM ".$table." WHERE submit_time $op %d  AND post_status='publish' $where AND post_id !=%d LIMIT 1",
                $prep_arr
            )
        );

        if($pid){
            $just_url = preg_match("/href=\"([^\"]*)\"/", $url, $matches);
            if(is_array($matches) && isset($matches[1])) {
                return str_replace($matches[1], get_permalink($pid), $url);
            }
        }

    }

    return $url;
}
}


add_action( 'geodir_add_listing_codeaddress_before_geocode', 'geodir_add_listing_codeaddress_before_geocode_lm',11 );

/**
 * Disable geodir_codeAddress from location manager. Adds return to js.
 *
 * @since 1.0.0
 * @package GeoDirectory_Location_Manager
 *
 * @global object $wpdb WordPress Database object.
 */
function geodir_add_listing_codeaddress_before_geocode_lm(){
	global $wpdb;
if(geodir_is_page( 'add-listing' ) && get_option('location_set_address_disable')){?>
return;// disable geodir_codeAddress from location manager
<?php }
}

add_filter('geodir_auto_change_address_fields_pin_move','geodir_location_set_pin_disable',10,1);
/**
 * Filters the auto change address fields values when moving the map pin.
 *
 * @since 1.0.0
 * @package GeoDirectory_Location_Manager
 *
 * @param bool $val Whether to change the country, state, city values in fields.
 * @return string|bool
 */
function geodir_location_set_pin_disable($val){
if(geodir_is_page( 'add-listing' ) && get_option('location_set_pin_disable')){
    return '0';//return false
}
    return $val;
}

/**
 * Set search near text.
 *
 * @since 1.0.0
 * @package GeoDirectory_Location_Manager
 *
 * @param string $near The current near value.
 * @param string $default_near_text The default near value.
 * @return string Filtered near value.
 */
function geodir_location_set_search_near_text( $near, $default_near_text = '' ) {
    	if ( trim( $near ) == '1' ) {
        		$near = trim( $default_near_text ) == '1' ? '' : $default_near_text;
        	}

	return $near;
}
add_filter( 'geodir_search_near_text', 'geodir_location_set_search_near_text', 1000, 2 );
?>