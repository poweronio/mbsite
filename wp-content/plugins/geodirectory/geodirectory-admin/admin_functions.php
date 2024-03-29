<?php
/**
 * Admin functions.
 *
 * @since 1.0.0
 * @package GeoDirectory
 */

/**
 * Updates option value when GeoDirectory get deactivated.
 * 
 * @since 1.0.0
 * @package GeoDirectory
 */
function geodir_deactivation()
{

    // Update installed variable
    update_option("geodir_installed", 0);

    // Remove rewrite rules and then recreate rewrite rules.
    flush_rewrite_rules();
}


/**
 * Deletes option value when GeoDirectory get uninstalled.
 *
 * @since 1.0.0
 * @package GeoDirectory
 */
function geodir_uninstall()
{

    delete_option('geodir_default_data_installed');

}

if (!function_exists('geodir_admin_styles')) {
    /**
     * Enqueue Admin Styles.
     *
     * @since 1.0.0
     * @package GeoDirectory
     */
    function geodir_admin_styles()
    {

        wp_register_style('geodirectory-admin-css', geodir_plugin_url() . '/geodirectory-assets/css/admin.css', array(), GEODIRECTORY_VERSION);
        wp_enqueue_style('geodirectory-admin-css');

        wp_register_style('geodirectory-frontend-style', geodir_plugin_url() . '/geodirectory-assets/css/style.css', array(), GEODIRECTORY_VERSION);
        wp_enqueue_style('geodirectory-frontend-style');

        wp_register_style('geodir-chosen-style', geodir_plugin_url() . '/geodirectory-assets/css/chosen.css', array(), GEODIRECTORY_VERSION);
        wp_enqueue_style('geodir-chosen-style');

        wp_register_style('geodirectory-jquery-ui-timepicker-css', geodir_plugin_url() . '/geodirectory-assets/css/jquery.ui.timepicker.css', array(), GEODIRECTORY_VERSION);
        wp_enqueue_style('geodirectory-jquery-ui-timepicker-css');

        wp_register_style('geodirectory-jquery-ui-css', geodir_plugin_url() . '/geodirectory-assets/css/jquery-ui.css', array(), GEODIRECTORY_VERSION);
        wp_enqueue_style('geodirectory-jquery-ui-css');

        wp_register_style('geodirectory-custom-fields-css', geodir_plugin_url() . '/geodirectory-assets/css/custom_field.css', array(), GEODIRECTORY_VERSION);
        wp_enqueue_style('geodirectory-custom-fields-css');

        wp_register_style('geodirectory-pluplodar-css', geodir_plugin_url() . '/geodirectory-assets/css/pluploader.css', array(), GEODIRECTORY_VERSION);
        wp_enqueue_style('geodirectory-pluplodar-css');

        wp_register_style('geodir-rating-style', geodir_plugin_url() . '/geodirectory-assets/css/jRating.jquery.css', array(), GEODIRECTORY_VERSION);
        wp_enqueue_style('geodir-rating-style');

        wp_register_style('geodir-rtl-style', geodir_plugin_url() . '/geodirectory-assets/css/rtl.css', array(), GEODIRECTORY_VERSION);
        wp_enqueue_style('geodir-rtl-style');

    }
}

if (!function_exists('geodir_admin_styles_req')) {
    /**
     * Loads stylesheets from CDN.
     *
     * @since 1.0.0
     * @package GeoDirectory
     */
    function geodir_admin_styles_req()
    {

        wp_register_style('geodirectory-font-awesome', '//netdna.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css', array(), GEODIRECTORY_VERSION);
        wp_enqueue_style('geodirectory-font-awesome');
    }
}

if (!function_exists('geodir_admin_scripts')) {
    /**
     * Enqueue Admin Scripts.
     *
     * @since 1.0.0
     * @package GeoDirectory
     */
    function geodir_admin_scripts()
    {

        wp_enqueue_script('jquery');
        /*wp_enqueue_script( 'jquery-ui-core' );
        wp_enqueue_script( 'jquery-ui-datepicker' );
        wp_enqueue_script('jquery-ui-slider'); */


        wp_enqueue_script('geodirectory-jquery-ui-timepicker-js', geodir_plugin_url() . '/geodirectory-assets/js/jquery.ui.timepicker.js', array('jquery-ui-datepicker', 'jquery-ui-slider'), '', true);

        wp_register_script('chosen', geodir_plugin_url() . '/geodirectory-assets/js/chosen.jquery.js', array(), GEODIRECTORY_VERSION);
        wp_enqueue_script('chosen');

        wp_register_script('geodirectory-choose-ajax', geodir_plugin_url() . '/geodirectory-assets/js/ajax-chosen.js', array(), GEODIRECTORY_VERSION);
        wp_enqueue_script('geodirectory-choose-ajax');

        if (isset($_REQUEST['listing_type'])) {
            wp_register_script('geodirectory-custom-fields-script', geodir_plugin_url() . '/geodirectory-assets/js/custom_fields.js', array(), GEODIRECTORY_VERSION);
        }

        wp_enqueue_script('geodirectory-custom-fields-script');
        $plugin_path = geodir_plugin_url() . '/geodirectory-functions/cat-meta-functions';

        wp_enqueue_script('tax-meta-clss', $plugin_path . '/js/tax-meta-clss.js', array('jquery'), null, true);

        $map_lang = "&language=" . geodir_get_map_default_language();
        /** This filter is documented in geodirectory_template_tags.php */
        $map_extra = apply_filters('geodir_googlemap_script_extra', '');
        wp_enqueue_script('geodirectory-googlemap-script', '//maps.google.com/maps/api/js?sensor=false' . $map_lang . $map_extra, '', NULL);

        wp_register_script('geodirectory-goMap-script', geodir_plugin_url() . '/geodirectory-assets/js/goMap.js', array(), GEODIRECTORY_VERSION);
        wp_enqueue_script('geodirectory-goMap-script');

        wp_register_script('geodir-jRating-js', geodir_plugin_url() . '/geodirectory-assets/js/jRating.jquery.js', array(), GEODIRECTORY_VERSION);
        wp_enqueue_script('geodir-jRating-js');

        wp_register_script('geodir-on-document-load', geodir_plugin_url() . '/geodirectory-assets/js/on_document_load.js', array(), GEODIRECTORY_VERSION);
        wp_enqueue_script('geodir-on-document-load');


        // SCRIPT FOR UPLOAD
        wp_enqueue_script('plupload-all');
        wp_enqueue_script('jquery-ui-sortable');

        wp_register_script('geodirectory-plupload-script', geodir_plugin_url() . '/geodirectory-assets/js/geodirectory-plupload.js', array(), GEODIRECTORY_VERSION);
        wp_enqueue_script('geodirectory-plupload-script');

        // SCRIPT FOR UPLOAD END


        // place js config array for plupload
        $plupload_init = array(
            'runtimes' => 'html5,silverlight,flash,html4',
            'browse_button' => 'plupload-browse-button', // will be adjusted per uploader
            'container' => 'plupload-upload-ui', // will be adjusted per uploader
            'drop_element' => 'dropbox', // will be adjusted per uploader
            'file_data_name' => 'async-upload', // will be adjusted per uploader
            'multiple_queues' => true,
            'max_file_size' => geodir_max_upload_size(),
            'url' => admin_url('admin-ajax.php'),
            'flash_swf_url' => includes_url('js/plupload/plupload.flash.swf'),
            'silverlight_xap_url' => includes_url('js/plupload/plupload.silverlight.xap'),
            'filters' => array(array('title' => __('Allowed Files', GEODIRECTORY_TEXTDOMAIN), 'extensions' => '*')),
            'multipart' => true,
            'urlstream_upload' => true,
            'multi_selection' => false, // will be added per uploader
            // additional post data to send to our ajax hook
            'multipart_params' => array(
                '_ajax_nonce' => "", // will be added per uploader
                'action' => 'plupload_action', // the ajax action name
                'imgid' => 0 // will be added per uploader
            )
        );
        $base_plupload_config = json_encode($plupload_init);


        $thumb_img_arr = array();

        if (isset($_REQUEST['pid']) && $_REQUEST['pid'] != '')
            $thumb_img_arr = geodir_get_images($_REQUEST['pid']);

        $totImg = '';
        $image_limit = '';
        if (!empty($thumb_img_arr)) {
            foreach ($thumb_img_arr as $img) {
                $curImages = $img->src . ",";
            }

            $totImg = count($thumb_img_arr);
        }


        $gd_plupload_init = array('base_plupload_config' => $base_plupload_config,
            'totalImg' => $totImg,
            'image_limit' => $image_limit,
            'upload_img_size' => geodir_max_upload_size());

        wp_localize_script('geodirectory-plupload-script', 'gd_plupload', $gd_plupload_init);

        $ajax_cons_data = array('url' => __(admin_url('admin-ajax.php')));
        wp_localize_script('geodirectory-custom-fields-script', 'geodir_admin_ajax', $ajax_cons_data);


        wp_register_script('geodirectory-admin-script', geodir_plugin_url() . '/geodirectory-assets/js/admin.js', array(), GEODIRECTORY_VERSION);
        wp_enqueue_script('geodirectory-admin-script');

        wp_enqueue_style('farbtastic');
        wp_enqueue_script('farbtastic');

        $screen = get_current_screen();
        if ($screen->base == 'post' && in_array($screen->post_type, geodir_get_posttypes())) {
            wp_enqueue_script('geodirectory-listing-validation-script', geodir_plugin_url() . '/geodirectory-assets/js/listing_validation_admin.js');
        }

        $ajax_cons_data = array('url' => __(get_option('siteurl') . '?geodir_ajax=true'));
        wp_localize_script('geodirectory-admin-script', 'geodir_ajax', $ajax_cons_data);

    }
}

if (!function_exists('geodir_admin_menu')) {
    /**
     * Admin Menus
     *
     * Sets up the admin menus in wordpress.
     *
     * @since 1.0.0
     * @package GeoDirectory
     * @global array $menu Menu array.
     * @global object $geodirectory GeoDirectory plugin object.
     */
    function geodir_admin_menu()
    {
        global $menu, $geodirectory;

        if (current_user_can('manage_options')) $menu[] = array('', 'read', 'separator-geodirectory', '', 'wp-menu-separator geodirectory');

        add_menu_page(__('Geodirectory', GEODIRECTORY_TEXTDOMAIN), __('Geodirectory', GEODIRECTORY_TEXTDOMAIN), 'manage_options', 'geodirectory', 'geodir_admin_panel', geodir_plugin_url() . '/geodirectory-assets/images/favicon.ico', '55.1984');


    }
}

if (!function_exists('geodir_admin_menu_order')) {
    /**
     * Order admin menus.
     *
     * @since 1.0.0
     * @package GeoDirectory
     * @param array $menu_order Menu order array.
     * @return array Modified menu order array.
     */
    function geodir_admin_menu_order($menu_order)
    {

        // Initialize our custom order array
        $geodir_menu_order = array();

        // Get the index of our custom separator
        $geodir_separator = array_search('separator-geodirectory', $menu_order);

        // Get index of posttype menu
        $post_types = geodir_get_posttypes();
        if (!empty($post_types)) {
            foreach ($post_types as $post_type) {
                $geodir_posts = array_search("edit.php?post_type={$post_type}", $menu_order);
            }
        }

        // Loop through menu order and do some rearranging
        foreach ($menu_order as $index => $item) :

            if ((('geodirectory') == $item)) :
                $geodir_menu_order[] = 'separator-geodirectory';
                if (!empty($post_types)) {
                    foreach ($post_types as $post_type) {
                        $geodir_menu_order[] = 'edit.php?post_type=' . $post_type;
                    }
                }
                $geodir_menu_order[] = $item;

                unset($menu_order[$geodir_separator]);
            //unset( $menu_order[$geodir_places] );
            elseif (!in_array($item, array('separator-geodirectory'))) :
                $geodir_menu_order[] = $item;
            endif;

        endforeach;

        // Return order
        return $geodir_menu_order;
    }
}

if (!function_exists('geodir_admin_custom_menu_order')) {
    /**
     * Enables custom menu order.
     *
     * @since 1.0.0
     * @package GeoDirectory
     * @return bool
     */
    function geodir_admin_custom_menu_order()
    {
        if (!current_user_can('manage_options')) return false;
        return true;
    }
}

/**
 * Function to show success or error message on admin option form submission.
 *
 * @since 1.0.0
 * @package GeoDirectory
 */
function geodir_before_admin_panel()
{
    if (isset($_REQUEST['installed']) && $_REQUEST['installed'] != '') {
        echo '<div id="message" class="updated fade">
                        <p style="float:right;">' . __('Like Geodirectory?', GEODIRECTORY_TEXTDOMAIN) . ' <a href="http://wordpress.org/extend/plugins/Geodirectory/" target="_blank">' . __('Support us by leaving a rating!', GEODIRECTORY_TEXTDOMAIN) . '</a></p>
                        <p><strong>' . __('Geodirectory has been installed and setup. Enjoy :)', GEODIRECTORY_TEXTDOMAIN) . '</strong></p>
                </div>';

    }

    if (isset($_REQUEST['msg']) && $_REQUEST['msg'] != '') {
        switch ($_REQUEST['msg']) {
            case 'success':
                echo '<div id="message" class="updated fade"><p><strong>' . __('Your settings have been saved.', GEODIRECTORY_TEXTDOMAIN) . '</strong></p></div>';
                flush_rewrite_rules(false);

                break;
			case 'fail':
				$gderr = isset($_REQUEST['gderr']) ? $_REQUEST['gderr'] : '';
				
				if ($gderr == 21)
			    	echo '<div id="message" class="error fade"><p><strong>' . __('Error: You can not add same permalinks for both Listing and Location, please try again.', GEODIRECTORY_TEXTDOMAIN) . '</strong></p></div>';
				else
					echo '<div id="message" class="error fade"><p><strong>' . __('Error: Your settings have not been saved, please try again.', GEODIRECTORY_TEXTDOMAIN) . '</strong></p></div>';
                break;
        }
    }

    if (!geodir_is_default_location_set()) {
        echo '<div class="updated fade"><p><strong>' . sprintf(__('Please %sclick here%s to set a default location, this will make the plugin work properly.', GEODIRECTORY_TEXTDOMAIN), '<a href=\'' . admin_url('admin.php?page=geodirectory&tab=default_location_settings') . '\'>', '</a>') . '</strong></p></div>';

    }

    if (!function_exists('curl_init')) {
        echo '<div class="error"><p><strong>' . __('CURL is not installed on this server, this can cause problems, please ask your server admin to install it.', GEODIRECTORY_TEXTDOMAIN) . '</strong></p></div>';

    }
}

/**
 * Handles data posted from GeoDirectory settings form.
 *
 * @since 1.0.0
 * @package GeoDirectory
 * @global array $geodir_settings Geodirectory settings array.
 * @param string $current_tab The current settings tab name.
 */
function geodir_handle_option_form_submit($current_tab)
{
    global $geodir_settings;
    if (file_exists(dirname(__FILE__) . '/option-pages/' . $current_tab . '_array.php')) {
        /**
         * Contains settings array for current tab.
         *
         * @since 1.0.0
         * @package GeoDirectory
         */
        include_once('option-pages/' . $current_tab . '_array.php');
    }
    if (isset($_POST) && $_POST && isset($_REQUEST['page']) && $_REQUEST['page'] == 'geodirectory') :
        if (!wp_verify_nonce($_REQUEST['_wpnonce'], 'geodir-settings')) die(__('Action failed. Please refresh the page and retry.', GEODIRECTORY_TEXTDOMAIN));
        if (!wp_verify_nonce($_REQUEST['_wpnonce-' . $current_tab], 'geodir-settings-' . $current_tab)) die(__('Action failed. Please refresh the page and retry.', GEODIRECTORY_TEXTDOMAIN));
		
		/**
		 * Fires before updating geodirectory admin settings.
		 *
		 * @since 1.4.2
		 *
		 * @param string $current_tab Current tab in geodirectory settings.
		 * @param array  $geodir_settings Array of geodirectory settings.
		 */
		do_action('geodir_before_update_options', $current_tab, $geodir_settings);		
		
        if (!empty($geodir_settings[$current_tab]))
            geodir_update_options($geodir_settings[$current_tab]);

        /**
         * Called after GeoDirectory options settings are updated.
         *
         * @since 1.0.0
         * @param array $geodir_settings The array of GeoDirectory settings.
         * @see 'geodir_before_update_options'
         */
        do_action('geodir_update_options', $geodir_settings);

        /**
         * Called after GeoDirectory options settings are updated.
         *
         * Provides tab specific settings.
         *
         * @since 1.0.0
         * @param string $current_tab The current settings tab name.
         * @param array $geodir_settings[$current_tab] The array of settings for the current settings tab.
         */
        do_action('geodir_update_options_' . $current_tab, $geodir_settings[$current_tab]);

        flush_rewrite_rules(false);

        $current_tab = isset($_REQUEST['tab']) ? $_REQUEST['tab'] : '';

        $redirect_url = admin_url('admin.php?page=geodirectory&tab=' . $current_tab . '&active_tab=' . $_REQUEST['active_tab'] . '&msg=success');

        wp_redirect($redirect_url);
        exit();
    endif;


}


if (!function_exists('geodir_autoinstall_admin_header') && get_option('geodir_installed')) {
    /**
     * GeoDirectory dummy data installation.
     *
     * @since 1.0.0
     * @package GeoDirectory
     * @global object $wpdb WordPress Database object.
     * @global string $plugin_prefix Geodirectory plugin table prefix.
     * @param string $post_type The post type.
     */
    function geodir_autoinstall_admin_header($post_type = 'gd_place')
    {

        global $wpdb, $plugin_prefix;

        if (!geodir_is_default_location_set()) {
            echo '<div class="updated fade"><p><strong>' . sprintf(__('Please %sclick here%s to set a default location, this will help to set location of all dummy data.', GEODIRECTORY_TEXTDOMAIN), '<a href=\'' . admin_url('admin.php?page=geodirectory&tab=default_location_settings') . '\'>', '</a>') . '</strong></p></div>';
        } else {

            $geodir_url = admin_url() . 'admin.php?page=geodirectory&tab=general_settings&active_tab=';

            $post_counts = $wpdb->get_var("SELECT count(post_id) FROM " . $plugin_prefix . $post_type . "_detail WHERE post_dummy='1'");

            if ($post_counts > 0) {
                $nonce = wp_create_nonce('geodir_dummy_posts_delete_noncename');

                $dummy_msg = '<div id="" class="geodir_auto_install updated highlight fade"><p><b>' . SAMPLE_DATA_SHOW_MSG . '</b><br /><a id="geodir_dummy_delete" class="button_delete" onclick="geodir_autoinstall(this,\'geodir_dummy_delete\',\'' . $nonce . '\',\'' . $post_type . '\')" href="javascript:void(0);" redirect_to="' . $geodir_url . '"  >' . DELETE_BTN_SAMPLE_MSG . '</a></p></div>';
                $dummy_msg .= '<div id="" style="display:none;" class="geodir_show_progress updated highlight fade"><p><b>' . GEODIR_SAMPLE_DATA_DELETE_MSG . '</b><br><img src="' . geodir_plugin_url() . '/geodirectory-assets/images/loadingAnimation.gif" /></p></div>';
            } else {
                $options_list = '';
                for ($option = 1; $option <= 30; $option++) {
                    $selected = '';
                    if ($option == 10)
                        $selected = 'selected="selected"';

                    $options_list .= '<option ' . $selected . ' value="' . $option . '">' . $option . '</option>';
                }

                $nonce = wp_create_nonce('geodir_dummy_posts_insert_noncename');

                $dummy_msg = '<div id="" class="geodir_auto_install updated highlight fade"><p><b>' . AUTO_INSATALL_MSG . '</b><br /><select class="selected_sample_data">' . $options_list . '</select><a id="geodir_dummy_insert" class="button_insert" href="javascript:void(0);" onclick="geodir_autoinstall(this,\'geodir_dummy_insert\',\'' . $nonce . '\',\'' . $post_type . '\')"   redirect_to="' . $geodir_url . '" >' . INSERT_BTN_SAMPLE_MSG . '</a></p></div>';
                $dummy_msg .= '<div id="" style="display:none;" class="geodir_show_progress updated highlight fade"><p><b>' . GEODIR_SAMPLE_DATA_IMPORT_MSG . '</b><br><img src="' . geodir_plugin_url() . '/geodirectory-assets/images/loadingAnimation.gif" /><br><span class="dummy_post_inserted"></span></div>';

            }
            echo $dummy_msg;
            ?>
            <script>
                <?php

                    $default_location = geodir_get_default_location();
                  $city =  isset($default_location->city) ? $default_location->city : '';
                  $region =isset($default_location->region) ? $default_location->region : '';
                  $country =isset($default_location->country) ? $default_location->country : '';
                  $city_latitude =isset($default_location->city_latitude) ? $default_location->city_latitude : '';
                  $city_longitude =isset($default_location->city_longitude) ? $default_location->city_longitude : '';

                ?>
                var geocoder = new google.maps.Geocoder();
                var CITY_ADDRESS = '<?php echo $city.','.$region.','.$country;?>';
                var bound_lat_lng;
                var lat = <?php echo $city_latitude; ?>;
                var lng = <?php echo $city_longitude; ?>;
                var latlng = new google.maps.LatLng(lat, lng);
                geocoder.geocode({'address': CITY_ADDRESS},
                    function (results, status) {
                        if (status == google.maps.GeocoderStatus.OK) {
                            // Bounds for North America
                            //	 alert((results[0].geometry.bounds==null))
                            if (results[0].geometry.bounds == null) {
                                bound_lat_lng1 = String(results[0].geometry.viewport.getSouthWest());
                                bound_lat_lng1 = bound_lat_lng1.replace(/[()]/g, "");

                                bound_lat_lng2 = String(results[0].geometry.viewport.getNorthEast());
                                bound_lat_lng2 = bound_lat_lng2.replace(/[()]/g, "");
                                bound_lat_lng2 = bound_lat_lng1 + "," + bound_lat_lng2;
                                bound_lat_lng = bound_lat_lng2.split(',');
                            }
                            else {
                                bound_lat_lng = String(results[0].geometry.bounds);

                                bound_lat_lng = bound_lat_lng.replace(/[()]/g, "");

                                bound_lat_lng = bound_lat_lng.split(',');
                            }

                            strictBounds = new google.maps.LatLngBounds(
                                new google.maps.LatLng(bound_lat_lng[0], bound_lat_lng[1]),
                                new google.maps.LatLng(bound_lat_lng[2], bound_lat_lng[3])
                            );

                        } else {
                            alert("<?php _e('Geocode was not successful for the following reason:',GEODIRECTORY_TEXTDOMAIN);?> " + status);
                        }
                    });


                var dummy_post_index = 1;
                function geodir_autoinstall(obj, id, nonce, posttype) {

                    var active_tab = jQuery(obj).closest('form').find('dl dd.gd-tab-active').attr('id');

                    var total_dummy_post_count = jQuery('#sub_' + active_tab).find('.selected_sample_data').val();

                    if (id == 'geodir_dummy_delete') {
                        if (confirm('<?php _e('Are you sure you want to delete dummy data?' , GEODIRECTORY_TEXTDOMAIN); ?>')) {
                            jQuery('#sub_' + active_tab).find('.geodir_auto_install').hide();
                            jQuery('#sub_' + active_tab).find('.geodir_show_progress').show();
                            jQuery.post('<?php echo geodir_get_ajax_url(); ?>&geodir_autofill=' + id + '&posttype=' + posttype + '&_wpnonce=' + nonce,
                                function (data) {
                                    window.location.href = jQuery('#' + id).attr('redirect_to') + active_tab;
                                });
                            return true;
                        } else {
                            return false;
                        }
                    }
                    else {

                        jQuery('#sub_' + active_tab).find('.geodir_auto_install').hide();
                        jQuery('#sub_' + active_tab).find('.geodir_show_progress').show();
                        jQuery.post('<?php echo geodir_get_ajax_url(); ?>&geodir_autofill=' + id + '&posttype=' + posttype + '&insert_dummy_post_index=' + dummy_post_index + '&city_bound_lat1=' + bound_lat_lng[0] + '&city_bound_lng1=' + bound_lat_lng[1] + '&city_bound_lat2=' + bound_lat_lng[2] + '&city_bound_lng2=' + bound_lat_lng[3] + '&_wpnonce=' + nonce,
                            function (data) {

                                jQuery(obj).closest('form').find('.dummy_post_inserted').html('<?php _e('Dummy post(s) inserted:',GEODIRECTORY_TEXTDOMAIN);?> ' + dummy_post_index + ' <?php _e('of' ,GEODIRECTORY_TEXTDOMAIN); ?> ' + total_dummy_post_count + '');
                                dummy_post_index++;
                                if (dummy_post_index <= total_dummy_post_count)
                                    geodir_autoinstall(obj, id, nonce, posttype);
                                else {
                                    window.location.href = jQuery('#' + id).attr('redirect_to') + active_tab;
                                }

                            });
                    }

                }
            </script>
        <?php
        }
    }
}

/**
 * Inserts GeoDirectory dummy posts.
 *
 * @since 1.0.0
 * @package GeoDirectory
 * @global object $wpdb WordPress Database object.
 * @global object $current_user Current user object.
 */
function geodir_insert_dummy_posts()
{
    geodir_default_taxonomies();

    ini_set('max_execution_time', 999999); //300 seconds = 5 minutes

    global $wpdb, $current_user;

    /**
     * Contains dumy post content.
     *
     * @since 1.0.0
     * @package GeoDirectory
     */
    include_once('place_dummy_post.php');

}

/**
 * Deletes GeoDirectory dummy data.
 *
 * @since 1.0.0
 * @package GeoDirectory
 * @global object $wpdb WordPress Database object.
 * @global string $plugin_prefix Geodirectory plugin table prefix.
 */
function geodir_delete_dummy_posts()
{
    global $wpdb, $plugin_prefix;


    $post_ids = $wpdb->get_results("SELECT post_id FROM " . $plugin_prefix . "gd_place_detail WHERE post_dummy='1'");


    foreach ($post_ids as $post_ids_obj) {
        wp_delete_post($post_ids_obj->post_id);
    }

    //double check posts are deleted
    $wpdb->get_results("DELETE FROM " . $plugin_prefix . "gd_place_detail WHERE post_dummy='1'");
}

/**
 * Default taxonomies
 *
 * Adds the default terms for taxonomies - placecategory. Modify at your own risk.
 * 
 * @since 1.0.0
 * @package GeoDirectory
 * @global object $wpdb WordPress Database object.
 * @global string $dummy_image_path The dummy image path.
 */
function geodir_default_taxonomies()
{

    global $wpdb, $dummy_image_path;

    $category_array = array('Attractions', 'Hotels', 'Restaurants', 'Food Nightlife', 'Festival', 'Videos', 'Feature');

    $last_catid = isset($last_catid) ? $last_catid : '';

    $last_term = get_term($last_catid, 'gd_placecategory');

    $uploads = wp_upload_dir(); // Array of key => value pairs
    //print_r($uploads) ;
    for ($i = 0; $i < count($category_array); $i++) {
        $parent_catid = 0;
        if (is_array($category_array[$i])) {
            $cat_name_arr = $category_array[$i];
            for ($j = 0; $j < count($cat_name_arr); $j++) {
                $catname = $cat_name_arr[$j];

                if (!term_exists($catname, 'gd_placecategory')) {
                    $last_catid = wp_insert_term($catname, 'gd_placecategory', $args = array('parent' => $parent_catid));

                    if ($j == 0) {
                        $parent_catid = $last_catid;
                    }


                    if (geodir_dummy_folder_exists())
                        $dummy_image_url = geodir_plugin_url() . "/geodirectory-admin/dummy/cat_icon";
                    else
                        $dummy_image_url = 'http://www.wpgeodirectory.com/dummy/cat_icon';
                    $catname = str_replace(' ', '_', $catname);
                    $uploaded = (array)fetch_remote_file("$dummy_image_url/" . $catname . ".png");

                    if (empty($uploaded['error'])) {
                        $new_path = $uploaded['file'];
                        $new_url = $uploaded['url'];
                    }

                    $wp_filetype = wp_check_filetype(basename($new_path), null);

                    $attachment = array(
                        'guid' => $uploads['baseurl'] . '/' . basename($new_path),
                        'post_mime_type' => $wp_filetype['type'],
                        'post_title' => preg_replace('/\.[^.]+$/', '', basename($new_path)),
                        'post_content' => '',
                        'post_status' => 'inherit'
                    );
                    $attach_id = wp_insert_attachment($attachment, $new_path);

                    // you must first include the image.php file
                    // for the function wp_generate_attachment_metadata() to work
                    require_once(ABSPATH . 'wp-admin/includes/image.php');
                    $attach_data = wp_generate_attachment_metadata($attach_id, $new_path);
                    wp_update_attachment_metadata($attach_id, $attach_data);

                    if (!get_tax_meta($last_catid['term_id'], 'ct_cat_icon', false, 'gd_place')) {
                        update_tax_meta($last_catid['term_id'], 'ct_cat_icon', array('id' => 'icon', 'src' => $new_url), 'gd_place');
                    }
                }
            }

        } else {
            $catname = $category_array[$i];

            if (!term_exists($catname, 'gd_placecategory')) {
                $last_catid = wp_insert_term($catname, 'gd_placecategory');

                if (geodir_dummy_folder_exists())
                    $dummy_image_url = geodir_plugin_url() . "/geodirectory-admin/dummy/cat_icon";
                else
                    $dummy_image_url = 'http://www.wpgeodirectory.com/dummy/cat_icon';
                $catname = str_replace(' ', '_', $catname);
                $uploaded = (array)fetch_remote_file("$dummy_image_url/" . $catname . ".png");

                if (empty($uploaded['error'])) {
                    $new_path = $uploaded['file'];
                    $new_url = $uploaded['url'];
                }

                $wp_filetype = wp_check_filetype(basename($new_path), null);

                $attachment = array(
                    'guid' => $uploads['baseurl'] . '/' . basename($new_path),
                    'post_mime_type' => $wp_filetype['type'],
                    'post_title' => preg_replace('/\.[^.]+$/', '', basename($new_path)),
                    'post_content' => '',
                    'post_status' => 'inherit'
                );

                $attach_id = wp_insert_attachment($attachment, $new_path);


                // you must first include the image.php file
                // for the function wp_generate_attachment_metadata() to work
                require_once(ABSPATH . 'wp-admin/includes/image.php');
                $attach_data = wp_generate_attachment_metadata($attach_id, $new_path);
                wp_update_attachment_metadata($attach_id, $attach_data);

                if (!get_tax_meta($last_catid['term_id'], 'ct_cat_icon', false, 'gd_place')) {
                    update_tax_meta($last_catid['term_id'], 'ct_cat_icon', array('id' => $attach_id, 'src' => $new_url), 'gd_place');
                }
            }
        }

    }
}

/**
 * Update options
 *
 * Updates the options on the geodirectory settings pages. Returns true if saved.
 *
 * @since 1.0.0
 * @package GeoDirectory
 * @param array $options The option array.
 * @param bool $dummy Is this dummy settings? Default: false.
 * @return bool Returns true if saved.
 */
