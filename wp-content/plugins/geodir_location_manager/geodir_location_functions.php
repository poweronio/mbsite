<?php
/**
 * Contains functions related to Location Manager plugin.
 *
 * @since 1.0.0
 * @package GeoDirectory_Location_Manager
 */

/**
 * Get location by location ID.
 *
 * @since 1.0.0
 * @package GeoDirectory_Location_Manager
 *
 * @global object $wpdb WordPress Database object.
 *
 * @param array $location_result Location table query results.
 * @param string $id Location ID.
 * @return array|mixed
 */
function geodir_get_location_by_id($location_result = array() , $id='')
{
	global $wpdb;
	if($id)
	{
		$get_result = $wpdb->get_row(
			$wpdb->prepare(
				"SELECT * FROM ".POST_LOCATION_TABLE." WHERE location_id = %d",
				array($id)
			)
		);
		if(!empty($get_result))
			$location_result = $get_result;

		}
		return $location_result;
}


/**
 * Get location array using arguments.
 *
 * @since 1.0.0
 * @package GeoDirectory_Location_Manager
 *
 * @global object $wpdb WordPress Database object.
 *
 * @param null|array $args {
 *    Attributes of args.
 *
 *    @type string $what What do you want to query. Possible values: city, region, country. Default: 'city'.
 *    @type string $city_val City value.
 *    @type string $region_val Region value.
 *    @type string $country_val Country value.
 *    @type string $country_non_restricted Non restricted countries.
 *    @type string $region_non_restricted Non restricted regions.
 *    @type string $city_non_restricted Non restricted cities.
 *    @type bool $filter_by_non_restricted Filter by non restricted?.
 *    @type string $compare_operator Comparison operator.
 *    @type string $country_column_name Country column name.
 *    @type string $region_column_name Region column name.
 *    @type string $city_column_name City column name.
 *    @type bool $location_link_part Location link part.
 *    @type string $order_by Order by value.
 *    @type string $no_of_records No of records to return.
 *    @type string $spage Current page number.
 *    @type array $format {
 *        Attributes of format.
 *
 *        @type string $type Type. Default: 'list'.
 *        @type string $container_wrapper Container wrapper. Default: 'ul'.
 *        @type string $container_wrapper_attr Container wrapper attr.
 *        @type string $item_wrapper Item wrapper. Default: 'li'.
 *        @type string $item_wrapper_attr Item wrapper attr.
 *
 *    }
 *
 * }
 * @param bool $switcher Todo: describe this part.
 * @return array|mixed|string
 */
function geodir_get_location_array( $args = null, $switcher = false ) {
	global $wpdb;
	$defaults = array(
					'what' => 'city',
					'city_val' => '',
					'region_val' => '',
					'country_val' => '' ,
					'country_non_restricted' => '',
					'region_non_restricted' => '',
					'city_non_restricted' => '',
					'filter_by_non_restricted' => true,
					'compare_operator' => 'like',
					'country_column_name' => 'country',
					'region_column_name' => 'region',
					'city_column_name' => 'city',
					'location_link_part' => true,
					'order_by' => 'asc',
					'no_of_records' => '',
					'spage' => '',
					'format' => array(
									'type' => 'list',
									'container_wrapper' => 'ul',
									'container_wrapper_attr' => '',
									'item_wrapper' => 'li',
									'item_wrapper_attr' => ''
								)
				);


	$location_args = wp_parse_args( $args, $defaults );
	$search_query = '';
	$location_link_column = '';
	$location_default = geodir_get_default_location();

	if( $location_args['filter_by_non_restricted'] ) {
		// Non restricted countries
		if( $location_args['country_non_restricted'] == '' ) {
			if( get_option( 'geodir_enable_country' ) == 'default' ) {
				$country_non_retsricted = isset( $location_default->country ) ? $location_default->country : '';
				$location_args['country_non_restricted']  = $country_non_retsricted;
			} else if( get_option( 'geodir_enable_country' ) == 'selected' ) {
				$country_non_retsricted = get_option( 'geodir_selected_countries' );

				if( !empty( $country_non_retsricted ) && is_array( $country_non_retsricted ) ) {
					$country_non_retsricted = implode(',' , $country_non_retsricted );
				}

				$location_args['country_non_restricted'] = $country_non_retsricted;
			}

			$location_args['country_non_restricted'] = geodir_parse_location_list( $location_args['country_non_restricted'] );
		}

		//Non restricted Regions
		if( $location_args['region_non_restricted'] == '' ) {
			if( get_option( 'geodir_enable_region' ) == 'default' ) {
				$regoin_non_restricted= isset( $location_default->region ) ? $location_default->region : '';
				$location_args['region_non_restricted']  = $regoin_non_restricted;
			} else if( get_option( 'geodir_enable_region' ) == 'selected' ) {
				$regoin_non_restricted = get_option( 'geodir_selected_regions' );
				if( !empty( $regoin_non_restricted ) && is_array( $regoin_non_restricted ) ) {
					$regoin_non_restricted = implode( ',', $regoin_non_restricted );
				}

				$location_args['region_non_restricted']  = $regoin_non_restricted;
			}

			$location_args['region_non_restricted'] = geodir_parse_location_list( $location_args['region_non_restricted'] );
		}

		//Non restricted cities
		if( $location_args['city_non_restricted'] == '' ) {
			if( get_option('geodir_enable_city') == 'default' ) {
				$city_non_retsricted = isset( $location_default->city ) ? $location_default->city : '';
				$location_args['city_non_restricted']  = $city_non_retsricted;
			} else if( get_option( 'geodir_enable_city' ) == 'selected' ) {
				$city_non_restricted = get_option( 'geodir_selected_cities' );

				if( !empty( $city_non_restricted ) && is_array( $city_non_restricted ) ) {
					$city_non_restricted = implode( ',', $city_non_restricted );
				}

				$location_args['city_non_restricted']  = $city_non_restricted;
			}
			$location_args['city_non_restricted'] = geodir_parse_location_list( $location_args['city_non_restricted'] );
		}
	}

	if( $location_args['what'] == '') {
		$location_args['what'] = 'city';
	}

	if( $location_args['location_link_part'] ) {
		switch( $location_args['what'] ) {
			case 'country':
				if ( get_option('permalink_structure') != '' ) {
					$location_link_column = ", CONCAT_WS('/', country_slug) AS location_link ";
				} else {
					$location_link_column = ", CONCAT_WS('&gd_country=', '', country_slug) AS location_link ";
				}
				break;
			case 'region':
				if ( get_option('permalink_structure') != '' ) {
					$location_link_column = ", CONCAT_WS('/', country_slug, region_slug) AS location_link ";
				} else {
					$location_link_column = ", CONCAT_WS('&', CONCAT('&gd_country=', country_slug), CONCAT('gd_region=', region_slug) ) AS location_link ";
				}
				break;
			case 'city':
				//if(get_option('geodir_show_location_url')=='all')
				{
					if ( get_option('permalink_structure') != '' ) {
						$location_link_column = ", CONCAT_WS('/', country_slug, region_slug, city_slug) AS location_link ";
					} else {
						$location_link_column = ", CONCAT_WS('&', CONCAT('&gd_country=', country_slug), CONCAT('gd_region=', region_slug) ,CONCAT('gd_city=' , city_slug)) AS location_link ";
					}
				}
				/*else
				{
					if ( get_option('permalink_structure') != '' )
						$location_link_column = " ,   city_slug as location_link ";
					else
						$location_link_column = " , CONCAT_WS('&gd_city=', '',city_slug) as location_link ";

				}*/
				break;
			/*default:
				if(get_option('geodir_show_location_url')=='all')
				{
					if ( get_option('permalink_structure') != '' )
						$location_link_column = " , CONCAT_WS('/', country_slug, region_slug, city_slug) as location_link ";
					else
						$location_link_column = " , CONCAT_WS('&', CONCAT('&gd_country=' ,country_slug) ,CONCAT('gd_region=' , region_slug) ,CONCAT('gd_city=' , city_slug)) as location_link ";
				}
				else
				{
					if ( get_option('permalink_structure') != '' )
						$location_link_column = " ,   city_slug as location_link ";
					else
						$location_link_column = " , CONCAT_WS('&gd_city=', '',city_slug) as location_link ";

				}
				break;*/
		}
	}

	switch( $location_args['compare_operator'] ) {
		case 'like' :
			if( isset( $location_args['country_val'] ) && $location_args['country_val'] != '' ) {
				//$search_query .= " AND lower(".$location_args['country_column_name'].") like  '". mb_strtolower( $location_args['country_val'] )."%' ";
				$countries_search_sql = geodir_countries_search_sql( $location_args['country_val'] );
				$countries_search_sql = $countries_search_sql != '' ? " OR FIND_IN_SET(country, '" . $countries_search_sql . "')" : '';
				$translated_country_val = sanitize_title( trim( wp_unslash( $location_args['country_val'] ) ) );
				$search_query .= " AND ( lower(".$location_args['country_column_name'].") like  '%". mb_strtolower( $location_args['country_val'] )."%' OR  lower(country_slug) LIKE '". $translated_country_val ."%' " . $countries_search_sql . " ) ";
			}

			if(isset($location_args['region_val']) &&  $location_args['region_val'] !='')
			{
				$search_query .= " AND lower(".$location_args['region_column_name'].") like  '%". mb_strtolower($location_args['region_val'])."%' ";
			}

			if(isset($location_args['city_val']) && $location_args['city_val'] !='')
			{
				$search_query .= " AND lower(".$location_args['city_column_name'].") like  '%". mb_strtolower($location_args['city_val'])."%' ";
			}
			break;

		case 'in' :

			if(isset($location_args['country_val'])  && $location_args['country_val'] !='')
			{
				$location_args['country_val'] = geodir_parse_location_list($location_args['country_val']) ;
				$search_query .= " AND lower(".$location_args['country_column_name'].") in($location_args[country_val]) ";
			}

			if(isset($location_args['region_val']) && $location_args['region_val'] !='' )
			{
				$location_args['region_val'] = geodir_parse_location_list($location_args['region_val']) ;
				$search_query .= " AND lower(".$location_args['region_column_name'].") in($location_args[region_val]) ";
			}

			if(isset($location_args['city_val'])  && $location_args['city_val'] !=''  )
			{
				$location_args['city_val'] = geodir_parse_location_list($location_args['city_val']) ;
				$search_query .= " AND lower(".$location_args['city_column_name'].") in($location_args[city_val]) ";
			}

			break;
		default :
			if(isset($location_args['country_val']) && $location_args['country_val'] !='' )
			{
				//$search_query .= " AND lower(".$location_args['country_column_name'].") =  '". mb_strtolower($location_args['country_val'])."' ";
				$countries_search_sql = geodir_countries_search_sql( $location_args['country_val'] );
				$countries_search_sql = $countries_search_sql != '' ? " OR FIND_IN_SET(country, '" . $countries_search_sql . "')" : '';
				$translated_country_val = sanitize_title( trim( wp_unslash( $location_args['country_val'] ) ) );
				$search_query .= " AND ( lower(".$location_args['country_column_name'].") =  '". mb_strtolower($location_args['country_val'])."' OR  lower(country_slug) LIKE '". $translated_country_val ."%' " . $countries_search_sql . " ) ";
			}

			if(isset($location_args['region_val']) && $location_args['region_val'] !='')
			{
				$search_query .= " AND lower(".$location_args['region_column_name'].") =  '". mb_strtolower($location_args['region_val'])."' ";
			}

			if(isset($location_args['city_val']) && $location_args['city_val'] !='' )
			{
				$search_query .= " AND lower(".$location_args['city_column_name'].") =  '". mb_strtolower($location_args['city_val'])."' ";
			}
			break ;

	} // end of switch


	if($location_args['country_non_restricted'] != '') {
		$search_query .= " AND LOWER(country) IN ($location_args[country_non_restricted]) ";
	}

	if($location_args['region_non_restricted'] != '') {
		if( $location_args['what'] == 'region' || $location_args['what'] == 'city' ) {
			$search_query .= " AND LOWER(region) IN ($location_args[region_non_restricted]) ";
		}
	}

	if($location_args['city_non_restricted'] != '') {
		if($location_args['what'] == 'city' ) {
			$search_query .= " AND LOWER(city) IN ($location_args[city_non_restricted]) ";
		}
	}


	//page
	if($location_args['no_of_records']){
	$spage = $location_args['no_of_records']*$location_args['spage'];
	}else{
	$spage = "0";
	}

	// limit
	$limit = $location_args['no_of_records'] != '' ? ' LIMIT '.$spage.', ' . (int)$location_args['no_of_records'] . ' ' : '';

	// display all locations with same name also
	$search_field = $location_args['what'];
	if( $switcher ) {
		$select = $search_field . $location_link_column;
		$group_by = $search_field;
		$order_by = $search_field;
		if( $search_field == 'city' ) {
			$select .= ', country, region, city, country_slug, region_slug, city_slug';
			$group_by = 'country, region, city';
			$order_by = 'city, region, country';
		} else if( $search_field == 'region' ) {
			$select .= ', country, region, country_slug, region_slug';
			$group_by = 'country, region';
			$order_by = 'region, country';
		} else if( $search_field == 'country' ) {
			$select .= ', country, country_slug';
			$group_by = 'country';
			$order_by = 'country';
		}

		$main_location_query = "SELECT " . $select . " FROM " .POST_LOCATION_TABLE." WHERE 1=1 " . $search_query . " GROUP BY " . $group_by . " ORDER BY " . $order_by . " " . $location_args['order_by'] . " " . $limit;
	} else {
		$main_location_query = "SELECT $location_args[what] $location_link_column FROM " .POST_LOCATION_TABLE." WHERE 1=1 " .  $search_query . " GROUP BY $location_args[what] ORDER BY $location_args[what] $location_args[order_by] $limit";
	}

	$locations = $wpdb->get_results( $main_location_query );

	if( $switcher && !empty( $locations ) ) {
		$new_locations = array();

		foreach( $locations as $location ) {
			//print_r($location);
			//echo '###'.$search_field;
			$new_location = $location;
			$label = $location->$search_field;
			if( ( $search_field == 'city' || $search_field == 'region' ) && (int)geodir_location_check_duplicate( $search_field, $label ) > 1 ) {

				if( $search_field == 'city' ) {
					$label .= ', ' . $location->region;
				} else if( $search_field == 'region' ) {
					$country_iso2 = geodir_location_get_iso2( $location->country );
					$country_iso2 = $country_iso2 != '' ? $country_iso2 : $location->country;
					$label .= $country_iso2 != '' ? ', ' . $country_iso2 : '';
				}
			}
			$new_location->title = $location->$search_field;
			$new_location->$search_field = $label;
			$new_location->label = $label;
			$new_locations[] = $new_location;
		}
		$locations = $new_locations;
	}

	$location_as_formated_list = "";
	if(!empty($location_args['format']))
	{
		if($location_args['format']['type']=='array')
			return $locations ;
		elseif($location_args['format']['type']=='jason')
			return json_encode($locations) ;
		else
		{
			$base_location_link = geodir_get_location_link('base');
			$container_wrapper = '' ;
			$container_wrapper_attr = '' ;
			$item_wrapper = '' ;
			$item_wrapper_attr = '' ;

			if(isset($location_args['format']['container_wrapper']) && !empty($location_args['format']['container_wrapper']))
				$container_wrapper = $location_args['format']['container_wrapper'] ;

			if(isset($location_args['format']['container_wrapper_attr']) && !empty($location_args['format']['container_wrapper_attr']))
				$container_wrapper_attr = $location_args['format']['container_wrapper_attr'] ;

			if(isset($location_args['format']['item_wrapper']) && !empty($location_args['format']['item_wrapper']))
				$item_wrapper = $location_args['format']['item_wrapper'] ;

			if(isset($location_args['format']['item_wrapper_attr']) && !empty($location_args['format']['item_wrapper_attr']))
				$item_wrapper_attr = $location_args['format']['item_wrapper_attr'] ;


			if(!empty($container_wrapper))
				$location_as_formated_list = "<" . $container_wrapper . " " . $container_wrapper_attr . " >";

			if(!empty($locations))
			{
				foreach($locations as $location)
				{
					if(!empty($item_wrapper))
						$location_as_formated_list .= "<" . $item_wrapper . " " . $item_wrapper_attr . " >";
					if(isset($location->location_link))
					{
						$location_as_formated_list .= "<a href='" . geodir_location_permalink_url( $base_location_link. $location->location_link ). "' ><i class='fa fa-caret-right'></i> ";
					}

					$location_as_formated_list .= $location->$location_args['what'] ;

					if(isset($location->location_link))
					{
						$location_as_formated_list .= "</a>";
					}

					if(!empty($item_wrapper))
						$location_as_formated_list .="</" . $item_wrapper . ">";
				}
			}

			return $location_as_formated_list ;
		}
	}
	return $locations ;
}

