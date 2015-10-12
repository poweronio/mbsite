<?php
/**
 * Term and review count, common functions.
 *
 * @since 1.0.0
 * @package GeoDirectory_Location_Manager
 */

/**
 * Get review count or term count.
 *
 * @since 1.0.0
 * @package GeoDirectory_Location_Manager
 *
 * @global object $wpdb WordPress Database object.
 * @global string $plugin_prefix Geodirectory plugin table prefix.
 *
 * @param int|string $term_id The term ID.
 * @param string $taxonomy Taxonomy slug.
 * @param string $post_type The post type.
 * @param string $location_type Location type. Possible values 'gd_city','gd_region','gd_country'.
 * @param array $loc {
 *    Attributes of the location array.
 *
 *    @type string $gd_country The country slug.
 *    @type string $gd_region The region slug.
 *    @type string $gd_city The city slug.
 *
 * }
 * @param string $count_type Count type. Possible values are 'review_count', 'term_count'.
 * @return int|null|string
 */
function geodir_filter_listings_where_set_loc( $term_id, $taxonomy, $post_type, $location_type,$loc, $count_type ) {
	global $wpdb, $plugin_prefix;

	$table = $plugin_prefix . $post_type . '_detail';

    if(!$loc){

        $loc = geodir_get_current_location_terms();
    }

	$country ='';
	$region ='';
	$city = '';
	if (isset($loc['gd_city']) && $loc['gd_city'] != '') {
		$city = $loc['gd_city'];
	}
	if (isset($loc['gd_region']) && $loc['gd_region'] != '') {
		$region = $loc['gd_region'];
	}
	if (isset($loc['gd_country']) && $loc['gd_country'] != '') {
		$country = $loc['gd_country'];
	}

	$where = '';
	if ( $country!= '') {
		$where .= " AND post_locations LIKE '%,[".$country."]' ";
	}

	if ( $region != '' && $location_type!='gd_country' ) {
		$where .= " AND post_locations LIKE '%,[".$region."],%' ";
	}

	if ( $city != '' && $location_type!='gd_country' && $location_type!='gd_region' ) {
		$where .= " AND post_locations LIKE '[".$city."],%' ";
	}


	if ($count_type == 'review_count') {
		$sql = "SELECT COALESCE(SUM(rating_count),0) FROM  $table WHERE post_status = 'publish' $where AND FIND_IN_SET(" . $term_id . ", " . $taxonomy . ")";
	}else {
		$sql = "SELECT COUNT(post_id) FROM  $table WHERE post_status = 'publish' $where AND FIND_IN_SET(" . $term_id . ", " . $taxonomy . ")";
	}
	/**
	 * Filter terms count sql query.
	 *
	 * @since 1.3.8
	 * @param string $sql Database sql query..
	 * @param int $term_id The term ID.
	 * @param int $taxonomy The taxonomy Id.
	 * @param string $post_type The post type.
	 * @param string $location_type Location type .
	 * @param string $loc Current location terms.
	 * @param string $count_type The term count type.
	 */
	$sql = apply_filters('geodir_location_count_reviews_by_term_sql', $sql, $term_id, $taxonomy, $post_type, $location_type, $loc, $count_type);

	///echo $sql;exit;
	$count = $wpdb->get_var($sql);

	return $count;
    //todo: Following code is unreachable. remove it if not necessary.
    $count = 0;
	if ($count_type == 'review_count') {
		foreach($rows as $post) {
			$count = $count + $post->comment_count;
		}
	} elseif ($count_type == 'term_count') {
		$count = count($rows);
	}

	return $count;
}

/**
 * Insert term count for a location.
 *
 * @since 1.0.0
 * @package GeoDirectory_Location_Manager
 *
 * @global object $wpdb WordPress Database object.
 *
 * @param $location_name
 * @param string $location_type Location type. Possible values 'gd_city','gd_region','gd_country'.
 * @param string $count_type Count type. Possible values are 'review_count', 'term_count'.
 * @param null $row_id
 * @param array $loc {
 *    Attributes of the location array.
 *
 *    @type string $gd_country The country slug.
 *    @type string $gd_region The region slug.
 *    @type string $gd_city The city slug.
 *
 * }
 * @return array
 */