function geodir_update_options($options, $dummy = false)
{
    //print_r($_POST);exit;
    if ((!isset($_POST) || !$_POST) && !$dummy) return false;

    foreach ($options as $value) {
        if ($dummy && isset($value['std']))
            $_POST[$value['id']] = $value['std'];


        if (isset($value['type']) && $value['type'] == 'checkbox') :

            if (isset($value['id']) && isset($_POST[$value['id']])) {
                update_option($value['id'], $_POST[$value['id']]);
            } else {
                update_option($value['id'], 0);
            }

        elseif (isset($value['type']) && $value['type'] == 'image_width') :

            if (isset($value['id']) && isset($_POST[$value['id'] . '_width'])) {
                update_option($value['id'] . '_width', $_POST[$value['id'] . '_width']);
                update_option($value['id'] . '_height', $_POST[$value['id'] . '_height']);
                if (isset($_POST[$value['id'] . '_crop'])) :
                    update_option($value['id'] . '_crop', 1);
                else :
                    update_option($value['id'] . '_crop', 0);
                endif;
            } else {
                update_option($value['id'] . '_width', $value['std']);
                update_option($value['id'] . '_height', $value['std']);
                update_option($value['id'] . '_crop', 1);
            }

        elseif (isset($value['type']) && $value['type'] == 'map') :
            $post_types = array();
            $categories = array();
            $i = 0;

            if (!empty($_POST['home_map_post_types'])) :
                foreach ($_POST['home_map_post_types'] as $post_type) :
                    $post_types[] = $post_type;
                endforeach;
            endif;

            update_option('geodir_exclude_post_type_on_map', $post_types);

            if (!empty($_POST['post_category'])) :
                foreach ($_POST['post_category'] as $texonomy => $cat_arr) :
                    $categories[$texonomy] = array();
                    foreach ($cat_arr as $category) :
                        $categories[$texonomy][] = $category;
                    endforeach;
                    $categories[$texonomy] = !empty($categories[$texonomy]) ? array_unique($categories[$texonomy]) : array();
                endforeach;
            endif;
            update_option('geodir_exclude_cat_on_map', $categories);
            update_option('geodir_exclude_cat_on_map_upgrade', 1);
        elseif (isset($value['type']) && $value['type'] == 'map_default_settings') :


            if (!empty($_POST['geodir_default_map_language'])):
                update_option('geodir_default_map_language', $_POST['geodir_default_map_language']);
            endif;


            if (!empty($_POST['geodir_default_map_search_pt'])):
                update_option('geodir_default_map_search_pt', $_POST['geodir_default_map_search_pt']);
            endif;


        elseif (isset($value['type']) && $value['type'] == 'file') :


            if (isset($_POST[$value['id'] . '_remove']) && $_POST[$value['id'] . '_remove']) {// if remove is set then remove the file

                if (get_option($value['id'])) {
                    $image_name_arr = explode('/', get_option($value['id']));
                    $noimg_name = end($image_name_arr);
                    $img_path = $uploads['path'] . '/' . $noimg_name;
                    if (file_exists($img_path))
                        unlink($img_path);
                }

                update_option($value['id'], '');
            }

            $uploadedfile = isset($_FILES[$value['id']]) ? $_FILES[$value['id']] : '';
            $filename = isset($_FILES[$value['id']]['name']) ? $_FILES[$value['id']]['name'] : '';

            if (!empty($filename)):
                $ext = pathinfo($filename, PATHINFO_EXTENSION);
                $uplaods = array();

                foreach ($uploadedfile as $key => $uplaod):
                    if ($key == 'name'):
                        $uplaods[$key] = $filename;
                    else :
                        $uplaods[$key] = $uplaod;
                    endif;
                endforeach;

                $uploads = wp_upload_dir();

                if (get_option($value['id'])) {
                    $image_name_arr = explode('/', get_option($value['id']));
                    $noimg_name = end($image_name_arr);
                    $img_path = $uploads['path'] . '/' . $noimg_name;
                    if (file_exists($img_path))
                        unlink($img_path);
                }

                $upload_overrides = array('test_form' => false);
                $movefile = wp_handle_upload($uplaods, $upload_overrides);

                update_option($value['id'], $movefile['url']);

            endif;

            if (!get_option($value['id']) && isset($value['value'])):
                update_option($value['id'], $value['value']);
            endif;


        else :
            // same menu setting per theme.
            if (isset($value['id']) && $value['id'] == 'geodir_theme_location_nav' && isset($_POST[$value['id']])) {
                $theme = wp_get_theme();
                update_option('geodir_theme_location_nav_' . $theme->name, $_POST[$value['id']]);
            }

            if (isset($value['id']) && isset($_POST[$value['id']])) {
                update_option($value['id'], $_POST[$value['id']]);
            } else {
                delete_option($value['id']);
            }

        endif;
    }
    if ($dummy)
        unset($_POST);
    return true;

}

/**
 * create custom fields for place.
 *
 * @since 1.0.0
 * @package GeoDirectory
 * @param array $tabs {
 *    Attributes of the tabs array.
 *
 *    @type array $general_settings {
 *        Attributes of general settings.
 *
 *        @type string $label Default "General".
 *
 *    }
 *    @type array $design_settings {
 *        Attributes of design settings.
 *
 *        @type string $label Default "Design".
 *
 *    }
 *    @type array $permalink_settings {
 *        Attributes of permalink settings.
 *
 *        @type string $label Default "Permalinks".
 *
 *    }
 *    @type array $notifications_settings {
 *        Attributes of notifications settings.
 *
 *        @type string $label Default "Notifications".
 *
 *    }
 *    @type array $default_location_settings {
 *        Attributes of default location settings.
 *
 *        @type string $label Default "Set Default Location".
 *
 *    }
 *
 * }
 * @return array Modified tabs array.
 */
function places_custom_fields_tab($tabs)
{

    $geodir_post_types = get_option('geodir_post_types');

    if (!empty($geodir_post_types)) {

        foreach ($geodir_post_types as $geodir_post_type => $geodir_posttype_info):
            if ($geodir_post_type == 'gd_place' && get_option('geodir_disable_place_tax')) {
                continue;
            }
            $listing_slug = $geodir_posttype_info['labels']['singular_name'];

            $tabs[$geodir_post_type . '_fields_settings'] = array(
                'label' => __(ucfirst($listing_slug) . ' Settings', GEODIRECTORY_TEXTDOMAIN),
                'subtabs' => array(
                    array('subtab' => 'custom_fields',
                        'label' => __('Custom Fields', GEODIRECTORY_TEXTDOMAIN),
                        'request' => array('listing_type' => $geodir_post_type)),
                    array('subtab' => 'sorting_options',
                        'label' => __('Sorting Options', GEODIRECTORY_TEXTDOMAIN),
                        'request' => array('listing_type' => $geodir_post_type)),
                ),
                'tab_index' => 9,
                'request' => array('listing_type' => $geodir_post_type)
            );

        endforeach;

    }

    return $tabs;
}


/**
 * Adds GD Tools settings menu to GeoDirectory settings.
 *
 * Can be found here. WP Admin -> Geodirectory -> GD Tools.
 *
 * @since 1.0.0
 * @package GeoDirectory
 * @param array $tabs Tab menu array {@see places_custom_fields_tab()}.
 * @return array Modified tab meny array.
 */
function geodir_tools_setting_tab($tabs)
{
    $tabs['tools_settings'] = array('label' => __('GD Tools', GEODIRECTORY_TEXTDOMAIN));
    return $tabs;
}

/**
 * Adds Theme Compatibility menu item to GeoDirectory settings page.
 *
 * Can be found here. WP Admin -> Geodirectory -> Theme Compatibility.
 *
 * @since 1.0.0
 * @package GeoDirectory
 * @param array $tabs Tab menu array {@see places_custom_fields_tab()}.
 * @return array Modified tab meny array.
 */
function geodir_compatibility_setting_tab($tabs)
{
    $tabs['compatibility_settings'] = array('label' => __('Theme Compatibility', GEODIRECTORY_TEXTDOMAIN));
    return $tabs;
}


/**
 * Adds Extend Geodirectory menu item to GeoDirectory settings page.
 *
 * Can be found here. WP Admin -> Geodirectory -> Extend Geodirectory.
 *
 * @since 1.0.0
 * @package GeoDirectory
 * @param array $tabs Tab menu array {@see places_custom_fields_tab()}.
 * @return array Modified tab meny array.
 */
function geodir_extend_geodirectory_setting_tab($tabs)
{
    $tabs['extend_geodirectory_settings'] = array('label' => __('Extend Geodirectory', GEODIRECTORY_TEXTDOMAIN), 'url' => 'http://wpgeodirectory.com', 'target' => '_blank');
    return $tabs;
}


if (!function_exists('geodir_edit_post_columns')) {
    /**
     * Modify admin post listing page columns.
     *
     * @since 1.0.0
     * @package GeoDirectory
     * @param array $columns The column array.
     * @return array Altered column array.
     */
    function geodir_edit_post_columns($columns)
    {

        $new_columns = array('location' => __('Location (ID)', GEODIRECTORY_TEXTDOMAIN),
            'categorys' => __('Categories', GEODIRECTORY_TEXTDOMAIN));

        if (($offset = array_search('author', array_keys($columns))) === false) // if the key doesn't exist
        {
            $offset = 0; // should we prepend $array with $data?
            $offset = count($columns); // or should we append $array with $data? lets pick this one...
        }

        $columns = array_merge(array_slice($columns, 0, $offset), $new_columns, array_slice($columns, $offset));

        $columns = array_merge($columns, array('expire' => __('Expires', GEODIRECTORY_TEXTDOMAIN)));

        return $columns;
    }
}


if (!function_exists('geodir_manage_post_columns')) {
    /**
     * Adds content to our custom post listing page columns.
     *
     * @since 1.0.0
     * @package GeoDirectory
     * @global object $wpdb WordPress Database object.
     * @global object $post WordPress Post object.
     * @param string $column The column name.
     * @param int $post_id The post ID.
     */
    function geodir_manage_post_columns($column, $post_id)
    {
        global $post, $wpdb;

        switch ($column):
            /* If displaying the 'city' column. */
            case 'location' :
                $location_id = geodir_get_post_meta($post->ID, 'post_location_id', true);
                $location = geodir_get_location($location_id);
                /* If no city is found, output a default message. */
                if (empty($location)) {
                    _e('Unknown', GEODIRECTORY_TEXTDOMAIN);
                } else {
                    /* If there is a city id, append 'city name' to the text string. */
                    $add_location_id = $location_id > 0 ? ' (' . $location_id . ')' : '';
                    echo(__($location->country, GEODIRECTORY_TEXTDOMAIN) . '-' . $location->region . '-' . $location->city . $add_location_id);
                }
                break;

            /* If displaying the 'expire' column. */
            case 'expire' :
                $expire_date = geodir_get_post_meta($post->ID, 'expire_date', true);
                $d1 = $expire_date; // get expire_date
                $d2 = date('Y-m-d'); // get current date
                $state = __('days left', GEODIRECTORY_TEXTDOMAIN);
                $date_diff_text = '';
                $expire_class = 'expire_left';
                if ($expire_date != 'Never') {
                    if (strtotime($d1) < strtotime($d2)) {
                        $state = __('days overdue', GEODIRECTORY_TEXTDOMAIN);
                        $expire_class = 'expire_over';
                    }
                    $date_diff = round(abs(strtotime($d1) - strtotime($d2)) / 86400); // get the differance in days
                    $date_diff_text = '<br /><span class="' . $expire_class . '">(' . $date_diff . ' ' . $state . ')</span>';
                }
                /* If no expire_date is found, output a default message. */
                if (empty($expire_date))
                    echo __('Unknown', GEODIRECTORY_TEXTDOMAIN);
                /* If there is a expire_date, append 'days left' to the text string. */
                else
                    echo $expire_date . $date_diff_text;
                break;

            /* If displaying the 'categorys' column. */
            case 'categorys' :

                /* Get the categorys for the post. */


                $terms = wp_get_object_terms($post_id, get_object_taxonomies($post));

                /* If terms were found. */
                if (!empty($terms)) {
                    $out = array();
                    /* Loop through each term, linking to the 'edit posts' page for the specific term. */
                    foreach ($terms as $term) {
                        if (!strstr($term->taxonomy, 'tag')) {
                            $out[] = sprintf('<a href="%s">%s</a>',
                                esc_url(add_query_arg(array('post_type' => $post->post_type, $term->taxonomy => $term->slug), 'edit.php')),
                                esc_html(sanitize_term_field('name', $term->name, $term->term_id, $term->taxonomy, 'display'))
                            );
                        }
                    }
                    /* Join the terms, separating them with a comma. */
                    echo(join(', ', $out));
                } /* If no terms were found, output a default message. */
                else {
                    _e('No Categories', GEODIRECTORY_TEXTDOMAIN);
                }
                break;

        endswitch;
    }
}


if (!function_exists('geodir_post_sortable_columns')) {
    /**
     * Makes admin post listing page columns sortable.
     *
     * @since 1.0.0
     * @package GeoDirectory
     * @param array $columns The column array.
     * @return array Altered column array.
     */
    function geodir_post_sortable_columns($columns)
    {

        $columns['expire'] = 'expire';

        return $columns;
    }
}

/**
 * Saves listing data from request variable to database.
 *
 * @since 1.0.0
 * @package GeoDirectory
 * @global object $wpdb WordPress Database object.
 * @global object $current_user Current user object.
 * @global object $post WordPress Post object.
 * @param int $post_id The post ID.
 */
function geodir_post_information_save($post_id,$post)
{

    global $wpdb, $current_user;//, $post;

    if(isset($post->post_type) && ($post->post_type=='nav_menu_item' || $post->post_type=='page' || $post->post_type=='post')){
        return;
    }

   /* echo '###post';
    print_r($_POST);
    echo '###request';
    print_r($_REQUEST);
    echo '###';
    print_r($post);
    exit;
   */
    //unsetting the listing session here causes problems, it should be the last thing to be done.
    /*if(isset($_SESSION['listing'])){
		unset($_SESSION['listing']);
	}*/

    $geodir_posttypes = geodir_get_posttypes();

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
        return;

    if (!wp_is_post_revision($post_id) &&
        isset($post->post_type) && in_array($post->post_type, $geodir_posttypes)
    ):

        if (isset($_REQUEST['_status']))
            geodir_change_post_status($post_id, $_REQUEST['_status']);

        /*if ( !wp_verify_nonce( $_POST['geodir_post_setting_noncename'], plugin_basename( __FILE__ ) ) )
        return;*/

        if (isset($_REQUEST['action']) && ($_REQUEST['action'] == 'trash' || $_REQUEST['action'] == 'untrash'))
            return;

        if (!isset($_POST['geodir_post_info_noncename']) || !wp_verify_nonce($_POST['geodir_post_info_noncename'], plugin_basename(__FILE__)))
            return;

        /*if ( !wp_verify_nonce( $_POST['geodir_post_addinfo_noncename'], plugin_basename( __FILE__ ) ) )
        return;*/

        if (!isset($_POST['geodir_post_attachments_noncename']) || !wp_verify_nonce($_POST['geodir_post_attachments_noncename'], plugin_basename(__FILE__)))
            return;


        geodir_save_listing($_REQUEST);


    endif;
}

if (!function_exists('geodir_insert_csv_post_data') && get_option('geodir_installed')) {
    /**
     * Function to insert csv post data.
     *
     * @since 1.0.0
     * @package GeoDirectory
     * @global object $wpdb WordPress Database object.
     * @global string $plugin_prefix Geodirectory plugin table prefix.
     */
    function geodir_insert_csv_post_data()
    {
        global $wpdb, $plugin_prefix;

        $svalue = '';
        ?>
        <script type="text/javascript">
            jQuery(document).ready(function () {
                jQuery("#import_data").click(function () {
                    var geodir_import_data = jQuery('.geodir_import_data').val();
                    var csv_filename = jQuery('.csv_filename').val();
                    if (csv_filename != '') {
                        jQuery.post('<?php echo geodir_get_ajax_url(); ?>&geodir_import_data=' + geodir_import_data + '&filename=' + csv_filename, function (data) {
                            //alert(data);
                            window.location.href = data;
                        });
                    } else {
                        alert('<?php echo PLZ_SELECT_CSV_FILE; ?>');
                    }
                });

                jQuery(".uploadcsv_button").click(function () {
                    setInterval(function () {
                        var checkvalue = jQuery('.csv_filename').val();
                        if (checkvalue != '') {
                            jQuery('.csv_button_div').show();
                        }
                    }, 1000);
                });


            });
            jQuery(document).ready(function () {
                jQuery(".gd_uploadcsv_button").click(function () {
                    setInterval(function () {
                        var checkvalue = jQuery('.gd_csv_filename').val();
                        if (checkvalue != '') {
                            jQuery('.gd_csv_button_div').show();
                        }
                    }, 1000);
                });
            });
        </script>
        <?php

        if (isset($_REQUEST['active_tab']) && $_REQUEST['active_tab'] == 'csv_upload_settings') {

            if (isset($_REQUEST['msg']) && $_REQUEST['msg'] == 'success') {
                $rowcount = $_REQUEST['rowcount'];
                $uploads = wp_upload_dir(); ?>

                <div class="updated fade below-h2" id="message"
                     style="background-color: rgb(255, 251, 204); margin-left:0px; margin-top:0px; margin-bottom:10px;">

                    <?php

                    if ($_REQUEST['invalidcount'] == 0 && $_REQUEST['blank_address'] == 0 && $_REQUEST['invalid_post_type'] == 0 && $_REQUEST['invalid_title'] == 0) {

                        echo '<p>' . CSV_INSERT_DATA . '</p>';

                    }

                    echo '<p>';
                    printf(CSV_TOTAL_RECORD, $rowcount);
                    echo '</p>';

                    if (isset($_REQUEST['invalidcount']) && $_REQUEST['invalidcount'] > 0) {

                        echo '<p>';
                        printf(CSV_INVALID_DEFUALT_ADDRESS, $_REQUEST['invalidcount'], $_REQUEST['total_records']);
                        echo '</p>';
                    }

                    if (isset($_REQUEST['blank_address']) && $_REQUEST['blank_address'] > 0) {

                        echo '<p>';
                        printf(CSV_INVALID_TOTAL_RECORD, $_REQUEST['blank_address'], $_REQUEST['total_records']);
                        echo '</p>';

                    }

                    if (isset($_REQUEST['invalid_post_type']) && $_REQUEST['invalid_post_type'] > 0) {

                        echo '<p>';
                        printf(CSV_INVALID_POST_TYPE, $_REQUEST['invalid_post_type'], $_REQUEST['total_records']);
                        echo '</p>';

                    }

                    if (isset($_REQUEST['invalid_title']) && $_REQUEST['invalid_title'] > 0) {

                        echo '<p>';
                        printf(CSV_BLANK_POST_TITLE, $_REQUEST['invalid_title'], $_REQUEST['total_records']);
                        echo '</p>';

                    }

                    if (isset($_REQUEST['upload_files']) && $_REQUEST['upload_files'] > 0) {

                        echo '<p>';
                        printf(CSV_TRANSFER_IMG_FOLDER, $uploads['subdir']);
                        echo '</p>';

                    }


                    ?>

                </div>

            <?php }?>

            <?php if (isset($_REQUEST['emsg']) && $_REQUEST['emsg'] == 'wrong') { ?>

                <div class="updated fade below-h2" id="message"
                     style="background-color: rgb(255, 251, 204); margin-left:0px; margin-top:0px; margin-bottom:10px;  color:#FF0000;">
                    <p><?php echo CSV_INVAILD_FILE; ?></p>

                </div>

            <?php }?>

            <?php if (isset($_REQUEST['emsg']) == 'csvonly') { ?>

                <div class="updated fade below-h2" id="message"
                     style="background-color: rgb(255, 251, 204); margin-left:0px; margin-top:0px; margin-bottom:10px; color:#FF0000;">
                    <p><?php echo CSV_UPLOAD_ONLY; ?></p>

                </div>

            <?php }
        } ?>
        <?php /* ?>
    <table class="form-table">
        <tbody>
            <tr valign="top" class="single_select_page">
                <th class="titledesc" scope="row"><?php echo SELECT_CSV_FILE;?></th>
                <td class="forminp">

					<?php
                    $id = "import_image";
                    $multiple = false; // allow multiple files upload
                    ?>
                 	<div class="gtd-formfeild">
                        <div class="gtd-form_row clearfix" id="<?php echo $id; ?>dropbox">

                        <div class="plupload-upload-uic hide-if-no-js" id="<?php echo $id; ?>plupload-upload-ui">
                        <input type="text" readonly="readonly" name="<?php echo $id; ?>" class="csv_filename" id="<?php echo $id; ?>" value="<?php echo $svalue; ?>" /><input id="<?php echo $id; ?>plupload-browse-button" type="button" value="<?php echo SELECT_UPLOAD_CSV; ?>" class="uploadcsv_button" /><br />
                       <a href="<?php echo geodir_plugin_url() . '/geodirectory-assets/place_listing.csv'?>" ><?php _e("Download sample csv", GEODIRECTORY_TEXTDOMAIN)?></a>
                       <?php do_action('geodir_sample_csv_download_link'); ?>

                        <span class="ajaxnonceplu" id="ajaxnonceplu<?php echo wp_create_nonce($id . 'pluploadan'); ?>"></span><br /><br />
    						<div class="filelist"></div>

                        </div>
                   </div>
                	</div>
                    <span id="upload-error" style="display:none"></span>
                	<span class="description"></span><br />
                    <div class="csv_button_div" style="display:none;">
                    <input type="hidden" class="geodir_import_data" name="geodir_import_data" value="save" />					        			<input type="button" value="<?php echo CSV_IMPORT_DATA; ?>" id="import_data" class="button-primary" name="save">				</div>

                </td>
            </tr>

        </tbody>
    </table>
	<?php */ ?>
        <?php
        $id = "gd_import_csv";
        $multiple = false; // allow multiple files upload
        $uploads = wp_upload_dir();
        ?>
        <style>
            .gd-csv-form-table .filelist div.file:last-child {
                display: block !important;
                opacity: 1 !important
            }

            .gd-import-progress {
                margin-top: 10px
            }

            .gd-import-file {
                background: #ececec;
                border: 1px solid #ccc;
                clear: both;
                margin-bottom: 4px;
                padding: 5px;
            }

            .gd-fileprogress {
                background: #b7c53d;
                height: 5px;
                width: 0;
                margin-top: 3px
            }

            .gd-csv-form-table .fileprogress {
                margin-top: 3px
            }

            #gd-import-msg {
                padding-top: 12px
            }

            #gd-import-msg #message {
                background-color: rgb(255, 251, 204);
            }
        </style>
        <script type="text/javascript">
            var timout;
            function gdPrepareImport() {
                jQuery('#gd-import-msg').hide();
                var gd_prepared = jQuery('#gd_prepared').val();
                var uploadedFile = jQuery('#gd_import_csv').val();
                if (gd_prepared == uploadedFile) {
                    gdContinueImport();
                    jQuery('#gd_import_data').attr('disabled', 'disabled');
                } else {
                    var gddata = new Array();
                    gddata = {
                        'uploadedFile': uploadedFile,
                    }
                    jQuery.ajax({
                        type: 'POST',
                        url: ajaxurl,
                        dataType: 'json',
                        data: {
                            'action': 'gdImportCsv',
                            'gddata': gddata,
                            'task': 'prepare'
                        },
                        success: function (data) {
                            if (typeof data == 'object') {
                                if (data.error) {
                                    jQuery('#gd-import-msg').find('#message').removeClass('updated').addClass('error').html('<p>' + data.error + '</p>');
                                    jQuery('#gd-import-msg').show();
                                } else {
                                    jQuery('#gd_tmpcount').val('0');
                                    jQuery('#gd_tot_records').val(data.rows);
                                    jQuery('#gd_checktotal').val(data.rows);
                                    jQuery('#gd_prepared').val(uploadedFile);

                                    jQuery('#gd_rowcount').val('0');
                                    jQuery('#gd_invalidcount').val('0');
                                    jQuery('#gd_blank_address').val('0');
                                    jQuery('#gd_upload_files').val('0');
                                    jQuery('#gd_invalid_post_type').val('0');
                                    jQuery('#gd_invalid_title').val('0');
                                    jQuery('#gd_total_records').val('0');

                                    gdStartImport();
                                }
                            }
                        },
                        error: function (errorThrown) {
                            console.log(errorThrown);
                        }
                    });
                }
            }
            function gdTerminateImport() {
                console.log('gdTerminateImport()');
                jQuery('#gd_terminateaction').val('terminate');
                jQuery('#gd_import_data').hide();
                jQuery('#gd_stop_import').hide();
                jQuery('#gd_process_data').hide();
                jQuery('#gd_continue_data').show();
            }
            function gdContinueImport() {
                var tmpCnt = jQuery('#gd_tmpcount').val();
                var tot_no_of_records = jQuery('#gd_tot_records').val();
                if (parseInt(tmpCnt) > parseInt(tot_no_of_records)) {
                    jQuery('#gd_stop_import').hide();
                } else {
                    jQuery('#gd_stop_import').show();
                }
                jQuery('#gd_import_data').show();
                jQuery('#gd_import_data').attr('disabled', 'disabled');
                jQuery('#gd_process_data').css({
                    'display': 'inline-block'
                });
                jQuery('#gd_continue_data').hide();
                jQuery('#gd_terminateaction').val('continue');
                clearTimeout(timout);
                timout = setTimeout(function () {
                    gdStartImport();
                }, 0);
            }
            function gdStartImport(siteurl) {
                var importlimit = 1;
                var get_requested_count = importlimit;
                var tot_no_of_records = jQuery('#gd_checktotal').val();
                var uploadedFile = jQuery('#gd_import_csv').val();
                var tmpCnt = jQuery('#gd_tmpcount').val();
                var no_of_tot_records = jQuery('#gd_tot_records').val();

                if (!uploadedFile) {
                    jQuery('#gd_import_data').removeAttr('disabled');
                    jQuery('#gd_import_data').show();
                    jQuery('#gd_stop_import').hide();
                    jQuery('#gd_process_data').hide();
                    jQuery('#gd-import-progress').hide();
                    jQuery('.gd-fileprogress').width(0);
                    jQuery('#gd-import-done').text('0');
                    jQuery('#gd-import-total').text('0');
                    jQuery('#gd-import-perc').text('0%');

                    jQuery('.gd-csv-form-table .filelist .file').remove();
                    jQuery('#gd-import-msg').find('#message').removeClass('updated').addClass('error').html("<p><?php echo esc_attr( PLZ_SELECT_CSV_FILE );?></p>");
                    jQuery('#gd-import-msg').show();

                    return false;
                }

                jQuery('#gd-import-total').text(tot_no_of_records);
                jQuery('#gd_stop_import').show();
                jQuery('#gd_process_data').css({
                    'display': 'inline-block'
                });
                jQuery('#gd-import-progress').show();
                if ((parseInt(no_of_tot_records) / 100) > 0) {
                    importlimit = parseInt(parseInt(no_of_tot_records) / 100);
                }
                if (importlimit == 1) {
                    if (parseInt(no_of_tot_records) > 50) {
                        importlimit = 5;
                    } else if (parseInt(no_of_tot_records) > 10 && parseInt(no_of_tot_records) < 51) {
                        importlimit = 2;
                    }
                }
                if (importlimit > 10) {
                    importlimit = 10;
                }
                if (importlimit < 1) {
                    importlimit = 1;
                }

                get_requested_count = importlimit;

                var tempCount = parseInt(tmpCnt);
                var totalCount = parseInt(tot_no_of_records);
                if (tempCount >= totalCount) {
                    jQuery('#gd_import_data').removeAttr('disabled');
                    jQuery('#gd_import_data').show();
                    jQuery('#gd_stop_import').hide();
                    jQuery('#gd_process_data').hide();
                    gd_showStatusMsg();
                    jQuery('#gd_import_csv').val('');
                    jQuery('#gd_prepared').val('');

                    return false;
                }
                jQuery('#gd-import-msg').hide();
                var gddata = new Array();
                gddata = {
                    'importlimit': importlimit,
                    'totRecords': tot_no_of_records,
                    'uploadedFile': uploadedFile,
                    'tmpcount': tmpCnt,
                }
                var tmpLoc = jQuery('#tmpLoc').val();

                var gd_rowcount = parseInt(jQuery('#gd_rowcount').val());
                var gd_invalidcount = parseInt(jQuery('#gd_invalidcount').val());
                var gd_blank_address = parseInt(jQuery('#gd_blank_address').val());
                var gd_upload_files = parseInt(jQuery('#gd_upload_files').val());
                var gd_invalid_post_type = parseInt(jQuery('#gd_invalid_post_type').val());
                var gd_invalid_title = parseInt(jQuery('#gd_invalid_title').val());
                var gd_total_records = parseInt(jQuery('#gd_total_records').val());

                jQuery.ajax({
                    type: 'POST',
                    url: ajaxurl,
                    dataType: 'json',
                    data: {
                        'action': 'gdImportCsv',
                        'gddata': gddata,
                        'siteurl': siteurl,
                    },
                    success: function (data) {
                        if (typeof data == 'object') {
                            if (data.error) {
                                jQuery('#gd_import_data').removeAttr('disabled');
                                jQuery('#gd_import_data').show();
                                jQuery('#gd_stop_import').hide();
                                jQuery('#gd_process_data').hide();
                                jQuery('#gd-import-msg').find('#message').removeClass('updated').addClass('error').html('<p>' + data.error + '</p>');
                                jQuery('#gd-import-msg').show();
                            } else {
                                gd_rowcount = gd_rowcount + parseInt(data.rowcount);
                                gd_invalidcount = gd_invalidcount + parseInt(data.invalidcount);
                                gd_blank_address = gd_blank_address + parseInt(data.blank_address);
                                gd_upload_files = gd_upload_files + parseInt(data.upload_files);
                                gd_invalid_post_type = gd_invalid_post_type + parseInt(data.invalid_post_type);
                                gd_invalid_title = gd_invalid_title + parseInt(data.invalid_title);
                                gd_total_records = gd_total_records + parseInt(data.total_records);

                                jQuery('#gd_rowcount').val(gd_rowcount);
                                jQuery('#gd_invalidcount').val(gd_invalidcount);
                                jQuery('#gd_blank_address').val(gd_blank_address);
                                jQuery('#gd_upload_files').val(gd_upload_files);
                                jQuery('#gd_invalid_post_type').val(gd_invalid_post_type);
                                jQuery('#gd_invalid_title').val(gd_invalid_title);
                                jQuery('#gd_total_records').val(gd_total_records);

                                if (parseInt(tmpCnt) == parseInt(tot_no_of_records)) {
                                    jQuery('#gd-import-done').text(tot_no_of_records);
                                    jQuery('#gd-import-perc').text('100%');
                                    jQuery('.gd-fileprogress').css({
                                        'width': '100%'
                                    });
                                    jQuery('#gd_import_csv').val('');
                                    jQuery('#gd_prepared').val('');
                                    gd_showStatusMsg();
                                    jQuery('#gd_stop_import').hide();
                                }
                                if (parseInt(tmpCnt) < parseInt(tot_no_of_records)) {
                                    var terminate_action = jQuery('#gd_terminateaction').val();
                                    if (terminate_action == 'continue') {
                                        //console.log('Limit: ' + importlimit + ', Total: ' + tot_no_of_records);
                                        var nTmpCnt = parseInt(tmpCnt) + parseInt(importlimit);
                                        nTmpCnt = nTmpCnt > tot_no_of_records ? tot_no_of_records : nTmpCnt;

                                        jQuery('#gd_tmpcount').val(nTmpCnt);

                                        jQuery('#gd-import-done').text(nTmpCnt);
                                        if (parseInt(tot_no_of_records) > 0) {
                                            var percentage = ((parseInt(nTmpCnt) / parseInt(tot_no_of_records)) * 100);
                                            percentage = percentage > 100 ? 100 : percentage;
                                            jQuery('#gd-import-perc').text(parseInt(percentage) + '%');
                                            jQuery('.gd-fileprogress').css({
                                                'width': percentage + '%'
                                            });
                                        }

                                        clearTimeout(timout);
                                        timout = setTimeout(function () {
                                            gdStartImport();
                                        }, 0);
                                    } else {
                                        jQuery('#gd_import_data').hide();
                                        jQuery('#gd_stop_import').hide();
                                        jQuery('#gd_process_data').hide();
                                        jQuery('#gd_continue_data').show();
                                        return false;
                                    }
                                } else {
                                    jQuery('#gd_import_data').removeAttr('disabled');
                                    jQuery('#gd_import_data').show();
                                    jQuery('#gd_stop_import').hide();
                                    jQuery('#gd_process_data').hide();
                                    return false;
                                }
                            }
                        }
                    },
                    error: function (errorThrown) {
                        jQuery('#gd_import_data').removeAttr('disabled');
                        jQuery('#gd_import_data').show();
                        jQuery('#gd_stop_import').hide();
                        jQuery('#gd_process_data').hide();
                        console.log(errorThrown);
                    }
                });
            }
            function gd_showStatusMsg() {
                var gd_rowcount = parseInt(jQuery('#gd_rowcount').val());
                var gd_invalidcount = parseInt(jQuery('#gd_invalidcount').val());
                var gd_blank_address = parseInt(jQuery('#gd_blank_address').val());
                var gd_upload_files = parseInt(jQuery('#gd_upload_files').val());
                var gd_invalid_post_type = parseInt(jQuery('#gd_invalid_post_type').val());
                var gd_invalid_title = parseInt(jQuery('#gd_invalid_title').val());
                var gd_total_records = parseInt(jQuery('#gd_total_records').val());

                var gdMsg = '<p></p>';
                if (gd_invalidcount == 0 && gd_blank_address == 0 && gd_invalid_post_type == 0 && gd_invalid_title == 0) {
                    gdMsg += "<p><?php echo esc_attr( CSV_INSERT_DATA );?></p>";
                }

                var msgParse = '<p><?php echo addslashes( CSV_TOTAL_RECORD );?></p>';
                gdMsg += msgParse.replace("%s", gd_rowcount);

                if (gd_invalidcount > 0) {
                    msgParse = msgParse = '<p><?php echo sprintf( CSV_INVALID_DEFUALT_ADDRESS, '%s', '%d' );?></p>';
                    msgParse = msgParse.replace("%s", gd_invalidcount);
                    msgParse = msgParse.replace("%d", gd_total_records);
                    gdMsg += msgParse;
                }
                if (gd_blank_address > 0) {
                    msgParse = '<p><?php echo addslashes( sprintf( CSV_INVALID_TOTAL_RECORD, '%s', '%d' ) );?></p>';
                    msgParse = msgParse.replace("%s", gd_blank_address);
                    msgParse = msgParse.replace("%d", gd_total_records);
                    gdMsg += msgParse;
                }
                if (gd_invalid_post_type > 0) {
                    msgParse = '<p><?php echo addslashes( sprintf( CSV_INVALID_POST_TYPE, '%s', '%d' ) );?></p>';
                    msgParse = msgParse.replace("%s", gd_invalid_post_type);
                    msgParse = msgParse.replace("%d", gd_total_records);
                    gdMsg += msgParse;
                }
                if (gd_invalid_title > 0) {
                    msgParse = '<p><?php echo addslashes( sprintf( CSV_BLANK_POST_TITLE, '%s', '%d' ) );?></p>';
                    msgParse = msgParse.replace("%s", gd_invalid_title);
                    msgParse = msgParse.replace("%d", gd_total_records);
                    gdMsg += msgParse;
                }
                if (gd_upload_files > 0) {
                    gdMsg += '<p><?php echo addslashes( sprintf( CSV_TRANSFER_IMG_FOLDER, $uploads['subdir'] ) );?></p>';
                }
                gdMsg += '<p></p>';
                jQuery('#gd-import-msg').find('#message').removeClass('error').addClass('updated').html(gdMsg);
                jQuery('#gd-import-msg').show();
            }
        </script>
        <table class="form-table gd-csv-form-table">
            <tbody>
            <tr valign="top" class="single_select_page">
                <th class="titledesc" scope="row"><?php echo SELECT_CSV_FILE;?></th>
                <td class="forminp">
                    <div class="gtd-formfeild">
                        <div class="gtd-form_row clearfix" id="<?php echo $id; ?>dropbox">
                            <div class="plupload-upload-uic hide-if-no-js" id="<?php echo $id; ?>plupload-upload-ui">
                                <input type="text" readonly="readonly" name="<?php echo $id; ?>" class="gd_csv_filename"
                                       id="<?php echo $id; ?>" value="<?php echo $svalue; ?>"/>
                                <input id="<?php echo $id; ?>plupload-browse-button" type="button"
                                       value="<?php echo SELECT_UPLOAD_CSV; ?>" class="gd_uploadcsv_button"/>
                                <br/>
                                <a href="<?php echo geodir_plugin_url() . '/geodirectory-assets/place_listing.csv'?>"><?php _e("Download sample csv", GEODIRECTORY_TEXTDOMAIN)?></a>
                                <?php
                                /**
                                 * Called just after the sample CSV download link.
                                 *
                                 * @since 1.0.0
                                 */
                                do_action('geodir_sample_csv_download_link'); ?>
                                <span class="ajaxnonceplu"
                                      id="ajaxnonceplu<?php echo wp_create_nonce($id . 'pluploadan'); ?>"></span><br/>
                                <br/>

                                <div class="filelist"></div>
                            </div>
                        </div>
                    </div>
                    <span id="<?php echo $id; ?>upload-error" style="display:none"></span> <span
                        class="description"></span><br/>

                    <div class="gd_csv_button_div" style="display:none;">
                        <input type="hidden" class="geodir_import_file" name="geodir_import_file" value="save"/>
                        <input onclick="gdPrepareImport()" type="button" value="<?php echo CSV_IMPORT_DATA; ?>"
                               id="gd_import_data" class="button-primary" name="save">
                        <input onclick="gdContinueImport()" type="button" value="Continue Import Data"
                               id="gd_continue_data" class="button-primary" style="display:none"/>
                        <input type="button" value="<?php _e("Terminate Import Data", GEODIRECTORY_TEXTDOMAIN);?>"
                               id="gd_stop_import" class="button-primary" name="gd_stop_import" style="display:none"
                               onclick="gdTerminateImport()"/>

                        <div id="gd_process_data" style="display:none"><span class="spinner"
                                                                             style="display:inline-block;margin:0 5px 0 5px;float:left"></span><?php _e("Wait, processing import data...", GEODIRECTORY_TEXTDOMAIN);?>
                        </div>
                    </div>
                    <div id="gd_importer" style="display:none">
                        <input type="hidden" id="gd_tmpcount" value="0"/>
                        <input type="hidden" id="gd_tot_records" value="100"/>
                        <input type="hidden" id="gd_checktotal" value="100"/>
                        <input type="hidden" id="gd_terminateaction" value="continue"/>
                        <input type="hidden" id="gd_prepared" value="continue"/>
                        <input type="hidden" id="gd_rowcount" value="0"/>
                        <input type="hidden" id="gd_invalidcount" value="0"/>
                        <input type="hidden" id="gd_blank_address" value="0"/>
                        <input type="hidden" id="gd_upload_files" value="0"/>
                        <input type="hidden" id="gd_invalid_post_type" value="0"/>
                        <input type="hidden" id="gd_invalid_title" value="0"/>
                        <input type="hidden" id="gd_total_records" value="0"/>
                    </div>
                    <div class="gd-import-progress" id="gd-import-progress" style="display:none">
                        <div class="gd-import-file"><b><?php _e("Import Data Status :", GEODIRECTORY_TEXTDOMAIN);?> </b><font
                                id="gd-import-done">0</font> / <font id="gd-import-total">0</font>&nbsp;( <font
                                id="gd-import-perc">0%</font> )
                            <div class="gd-fileprogress"></div>
                        </div>
                    </div>
                    <div class="gd-import-msg" id="gd-import-msg" style="display:none">
                        <div id="message" class="message fade"></div>
                    </div>
                </td>
            </tr>
            </tbody>
        </table>

    <?php }
}