/**
 * Get ISO2 country code for the given country.
 *
 * @since 1.0.0
 * @package GeoDirectory_Location_Manager
 *
 * @global object $wpdb WordPress Database object.
 *
 * @param string $country Country name.
 * @return null|string
 */
function geodir_location_get_iso2( $country ) {
	global $wpdb;
	$sql = $wpdb->prepare( "SELECT ISO2 FROM " . GEODIR_COUNTRIES_TABLE . " WHERE Country LIKE %s", $country );
	$result = $wpdb->get_var( $sql );
	return $result;
}

/**
 * Check location duplicates.
 *
 * @since 1.0.0
 * @package GeoDirectory_Location_Manager
 *
 * @global object $wpdb WordPress Database object.
 *
 * @param string $field The field to check for duplicates.
 * @param string $location The location value to check for duplicates.
 * @return int Total rows found.
 */
function geodir_location_check_duplicate( $field, $location ) {
	global $wpdb;

	$sql = '';
	$result = 0;
	if( $field == 'city' ) {
		$sql = $wpdb->prepare( "SELECT COUNT(*) AS total FROM " . POST_LOCATION_TABLE . " WHERE " . $field . "=%s GROUP BY " . $field, $location, $location );
		$row = $wpdb->get_results( $sql );
		if( !empty( $row ) && isset( $row[0]->total ) ) {
			$result = (int)$row[0]->total;
		}
	} else if( $field == 'region' ) {
		$sql = $wpdb->prepare( "SELECT COUNT(*) AS total FROM " . POST_LOCATION_TABLE . " WHERE " . $field . "=%s GROUP BY country, " . $field, $location, $location );
		$row = $wpdb->get_results( $sql );
		if( !empty( $row ) && count( $row ) > 0 ) {
			$result = (int)count( $row );
		}
	}
	return $result;
}

/**
 * Returns countries array.
 *
 * @since 1.0.0
 * @package GeoDirectory_Location_Manager
 *
 * @global object $wpdb WordPress Database object.
 *
 * @param string $from Get countries from table or option?
 * @return array Countries array.
 */
function geodir_get_countries_array( $from = 'table' ) {
	global $wpdb;

	if( $from == 'table' ) {
		$countries = $wpdb->get_col( "SELECT Country FROM " . GEODIR_COUNTRIES_TABLE );
	} else {
		$countries = get_option( 'geodir_selected_countries' );
	}
	$countires_array = '' ;
	foreach( $countries as $key => $country ) {
		$countires_array[$country] = __( $country, GEODIRECTORY_TEXTDOMAIN ) ;
	}
	asort($countires_array);

	return $countires_array ;
}

/**
 * Get countries in a dropdown.
 *
 * @since 1.0.0
 * @package GeoDirectory_Location_Manager
 *
 * @global object $wpdb WordPress Database object.
 *
 * @param $selected_option
 */
function geodir_get_limited_country_dl( $selected_option ) {
	global $wpdb;

	$selected = '';
	$countries = geodir_get_countries_array( 'saved_option' );

	$out_put = '<option ' . $selected . ' value="">' . __( 'Select Country', GEODIRECTORY_TEXTDOMAIN ). '</option>';
	$countries_ISO2 = $wpdb->get_results( "SELECT Country, ISO2 FROM " . GEODIR_COUNTRIES_TABLE );

	foreach( $countries_ISO2 as $c2 ) {
		$ISO2[$c2->Country] = $c2->ISO2;
	}

	foreach( $countries as $country ) {
		$ccode = $ISO2[$country];
		$selected = '';
		if( $selected_option == $country ) {
			$selected = ' selected="selected" ';
		}

		$out_put .= '<option ' . $selected . ' value="' . $country . '" data-country_code="' . $ccode . '">' . __( $country, GEODIRECTORY_TEXTDOMAIN ) . '</option>';
    }

	echo $out_put;
}

/**
 * Get location data as an array or object.
 *
 * @since 1.0.0
 * @package GeoDirectory_Location_Manager
 *
 * @param string $which Location type. Possible values are 'country', 'region', 'city'. Default: 'country'.
 * @param string $format Output format. Possible values are 'array', 'object'. Default: 'array'.
 * @return object|string|array Location array or object.
 */
function geodir_get_limited_location_array($which='country' , $format='array')
{
	$location_array = '' ;
	$locations = '' ;
	switch($which)
	{
		case 'country':
						$locations =	get_option('geodir_selected_countries');
						break;
		case 'region':
						$locations =	get_option('geodir_selected_regions');
						break;
		case 'city':
						$locations =	get_option('geodir_selected_cities');
						break;
	}

	if(!empty($locations) && is_array($locations))
	{
		foreach($locations as $location)
		$location_array[$location] = $location ;
	}

	if($format=='object')
		$location_array = (object)$location_array ;

	return $location_array ;
}


/**
 * Handles location form data.
 *
 * @since 1.0.0
 * @package GeoDirectory_Location_Manager
 */
function geodir_location_form_submit_handler()
{
	if(isset($_REQUEST['geodir_location_merge']) && $_REQUEST['geodir_location_merge'] == 'merge')
	{
		include_once('geodir_merge_field.php');
		exit;
	}

	if(isset($_REQUEST['location_ajax_action']))
	{
		switch($_REQUEST['location_ajax_action']):
			case 'settings':

				geodir_update_options(geodir_location_default_options());

				$msg = GD_LOCATION_SETTINGS_SAVED;

				$msg = urlencode($msg);

				$location = admin_url()."admin.php?page=geodirectory&tab=managelocation_fields&subtab=geodir_location_setting&location_success=".$msg;
				wp_redirect($location);
				exit;

			break;
			case 'location':
				geodir_add_location();
			break;
			case 'add_hood':
				geodir_add_neighbourhood();
			break;
			case 'set_default':
				geodir_set_default();
			break;
			case 'merge':
				geodir_merge_location();
			break;
			case 'delete':
				geodir_delete_location();
			break;
			case 'delete_hood':
				geodir_delete_hood();
			break;
			case 'merge_cities':
				include_once('geodir_merge_field.php');
				exit();
			break;
			case 'set_region_on_map':
				geodir_get_region_on_map();
			break;
			case 'geodir_set_location_seo':
				geodir_get_location_seo_settings();
			break;
			case 'geodir_save_cat_location':
				geodir_save_cat_location();
			break;
			case 'geodir_change_cat_location':
				geodir_change_cat_location();
			break;


		endswitch;
	}
}


/**
 * Handles location SEO settings form data.
 *
 * @since 1.0.0
 * @package GeoDirectory_Location_Manager
 *
 * @global object $wpdb WordPress Database object.
 */
function geodir_get_location_seo_settings()
{
	global $wpdb;

	if(isset($_REQUEST['wpnonce']) && current_user_can( 'manage_options' ) && isset($_REQUEST['location_slug'])) {

		if ( !wp_verify_nonce( $_REQUEST['wpnonce'], 'geodir_set_location_seo'.$_REQUEST['location_slug'] ) ) {
			echo 'FAIL';
			exit;
		}

		$field = isset($_REQUEST['field']) && ($_REQUEST['field']=='geodir_meta_keyword' || $_REQUEST['field']=='geodir_meta_description') ? $_REQUEST['field'] : '';
		$seo_value = isset($_REQUEST['field_val']) ? trim($_REQUEST['field_val']) : '';

		if ($field=='' || $seo_value=='') {
			echo 'FAIL';
			exit;
		}
		$seo_field = $_REQUEST['field']=='geodir_meta_keyword' ? 'seo_title' : 'seo_desc';

		$location_type = isset($_REQUEST['location_type']) ? $_REQUEST['location_type'] : '';
		$country_slug = isset($_REQUEST['country_slug']) ? $_REQUEST['country_slug'] : '';
		$region_slug = isset($_REQUEST['region_slug']) ? $_REQUEST['region_slug'] : '';
		$location_slug = isset($_REQUEST['location_slug']) ? $_REQUEST['location_slug'] : '';

		if ($seo_field=='seo_title') {
			$seo_value = substr($seo_value, 0, 140);
		} else {
			$seo_value = substr($seo_value, 0, 100000);
		}

		$seo_info = geodir_location_seo_by_slug($location_slug, $location_type, $country_slug, $region_slug);

		$date_now = date('Y-m-d H:i:s');

		switch($location_type) {
			case 'country': {
				if (!empty($seo_info)) {
					$sql = $wpdb->prepare("UPDATE ".LOCATION_SEO_TABLE." SET ".$seo_field."=%s, date_updated=%s WHERE seo_id=%d", array($seo_value, $date_now, $seo_info->seo_id));
				} else {
					$sql = $wpdb->prepare("INSERT INTO ".LOCATION_SEO_TABLE." SET location_type=%s, country_slug=%s, ".$seo_field."=%s, date_created=%s", array($location_type, $location_slug, $seo_value, $date_now));
				}
				if ($wpdb->query($sql)) {
					echo 'OK';
					exit;
				}
			}
			break;
			case 'region': {
				if (!empty($seo_info)) {
					$sql = $wpdb->prepare("UPDATE ".LOCATION_SEO_TABLE." SET country_slug=%s, ".$seo_field."=%s, date_updated=%s WHERE seo_id=%d", array($country_slug, $seo_value, $date_now, $seo_info->seo_id));
				} else {
					$sql = $wpdb->prepare("INSERT INTO ".LOCATION_SEO_TABLE." SET location_type=%s, country_slug=%s, region_slug=%s, ".$seo_field."=%s, date_created=%s", array($location_type, $country_slug, $location_slug, $seo_value, $date_now));
				}
				if ($wpdb->query($sql)) {
					echo 'OK';
					exit;
				}
			}
			break;
			case 'city': {
				if (!empty($seo_info)) {
					$sql = $wpdb->prepare("UPDATE ".LOCATION_SEO_TABLE." SET country_slug=%s, region_slug=%s, ".$seo_field."=%s, date_updated=%s WHERE seo_id=%d", array($country_slug, $region_slug, $seo_value, $date_now, $seo_info->seo_id));
				} else {
					$sql = $wpdb->prepare("INSERT INTO ".LOCATION_SEO_TABLE." SET location_type=%s, country_slug=%s, region_slug=%s, city_slug=%s, ".$seo_field."=%s, date_created=%s", array($location_type, $country_slug, $region_slug, $location_slug, $seo_value, $date_now));
				}
				if ($wpdb->query($sql)) {
					$info = geodir_city_info_by_slug($location_slug, $country_slug, $region_slug);
					if (!empty($info)) {
						$location_field = $seo_field=='seo_title' ? 'city_meta' : 'city_desc';
						$sql = $wpdb->prepare("UPDATE ".POST_LOCATION_TABLE." SET ".$location_field."=%s WHERE location_id=%d", array($seo_value, $info->location_id));
						$wpdb->query($sql);
					}
					echo 'OK';
					exit;
				}
			}
			break;
		}
	}

			$msg = urlencode( __('Location SEO updated successfully.',GEODIRLOCATION_TEXTDOMAIN) );

			$location = admin_url()."admin.php?page=geodirectory&tab=managelocation_fields&subtab=geodir_location_seo&location_success=".$msg;
			wp_redirect($location);
			exit;
}