function geodir_insert_term_count_by_loc($location_name, $location_type, $count_type, $row_id=null,$loc) {
    global $wpdb;
	$post_types = geodir_get_posttypes();
	$term_array = array();
	foreach($post_types as $post_type) {
		$taxonomy = geodir_get_taxonomies($post_type);
		$taxonomy = $taxonomy[0];

        $args = array(
            'hide_empty' => false,
			'gd_no_loop' => true
        );

		$terms = get_terms($taxonomy, $args);
		foreach ($terms as $term) {
			$count = geodir_filter_listings_where_set_loc($term->term_id, $taxonomy, $post_type, $location_type, $loc, $count_type);
			$term_array[$term->term_id] = $count;
		}
	}

	$data = serialize($term_array);

    if ( $row_id ) {
        $wpdb->query(
            $wpdb->prepare(
                "UPDATE " . GEODIR_TERM_META . " set
                " . $count_type . " = %s WHERE id=" . $row_id . "",
                array( $data )
            )
        );

    } else {
        $wpdb->query(
            $wpdb->prepare(
                "INSERT into " . GEODIR_TERM_META . " set
                location_type = %s,
                location_name = %s,
                " . $count_type . " = %s",
                array( $location_type, $location_name, $data )
            )
        );

    }
	return $term_array;
}

/**
 * Get term count for a location.
 *
 * @since 1.0.0
 * @package GeoDirectory_Location_Manager
 *
 * @global object $wpdb WordPress Database object.
 *
 * @param string $count_type Count type. Possible values are 'review_count', 'term_count'.
 * @param null|string $location_name Location name slug. Ex: new-york.
 * @param null|string $location_type Location type. Possible values 'gd_city','gd_region','gd_country'.
 * @param bool $force_update Do you want to force update? default: false.
 * @param bool|array $loc {
 *    Attributes of the location array.
 *
 *    @type string $gd_country The country slug.
 *    @type string $gd_region The region slug.
 *    @type string $gd_city The city slug.
 *
 * }
 * @return array|mixed|void
 */
function geodir_get_loc_term_count($count_type = 'term_count', $location_name=null, $location_type=null, $force_update=false, $loc=false ) {
	//accepted count type: term_count, review_count
	global $wpdb;

    if (!$location_name || !$location_type) {
        $loc = geodir_get_current_location_terms();

        if (isset($loc['gd_city']) && $loc['gd_city'] != '') {
            $location_name = $loc['gd_city'];
            $location_type = 'gd_city';
        } elseif (isset($loc['gd_region']) && $loc['gd_region'] != '') {
            $location_name = $loc['gd_region'];
            $location_type = 'gd_region';
        } elseif (isset($loc['gd_country']) && $loc['gd_country'] != '') {
            $location_name = $loc['gd_country'];
            $location_type = 'gd_country';
        }
    }

    if ($location_name && $location_type) {
        $sql = $wpdb->prepare( "SELECT * FROM " . GEODIR_TERM_META . " WHERE location_type=%s AND location_name=%s LIMIT 1", array( $location_type, $location_name ) );
        $row = $wpdb->get_row( $sql );

        if ( $row ) {
            if ( $force_update || !$row->$count_type) {
                return geodir_insert_term_count_by_loc( $location_name, $location_type, $count_type, $row->id,$loc );
            } else {
                $data = unserialize( $row->$count_type );
                return $data;
            }
        } else {
            return geodir_insert_term_count_by_loc( $location_name, $location_type, $count_type,null,$loc );
        }
    } else {
        return;
    }
}

/*-----------------------------------------------------------------------------------*/
/*  Term count functions
/*-----------------------------------------------------------------------------------*/

/**
 * Update post term count for the given post id.
 *
 * @since 1.0.0
 * @package GeoDirectory_Location_Manager
 *
 * @param int $post_id The post ID.
 * @param array $post {
 *    Attributes of the location array.
 *
 *    @type string $post_type The post type.
 *    @type string $post_country The country name.
 *    @type string $post_region The region name.
 *    @type string $post_city The city name.
 *
 * }
 */