if (!function_exists('geodir_import_data')) {
    /**
     * Imports data from csv file.
     *
     * @since 1.0.0
     * @package GeoDirectory
     * @global object $wpdb WordPress Database object.
     * @global string $plugin_prefix Geodirectory plugin table prefix.
     * @global object $current_user Current user object.
     */
    function geodir_import_data()
    {

        global $wpdb, $plugin_prefix, $current_user;

        if (isset($_REQUEST['geodir_import_data']) && !empty($_REQUEST['geodir_import_data'])) {
            $uploads = wp_upload_dir();
            $uploads_dir = $uploads['path'];

            $curr_img_url = $_REQUEST['filename'];

            $image_name_arr = explode('/', $curr_img_url);

            $filename = end($image_name_arr);

            $target_path = $uploads_dir . '/temp_' . $current_user->data->ID . '/' . $filename;

            $destination_path = $uploads_dir . '/temp_' . $current_user->data->ID;

            $csv_target_path = $target_path;

            ini_set('auto_detect_line_endings', true);

            $fd = fopen($target_path, "rt");

            $total_records = 0;
            $rowcount = 0;
            $address_invalid = 0;
            $blank_address = 0;
            $upload_files = 0;
            $invalid_post_type = 0;
            $invalid_title = 0;

            $customKeyarray = array();

            $gd_post_info = array();

            $post_location = array();
            $countpost = 0;

            $uploaded_file_type = pathinfo($filename, PATHINFO_EXTENSION);

            $extensionarr = array('csv', 'CSV');

            if (in_array($uploaded_file_type, $extensionarr)) {

                while (!feof($fd)) {
                    $buffer = fgetcsv($fd, 40960);

                    if ($rowcount == 0) {
                        for ($k = 0; $k < count($buffer); $k++) {
                            $customKeyarray[$k] = $buffer[$k];
                        }

                        if ($customKeyarray[0] == '') {
                            echo $geodir_url = admin_url() . 'admin.php?page=geodirectory&tab=general_settings&active_tab=csv_upload_settings&emsg=wrong';
                            exit;
                        }
                    } elseif (!empty($buffer)) {

                        $total_records++;

                        $post_title = addslashes($buffer[0]);
                        $current_post_author = $buffer[1];
                        $post_desc = addslashes($buffer[2]);
                        $post_cat = array();
                        $catids_arr = array();
                        $post_cat = trim($buffer[3]); // comma seperated category name

                        if ($post_cat) {
                            $post_cat_arr = explode(',', $post_cat);

                            for ($c = 0; $c < count($post_cat_arr); $c++) {
                                $catid = wp_kses_normalize_entities(trim($post_cat_arr[$c]));
                                if (!empty($buffer[5])) {
                                    if (in_array($buffer[5], geodir_get_posttypes())) {
                                        $p_taxonomy = geodir_get_taxonomies(addslashes($buffer[5]));
                                        if (get_term_by('name', $catid, $p_taxonomy[0])) {
                                            $cat = get_term_by('name', $catid, $p_taxonomy[0]);
                                            $catids_arr[] = $cat->slug;
                                        } elseif (get_term_by('slug', $catid, $p_taxonomy[0])) {
                                            $cat = get_term_by('slug', $catid, $p_taxonomy[0]);
                                            $catids_arr[] = $cat->slug;
                                        }
                                    }
                                }
                            }
                        }

                        if (!$catids_arr) {
                            $catids_arr[] = 1;
                        }

                        $post_tags = trim($buffer[4]); // comma seperated tags

                        $tag_arr = '';
                        if ($post_tags) {

                            $tag_arr = explode(',', $post_tags);
                        }

                        $table = $plugin_prefix . $buffer[5] . '_detail'; // check table in database

                        $error = '';
                        if ($wpdb->get_var("SHOW TABLES LIKE '" . $table . "'") != $table) {

                            $invalid_post_type++;
                            continue;

                        }

                        if ($post_title != '') {

                            $menu_order = 0;

                            $image_folder_name = 'uplaod/';

                            $image_names = array();

                            for ($c = 5; $c < count($customKeyarray); $c++) {
                                $gd_post_info[$customKeyarray[$c]] = addslashes($buffer[$c]);

                                if ($customKeyarray[$c] == 'IMAGE') {
                                    $buffer[$c] = trim($buffer[$c]);
                                    if (!empty($buffer[$c])) {
                                        $image_names[] = $buffer[$c];
                                    }

                                }

                                if ($customKeyarray[$c] == 'alive_days') {
                                    if ($buffer[$c] != '0' && $buffer[$c] != '') {
                                        $submitdata = date('Y-m-d');
                                        $gd_post_info['expire_date'] = date('Y-m-d', strtotime($submitdata . "+" . addslashes($buffer[$c]) . " days"));
                                    } else {
                                        $gd_post_info['expire_date'] = 'Never';
                                    }
                                }


                                if ($customKeyarray[$c] == 'post_city') {
                                    $post_city = addslashes($buffer[$c]);
                                }

                                if ($customKeyarray[$c] == 'post_region') {
                                    $post_region = addslashes($buffer[$c]);
                                }

                                if ($customKeyarray[$c] == 'post_country') {
                                    $post_country = addslashes($buffer[$c]);
                                }

                                if ($customKeyarray[$c] == 'post_latitude') {
                                    $post_latitude = addslashes($buffer[$c]);
                                }

                                if ($customKeyarray[$c] == 'post_longitude') {
                                    $post_longitude = addslashes($buffer[$c]);
                                }

                            }

                            /* ================ before array create ============== */

                            $location_result = geodir_get_default_location();

                            if ((!isset($gd_post_info['post_city']) || $gd_post_info['post_city'] == '') || (!isset($gd_post_info['post_region']) || $gd_post_info['post_region'] == '') || (!isset($gd_post_info['post_country']) || $gd_post_info['post_country'] == '') || (!isset($gd_post_info['post_address']) || $gd_post_info['post_address'] == '') || (!isset($gd_post_info['post_latitude']) || $gd_post_info['post_latitude'] == '') || (!isset($gd_post_info['post_longitude']) || $gd_post_info['post_longitude'] == '')) {

                                $blank_address++;
                                continue;

                            } elseif ($location_result->location_id == 0) {

                                if ((strtolower($gd_post_info['post_city']) != strtolower($location_result->city)) ||
                                    (strtolower($gd_post_info['post_region']) != strtolower($location_result->region)) ||
                                    (strtolower($gd_post_info['post_country']) != strtolower($location_result->country))
                                ) {

                                    $address_invalid++;
                                    continue;

                                }

                            }


                            $my_post['post_title'] = $post_title;
                            $my_post['post_content'] = $post_desc;
                            $my_post['post_type'] = addslashes($buffer[5]);
                            $my_post['post_author'] = $current_post_author;
                            $my_post['post_status'] = 'publish';
                            $my_post['post_category'] = $catids_arr;
                            $my_post['post_tags'] = $tag_arr;


                            $gd_post_info['post_tags'] = $tag_arr;
                            $gd_post_info['post_title'] = $post_title;
                            $gd_post_info['post_status'] = 'publish';
                            $gd_post_info['submit_time'] = time();
                            $gd_post_info['submit_ip'] = $_SERVER['REMOTE_ADDR'];


                            $last_postid = wp_insert_post($my_post);
                            $countpost++;


                            // Check if we need to save post location as new location

                            if ($location_result->location_id > 0) {
                                if (isset($post_city) && isset($post_region)) {

                                    $request_info['post_location'] = array('city' => $post_city,
                                        'region' => $post_region,
                                        'country' => $post_country,
                                        'geo_lat' => $post_latitude,
                                        'geo_lng' => $post_longitude);

                                    $post_location_info = $request_info['post_location'];
                                    if ($location_id = geodir_add_new_location($post_location_info))
                                        $post_location_id = $location_id;

                                } else {
                                    $post_location_id = 0;
                                }
                            } else {
                                $post_location_id = 0;
                            }

                            /* ------- get default package info ----- */
                            $payment_info = array();
                            $package_info = array();

                            $package_info = (array)geodir_post_package_info($package_info, '', $buffer[5]);
                            $package_id = '';
                            if (isset($gd_post_info['package_id']) && $gd_post_info['package_id'] != '') {
                                $package_id = $gd_post_info['package_id'];
                            }

                            if (!empty($package_info)) {
                                $payment_info['package_id'] = $package_info['pid'];
                                if (isset($package_info['alive_days']) && $package_info['alive_days'] != 0) {
                                    $payment_info['expire_date'] = date('Y-m-d', strtotime("+" . $package_info['alive_days'] . " days"));
                                } else {
                                    $payment_info['expire_date'] = 'Never';
                                }

                                $gd_post_info = array_merge($gd_post_info, $payment_info);
                            }


                            $gd_post_info['post_location_id'] = $post_location_id;

                            $post_type = get_post_type($last_postid);

                            $table = $plugin_prefix . $post_type . '_detail';

                            geodir_save_post_info($last_postid, $gd_post_info);

                            if (!empty($image_names)) {
                                $upload_files++;
                                $menu_order = 1;
                                foreach ($image_names as $image_name) {

                                    $img_name_arr = explode('.', $image_name);

                                    $uploads = wp_upload_dir();
                                    $sub_dir = $uploads['subdir'];

                                    $arr_file_type = wp_check_filetype($image_name);
                                    $uploaded_file_type = $arr_file_type['type'];

                                    $attachment = array();
                                    $attachment['post_id'] = $last_postid;
                                    $attachment['title'] = $img_name_arr[0];
                                    $attachment['content'] = '';
                                    $attachment['file'] = $sub_dir . '/' . $image_name;
                                    $attachment['mime_type'] = $uploaded_file_type;
                                    $attachment['menu_order'] = $menu_order;
                                    $attachment['is_featured'] = 0;

                                    $attachment_set = '';

                                    foreach ($attachment as $key => $val) {
                                        if ($val != '')
                                            $attachment_set .= $key . " = '" . $val . "', ";
                                    }

                                    $attachment_set = trim($attachment_set, ", ");

                                    $wpdb->query("INSERT INTO " . GEODIR_ATTACHMENT_TABLE . " SET " . $attachment_set);

                                    if ($menu_order == 1) {

                                        $post_type = get_post_type($last_postid);

                                        $wpdb->query($wpdb->prepare("UPDATE " . $table . " SET featured_image = %s where post_id =%d", array($sub_dir . '/' . $image_name, $last_postid)));

                                    }

                                    $menu_order++;
                                }
                            }

                            $gd_post_info['package_id'] = $package_id;

                            /** This action is documented in geodirectory-functions/post-functions.php */
                            do_action('geodir_after_save_listing', $last_postid, $gd_post_info);

                            if (!empty($buffer[5])) {
                                if (in_array($buffer[5], geodir_get_posttypes())) {

                                    $taxonomies = geodir_get_posttype_info(addslashes($buffer[5]));
                                    wp_set_object_terms($last_postid, $my_post['post_tags'], $taxonomy = $taxonomies['taxonomies'][1]);
                                    wp_set_object_terms($last_postid, $my_post['post_category'], $taxonomy = $taxonomies['taxonomies'][0]);


                                    $post_default_category = isset($my_post['post_default_category']) ? $my_post['post_default_category'] : '';

                                    $post_category_str = isset($my_post['post_category_str']) ? $my_post['post_category_str'] : '';
                                    geodir_set_postcat_structure($last_postid, $taxonomy, $post_default_category, $post_category_str);

                                }

                            }

                        } else {
                            $invalid_title++;
                        }
                    }
                    $rowcount++;
                }
                fclose($fd);
                //unlink($csv_target_path);
                //rmdir($destination_path);
                if (!empty($filename))
                    geodir_remove_temp_images();


                echo $geodir_url = admin_url() . 'admin.php?page=geodirectory&tab=general_settings&active_tab=csv_upload_settings&msg=success&rowcount=' . $countpost . '&invalidcount=' . $address_invalid . '&blank_address=' . $blank_address . '&upload_files=' . $upload_files . '&invalid_post_type=' . $invalid_post_type . '&invalid_title=' . $invalid_title . '&total_records=' . $total_records;
                exit;
            } else {
                echo $geodir_url = admin_url() . 'admin.php?page=geodirectory&tab=general_settings&active_tab=csv_upload_settings&emsg=csvonly';
                exit;
            }
        }

    }
}

/**
 * Admin fields
 *
 * Loops though the geodirectory options array and outputs each field.
 *
 * @since 1.0.0
 * @package GeoDirectory
 * @global object $geodirectory GeoDirectory plugin object.
 * @param array $options The options array.
 */