/**
 * Get location SEO information using location slug.
 *
 * @since 1.0.0
 * @package GeoDirectory_Location_Manager
 *
 * @global object $wpdb WordPress Database object.
 *
 * @param string $slug Location slug.
 * @param string $location_type Location type. Possible values 'gd_city','gd_region','gd_country'.
 * @param string $country_slug Country slug.
 * @param string $region_slug Region slug.
 * @return mixed|null
 */
function geodir_location_seo_by_slug($slug, $location_type='city', $country_slug='', $region_slug='')
{
	global $wpdb;
	if ($slug=='') {
		return NULL;
	}

	$whereField = '1';
	$whereVal = array();

	switch($location_type) {
		case 'country': {
			$whereField .= ' AND location_type=%s AND country_slug=%s';
			$whereVal[] = $location_type;
			$whereVal[] = $slug;
		}
		break;
		case 'region': {
			$whereField .= ' AND location_type=%s AND region_slug=%s';
			$whereVal[] = $location_type;
			$whereVal[] = $slug;
			if ($country_slug!='') {
				$whereField .= ' AND country_slug=%s';
				$whereVal[] = $country_slug;
			}
		}
		break;
		case 'city': {
			$whereField .= ' AND location_type=%s AND city_slug=%s';
			$whereVal[] = $location_type;
			$whereVal[] = $slug;
			if ($country_slug!='') {
				$whereField .= ' AND country_slug=%s';
				$whereVal[] = $country_slug;
			}
			if ($region_slug!='') {
				$whereField .= ' AND region_slug=%s';
				$whereVal[] = $region_slug;
			}
		}
		break;
	}
	if (empty($whereVal)) {
		return NULL;
	}

	$sql = $wpdb->prepare( "SELECT seo_id, seo_title, seo_desc FROM ".LOCATION_SEO_TABLE." WHERE ".$whereField." ORDER BY seo_id LIMIT 1", $whereVal );

	$row = $wpdb->get_row($sql);
	if (is_object($row)) {
		return $row;
	}
	return NULL;
}

/**
 * Get location city information using location slug.
 *
 * @since 1.0.0
 * @package GeoDirectory_Location_Manager
 *
 * @global object $wpdb WordPress Database object.
 *
 * @param string $slug Location slug.
 * @param string $country_slug Country slug.
 * @param string $region_slug Region slug.
 * @return mixed|null
 */
function geodir_city_info_by_slug($slug, $country_slug='', $region_slug='')
{
	global $wpdb;

	if ($slug=='') {
		return NULL;
	}

	$whereVal = array();
	$whereField = 'city_slug=%s';
	$whereVal[] = $slug;

	if ($country_slug!='') {
		$whereField .= ' AND country_slug=%s';
		$whereVal[] = $country_slug;
	}
	if ($region_slug!='') {
		$whereField .= ' AND region_slug=%s';
		$whereVal[] = $region_slug;
	}

	$row = $wpdb->get_row(
		$wpdb->prepare( "SELECT location_id, country_slug, region_slug, city_slug, country, region, city, city_meta, city_desc FROM ".POST_LOCATION_TABLE." WHERE ".$whereField." ORDER BY location_id LIMIT 1", $whereVal )
	);
	if (is_object($row)) {
		return $row;
	}
	return NULL;
}

/**
 * Get region on map.
 *
 * @since 1.0.0
 * @package GeoDirectory_Location_Manager
 *
 * @global object $wpdb WordPress Database object.
 */
function geodir_get_region_on_map()
{

	global $wpdb;

	if(isset($_REQUEST['country']) && $_REQUEST['country'] != '' && isset($_REQUEST['city']) && $_REQUEST['city'] != ''){

		$region = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT region FROM ".POST_LOCATION_TABLE." WHERE country=%s AND city=%s",
				array($_REQUEST['country'],$_REQUEST['city'])
			)
		);

		if(!$region)
			$region = $_REQUEST['state'];

		echo $region;
	}
	exit;
}


/**
 * Handles 'add neighbourhood' form data.
 *
 * @since 1.0.0
 * @package GeoDirectory_Location_Manager
 *
 * @global object $wpdb WordPress Database object.
 * @global string $plugin_prefix Geodirectory plugin table prefix.
 */
function geodir_add_neighbourhood()
{

	global $wpdb,$plugin_prefix;

	if(isset($_REQUEST['location_addedit_nonce']) && current_user_can( 'manage_options' )){

		if ( !wp_verify_nonce( $_REQUEST['location_addedit_nonce'], 'location_add_edit_nonce' ) )
		return;

		$hood_name = $_REQUEST['hood_name'];
		$gd_latitude = $_REQUEST['gd_latitude'];
		$gd_longitude = $_REQUEST['gd_longitude'];
		$city_id = $_REQUEST['update_city'];
		$hood_id = $_REQUEST['update_hood'];
		$hood_slug = create_location_slug($hood_name);

		$countslug = $wpdb->get_var(
			$wpdb->prepare(
			"select COUNT(hood_id) AS total from ".POST_NEIGHBOURHOOD_TABLE." WHERE hood_slug LIKE %d",
			array($hood_slug.'%')
			)
		);

		if($countslug!='0'){
			$number = $countslug+1;
			$hood_slug = $hood_slug.'-'.$number;
		}

		if($hood_id)
		{
			$duplicate = $wpdb->get_var(
				$wpdb->prepare(
					"select hood_id from ".POST_NEIGHBOURHOOD_TABLE." WHERE hood_location_id = %d AND hood_name=%s AND hood_id!=%d",
					array($city_id,$hood_name,$hood_id)
				)
			);

		}
		else
		{

			$duplicate = $wpdb->get_var(
				$wpdb->prepare(
				"select hood_id from ".POST_NEIGHBOURHOOD_TABLE." WHERE hood_location_id = %d AND hood_name=%s",
				array($city_id,$hood_name)
				)
			);

		}

		if($duplicate!='')
		{
			$setid = '';
			if($hood_id){

				$setid = '&hood_id='.$hood_id;

			}

			$msg = GD_NEIGHBOURHOOD_EXITS;

			$msg = urlencode($msg);

			$location = admin_url()."admin.php?page=geodirectory&tab=managelocation_fields&subtab=geodir_location_addedit&add_hood=true&location_error=".$msg."&id=".$city_id.$setid;
			wp_redirect($location);
			exit;
		}

		if($_POST['location_ajax_action'] == 'add_hood')
		{


			if($hood_id)
			{
				$sql = $wpdb->prepare("UPDATE ".POST_NEIGHBOURHOOD_TABLE." SET
				hood_location_id=%d,
				hood_name=%s,
				hood_latitude=%s,
				hood_longitude=%s,
				hood_slug=%s
				WHERE hood_id = %d",
				array($city_id,$hood_name,$gd_latitude,$gd_longitude,$hood_slug,$hood_id));

			$location_hood = $wpdb->get_results(
				$wpdb->prepare(
					"SELECT l.city, n.hood_slug FROM ".POST_LOCATION_TABLE." l, ".POST_NEIGHBOURHOOD_TABLE." n WHERE n.hood_location_id=l.location_id AND hood_id=%d",
					array($hood_id)
				)
			);

			$geodir_posttypes = geodir_get_posttypes();

			foreach($geodir_posttypes as $geodir_posttype){

				$table = $plugin_prefix . $geodir_posttype . '_detail';

				if($wpdb->get_var("SHOW COLUMNS FROM ".$table." WHERE field = 'post_neighbourhood'"))
				{
					if(!empty($location_hood)){
						foreach($location_hood as $hood_del){

							$wpdb->query(
								$wpdb->prepare(
									"UPDATE ".$table." SET post_neighbourhood=%s WHERE post_city=%s AND post_neighbourhood=%s",
									array($hood_slug,$hood_del->city,$hood_del->hood_slug)
								)
							);

						}
					}

				}
		 }

				$msg = MSG_NEIGHBOURHOOD_UPDATED;

			}
			else
			{
				$sql = $wpdb->prepare("INSERT INTO ".POST_NEIGHBOURHOOD_TABLE." SET
				hood_location_id=%d,
				hood_name=%s,
				hood_slug=%s,
				hood_latitude=%s,
				hood_longitude=%s",
				array($city_id,$hood_name,$hood_slug,$gd_latitude,$gd_longitude));

				$msg = MSG_NEIGHBOURHOOD_ADDED;

			}

			$wpdb->query($sql);

			$msg = urlencode($msg);

			$location = admin_url()."admin.php?page=geodirectory&tab=managelocation_fields&subtab=geodir_location_manager&location_success=".$msg."&city_hood=hoodlist&id=".$city_id;
			wp_redirect($location);
			exit;
		}

	}else{
		wp_redirect(home_url().'/?geodir_signup=true');
		exit();
	}
}

/**
 * Get neighbourhoods in dropdown.
 *
 * @since 1.0.0
 * @package GeoDirectory_Location_Manager
 *
 * @global object $wpdb WordPress Database object.
 *
 * @param string $city
 * @param string $selected_id
 * @param bool $echo
 * @return string
 */
function geodir_get_neighbourhoods_dl($city='', $selected_id='', $echo = true)
{
	global $wpdb;


	$neighbourhoods = $wpdb->get_results(
		$wpdb->prepare(
			"SELECT * FROM ".POST_NEIGHBOURHOOD_TABLE." hood, ".POST_LOCATION_TABLE." location WHERE hood.hood_location_id = location.location_id AND location.city=%s ORDER BY hood_name ",
			array($city)
		)
	);

	$selectoptions = '';
	if(!empty($neighbourhoods)){
		$selectoptions = '<option value="">'.__('Select Neighbourhood',GEODIRLOCATION_TEXTDOMAIN).'</option>';

		foreach($neighbourhoods as $neighbourhood)
		{
			$selected = '';
			if($neighbourhood->hood_slug == $selected_id)
				$selected = ' selected="selected" ';

			$selectoptions.= '<option value="'.$neighbourhood->hood_slug.'" '.$selected.'>'.$neighbourhood->hood_name.'</option>';

		}
	}

	if($echo)
		echo $selectoptions;
	else
		return $selectoptions;
}


/**
 * Handles 'add location' form data.
 *
 * @since 1.0.0
 * @package GeoDirectory_Location_Manager
 *
 * @global object $wpdb WordPress Database object.
 * @global string $plugin_prefix Geodirectory plugin table prefix.
 */
function geodir_add_location()
{
	global $wpdb,$plugin_prefix;

	if(isset($_REQUEST['location_addedit_nonce']) && current_user_can( 'manage_options' )){

		if ( !wp_verify_nonce( $_REQUEST['location_addedit_nonce'], 'location_add_edit_nonce' ) )
		return;

		$gd_city = $_REQUEST['gd_city'];
		$gd_region = $_REQUEST['gd_region'];
		$gd_country = $_REQUEST['gd_country'];
		$gd_latitude = $_REQUEST['gd_latitude'];
		$gd_longitude = $_REQUEST['gd_longitude'];
		$city_meta = $_REQUEST['city_meta'];
		$city_desc = $_REQUEST['city_desc'];

		$id = $_REQUEST['update_city'];

		if($id)
		{
			$duplicate = $wpdb->get_var(
				$wpdb->prepare(
					"select location_id from ".POST_LOCATION_TABLE." WHERE city = %s AND region=%s AND country=%s AND location_id!=%d",
					array($gd_city,$gd_region,$gd_country,$id)
				)
			);

		}
		else
		{

			$duplicate = $wpdb->get_var(
				$wpdb->prepare(
					"select location_id from ".POST_LOCATION_TABLE." WHERE city = %s AND region=%s AND country=%s",
					array($gd_city,$gd_region,$gd_country)
				)
			);

		}

		if($duplicate!='')
		{
			$setid = '';
			if($id){ $setid = '&id='.$id; }

			$msg = GD_LOCATION_EXITS;

			$msg = urlencode($msg);

			$location = admin_url()."admin.php?page=geodirectory&tab=managelocation_fields&subtab=geodir_location_addedit&location_error=".$msg.$setid;
			wp_redirect($location);
			exit;
		}

		if($_POST['location_ajax_action'] == 'location')
		{

			$country_slug = create_location_slug($gd_country);
			$region_slug = create_location_slug($gd_region);
			$city_slug = create_location_slug($gd_city);

			if($id)
			{
				$old_location = geodir_get_location_by_id('' , $id);

				$sql = $wpdb->prepare("UPDATE ".POST_LOCATION_TABLE." SET
					country=%s,
					region=%s,
					city=%s,
					city_latitude=%s,
					city_longitude=%s,
					country_slug = %s,
					region_slug = %s,
					city_slug = %s,
					city_meta=%s,
					city_desc=%s WHERE location_id = %d",
					array($gd_country,$gd_region,$gd_city,$gd_latitude,$gd_longitude,$country_slug,$region_slug,$city_slug,$city_meta,$city_desc,$id)

				);

				$wpdb->query($sql);

				$geodir_location = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".POST_LOCATION_TABLE." WHERE is_default='1' AND location_id = %d",array($id)), "OBJECT" );

				if(!empty($geodir_location))
					update_option('geodir_default_location', $geodir_location); // UPDATE DEFAULT LOCATION OPTION

				$msg = GD_LOCATION_UPDATED;

				//UPDATE AND DELETE LISTING
				$posttype = geodir_get_posttypes();
				if (isset($_REQUEST['listing_action']) && $_REQUEST['listing_action'] == 'delete') {

					foreach ($posttype as $posttypeobj) {

						/* do not update latitude and longitude otrherwise all listings will be spotted on one point on map
						if ($old_location->city_latitude != $gd_latitude || $old_location->city_longitude != $gd_longitude) {

							$del_post_sql = $wpdb->get_results(
								$wpdb->prepare(
									"SELECT post_id from ".$plugin_prefix.$posttypeobj."_detail WHERE post_location_id = %d AND (post_city != %s OR post_region != %s)",
									array($id,$gd_city,$gd_region)
								)
							);
							if (!empty($del_post_sql)) {
								foreach ($del_post_sql as $del_post_info) {
									$postid = (int)$del_post_info->post_id;
									//wp_delete_post($postid); // update post location instead of delete post
									$sql = $wpdb->prepare(
										"UPDATE ".$plugin_prefix.$posttypeobj."_detail SET post_latitude=%s, post_longitude=%s WHERE post_location_id=%d AND post_id=%d",
										array( $gd_latitude, $gd_longitude, $id, $postid )
									);
									$wpdb->query($sql);
								}
							}
						}
						*/

						$post_locations =  '['.$city_slug.'],['.$region_slug.'],['.$country_slug.']'; // set all overall post location

						$sql = $wpdb->prepare(
								"UPDATE ".$plugin_prefix.$posttypeobj."_detail SET post_city=%s, post_region=%s, post_country=%s, post_locations=%s
								WHERE post_location_id=%d AND ( post_city!=%s OR post_region!=%s OR post_country!=%s)",
								array($gd_city,$gd_region,$gd_country,$post_locations,$id,$gd_city,$gd_region,$gd_country)
							);
						$wpdb->query($sql);
					}
				}

			}
			else
			{

				$location_info = array();
				$location_info['city'] = $gd_city;
				$location_info['region'] = $gd_region;
				$location_info['country'] = $gd_country;
				$location_info['country_slug'] = $country_slug;
				$location_info['region_slug'] = $region_slug;
				$location_info['city_slug'] = $city_slug;
				$location_info['city_latitude'] = $gd_latitude;
				$location_info['city_longitude'] = $gd_longitude;
				$location_info['is_default'] = 0;
				$location_info['city_meta'] = $city_meta;
				$location_info['city_desc'] = $city_desc;

				geodir_add_new_location_via_adon($location_info);

				$msg = GD_LOCATION_SAVED;

			}

			$msg = urlencode($msg);

			$location = admin_url()."admin.php?page=geodirectory&tab=managelocation_fields&subtab=geodir_location_manager&location_success=".$msg;
			wp_redirect($location);
			exit;
		}

	}else{
		wp_redirect(home_url().'/?geodir_signup=true');
		exit();
	}

}