function geodir_term_post_count_update($post_id, $post) {
	$geodir_posttypes = geodir_get_posttypes();

    //print_r($post);exit;

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
		return;

    /*print_r($post);
    echo '###';
    print_r($_REQUEST);
    exit;*/
    if(!isset($post['post_type'])){
        $post['post_type'] = get_post_type( $post_id );
    }

    if( !wp_is_post_revision( $post_id ) && isset($post['post_type']) && in_array($post['post_type'],$geodir_posttypes )) {
        //if( !wp_is_post_revision( $post_id ) && isset($post->post_type) && in_array($post->post_type,$geodir_posttypes )) {

		//if ( !wp_verify_nonce( $_POST['geodir_post_info_noncename'], 'geodirectory/geodirectory-admin/admin_functions.php' ) )
		//    return;

		$country = isset($post['post_country']) ? $post['post_country'] : '';
		$region = isset($post['post_region']) ? $post['post_region'] : '';
		$city = isset($post['post_city']) ? $post['post_city'] : '';
		$country_slug = create_location_slug($country);
		$region_slug = create_location_slug($region);
		$city_slug = create_location_slug($city);

		$loc = array();
		$loc['gd_city'] = $city_slug;
		$loc['gd_region'] = $region_slug;
		$loc['gd_country'] = $country_slug;

		foreach($loc as $key => $value) {
			if ($value != '') {
				geodir_get_loc_term_count('term_count', $value, $key, true,$loc);
			}
		}
        //exit;
	}
}


add_action( 'geodir_after_save_listing', 'geodir_term_post_count_update', 100, 2);
//add_action( 'save_post', 'geodir_term_post_count_update', 100, 2);


/**
 * Returns the term count array.
 *
 * @since 1.0.0
 * @package GeoDirectory_Location_Manager
 *
 * @return array|mixed|void
 */
function geodir_get_loc_term_count_filter() {
    $data = geodir_get_loc_term_count('term_count');
    return $data;
}
add_filter( 'geodir_get_term_count_array', 'geodir_get_loc_term_count_filter' );

if (!is_admin() || (defined('DOING_AJAX') && DOING_AJAX)) {
	add_filter('get_terms', 'gd_get_terms', 10, 3);
}

/**
 * Get terms with term count.
 *
 * @since 1.0.0
 * @package GeoDirectory_Location_Manager
 *
 * @param array $arr Term array.
 * @param string $tax Taxonomy name.
 * @param array $args GD args.
 * @return mixed
 */
function gd_get_terms($arr,$tax,$args){


if(isset($args['gd_no_loop'])){return $arr;}// so we don't do an infinit loop

	if(!empty($arr)){
		$term_count = geodir_get_loc_term_count('term_count');	
		
		/**
		 * Filter the terms count by location.
		 *
		 * @since 1.3.4
		 *
		 * @param array $terms_count Array of term count row.
		 * @param array $terms Array of terms.
		 */
		$term_count = apply_filters( 'geodir_loc_term_count', $term_count, $arr );
		
		foreach ($arr as $term) {
			if (isset($term->term_id) && isset($term_count[$term->term_id])) {
				$term->count = $term_count[$term->term_id];
			}
		}
	}

	return $arr;
}

/*-----------------------------------------------------------------------------------*/
/*  Review count functions
/*-----------------------------------------------------------------------------------*/

/**
 * Update review count for each location.
 *
 * @since 1.0.0
 * @package GeoDirectory_Location_Manager
 *
 * @param int $post_id The post ID.
 */
function geodir_term_review_count_update($post_id) {
	$geodir_posttypes = geodir_get_posttypes();
    $post = get_post($post_id);
    if (isset($post->post_type) && in_array($post->post_type,$geodir_posttypes )) {
        $locations = geodir_get_post_meta( $post_id, 'post_locations' );
        if ( $locations ) {
            $array = explode( ',', $locations );

            $loc = array();
            $loc['gd_city'] = str_replace( array( '[', ']' ), '', $array[0] );
            $loc['gd_region'] = str_replace( array( '[', ']' ), '', $array[1] );
            $loc['gd_country'] = str_replace( array( '[', ']' ), '', $array[2] );

            foreach($loc as $key => $value) {
                if ($value != '') {
                    geodir_get_loc_term_count('review_count', $value, $key, true,$loc);
                }
            }

        }
    }
    return;
}

//add_action( 'wp_update_comment_count', 'geodir_term_review_count_update', 100, 1);
add_action( 'geodir_update_postrating', 'geodir_term_review_count_update', 100, 1);


/**
 * Returns the review count array.
 *
 * @since 1.0.0
 * @package GeoDirectory_Location_Manager
 *
 * @return array|mixed|void
 */
function geodir_get_loc_review_count_action() {
    $data = geodir_get_loc_term_count('review_count');

    return $data;
}
add_filter( 'geodir_count_reviews_by_terms_before', 'geodir_get_loc_review_count_action' );