function geodir_admin_fields($options)
{
    global $geodirectory;

    $first_title = true;
    $tab_id = '';
    $i = 0;
    foreach ($options as $value) :
        if (!isset($value['name'])) $value['name'] = '';
        if (!isset($value['class'])) $value['class'] = '';
        if (!isset($value['css'])) $value['css'] = '';
        if (!isset($value['std'])) $value['std'] = '';
        $desc = '';
        switch ($value['type']) :
            case 'dummy_installer':
                $post_type = isset($value['post_type']) ? $value['post_type'] : 'gd_place';
                geodir_autoinstall_admin_header($post_type);
                break;
            case 'csv_installer':
                geodir_insert_csv_post_data();
                break;
            case 'title':

                if ($i == 0) {
                    echo '<dl id="geodir_oiption_tabs" class="gd-tab-head"></dl>';
                    echo '<div class="inner_content_tab_main">';
                }

                $i++;

                if (isset($value['id']) && $value['id'])
                    $tab_id = $value['id'];

                if (isset($value['desc']) && $value['desc'])
                    $desc = '<span style=" text-transform:none;">:- ' . $value['desc'] . '</span>';

                if (isset($value['name']) && $value['name']) {
                    if ($first_title === true) {
                        $first_title = false;
                    } else {
                        echo '</div>';
                    }
                    echo '<dd id="' . trim($tab_id) . '" class="geodir_option_tabs" ><a href="javascript:void(0);">' . $value['name'] . '</a></dd>';

                    echo '<div id="sub_' . trim($tab_id) . '" class="gd-content-heading" style=" margin-bottom:10px;" >';
                }

                /**
                 * Called after a GeoDirectory settings title is output in the GD settings page.
                 *
                 * The action is called dynamically geodir_settings_$value['id'].
                 *
                 * @since 1.0.0
                 */
                do_action('geodir_settings_' . sanitize_title($value['id']));
                break;

            case 'no_tabs':

                echo '<div class="inner_content_tab_main">';
                echo '<div id="sub_' . trim($tab_id) . '" class="gd-content-heading" style=" margin-bottom:10px;" >';

                break;

            case 'sectionstart':
                if (isset($value['desc']) && $value['desc'])
                    $desc = '<span style=" text-transform:none;"> - ' . $value['desc'] . '</span>';
                if (isset($value['name']) && $value['name'])
                    echo '<h3>' . $value['name'] . $desc . '</h3>';
                /**
                 * Called after a GeoDirectory settings sectionstart is output in the GD settings page.
                 *
                 * The action is called dynamically geodir_settings_$value['id']_start.
                 *
                 * @since 1.0.0
                 */
                if (isset($value['id']) && $value['id']) do_action('geodir_settings_' . sanitize_title($value['id']) . '_start');
                echo '<table class="form-table">' . "\n\n";

                break;
            case 'sectionend':
                /**
                 * Called before a GeoDirectory settings sectionend is output in the GD settings page.
                 *
                 * The action is called dynamically geodir_settings_$value['id']_end.
                 *
                 * @since 1.0.0
                 */
                if (isset($value['id']) && $value['id']) do_action('geodir_settings_' . sanitize_title($value['id']) . '_end');
                echo '</table>';
                /**
                 * Called after a GeoDirectory settings sectionend is output in the GD settings page.
                 *
                 * The action is called dynamically geodir_settings_$value['id']_end.
                 *
                 * @since 1.0.0
                 */
                if (isset($value['id']) && $value['id']) do_action('geodir_settings_' . sanitize_title($value['id']) . '_after');
                break;
            case 'text':
                ?>
                <tr valign="top">
                <th scope="row" class="titledesc"><?php echo $value['name']; ?></th>
                <td class="forminp"><input name="<?php echo esc_attr($value['id']); ?>"
                                           id="<?php echo esc_attr($value['id']); ?>"
                                           type="<?php echo esc_attr($value['type']); ?>"
                                           style=" <?php echo esc_attr($value['css']); ?>"
                                           value="<?php if (get_option($value['id']) !== false && get_option($value['id']) !== null) {
                                               echo esc_attr(stripslashes(get_option($value['id'])));
                                           } else {
                                               echo esc_attr($value['std']);
                                           } ?>"/> <span class="description"><?php echo $value['desc']; ?></span></td>
                </tr><?php
                break;

            case 'password':
                ?>
                <tr valign="top">
                <th scope="row" class="titledesc"><?php echo $value['name']; ?></th>
                <td class="forminp"><input name="<?php echo esc_attr($value['id']); ?>"
                                           id="<?php echo esc_attr($value['id']); ?>"
                                           type="<?php echo esc_attr($value['type']); ?>"
                                           style="<?php echo esc_attr($value['css']); ?>"
                                           value="<?php if (get_option($value['id']) !== false && get_option($value['id']) !== null) {
                                               echo esc_attr(stripslashes(get_option($value['id'])));
                                           } else {
                                               echo esc_attr($value['std']);
                                           } ?>"/> <span class="description"><?php echo $value['desc']; ?></span></td>
                </tr><?php
                break;

            case 'html_content':
                ?>
                <tr valign="top">
                <th scope="row" class="titledesc"><?php echo $value['name']; ?></th>
                <td class="forminp"><span class="description"><?php echo $value['desc']; ?></span></td>
                </tr><?php
                break;

            case 'color' :
                ?>
                <tr valign="top">
                <th scope="row" class="titledesc"><?php echo $value['name']; ?></th>
                <td class="forminp"><input name="<?php echo esc_attr($value['id']); ?>"
                                           id="<?php echo esc_attr($value['id']); ?>" type="text"
                                           style="<?php echo esc_attr($value['css']); ?>"
                                           value="<?php if (get_option($value['id']) !== false && get_option($value['id']) !== null) {
                                               echo esc_attr(stripslashes(get_option($value['id'])));
                                           } else {
                                               echo esc_attr($value['std']);
                                           } ?>" class="colorpick"/> <span
                        class="description"><?php echo $value['desc']; ?></span>

                    <div id="colorPickerDiv_<?php echo esc_attr($value['id']); ?>" class="colorpickdiv"
                         style="z-index: 100;background:#eee;border:1px solid #ccc;position:absolute;display:none;"></div>
                </td>
                </tr><?php
                break;
            case 'image_width' :
                ?>
                <tr valign="top">
                <th scope="row" class="titledesc"><?php echo $value['name'] ?></th>
                <td class="forminp">

                    <?php _e('Width', GEODIRECTORY_TEXTDOMAIN); ?> <input
                        name="<?php echo esc_attr($value['id']); ?>_width"
                        id="<?php echo esc_attr($value['id']); ?>_width" type="text" size="3"
                        value="<?php if ($size = get_option($value['id'] . '_width')) echo stripslashes($size); else echo $value['std']; ?>"/>

                    <?php _e('Height', GEODIRECTORY_TEXTDOMAIN); ?> <input
                        name="<?php echo esc_attr($value['id']); ?>_height"
                        id="<?php echo esc_attr($value['id']); ?>_height" type="text" size="3"
                        value="<?php if ($size = get_option($value['id'] . '_height')) echo stripslashes($size); else echo $value['std']; ?>"/>

                    <label><?php _e('Hard Crop', GEODIRECTORY_TEXTDOMAIN); ?> <input
                            name="<?php echo esc_attr($value['id']); ?>_crop"
                            id="<?php echo esc_attr($value['id']); ?>_crop"
                            type="checkbox" <?php if (get_option($value['id'] . '_crop') != '') checked(get_option($value['id'] . '_crop'), 1); else checked(1); ?> /></label>

                    <span class="description"><?php echo $value['desc'] ?></span></td>
                </tr><?php
                break;
            case 'select':
                ?>
                <tr valign="top">
                <th scope="row" class="titledesc"><?php echo $value['name'] ?></th>
                <td class="forminp"><select name="<?php echo esc_attr($value['id']); ?>"
                                            id="<?php echo esc_attr($value['id']); ?>"
                                            style="<?php echo esc_attr($value['css']); ?>"
                                            class="<?php if (isset($value['class'])) echo $value['class']; ?>"
                                            option-ajaxchosen="false">
                        <?php
                        foreach ($value['options'] as $key => $val) {
                            $geodir_select_value = '';
                            if (get_option($value['id']) != '') {
                                if (get_option($value['id']) != '' && get_option($value['id']) == $key)
                                    $geodir_select_value = ' selected="selected" ';
                            } else {
                                if ($value['std'] == $key)
                                    $geodir_select_value = ' selected="selected" ';
                            }


                            ?>
                            <option
                                value="<?php echo esc_attr($key); ?>" <?php echo $geodir_select_value; ?> ><?php echo ucfirst($val) ?></option>
                        <?php
                        }
                        ?>
                    </select> <span class="description"><?php echo $value['desc'] ?></span>
                </td>
                </tr><?php
                break;

            case 'multiselect':
                ?>
                <tr valign="top">
                <th scope="row" class="titledesc"><?php echo $value['name']; ?></th>
                <td class="forminp"><select multiple="multiple" name="<?php echo esc_attr($value['id']); ?>[]"
                                            id="<?php echo esc_attr($value['id']); ?>"
                                            style="<?php echo esc_attr($value['css']); ?>"
                                            class="<?php if (isset($value['class'])) echo $value['class']; ?>"
                                            data-placeholder="<?php if (isset($value['placeholder_text'])) echo $value['placeholder_text'];?>"
                                            option-ajaxchosen="false">
                        <?php
                        foreach ($value['options'] as $key => $val) {
                            if (strpos($key, 'optgroup_start-') === 0) {
                                ?><optgroup label="<?php echo ucfirst($val); ?>"><?php
                            } else if (strpos($key, 'optgroup_end-') === 0) {
                                ?></optgroup><?php
                            } else {
                                ?>
                                <option
                                    value="<?php echo esc_attr($key); ?>" <?php if (is_array(get_option($value['id']))) {
                                    if (in_array($key, get_option($value['id']))) { ?> selected="selected" <?php }
                                } ?>><?php echo ucfirst($val) ?></option>
                            <?php
                            }
                        }
                        ?>
                    </select> <span class="description"><?php echo $value['desc'] ?></span>
                </td>
                </tr><?php
                break;
            case 'file':
                ?>
                <tr valign="top">
                <th scope="row" class="titledesc"><?php echo $value['name']; ?></th>
                <td class="forminp">
                    <input type="file" name="<?php echo esc_attr($value['id']); ?>"
                           id="<?php echo esc_attr($value['id']); ?>" style="<?php echo esc_attr($value['css']); ?>"
                           class="<?php if (isset($value['class'])) echo $value['class']; ?>"/>
                    <?php if (get_option($value['id'])) { ?>
                        <input type="hidden" name="<?php echo esc_attr($value['id']); ?>_remove"
                               id="<?php echo esc_attr($value['id']); ?>_remove" value="0">
                        <span class="description"> <?php $uploads = wp_upload_dir(); ?> <a
                                href="<?php echo get_option($value['id']); ?>"
                                target="_blank"><?php echo get_option($value['id']); ?></a> <i
                                title="<?php _e('remove file (set to empty)', GEODIRECTORY_TEXTDOMAIN); ?>"
                                onclick="jQuery('#<?php echo esc_attr($value['id']); ?>_remove').val('1'); jQuery( this ).parent().text('<?php _e('save to remove file', GEODIRECTORY_TEXTDOMAIN); ?>');"
                                class="fa fa-times gd-remove-file"></i></span>

                    <?php } ?>
                </td>
                </tr><?php
                break;
            case 'map_default_settings' :
                ?>

                <tr valign="top">
                    <th class="titledesc" width="40%"><?php _e('Default map language', GEODIRECTORY_TEXTDOMAIN);?></th>
                    <td width="60%">
                        <select name="geodir_default_map_language" style="width:60%">
                            <?php
                            $arr_map_langages = array(
                                'ar' => __('ARABIC', GEODIRECTORY_TEXTDOMAIN),
                                'eu' => __('BASQUE', GEODIRECTORY_TEXTDOMAIN),
                                'bg' => __('BULGARIAN', GEODIRECTORY_TEXTDOMAIN),
                                'bn' => __('BENGALI', GEODIRECTORY_TEXTDOMAIN),
                                'ca' => __('CATALAN', GEODIRECTORY_TEXTDOMAIN),
                                'cs' => __('CZECH', GEODIRECTORY_TEXTDOMAIN),
                                'da' => __('DANISH', GEODIRECTORY_TEXTDOMAIN),
                                'de' => __('GERMAN', GEODIRECTORY_TEXTDOMAIN),
                                'el' => __('GREEK', GEODIRECTORY_TEXTDOMAIN),
                                'en' => __('ENGLISH', GEODIRECTORY_TEXTDOMAIN),
                                'en-AU' => __('ENGLISH (AUSTRALIAN)', GEODIRECTORY_TEXTDOMAIN),
                                'en-GB' => __('ENGLISH (GREAT BRITAIN)', GEODIRECTORY_TEXTDOMAIN),
                                'es' => __('SPANISH', GEODIRECTORY_TEXTDOMAIN),
                                'eu' => __('BASQUE', GEODIRECTORY_TEXTDOMAIN),
                                'fa' => __('FARSI', GEODIRECTORY_TEXTDOMAIN),
                                'fi' => __('FINNISH', GEODIRECTORY_TEXTDOMAIN),
                                'fil' => __('FILIPINO', GEODIRECTORY_TEXTDOMAIN),
                                'fr' => __('FRENCH', GEODIRECTORY_TEXTDOMAIN),
                                'gl' => __('GALICIAN', GEODIRECTORY_TEXTDOMAIN),
                                'gu' => __('GUJARATI', GEODIRECTORY_TEXTDOMAIN),
                                'hi' => __('HINDI', GEODIRECTORY_TEXTDOMAIN),
                                'hr' => __('CROATIAN', GEODIRECTORY_TEXTDOMAIN),
                                'hu' => __('HUNGARIAN', GEODIRECTORY_TEXTDOMAIN),
                                'id' => __('INDONESIAN', GEODIRECTORY_TEXTDOMAIN),
                                'it' => __('ITALIAN', GEODIRECTORY_TEXTDOMAIN),
                                'iw' => __('HEBREW', GEODIRECTORY_TEXTDOMAIN),
                                'ja' => __('JAPANESE', GEODIRECTORY_TEXTDOMAIN),
                                'kn' => __('KANNADA', GEODIRECTORY_TEXTDOMAIN),
                                'ko' => __('KOREAN', GEODIRECTORY_TEXTDOMAIN),
                                'lt' => __('LITHUANIAN', GEODIRECTORY_TEXTDOMAIN),
                                'lv' => __('LATVIAN', GEODIRECTORY_TEXTDOMAIN),
                                'ml' => __('MALAYALAM', GEODIRECTORY_TEXTDOMAIN),
                                'mr' => __('MARATHI', GEODIRECTORY_TEXTDOMAIN),
                                'nl' => __('DUTCH', GEODIRECTORY_TEXTDOMAIN),
                                'no' => __('NORWEGIAN', GEODIRECTORY_TEXTDOMAIN),
                                'pl' => __('POLISH', GEODIRECTORY_TEXTDOMAIN),
                                'pt' => __('PORTUGUESE', GEODIRECTORY_TEXTDOMAIN),
                                'pt-BR' => __('PORTUGUESE (BRAZIL)', GEODIRECTORY_TEXTDOMAIN),
                                'pt-PT' => __('PORTUGUESE (PORTUGAL)', GEODIRECTORY_TEXTDOMAIN),
                                'ro' => __('ROMANIAN', GEODIRECTORY_TEXTDOMAIN),
                                'ru' => __('RUSSIAN', GEODIRECTORY_TEXTDOMAIN),
                                'ru' => __('RUSSIAN', GEODIRECTORY_TEXTDOMAIN),
                                'sk' => __('SLOVAK', GEODIRECTORY_TEXTDOMAIN),
                                'sl' => __('SLOVENIAN', GEODIRECTORY_TEXTDOMAIN),
                                'sr' => __('SERBIAN', GEODIRECTORY_TEXTDOMAIN),
                                'sv' => __('	SWEDISH', GEODIRECTORY_TEXTDOMAIN),
                                'tl' => __('TAGALOG', GEODIRECTORY_TEXTDOMAIN),
                                'ta' => __('TAMIL', GEODIRECTORY_TEXTDOMAIN),
                                'te' => __('TELUGU', GEODIRECTORY_TEXTDOMAIN),
                                'th' => __('THAI', GEODIRECTORY_TEXTDOMAIN),
                                'tr' => __('TURKISH', GEODIRECTORY_TEXTDOMAIN),
                                'uk' => __('UKRAINIAN', GEODIRECTORY_TEXTDOMAIN),
                                'vi' => __('VIETNAMESE', GEODIRECTORY_TEXTDOMAIN),
                                'zh-CN' => __('CHINESE (SIMPLIFIED)', GEODIRECTORY_TEXTDOMAIN),
                                'zh-TW' => __('CHINESE (TRADITIONAL)', GEODIRECTORY_TEXTDOMAIN),
                            );
                            $geodir_default_map_language = get_option('geodir_default_map_language');
                            if (empty($geodir_default_map_language))
                                $geodir_default_map_language = 'en';
                            foreach ($arr_map_langages as $language_key => $language_txt) {
                                if (!empty($geodir_default_map_language) && $language_key == $geodir_default_map_language)
                                    $geodir_default_language_selected = "selected='selected'";
                                else
                                    $geodir_default_language_selected = '';

                                ?>
                                <option
                                    value="<?php echo $language_key?>" <?php echo $geodir_default_language_selected; ?>><?php echo $language_txt; ?></option>

                            <?php }
                            ?>
                        </select>
                    </td>
                </tr>

                <tr valign="top">
                    <th class="titledesc"
                        width="40%"><?php _e('Default post type search on map', GEODIRECTORY_TEXTDOMAIN);?></th>
                    <td width="60%">
                        <select name="geodir_default_map_search_pt" style="width:60%">
                            <?php
                            $post_types = geodir_get_posttypes('array');
                            $geodir_default_map_search_pt = get_option('geodir_default_map_search_pt');
                            if (empty($geodir_default_map_search_pt))
                                $geodir_default_map_search_pt = 'gd_place';
                            if (is_array($post_types)) {
                                foreach ($post_types as $key => $post_types_obj) {
                                    if (!empty($geodir_default_map_search_pt) && $key == $geodir_default_map_search_pt)
                                        $geodir_search_pt_selected = "selected='selected'";
                                    else
                                        $geodir_search_pt_selected = '';

                                    ?>
                                    <option
                                        value="<?php echo $key?>" <?php echo $geodir_search_pt_selected; ?>><?php echo $post_types_obj['labels']['singular_name']; ?></option>

                                <?php }

                            }

                            ?>
                        </select>
                    </td>
                </tr>

                <?php
                break;

            case 'map':
                ?>
                <tr valign="top">
                    <td class="forminp">
                        <?php
                        global $post_cat, $cat_display;
                        $post_types = geodir_get_posttypes('object');
                        $cat_display = 'checkbox';
                        $gd_post_types = get_option('geodir_exclude_post_type_on_map');
                        $gd_cats = get_option('geodir_exclude_cat_on_map');
                        $gd_cats_upgrade = (int)get_option('geodir_exclude_cat_on_map_upgrade');
                        $count = 1;
                        ?>
                        <table width="70%" class="widefat">
                            <thead>
                            <tr>
                                <th><b><?php echo DESIGN_POST_TYPE_SNO; ?></b></th>
                                <th><b><?php echo DESIGN_POST_TYPE; ?></b></th>
                                <th><b><?php echo DESIGN_POST_TYPE_CAT; ?></b></th>
                            </tr>
                            <?php
                            $gd_categs = $gd_cats;
                            foreach ($post_types as $key => $post_types_obj) :
                                $checked = is_array($gd_post_types) && in_array($key, $gd_post_types) ? 'checked="checked"' : '';
                                $gd_taxonomy = geodir_get_taxonomies($key);
                                if ($gd_cats_upgrade) {
                                    $gd_cat_taxonomy = isset($gd_taxonomy[0]) ? $gd_taxonomy[0] : '';
                                    $gd_cats = isset($gd_categs[$gd_cat_taxonomy]) ? $gd_categs[$gd_cat_taxonomy] : array();
                                    $gd_cats = !empty($gd_cats) && is_array($gd_cats) ? array_unique($gd_cats) : array();
                                }
                                $post_cat = implode(',', $gd_cats);
                                $gd_taxonomy_list = geodir_custom_taxonomy_walker($gd_taxonomy);
                                ?>
                                <tr>
                                    <td valign="top" width="5%"><?php echo $count; ?></td>
                                    <td valign="top" width="25%" id="td_post_types"><input type="checkbox"
                                                                                           name="home_map_post_types[]"
                                                                                           id="<?php echo esc_attr($value['id']); ?>"
                                                                                           value="<?php echo $key; ?>"
                                                                                           class="map_post_type" <?php echo $checked;?> />
                                        <?php echo $post_types_obj->labels->singular_name; ?></td>
                                    <td width="40%">
                                        <div class="home_map_category" style="overflow:auto;width:200px;height:100px;"
                                             id="<?php echo $key; ?>"><?php echo $gd_taxonomy_list; ?></div>
                                    </td>
                                </tr>
                                <?php $count++; endforeach; ?>
                            </thead>
                        </table>
                        <p><?php _e('Note: Tick respective post type or categories which you want to hide from home page map widget.', GEODIRECTORY_TEXTDOMAIN)?></p>
                    </td>
                </tr>
                <?php
                break;

            case 'checkbox' :

                if (!isset($value['checkboxgroup']) || (isset($value['checkboxgroup']) && $value['checkboxgroup'] == 'start')) :
                    ?>
                    <tr valign="top">
                    <th scope="row" class="titledesc"><?php echo $value['name'] ?></th>
                    <td class="forminp">
                <?php
                endif;

                ?>
                <fieldset>
                    <legend class="screen-reader-text"><span><?php echo $value['name'] ?></span></legend>
                    <label for="<?php echo $value['id'] ?>">
                        <input name="<?php echo esc_attr($value['id']); ?>" id="<?php echo esc_attr($value['id']); ?>"
                               type="checkbox" value="1" <?php checked(get_option($value['id']), true); ?> />
                        <?php echo $value['desc'] ?></label><br>
                </fieldset>
                <?php

                if (!isset($value['checkboxgroup']) || (isset($value['checkboxgroup']) && $value['checkboxgroup'] == 'end')) :
                    ?>
                    </td>
                    </tr>
                <?php
                endif;

                break;

            case 'radio' :

                if (!isset($value['radiogroup']) || (isset($value['radiogroup']) && $value['radiogroup'] == 'start')) :
                    ?>
                    <tr valign="top">
                    <th scope="row" class="titledesc"><?php echo $value['name'] ?></th>
                    <td class="forminp">
                <?php
                endif;

                ?>
                <fieldset>
                    <legend class="screen-reader-text"><span><?php echo $value['name'] ?></span></legend>
                    <label for="<?php echo $value['id'];?>">
                        <input name="<?php echo esc_attr($value['id']); ?>"
                               id="<?php echo esc_attr($value['id'] . $value['value']); ?>" type="radio"
                               value="<?php echo $value['value'] ?>" <?php if (get_option($value['id']) == $value['value']) {
                            echo 'checked="checked"';
                        } ?> />
                        <?php echo $value['desc']; ?></label><br>
                </fieldset>
                <?php

                if (!isset($value['radiogroup']) || (isset($value['radiogroup']) && $value['radiogroup'] == 'end')) :
                    ?>
                    </td>
                    </tr>
                <?php
                endif;

                break;

            case 'textarea':
                ?>
                <tr valign="top">
                <th scope="row" class="titledesc"><?php echo $value['name'] ?></th>
                <td class="forminp">
                    <textarea
                        <?php if (isset($value['args'])) echo $value['args'] . ' '; ?>name="<?php echo esc_attr($value['id']); ?>"
                        id="<?php echo esc_attr($value['id']); ?>"
                        style="<?php echo esc_attr($value['css']); ?>"><?php if (get_option($value['id'])) echo esc_textarea(stripslashes(get_option($value['id']))); else echo esc_textarea($value['std']); ?></textarea><span
                        class="description"><?php echo $value['desc'] ?></span>

                </td>
                </tr><?php
                break;

            case 'editor':
                ?>
                <tr valign="top">
                <th scope="row" class="titledesc"><?php echo $value['name'] ?></th>
                <td class="forminp"><?php

                    $content = '';
                    if (get_option($value['id']))
                        $content = stripslashes(get_option($value['id']));
                    else
                        $content = $value['std'];

                    $editor_settings = array('media_buttons' => false, 'textarea_rows' => 10);

                    wp_editor($content, esc_attr($value['id']), $editor_settings);

                    ?> <span class="description"><?php echo $value['desc'] ?></span>

                </td>
                </tr><?php
                break;

            case 'single_select_page' :
                $page_setting = (int)get_option($value['id']);

                $args = array('name' => $value['id'],
                    'id' => $value['id'],
                    'sort_column' => 'menu_order',
                    'sort_order' => 'ASC',
                    'show_option_none' => ' ',
                    'class' => $value['class'],
                    'echo' => false,
                    'selected' => $page_setting);

                if (isset($value['args'])) $args = wp_parse_args($value['args'], $args);

                ?>
                <tr valign="top" class="single_select_page">
                <th scope="row" class="titledesc"><?php echo $value['name'] ?></th>
                <td class="forminp">
                    <?php echo str_replace(' id=', " data-placeholder='" . __('Select a page...', 'geodirectory') . "' style='" . $value['css'] . "' class='" . $value['class'] . "' id=", wp_dropdown_pages($args)); ?>
                    <span class="description"><?php echo $value['desc'] ?></span>
                </td>
                </tr><?php
                break;
            case 'single_select_country' :
                $countries = $geodirectory->countries->countries;
                $country_setting = (string)get_option($value['id']);
                if (strstr($country_setting, ':')) :
                    $country = current(explode(':', $country_setting));
                    $state = end(explode(':', $country_setting));
                else :
                    $country = $country_setting;
                    $state = '*';
                endif;
                ?>
                <tr valign="top">
                <th scope="rpw" class="titledesc"><?php echo $value['name'] ?></th>
                <td class="forminp"><select name="<?php echo esc_attr($value['id']); ?>"
                                            style="<?php echo esc_attr($value['css']); ?>"
                                            data-placeholder="<?php _e('Choose a country&hellip;', GEODIRECTORY_TEXTDOMAIN); ?>"
                                            title="Country" class="chosen_select">
                        <?php echo $geodirectory->countries->country_dropdown_options($country, $state); ?>
                    </select> <span class="description"><?php echo $value['desc'] ?></span>
                </td>
                </tr><?php
                break;
            case 'multi_select_countries' :
                $countries = $geodirectory->countries->countries;
                asort($countries);
                $selections = (array)get_option($value['id']);
                ?>
                <tr valign="top">
                <th scope="row" class="titledesc"><?php echo $value['name'] ?></th>
                <td class="forminp">
                    <select multiple="multiple" name="<?php echo esc_attr($value['id']); ?>[]" style="width:450px;"
                            data-placeholder="<?php _e('Choose countries&hellip;', GEODIRECTORY_TEXTDOMAIN); ?>"
                            title="Country" class="chosen_select">
                        <?php
                        if ($countries) foreach ($countries as $key => $val) :
                            echo '<option value="' . $key . '" ' . selected(in_array($key, $selections), true, false) . '>' . $val . '</option>';
                        endforeach;
                        ?>
                    </select>
                </td>
                </tr>

                <?php

                break;

            case 'google_analytics' :
                $selections = (array)get_option($value['id']);
                if(get_option('geodir_ga_client_id') && get_option('geodir_ga_client_secret') ) {
                    ?>
                    <tr valign="top">
                        <th scope="row" class="titledesc"><?php echo $value['name'] ?></th>
                        <td class="forminp">


                            <?php

                            $oAuthURL = "https://accounts.google.com/o/oauth2/auth?";
                            $scope = "scope=https://www.googleapis.com/auth/analytics.readonly";
                            $state = "&state=123";//any string
                            $redirect_uri = "&redirect_uri=" . admin_url('admin-ajax.php') . "?action=geodir_ga_callback";
                            $response_type = "&response_type=code";
                            $client_id = "&client_id=".get_option('geodir_ga_client_id');
                            $access_type = "&access_type=offline";
                            $approval_prompt = "&approval_prompt=force";

                            $auth_url = $oAuthURL . $scope . $state . $redirect_uri . $response_type . $client_id . $access_type . $approval_prompt;


                            ?>
                            <script>
                                function gd_ga_popup() {
                                    var win = window.open("<?php echo $auth_url;?>", "Google Analytics", "");
                                    var pollTimer = window.setInterval(function () {
                                        if (win.closed !== false) { // !== is required for compatibility with Opera
                                            window.clearInterval(pollTimer);

                                            jQuery(".general_settings .submit .button-primary").trigger('click');
                                        }
                                    }, 200);
                                }
                            </script>

                            <?php
                            if (get_option('gd_ga_refresh_token')) {
                                ?>
                                <span class="button-primary"
                                      onclick="gd_ga_popup();"><?php _e('Re-authorize', GEODIRECTORY_TEXTDOMAIN); ?></span>
                                <span
                                    style="color: green; font-weight: bold;"><?php _e('Authorized', GEODIRECTORY_TEXTDOMAIN); ?></span>
                            <?php
                            } else {
                                ?>
                                <span class="button-primary"
                                      onclick="gd_ga_popup();"><?php _e('Authorize', GEODIRECTORY_TEXTDOMAIN);?></span>
                            <?php
                            }
                            ?>
                        </td>
                    </tr>

                <?php
                }

                break;

            case 'field_seperator' :

                ?>
                <tr valign="top">
                    <td colspan="2" class="forminp geodir_line_seperator"></td>
                </tr>
                <?php

                break;

        endswitch;

    endforeach;

    if ($first_title === false) {
        echo "</div>";
    }

    ?>

    <script type="text/javascript">


        jQuery(document).ready(function () {

            jQuery('.geodir_option_tabs').each(function (ele) {
                jQuery('#geodir_oiption_tabs').append(jQuery(this));
            });


            jQuery('.geodir_option_tabs').removeClass('gd-tab-active');
            jQuery('.geodir_option_tabs:first').addClass('gd-tab-active');

            jQuery('.gd-content-heading').hide();
            jQuery('.gd-content-heading:first').show();
            jQuery('.geodir_option_tabs').bind('click', function () {
                var tab_id = jQuery(this).attr('id');

                if (tab_id == 'dummy_data_settings') {
                    jQuery('p .button-primary').hide();
                } else if (tab_id == 'csv_upload_settings') {
                    jQuery('p .button-primary').hide();
                } else {
                    jQuery('.button-primary').show();
                }

                if (jQuery('#sub_' + tab_id + ' div').hasClass('geodir_auto_install'))
                    jQuery('p .button-primary').hide();

                jQuery('.geodir_option_tabs').removeClass('gd-tab-active');
                jQuery(this).addClass('gd-tab-active');
                jQuery('.gd-content-heading').hide();
                jQuery('#sub_' + tab_id).show();
                jQuery('.active_tab').val(tab_id);
                jQuery("select.chosen_select").trigger("chosen:updated"); //refresh closen
            });


            <?php
            if(isset($_REQUEST['active_tab']) && $_REQUEST['active_tab'] != '')
            {

                ?>
            jQuery('.geodir_option_tabs').removeClass('gd-tab-active');
            jQuery('#<?php echo $_REQUEST['active_tab'];?>').addClass('gd-tab-active');

            jQuery('.gd-content-heading').hide();
            jQuery('#sub_<?php echo $_REQUEST['active_tab'];?>').show();


            <?php
        }
        ?>

        });

    </script>

<?php
}

/**
 * Prints post information meta box content.
 *
 * @since 1.0.0
 * @package GeoDirectory
 * @global object $post The post object.
 * @global int $post_id The post ID.
 */
function geodir_post_info_setting()
{
    global $post, $post_id;

    $post_type = get_post_type();

    $package_info = array();

    $package_info = geodir_post_package_info($package_info, $post, $post_type);
    wp_nonce_field(plugin_basename(__FILE__), 'geodir_post_info_noncename');
    echo '<div id="geodir_wrapper">';
    /**
     * Called before the GD custom fields are output in the wp-admin area.
     *
     * @since 1.0.0
     * @see 'geodir_after_default_field_in_meta_box'
     */
    do_action('geodir_before_default_field_in_meta_box');
    //geodir_get_custom_fields_html($package_info->pid,'default',$post_type);
    // to display all fields in one information box
    geodir_get_custom_fields_html($package_info->pid, 'all', $post_type);
    /**
     * Called after the GD custom fields are output in the wp-admin area.
     *
     * @since 1.0.0
     * @see 'geodir_before_default_field_in_meta_box'
     */
    do_action('geodir_after_default_field_in_meta_box');
    echo '</div>';
}

/**
 * Prints additional information meta box content.
 *
 * @since 1.0.0
 * @package GeoDirectory
 * @global object $post The post object.
 * @global int $post_id The post ID.
 */
function geodir_post_addinfo_setting()
{
    global $post, $post_id;

    $post_type = get_post_type();

    $package_info = array();

    $package_info = geodir_post_package_info($package_info, $post, $post_type);

    wp_nonce_field(plugin_basename(__FILE__), 'geodir_post_addinfo_noncename');
    echo '<div id="geodir_wrapper">';
    geodir_get_custom_fields_html($package_info->pid, 'custom', $post_type);
    echo '</div>';

}

/**
 * Prints Attachments meta box content.
 *
 * @since 1.0.0
 * @package GeoDirectory
 * @global object $post The post object.
 * @global int $post_id The post ID.
 */
function geodir_post_attachments()
{
    global $post, $post_id;

    wp_nonce_field(plugin_basename(__FILE__), 'geodir_post_attachments_noncename');

    if (geodir_get_featured_image($post_id, 'thumbnail')) {
        echo '<h4>' . __('Featured Image', GEODIRECTORY_TEXTDOMAIN) . '</h4>';
        geodir_show_featured_image($post_id, 'thumbnail');
    }

    $image_limit = 0;

    ?>


    <h5 class="form_title">
        <?php if ($image_limit != 0 && $image_limit == 1) {
            echo '<br /><small>(' . __('You can upload', GEODIRECTORY_TEXTDOMAIN) . ' ' . $image_limit . ' ' . __('image with this package', GEODIRECTORY_TEXTDOMAIN) . ')</small>';
        } ?>
        <?php if ($image_limit != 0 && $image_limit > 1) {
            echo '<br /><small>(' . __('You can upload', GEODIRECTORY_TEXTDOMAIN) . ' ' . $image_limit . ' ' . __('images with this package', GEODIRECTORY_TEXTDOMAIN) . ')</small>';
        } ?>
        <?php if ($image_limit == 0) {
            echo '<br /><small>(' . __('You can upload unlimited images with this package', GEODIRECTORY_TEXTDOMAIN) . ')</small>';
        } ?>
    </h5>


    <?php

    $curImages = geodir_get_images($post_id);
    $place_img_array = array();

    if (!empty($curImages)):
        foreach ($curImages as $p_img):
            $place_img_array[] = $p_img->src;
        endforeach;
    endif;

    if (!empty($place_img_array))
        $curImages = implode(',', $place_img_array);


    // adjust values here
    $id = "post_images"; // this will be the name of form field. Image url(s) will be submitted in $_POST using this key. So if $id == �img1� then $_POST[�img1�] will have all the image urls

    $svalue = $curImages; // this will be initial value of the above form field. Image urls.

    $multiple = true; // allow multiple files upload

    $width = geodir_media_image_large_width(); // If you want to automatically resize all uploaded images then provide width here (in pixels)

    $height = geodir_media_image_large_height(); // If you want to automatically resize all uploaded images then provide height here (in pixels)

    ?>

    <div class="gtd-form_row clearfix" id="<?php echo $id; ?>dropbox" style="border:1px solid #999999; padding:5px;"
         align="center">
        <input type="hidden" name="<?php echo $id; ?>" id="<?php echo $id; ?>" value="<?php echo $svalue; ?>"/>

        <div
            class="plupload-upload-uic hide-if-no-js <?php if ($multiple): ?>plupload-upload-uic-multiple<?php endif; ?>"
            id="<?php echo $id; ?>plupload-upload-ui">
            <h4><?php _e('Drop files to upload', GEODIRECTORY_TEXTDOMAIN);?></h4>
            <input id="<?php echo $id; ?>plupload-browse-button" type="button"
                   value="<?php _e('Select Files', GEODIRECTORY_TEXTDOMAIN); ?>" class="button"/>
            <span class="ajaxnonceplu" id="ajaxnonceplu<?php echo wp_create_nonce($id . 'pluploadan'); ?>"></span>
            <?php if ($width && $height): ?>
                <span class="plupload-resize"></span>
                <span class="plupload-width" id="plupload-width<?php echo $width; ?>"></span>
                <span class="plupload-height" id="plupload-height<?php echo $height; ?>"></span>
            <?php endif; ?>
            <div class="filelist"></div>
        </div>
        <div class="plupload-thumbs <?php if ($multiple): ?>plupload-thumbs-multiple<?php endif; ?> clearfix"
             id="<?php echo $id; ?>plupload-thumbs" style="border-top:1px solid #ccc; padding-top:10px;">
        </div>
        <span
            id="upload-msg"><?php _e('Please drag &amp; drop the images to rearrange the order', GEODIRECTORY_TEXTDOMAIN);?></span>
        <span id="<?php echo $id; ?>upload-error" style="display:none"></span>
    </div>

<?php

}

/**
 * Updates custom table when post get updated.
 *
 * @since 1.0.0
 * @package GeoDirectory
 * @param int $post_ID The post ID.
 * @param object $post_after Post object after the update.
 * @param object $post_before Post object before the update.
 */
function geodir_action_post_updated($post_ID, $post_after, $post_before)
{
    $post_type = get_post_type($post_ID);

    if (isset($_POST['action']) && $_POST['action'] == 'inline-save') {
        if ($post_type != '' && in_array($post_type, geodir_get_posttypes()) && !wp_is_post_revision($post_ID) && !empty($post_after->post_title) && $post_after->post_title != $post_before->post_title) {
            geodir_save_post_meta($post_ID, 'post_title', $post_after->post_title);
        }
    }
}

/**
 * Add Listing published bcc option.
 *
 * WP Admin -> Geodirectory -> Notifications -> Site Bcc Options
 *
 * @since 1.0.0
 * @package GeoDirectory
 * @param array $settings The settings array.
 * @return array
 */
function geodir_notification_add_bcc_option($settings)
{
    if (!empty($settings)) {
        $new_settings = array();
        foreach ($settings as $setting) {
            if (isset($setting['id']) && $setting['id'] == 'site_bcc_options' && isset($setting['type']) && $setting['type'] == 'sectionend') {
                $geodir_bcc_listing_published_yes = array(
                    'name' => __('Listing published', GEODIRECTORY_TEXTDOMAIN),
                    'desc' => __('Yes', GEODIRECTORY_TEXTDOMAIN),
                    'id' => 'geodir_bcc_listing_published',
                    'std' => 'yes',
                    'type' => 'radio',
                    'value' => '1',
                    'radiogroup' => 'start'
                );

                $geodir_bcc_listing_published_no = array(
                    'name' => __('Listing published', GEODIRECTORY_TEXTDOMAIN),
                    'desc' => __('No', GEODIRECTORY_TEXTDOMAIN),
                    'id' => 'geodir_bcc_listing_published',
                    'std' => 'yes',
                    'type' => 'radio',
                    'value' => '0',
                    'radiogroup' => 'end'
                );

                $new_settings[] = $geodir_bcc_listing_published_yes;
                $new_settings[] = $geodir_bcc_listing_published_no;
            }
            $new_settings[] = $setting;
        }
        $settings = $new_settings;
    }

    return $settings;
}


add_action('wp_ajax_get_gd_theme_compat_callback', 'get_gd_theme_compat_callback');

/**
 * Exports theme compatibility data for given theme.
 *
 * @since 1.0.0
 * @package GeoDirectory
 * @global object $wpdb WordPress Database object.
 */
function get_gd_theme_compat_callback()
{
    global $wpdb;
    $themes = get_option('gd_theme_compats');

    if (isset($_POST['theme']) && isset($themes[$_POST['theme']]) && !empty($themes[$_POST['theme']])) {
        if (isset($_POST['export'])) {
            echo json_encode(array($_POST['theme'] => $themes[$_POST['theme']]));
        } else {
            echo json_encode($themes[$_POST['theme']]);
        }

    }

    die();
}

add_action('wp_ajax_get_gd_theme_compat_import_callback', 'get_gd_theme_compat_import_callback');

/**
 * Imports theme compatibility data for given theme.
 *
 * @since 1.0.0
 * @package GeoDirectory
 * @global object $wpdb WordPress Database object.
 */
function get_gd_theme_compat_import_callback()
{
    global $wpdb;
    $themes = get_option('gd_theme_compats');
    if (isset($_POST['theme']) && !empty($_POST['theme'])) {
        $json = json_decode(stripslashes($_POST['theme']), true);
        if (!empty($json) && is_array($json)) {
            $key = key($json);
            $themes[$key] = $json[$key];
            update_option('gd_theme_compats', $themes);
            echo $key;
            die();
        }
    }
    echo '0';
    die();
}


/**
 * Sets theme compatabilty options.
 *
 * @since 1.0.0
 * @package GeoDirectory
 * @global object $wpdb WordPress Database object.
 */
function gd_set_theme_compat()
{
    global $wpdb;
    $theme = wp_get_theme();

    if ($theme->parent()) {
        $theme_name = str_replace(" ", "_", $theme->parent()->get('Name'));
    } else {
        $theme_name = str_replace(" ", "_", $theme->get('Name'));
    }

    $theme_compats = get_option('gd_theme_compats');
    $current_compat = get_option('gd_theme_compat');
    $current_compat = str_replace("_custom", "", $current_compat);

    if ($current_compat == $theme_name && strpos("_custom", get_option('gd_theme_compat')) !== false) {
        return;
    }// if already running corect compat then bail

    if (isset($theme_compats[$theme_name])) {// if there is a compat avail then set it
        update_option('gd_theme_compat', $theme_name);
        update_option('theme_compatibility_setting', $theme_compats[$theme_name]);

        // if there are default options to set then set them
        if (isset($theme_compats[$theme_name]['geodir_theme_compat_default_options']) && !empty($theme_compats[$theme_name]['geodir_theme_compat_default_options'])) {

            foreach ($theme_compats[$theme_name]['geodir_theme_compat_default_options'] as $key => $val) {
                update_option($key, $val);
            }
        }

    } else {
        update_option('gd_theme_compat', '');
        update_option('theme_compatibility_setting', '');
    }


}


add_action('wp_loaded', 'gd_check_avada_compat');
/**
 * Function to check if Avada needs header.php replaced
 *
 * @since 1.0.0
 * @package GeoDirectory
 */
function gd_check_avada_compat()
{
    if (function_exists('avada_load_textdomain') && !get_option('avada_nag')) {
        add_action('admin_notices', 'gd_avada_compat_warning');
    }
}


/**
 * Displays Avada compatibility warning.
 *
 * @since 1.0.0
 * @package GeoDirectory
 */
function gd_avada_compat_warning()
{

    /*
    $msg_type = error
    $msg_type = updated fade
    $msg_type = update-nag
    */

    $plugin = 'avada-nag';
    $timestamp = 'avada-nag1234';
    $message = __('Since Avada 3.8+ they have added hooks for compatibility for GeoDirectory so the header.php modification is no longer required. <a href="http://docs.wpgeodirectory.com/avada-compatibility-header-php/" target="_blank">See here</a>', GEODIRECTORY_TEXTDOMAIN);
    echo '<div id="' . $timestamp . '"  class="error">';
    echo '<span class="gd-remove-noti" onclick="gdRemoveANotification(\'' . $plugin . '\',\'' . $timestamp . '\');" ><i class="fa fa-times"></i></span>';
    echo "<img class='gd-icon-noti' src='" . plugin_dir_url('') . "geodirectory/geodirectory-assets/images/favicon.ico' > ";
    echo "<p>$message</p>";
    echo "</div>";

    ?>
    <script>
        function gdRemoveANotification($plugin, $timestamp) {

            jQuery('#' + $timestamp).css("background-color", "red");
            jQuery('#' + $timestamp).fadeOut("slow");
            // This does the ajax request
            jQuery.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    'action': 'geodir_avada_remove_notification',
                    'plugin': $plugin,
                    'timestamp': $timestamp
                },
                success: function (data) {
                    // This outputs the result of the ajax request
                    //alert(data);
                },
                error: function (errorThrown) {
                    console.log(errorThrown);
                }
            });

        }
    </script>
    <style>
        .gd-icon-noti {
            float: left;
            margin-top: 10px;
            margin-right: 5px;
        }

        .update-nag .gd-icon-noti {
            margin-top: 2px;
        }

        .gd-remove-noti {
            float: right;
            margin-top: -20px;
            margin-right: -20px;
            color: #FF0000;
            cursor: pointer;
        }

        .updated .gd-remove-noti, .error .gd-remove-noti {
            float: right;
            margin-top: -10px;
            margin-right: -17px;
            color: #FF0000;
            cursor: pointer;
        }


    </style>