/**
 * Delete neighbourhood by ID.
 *
 * @since 1.0.0
 * @package GeoDirectory_Location_Manager
 *
 * @global object $wpdb WordPress Database object.
 * @global string $plugin_prefix Geodirectory plugin table prefix.
 *
 * @param int $hood_id The neighbourhood ID.
 */
function geodir_neighbourhood_delete($hood_id)
{

	global $wpdb,$plugin_prefix;

	$location_hood = $wpdb->get_results(
		$wpdb->prepare(
			"SELECT l.city, n.hood_slug FROM ".POST_LOCATION_TABLE." l, ".POST_NEIGHBOURHOOD_TABLE." n WHERE n.hood_location_id=l.location_id AND hood_id=%d",
			array($hood_id)
		)
	);

	$geodir_posttypes = geodir_get_posttypes();

	foreach($geodir_posttypes as $geodir_posttype){

		$table = $plugin_prefix . $geodir_posttype . '_detail';

		if($wpdb->get_var("SHOW COLUMNS FROM ".$table." WHERE field = 'post_neighbourhood'"))
		{
			if(!empty($location_hood)){
				foreach($location_hood as $hood_del){

					$wpdb->query(
						$wpdb->prepare(
							"UPDATE ".$table." SET post_neighbourhood='' WHERE post_city=%s AND post_neighbourhood=%s",
							array($hood_del->city,$hood_del->hood_slug)
						)
					);

				}
			}

		}
 }

 $wpdb->query($wpdb->prepare("DELETE FROM ".POST_NEIGHBOURHOOD_TABLE." WHERE hood_id=%d",array($hood_id)));

}

/**
 * Merge locations.
 *
 * @since 1.0.0
 * @package GeoDirectory_Location_Manager
 *
 * @global object $wpdb WordPress Database object.
 * @global string $plugin_prefix Geodirectory plugin table prefix.
 */
function geodir_merge_location()
{

	global $wpdb,$plugin_prefix;
	if(isset($_REQUEST['location_merge_nonce']) && current_user_can( 'manage_options' )){

		if ( !wp_verify_nonce( $_REQUEST['location_merge_nonce'], 'location_merge_wpnonce' ) )
		exit;

		$geodir_location_merge_ids = trim($_REQUEST['geodir_location_merge_ids'], ',');

		$gd_merge = $_REQUEST['gd_merge'];

		$gd_city = $_REQUEST['gd_city'];

		$gd_region = $_REQUEST['gd_region'];

		$gd_country = $_REQUEST['gd_country'];

		$gd_lat = $_REQUEST['gd_lat'];

		$gd_log = $_REQUEST['gd_log'];

		$geodir_postlocation_merge_ids = array();

		$geodir_merge_ids_array = explode(',',$geodir_location_merge_ids);

		$geodir_merge_ids_length = count($geodir_merge_ids_array);
		$format = array_fill(0, $geodir_merge_ids_length, '%d');
		$format = implode(',', $format);

		$geodir_postlocation_merge_ids = $geodir_merge_ids_array;
		$geodir_postlocation_merge_ids[] = $gd_merge;

		$gd_location_sql = $wpdb->prepare("select * from ".POST_LOCATION_TABLE." WHERE location_id IN ($format) AND location_id!=%d", $geodir_postlocation_merge_ids );

		 $gd_locationinfo = $wpdb->get_results($gd_location_sql);

		 $check_default = '';
		 foreach($gd_locationinfo as $gd_locationinfo_obj)
		 {

			$locationid = $gd_locationinfo_obj->location_id;

			if(!$check_default){

				$check_default = $wpdb->get_var(
					$wpdb->prepare(
						"SELECT location_id FROM ".POST_LOCATION_TABLE." WHERE is_default='1' AND location_id = %d",
						array($locationid)
					)
				);

			}


			/*$location_hood = $wpdb->get_results(
				$wpdb->prepare(
					"SELECT hood_id FROM ".POST_NEIGHBOURHOOD_TABLE." WHERE hood_location_id=%d",
					array($locationid)
				)
			);

			if(!empty($location_hood)){
				foreach($location_hood as $hood_del){

					geodir_neighbourhood_delete($hood_del->hood_id);

				}
			}*/


			$gd_location_del = $wpdb->prepare("DELETE FROM ".POST_LOCATION_TABLE." WHERE  location_id = %d",array($locationid));

			$wpdb->query($gd_location_del);

		 }


		 	$country_slug = create_location_slug($gd_country);
			$region_slug = create_location_slug($gd_region);
			$city_slug = create_location_slug($gd_city);

		 //FILL SELECTED CITY IN MERGE LOCATIONS POST
		 $geodir_posttypes = geodir_get_posttypes();

		 foreach($geodir_posttypes as $geodir_posttype){

			 $table = $plugin_prefix . $geodir_posttype . '_detail';

			 $gd_placedetail_sql = $wpdb->prepare(
			 					"select * from ". $table." WHERE post_location_id IN ($format)",
								$geodir_merge_ids_array
								);

			 $gd_placedetailinfo = $wpdb->get_results($gd_placedetail_sql);

			 foreach($gd_placedetailinfo as $gd_placedetailinfo_obj)
			 {
				$postid = $gd_placedetailinfo_obj->post_id;

				$post_locations =  '['.$city_slug.'],['.$region_slug.'],['.$country_slug.']'; // set all overall post location

				 $gd_rep_locationid = $wpdb->prepare("UPDATE ". $table." SET
										post_location_id=%d,
										post_city	= %s,
										post_region	= %s,
										post_country	= %s,
										post_locations = %s
										WHERE  post_id = %d",
										array($gd_merge,$gd_city,$gd_region,$gd_country,$post_locations,$postid));

				$wpdb->query($gd_rep_locationid);

			 }

		 }


		$setdefault = '';
		if(isset($check_default) && $check_default!='')
		{
			$setdefault = ", is_default='1'";

		}

		//UPDATE SELECTED LOCATION

		$sql = $wpdb->prepare("UPDATE ".POST_LOCATION_TABLE." SET
				country=%s,
				region=%s,
				city=%s,
				city_latitude=%s,
				city_longitude=%s,
				country_slug = %s,
				region_slug = %s,
				city_slug = %s
				".$setdefault."
				WHERE location_id = %d",
				array($gd_country,$gd_region,$gd_city,$gd_lat,$gd_log,$country_slug,$region_slug,$city_slug,$gd_merge));

		$wpdb->query($sql);

		if($setdefault != '')
			geodir_location_set_default($gd_merge);

		/* ----- update hooks table ---- */

		$location_hood_info = $wpdb->query(
			$wpdb->prepare(
				"UPDATE ".POST_NEIGHBOURHOOD_TABLE." SET hood_location_id=".$gd_merge." WHERE hood_location_id IN ($format)",
				$geodir_merge_ids_array
			)
		);


			$msg = MSG_LOCATION_MERGE_SUCCESS;
			$msg = urlencode($msg);

		 $location = admin_url()."admin.php?page=geodirectory&tab=managelocation_fields&subtab=geodir_location_manager&location_success=".$msg;

		 wp_redirect($location);

		 exit;

		}else{
			wp_redirect(home_url().'/?geodir_signup=true');
			exit();
		}
}


/**
 * Set default location.
 *
 * @since 1.0.0
 * @package GeoDirectory_Location_Manager
 *
 * @global object $wpdb WordPress Database object.
 *
 * @param int $locationid Location ID.
 */