<?php

}


/**
 * Removes Avada compatibility warning.
 *
 * @since 1.0.0
 * @package GeoDirectory
 */
function geodir_avada_remove_notification()
{
    update_option('avada_nag', TRUE);

    // Always die in functions echoing ajax content
    die();
}


add_action('wp_ajax_geodir_avada_remove_notification', 'geodir_avada_remove_notification');

/**
 * Get the current post type in the wordPress admin
 *
 * @since 1.4.2
 * @package GeoDirectory
 *
 * @global null|WP_Post $post Post object.
 * @global string $typenow Post type.
 * @global object|WP_Screen $current_screen Current screen object
 *
 * @return string Post type ex: gd_place
 */
function geodir_admin_current_post_type() {
	global $post, $typenow, $current_screen;
	
	$post_type = NULL;
	
	if ($post && isset($post->post_type))
		$post_type = $post->post_type;
	elseif ($typenow)
		$post_type = $typenow;
	elseif ($current_screen && isset($current_screen->post_type))
		$post_type = $current_screen->post_type;
	elseif (isset($_REQUEST['post_type']))
		$post_type = sanitize_key($_REQUEST['post_type']);
	elseif (isset($_REQUEST['post']) && get_post_type($_REQUEST['post']))
		$post_type = get_post_type($_REQUEST['post']);

	return $post_type;
}

/**
 * Fires before updating geodirectory admin settings.
 *
 * @since 1.4.2
 * @package GeoDirectory
 *
 * @param string $current_tab Current tab in geodirectory settings.
 * @param array  $geodir_settings Array of geodirectory settings.
 */
function geodir_before_update_options($current_tab, $geodir_settings) {
	$active_tab = isset($_REQUEST['active_tab']) ? trim($_REQUEST['active_tab']) : '';
		
	// Permalink settings
	if ($current_tab == 'permalink_settings') {
		$listing_prefix = isset($_POST['geodir_listing_prefix']) ? trim($_POST['geodir_listing_prefix']) : '';
		$location_prefix = isset($_POST['geodir_location_prefix']) ? trim($_POST['geodir_location_prefix']) : '';
		
		// Don't allow same slug url for listing and location
		if (strtolower($listing_prefix) == strtolower($location_prefix)) {
			$redirect_url = admin_url('admin.php?page=geodirectory&tab=' . $current_tab . '&active_tab=' . $active_tab . '&msg=fail&gderr=21');
        	wp_redirect($redirect_url);
			exit;
		}
	}
}


/**
 * Removes the preview buttons from the wp-admin area for GD post types.
 *
 * This was removed as the preview page was causing bugs.
 *
 * @global string $post_type The current post type.
 * @since 1.4.3
 * @package GeoDirectory
 */
function geodir_hide_admin_preview_button() {
    global $post_type;
    $post_types = geodir_get_posttypes();
    if(in_array($post_type, $post_types))
        echo '<style type="text/css">#post-preview, #view-post-btn{display: none;}</style>';
}
add_action( 'admin_head-post-new.php', 'geodir_hide_admin_preview_button' );
add_action( 'admin_head-post.php', 'geodir_hide_admin_preview_button' );

/**
 * Add the tab in left sidebar menu fro import & export page.
 *
 * @since 1.4.6
 * @package GeoDirectory
 *
 * @return array Array of tab data.
 */
function geodir_import_export_tab( $tabs ) {
	$tabs['import_export'] = array( 'label' => __( 'Import & Export', GEODIRECTORY_TEXTDOMAIN ) );
    return $tabs;
}

/**
 * Display the page to manage import/export categories/listings.
 *
 * @since 1.4.6
 * @package GeoDirectory
 *
 * @return string Html content.
 */
function geodir_import_export_page() {
	$nonce = wp_create_nonce( 'geodir_import_export_nonce' );
	$gd_cats_sample_csv = geodir_plugin_url() . '/geodirectory-assets/gd_sample_categories.csv';
    /**
     * Filter sample category data csv file url.
     *
     * @since 1.0.0
     * @package GeoDirectory
     *
     * @param string $gd_cats_sample_csv Sample category data csv file url.
     */
	$gd_cats_sample_csv = apply_filters( 'geodir_export_cats_sample_csv', $gd_cats_sample_csv );
	
	$gd_posts_sample_csv = geodir_plugin_url() . '/geodirectory-assets/place_listing.csv';
    /**
     * Filter sample post data csv file url.
     *
     * @since 1.0.0
     * @package GeoDirectory
     *
     * @param string $gd_posts_sample_csv Sample post data csv file url.
     */
    $gd_posts_sample_csv = apply_filters( 'geodir_export_posts_sample_csv', $gd_posts_sample_csv );
	
	$gd_posttypes = geodir_get_posttypes( 'array' );
	
	$gd_posttypes_option = '';
	foreach ( $gd_posttypes as $gd_posttype => $row ) {
		$gd_posttypes_option .= '<option value="' . $gd_posttype . '">' . __( $row['labels']['name'], GEODIRECTORY_TEXTDOMAIN ) . '</option>';
	}
	wp_enqueue_script( 'jquery-ui-progressbar' );
	
	$uploads = wp_upload_dir();
?>
</form>
<div class="inner_content_tab_main gd-import-export">
  <h3><?php _e( 'GD Import & Export CSV', GEODIRECTORY_TEXTDOMAIN ) ;?></h3>
  <span class="description"><?php _e( 'Import & export csv for GD listings & categories.', GEODIRECTORY_TEXTDOMAIN ) ;?></span>
  <div class="gd-content-heading">

  <?php
    ini_set('max_execution_time', 999999);
    $ini_max_execution_time_check = @ini_get( 'max_execution_time' );
    ini_restore('max_execution_time');

    if($ini_max_execution_time_check != 999999){ // only show these setting to the user if we can't change the ini setting
        ?>
	<div id="gd_ie_reqs" class="metabox-holder">
      <div class="meta-box-sortables ui-sortable">
        <div class="postbox">
          <h3 class="hndle"><span style='vertical-align:top;'><?php echo __( 'PHP Requirements for GD Import & Export CSV', GEODIRECTORY_TEXTDOMAIN );?></span></h3>
          <div class="inside">
            <span class="description"><?php echo __( 'Note: In case GD import & export csv not working for larger data then please check and configure following php settings.', GEODIRECTORY_TEXTDOMAIN );?></span>
			<table class="form-table">
				<thead>
				  <tr>
				  	<th><?php _e( 'PHP Settings', GEODIRECTORY_TEXTDOMAIN );?></th><th><?php _e( 'Current Value', GEODIRECTORY_TEXTDOMAIN );?></th><th><?php _e( 'Recommended Value', GEODIRECTORY_TEXTDOMAIN );?></th>
				  </tr>
				</thead>
				<tbody>
				  <tr>
				  	<td>max_input_time</td><td><?php echo @ini_get( 'max_input_time' );?></td><td>3000</td>
				  </tr>
				  <tr>
				  	<td>max_execution_time</td><td><?php  echo @ini_get( 'max_execution_time' );?></td><td>3000</td>
				  </tr>
				  <tr>
				  	<td>memory_limit</td><td><?php echo @ini_get( 'memory_limit' );?></td><td>256M</td>
				  </tr>
				</tbody>
		    </table>
		  </div>
		</div>
	  </div>
	</div>
	<?php }?>
	<div id="gd_ie_imposts" class="metabox-holder">
      <div class="meta-box-sortables ui-sortable">
        <div id="gd_ie_im_posts" class="postbox">
          <h3 class="hndle"><span style='vertical-align:top;'><?php echo __( 'GD Listings: Import CSV', GEODIRECTORY_TEXTDOMAIN );?></span></h3>
          <div class="inside">
            <table class="form-table">
				<tbody>
				  <tr>
					<td class="gd-imex-box">
						<div class="gd-im-choices">
						<p><input type="radio" value="update" name="gd_im_choicepost" id="gd_im_pchoice_u" /><label for="gd_im_pchoice_u"><?php _e( 'Update listing if post with post_id already exists.', GEODIRECTORY_TEXTDOMAIN );?></label></p>
						<p><input type="radio" checked="checked" value="skip" name="gd_im_choicepost" id="gd_im_pchoice_s" /><label for="gd_im_pchoice_s"><?php _e( 'Ignore listing if post with post_id already exists.', GEODIRECTORY_TEXTDOMAIN );?></label></p>
						</div>
						<div class="plupload-upload-uic hide-if-no-js" id="gd_im_postplupload-upload-ui">
							<input type="text" readonly="readonly" name="gd_im_post_file" class="gd-imex-file gd_im_post_file" id="gd_im_post" onclick="jQuery('#gd_im_postplupload-browse-button').trigger('click');" />
							<input id="gd_im_postplupload-browse-button" type="button" value="<?php echo SELECT_UPLOAD_CSV; ?>" class="gd-imex-pupload button-primary" /><input type="button" value="<?php echo esc_attr( __( 'Download Sample CSV', GEODIRECTORY_TEXTDOMAIN ) );?>" class="button-secondary" name="gd_ie_imposts_sample" id="gd_ie_imposts_sample">
						<input type="hidden" id="gd_ie_imposts_csv" value="<?php echo $gd_posts_sample_csv;?>" />
							<?php
							/**
							 * Called just after the sample CSV download link.
							 *
							 * @since 1.0.0
							 */
							do_action('geodir_sample_csv_download_link');
							?>
							<span class="ajaxnonceplu" id="ajaxnonceplu<?php echo wp_create_nonce( 'gd_im_postpluploadan' ); ?>"></span>
							<div class="filelist"></div>
						</div>
						<span id="gd_im_catupload-error" style="display:none"></span>
						<span class="description"></span>
						<div id="gd_importer" style="display:none">
							<input type="hidden" id="gd_total" value="0"/>
							<input type="hidden" id="gd_prepared" value="continue"/>
							<input type="hidden" id="gd_processed" value="0"/>
							<input type="hidden" id="gd_created" value="0"/>
							<input type="hidden" id="gd_updated" value="0"/>
							<input type="hidden" id="gd_skipped" value="0"/>
							<input type="hidden" id="gd_invalid" value="0"/>
							<input type="hidden" id="gd_invalid_addr" value="0"/>
							<input type="hidden" id="gd_images" value="0"/>
							<input type="hidden" id="gd_terminateaction" value="continue"/>
						</div>
						<div class="gd-import-progress" id="gd-import-progress" style="display:none">
							<div class="gd-import-file"><b><?php _e("Import Data Status :", GEODIRECTORY_TEXTDOMAIN);?> </b><font
									id="gd-import-done">0</font> / <font id="gd-import-total">0</font>&nbsp;( <font
									id="gd-import-perc">0%</font> )
								<div class="gd-fileprogress"></div>
							</div>
						</div>
						<div class="gd-import-msg" id="gd-import-msg" style="display:none">
							<div id="message" class="message fade"></div>
						</div>
                    	<div class="gd-imex-btns" style="display:none;">
                        	<input type="hidden" class="geodir_import_file" name="geodir_import_file" value="save"/>
                        	<input onclick="gd_imex_PrepareImport(this, 'post')" type="button" value="<?php echo CSV_IMPORT_DATA; ?>" id="gd_import_data" class="button-primary" />
                        	<input onclick="gd_imex_ContinueImport(this, 'post')" type="button" value="<?php _e( "Continue Import Data", GEODIRECTORY_TEXTDOMAIN );?>" id="gd_continue_data" class="button-primary" style="display:none"/>
                        	<input type="button" value="<?php _e("Terminate Import Data", GEODIRECTORY_TEXTDOMAIN);?>" id="gd_stop_import" class="button-primary" name="gd_stop_import" style="display:none" onclick="gd_imex_TerminateImport(this, 'post')"/>
							<div id="gd_process_data" style="display:none">
								<span class="spinner is-active" style="display:inline-block;margin:0 5px 0 5px;float:left"></span><?php _e("Wait, processing import data...", GEODIRECTORY_TEXTDOMAIN);?>
							</div>
						</div>
					</td>
				  </tr>
				</tbody>
			</table>
          </div>
        </div>
      </div>
    </div>
	<div id="gd_ie_excategs" class="metabox-holder">
	  <div class="meta-box-sortables ui-sortable">
		<div id="gd_ie_ex_posts" class="postbox">
		  <h3 class="hndle"><span style='vertical-align:top;'><?php echo __( 'GD Listings: Export CSV', GEODIRECTORY_TEXTDOMAIN );?></span></h3>
		  <div class="inside">
			<table class="form-table">
			  <tbody>
				<tr>
				  <td class="fld"><label for="gd_post_type">
					<?php _e( 'Post Type:', GEODIRECTORY_TEXTDOMAIN );?>
					</label></td>
				  <td><select name="gd_post_type" id="gd_post_type" style="min-width:140px">
					  <?php echo $gd_posttypes_option;?>
					</select></td>
				</tr>
				<tr>
				  <td class="fld" style="vertical-align: top"><label>
					<?php _e( 'Progress:', GEODIRECTORY_TEXTDOMAIN );?>
					</label></td>
				  <td><div id='gd_progressbar_box'>
					  <div id="gd_progressbar" class="gd_progressbar">
						<div class="gd-progress-label"></div>
					  </div>
					</div>
					<p style="display:inline-block">
					  <?php _e( 'Elapsed Time:', GEODIRECTORY_TEXTDOMAIN );?>
					</p>
					  
					<p id="gd_timer" class="gd_timer">00:00:00</p></td>
				</tr>
				<tr>
				  <td colspan="2"><input type="submit" value="<?php echo esc_attr( __( 'Export CSV', GEODIRECTORY_TEXTDOMAIN ) );?>" class="button-primary" name="gd_ie_exposts_submit" id="gd_ie_exposts_submit">
				  </td>
				</tr>
			  </tbody>
			</table>
		  </div>
		</div>
	  </div>
	</div>
	<div id="gd_ie_imcategs" class="metabox-holder">
      <div class="meta-box-sortables ui-sortable">
        <div id="gd_ie_imcats" class="postbox">
          <h3 class="hndle"><span style='vertical-align:top;'><?php echo __( 'GD Categories: Import CSV', GEODIRECTORY_TEXTDOMAIN );?></span></h3>
          <div class="inside">
            <table class="form-table">
				<tbody>
				  <tr>
					<td class="gd-imex-box">
						<div class="gd-im-choices">
						<p><input type="radio" value="update" name="gd_im_choicecat" id="gd_im_cchoice_u" /><label for="gd_im_cchoice_u"><?php _e( 'Update item if item with cat_id/cat_slug already exists.', GEODIRECTORY_TEXTDOMAIN );?></label></p>
						<p><input type="radio" checked="checked" value="skip" name="gd_im_choicecat" id="gd_im_cchoice_s" /><label for="gd_im_cchoice_s"><?php _e( 'Ignore item if item with cat_id/cat_slug already exists.', GEODIRECTORY_TEXTDOMAIN );?></label></p>
						</div>
						<div class="plupload-upload-uic hide-if-no-js" id="gd_im_catplupload-upload-ui">
							<input type="text" readonly="readonly" name="gd_im_cat_file" class="gd-imex-file gd_im_cat_file" id="gd_im_cat" onclick="jQuery('#gd_im_catplupload-browse-button').trigger('click');" />
							<input id="gd_im_catplupload-browse-button" type="button" value="<?php echo SELECT_UPLOAD_CSV; ?>" class="gd-imex-cupload button-primary" /><input type="button" value="<?php echo esc_attr( __( 'Download Sample CSV', GEODIRECTORY_TEXTDOMAIN ) );?>" class="button-secondary" name="gd_ie_imcats_sample" id="gd_ie_imcats_sample">
						<input type="hidden" id="gd_ie_imcats_csv" value="<?php echo $gd_cats_sample_csv;?>" />
						<?php
						/**
						 * Called just after the sample CSV download link.
						 *
						 * @since 1.0.0
                         * @package GeoDirectory
						 */
						do_action('geodir_sample_cats_csv_download_link');
						?>
							<span class="ajaxnonceplu" id="ajaxnonceplu<?php echo wp_create_nonce( 'gd_im_catpluploadan' ); ?>"></span>
							<div class="filelist"></div>
						</div>
						<span id="gd_im_catupload-error" style="display:none"></span>
						<span class="description"></span>
						<div id="gd_importer" style="display:none">
							<input type="hidden" id="gd_total" value="0"/>
							<input type="hidden" id="gd_prepared" value="continue"/>
							<input type="hidden" id="gd_processed" value="0"/>
							<input type="hidden" id="gd_created" value="0"/>
							<input type="hidden" id="gd_updated" value="0"/>
							<input type="hidden" id="gd_skipped" value="0"/>
							<input type="hidden" id="gd_invalid" value="0"/>
							<input type="hidden" id="gd_images" value="0"/>
							<input type="hidden" id="gd_terminateaction" value="continue"/>
						</div>
						<div class="gd-import-progress" id="gd-import-progress" style="display:none">
							<div class="gd-import-file"><b><?php _e("Import Data Status :", GEODIRECTORY_TEXTDOMAIN);?> </b><font
									id="gd-import-done">0</font> / <font id="gd-import-total">0</font>&nbsp;( <font
									id="gd-import-perc">0%</font> )
								<div class="gd-fileprogress"></div>
							</div>
						</div>
						<div class="gd-import-msg" id="gd-import-msg" style="display:none">
							<div id="message" class="message fade"></div>
						</div>
                    	<div class="gd-imex-btns" style="display:none;">
                        	<input type="hidden" class="geodir_import_file" name="geodir_import_file" value="save"/>
                        	<input onclick="gd_imex_PrepareImport(this, 'cat')" type="button" value="<?php echo CSV_IMPORT_DATA; ?>" id="gd_import_data" class="button-primary" />
                        	<input onclick="gd_imex_ContinueImport(this, 'cat')" type="button" value="<?php _e( "Continue Import Data", GEODIRECTORY_TEXTDOMAIN );?>" id="gd_continue_data" class="button-primary" style="display:none"/>
                        	<input type="button" value="<?php _e("Terminate Import Data", GEODIRECTORY_TEXTDOMAIN);?>" id="gd_stop_import" class="button-primary" name="gd_stop_import" style="display:none" onclick="gd_imex_TerminateImport(this, 'cat')"/>
							<div id="gd_process_data" style="display:none">
								<span class="spinner is-active" style="display:inline-block;margin:0 5px 0 5px;float:left"></span><?php _e("Wait, processing import data...", GEODIRECTORY_TEXTDOMAIN);?>
							</div>
						</div>
					</td>
				  </tr>
				</tbody>
			</table>
          </div>
        </div>
      </div>
    </div>
	<div id="gd_ie_excategs" class="metabox-holder">
      <div class="meta-box-sortables ui-sortable">
        <div id="gd_ie_ex_cats" class="postbox">
          <h3 class="hndle"><span style='vertical-align:top;'><?php echo __( 'GD Categories: Export CSV', GEODIRECTORY_TEXTDOMAIN );?></span></h3>
          <div class="inside">
            <table class="form-table">
				<tbody>
				  <tr>
					<td class="fld"><label for="gd_post_type"><?php _e( 'Post Type:', GEODIRECTORY_TEXTDOMAIN );?></label></td>
					<td><select name="gd_post_type" id="gd_post_type" style="min-width:140px"><?php echo $gd_posttypes_option;?></select></td>
				  </tr>
				  <tr>
					<td class="fld" style="vertical-align: top"><label><?php _e( 'Progress:', GEODIRECTORY_TEXTDOMAIN );?></label></td>
					<td><div id='gd_progressbar_box'><div id="gd_progressbar" class="gd_progressbar"><div class="gd-progress-label"></div></div></div><p style="display:inline-block"><?php _e( 'Elapsed Time:', GEODIRECTORY_TEXTDOMAIN );?></p>&nbsp;&nbsp;<p id="gd_timer" class="gd_timer">00:00:00</p></td>
				  </tr>
				  <tr>
					<td colspan="2">
						<input type="submit" value="<?php echo esc_attr( __( 'Export CSV', GEODIRECTORY_TEXTDOMAIN ) );?>" class="button-primary" name="gd_ie_excats_submit" id="gd_ie_excats_submit">
					</td>
				  </tr>
				</tbody>
			</table>
          </div>
        </div>
      </div>
    </div>
	<?php
	/**
	 * Allows you to add more setting to the GD > Import & Export page.
	 *
	 * Called after the last setting on the GD > Import & Export page.
	 * @since 1.4.6
     * @package GeoDirectory
	 */
	do_action( 'geodir_import_export' );
	?>
  </div>
</div>
<script type="text/javascript">
var timoutC, timoutP;

function gd_imex_PrepareImport(el, type) {
	var cont = jQuery(el).closest('.gd-imex-box');
	var gd_prepared = jQuery('#gd_prepared', cont).val();
	var uploadedFile = jQuery('#gd_im_' + type, cont).val();
	jQuery('gd-import-msg', cont).hide();
	if(gd_prepared == uploadedFile) {
		gd_imex_ContinueImport(el, type);
		jQuery('#gd_import_data', cont).attr('disabled', 'disabled');
	} else {
		jQuery.ajax({
			url: ajaxurl,
			type: "POST",
			data: 'action=geodir_import_export&task=prepare_import&_pt=' + type + '&_file=' + uploadedFile + '&_nonce=<?php echo $nonce;?>',
			dataType: 'json',
			cache: false,
			success: function(data) {
				if(typeof data == 'object') {
					if(data.error) {
						jQuery('#gd-import-msg', cont).find('#message').removeClass('updated').addClass('error').html('<p>' + data.error + '</p>');
						jQuery('#gd-import-msg', cont).show();
					} else if(!data.error && typeof data.rows != 'undefined') {
						jQuery('#gd_total', cont).val(data.rows);
						jQuery('#gd_prepared', cont).val(uploadedFile);
						jQuery('#gd_processed', cont).val('0');
						jQuery('#gd_created', cont).val('0');
						jQuery('#gd_updated', cont).val('0');
						jQuery('#gd_skipped', cont).val('0');
						jQuery('#gd_invalid', cont).val('0');
						jQuery('#gd_images', cont).val('0');
						if(type == 'post') {
							jQuery('#gd_invalid_addr', cont).val('0');
						}
						gd_imex_StartImport(el, type);
					}
				}
			},
			error: function(errorThrown) {
				console.log(errorThrown);
			}
		});
	}
}

function gd_imex_StartImport(el, type) {	
	var cont = jQuery(el).closest('.gd-imex-box');
	
	var limit = 1;
	var total = parseInt(jQuery('#gd_total', cont).val());
	var total_processed = parseInt(jQuery('#gd_processed', cont).val());
	var uploadedFile = jQuery('#gd_im_' + type, cont).val();
	var choice = jQuery('input[name="gd_im_choice'+ type +'"]:checked', cont).val();

	if (!uploadedFile) {
		jQuery('#gd_import_data', cont).removeAttr('disabled').show();
		jQuery('#gd_stop_import', cont).hide();
		jQuery('#gd_process_data', cont).hide();
		jQuery('#gd-import-progress', cont).hide();
		jQuery('.gd-fileprogress', cont).width(0);
		jQuery('#gd-import-done', cont).text('0');
		jQuery('#gd-import-total', cont).text('0');
		jQuery('#gd-import-perc', cont).text('0%');

		jQuery(cont).find('.filelist .file').remove();
		
		jQuery('#gd-import-msg', cont).find('#message').removeClass('updated').addClass('error').html("<p><?php echo esc_attr( PLZ_SELECT_CSV_FILE );?></p>");
		jQuery('#gd-import-msg', cont).show();
		
		return false;
	}

	jQuery('#gd-import-total', cont).text(total);
	jQuery('#gd_stop_import', cont).show();
	jQuery('#gd_process_data', cont).css({
		'display': 'inline-block'
	});
	jQuery('#gd-import-progress', cont).show();
	if ((parseInt(total) / 100) > 0) {
		limit = parseInt(parseInt(total) / 100);
	}
	if (limit == 1) {
		if (parseInt(total) > 50) {
			limit = 5;
		} else if (parseInt(total) > 10 && parseInt(total) < 51) {
			limit = 2;
		}
	}
	if (limit > 10) {
		limit = 10;
	}
	if (limit < 1) {
		limit = 1;
	}

	if ( parseInt(limit) > parseInt(total) )
		limit = parseInt(total);
	if (total_processed >= total) {
		jQuery('#gd_import_data', cont).removeAttr('disabled').show();
		jQuery('#gd_stop_import', cont).hide();
		jQuery('#gd_process_data', cont).hide();
		
		gd_imex_showStatusMsg(el, type);
		
		jQuery('#gd_im_' + type, cont).val('');
		jQuery('#gd_prepared', cont).val('');

		return false;
	}
	jQuery('#gd-import-msg', cont).hide();
		
	var gd_processed = parseInt(jQuery('#gd_processed', cont).val());
	var gd_created = parseInt(jQuery('#gd_created', cont).val());
	var gd_updated = parseInt(jQuery('#gd_updated', cont).val());
	var gd_skipped = parseInt(jQuery('#gd_skipped', cont).val());
	var gd_invalid = parseInt(jQuery('#gd_invalid', cont).val());
	var gd_images = parseInt(jQuery('#gd_images', cont).val());
	if (type=='post') {
		var gd_invalid_addr = parseInt(jQuery('#gd_invalid_addr', cont).val());
	}
	
	var gddata = '&limit=' + limit + '&processed=' + gd_processed;
	jQuery.ajax({
		url: ajaxurl,
		type: "POST",
		data: 'action=geodir_import_export&task=import_' + type + '&_pt=' + type + '&_file=' + uploadedFile + gddata + '&_ch=' + choice + '&_nonce=<?php echo $nonce;?>',
		dataType : 'json',
		cache: false,
		success: function (data) {
			if (typeof data == 'object') {
				if (data.error) {
					jQuery('#gd_import_data', cont).removeAttr('disabled').show();
					jQuery('#gd_stop_import', cont).hide();
					jQuery('#gd_process_data', cont).hide();
					jQuery('#gd-import-msg', cont).find('#message').removeClass('updated').addClass('error').html('<p>' + data.error + '</p>');
					jQuery('#gd-import-msg', cont).show();
				} else {
					gd_processed = gd_processed + parseInt(data.processed);
					gd_created = gd_created + parseInt(data.created);
					gd_updated = gd_updated + parseInt(data.updated);
					gd_skipped = gd_skipped + parseInt(data.skipped);
					gd_invalid = gd_invalid + parseInt(data.invalid);
					gd_images = gd_images + parseInt(data.images);
					if (type=='post' && typeof data.invalid_addr != 'undefined') {
						gd_invalid_addr = gd_invalid_addr + parseInt(data.invalid_addr);
					}

					jQuery('#gd_processed', cont).val(gd_processed);
					jQuery('#gd_created', cont).val(gd_created);
					jQuery('#gd_updated', cont).val(gd_updated);
					jQuery('#gd_skipped', cont).val(gd_skipped);
					jQuery('#gd_invalid', cont).val(gd_invalid);
					jQuery('#gd_images', cont).val(gd_images);
					if (type=='post') {
						jQuery('#gd_invalid_addr', cont).val(gd_invalid_addr);
					}

					if (parseInt(gd_processed) == parseInt(total)) {
						jQuery('#gd-import-done', cont).text(total);
						jQuery('#gd-import-perc', cont).text('100%');
						jQuery('.gd-fileprogress', cont).css({
							'width': '100%'
						});
						jQuery('#gd_im_' + type, cont).val('');
						jQuery('#gd_prepared', cont).val('');
						
						gd_imex_showStatusMsg(el, type);
						
						jQuery('#gd_stop_import', cont).hide();
					}
					if (parseInt(gd_processed) < parseInt(total)) {
						var terminate_action = jQuery('#gd_terminateaction', cont).val();
						if (terminate_action == 'continue') {
							var nTmpCnt = parseInt(total_processed) + parseInt(limit);
							nTmpCnt = nTmpCnt > total ? total : nTmpCnt;

							jQuery('#gd_processed', cont).val(nTmpCnt);

							jQuery('#gd-import-done', cont).text(nTmpCnt);
							if (parseInt(total) > 0) {
								var percentage = ((parseInt(nTmpCnt) / parseInt(total)) * 100);
								percentage = percentage > 100 ? 100 : percentage;
								jQuery('#gd-import-perc', cont).text(parseInt(percentage) + '%');
								jQuery('.gd-fileprogress', cont).css({
									'width': percentage + '%'
								});
							}
							
							if (type=='cat') {
								clearTimeout(timoutC);
								timoutC = setTimeout(function () {
									gd_imex_StartImport(el, type);
								}, 0);
							}
							if (type=='post') {
								clearTimeout(timoutP);
								timoutP = setTimeout(function () {
									gd_imex_StartImport(el, type);
								}, 0);
							}
						} else {
							jQuery('#gd_import_data', cont).hide();
							jQuery('#gd_stop_import', cont).hide();
							jQuery('#gd_process_data', cont).hide();
							jQuery('#gd_continue_data', cont).show();
							return false;
						}
					} else {
						jQuery('#gd_import_data', cont).removeAttr('disabled').show();
						jQuery('#gd_stop_import', cont).hide();
						jQuery('#gd_process_data', cont).hide();
						return false;
					}
				}
			} else {
				jQuery('#gd_import_data', cont).removeAttr('disabled').show();
				jQuery('#gd_stop_import', cont).hide();
				jQuery('#gd_process_data', cont).hide();
			}
		},
		error: function (errorThrown) {
			jQuery('#gd_import_data', cont).removeAttr('disabled').show();
			jQuery('#gd_stop_import', cont).hide();
			jQuery('#gd_process_data', cont).hide();
			console.log(errorThrown);
		}
	});
}

function gd_imex_TerminateImport(el, type) {	
	var cont = jQuery(el).closest('.gd-imex-box');
	jQuery('#gd_terminateaction', cont).val('terminate');
	jQuery('#gd_import_data', cont).hide();
	jQuery('#gd_stop_import', cont).hide();
	jQuery('#gd_process_data', cont).hide();
	jQuery('#gd_continue_data', cont).show();
}

function gd_imex_ContinueImport(el, type) {	
	var cont = jQuery(el).closest('.gd-imex-box');
	var processed = jQuery('#gd_processed', cont).val();
	var total = jQuery('#gd_total', cont).val();
	if (parseInt(processed) > parseInt(total)) {
		jQuery('#gd_stop_import', cont).hide();
	} else {
		jQuery('#gd_stop_import', cont).show();
	}
	jQuery('#gd_import_data', cont).show();
	jQuery('#gd_import_data', cont).attr('disabled', 'disabled');
	jQuery('#gd_process_data', cont).css({
		'display': 'inline-block'
	});
	jQuery('#gd_continue_data', cont).hide();
	jQuery('#gd_terminateaction', cont).val('continue');
	
	if (type=='cat') {
		clearTimeout(timoutC);
		timoutC = setTimeout(function () {
			gd_imex_StartImport(el, type);
		}, 0);
	}
	
	if (type=='post') {
		clearTimeout(timoutP);
		timoutP = setTimeout(function () {
			gd_imex_StartImport(el, type);
		}, 0);
	}
}

function gd_imex_showStatusMsg(el, type) {
	var cont = jQuery(el).closest('.gd-imex-box');
	
	var total = parseInt(jQuery('#gd_total', cont).val());
	var processed = parseInt(jQuery('#gd_processed', cont).val());
	var created = parseInt(jQuery('#gd_created', cont).val());
	var updated = parseInt(jQuery('#gd_updated', cont).val());
	var skipped = parseInt(jQuery('#gd_skipped', cont).val());
	var invalid = parseInt(jQuery('#gd_invalid', cont).val());
	var images = parseInt(jQuery('#gd_images', cont).val());
	if (type=='post') {
		var invalid_addr = parseInt(jQuery('#gd_invalid_addr', cont).val());
	}
	
	var gdMsg = '<p></p>';
	if ( processed > 0 ) {
		var msgParse = '<p><?php echo addslashes( sprintf( __( 'Total %s item(s) found.', GEODIRECTORY_TEXTDOMAIN ), '%s' ) );?></p>';
		msgParse = msgParse.replace("%s", processed);
		gdMsg += msgParse;
	}
	
	if ( updated > 0 ) {
		var msgParse = '<p><?php echo addslashes( sprintf( __( '%s / %s item(s) updated.', GEODIRECTORY_TEXTDOMAIN ), '%s', '%d' ) );?></p>';
		msgParse = msgParse.replace("%s", updated);
		msgParse = msgParse.replace("%d", processed);
		gdMsg += msgParse;
	}
	
	if ( created > 0 ) {
		var msgParse = '<p><?php echo addslashes( sprintf( __( '%s / %s item(s) added.', GEODIRECTORY_TEXTDOMAIN ), '%s', '%d' ) );?></p>';
		msgParse = msgParse.replace("%s", created);
		msgParse = msgParse.replace("%d", processed);
		gdMsg += msgParse;
	}
	
	if ( skipped > 0 ) {
		var msgParse = '<p><?php echo addslashes( sprintf( __( '%s / %s item(s) ignored due to already exists.', GEODIRECTORY_TEXTDOMAIN ), '%s', '%d' ) );?></p>';
		msgParse = msgParse.replace("%s", skipped);
		msgParse = msgParse.replace("%d", processed);
		gdMsg += msgParse;
	}
	
	if (type=='post' && invalid_addr > 0) {
		var msgParse = '<p><?php echo addslashes( sprintf( __( '%s / %s item(s) could not be added due to blank/invalid address(city, region, country, latitude, longitude).', GEODIRECTORY_TEXTDOMAIN ), '%s', '%d' ) );?></p>';
		msgParse = msgParse.replace("%s", invalid_addr);
		msgParse = msgParse.replace("%d", total);
		gdMsg += msgParse;
	}
	
	if (invalid > 0) {
		var msgParse = '<p><?php echo addslashes( sprintf( __( '%s / %s item(s) could not be added due to blank title/invalid post type.', GEODIRECTORY_TEXTDOMAIN ), '%s', '%d' ) );?></p>';
		msgParse = msgParse.replace("%s", invalid);
		msgParse = msgParse.replace("%d", total);
		gdMsg += msgParse;
	}
	
	if (images > 0) {
		gdMsg += '<p><?php echo addslashes( sprintf( CSV_TRANSFER_IMG_FOLDER, $uploads['subdir'] ) );?></p>';
	}
	gdMsg += '<p></p>';
	jQuery('#gd-import-msg', cont).find('#message').removeClass('error').addClass('updated').html(gdMsg);
	jQuery('#gd-import-msg', cont).show();
	return;
}

function gd_progressbar(el, value, label) {
	var value = parseFloat(value);
	if ( value <= 100 ) {
		jQuery(el).find('#gd_progressbar').progressbar("value",value);
		if (typeof label != 'undefined') {
			jQuery(el).find('#gd_progressbar .gd-progress-label').text(label);
		}
	}
}
			
jQuery(function(){
	var intIp;
	var intIc;
	
	jQuery(".gd-imex-pupload").click(function () {
		var $this = this;
		var $cont = jQuery($this).closest('.gd-imex-box');
		clearInterval(intIp);
		intIp = setInterval(function () {
			if (jQuery($cont).find('.gd-imex-file').val()) {
				jQuery($cont).find('.gd-imex-btns').show();
			}
		}, 1000);
	});
	
	jQuery(".gd-imex-cupload").click(function () {
		var $this = this;
		var $cont = jQuery($this).closest('.gd-imex-box');
		clearInterval(intIc);
		intIc = setInterval(function () {
			if (jQuery($cont).find('.gd-imex-file').val()) {
				jQuery($cont).find('.gd-imex-btns').show();
			}
		}, 1000);
	});
				
	jQuery('#gd_ie_imposts_sample').click(function(){
		if (jQuery('#gd_ie_imposts_csv').val() != '') {
			window.location.href = jQuery('#gd_ie_imposts_csv').val();
			return false;
		}
	});
	
	jQuery('#gd_ie_imcats_sample').click(function(){
		if (jQuery('#gd_ie_imcats_csv').val() != '') {
			window.location.href = jQuery('#gd_ie_imcats_csv').val();
			return false;
		}
	});
	
	jQuery('.gd-import-export .geodir_event_csv_download a').addClass('button-secondary');
	
	jQuery( '.gd_progressbar' ).each(function(){
		jQuery(this).progressbar({value:0});
	});
	
	var timer_posts;
	var pseconds;
	var stat_posts;
	jQuery('#gd_ie_exposts_submit').click(function(){
		pseconds = 1;
		
		var el = jQuery(this).closest('.postbox');
		var post_type = jQuery(el).find('#gd_post_type').val();
		if ( !post_type ) {
			jQuery(el).find('#gd_post_type').focus();
			return false;
		}
		window.clearInterval(timer_posts);
		
		jQuery(this).prop('disabled', true);
		
		timer_posts = window.setInterval( function() {
			jQuery(el).find(".gd_timer").gdposts_timer();
		}, 1000);
		
		gd_process_export_posts(el, post_type);
	});
	
	jQuery.fn.gdposts_timer = function() {
		pseconds++;		
		jQuery(this).text( pseconds.toString().toHMS() );
	}
	
	var timer_cats;
	var cseconds;
	var stat_cats;
	jQuery('#gd_ie_excats_submit').click(function(){
		cseconds = 1;
		
		var el = jQuery(this).closest('.postbox');
		var post_type = jQuery(el).find('#gd_post_type').val();
		if ( !post_type ) {
			jQuery(el).find('#gd_post_type').focus();
			return false;
		}
		window.clearInterval(timer_cats);
		
		jQuery(this).prop('disabled', true);
		
		timer_cats = window.setInterval( function() {
			jQuery(el).find(".gd_timer").gdcats_timer();
		}, 1000);
		
		gd_process_export_cats(el, post_type);
	});
	
	jQuery.fn.gdcats_timer = function() {
		cseconds++;		
		jQuery(this).text( cseconds.toString().toHMS() );
	}
	
	String.prototype.toHMS = function () {
		var sec_num = parseInt(this, 10); // don't forget the second param
		var hours   = Math.floor(sec_num / 3600);
		var minutes = Math.floor((sec_num - (hours * 3600)) / 60);
		var seconds = sec_num - (hours * 3600) - (minutes * 60);

		if (hours   < 10) {hours   = "0"+hours;}
		if (minutes < 10) {minutes = "0"+minutes;}
		if (seconds < 10) {seconds = "0"+seconds;}
		var time    = hours+':'+minutes+':'+seconds;
		return time;
	}
		
	function gd_process_export_posts(el, post_type) {
		gd_progressbar(el, 0, "<?php echo esc_attr( __( 'Exporting...', GEODIRECTORY_TEXTDOMAIN ) );?>");
		jQuery(el).find('#gd_timer').text('00:00:00');

		jQuery.ajax({
			url: ajaxurl,
			type: "POST",
			data: 'action=geodir_import_export&task=export_posts&_pt=' + post_type + '&_nonce=<?php echo $nonce;?>',
			dataType : 'json',
			cache: false,
			beforeSend: function (jqXHR, settings) {
				jQuery(el).find('#gd_timer').text('00:00:01');
				
				window.clearInterval(stat_posts);
				stat_posts = window.setInterval( function() {
					gd_process_stats( el, 'export_posts', post_type );
				}, 2500);
			},
			success: function( data ) {
				window.clearInterval(stat_posts);
				window.clearInterval(timer_posts);
				
				jQuery(el).find('input[type="submit"]').prop('disabled', false);
				
				if (typeof data == 'object') {
					if (typeof data.error != 'undefined' && data.error) {
						gd_progressbar(el, 0, data.error);
					} else {
						if (typeof data.total != 'undefined' && parseInt(data.total) > 0) {
							gd_progressbar(el, 100, "<?php echo esc_attr( __( 'Complete!', GEODIRECTORY_TEXTDOMAIN ) );?>");
						}
						if (typeof data.csv_file != 'undefined' && data.csv_file != '' ) {
							window.location.href = data.csv_file;
							return true;
						}
					}
				}
			},
			error: function( data ) {
				window.clearInterval(stat_posts);
				window.clearInterval(timer_posts);
				return;
			}
		});
	}
	
	function gd_process_export_cats(el, post_type) {
		gd_progressbar(el, 0, "<?php echo esc_attr( __( 'Exporting...', GEODIRECTORY_TEXTDOMAIN ) );?>");
		jQuery(el).find('#gd_timer').text('00:00:00');

		jQuery.ajax({
			url: ajaxurl,
			type: "POST",
			data: 'action=geodir_import_export&task=export_cats&_pt=' + post_type + '&_nonce=<?php echo $nonce;?>',
			dataType : 'json',
			cache: false,
			beforeSend: function (jqXHR, settings) {
				jQuery(el).find('#gd_timer').text('00:00:01');
				
				window.clearInterval(stat_cats);
				
				stat_cats = window.setInterval( function() {
					gd_process_stats( el, 'export_cats', post_type );
				}, 2500);
			},
			success: function( data ) {
				if (typeof data == 'object') {
					if (typeof data.error != 'undefined' && data.error) {
						gd_progressbar(el, 0, data.error);
					} else {
						if (typeof data.total != 'undefined' && parseInt(data.total) > 0) {
							gd_progressbar(el, 100, "<?php echo esc_attr( __( 'Complete!', GEODIRECTORY_TEXTDOMAIN ) );?>");
						}
						if (typeof data.csv_file != 'undefined' && data.csv_file != '' ) {
							window.location.href = data.csv_file;
							return true;
						}
					}
				}
			},
			error: function( data ) {
			},
			complete: function( jqXHR, textStatus  ) {
				window.clearInterval(stat_cats);
				window.clearInterval(timer_cats);
				
				jQuery(el).find('input[type="submit"]').prop('disabled', false);
			}
		});
	}
	
	function gd_process_stats(el, task, post_type) {
		jQuery.ajax({
			url: ajaxurl,
			type: "POST",
			data: 'action=geodir_import_export&task='+ task +'&_pt=' + post_type + '&_st=true&_nonce=<?php echo $nonce;?>',
			dataType : 'json',
			cache: false,
			timeout: 2490,
			success: function(data) {
				if (typeof data == 'object' && parseFloat(data.percentage) > 0) {
					gd_progressbar(el, parseFloat(data.percentage));
				}
				return true;
			},
			error: function( data ) {
				if (data.statusText != 'timeout' && data.status != 200) {
					if (task == 'export_posts') {
						window.clearInterval(stat_posts);
					} else if (task == 'export_cats') {
						window.clearInterval(stat_cats);
					}
				}
				return false;
			}
		});
		return true;
	}
});
</script>
<?php
}

/**
 * Initiate the WordPress file system and provide fallback if needed.
 *
 * @since 1.4.8
 * @package GeoDirectory
 * @return bool|string Returns the file system class on success. False on failure.
 */
function geodir_init_filesystem()
{

    if(!function_exists('get_filesystem_method')){
        require_once(ABSPATH."/wp-admin/includes/file.php");
    }
    $access_type = get_filesystem_method();
    if ($access_type === 'direct') {
        /* you can safely run request_filesystem_credentials() without any issues and don't need to worry about passing in a URL */
        $creds = request_filesystem_credentials(site_url() . '/wp-admin/', '', false, false, array());

        /* initialize the API */
        if (!WP_Filesystem($creds)) {
            /* any problems and we exit */
            //return '@@@3';
            return false;
        }

        global $wp_filesystem;
        return $wp_filesystem;
        /* do our file manipulations below */
    } elseif (defined('FTP_USER')) {
        $creds = request_filesystem_credentials(site_url() . '/wp-admin/', '', false, false, array());

        /* initialize the API */
        if (!WP_Filesystem($creds)) {
            /* any problems and we exit */
            //return '@@@33';
            return false;
        }

        global $wp_filesystem;
        //return '@@@1';
        return $wp_filesystem;

    } else {
        //return '@@@2';
        /* don't have direct write access. Prompt user with our notice */
        add_action('admin_notice', 'geodir_filesystem_notice');
        return false;
    }

}


add_action('admin_init', 'geodir_filesystem_notice');

/**
 * Output error message for file system access.
 *
 * Displays an admin message if the WordPress file system can't be automatically accessed. Called via admin_init hook.
 *
 * @since 1.4.8
 * @since 1.4.9 Added check to not run function when doing ajax calls.
 * @package GeoDirectory
 */
function geodir_filesystem_notice()
{   if ( defined( 'DOING_AJAX' ) ){return;}
    $access_type = get_filesystem_method();
    if ($access_type === 'direct') {
    } elseif (!defined('FTP_USER')) {
        ?>
        <div class="error">
            <p><?php _e('GeoDirectory does not have access to your filesystem, thing like import/export will not work. Please define your details in wp-config.php as explained here', GEODIRECTORY_TEXTDOMAIN); ?>
                <a target="_blank" href="http://codex.wordpress.org/Editing_wp-config.php#WordPress_Upgrade_Constants">http://codex.wordpress.org/Editing_wp-config.php#WordPress_Upgrade_Constants</a>
            </p>
        </div>
    <?php }
}



/**
 * Handle import/export for categories & listings.
 *
 * @since 1.4.6
 * @package GeoDirectory
 *
 * @global object $wpdb WordPress Database object.
 * @global string $plugin_prefix Geodirectory plugin table prefix.
 * @global object $current_user Current user object.
 * @global null|object $wp_filesystem WP_Filesystem object.
 * @return string Json data.
 */
function geodir_ajax_import_export() {
	global $wpdb, $plugin_prefix, $current_user, $wp_filesystem;

    // try to set higher limits for import
    @ini_set('max_input_time', 3000);
    @ini_set('max_execution_time', 3000);
    @ini_set('memory_limit', '256M');
	error_reporting(0);
	
	$json = array();
	
	if ( !current_user_can( 'manage_options' ) ) {
		wp_send_json( $json );
	}
	
	$task = isset( $_REQUEST['task'] ) ? $_REQUEST['task'] : NULL;
	$nonce = isset( $_REQUEST['_nonce'] ) ? $_REQUEST['_nonce'] : NULL;
	$stat = isset( $_REQUEST['_st'] ) ? $_REQUEST['_st'] : false;
	
	if ( !wp_verify_nonce( $nonce, 'geodir_import_export_nonce' ) ) {
		wp_send_json( $json );
	}

	$post_type = isset( $_REQUEST['_pt'] ) ? $_REQUEST['_pt'] : NULL;
	
	/*if( empty( $wp_filesystem ) ) {
		require_once( ABSPATH . '/wp-admin/includes/file.php' );
		WP_Filesystem();
		global $wp_filesystem;
	}*/

    $wp_filesystem = geodir_init_filesystem();
    if (!$wp_filesystem) {
        $json['error'] = __( 'Fail, something wrong to create csv file.', GEODIRECTORY_TEXTDOMAIN );
        wp_send_json( $json );
        exit;
    }
	
	$csv_file_dir = geodir_path_import_export( false );
	if ( !$wp_filesystem->is_dir( $csv_file_dir ) ) {
		$wp_filesystem->mkdir( $csv_file_dir, FS_CHMOD_DIR );
	}
			
	switch ( $task ) {
		case 'export_posts': {
			// WPML
			$is_wpml = geodir_is_wpml();
			if ($is_wpml) {
				global $sitepress;
				$active_lang = ICL_LANGUAGE_CODE;
				
				$sitepress->switch_lang('all', true);
			}
			// WPML
			if ( $post_type == 'gd_event' ) {
				//add_filter( 'geodir_imex_count_posts', 'geodir_imex_count_events', 10, 2 );
				add_filter( 'geodir_imex_export_posts_query', 'geodir_imex_get_events_query', 10, 2 );
			}

			$file_name = $post_type . '_' . date( 'j_n_y' );
			
			$posts_count = geodir_get_posts_count( $post_type );
			$file_url = geodir_path_import_export() . '/' . $file_name . '.csv';
			$file_path = $csv_file_dir . '/' . $file_name . '.csv';
			$file_path_temp = $csv_file_dir . '/' . $post_type . '_' . $nonce . '.csv';
			
			if ( isset( $_REQUEST['_st'] ) ) {
				$line_count = (int)geodir_import_export_line_count( $file_path_temp );
				$percentage = count( $posts_count ) > 0 && $line_count > 0 ? ceil( $line_count / $posts_count ) * 100 : 0;
				$percentage = min( $percentage, 100 );
				
				$json['percentage'] = $percentage;
				// WPML
				if ($is_wpml) {
					$sitepress->switch_lang($active_lang, true);
				}
				// WPML
				wp_send_json( $json );
			} else {				
				if ( $wp_filesystem->exists( $file_path ) ) {
					$wp_filesystem->delete( $file_path );
				}
				
				if ( !$posts_count > 0 ) {
					$json['error'] = __( 'No records to export.', GEODIRECTORY_TEXTDOMAIN );
				} else {
					$args = array( 'hide_empty' => 0 );
					$posts = geodir_imex_get_posts( $post_type );
					
					if ( !empty( $posts ) ) {
						$total_posts = count( $posts );
						$per_page = 100;
						$total_pages = ceil( $total_posts / $per_page );
						
						for ( $i = 0; $i <= $total_pages; $i++ ) {
							$save_posts = array_slice( $posts , ( $i * $per_page ), $per_page );
							
							$clear = $i == 0 ? true : false;
							geodir_save_csv_data( $file_path_temp, $save_posts, $clear );
						}
						
						if ( $wp_filesystem->exists( $file_path_temp ) ) {
							$wp_filesystem->move( $file_path_temp, $file_path, true );
						}
						
						if ( $wp_filesystem->exists( $file_path ) ) {
							$json['total'] = $posts_count;
							$json['csv_file'] = $file_url;
						} else {
							$json['error'] = __( 'Fail, something wrong to create csv file.', GEODIRECTORY_TEXTDOMAIN );
						}
					} else {
						$json['error'] = __( 'No records to export.', GEODIRECTORY_TEXTDOMAIN );
					}
				}
				// WPML
				if ($is_wpml) {
					$sitepress->switch_lang($active_lang, true);
				}
				// WPML
				wp_send_json( $json );
			}
		}
		break;
		case 'export_cats': {
			// WPML
			$is_wpml = geodir_is_wpml();
			if ($is_wpml) {
				global $sitepress;
				$active_lang = ICL_LANGUAGE_CODE;
				
				$sitepress->switch_lang('all', true);
			}
			// WPML
			$file_name = $post_type . 'category_' . date( 'j_n_y' );
			
			$terms_count = geodir_get_terms_count( $post_type );
			$file_url = geodir_path_import_export() . '/' . $file_name . '.csv';
			$file_path = $csv_file_dir . '/' . $file_name . '.csv';
			$file_path_temp = $csv_file_dir . '/' . $post_type . 'category_' . $nonce . '.csv';
			
			if ( isset( $_REQUEST['_st'] ) ) {
				$line_count = (int)geodir_import_export_line_count( $file_path_temp );
				$percentage = count( $terms_count ) > 0 && $line_count > 0 ? ceil( $line_count / $terms_count ) * 100 : 0;
				$percentage = min( $percentage, 100 );
				
				$json['percentage'] = $percentage;
				// WPML
				if ($is_wpml) {
					$sitepress->switch_lang($active_lang, true);
				}
				// WPML
				wp_send_json( $json );
			} else {				
				if ( $wp_filesystem->exists( $file_path ) ) {
					$wp_filesystem->delete( $file_path );
				}
				
				if ( !$terms_count > 0 ) {
					$json['error'] = __( 'No records to export.', GEODIRECTORY_TEXTDOMAIN );
				} else {
					$args = array( 'hide_empty' => 0 );
					$terms = geodir_imex_get_terms( $post_type );
					
					if ( !empty( $terms ) ) {
						$total_terms = count( $terms );
						$per_page = 50;
						$total_pages = ceil( $total_terms / $per_page );
						
						for ( $i = 0; $i <= $total_pages; $i++ ) {
							$save_terms = array_slice( $terms , ( $i * $per_page ), $per_page );
							
							$clear = $i == 0 ? true : false;
							geodir_save_csv_data( $file_path_temp, $save_terms, $clear );
						}
						
						if ( $wp_filesystem->exists( $file_path_temp ) ) {
							$wp_filesystem->move( $file_path_temp, $file_path, true );
						}
						
						if ( $wp_filesystem->exists( $file_path ) ) {
							$json['total'] = $terms_count;
							$json['csv_file'] = $file_url;
						} else {
							$json['error'] = __( 'Fail, something wrong to create csv file.', GEODIRECTORY_TEXTDOMAIN );
						}
					} else {
						$json['error'] = __( 'No records to export.', GEODIRECTORY_TEXTDOMAIN );
					}
				}
				// WPML
				if ($is_wpml) {
					$sitepress->switch_lang($active_lang, true);
				}
				// WPML
				wp_send_json( $json );
			}
		}
		break;
		case 'prepare_import':
		case 'import_cat':
		case 'import_post': {
			// WPML
			$is_wpml = geodir_is_wpml();
			if ($is_wpml) {
				global $sitepress;
				$active_lang = ICL_LANGUAGE_CODE;
			}
			// WPML
			
			ini_set( 'auto_detect_line_endings', true );
			
			$uploads = wp_upload_dir();
			$uploads_dir = $uploads['path'];
			$uploads_subdir = $uploads['subdir'];
			
			$csv_file = isset( $_POST['_file'] ) ? $_POST['_file'] : NULL;
			$import_choice = isset( $_REQUEST['_ch'] ) ? $_REQUEST['_ch'] : 'skip';
			
			$csv_file_arr = explode( '/', $csv_file );
			$csv_filename = end( $csv_file_arr );
			$target_path = $uploads_dir . '/temp_' . $current_user->data->ID . '/' . $csv_filename;
			
			$json['file'] = $csv_file;
			$json['error'] = __( 'The uploaded file is not a valid csv file. Please try again.', GEODIRECTORY_TEXTDOMAIN );
			
			if ( $csv_file && $wp_filesystem->is_file( $target_path ) && $wp_filesystem->exists( $target_path ) ) {
				$wp_filetype = wp_check_filetype_and_ext( $target_path, $csv_filename );
				
				if (!empty($wp_filetype) && isset($wp_filetype['ext']) && strtolower($wp_filetype['ext']) == 'csv') {
					$json['error'] = NULL;
					$json['rows'] = 0;
					
					if ( ( $handle = fopen($target_path, "r" ) ) !== FALSE ) {
						while ( ( $data = fgetcsv( $handle, 100000, "," ) ) !== FALSE ) {
							if ( !empty( $data ) ) {
								$file[] = $data;
							}
						}
						fclose($handle);
					}
	
					$json['rows'] = (!empty($file) && count($file) > 1) ? count($file) - 1 : 0;
					
					if (!$json['rows'] > 0) {
						$json['error'] = __('No data found in csv file.', GEODIRECTORY_TEXTDOMAIN);
					}
				} else {
					wp_send_json( $json );
				}
			} else {
				wp_send_json( $json );
			}
			
			if ( $task == 'prepare_import' || !empty( $json['error'] ) ) {
				wp_send_json( $json );
			}
			
			$total = $json['rows'];
			$limit = isset($_POST['limit']) ? (int)$_POST['limit'] : 1;
			$processed = isset($_POST['processed']) ? (int)$_POST['processed'] : 0;
			
			$count = $limit;
			$requested_limit = $limit;
			
			if ($count < $total) {
				$count = $processed + $count;
				if ($count > $total) {
					$count = $total;
				}
			} else {
				$count = $total;
			}
			
			$created = 0;
			$updated = 0;
			$skipped = 0;
			$invalid = 0;
			$invalid_addr = 0;
			$images = 0;
			
			$invalid_title = 0;
			$customKeyarray = array();
			$gd_post_info = array();
			$post_location = array();
			$countpost = 0;
			
			$post_types = geodir_get_posttypes();
	
			if ( $task == 'import_cat' ) {				
				if (!empty($file)) {
					$columns = isset($file[0]) ? $file[0] : NULL;
					
					if (empty($columns) || (!empty($columns) && $columns[0] == '')) {
						$json['error'] = CSV_INVAILD_FILE;
						wp_send_json( $json );
					}
					
					for ($i = 1; $i <= $limit; $i++) {
						$index = $processed + $i;
						
						if (isset($file[$index])) {
							$row = $file[$index];
							$row = array_map( 'trim', $row );
							$row = array_map( 'utf8_encode', $row );
							
							$cat_id = '';
							$cat_name = '';
							$cat_slug = '';
							$cat_posttype = '';
							$cat_parent = '';
							$cat_description = '';
							$cat_top_description = '';
							$cat_image = '';
							$cat_icon = '';
							$cat_language = '';
							
							$c = 0;
							foreach ($columns as $column ) {
								if ( $column == 'cat_id' ) {
									$cat_id = (int)$row[$c];
								} else if ( $column == 'cat_name' ) {
									$cat_name = $row[$c];
								} else if ( $column == 'cat_slug' ) {
									$cat_slug = $row[$c];
								} else if ( $column == 'cat_posttype' ) {
									$cat_posttype = $row[$c];
								} else if ( $column == 'cat_parent' ) {
									$cat_parent = trim($row[$c]);
								} else if ( $column == 'cat_description' ) {
									$cat_description = $row[$c];
								} else if ( $column == 'cat_top_description' ) {
									$cat_top_description = $row[$c];
								} else if ( $column == 'cat_image' ) {
									$cat_image = $row[$c];
								} else if ( $column == 'cat_icon' ) {
									$cat_icon = $row[$c];
								}
								// WPML
								if ($is_wpml && $column == 'cat_language') {
									$cat_language = strtolower(trim($row[$c]));
								}
								// WPML
								$c++;
							}
							
							if ( $cat_name == '' || !in_array( $cat_posttype, $post_types ) ) {
								$invalid++;
								continue;
							}
							
							// WPML
							if ($is_wpml && $cat_language != '') {
								$sitepress->switch_lang($cat_language, true);
							}
							// WPML
														
							$term_data = array();
							$term_data['name'] = $cat_name;
							$term_data['slug'] = $cat_slug;
							$term_data['description'] = $cat_description;
							$term_data['top_description'] = $cat_top_description;
							$term_data['image'] = $cat_image != '' ? basename( $cat_image ) : '';
							$term_data['icon'] = $cat_icon != '' ? basename( $cat_icon ) : '';
							
							$term_data = array_map( 'utf8_encode', $term_data );
							
							$taxonomy = $cat_posttype . 'category';
							
							$term_data['taxonomy'] = $taxonomy;

							$term_parent_id = 0;
							if ($cat_parent != "" || (int)$cat_parent > 0) {
								$term_parent = '';
								
								if ( $term_parent = get_term_by( 'name', $cat_parent, $taxonomy ) ) {
									$term_parent = $term_parent;
								} else if ( $term_parent = get_term_by( 'slug', $cat_parent, $taxonomy ) ) {
									$term_parent = $term_parent;
								} else if ( $term_parent = get_term_by( 'id', $cat_parent, $taxonomy ) ) {
									$term_parent = $term_parent;
								} else {
									$term_parent_data = array();
									$term_parent_data['name'] = $cat_parent;											
									$term_parent_data = array_map( 'utf8_encode', $term_parent_data );										
									$term_parent_data['taxonomy'] = $taxonomy;
									
									$term_parent_id = (int)geodir_imex_insert_term( $taxonomy, $term_parent_data );
								}
								
								if ( !empty( $term_parent ) && !is_wp_error( $term_parent ) ) {
									$term_parent_id = (int)$term_parent->term_id;
								}
							}
							$term_data['parent'] = (int)$term_parent_id;
						
							$term_id = NULL;
							if ( $import_choice == 'update' ) {
								if ( $cat_id > 0 && $term = (array)term_exists( $cat_id, $taxonomy ) ) {
									$term_data['term_id'] = $term['term_id'];
									
									if ( $term_id = geodir_imex_update_term( $taxonomy, $term_data ) ) {
										$updated++;
									} else {
										$invalid++;
									}
								} else if ( $term_data['slug'] != '' && $term = (array)term_exists( $term_data['slug'], $taxonomy ) ) {
									$term_data['term_id'] = $term['term_id'];
									
									if ( $term_id = geodir_imex_update_term( $taxonomy, $term_data ) ) {
										$updated++;
									} else {
										$invalid++;
									}
								} else {
									if ( $term_id = geodir_imex_insert_term( $taxonomy, $term_data ) ) {
										$created++;
									} else {
										$invalid++;
									}
								}
							} else if ( $import_choice == 'skip' ) {
								if ( $cat_id > 0 && $term = (array)term_exists( $cat_id, $taxonomy ) ) {
									$skipped++;
								} else if ( $term_data['slug'] != '' && $term = (array)term_exists( $term_data['slug'], $taxonomy ) ) {
									$skipped++;
								} else {
									if ( $term_id = geodir_imex_insert_term( $taxonomy, $term_data ) ) {										
										$created++;
									} else {
										$invalid++;
									}
								}
							} else {
								$invalid++;
							}
							
							if ( $term_id ) {
								if ( isset( $term_data['top_description'] ) ) {
									update_tax_meta( $term_id, 'ct_cat_top_desc', $term_data['top_description'], $cat_posttype );
								}
			
								$attachment = false;
								if ( isset( $term_data['image'] ) && $term_data['image'] != '' ) {
									$cat_image = geodir_get_default_catimage( $term_id, $cat_posttype );
									$cat_image = !empty( $cat_image ) && isset( $cat_image['src'] ) ? $cat_image['src'] : '';
									
									if ( basename($cat_image) != $term_data['image'] ) {
										$attachment = true;
										update_tax_meta( $term_id, 'ct_cat_default_img', array( 'id' => 'image', 'src' => $uploads['url'] . '/' . $term_data['image'] ), $cat_posttype );
									}
								}
								
								if ( isset( $term_data['icon'] ) && $term_data['icon'] != '' ) {
									$cat_icon = get_tax_meta( $term_id, 'ct_cat_icon', false, $cat_posttype );
									$cat_icon = !empty( $cat_icon ) && isset( $cat_icon['src'] ) ? $cat_icon['src'] : '';
										
									if ( basename($cat_icon) != $term_data['icon'] ) {
										$attachment = true;
										update_tax_meta( $term_id, 'ct_cat_icon', array( 'id' => 'icon', 'src' => $uploads['url'] . '/' . $term_data['icon'] ), $cat_posttype );
									}
								}
								
								if ( $attachment ) {
									$images++;
								}
							}
							
							// WPML
							if ($is_wpml && $cat_language != '') {
								$sitepress->switch_lang($active_lang, true);
							}
							// WPML
						}
					}
				}
				
				$json = array();
				$json['processed'] = $limit;
				$json['created'] = $created;
				$json['updated'] = $updated;
				$json['skipped'] = $skipped;
				$json['invalid'] = $invalid;
				$json['images'] = $images;
				
				wp_send_json( $json );
			} else if ( $task == 'import_post' ) {				
				if (!empty($file)) {
					$wp_post_statuses = get_post_statuses(); // All of the WordPress supported post statuses.
					$default_status = 'publish';
					$current_date = date_i18n( 'Y-m-d', time() );
					
					$columns = isset($file[0]) ? $file[0] : NULL;
					
					if (empty($columns) || (!empty($columns) && $columns[0] == '')) {
						$json['error'] = CSV_INVAILD_FILE;
						wp_send_json( $json );
					}
					
					for ($i = 1; $i <= $limit; $i++) {
						$index = $processed + $i;
						$gd_post = array();
						
						if (isset($file[$index])) {
							$row = $file[$index];
							$row = array_map( 'trim', $row );
							$row = array_map( 'utf8_encode', $row );
							$row = array_map( 'addslashes_gpc', $row );
							
							$post_id = '';
							$post_title = '';
							$post_author = '';
							$post_content = '';
							$post_category_arr = array();
							$post_tags = array();
							$post_type = '';
							$post_status = '';
							$geodir_video = '';
							$post_address = '';
							$post_city = '';
							$post_region = '';
							$post_country = '';
							$post_zip = '';
							$post_latitude = '';
							$post_longitude = '';
							$geodir_timing = '';
							$geodir_contact = '';
							$geodir_email = '';
							$geodir_website = '';
							$geodir_twitter = '';
							$geodir_facebook = '';
							$geodir_twitter = '';
							$post_images = array();
							
							$expire_date = 'Never';
							
							$language = '';
							$original_post_id = '';
														
							$c = 0;
							foreach ($columns as $column ) {
								$gd_post[$column] = $row[$c];
								
								if ( $column == 'post_id' ) {
									$post_id = $row[$c];
								} else if ( $column == 'post_title' ) {
									$post_title = $row[$c];
								} else if ( $column == 'post_author' ) {
									$post_author = $row[$c];
								} else if ( $column == 'post_content' ) {
									$post_content = $row[$c];
								} else if ( $column == 'post_category' && $row[$c] != '' ) {
									$post_category_arr = explode( ',', $row[$c] );
								} else if ( $column == 'post_tags' && $row[$c] != '' ) {
									$post_tags = explode( ',', $row[$c] );
								} else if ( $column == 'post_type' ) {
									$post_type = $row[$c];
								} else if ( $column == 'post_status' ) {
									$post_status = sanitize_key( $row[$c] );
								} else if ( $column == 'is_featured' ) {
									$is_featured = (int)$row[$c];
								} else if ( $column == 'geodir_video' ) {
									$geodir_video = $row[$c];
								} else if ( $column == 'post_address' ) {
									$post_address = $row[$c];
								} else if ( $column == 'post_city' ) {
									$post_city = $row[$c];
								} else if ( $column == 'post_region' ) {
									$post_region = $row[$c];
								} else if ( $column == 'post_country' ) {
									$post_country = $row[$c];
								} else if ( $column == 'post_zip' ) {
									$post_zip = $row[$c];
								} else if ( $column == 'post_latitude' ) {
									$post_latitude = $row[$c];
								} else if ( $column == 'post_longitude' ) {
									$post_longitude = $row[$c];
								} else if ( $column == 'geodir_timing' ) {
									$geodir_timing = $row[$c];
								} else if ( $column == 'geodir_contact' ) {
									$geodir_contact = $row[$c];
								} else if ( $column == 'geodir_email' ) {
									$geodir_email = $row[$c];
								} else if ( $column == 'geodir_website' ) {
									$geodir_website = $row[$c];
								} else if ( $column == 'geodir_twitter' ) {
									$geodir_twitter = $row[$c];
								} else if ( $column == 'geodir_facebook' ) {
									$geodir_facebook = $row[$c];
								} else if ( $column == 'geodir_twitter' ) {
									$geodir_twitter = $row[$c];
								} else if ( $column == 'IMAGE' && !empty( $row[$c] ) && $row[$c] != '' ) {
									$post_images[] = $row[$c];
								} else if ( $column == 'alive_days' && (int)$row[$c] > 0 ) {
									$expire_date = date_i18n( 'Y-m-d', strtotime( $current_date . '+' . (int)$row[$c] . ' days' ) );
								} else if ( $column == 'expire_date' && $row[$c] != '' && strtolower($row[$c]) != 'never' ) {
									$row[$c] = str_replace('/', '-', $row[$c]);
									$expire_date = date_i18n( 'Y-m-d', strtotime( $row[$c] ) );
								}
								// WPML
								if ($is_wpml) {
									if ($column == 'language') {
										$language = strtolower(trim($row[$c]));
									} else if ($column == 'original_post_id') {
										$original_post_id = (int)$row[$c];
									}
								}
								// WPML
								$c++;
							}
							
							// WPML
							if ($is_wpml && $language != '') {
								$sitepress->switch_lang($language, true);
							}
							// WPML

							$gd_post['IMAGE'] = $post_images;
							
							$post_status = !empty( $post_status ) ? sanitize_key( $post_status ) : $default_status;
							$post_status = !empty( $wp_post_statuses ) && !isset( $wp_post_statuses[$post_status] ) ? $default_status : $post_status;
																												
							$valid = true;
							
							if ( $post_title == '' || !in_array( $post_type, $post_types ) ) {
								$invalid++;
								$valid = false;
							}
							
							$location_allowed = function_exists( 'geodir_cpt_no_location' ) && geodir_cpt_no_location( $post_type ) ? false : true;
							if ( $location_allowed ) {
								$location_result = geodir_get_default_location();
								if ( $post_address == '' || $post_city == '' || $post_region == '' || $post_country == '' || $post_latitude == '' || $post_longitude == '' ) {
									$invalid_addr++;
									$valid = false;
								} else if ( !empty( $location_result ) && $location_result->location_id == 0 ) {
									if ( ( strtolower( $post_city ) != strtolower( $location_result->city ) ) || ( strtolower( $post_region ) != strtolower( $location_result->region ) ) || (strtolower( $post_country ) != strtolower( $location_result->country ) ) ) {
										$invalid_addr++;
										$valid = false;
									} else {
										if (!function_exists('geodir_location_plugin_activated')) {
											$gd_post['post_locations'] = '[' . $location_result->city_slug . '],[' . $location_result->region_slug . '],[' . $location_result->country_slug . ']'; // Set the default location when location manager not activated.
										}
									}
								}
							}
							
							if ( !$valid ) {
								continue;
							}
														
							$cat_taxonomy = $post_type . 'category';
							$tags_taxonomy = $post_type . '_tags';
							
							$post_category = array();
							if ( !empty( $post_category_arr ) ) {
								foreach ( $post_category_arr as $value ) {
									$category_name = wp_kses_normalize_entities( trim( $value ) );
									
									if ( $category_name != '' ) {
										$term_category = array();
										
										if ( $term = get_term_by( 'name', $category_name, $cat_taxonomy ) ) {
											$term_category = $term;
										} else if ( $term = get_term_by( 'slug', $category_name, $cat_taxonomy ) ) {
											$term_category = $term;
										} else {
											$term_data = array();
											$term_data['name'] = $category_name;											
											$term_data = array_map( 'utf8_encode', $term_data );										
											$term_data['taxonomy'] = $cat_taxonomy;
											
											$term_id = geodir_imex_insert_term( $cat_taxonomy, $term_data );
											if ( $term_id ) {
												$term_category = get_term( $term_id, $cat_taxonomy );
											}
										}
										
										if ( !empty( $term_category ) && !is_wp_error( $term_category ) ) {
											//$post_category[] = $term_category->slug;
											$post_category[] = intval($term_category->term_id);
										}
									}
								}
							}
							
							$save_post = array();
							$save_post['post_title'] = $post_title;
							$save_post['post_content'] = $post_content;
							$save_post['post_type'] = $post_type;
							$save_post['post_author'] = $post_author;
							$save_post['post_status'] = $post_status;
							$save_post['post_category'] = $post_category;
							$save_post['post_tags'] = $post_tags;
														
							$saved_post_id = NULL;
							if ( $import_choice == 'update' ) {
								if ( $post_id > 0 && get_post( $post_id ) ) {
									$save_post['ID'] = $post_id;
									
									if ( wp_update_post( $save_post ) ) {
										$saved_post_id = $post_id;
										$updated++;	
									}
								} else {
									if ( $saved_post_id = wp_insert_post( $save_post ) ) {
										$created++;	
									}
								}
								
								if ( !$saved_post_id > 0 ) {
									$invalid++;
								}
							} else if ( $import_choice == 'skip' ) {
								if ( $post_id > 0 && get_post( $post_id ) ) {
									$skipped++;	
								} else {
									if ( $saved_post_id = wp_insert_post( $save_post ) ) {
										$created++;	
									} else {
										$invalid++;
									}
								}
							} else {
								$invalid++;
							}
							
							if ( (int)$saved_post_id > 0 ) {							
								// WPML
								if ($is_wpml && $original_post_id > 0 && $language != '') {
									$wpml_post_type = 'post_' . $post_type;
									$source_language = geodir_get_language_for_element( $original_post_id, $wpml_post_type );
									$source_language = $source_language != '' ? $source_language : $sitepress->get_default_language();

									$trid = $sitepress->get_element_trid( $original_post_id, $wpml_post_type );
									
									$sitepress->set_element_language_details( $saved_post_id, $wpml_post_type, $trid, $language, $source_language );
								}
								// WPML
								$gd_post_info = geodir_get_post_info( $saved_post_id );
								
								$gd_post['post_id'] = $saved_post_id;
								$gd_post['ID'] = $saved_post_id;
								$gd_post['post_tags'] = $post_tags;
								$gd_post['post_title'] = $post_title;
								$gd_post['post_status'] = $post_status;
								$gd_post['submit_time'] = time();
								$gd_post['submit_ip'] = $_SERVER['REMOTE_ADDR'];
													
								// post location
								$post_location_id = 0;
								if ( $location_allowed && !empty( $location_result ) && $location_result->location_id > 0 ) {
									$post_location_info = array(
																'city' => $post_city,
																'region' => $post_region,
																'country' => $post_country,
																'geo_lat' => $post_latitude,
																'geo_lng' => $post_longitude
															);
									if ( $location_id = (int)geodir_add_new_location( $post_location_info ) ) {
										$post_location_id = $location_id;
									}
								}
								$gd_post['post_location_id'] = $post_location_id;
								
								// post package info
								$package_id = isset( $gd_post['package_id'] ) && !empty( $gd_post['package_id'] ) ? (int)$gd_post['package_id'] : 0;
								if (!$package_id && !empty($gd_post_info) && isset($gd_post_info->package_id) && $gd_post_info->package_id) {
									$package_id = $gd_post_info->package_id;
								}
								
								$package_info = array();
								if ($package_id && function_exists('geodir_get_package_info_by_id')) {
									$package_info = (array)geodir_get_package_info_by_id($package_id);
									
									if (!(!empty($package_info) && isset($package_info['post_type']) && $package_info['post_type'] == $post_type)) {
										$package_info = array();
									}
								}
								
								if (empty($package_info)) {
									$package_info = (array)geodir_post_package_info( array(), '', $post_type );
								}
								 
								if (!empty($package_info))	 {
									$package_id = $package_info['pid'];
									
									if (isset($gd_post['alive_days']) || isset($gd_post['expire_date'])) {
										$gd_post['expire_date'] = $expire_date;
									} else {
										if ( isset( $package_info['days'] ) && (int)$package_info['days'] > 0 ) {
											$gd_post['alive_days'] = (int)$package_info['days'];
											$gd_post['expire_date'] = date_i18n( 'Y-m-d', strtotime( $current_date . '+' . (int)$package_info['days'] . ' days' ) );
										} else {
											$gd_post['expire_date'] = 'Never';
										}
									}
									
									$gd_post['package_id'] = $package_id;
								}

								$table = $plugin_prefix . $post_type . '_detail';
								
								if ($post_type == 'gd_event') {
									$gd_post = geodir_imex_process_event_data($gd_post);
								}
								
								if (isset($gd_post['post_id'])) {
									unset($gd_post['post_id']);
								}
								
								// Export franchise fields
								$is_franchise_active = is_plugin_active( 'geodir_franchise/geodir_franchise.php' ) && geodir_franchise_enabled( $post_type ) ? true : false;
								if ($is_franchise_active) {
									if ( isset( $gd_post['gd_is_franchise'] ) && (int)$gd_post['gd_is_franchise'] == 1 ) {
										$gd_franchise_lock = array();
										
										if ( isset( $gd_post['gd_franchise_lock'] ) ) {
											$gd_franchise_lock = str_replace(" ", "", $gd_post['gd_franchise_lock'] );
											$gd_franchise_lock = trim( $gd_franchise_lock );
											$gd_franchise_lock = explode( ",", $gd_franchise_lock );
										}
										
										update_post_meta( $saved_post_id, 'gd_is_franchise', 1 );
										update_post_meta( $saved_post_id, 'gd_franchise_lock', $gd_franchise_lock );
									} else {
										if ( isset( $gd_post['franchise'] ) && (int)$gd_post['franchise'] > 0 && geodir_franchise_check( (int)$gd_post['franchise'] ) ) {
											geodir_save_post_meta( $saved_post_id, 'franchise', (int)$gd_post['franchise'] );
										}
									}
								}
								
								if (!empty($save_post['post_category']) && is_array($save_post['post_category'])) {
									$save_post['post_category'] = array_unique( array_map( 'intval', $save_post['post_category'] ) );
									$gd_post[$cat_taxonomy] = $save_post['post_category'];
								}
								
								// Save post info
								geodir_save_post_info( $saved_post_id, $gd_post );	

								// post taxonomies
								if ( !empty( $save_post['post_category'] ) ) {
									wp_set_object_terms( $saved_post_id, $save_post['post_category'], $cat_taxonomy );
									
									$post_default_category = isset( $save_post['post_default_category'] ) ? $save_post['post_default_category'] : '';
									$post_category_str = isset( $save_post['post_category_str'] ) ? $save_post['post_category_str'] : '';
                            		
									geodir_set_postcat_structure( $saved_post_id, $cat_taxonomy, $post_default_category, $post_category_str );
								}
								
								if ( !empty( $save_post['post_tags'] ) ) {
									wp_set_object_terms( $saved_post_id, $save_post['post_tags'], $tags_taxonomy );
								}		

								// Post images
								if ( !empty( $post_images ) ) {
									$post_images = array_unique($post_images);
									
									$old_post_images_arr = array();
									$saved_post_images_arr = array();
									
									$order = 1;
									
									$old_post_images = geodir_get_images( $saved_post_id );
									if (!empty($old_post_images)) {
										foreach( $old_post_images as $old_post_image ) {
											if (!empty($old_post_image) && isset($old_post_image->file) && $old_post_image->file != '') {
												$old_post_images_arr[] = $old_post_image->file;
											}
										}
									}
									
									foreach ( $post_images as $post_image ) {
										$image_name = basename( $post_image );
										$saved_post_images_arr[] = $image_name;
										
										if (!empty($old_post_images_arr) && in_array( $image_name, $old_post_images_arr) ) {
											continue; // Skip if image already exists.
										}
										
										$image_name_parts = explode( '.', $image_name );
										array_pop( $image_name_parts );
										$proper_image_name = implode( '.', $image_name_parts );
										
										$arr_file_type = wp_check_filetype( $image_name );
										
										if ( !empty( $arr_file_type ) ) {
											$uploaded_file_type = $arr_file_type['type'];
											
											$attachment = array();
											$attachment['post_id'] = $saved_post_id;
											$attachment['title'] = $proper_image_name;
											$attachment['content'] = '';
											$attachment['file'] = $uploads_subdir . '/' . $image_name;
											$attachment['mime_type'] = $uploaded_file_type;
											$attachment['menu_order'] = $order;
											$attachment['is_featured'] = 0;

											$attachment_set = '';
											foreach ( $attachment as $key => $val ) {
												if ( $val != '' ) {
													$attachment_set .= $key . " = '" . $val . "', ";
												}
											}
											$attachment_set = trim( $attachment_set, ", " );
																						
											// Add new attachment
											$wpdb->query( "INSERT INTO " . GEODIR_ATTACHMENT_TABLE . " SET " . $attachment_set );
																						
											$order++;
										}
									}
									
									$saved_post_images_sql = !empty($saved_post_images_arr) ? " AND ( file NOT LIKE '%/" . implode("' AND file NOT LIKE '%/",  $saved_post_images_arr) . "' )" : '';
									// Remove previous attachment
									$wpdb->query( "DELETE FROM " . GEODIR_ATTACHMENT_TABLE . " WHERE post_id = " . (int)$saved_post_id . " " . $saved_post_images_sql );
									
									if ( !empty( $saved_post_images_arr ) ) {
										$menu_order = 1;
										
										foreach ( $saved_post_images_arr as $img_name ) {
											$wpdb->query( $wpdb->prepare( "UPDATE " . GEODIR_ATTACHMENT_TABLE . " SET menu_order = %d WHERE post_id =%d AND file LIKE %s", array( $menu_order, $saved_post_id, '%/' . $img_name ) ) );
											
											if( $menu_order == 1 ) {
												if ( $featured_image = $wpdb->get_var( $wpdb->prepare( "SELECT file FROM " . GEODIR_ATTACHMENT_TABLE . " WHERE post_id =%d AND file LIKE %s", array( $saved_post_id, '%/' . $img_name ) ) ) ) {
													$wpdb->query( $wpdb->prepare( "UPDATE " . $table . " SET featured_image = %s WHERE post_id =%d", array( $featured_image, $saved_post_id ) ) );
												}
											}
											$menu_order++;
										}
									}
									
									if ( $order > 1 ) {
										$images++;
									}
								}
								
								/** This action is documented in geodirectory-functions/post-functions.php */
                    			do_action( 'geodir_after_save_listing', $saved_post_id, $gd_post );
								
								if (isset($is_featured)) {
									geodir_save_post_meta($saved_post_id, 'is_featured', $is_featured);
								}
								if (isset($gd_post['expire_date'])) {
									geodir_save_post_meta($saved_post_id, 'expire_date', $gd_post['expire_date']);
								}
							}
							
							// WPML
							if ($is_wpml && $language != '') {
								$sitepress->switch_lang($active_lang, true);
							}
							// WPML
						}
					}
				}
				
				$json = array();
				$json['processed'] = $limit;
				$json['created'] = $created;
				$json['updated'] = $updated;
				$json['skipped'] = $skipped;
				$json['invalid'] = $invalid;
				$json['invalid_addr'] = $invalid_addr;
				$json['images'] = $images;
				
				wp_send_json( $json );
			}
		}
		break;
	}
	echo '0';
	wp_die();
}