function geodir_location_set_default($locationid)
{

	global $wpdb;

	$wpdb->query("update ".POST_LOCATION_TABLE." set is_default='0'");

	$gd_location_default = $wpdb->prepare("UPDATE ".POST_LOCATION_TABLE." SET
							is_default='1'
							WHERE  location_id = %d", array($locationid) );

	$wpdb->query($gd_location_default);

	$geodir_location = $wpdb->get_row("SELECT * FROM ".POST_LOCATION_TABLE." WHERE is_default='1'", "OBJECT" );

	update_option('geodir_default_location', $geodir_location); // UPDATE DEFAULT LOCATION OPTION

}

/**
 * Handles set default location request.
 *
 * @since 1.0.0
 * @package GeoDirectory_Location_Manager
 *
 * @global object $wpdb WordPress Database object.
 */
function geodir_set_default()
{
	global $wpdb;

	if(isset($_REQUEST['_wpnonce']) && isset($_REQUEST['id']) && current_user_can( 'manage_options' )){

		$locationid = $_REQUEST['id'];

		if ( !wp_verify_nonce( $_REQUEST['_wpnonce'], 'location_action_'.$_REQUEST['id'] ) )
				return;

		geodir_location_set_default($locationid);

		$msg = MSG_LOCATION_SET_DEFAULT;
		$msg = urlencode($msg);

		$location = admin_url()."admin.php?page=geodirectory&tab=managelocation_fields&subtab=geodir_location_manager&location_success=".$msg;

		wp_redirect($location);

		exit;

	}else{
		wp_redirect(home_url().'/?geodir_signup=true');
		exit();
	}

}


/**
 * Handles location deletion request.
 *
 * @since 1.0.0
 * @package GeoDirectory_Location_Manager
 *
 * @global object $wpdb WordPress Database object.
 * @global string $plugin_prefix Geodirectory plugin table prefix.
 *
 * @return bool
 */
function geodir_delete_location() {
	global $wpdb, $plugin_prefix;
	
	if ( isset( $_REQUEST['_wpnonce'] ) && isset( $_REQUEST['id'] ) && current_user_can( 'manage_options' ) ) {
		if ( is_array( $_REQUEST['id'] ) && !empty( $_REQUEST['id'] ) ) {
			$ids = $_REQUEST['id'];
			
			if ( !wp_verify_nonce( $_REQUEST['_wpnonce'], 'location_action_bulk_delete' ) ) {
				return false;
			}
			
			$success = 0;
			foreach ( $ids as $id ) {				
				if ( geodir_location_delete_by_id( $id ) ) {					
					$success++;
				}
			}
			
			$message = __( 'No location deleted.', GEODIRLOCATION_TEXTDOMAIN );
			
			if ( $success > 0 ) {
				$message = $success > 1 ? wp_sprintf( __( '%d locations deleted successfully.', GEODIRLOCATION_TEXTDOMAIN ), $success ) : __( 'Location deleted successfully', GEODIRLOCATION_TEXTDOMAIN );
			}
			
			$message = urlencode( $message );
			
			if ( isset( $_REQUEST['return'] ) && !empty( $_REQUEST['return'] ) ) {
				$location = $_REQUEST['return'] . '&location_success=' . $message;
			} else {
				$location = admin_url() . 'admin.php?page=geodirectory&tab=managelocation_fields&subtab=geodir_location_manager&location_success=' . $message;
			}		
		} else {
			$id = $_REQUEST['id'];
	
			if ( !wp_verify_nonce( $_REQUEST['_wpnonce'], 'location_action_' . $id ) )
				return false;
				
			$message = __( 'No location deleted.', GEODIRLOCATION_TEXTDOMAIN );
			
			if ( geodir_location_delete_by_id( $id ) ) {
				$message = MSG_LOCATION_DELETED;
			}
	
			$message = urlencode( $message );
			$location = admin_url() . "admin.php?page=geodirectory&tab=managelocation_fields&subtab=geodir_location_manager&location_success=" . $message;
		}
		
		wp_redirect( $location );
		exit;
	} else {
		wp_redirect( home_url() . '/?geodir_signup=true' );
		exit;
	}
}

//DELETE NEIGHBOURHOOD FUNCTION

/**
 * Handles neighbourhood deletion request.
 *
 * @since 1.0.0
 * @package GeoDirectory_Location_Manager
 *
 * @global object $wpdb WordPress Database object.
 */
function geodir_delete_hood()
{
	global $wpdb;

	if(isset($_REQUEST['_wpnonce']) && isset($_REQUEST['id']) && isset($_REQUEST['city_id']) && current_user_can( 'manage_options' )){

	if ( !wp_verify_nonce( $_REQUEST['_wpnonce'], 'neighbourhood_delete_'.$_REQUEST['id'] ) )
				return;

	$hoodid = $_REQUEST['id'];
	$city_id = $_REQUEST['city_id'];

	if($hoodid)
	{

		geodir_neighbourhood_delete($hoodid);

		$msg = MSG_NEIGHBOURHOOD_DELETED;
		$msg = urlencode($msg);

		$location = admin_url()."admin.php?page=geodirectory&tab=managelocation_fields&subtab=geodir_location_manager&location_success=".$msg."&city_hood=hoodlist&id=".$city_id;
		wp_redirect($location);

		exit;
	}

	}else{
		wp_redirect(home_url().'/?geodir_signup=true');
		exit();
	}

}

/**
 * Get neighbourhoods for the given location ID.
 *
 * @since 1.0.0
 * @package GeoDirectory_Location_Manager
 *
 * @global object $wpdb WordPress Database object.
 *
 * @param string|int $location Location ID.
 * @return bool|mixed
 */
function geodir_get_neighbourhoods($location = '')
{

	global $wpdb;

	$neighbourhoods = $wpdb->get_results($wpdb->prepare("SELECT * FROM ".POST_NEIGHBOURHOOD_TABLE." WHERE hood_location_id = %d ORDER BY hood_name ", array($location)));

	return (!empty($neighbourhoods)) ?  $neighbourhoods : false;

}


/**
 * Default settings for location manager.
 *
 * @since 1.0.0
 * @package GeoDirectory_Location_Manager
 *
 * @param array $arr
 * @return array
 */
function geodir_location_default_options($arr=array())
{


	$country_array= geodir_get_countries_array();

	$args=array(
					'what'=>'region' ,
					'echo' => false,
					'filter_by_non_restricted'=>false,
					'format'=> array('type'=>'array')
				);

	$region_obj= (array)geodir_get_location_array($args);
	$region_array = '' ;
	foreach( $region_obj as $region)
	{
		$region_array[$region->region] = $region->region ;
	}


	$args=array(
							'what'=>'city' ,
							'echo' => false,
							'filter_by_non_restricted'=>false,
							'format'=> array('type'=>'array')
						);

	$city_obj= (array)geodir_get_location_array($args);
	$city_array = '' ;
	foreach( $city_obj as $city)
	{
		$city_array[$city->city] = $city->city ;
	}

	$arr[] = array( 'name' => __( 'Location Settings', GEODIRLOCATION_TEXTDOMAIN ), 'type' => 'no_tabs', 'desc' => '', 'id' => 'location_setting_options' );

	$arr[] = array( 'name' => __( 'Main Navigation Settings', GEODIRLOCATION_TEXTDOMAIN), 'type' => 'sectionstart', 'id' => 'location_setting_switcher_options');

	$arr[] = array(
		'name' => __( 'Show location switcher in menu', GEODIRLOCATION_TEXTDOMAIN ),
		'desc' 		=> sprintf(__( 'Show change location navigation in main menu? (untick to disable) If you disable this option, none of the change location link will appear in main navigation.', GEODIRLOCATION_TEXTDOMAIN )),
		'id' 		=> 'geodir_show_changelocation_nave',
		'std' 		=> '',
		'type' 		=> 'checkbox',
		'value' => '1',
	);


	$arr[] = array(
		'name' => 	'',
		'desc' 		=> __( 'List drilled-down Regions, Cities.', GEODIRLOCATION_TEXTDOMAIN ),
		'id' 		=> 'geodir_location_switcher_list_mode',
		'std' 		=> '',
		'type' 		=> 'radio',
		'value'		=> 'drill',
		'radiogroup'		=> 'start'
	);

	$arr[] = array(
			'name' => '',
			'desc' 		=> __( 'List all Countries, Regions, Cities.', GEODIRLOCATION_TEXTDOMAIN ),
			'id' 		=> 'geodir_location_switcher_list_mode',
			'std' 		=> '',
			'type' 		=> 'radio',
			'value'		=> 'all',
			'radiogroup'		=> 'end'
		);


	$arr[] = array( 'type' => 'sectionend', 'id' => 'location_setting_switcher_options');


	/*$arr[] = array( 'name' => GD_LOCATION_SETTINGS, 'type' => 'sectionstart', 'id' => 'location_setting_default_options');

	$arr[] = array(
			'name'  => GD_LOCATION_MULTICITY,
			'desc' 	=> GD_LOCATION_MULTICITY_DESC,
			'id' 	=> 'location_multicity',
			'std' 		=> '',
			'type' 	=> 'checkbox',
			'std' 	=> 'yes'
		);

	$arr[] = array(
			'name'  => GD_LOCATION_EVERYWHERE,
			'desc' 	=> GD_LOCATION_EVERYWHERE_DESC,
			'id' 	=> 'location_everywhere',
			'std' 		=> '',
			'type' 	=> 'checkbox',
			'std' 	=> 'yes'
		);

	$arr[] = array( 'type' => 'sectionend', 'id' => 'location_setting_default_options');*/


		/* -------- start location settings ----- */
	$arr[] = array( 'name' => GD_LOCATION_SETTINGS, 'type' => 'sectionstart', 'id' => 'geodir_location_setting');

	$arr[] = array(
		'name' => __( 'Home Page Results', GEODIRLOCATION_TEXTDOMAIN ),
		'desc' 		=> __( 'Show default location results on home page (First time only, if geodirectory home page is your site home page and user comes to home page).', GEODIRLOCATION_TEXTDOMAIN ),
		'id' 		=> 'geodir_result_by_location',
		'std' 		=> 'everywhere',
		'type' 		=> 'radio',
		'value'		=> 'default',
		'radiogroup'		=> 'start'
	);

	$arr[] = array(
			'name' => '',
			'desc' 		=> __( 'Show everywhere location results on home page (First time only, if geodirectory home page is your site home page and user comes to home page).', GEODIRLOCATION_TEXTDOMAIN ),
			'id' 		=> 'geodir_result_by_location',
			'std' 		=> 'everywhere',
			'type' 		=> 'radio',
			'value'		=> 'everywhere',
			'radiogroup'		=> 'end'
		);

	$arr[] = array('name' => '',
	'id' 		=> '',
	'type' => 'field_seperator',
	);

	$arr[] = array(
		'name' => __( 'Country', GEODIRLOCATION_TEXTDOMAIN ),
		'desc' 		=> __( 'Enable default country (country drop-down will not appear on add listing and location switcher).', GEODIRLOCATION_TEXTDOMAIN ),
		'id' 		=> 'geodir_enable_country',
		'std' 		=> 'multi',
		'type' 		=> 'radio',
		'value'		=> 'default',
		'radiogroup'		=> 'start'
	);

	$arr[] = array(
			'name' => '',
			'desc' 		=> __( 'Enable Multi Countries', GEODIRLOCATION_TEXTDOMAIN ),
			'id' 		=> 'geodir_enable_country',
			'std' 		=> 'multi',
			'type' 		=> 'radio',
			'value'		=> 'multi',
			'radiogroup'		=> ''
		);

	$arr[] = array(
			'name' => '',
			'desc' 		=> __( 'Enable Selected Countries', GEODIRLOCATION_TEXTDOMAIN ),
			'id' 		=> 'geodir_enable_country',
			'std' 		=> 'multi',
			'type' 		=> 'radio',
			'value'		=> 'selected',
			'radiogroup'		=> 'end'
		);


	$arr[] = array(
	'name' => '',
		'desc' 		=> __( 'Only select countries will appear in country drop-down on add listing page and location switcher. Make sure to have default country in your selected countries list for proper site functioning.', GEODIRLOCATION_TEXTDOMAIN ),
		'tip' 		=> '',
		'id' 		=> 'geodir_selected_countries',
		'css' 		=> 'min-width:300px;',
		'std' 		=> array(),
		'type' 		=> 'multiselect',
		'placeholder_text' => __( 'Select Countries', GEODIRLOCATION_TEXTDOMAIN ),
		'class'		=> 'chosen_select',
		'options' =>  $country_array
	);

	$arr[] = array(
			'name' => '',
			'desc' 		=> __( 'Add everywhere option in location switcher country drop-down.', GEODIRLOCATION_TEXTDOMAIN ),
			'id' 		=> 'geodir_everywhere_in_country_dropdown',
			'std' 		=> '1',
			'type' 		=> 'checkbox',
			'value'		=> '1',
		);

	$arr[] = array('name' => '',
	'id' 		=> '',
	'type' => 'field_seperator',
	);

	/*state*/
	$arr[] = array(
		'name' => __( 'Region', GEODIRLOCATION_TEXTDOMAIN ),
		'desc' 		=> __( 'Enable default region (region drop-down will not appear on add listing and location switcher).', GEODIRLOCATION_TEXTDOMAIN ),
		'id' 		=> 'geodir_enable_region',
		'std' 		=> 'multi',
		'type' 		=> 'radio',
		'value'		=> 'default',
		'radiogroup'		=> 'start'
	);

	$arr[] = array(
			'name' => '',
			'desc' 		=> __( 'Enable Multi Regions', GEODIRLOCATION_TEXTDOMAIN ),
			'id' 		=> 'geodir_enable_region',
			'std' 		=> 'multi',
			'type' 		=> 'radio',
			'value'		=> 'multi',
			'radiogroup'		=> ''
		);

	$arr[] = array(
			'name' => '',
			'desc' 		=> __( 'Enable Selected Regions', GEODIRLOCATION_TEXTDOMAIN ),
			'id' 		=> 'geodir_enable_region',
			'std' 		=> 'multi',
			'type' 		=> 'radio',
			'value'		=> 'selected',
			'radiogroup'		=> 'end'
		);

	$arr[] = array(
	'name' => '',
		'desc' 		=> __( 'Only select regions will appear in region drop-down on add listing page and location switcher. Make sure to have default region in your selected regions list for proper site functioning', GEODIRLOCATION_TEXTDOMAIN ),
		'tip' 		=> '',
		'id' 		=> 'geodir_selected_regions',
		'css' 		=> 'min-width:300px;',
		'std' 		=> array(),
		'type' 		=> 'multiselect',
		'placeholder_text' => __( 'Select Regions', GEODIRLOCATION_TEXTDOMAIN ),
		'class'		=> 'chosen_select',
		'options' => $region_array
	);

	$arr[] = array(
			'name' => '',
			'desc' 		=> __( 'Add everywhere option in location switcher region drop-down.', GEODIRLOCATION_TEXTDOMAIN ),
			'id' 		=> 'geodir_everywhere_in_region_dropdown',
			'std' 		=> '1',
			'type' 		=> 'checkbox',
			'value'		=> '1',
		);

	$arr[] = array('name' => '',
	'id' 		=> '',
	'type' => 'field_seperator',
	);

	/*city*/
	$arr[] = array(
		'name' => __( 'City', GEODIRLOCATION_TEXTDOMAIN ),
		'desc' 		=> __( 'Enable default city (City drop-down will not appear on add listing and location switcher).', GEODIRLOCATION_TEXTDOMAIN ),
		'id' 		=> 'geodir_enable_city',
		'std' 		=> 'multi',
		'type' 		=> 'radio',
		'value'		=> 'default',
		'radiogroup'		=> 'start'
	);

	$arr[] = array(
			'name' => '',
			'desc' 		=> __( 'Enable Multicity', GEODIRLOCATION_TEXTDOMAIN ),
			'id' 		=> 'geodir_enable_city',
			'std' 		=> 'multi',
			'type' 		=> 'radio',
			'value'		=> 'multi',
			'radiogroup'		=> ''
		);

	$arr[] = array(
			'name' => '',
			'desc' 		=> __( 'Enable Selected City', GEODIRLOCATION_TEXTDOMAIN ),
			'id' 		=> 'geodir_enable_city',
			'std' 		=> 'multi',
			'type' 		=> 'radio',
			'value'		=> 'selected',
			'radiogroup'		=> 'end'
		);

	$arr[] = array(
	'name' => '',
		'desc' 		=> __( 'Only select cities will appear in city drop-down on add listing page and location switcher. Make sure to have default city in your selected cities list for proper site functioning', GEODIRLOCATION_TEXTDOMAIN ),
		'tip' 		=> '',
		'id' 		=> 'geodir_selected_cities',
		'css' 		=> 'min-width:300px;',
		'std' 		=> array(),
		'type' 		=> 'multiselect',
		'placeholder_text' => __( 'Select Cities', GEODIRLOCATION_TEXTDOMAIN ),
		'class'		=> 'chosen_select',
		'options' => $city_array
	);

	$arr[] = array(
			'name' => '',
			'desc' 		=> __( 'Add everywhere option in location switcher city drop-down.', GEODIRLOCATION_TEXTDOMAIN ),
			'id' 		=> 'geodir_everywhere_in_city_dropdown',
			'std' 		=> '1',
			'type' 		=> 'checkbox',
			'value'		=> '1',
		);

	$arr[] = array('name' => '',
	'id' 		=> '',
	'type' => 'field_seperator',
	);

	$arr[] = array(
			'name'  => GD_LOCATION_NEIGHBOURHOODS,
			'desc' 	=> GD_LOCATION_NEIGHBOURHOODS_DESC,
			'id' 	=> 'location_neighbourhoods',
			'std' 		=> '',
			'type' 	=> 'checkbox',
			'std' 	=> 'yes'
		);






	$arr[] = array( 'type' => 'sectionend', 'id' => 'geodir_location_setting');

	$arr[] = array( 'name' => __( 'Add listing form settings', GEODIRLOCATION_TEXTDOMAIN ), 'type' => 'sectionstart', 'id' => 'geodir_location_setting_add_listing');

	$arr[] = array(
			'name'  => __( 'Disable Google address autocomplete?', GEODIRLOCATION_TEXTDOMAIN ),
			'desc' 	=> __( 'This will stop the address sugestions when typing in address box on add listing page.', GEODIRLOCATION_TEXTDOMAIN ),
			'id' 	=> 'location_address_fill',
			'std' 		=> '',
			'type' 	=> 'checkbox'
		);

	$arr[] = array(
			'name'  => __( 'Show all locations in dropdown?', GEODIRLOCATION_TEXTDOMAIN ),
			'desc' 	=> __( 'This is usefull if you have a small directory but can break your site if you have many locations', GEODIRLOCATION_TEXTDOMAIN ),
			'id' 	=> 'location_dropdown_all',
			'std' 		=> '',
			'type' 	=> 'checkbox'
		);

	$arr[] = array(
			'name'  => __( 'Disable set address on map from changing address fields', GEODIRLOCATION_TEXTDOMAIN ),
			'desc' 	=> __( 'This is usefull if you have a small directory and you have custom locations or your locations are not known by the Google API and they break the address. (highly recommended not to enable this)', GEODIRLOCATION_TEXTDOMAIN ),
			'id' 	=> 'location_set_address_disable',
			'std' 		=> '',
			'type' 	=> 'checkbox'
		);

    $arr[] = array(
        'name'  => __( 'Disable move map pin from changing address fields', GEODIRLOCATION_TEXTDOMAIN ),
        'desc' 	=> __( 'This is usefull if you have a small directory and you have custom locations or your locations are not known by the Google API and they break the address. (highly recommended not to enable this)', GEODIRLOCATION_TEXTDOMAIN ),
        'id' 	=> 'location_set_pin_disable',
        'std' 		=> '',
        'type' 	=> 'checkbox'
    );

	$arr[] = array( 'type' => 'sectionend', 'id' => 'geodir_location_setting_add_listing');


	/* -------- end location settings ----- */


	$arr = apply_filters('geodir_location_default_options' ,$arr );

	return $arr;
}


/**
 * Get locations by keyword.
 *
 * @since 1.0.0
 * @package GeoDirectory_Location_Manager
 *
 * @global object $wpdb WordPress Database object.
 *
 * @param string $term Search type. Possible values are 'country', 'region', 'city'.
 * @param string $search Keyword.
 * @param bool $single Return only single row? Default: false.
 * @return bool|mixed
 */
function geodir_get_locations($term = '', $search = '', $single = false)
{

	global $wpdb;

	$where = $group_by = '';

	$where_array = array();

	switch($term):
		case 'country':
			if($search !='' ){
				$where = $wpdb->prepare(" AND ( country = %s OR country_slug = %s )", array($search,$search));
			}else{ $group_by = " GROUP BY country ";}
		break;
		case 'region':
			if($search !='' ){
				$where = $wpdb->prepare(" AND ( region = %s OR region_slug = %s ) ", array($search,$search));
			}else{ $group_by = " GROUP BY region ";}
		break;
		case 'city':
			if($search !='' ){
				$where = $wpdb->prepare(" AND ( city = %s OR city_slug = %s ) ", array($search,$search));
			}else{ $group_by = " GROUP BY city ";}
		break;
	endswitch;

	$locations = $wpdb->get_results(
			"SELECT * FROM ".POST_LOCATION_TABLE." WHERE 1=1 ".$where.$group_by." ORDER BY city "
	);

	return (!empty($locations)) ?  $locations : false;

}
/**/

/**
 * Get default location latitude.
 *
 * @since 1.0.0
 * @package GeoDirectory_Location_Manager
 *
 * @param float $lat Default latitude.
 * @param string $is_default Is default?
 * @return string Default location latitude.
 */
function geodir_location_default_latitude($lat, $is_default)
{

	if($is_default == '1' && isset($_SESSION['gd_multi_location']) && !isset($_REQUEST['pid']) && !isset($_REQUEST['backandedit']) && !isset($_SESSION['listing'])){

		if(isset($_SESSION['gd_city']) && $_SESSION['gd_city'] != '' )
			$location = geodir_get_locations('city',$_SESSION['gd_city']);
		elseif(isset($_SESSION['gd_region']) && $_SESSION['gd_region'] != '' )
			$location = geodir_get_locations('region',$_SESSION['gd_region']);
		elseif(isset($_SESSION['gd_country']) && $_SESSION['gd_country'] != '' )
			$location = geodir_get_locations('country',$_SESSION['gd_country']);

		if(isset($location) && $location)
			$location = end($location);

		$lat = isset($location->city_latitude) ? $location->city_latitude : '';
	}

	return $lat;

}

/**
 * Get default location longitude.
 *
 * @since 1.0.0
 * @package GeoDirectory_Location_Manager
 *
 * @param float $lat Default longitude.
 * @param string $is_default Is default?
 * @return string Default location longitude.
 */
function geodir_location_default_longitude($lat, $is_default)
{

	if($is_default == '1' && isset($_SESSION['gd_multi_location']) && !isset($_REQUEST['pid']) && !isset($_REQUEST['backandedit']) && !isset($_SESSION['listing'])){

		if(isset($_SESSION['gd_city']) && $_SESSION['gd_city'] != '' )
			$location = geodir_get_locations('city',$_SESSION['gd_city']);
		elseif(isset($_SESSION['gd_region']) && $_SESSION['gd_region'] != '' )
			$location = geodir_get_locations('region',$_SESSION['gd_region']);
		elseif(isset($_SESSION['gd_country']) && $_SESSION['gd_country'] != '' )
			$location = geodir_get_locations('country',$_SESSION['gd_country']);

		if(isset($location) && $location)
			$location = end($location);

		$lat = isset($location->city_longitude) ? $location->city_longitude : '';
	}

	return $lat;
}


/**
 * Function for addons to add new location.
 *
 * @since 1.0.0
 * @package GeoDirectory_Location_Manager
 *
 * @global object $wpdb WordPress Database object.
 *
 * @param array $location_info Location information.
 * @return mixed
 */
function geodir_add_new_location_via_adon($location_info)
{

	global $wpdb;
	if(!empty($location_info)){

		$get_location_info = $wpdb->get_row($wpdb->prepare("SELECT * from ".POST_LOCATION_TABLE." where city like %s AND region like %s AND country like %s",array($location_info['city'],$location_info['region'],$location_info['country'] )), "OBJECT" );

		if(empty($get_location_info)){

			$city_meta = isset($location_info['city_meta']) ? $location_info['city_meta'] : '';
			$city_desc = isset($location_info['city_desc']) ? $location_info['city_desc'] : '';

			$wpdb->query(
				$wpdb->prepare("INSERT INTO ".POST_LOCATION_TABLE." SET
					city = %s,
					region = %s,
					country = %s,
					country_slug = %s,
					region_slug = %s,
					city_slug = %s,
					city_latitude = %s,
					city_longitude = %s,
					is_default	=	%s ,
					city_meta = %s,
					city_desc = %s",

					array($location_info['city'],$location_info['region'],$location_info['country'],$location_info['country_slug'],$location_info['region_slug'],$location_info['city_slug'],$location_info['city_latitude'],$location_info['city_longitude'],$location_info['is_default'],$city_meta,$city_desc)

				)
			);

			$last_location_id = $wpdb->insert_id;

			$location_info = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".POST_LOCATION_TABLE." WHERE location_id=%d",array($last_location_id)), "OBJECT" );

		}else{
			$location_info = $get_location_info;
		}

	}

	return $location_info;
}

/**
 * Adds extra
 *
 * @since 1.0.0
 * @package GeoDirectory_Location_Manager
 *
 * @param $address
 * @param $field_info
 */
function geodir_location_address_extra_admin_fields($address, $field_info)
{
		(isset($field_info->is_admin) && $field_info->is_admin=='1') ? $display_field = 'style="display:none;"' : $display_field = '';?>

			<tr <?php echo $display_field;?> >
					<td ><strong><?php _e('Display City :', GEODIRLOCATION_TEXTDOMAIN);?></strong></td>
					<td align="left">
						<input type="checkbox"  name="extra[show_city]" id="show_city"  value="1" <?php if(isset($address['show_city']) && $address['show_city']=='1'){ echo 'checked="checked"';}?> />
						<span><?php _e('Select if you want to show city field in address section.', GEODIRLOCATION_TEXTDOMAIN);?></span>
					</td>
			</tr>

			<tr>
					<td ><strong><?php _e('City label :', GEODIRLOCATION_TEXTDOMAIN);?></strong></td>
					<td align="left">
						<input type="text" name="extra[city_lable]" id="city_lable"  value="<?php if(isset($address['city_lable'])){ echo $address['city_lable'];}?>" />
						<span><?php _e('Enter city field label in address section.', GEODIRLOCATION_TEXTDOMAIN);?></span>
					</td>
			</tr>

		 <?php (isset($field_info->is_admin) && $field_info->is_admin=='1') ? $display_field = 'style="display:none;"' : $display_field = '';?>
			<tr <?php echo $display_field;?> >
					<td ><strong><?php _e('Display Region :', GEODIRLOCATION_TEXTDOMAIN);?></strong></td>
					<td align="left">
						<input type="checkbox"  name="extra[show_region]" id="show_region"  value="1" <?php if(isset($address['show_region']) && $address['show_region']=='1'){ echo 'checked="checked"';}?>/>
						<span><?php _e('Select if you want to show region field in address section.', GEODIRLOCATION_TEXTDOMAIN);?></span>
					</td>
			</tr>

			<tr>
					<td ><strong><?php _e('Region label :', GEODIRLOCATION_TEXTDOMAIN);?></strong></td>
					<td align="left">
						<input type="text" name="extra[region_lable]" id="region_lable"  value="<?php if(isset($address['region_lable'])){ echo $address['region_lable'];}?>" />
						<span><?php _e('Enter region field label in address section.', GEODIRLOCATION_TEXTDOMAIN);?></span>
					</td>
			</tr>

		 <?php (isset($field_info->is_admin) && $field_info->is_admin=='1') ? $display_field = 'style="display:none;"' : $display_field = '';?>
			<tr <?php echo $display_field;?> >
					<td ><strong><?php _e('Display Country :', GEODIRLOCATION_TEXTDOMAIN);?></strong></td>
					<td align="left">
						<input type="checkbox"  name="extra[show_country]" id="show_country"  value="1" <?php if(isset($address['show_country']) && $address['show_country']=='1'){ echo 'checked="checked"';}?>/>
						<span><?php _e('Select if you want to show country field in address section.', GEODIRLOCATION_TEXTDOMAIN);?></span>
					</td>
			</tr>

		 <tr>
				<td ><strong><?php _e('Country label :', GEODIRLOCATION_TEXTDOMAIN);?></strong></td>
				<td align="left">
					<input type="text" name="extra[country_lable]" id="country_lable"  value="<?php if(isset($address['country_lable'])) {echo $address['country_lable'];}?>" />
					<span><?php _e('Enter country field label in address section.', GEODIRLOCATION_TEXTDOMAIN);?></span>
				</td>
		</tr>
	<?php
}




//// Location DB requests

/**
 * Parse location list for DB request.
 *
 * @since 1.0.0
 * @package GeoDirectory_Location_Manager
 *
 * @param $list
 * @return string
 */
function geodir_parse_location_list($list)
{
	$list_for_query ='';
	if(!empty($list))
	{
		$list_array = explode(',' , $list);
		if(!empty($list_array ))
		{
			foreach($list_array as $list_item)
			{
				$list_for_query .= "," . "'".mb_strtolower($list_item )."'" ;
			}
		}
	}
	if(!empty($list_for_query))
		$list_for_query  = trim($list_for_query , ',');

	return $list_for_query ;
}

/**
 * Get current location city or region or country info.
 *
 * @since 1.0.0
 * @package GeoDirectory_Location_Manager
 *
 * @return string city or region or country info.
 */
function geodir_what_is_current_location()
{
	$city = geodir_get_current_location(array('what' => 'city' , 'echo'=>false)) ;
	$region = geodir_get_current_location(array('what' => 'region' , 'echo'=>false)) ;
	$country = geodir_get_current_location(array('what' => 'country' , 'echo'=>false)) ;

	if(!empty($city))
		return 'city' ;

	if(!empty($region))
		return 'region' ;

	if(!empty($country))
		return 'country' ;

	return '';

}

add_filter('geodir_seo_meta_location_description', 'geodir_set_location_meta_desc', 10);
/**
 * Add location information to the meta description.
 *
 * @since 1.0.0
 * @package GeoDirectory_Location_Manager
 *
 * @global object $wpdb WordPress Database object.
 * @global object $wp WordPress object.
 *
 * @param string $seo_desc Meta description text.
 * @return null|string Altered meta desc.
 */
function geodir_set_location_meta_desc( $seo_desc='' ){
	global $wpdb, $wp;

	$gd_country = get_query_var( 'gd_country' );
	$gd_region = get_query_var( 'gd_region' );
	$gd_city = get_query_var( 'gd_city' );

	if ($gd_city) {
		$info = geodir_city_info_by_slug($gd_city, $gd_country, $gd_region);
		if (!empty($info)) {
			$seo_desc .= $info->city_meta!='' ? $info->city_meta : $info->city_meta;
		}
	} else if (!$gd_city && $gd_region) {
		$info = geodir_location_seo_by_slug($gd_region, 'region', $gd_country);
		if (!empty($info)) {
			$seo_desc .= $info->seo_desc!='' ? $info->seo_desc : $info->seo_title;
		}
	} else if (!$gd_city && !$gd_region && $gd_country) {
		$info = geodir_location_seo_by_slug($gd_country, 'country');
		if (!empty($info)) {
			$seo_desc .= $info->seo_desc!='' ? $info->seo_desc : $info->seo_title;
		}
	}
	$location_desc = $seo_desc;
	if ($location_desc=='') {
		return NULL;
	} else {
		return $location_desc;
	}

}

/**
 * Save category location.
 *
 * @since 1.0.0
 * @package GeoDirectory_Location_Manager
 *
 * @global object $wpdb WordPress Database object.
 */
function geodir_save_cat_location() {
	global $wpdb;

	$wpnonce = isset($_REQUEST['wpnonce']) ? $_REQUEST['wpnonce'] : '';
	$locid = isset($_REQUEST['locid']) ? (int)$_REQUEST['locid'] : '';
	$catid = isset($_REQUEST['catid']) ? (int)$_REQUEST['catid'] : '';
	$posttype = isset($_REQUEST['posttype']) ? $_REQUEST['posttype'] : '';
	$content = isset($_REQUEST['content']) ? $_REQUEST['content'] : '';
	$loc_default = isset($_REQUEST['loc_default']) ? $_REQUEST['loc_default'] : '';

	$category_taxonomy = geodir_get_taxonomies($posttype);
	$taxonomy = isset($category_taxonomy[0]) && $category_taxonomy[0] ? $category_taxonomy[0] : 'gd_placecategory';

	if(is_admin() && $wpnonce && current_user_can( 'manage_options' ) && $locid>0 && $catid>0 && $posttype) {
		$option = array();
		$option['gd_cat_loc_default'] = (int)$loc_default;
		$option['gd_cat_loc_cat_id'] = $catid;
		$option['gd_cat_loc_post_type'] = $posttype;
		$option['gd_cat_loc_taxonomy'] = $taxonomy;
		$option_name = 'geodir_cat_loc_'.$posttype.'_'.$catid;

		update_option($option_name, $option);

		$option = array();
		$option['gd_cat_loc_loc_id'] = (int)$locid;
		$option['gd_cat_loc_cat_id'] = (int)$catid;
		$option['gd_cat_loc_post_type'] = $posttype;
		$option['gd_cat_loc_taxonomy'] = $taxonomy;
		$option['gd_cat_loc_desc'] = $content;
		$option_name = 'geodir_cat_loc_'.$posttype.'_'.$catid.'_'.$locid;

		update_option($option_name, $option);

		echo 'OK';
		exit;
	}
	echo 'FAIL';
	exit;
}

/**
 * Change category location.
 *
 * @since 1.0.0
 * @package GeoDirectory_Location_Manager
 *
 * @global object $wpdb WordPress Database object.
 */
function geodir_change_cat_location() {
	global $wpdb;

	$wpnonce = isset($_REQUEST['wpnonce']) ? $_REQUEST['wpnonce'] : '';
	$gd_location = isset($_REQUEST['locid']) ? (int)$_REQUEST['locid'] : '';
	$term_id = isset($_REQUEST['catid']) ? (int)$_REQUEST['catid'] : '';
	$post_type = isset($_REQUEST['posttype']) ? $_REQUEST['posttype'] : '';

	if(is_admin() && $wpnonce && current_user_can( 'manage_options' ) && $gd_location>0 && $term_id>0 && $post_type) {
		$option_name = 'geodir_cat_loc_'.$post_type.'_'.$term_id.'_'.$gd_location;
		$option = get_option($option_name);
		$gd_cat_loc_desc = !empty($option) && isset($option['gd_cat_loc_desc']) ? $option['gd_cat_loc_desc'] : '';
		echo stripslashes_deep($gd_cat_loc_desc);
		exit;
	}
	echo 'FAIL';
	exit;
}

/**
 * Get actual location name.
 *
 * @since 1.0.0
 * @package GeoDirectory_Location_Manager
 *
 * @param $type
 * @param $term
 * @param bool $translated
 * @return null|string|void
 */
function get_actual_location_name($type, $term, $translated=false) {
	if ($type=='' || $term=='') {
		return NULL;
	}
	$row = geodir_get_locations($type, $term);
	$value = !empty($row) && !empty($row[0]) && isset($row[0]->$type) ? $row[0]->$type : '';
	if( $translated ) {
		$value = __( $value, GEODIRECTORY_TEXTDOMAIN );
	}
	return $value;
}

/**
 * Get location count for a country.
 *
 * @since 1.0.0
 * @package GeoDirectory_Location_Manager
 *
 * @global object $wpdb WordPress Database object.
 * @global string $plugin_prefix Geodirectory plugin table prefix.
 *
 * @param string $country Country name.
 * @param string $country_slug Country slug.
 * @param bool $with_translated Return with translation? Default: true.
 * @return int Listing count.
 */
function count_listings_by_country( $country, $country_slug='', $with_translated=false ) {
	global $wpdb, $plugin_prefix;

	$geodir_posttypes = geodir_get_posttypes();

	$total = 0;
	if ( $country == '' ) {
		return $total;
	}

	foreach( $geodir_posttypes as $geodir_posttype ) {
		$table = $plugin_prefix . $geodir_posttype . '_detail';

		if( $with_translated ) {
			$country_translated = __( $country, GEODIRECTORY_TEXTDOMAIN);
			$sql = "SELECT COUNT(*) FROM " . $table . " WHERE post_country LIKE '".$country."' OR post_country LIKE '".$country_translated."' OR post_locations LIKE '%,[".$country_slug."]'";
		} else {
			$sql = $wpdb->prepare( "SELECT COUNT(*) FROM " . $table . " WHERE post_country LIKE %s", array( $country ) );
		}
		$count = (int)$wpdb->get_var( $sql );

		$total += $count;
	}
	return $total;
}

/**
 * Get countries from post location table.
 *
 * @since 1.0.0
 * @package GeoDirectory_Location_Manager
 *
 * @global object $wpdb WordPress Database object.
 *
 * @return array Countries array.
 */
function get_post_location_countries() {
	global $wpdb;
	$sql = "SELECT country, country_slug, count(location_id) AS total FROM " . POST_LOCATION_TABLE . " WHERE country_slug != '' && country != '' GROUP BY country_slug ORDER BY country ASC";
	$rows = $wpdb->get_results( $sql );
	return $rows;
}

/**
 * Get post country using country slug.
 *
 * @since 1.0.0
 * @package GeoDirectory_Location_Manager
 *
 * @global object $wpdb WordPress Database object.
 *
 * @param string $country_slug Country slug.
 * @return null|string Country name.
 */
function get_post_country_by_slug( $country_slug ) {
	global $wpdb;
	$sql = $wpdb->prepare( "SELECT country FROM " . POST_LOCATION_TABLE . " WHERE country_slug != '' && country_slug = %s GROUP BY country_slug ORDER BY country ASC", array( $country_slug ) );
	$value = $wpdb->get_var( $sql );
	return $value;
}

/**
 * Update location with translated string.
 *
 * @since 1.0.0
 * @package GeoDirectory_Location_Manager
 *
 * @global object $wpdb WordPress Database object.
 * @global string $plugin_prefix Geodirectory plugin table prefix.
 *
 * @param $country_slug
 * @return bool
 */
function geodir_update_location_translate( $country_slug ) {
	global $wpdb, $plugin_prefix;
	if( $country_slug == '' ) {
		return false;
	}

	$country = get_post_country_by_slug( $country_slug );
	if( $country == '' ) {
		return false;
	}

	$geodir_posttypes = geodir_get_posttypes();

	$country_translated = __( $country, GEODIRECTORY_TEXTDOMAIN );
	$country_translated = trim( wp_unslash( $country_translated ) );
	$country_slug_translated = sanitize_title( $country_translated );

	$country_slug = apply_filters( 'geodir_filter_update_location_translate', $country_slug, $country, $country_translated, $country_slug_translated );

	do_action( 'geodir_action_update_location_translate', $country_slug, $country, $country_translated, $country_slug_translated );

	if( $country_slug == $country_slug_translated ) {
		return false;
	}

	$sql = $wpdb->prepare( "SELECT location_id FROM " . POST_LOCATION_TABLE . " WHERE country_slug=%s", array( $country_slug ) );
	$location_ids = $wpdb->get_col( $sql );

	/* update in post locations table */
	$update_locations = false;
	//$sql = $wpdb->prepare( "UPDATE " . POST_LOCATION_TABLE . " SET country=%s, country_slug=%s WHERE country_slug=%s", array( $country_translated, $country_slug_translated, $country_slug ) );
	$sql = $wpdb->prepare( "UPDATE " . POST_LOCATION_TABLE . " SET country_slug=%s WHERE country_slug=%s", array( $country_slug_translated, $country_slug ) );
	$update_locations = $wpdb->query($sql);

	/* update in post listings table */
	$update_listings = false;
	if( !empty( $location_ids ) ) {
		$location_ids = implode( ",", $location_ids );
		foreach( $geodir_posttypes as $geodir_posttype ) {
			$table = $plugin_prefix . $geodir_posttype . '_detail';

			$sql = "SELECT post_id, post_locations, post_location_id FROM " . $table . " WHERE post_location_id IN(" . $location_ids  . ")";
			$listings = $wpdb->get_results( $sql );

			if( !empty( $listings ) ) {
				foreach( $listings as $listing ) {
					$post_id = $listing->post_id;
					$location_id = $listing->post_location_id;
					$post_locations = $listing->post_locations;
					if( $post_locations != '' ) {
						$post_locations_arr = explode( ",", $post_locations );

						if( isset( $post_locations_arr[2] ) && trim($post_locations_arr[2]) != '[]' ) {
							$post_locations_arr[2] = '[' . $country_slug_translated . ']';
							$post_locations = implode( ",", $post_locations_arr );
						} else {
							$post_locations = '';
						}
					}

					if( $post_locations == '' ) {
						$location_info = geodir_get_location_by_id( '', $location_id );
						if( !empty( $location_info ) && isset( $location_info->location_id ) ) {
							$post_locations = '['. $location_info->city_slug .'],['. $location_info->region_slug .'],['. $country_slug_translated .']';
						}
					}

					$sql = $wpdb->prepare( "UPDATE " . $table . " SET post_locations=%s, post_country=%s WHERE post_id=%d", array( $post_locations, $country_translated, $post_id ) );
					$update_locations = $wpdb->query($sql);
				}
			}
		}
		$update_locations = true;
	}

	/* update in location seo table */
	$update_location_seo = false;
	$sql = $wpdb->prepare( "UPDATE " . LOCATION_SEO_TABLE . " SET country_slug=%s WHERE country_slug=%s", array( $country_slug_translated, $country_slug ) );
	$update_location_seo = $wpdb->query($sql);

	if( $update_locations || $update_listings || $update_location_seo ) {
		return true;
	}
	return false;
}

/**
 * Returns countries search SQL.
 *
 * @since 1.0.0
 * @package GeoDirectory_Location_Manager
 *
 * @param string $search Search string.
 * @param bool $array Return as array?. Default false.
 * @return array|string Search SQL
 */
function geodir_countries_search_sql( $search = '', $array = false ) {
	$countries = geodir_get_countries_array();
	$return = $array ? array() : '';

	$search = strtolower( trim( $search ) );
	if( $search == '' ) {
		return $return;
	}

	if( !empty( $countries ) ) {
		foreach( $countries as $row => $value ) {
			$strfind = strtolower( $value );

			if( $row != $value && strpos( $strfind, $search ) === 0 ) {
				$return[] = $row;
			}
		}
	}
	if( $array ) {
		return $return;
	}
	$return = !empty( $return ) ? implode( ",", $return ) : '';
	return $return;
}

/**
 * Clean up location permalink url.
 *
 * @since 1.0.0
 * @package GeoDirectory_Location_Manager
 *
 * @param string $url Permalink url.
 * @return null|string Url.
 */
function geodir_location_permalink_url( $url ) {
	if ( $url == '' ) {
		return NULL;
	}

	if ( get_option( 'permalink_structure' ) != '' ) {
		$url = trim( $url );
		$url = rtrim( $url, '/' ) . '/';
	}

	$url = apply_filters( 'geodir_location_filter_permalink_url', $url );

	return $url;
}

add_action( 'wp_ajax_gd_location_manager_set_user_location', 'gd_location_manager_set_user_location' );
add_action( 'wp_ajax_nopriv_gd_location_manager_set_user_location', 'gd_location_manager_set_user_location' );

/**
 * Set user location.
 *
 * @since 1.0.0
 * @package GeoDirectory_Location_Manager
 *
 * @global object $wpdb WordPress Database object.
 */
function gd_location_manager_set_user_location(){
	global $wpdb;
	$_SESSION['user_lat']=$_POST['lat'];
	$_SESSION['user_lon']=$_POST['lon'];
	if(isset($_POST['myloc']) && $_POST['myloc']){
	$_SESSION['my_location']=1;
	}else{
	$_SESSION['my_location']=0;
	}
	$_SESSION['user_pos_time']=time();
	die();
}

/**
 * Remove location and its data using location ID.
 *
 * @since 1.0.0
 * @package GeoDirectory_Location_Manager
 *
 * @global object $wpdb WordPress Database object.
 * @global string $plugin_prefix Geodirectory plugin table prefix.
 *
 * @param int $id Location ID.
 * @return bool Returns true when successful deletion.
 */
function geodir_location_delete_by_id( $id ) {
	global $wpdb, $plugin_prefix;
	
	if ( !current_user_can( 'manage_options' ) || !$id > 0 ) {
		return false;
	}

	$geodir_posttypes = geodir_get_posttypes();
	
	do_action( 'geodir_location_before_delete', $id );
	
	$location_info = $wpdb->get_row( $wpdb->prepare( "SELECT city_slug, is_default FROM " . POST_LOCATION_TABLE . " WHERE location_id = %d", array( $id ) ) );
	if ( !empty( $location_info ) && !empty( $location_info->is_default ) ) {
		return false; // Default location
	}
	
	foreach( $geodir_posttypes as $geodir_posttype ) {
		
		$table = $plugin_prefix . $geodir_posttype . '_detail';
		
		$rows = $wpdb->get_results( $wpdb->prepare( "SELECT post_id FROM " . $table . " WHERE post_location_id = %d", array( $id ) ) );
		
		if ( !empty( $rows ) ) {
			foreach ( $rows as $row ) {
				wp_delete_post( $row->post_id ); // Delete post
			}
		}
	}
	
	// Remove neighbourhood location
	$wpdb->query( $wpdb->prepare( "DELETE FROM " . POST_NEIGHBOURHOOD_TABLE . " WHERE hood_location_id = %d", array( $id ) ) );
			
	// Remove current location data
	if ( !empty( $location_info ) && !empty( $location_info->city_slug ) && isset( $_SESSION['gd_city'] ) && $_SESSION['gd_city'] == $location_info->city_slug ) {
		unset(	$_SESSION['gd_multi_location'], $_SESSION['gd_city'], $_SESSION['gd_region'], $_SESSION['gd_country'] );
	}
	
	// Remove post location data
	$wpdb->query( $wpdb->prepare( "DELETE FROM " . POST_LOCATION_TABLE . " WHERE location_id = %d", array( $id ) ) );
	
	do_action( 'geodir_location_after_delete', $id );
	
	return true;
}

/**
 * Get location countries.
 *
 * @since 1.0.0
 * @package GeoDirectory_Location_Manager
 *
 * @global object $wpdb WordPress Database object.
 *
 * @param bool $list Return as list? Default: false.
 * @return array|mixed
 */
function geodir_post_location_countries( $list = false ) {
	global $wpdb;
	$sql = "SELECT country, country_slug, count(location_id) AS total FROM " . POST_LOCATION_TABLE . " WHERE country_slug != '' && country != '' GROUP BY country_slug ORDER BY country ASC";
	$rows = $wpdb->get_results( $sql );
	
	$items = array();
	if ( $list && !empty( $rows ) ) {
		foreach( $rows as $row ) {
			$items[$row->country_slug] = get_actual_location_name( 'country', $row->country_slug, true );
		}
		
		asort( $items );
		
		$rows = $items;
	}
	
	return $rows;	
}

/**
 * Count neighbourhoods using location ID.
 *
 * @since 1.0.0
 * @package GeoDirectory_Location_Manager
 *
 * @global object $wpdb WordPress Database object.
 *
 * @param int $location_id Location ID.
 * @return null|string|int Neighbourhood Count.
 */
function geodir_count_hood_by_location( $location_id ) {
	global $wpdb;
	
	if ( !(int)$location_id > 0 ) {
		return NULL;
	}
	
	$sql = $wpdb->prepare( "SELECT COUNT(hood_id) FROM " . POST_NEIGHBOURHOOD_TABLE . " WHERE hood_location_id = %d", array( $location_id ) );
	$result = $wpdb->get_var( $sql );
	
	return $result;
}

/**
 * Get location list for manager location page.
 *
 * @since 1.0.0
 * @package GeoDirectory_Location_Manager
 *
 * @param array $args Location list query arags.
 * @return array Location array.
 */
function geodir_manage_location_get_list( $args = array() ) {
	
	$per_page = isset( $_REQUEST['per_page'] ) ? absint( $_REQUEST['per_page'] ) : 0;
	$search = isset( $_REQUEST['s'] ) ? wp_unslash( trim( $_REQUEST['s'] ) ) : '';
	$country = isset( $_REQUEST['country'] ) ? wp_unslash( trim( $_REQUEST['country'] ) ) : '';
	$per_page = $per_page > 0 ? $per_page : 20;
	
	$pagination_args = wp_parse_args( 
										$args, 
										array(
											'per_page' => $per_page,
											'search' => $search,
											'country' => $country,
										)
									);
	$rows = geodir_location_list( $pagination_args );
	
	return $rows;
}

/**
 * Get locations using given arguments.
 *
 * @since 1.0.0
 * @package GeoDirectory_Location_Manager
 *
 * @global object $wpdb WordPress Database object.
 *
 * @param array $args Query args.
 * @return array Location array.
 */
function geodir_location_list( $args = array() ) {
	global $wpdb;

	$where = '';
	
	if ( !empty( $args['search'] ) && $args['search'] != '' ) {
		$where .= "AND ( city LIKE '" . wp_slash( $args['search'] ) . "%' OR region LIKE '" . wp_slash( $args['search'] ) . "%' ) ";
	}
	
	if ( !empty( $args['country'] ) && $args['country'] != '' ) {
		$where .= "AND ( country LIKE '" . wp_slash( $args['country'] ) . "' OR country_slug LIKE '" . wp_slash( $args['country'] ) . "' ) ";
	}
	
	$sql = "SELECT COUNT(location_id) FROM " . POST_LOCATION_TABLE . " WHERE 1=1 " . $where;
	$total_items = $wpdb->get_var( $sql );
	
	if ( !empty( $args['count'] ) ) {
		return $total_items;
	}
	
	$total_pages = ( $total_items > 0 && isset( $args['per_page'] ) && $args['per_page'] > 0 ) ? ceil( $total_items / $args['per_page'] ) : 0;
	$args['total_pages'] = $total_pages;
	
	$pagenum = isset( $_REQUEST['paged'] ) ? absint( $_REQUEST['paged'] ) : 0;
	
	if ( isset( $args['total_pages'] ) && $pagenum > $args['total_pages'] ) {
		$pagenum = $args['total_pages'];
	}
	
	$pagenum = max( 1, $pagenum );
	$args['total_items'] = $total_items;
	$args['pagenum'] = $pagenum;
	
	$limits = '';
	if ( isset( $args['per_page'] ) && $args['per_page'] > 0 ) {
		$offset = ( $pagenum - 1 ) * $args['per_page'];
		if ( $offset > 0 ) {
			$limits = 'LIMIT ' . $offset . ',' . $args['per_page'];
		} else {
			$limits = 'LIMIT ' . $args['per_page'];
		}
	}
	
	$sql = "SELECT * FROM " . POST_LOCATION_TABLE . " WHERE 1=1 " . $where . " ORDER BY city, region, country ASC " . $limits;

	$items = $wpdb->get_results( $sql );
	$result = array();
	$result['items'] = $items;
	$result['total_items'] = $total_items;
	$result['total_pages'] = $total_pages;
	$result['pagenum'] = $pagenum;	
	$result['pagination'] = geodir_location_admin_pagination( $args );
	$result['pagination_top'] = geodir_location_admin_pagination( $args, 'top' );
	$result['filter_box'] = geodir_location_admin_search_box( __( 'Filter', GEODIRLOCATION_TEXTDOMAIN ), 'location' );

	return $result;
}

/**
 * Admin location pagination.
 *
 * @since 1.0.0
 * @package GeoDirectory_Location_Manager
 *
 * @param array $args Pagination arguments.
 * @param string $which Pagination position.
 * @return string Pagination HTML.
 */
function geodir_location_admin_pagination( $args, $which = 'bottom' ) {
	if ( empty( $args ) || empty( $args['total_items'] ) ) {
		return;
	}

	$total_items = $args['total_items'];
	$total_pages = $args['total_pages'];
	$infinite_scroll = false;
	if ( isset( $args['infinite_scroll'] ) ) {
		$infinite_scroll = $args['infinite_scroll'];
	}

	$output = '<span class="displaying-num">' . sprintf( _n( '1 item', '%s items', $total_items ), number_format_i18n( $total_items ) ) . '</span>';

	$current = $args['pagenum'];

	$current_url = set_url_scheme( 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] );

	$current_url = esc_url( remove_query_arg( array( 'hotkeys_highlight_last', 'hotkeys_highlight_first' ), $current_url ), '', '' );

	$page_links = array();

	$disable_first = $disable_last = '';
	if ( $current == 1 ) {
		$disable_first = ' disabled';
	}
	if ( $current == $total_pages ) {
		$disable_last = ' disabled';
	}
	$page_links[] = sprintf( "<a class='%s' title='%s' href='%s'>%s</a>",
		'first-page' . $disable_first,
		esc_attr__( 'Go to the first page', GEODIRLOCATION_TEXTDOMAIN ),
		esc_url( remove_query_arg( 'paged', $current_url ) ),
		'&laquo;'
	);

	$page_links[] = sprintf( "<a class='%s' title='%s' href='%s'>%s</a>",
		'prev-page' . $disable_first,
		esc_attr__( 'Go to the previous page', GEODIRLOCATION_TEXTDOMAIN ),
		esc_url( add_query_arg( 'paged', max( 1, $current-1 ), $current_url ) ),
		'&lsaquo;'
	);

	$html_current_page = $current;
	
	$html_total_pages = sprintf( "<span class='total-pages'>%s</span>", number_format_i18n( $total_pages ) );
	$page_links[] = '<span class="paging-input">' . sprintf( _x( '%1$s of %2$s', 'paging' ), $html_current_page, $html_total_pages ) . '</span>';

	$page_links[] = sprintf( "<a class='%s' title='%s' href='%s'>%s</a>",
		'next-page' . $disable_last,
		esc_attr__( 'Go to the next page', GEODIRLOCATION_TEXTDOMAIN ),
		esc_url( add_query_arg( 'paged', min( $total_pages, $current+1 ), $current_url ) ),
		'&rsaquo;'
	);

	$page_links[] = sprintf( "<a class='%s' title='%s' href='%s'>%s</a>",
		'last-page' . $disable_last,
		esc_attr__( 'Go to the last page', GEODIRLOCATION_TEXTDOMAIN ),
		esc_url( add_query_arg( 'paged', $total_pages, $current_url ) ),
		'&raquo;'
	);

	$pagination_links_class = 'pagination-links';
	if ( ! empty( $infinite_scroll ) ) {
		$pagination_links_class = ' hide-if-js';
	}
	$output .= "\n<span class='$pagination_links_class'>" . join( "\n", $page_links ) . '</span>';

	if ( $total_pages ) {
		$page_class = $total_pages < 2 ? ' one-page' : '';
	} else {
		$page_class = ' no-pages';
	}
	$pagination = "<div class='tablenav-pages{$page_class}'>$output</div>";

	return $pagination;
}