/**
 * Create new the post term.
 *
 * @since 1.4.6
 * @package GeoDirectory
 *
 * @param string $taxonomy Post taxonomy.
 * @param array $term_data {
 *    Attributes of term data.
 *
 *    @type string $name Term name.
 *    @type string $slug Term slug.
 *    @type string $description Term description.
 *    @type string $top_description Term top description.
 *    @type string $image Default Term image.
 *    @type string $icon Default Term icon.
 *    @type string $taxonomy Term taxonomy.
 *    @type int $parent Term parent ID.
 *
 * }
 * @return int|bool Term id when success, false when fail.
 */
function geodir_imex_insert_term( $taxonomy, $term_data ) {
	if ( empty( $taxonomy ) || empty( $term_data ) ) {
		return false;
	}
	
	$term = isset( $term_data['name'] ) && !empty( $term_data['name'] ) ? $term_data['name'] : '';
	$args = array();
	$args['description'] = isset( $term_data['description'] ) ? $term_data['description'] : '';
	$args['slug'] = isset( $term_data['slug'] ) ? $term_data['slug'] : '';
	$args['parent'] = isset( $term_data['parent'] ) ? (int)$term_data['parent'] : '';
	
	if ( ( !empty( $args['slug'] ) && term_exists( $args['slug'], $taxonomy ) ) || empty( $args['slug'] ) ) {
		$term_args = array_merge( $term_data, $args );
		$defaults = array( 'alias_of' => '', 'description' => '', 'parent' => 0, 'slug' => '');
		$term_args = wp_parse_args( $term_args, $defaults );
		$term_args = sanitize_term( $term_args, $taxonomy, 'db' );
		$args['slug'] = wp_unique_term_slug( $args['slug'], (object)$term_args );
	}
	
    if( !empty( $term ) ) {
		$result = wp_insert_term( $term, $taxonomy, $args );
        if( !is_wp_error( $result ) ) {
            return $term_id = isset( $result['term_id'] ) ? $result['term_id'] : 0;
        }
    }
	
	return false;
}

/**
 * Update the post term.
 *
 * @since 1.4.6
 * @package GeoDirectory
 *
 * @param string $taxonomy Post taxonomy.
 * @param array $term_data {
 *    Attributes of term data.
 *
 *    @type string $term_id Term ID.
 *    @type string $name Term name.
 *    @type string $slug Term slug.
 *    @type string $description Term description.
 *    @type string $top_description Term top description.
 *    @type string $image Default Term image.
 *    @type string $icon Default Term icon.
 *    @type string $taxonomy Term taxonomy.
 *    @type int $parent Term parent ID.
 *
 * }
 * @return int|bool Term id when success, false when fail.
 */
function geodir_imex_update_term( $taxonomy, $term_data ) {
	if ( empty( $taxonomy ) || empty( $term_data ) ) {
		return false;
	}
	
	$term = isset( $term_data['name'] ) && !empty( $term_data['name'] ) ? $term_data['name'] : '';
	$term_id = isset( $term_data['term_id'] ) && !empty( $term_data['term_id'] ) ? $term_data['term_id'] : 0;
	
	$args = array();
	$args['description'] = isset( $term_data['description'] ) ? $term_data['description'] : '';
	$args['slug'] = isset( $term_data['slug'] ) ? $term_data['slug'] : '';
	$args['parent'] = isset( $term_data['parent'] ) ? (int)$term_data['parent'] : '';
	
	if ( $term_id > 0 && $term_info = (array)get_term( $term_id, $taxonomy ) ) {
		$term_data['term_id'] = $term_info['term_id'];
		
		$result = wp_update_term( $term_data['term_id'], $taxonomy, $term_data );
		
		if( !is_wp_error( $result ) ) {
            return $term_id = isset( $result['term_id'] ) ? $result['term_id'] : 0;
        }
	} else if ( $term_data['slug'] != '' && $term_info = (array)term_exists( $term_data['slug'], $taxonomy ) ) {
		$term_data['term_id'] = $term_info['term_id'];
		
		$result = wp_update_term( $term_data['term_id'], $taxonomy, $term_data );
		
		if( !is_wp_error( $result ) ) {
            return $term_id = isset( $result['term_id'] ) ? $result['term_id'] : 0;
        }
	} else {
		return geodir_imex_insert_term( $taxonomy, $term_data );
	}
	
	return false;
}

/**
 * Get the posts counts for the current post type.
 *
 * @since 1.4.6
 * @package GeoDirectory
 *
 * @param string $post_type Post type.
 * @return int Posts count.
 */
function geodir_get_posts_count( $post_type ) {
	$posts_count = wp_count_posts( $post_type );
	$posts_count = array_sum( (array)$posts_count );
	
	/**
	 * Modify returned post counts for the current post type.
	 *
	 * @since 1.4.6
     * @package GeoDirectory
	 *
	 * @param int $posts_count Post counts.
	 * @param string $post_type Post type.
	 */
	$posts_count = apply_filters( 'geodir_imex_count_posts', $posts_count, $post_type );	 
	
	return $posts_count;
}

/**
 * Filter the posts counts for gd_event post type.
 *
 * @since 1.4.6
 * @package GeoDirectory
 *
 * @global object $wpdb WordPress Database object.
 * @global string $plugin_prefix Geodirectory plugin table prefix.
 *
 * @param int $posts_count Post counts.
 * @param string $post_type Post type.
 * @return int Posts count.
 */
function geodir_imex_count_events( $posts_count, $post_type ) {
	if ( $post_type == 'gd_event' ) {
		global $wpdb, $plugin_prefix;
		
		$table = $plugin_prefix . $post_type . '_detail';
		$schedule_table = EVENT_SCHEDULE;

		$query = "SELECT COUNT({$wpdb->posts}.ID) AS total FROM {$wpdb->posts} INNER JOIN {$table} ON ({$table}.post_id = {$wpdb->posts}.ID) INNER JOIN {$schedule_table} ON ({$schedule_table}.event_id = {$wpdb->posts}.ID) WHERE {$wpdb->posts}.post_type = %s";
		
		$posts_count = (int)$wpdb->get_var( $wpdb->prepare( $query, $post_type ) );
	}
	
	return $posts_count;
}

/**
 * Retrives the posts for the current post type.
 *
 * @since 1.4.6
 * @since 1.5.1 Updated to import & export recurring events.
 * @package GeoDirectory
 *
 * @global object $wp_filesystem WordPress FileSystem object.
 *
 * @param string $post_type Post type.
 * @return array Array of posts data.
 */
function geodir_imex_get_posts( $post_type ) {	
	global $wp_filesystem;
	
	$posts = geodir_get_export_posts( $post_type );
	
	$csv_rows = array();
	
	if ( !empty( $posts ) ) {
		$is_payment_plugin = is_plugin_active( 'geodir_payment_manager/geodir_payment_manager.php' );
		
		$csv_row = array();
		$csv_row[] = 'post_id';
		$csv_row[] = 'post_title';
		$csv_row[] = 'post_author';
		$csv_row[] = 'post_content';
		$csv_row[] = 'post_category';
		$csv_row[] = 'post_tags';
		$csv_row[] = 'post_type';
		if ( $post_type == 'gd_event' ) {
			$csv_row[] = 'event_date';
			$csv_row[] = 'event_enddate';
			$csv_row[] = 'starttime';
			$csv_row[] = 'endtime';
			
			$csv_row[] = 'is_recurring_event';
			$csv_row[] = 'event_duration_days';
			$csv_row[] = 'recurring_dates';
			$csv_row[] = 'is_whole_day_event';
			$csv_row[] = 'event_starttimes';
			$csv_row[] = 'event_endtimes';
			$csv_row[] = 'recurring_type';
			$csv_row[] = 'recurring_interval';
			$csv_row[] = 'recurring_week_days';
			$csv_row[] = 'recurring_week_nos';
			$csv_row[] = 'max_recurring_count';
			$csv_row[] = 'recurring_end_date';
		}
		$csv_row[] = 'post_status';
		$csv_row[] = 'is_featured';
		if ($is_payment_plugin) {
			$csv_row[] = 'package_id';
			$csv_row[] = 'expire_date';
		}
		$csv_row[] = 'geodir_video';
		$csv_row[] = 'post_address';
		$csv_row[] = 'post_city';
		$csv_row[] = 'post_region';
		$csv_row[] = 'post_country';
		$csv_row[] = 'post_zip';
		$csv_row[] = 'post_latitude';
		$csv_row[] = 'post_longitude';
		$csv_row[] = 'geodir_timing';
		$csv_row[] = 'geodir_contact';
		$csv_row[] = 'geodir_email';
		$csv_row[] = 'geodir_website';
		$csv_row[] = 'geodir_twitter';
		$csv_row[] = 'geodir_facebook';
		$csv_row[] = 'geodir_video';
		$csv_row[] = 'geodir_special_offers';
		// WPML
		$is_wpml = geodir_is_wpml();
		if ($is_wpml) {
			$csv_row[] = 'language';
			$csv_row[] = 'original_post_id';
		}
		// WPML
				
		$custom_fields = geodir_imex_get_custom_fields( $post_type );
		if ( !empty( $custom_fields ) ) {
			foreach ( $custom_fields as $custom_field ) {
				$csv_row[] = $custom_field->htmlvar_name;
			}
		}
		
		// Export franchise fields
		$is_franchise_active = is_plugin_active( 'geodir_franchise/geodir_franchise.php' ) && geodir_franchise_enabled( $post_type ) ? true : false;
		if ($is_franchise_active) {
			$csv_row[] = 'gd_is_franchise';
			$csv_row[] = 'gd_franchise_lock';
			$csv_row[] = 'franchise';
		}
		
		$csv_rows[] = $csv_row;

		$images_count = 5;
		foreach ( $posts as $post ) {
			$post_id = $post['ID'];
						
			$taxonomy_category = $post_type . 'category';
			$taxonomy_tags = $post_type . '_tags';
			
			$post_category = '';
			$post_tags = '';
			$terms = wp_get_post_terms( $post_id, array( $taxonomy_category, $taxonomy_tags ) );
			
			if ( !empty( $terms ) && !is_wp_error( $terms ) ) {
				$post_category = array();
				$post_tags = array();
			
				foreach ( $terms as $term ) {
					if ( $term->taxonomy == $taxonomy_category ) {
						$post_category[] = $term->name;
					}
					
					if ( $term->taxonomy == $taxonomy_tags ) {
						$post_tags[] = $term->name;
					}
				}
				
				$post_category = !empty( $post_category ) ? implode( ',', $post_category ) : '';
				$post_tags = !empty( $post_tags ) ? implode( ',', $post_tags ) : '';
			}
			
			$gd_post_info = geodir_get_post_info( $post_id );
			$post_info = (array)$gd_post_info;
			
			// Franchise data
			$franchise_id = NULL;
			$franchise_info = array();
			$locked_fields = array();
			
			if ($is_franchise_active && isset($post_info['franchise']) && (int)$post_info['franchise'] > 0 && geodir_franchise_check((int)$post_info['franchise'])) {
				$franchise_id = $post_info['franchise'];
				$gd_franchise_info = geodir_get_post_info($franchise_id);

				if (geodir_franchise_pkg_is_active($gd_franchise_info)) {
					$franchise_info = (array)$gd_franchise_info;
					$locked_fields = geodir_franchise_get_locked_fields($franchise_id, true);
					
					if (!empty($locked_fields)) {
						foreach( $locked_fields as $locked_field) {
							if (isset($post_info[$locked_field]) && isset($franchise_info[$locked_field])) {
								$post_info[$locked_field] = $franchise_info[$locked_field];
							}
							
							if (in_array($taxonomy_category, $locked_fields) || in_array('post_tags', $locked_fields)) {
								$franchise_terms = wp_get_post_terms( $franchise_id, array( $taxonomy_category, $taxonomy_tags ) );
			
								if ( !empty( $franchise_terms ) && !is_wp_error( $franchise_terms ) ) {
									$franchise_post_category = array();
									$franchise_post_tags = array();
								
									foreach ( $franchise_terms as $franchise_term ) {
										if ( $franchise_term->taxonomy == $taxonomy_category ) {
											$franchise_post_category[] = $franchise_term->name;
										}
										
										if ( $franchise_term->taxonomy == $taxonomy_tags ) {
											$franchise_post_tags[] = $franchise_term->name;
										}
									}
									
									if (in_array($taxonomy_category, $locked_fields)) {
										$post_category = !empty( $franchise_post_category ) ? implode( ',', $franchise_post_category ) : '';
									}
									if (in_array('post_tags', $locked_fields)) {
										$post_tags = !empty( $franchise_post_tags ) ? implode( ',', $franchise_post_tags ) : '';
									}
								}
							}
						}
					}
				}
			}
						
			$post_images = geodir_get_images( $post_id );
			$current_images = array();
			if ( !empty( $post_images ) ) {
				foreach ( $post_images as $post_image ) {
					$post_image = (array)$post_image;
					$image = !empty( $post_image ) && isset( $post_image['path'] ) && $wp_filesystem->is_file( $post_image['path'] ) && $wp_filesystem->exists( $post_image['path'] ) ? $post_image['src'] : '';
					if ( $image ) {
						$current_images[] = $image;
					}
				}
				
				$images_count = max( $images_count, count( $current_images ) );
			}

			$csv_row = array();
			$csv_row[] = $post_id; // post_id
			$csv_row[] = $post_info['post_title']; // post_title
			$csv_row[] = $post_info['post_author']; // post_author
			$csv_row[] = $post_info['post_content']; // post_content
			$csv_row[] = $post_category; // post_category
			$csv_row[] = $post_tags; // post_tags
			$csv_row[] = $post_type; // post_type
			if ( $post_type == 'gd_event' ) {
				$event_data = geodir_imex_get_event_data($post, $gd_post_info);
				$csv_row[] = $event_data['event_date']; // event_date
				$csv_row[] = $event_data['event_enddate']; // enddate
				$csv_row[] = $event_data['starttime']; // starttime
				$csv_row[] = $event_data['endtime']; // endtime
				
				$csv_row[] = $event_data['is_recurring_event']; // is_recurring
				$csv_row[] = $event_data['event_duration_days']; // duration_x
				$csv_row[] = $event_data['recurring_dates']; // recurring_dates
				$csv_row[] = $event_data['is_whole_day_event']; // all_day
				$csv_row[] = $event_data['event_starttimes']; // starttimes
				$csv_row[] = $event_data['event_endtimes']; // endtimes
				$csv_row[] = $event_data['recurring_type']; // repeat_type
				$csv_row[] = $event_data['recurring_interval']; // repeat_x
				$csv_row[] = $event_data['recurring_week_days']; // repeat_days
				$csv_row[] = $event_data['recurring_week_nos']; // repeat_weeks
				$csv_row[] = $event_data['max_recurring_count']; // max_repeat
				$csv_row[] = $event_data['recurring_end_date']; // repeat_end
			}
			$csv_row[] = $post_info['post_status']; // post_status
			$csv_row[] = (int)$post_info['is_featured'] == 1 ? 1 : ''; // is_featured
			if ($is_payment_plugin) {
				$csv_row[] = (int)$post_info['package_id']; // package_id
				$csv_row[] = $post_info['expire_date'] != '' && strtolower($post_info['expire_date']) != 'never' ? date_i18n('Y-m-d', strtotime($post_info['expire_date'])) : 'Never'; // expire_date
			}
			$csv_row[] = $post_info['geodir_video']; // geodir_video
			$csv_row[] = $post_info['post_address']; // post_address
			$csv_row[] = $post_info['post_city']; // post_city
			$csv_row[] = $post_info['post_region']; // post_region
			$csv_row[] = $post_info['post_country']; // post_country
			$csv_row[] = $post_info['post_zip']; // post_zip
			$csv_row[] = $post_info['post_latitude']; // post_latitude
			$csv_row[] = $post_info['post_longitude']; // post_longitude
			$csv_row[] = $post_info['geodir_timing']; // geodir_timing
			$csv_row[] = $post_info['geodir_contact']; // geodir_contact
			$csv_row[] = $post_info['geodir_email']; // geodir_email
			$csv_row[] = $post_info['geodir_website']; // geodir_website
			$csv_row[] = $post_info['geodir_twitter']; // geodir_twitter
			$csv_row[] = $post_info['geodir_facebook']; // geodir_facebook
			$csv_row[] = $post_info['geodir_video']; // geodir_video
			$csv_row[] = $post_info['geodir_special_offers']; // geodir_special_offers
			// WPML
			if ($is_wpml) {
				$csv_row[] = geodir_get_language_for_element( $post_id, 'post_' . $post_type );
				$csv_row[] = get_post_meta( $post_id, '_icl_lang_duplicate_of', true );
			}
			// WPML
			
			if ( !empty( $custom_fields ) ) {
				foreach ( $custom_fields as $custom_field ) {
					$csv_row[] = isset( $post_info[$custom_field->htmlvar_name] ) ? $post_info[$custom_field->htmlvar_name] : '';
				}
			}
			
			// Franchise data
			if ($is_franchise_active) {
				$gd_is_franchise = '';
				$locaked_fields = '';
				$franchise = '';
					
				if (geodir_franchise_pkg_is_active($gd_post_info)) {
					$gd_is_franchise = (int)get_post_meta( $post_id, 'gd_is_franchise', true );
					$locaked_fields = $gd_is_franchise ? get_post_meta( $post_id, 'gd_franchise_lock', true ) : '';
					$locaked_fields = (is_array($locaked_fields) && !empty($locaked_fields) ? implode(",", $locaked_fields) : '');
					$franchise = !$gd_is_franchise && isset($post_info['franchise']) && (int)$post_info['franchise'] > 0 ? (int)$post_info['franchise'] : 0; // franchise id
				}
				
				$csv_row[] = (int)$gd_is_franchise; // gd_is_franchise
				$csv_row[] = $locaked_fields; // gd_franchise_lock fields
				$csv_row[] = (int)$franchise; // franchise id
			}
			
			for ( $c = 0; $c < $images_count; $c++ ) {
				$csv_row[] = isset( $current_images[$c] ) ? $current_images[$c] : ''; // IMAGE
			}
			
			$csv_rows[] = $csv_row;
		}
		
		for ( $c = 0; $c < $images_count; $c++ ) {
			$csv_rows[0][] = 'IMAGE';
		}
	}
	return $csv_rows;
}

/**
 * Retrives the posts for the current post type.
 *
 * @since 1.4.6
 * @package GeoDirectory
 *
 * @global object $wpdb WordPress Database object.
 * @global string $plugin_prefix Geodirectory plugin table prefix.
 *
 * @param string $post_type Post type.
 * @return array Array of posts data.
 */
function geodir_get_export_posts( $post_type ) {
	global $wpdb, $plugin_prefix;

	if ( ! post_type_exists( $post_type ) )
		return new stdClass;
		
	$table = $plugin_prefix . $post_type . '_detail';

	$query = "SELECT {$wpdb->posts}.ID FROM {$wpdb->posts} INNER JOIN {$table} ON {$table}.post_id = {$wpdb->posts}.ID WHERE {$wpdb->posts}.post_type = %s ORDER BY {$wpdb->posts}.ID DESC";
	/**
	 * Modify returned posts SQL query for the current post type.
	 *
	 * @since 1.4.6
     * @package GeoDirectory
	 *
	 * @param int $query The SQL query.
	 * @param string $post_type Post type.
	 */
	$query = apply_filters( 'geodir_imex_export_posts_query', $query, $post_type );

	$results = (array)$wpdb->get_results( $wpdb->prepare( $query, $post_type ), ARRAY_A );

	/**
	 * Modify returned post results for the current post type.
	 *
	 * @since 1.4.6
     * @package GeoDirectory
	 *
	 * @param object $results An object containing all post ids.
	 * @param string $post_type Post type.
	 */
	return apply_filters( 'geodir_export_posts', $results, $post_type );
}

/**
 * Get the posts SQL query for the current post type.
 *
 * @since 1.4.6
 * @since 1.5.1 Query updated to get distinct posts. 
 * @package GeoDirectory
 *
 * @global object $wpdb WordPress Database object.
 * @global string $plugin_prefix Geodirectory plugin table prefix.
 *
 * @param string $query The SQL query.
 * @param string $post_type Post type.
 * @return string The SQL query.
 */
function geodir_imex_get_events_query( $query, $post_type ) {
	if ( $post_type == 'gd_event' ) {
		global $wpdb, $plugin_prefix;
		
		$table = $plugin_prefix . $post_type . '_detail';
		$schedule_table = EVENT_SCHEDULE;

		$query = "SELECT {$wpdb->posts}.ID, {$schedule_table}.event_date, {$schedule_table}.event_enddate AS enddate, {$schedule_table}.event_starttime AS starttime, {$schedule_table}.event_endtime AS endtime FROM {$wpdb->posts} INNER JOIN {$table} ON ({$table}.post_id = {$wpdb->posts}.ID) INNER JOIN {$schedule_table} ON ({$schedule_table}.event_id = {$wpdb->posts}.ID) WHERE {$wpdb->posts}.post_type = %s GROUP BY {$table}.post_id ORDER BY {$wpdb->posts}.ID ASC, {$schedule_table}.schedule_id ASC";
	}
	
	return $query; 
}

/**
 * Retrive terms count for given post type.
 *
 * @since 1.4.6
 * @package GeoDirectory
 *
 * @param  string $post_type Post type.
 * @return int Total terms count.
 */
function geodir_get_terms_count( $post_type ) {
	$args = array( 'hide_empty' => 0 );
	
	remove_all_filters( 'get_terms' );
	
	$taxonomy = $post_type . 'category';

	$count_terms = wp_count_terms( $taxonomy, $args );
	$count_terms = !is_wp_error( $count_terms ) ? $count_terms : 0;
	 
	return $count_terms;
}

/**
 * Retrieve terms for given post type.
 *
 * @since 1.4.6
 * @package GeoDirectory
 *
 * @param  string $post_type The post type.
 * @return array Array of terms data.
 */
function geodir_imex_get_terms( $post_type ) {
	$args = array( 'hide_empty' => 0, 'orderby' => 'id' );
	
	remove_all_filters( 'get_terms' );
	
	$taxonomy = $post_type . 'category';
	
	$terms = get_terms( $taxonomy, $args );

	$csv_rows = array();
	
	if ( !empty( $terms ) ) {
		$csv_row = array();
		$csv_row[] = 'cat_id';
		$csv_row[] = 'cat_name';
		$csv_row[] = 'cat_slug';
		$csv_row[] = 'cat_posttype';
		$csv_row[] = 'cat_parent';
		$csv_row[] = 'cat_description';
		$csv_row[] = 'cat_top_description';
		$csv_row[] = 'cat_image';
		$csv_row[] = 'cat_icon';
		// WPML
		$is_wpml = geodir_is_wpml();
		if ($is_wpml) {
			$csv_row[] = 'cat_language';
		}
		// WPML
		
		$csv_rows[] = $csv_row;
		
		foreach ( $terms as $term ) {			
			$cat_icon = get_tax_meta( $term->term_id, 'ct_cat_icon', false, $post_type );
			$cat_icon = !empty( $cat_icon ) && isset( $cat_icon['src'] ) ? $cat_icon['src'] : '';
			
			$cat_image = geodir_get_default_catimage( $term->term_id, $post_type );
			$cat_image = !empty( $cat_image ) && isset( $cat_image['src'] ) ? $cat_image['src'] : ''; 
			
			$cat_parent = '';
			if (isset($term->parent) && (int)$term->parent > 0 && term_exists((int)$term->parent, $taxonomy)) {
				$parent_term = (array)get_term_by( 'id', (int)$term->parent, $taxonomy );
				$cat_parent = !empty($parent_term) && isset($parent_term['name']) ? $parent_term['name'] : '';
			}
			
			$csv_row = array();
			$csv_row[] = $term->term_id;
			$csv_row[] = $term->name;
			$csv_row[] = $term->slug;
			$csv_row[] = $post_type;
			$csv_row[] = $cat_parent;
			$csv_row[] = $term->description;
			$csv_row[] = get_tax_meta( $term->term_id, 'ct_cat_top_desc', false, $post_type );
			$csv_row[] = $cat_image;
			$csv_row[] = $cat_icon;
			// WPML
			if ($is_wpml) {
				$csv_row[] = geodir_get_language_for_element( $term->term_id, 'tax_' . $taxonomy );
			}
			// WPML
			
			$csv_rows[] = $csv_row;
		}
	}
	return $csv_rows;
}

/**
 * Get the path of cache directory.
 *
 * @since 1.4.6
 * @package GeoDirectory
 *
 * @param  bool $relative True for relative path & False for absolute path.
 * @return string Path to the cache directory.
 */
function geodir_path_import_export( $relative = true ) {
	$upload_dir = wp_upload_dir();
	
	return $relative ? $upload_dir['baseurl'] . '/cache' : $upload_dir['basedir'] . '/cache';
}

/**
 * Save the data in CSV file to export.
 *
 * @since 1.4.6
 * @package GeoDirectory
 *
 * @global null|object $wp_filesystem WP_Filesystem object.
 *
 * @param  string $file_path Full path to file.
 * @param  array $csv_data Array of csv data.
 * @param  bool $clear If true then it overwrite data otherwise add rows at the end of file.
 * @return bool true if success otherwise false.
 */
function geodir_save_csv_data( $file_path, $csv_data = array(), $clear = true ) {
	if ( empty( $csv_data ) ) {
		return false;
	}
	
	global $wp_filesystem;
	
	$mode = $clear ? 'w+' : 'a+';
	
	if ( function_exists( 'fputcsv' ) ) {
		$file = fopen( $file_path, $mode );
		foreach( $csv_data as $csv_row ) {
			//$csv_row = array_map( 'utf8_decode', $csv_row );
			$write_successful = fputcsv( $file, $csv_row, ",", $enclosure = '"' );
		}
		fclose( $file );
	} else {
		foreach( $csv_data as $csv_row ) {
			//$csv_row = array_map( 'utf8_decode', $csv_row );
			$wp_filesystem->put_contents( $file_path, $csv_row );
		}
	}
		
	return true;
}

/**
 * Count the number of line from file.
 *
 * @since 1.4.6
 * @package GeoDirectory
 *
 * @global null|object $wp_filesystem WP_Filesystem object.
 *
 * @param  string $file Full path to file.
 * @return int No of file rows.
 */
function geodir_import_export_line_count( $file ) {
	global $wp_filesystem;
	
	if ( $wp_filesystem->is_file( $file ) && $wp_filesystem->exists( $file ) ) {
		$contents = $wp_filesystem->get_contents_array( $file );
		
		if ( !empty( $contents ) && is_array( $contents ) ) {
			return count( $contents ) - 1;
		}
	}
	
	return NULL;
}

/**
 * Returns queried data from custom fields table.
 *
 * @since 1.0.0
 * @package GeoDirectory
 * @global object $wpdb WordPress Database object.
 * @param string $post_type The post type.
 * @return object Queried object.
 */
function geodir_imex_get_custom_fields( $post_type ) {
	 global $wpdb;
	 
	 $sql = $wpdb->prepare("SELECT htmlvar_name FROM " . GEODIR_CUSTOM_FIELDS_TABLE . " WHERE post_type=%s AND is_active='1' AND is_admin!='1' ORDER BY id ASC", array( $post_type ) );
	 $rows = $wpdb->get_results( $sql );
	 
	 return $rows;
}

/**
 * Check wpml active or not.
 *
 * @since 1.5.0
 *
 * @return True if WPML is active else False.
 */
function geodir_is_wpml() {
	if (function_exists('icl_object_id')) {
		return true;
	}
	
	return false;
}

/**
 * Get WPML language code for current term.
 *
 * @since 1.5.0
 *
 * @global object $sitepress Sitepress WPML object.
 *
 * @param int $element_id Post ID or Term id.
 * @param string $element_type Element type. Ex: post_gd_place or tax_gd_placecategory.
 * @return Language code.
 */
function geodir_get_language_for_element($element_id, $element_type) {
	global $sitepress;
	
	return $sitepress->get_language_for_element($element_id, $element_type);
}

/**
 * Duplicate post details for WPML translation post.
 *
 * @since 1.5.0
 *
 * @param int $master_post_id Original Post ID.
 * @param string $lang Language code for translating post.
 * @param array $postarr Arraty of post data.
 * @param int $tr_post_id Translation Post ID.
 */
function geodir_icl_make_duplicate($master_post_id, $lang, $postarr, $tr_post_id) {
	$post_type = get_post_type($master_post_id);

	if (in_array($post_type, geodir_get_posttypes())) {				
		// Duplicate post details
		geodir_icl_duplicate_post_details($master_post_id, $tr_post_id, $lang);
		
		// Duplicate taxonomies
		geodir_icl_duplicate_taxonomies($master_post_id, $tr_post_id, $lang);
		
		// Duplicate post images
		geodir_icl_duplicate_post_images($master_post_id, $tr_post_id, $lang);
	}
}

/**
 * Duplicate post general details for WPML translation post.
 *
 * @since 1.5.0
 *
 * @global object $wpdb WordPress Database object.
 * @global string $plugin_prefix Geodirectory plugin table prefix.
 *
 * @param int $master_post_id Original Post ID.
 * @param int $tr_post_id Translation Post ID.
 * @param string $lang Language code for translating post.
 * @return bool True for success, False for fail.
 */
function geodir_icl_duplicate_post_details($master_post_id, $tr_post_id, $lang) {
	global $wpdb, $plugin_prefix;
	
	$post_type = get_post_type($master_post_id);
	$post_table = $plugin_prefix . $post_type . '_detail';
	
	$query = $wpdb->prepare("SELECT * FROM " . $post_table . " WHERE post_id = %d", array($master_post_id));
	$data = (array)$wpdb->get_row($query);
	
	if ( !empty( $data ) ) {
		$data['post_id'] = $tr_post_id;
		unset($data['default_category'], $data['marker_json'], $data['featured_image'], $data[$post_type . 'category'], $data['overall_rating'], $data['rating_count'], $data['ratings']);
		
		$wpdb->update($post_table, $data, array('post_id' => $tr_post_id));		
		return true;
	}
	
	return false;
}

/**
 * Duplicate post taxonomies for WPML translation post.
 *
 * @since 1.5.0
 *
 * @global object $sitepress Sitepress WPML object.
 * @global object $wpdb WordPress Database object.
 *
 * @param int $master_post_id Original Post ID.
 * @param int $tr_post_id Translation Post ID.
 * @param string $lang Language code for translating post.
 * @return bool True for success, False for fail.
 */
function geodir_icl_duplicate_taxonomies($master_post_id, $tr_post_id, $lang) {
	global $sitepress, $wpdb;
	$post_type = get_post_type($master_post_id);
	
	remove_filter('get_term', array($sitepress,'get_term_adjust_id')); // AVOID filtering to current language

	$taxonomies = get_object_taxonomies($post_type);
	foreach ($taxonomies as $taxonomy) {
		$terms = get_the_terms($master_post_id, $taxonomy);
		$terms_array = array();
		
		if ($terms) {
			foreach ($terms as $term) {
				$tr_id = apply_filters( 'translate_object_id',$term->term_id, $taxonomy, false, $lang);
				
				if (!is_null($tr_id)){
					// not using get_term - unfiltered get_term
					$translated_term = $wpdb->get_row($wpdb->prepare("
						SELECT * FROM {$wpdb->terms} t JOIN {$wpdb->term_taxonomy} x ON x.term_id = t.term_id WHERE t.term_id = %d AND x.taxonomy = %s", $tr_id, $taxonomy));

					$terms_array[] = $translated_term->term_id;
				}
			}

			if (!is_taxonomy_hierarchical($taxonomy)){
				$terms_array = array_unique( array_map( 'intval', $terms_array ) );
			}

			wp_set_post_terms($tr_post_id, $terms_array, $taxonomy);
			
			if ($taxonomy == $post_type . 'category') {
				geodir_set_postcat_structure($tr_post_id, $post_type . 'category');
			}
		}
	}
}

/**
 * Duplicate post images for WPML translation post.
 *
 * @since 1.5.0
 *
 * @global object $wpdb WordPress Database object.
 *
 * @param int $master_post_id Original Post ID.
 * @param int $tr_post_id Translation Post ID.
 * @param string $lang Language code for translating post.
 * @return bool True for success, False for fail.
 */
function geodir_icl_duplicate_post_images($master_post_id, $tr_post_id, $lang) {
	global $wpdb;
	
	$query = $wpdb->prepare("DELETE FROM " . GEODIR_ATTACHMENT_TABLE . " WHERE mime_type like %s AND post_id = %d", array('%image%', $tr_post_id));
	$wpdb->query($query);
	
	$query = $wpdb->prepare("SELECT * FROM " . GEODIR_ATTACHMENT_TABLE . " WHERE mime_type like %s AND post_id = %d ORDER BY menu_order ASC", array('%image%', $master_post_id));
	$post_images = $wpdb->get_results($query);
	
	if ( !empty( $post_images ) ) {
		foreach ( $post_images as $post_image) {
			$image_data = (array)$post_image;
			unset($image_data['ID']);
			$image_data['post_id'] = $tr_post_id;
			
			$wpdb->insert(GEODIR_ATTACHMENT_TABLE, $image_data);
			
			geodir_set_wp_featured_image($tr_post_id);
		}
		
		return true;
	}
	
	return false;
}

/**
 * Retrives the event data to export.
 *
 * @since 1.5.1
 * @package GeoDirectory
 *
 * @param array $post Post array.
 * @param object $gd_post_info Geodirectory Post object.
 * @return array Event data array.
 */
function geodir_imex_get_event_data($post, $gd_post_info) {
	$event_date = isset( $post['event_date'] ) && $post['event_date'] != '' && $post['event_date'] != '0000-00-00 00:00:00' ? date_i18n( 'd/m/Y', strtotime( $post['event_date'] ) ) : '';
	$event_enddate = $event_date;
	$starttime = isset( $post['starttime'] ) && $post['starttime'] != '' && $post['starttime'] != '00:00:00' ? date_i18n( 'H:i', strtotime( $post['starttime'] ) ) : '';
	$endtime = isset( $post['endtime'] ) && $post['endtime'] != '' && $post['endtime'] != '00:00:00' ? date_i18n( 'H:i', strtotime( $post['endtime'] ) ) : '';
	
	$is_recurring_event = '';
	$event_duration_days = '';
	$is_whole_day_event = '';
	$recurring_dates = '';
	$event_starttimes = '';
	$event_endtimes = '';
	$recurring_type = '';
	$recurring_interval = '';
	$recurring_week_days = '';
	$recurring_week_nos = '';
	$max_recurring_count = '';
	$recurring_end_date = '';
	$recurring_dates = '';
		
	$recurring_data = isset($gd_post_info->recurring_dates) ? maybe_unserialize($gd_post_info->recurring_dates) : array();
	if (!empty($recurring_data)) {
		$event_date = isset( $recurring_data['event_start'] ) && $recurring_data['event_start'] != '' && $recurring_data['event_start'] != '0000-00-00 00:00:00' ? date_i18n( 'd/m/Y', strtotime( $recurring_data['event_start'] ) ) : $event_date;
		$event_enddate = isset( $recurring_data['event_end'] ) && $recurring_data['event_end'] != '' && $recurring_data['event_end'] != '0000-00-00 00:00:00' ? date_i18n( 'd/m/Y', strtotime( $recurring_data['event_end'] ) ) : $event_date;
		$starttime = isset( $recurring_data['starttime'] ) && $recurring_data['starttime'] != '' && $recurring_data['starttime'] != '00:00:00' ? date_i18n( 'H:i', strtotime( $recurring_data['starttime'] ) ) : $starttime;
		$endtime = isset( $recurring_data['endtime'] ) && $recurring_data['endtime'] != '' && $recurring_data['endtime'] != '00:00:00' ? date_i18n( 'H:i', strtotime( $recurring_data['endtime'] ) ) : $endtime;
		$is_whole_day_event = !empty($recurring_data['all_day']) ? 1 : '';
		$different_times = !empty($recurring_data['different_times']) ? true : false;
	
		$recurring_pkg = geodir_event_recurring_pkg( $gd_post_info );
		$is_recurring = isset( $gd_post_info->is_recurring ) && (int)$gd_post_info->is_recurring == 0 ? false : true;
			
		if ($recurring_pkg && $is_recurring) {
			$recurring_dates = $event_date;
			$event_enddate = '';
			$is_recurring_event = 1;
						
			$recurring_type = !empty($recurring_data['repeat_type']) && in_array($recurring_data['repeat_type'], array('day', 'week', 'month', 'year', 'custom')) ? $recurring_data['repeat_type'] : 'custom';
			
			if (!empty($recurring_data['event_recurring_dates'])) {
				$event_recurring_dates = explode( ',', $recurring_data['event_recurring_dates'] );
				
				if (!empty($event_recurring_dates)) {
					$recurring_dates = array();
					
					foreach ($event_recurring_dates as $date) {
						$recurring_dates[] = date_i18n( 'd/m/Y', strtotime( $date ) );
					}
					
					$recurring_dates = implode(",", $recurring_dates);
				}
			}
			
			if ($recurring_type == 'custom') {
				if (!$is_whole_day_event) {
					$event_starttimes = $starttime;
					$event_endtimes = $endtime;
			
					if (!empty($recurring_data['starttimes'])) {
						$times = array();
						
						foreach ($recurring_data['starttimes'] as $time) {
							$times[] = $time != '00:00:00' ? date_i18n( 'H:i', strtotime( $time ) ) : '00:00';
						}
						
						$event_starttimes = implode(",", $times);
					}
					
					if (!empty($recurring_data['endtimes'])) {
						$times = array();
						
						foreach ($recurring_data['endtimes'] as $time) {
							$times[] = $time != '00:00:00' ? date_i18n( 'H:i', strtotime( $time ) ) : '00:00';
						}
						
						$event_endtimes = implode(",", $times);
					}
					
					if (!$different_times) {
						$event_starttimes = '';
						$event_endtimes = '';
					}
				}
			} else {
				$event_duration_days = isset($recurring_data['duration_x']) ? (int)$recurring_data['duration_x'] : 1;
				$recurring_interval = !empty($recurring_data['repeat_x']) && (int)$recurring_data['repeat_x'] > 0 ? $recurring_data['repeat_x'] : 1;
				
				if (($recurring_type == 'week' || $recurring_type == 'month') && !empty($recurring_data['repeat_days'])) {
					$week_days = array('Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat');
					
					$days = array();
					foreach ($recurring_data['repeat_days'] as $day) {
						if (isset($week_days[$day])) {
							$days[] = $week_days[$day];
						}
					}
					
					$recurring_week_days = implode(",", array_unique($days));
				}
				
				$recurring_week_nos = $recurring_type == 'month' && !empty($recurring_data['repeat_weeks']) ? implode(",", $recurring_data['repeat_weeks']) : $recurring_week_nos;
				if (!empty($recurring_data['repeat_end_type']) && (int)$recurring_data['repeat_end_type'] == 1) {
					$recurring_end_date = isset($recurring_data['repeat_end']) && $recurring_data['repeat_end'] != '' && $recurring_data['repeat_end'] != '0000-00-00 00:00:00' ? date_i18n( 'd/m/Y', strtotime( $recurring_data['repeat_end'] ) ) : '';
					$max_recurring_count = empty($recurring_end_date) ? 1 : '';
				} else {
					$max_recurring_count = (!empty($recurring_data['max_repeat']) && (int)$recurring_data['max_repeat'] > 0 ? (int)$recurring_data['max_repeat'] : 1);
				}
			}
		}
	}
	if ($is_whole_day_event) {
		$starttime = '';
		$endtime = '';
		$event_starttimes = '';
		$event_endtimes = '';
	}
	
	$data = array();
	$data['event_date'] = $event_date;
	$data['event_enddate'] = $event_enddate;
	$data['starttime'] = $starttime;
	$data['endtime'] = $endtime;
	$data['is_recurring_event'] = $is_recurring_event;
	$data['recurring_dates'] = $recurring_dates;
	$data['event_duration_days'] = $event_duration_days;
	$data['is_whole_day_event'] = $is_whole_day_event;
	$data['event_starttimes'] = $event_starttimes;
	$data['event_endtimes'] = $event_endtimes;
	$data['recurring_type'] = $recurring_type;
	$data['recurring_interval'] = $recurring_interval;
	$data['recurring_week_days'] = $recurring_week_days;
	$data['recurring_week_nos'] = $recurring_week_nos;
	$data['max_recurring_count'] = $max_recurring_count;
	$data['recurring_end_date'] = $recurring_end_date;
	
	return $data;
}

/**
 * Convert date format to store in database.
 *
 * PHP date() function doesn't work well with d/m/Y format
 * so this function validate and convert date to store in db.
 *
 * @since 1.5.1
 * @package GeoDirectory
 *
 * @param string $date Date in Y-m-d or d/m/Y format.
 * @return doesn't Date.
 */
function geodir_imex_get_date_ymd($date) {
	if (strpos($date, '/') !== false) {
		$date = str_replace('/', '-', $date); // PHP doesn't work well with dd/mm/yyyy format.
	}
	
	$date = date_i18n('Y-m-d', strtotime($date));
	return $date;
}

/**
 * Validate the event data.
 *
 * @since 1.5.1
 * @package GeoDirectory
 *
 * @param array $gd_post Post array.
 * @return array Event data array.
 */
function geodir_imex_process_event_data($gd_post) {
	$recurring_pkg = geodir_event_recurring_pkg( (object)$gd_post );
	
	$is_recurring = isset( $gd_post['is_recurring_event'] ) && (int)$gd_post['is_recurring_event'] == 0 ? false : true;
	$event_date = isset($gd_post['event_date']) && $gd_post['event_date'] != '' ? geodir_imex_get_date_ymd($gd_post['event_date']) : '';
	$event_enddate = isset($gd_post['event_enddate']) && $gd_post['event_enddate'] != '' ? geodir_imex_get_date_ymd($gd_post['event_enddate']) : $event_date;
	$all_day = isset($gd_post['is_whole_day_event']) && !empty($gd_post['is_whole_day_event']) ? true : false;
	$starttime = isset($gd_post['starttime']) && !$all_day ? $gd_post['starttime'] : '';
	$endtime = isset($gd_post['endtime']) && !$all_day ? $gd_post['endtime'] : '';
	
	$repeat_type = '';
	$different_times = '';
	$starttimes = '';
	$endtimes = '';
	$repeat_days = '';
	$repeat_weeks = '';
	$event_recurring_dates = '';
	$repeat_x = '';
	$duration_x = '';
	$repeat_end_type = '';
	$max_repeat = '';
	$repeat_end = '';
	
	if ($recurring_pkg && $is_recurring) {
		$repeat_type = $gd_post['recurring_type'];
		
		if ($repeat_type == 'custom') {
			$starttimes = !$all_day && !empty($gd_post['event_starttimes']) ? explode(",", $gd_post['event_starttimes']) : array();
			$endtimes = !$all_day && !empty($gd_post['event_endtimes']) ? explode(",", $gd_post['event_endtimes']) : array();
			
			if (!empty($starttimes) || !empty($endtimes)) {
				$different_times = true;
			}
			
			$recurring_dates = isset($gd_post['recurring_dates']) && $gd_post['recurring_dates'] != '' ? explode(",", $gd_post['recurring_dates']) : array();
			if (!empty($recurring_dates)) {
				$event_recurring_dates = array();
				
				foreach ($recurring_dates as $recurring_date) {
					$recurring_date = trim($recurring_date);
					
					if ($recurring_date != '') {
						$event_recurring_dates[] = geodir_imex_get_date_ymd($recurring_date);
					}
				}
				
				$event_recurring_dates = array_unique($event_recurring_dates);
				$event_recurring_dates = implode(",", $event_recurring_dates);
			}
		} else {
			$duration_x = !empty( $gd_post['event_duration_days'] ) ? (int)$gd_post['event_duration_days'] : 1;
			$repeat_x = !empty( $gd_post['recurring_interval'] ) ? (int)$gd_post['recurring_interval'] : 1;
			$max_repeat = !empty( $gd_post['max_recurring_count'] ) ? (int)$gd_post['max_recurring_count'] : 1;
			$repeat_end = !empty( $gd_post['recurring_end_date'] ) ? geodir_imex_get_date_ymd($gd_post['recurring_end_date']) : '';
			
			$repeat_end_type = $repeat_end != '' ? 1 : 0;
			$max_repeat = $repeat_end != '' ? '' : $max_repeat;
			
			$week_days = array_flip(array('sun', 'mon', 'tue', 'wed', 'thu', 'fri', 'sat'));
			
			$a_repeat_days = isset($gd_post['recurring_week_days']) && trim($gd_post['recurring_week_days'])!='' ? explode(',', trim($gd_post['recurring_week_days'])) : array();
			$repeat_days = array();
			if (!empty($a_repeat_days)) {
				foreach ($a_repeat_days as $repeat_day) {
					$repeat_day = strtolower(trim($repeat_day));
					
					if ($repeat_day != '' && isset($week_days[$repeat_day])) {
						$repeat_days[] = $week_days[$repeat_day];
					}
				}
				
				$repeat_days = array_unique($repeat_days);
			}
			
			$a_repeat_weeks = isset($gd_post['recurring_week_nos']) && trim($gd_post['recurring_week_nos']) != '' ? explode(",", trim($gd_post['recurring_week_nos'])) : array();
			$repeat_weeks = array();
			if (!empty($a_repeat_weeks)) {
				foreach ($a_repeat_weeks as $repeat_week) {
					$repeat_weeks[] = (int)$repeat_week;
				}
				
				$repeat_weeks = array_unique($repeat_weeks);
			}
		}
	}
	
	if (isset($gd_post['recurring_dates'])) {
		unset($gd_post['recurring_dates']);
	}
	
	$gd_post['is_recurring'] = $is_recurring;
	$gd_post['event_date'] = $event_date;
	$gd_post['event_start'] = $event_date;
	$gd_post['event_end'] = $event_enddate;
	$gd_post['all_day'] = $all_day;
	$gd_post['starttime'] = $starttime;
	$gd_post['endtime'] = $endtime;
	
	$gd_post['repeat_type'] = $repeat_type;
	$gd_post['different_times'] = $different_times;
	$gd_post['starttimes'] = $starttimes;
	$gd_post['endtimes'] = $endtimes;
	$gd_post['repeat_days'] = $repeat_days;
	$gd_post['repeat_weeks'] = $repeat_weeks;
	$gd_post['event_recurring_dates'] = $event_recurring_dates;
	$gd_post['repeat_x'] = $repeat_x;
	$gd_post['duration_x'] = $duration_x;
	$gd_post['repeat_end_type'] = $repeat_end_type;
	$gd_post['max_repeat'] = $max_repeat;
	$gd_post['repeat_end'] = $repeat_end;

	return $gd_post;
}