/**
 * 'Manage location' page search form.
 *
 * @since 1.0.0
 * @package GeoDirectory_Location_Manager
 *
 * @param string $text Submit button text.
 * @param string $text_input_id HTML id for input box.
 * @return string search form HTML.
 */
function geodir_location_admin_search_box( $text, $text_input_id ) {
	$input_id = $text_input_id . '-search-input';
	$current_url = set_url_scheme( 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] );
	$current_url = remove_query_arg( array( 'hotkeys_highlight_last', 'hotkeys_highlight_first' ), $current_url );
	$current_url_search = esc_url( remove_query_arg( array( 's', 'country', 'paged' ), $current_url ), '', '' );
	$current_url = esc_url( $current_url);
	
	$countries = geodir_post_location_countries( true );
	$country = isset( $_REQUEST['country'] ) ? wp_unslash( trim( $_REQUEST['country'] ) ) : '';
	
	ob_start();
	?>
	<label class="screen-reader-text" for="geodir_country"><?php echo __( 'Select Country', GEODIRLOCATION_TEXTDOMAIN ); ?></label>
	<select id="geodir_country" name="geodir_country">
		<option style="color:#888888" value=""><?php echo __( 'Country', GEODIRLOCATION_TEXTDOMAIN ); ?></option>
		<?php if ( !empty( $countries ) ) { ?>
			<?php foreach ( $countries as $country_slug => $country_text ) { ?>
				<option value="<?php echo $country_slug; ?>" <?php echo ( $country_slug == $country ? 'selected="selected"' : '' ); ?>><?php echo $country_text; ?></option>
			<?php } ?>
		<?php } ?>
	</select>
	<input type="search" onkeypress="return geodir_filter_location(event)" id="<?php echo $input_id ?>" placeholder="<?php echo esc_attr__( 'City or Region', GEODIRLOCATION_TEXTDOMAIN ); ?>" name="s" value="<?php _admin_search_query(); ?>" />&nbsp;&nbsp;<input type="button" value="<?php echo $text; ?>" class="button" id="<?php echo $text_input_id . '-search-submit'; ?>" name="<?php echo $text_input_id . '_search_submit'; ?>" onclick="return geodir_filter_location()" />&nbsp;&nbsp;<input type="button" value="<?php _e( 'Reset', GEODIRLOCATION_TEXTDOMAIN ); ?>" class="button" id="<?php echo $text_input_id . '-search-reset'; ?>" name="<?php echo $text_input_id . '_search_reset'; ?>" onclick="jQuery('#geodir_country').val('');jQuery('#location-search-input').val('');return geodir_filter_location();" /><input type="hidden" id="gd_location_page_url" value="<?php echo $current_url;?>" /><input type="hidden" id="gd_location_bulk_url" value="<?php echo esc_url( admin_url().'admin-ajax.php?action=geodir_locationajax_action&location_ajax_action=delete&_wpnonce=' . wp_create_nonce( 'location_action_bulk_delete' ) ); ?>" />
	<script type="text/javascript"> function geodir_filter_location(e) { 
	if( typeof e=='undefined' || ( typeof e!='undefined' && e.keyCode == '13' ) ) { if( typeof e!='undefined' ) { e.preventDefault(); } window.location.href = '<?php echo $current_url_search;?>&s='+jQuery('#location-search-input').val()+'&country='+jQuery('#geodir_country').val(); } } </script>
	<?php 
	$content = ob_get_clean();
	
	return $content;
}