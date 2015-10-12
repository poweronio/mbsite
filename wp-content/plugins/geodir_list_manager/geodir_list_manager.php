<?php
/*
Plugin Name: GeoDirectory Lists
Plugin URI: http://wpgeodirectory.com
Description: GeoDirectory Lists manager.
Version: 0.0.1
Author: GeoDirectory
Author URI: http://wpgeodirectory.com

*/

define("GEODIRLISTS_VERSION", "0.0.1");

global $wpdb, $plugin_prefix, $is_custom_loop,$geodir_addon_list;
if(is_admin()){
    require_once('gd_update.php'); // require update script
}
///GEODIRECTORY CORE ALIVE CHECK START
if(is_admin()){
    include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
    if(!is_plugin_active('geodirectory/geodirectory.php')){
        return;
    }}/// GEODIRECTORY CORE ALIVE CHECK END

$geodir_addon_list['geodir_list_manager'] = 'yes' ;

if(!isset($plugin_prefix))
    $plugin_prefix = $wpdb->prefix.'geodir_';

/**
 * Localisation
 **/
if (!defined('GEODIRLISTS_TEXTDOMAIN')) define('GEODIRLISTS_TEXTDOMAIN', 'geodirlists');

function gd_list_post_type() {
    if ( ! post_type_exists('gd_list') ) {
        $labels = array (
            'name'          => __('Lists', GEODIRLISTS_TEXTDOMAIN),
            'singular_name' => __('List', GEODIRLISTS_TEXTDOMAIN),
            'add_new'       => __('Add New', GEODIRLISTS_TEXTDOMAIN),
            'add_new_item'  => __('Add New List', GEODIRLISTS_TEXTDOMAIN),
            'edit_item'     => __('Edit List', GEODIRLISTS_TEXTDOMAIN),
            'new_item'      => __('New List', GEODIRLISTS_TEXTDOMAIN),
            'view_item'     => __('View List', GEODIRLISTS_TEXTDOMAIN),
            'search_items'  => __('Search Lists', GEODIRLISTS_TEXTDOMAIN),
            'not_found'     => __('No List Found', GEODIRLISTS_TEXTDOMAIN),
            'not_found_in_trash' => __('No List Found In Trash', GEODIRLISTS_TEXTDOMAIN) );

        $args = array (
            'labels' => $labels,
            'can_export' => true,
            'capability_type' => 'post',
            'description' => __('List post type.', GEODIRLISTS_TEXTDOMAIN),
            'has_archive' => 'lists',
            'hierarchical' => false,
            'map_meta_cap' => true,
            'public' => true,
            'query_var' => true,
            'rewrite' => array ('slug' => 'lists', 'with_front' => false, 'hierarchical' => true),
            'supports' => array( 'title', 'editor')
        );
        register_post_type(__('gd_list', GEODIRLISTS_TEXTDOMAIN), $args);

    }
}
add_action( 'init', 'gd_list_post_type' );

if ( ! empty ( $GLOBALS['pagenow'] ) && 'plugins.php' === $GLOBALS['pagenow'] ) {
    add_action( 'admin_notices', 'gd_list_check_admin_notices', 0 );
}

/**
 * List manager requirements check
 */
function gd_list_check_plugin_requirements()
{
    $errors = array ();

    if ( !is_plugin_active('posts-to-posts/posts-to-posts.php') ) {
        $errors[] =  __( 'Addon requires <a href="https://wordpress.org/plugins/posts-to-posts/" target="_blank">Posts 2 Posts</a> plugin.', GEODIRLISTS_TEXTDOMAIN );
    }
    return $errors;

}

/**
 * List manager admin notices
 */
function gd_list_check_admin_notices()
{
    $errors = gd_list_check_plugin_requirements();

    if ( empty ( $errors ) )
        return;

    // Suppress "Plugin activated" notice.
    unset( $_GET['activate'] );

    // This plugin's name
    $name = get_file_data( __FILE__, array ( 'Plugin Name' ) );

    $message = __( '<i>'.$name[0].'</i> has been deactivated.', GEODIRLISTS_TEXTDOMAIN );

    printf(
        '<div class="error"><p>%1$s</p>
        <p>%2$s</p></div>',
        join( '</p><p>', $errors ),
        $message
    );

    deactivate_plugins( plugin_basename( __FILE__ ) );
}

function gd_list_p2p_connection() {

    $all_postypes = geodir_get_posttypes();

    if (!$all_postypes) {
        $all_postypes = array('gd_place');
    }
    foreach ($all_postypes as $pt) {
        p2p_register_connection_type(
            array(
                'name'  => $pt.'_to_gd_list',
                'from'  => $pt,
                'to'    => 'gd_list',
                'admin_box' => array(
                    'show' => 'to',
                    'context' => 'side'
                )
            )
        );
    }

}
add_action( 'p2p_init', 'gd_list_p2p_connection');

function gd_list_create_pages()
{
    $list_page_id = get_option('geodir_add_list_page');
    if(!$list_page_id || ( FALSE === get_post_status( $list_page_id ))) {
        include_once(geodir_plugin_path() . '/geodirectory-admin/admin_install.php');
        geodir_create_page(esc_sql(_x('add-list', 'page_slug', GEODIRLISTS_TEXTDOMAIN)), 'geodir_add_list_page', __('Add List', GEODIRLISTS_TEXTDOMAIN), '');
    }
}
register_activation_hook( __FILE__, 'gd_list_create_pages', 99 );

function gd_list_plugin_url() {

    if (is_ssl()) :
        return str_replace('http://', 'https://', WP_PLUGIN_URL) . "/" . plugin_basename( dirname(__FILE__));
    else :
        return WP_PLUGIN_URL . "/" . plugin_basename( dirname(__FILE__));
    endif;
}

function gd_list_plugin_path()
{
    return WP_PLUGIN_DIR . "/" . plugin_basename(dirname(__FILE__));
}

add_filter( 'template_include', 'add_list_page_template' );
function add_list_page_template( $template ) {

    $list_page_id = get_option('geodir_add_list_page');
    if ( is_page( $list_page_id )  ) {
        $template = locate_template( array( 'geodirectory/add-list.php' ) );
        if (!$template) $template = gd_list_plugin_path() . '/geodirectory-templates/add-list.php';
        return $template = apply_filters('geodir_template_add_list', $template);
    }
    return $template;
}

function geodir_action_add_list_page_title()
{
    echo '<header class=""><h3>';

    if (isset($_REQUEST['pid']) && $_REQUEST['pid'] != '') {
        echo apply_filters('geodir_add_listing_page_title_text', (ucwords(__('Edit List', GEODIRLISTS_TEXTDOMAIN))));
    } elseif (isset($listing_type)) {
        echo apply_filters('geodir_add_listing_page_title_text', (ucwords(__('Add List', GEODIRLISTS_TEXTDOMAIN))));
    } else {
        apply_filters('geodir_add_listing_page_title_text', the_title());
    }
    echo '</h3></header>';
}
add_action('geodir_add_list_page_title', 'geodir_action_add_list_page_title', 10);

add_action('geodir_add_list_form', 'geodir_action_add_list_form', 10);
function geodir_action_add_list_form()
{
    $user_id = get_current_user_id();
    if ( ! $user_id ) {
        return;
    }
    $error = null;
    $pid = 0;
    if(isset($_GET['pid'])) {
        $pid = (int) sanitize_text_field(esc_sql($_GET['pid']));
        if($pid) {
              if (!current_user_can('edit_post', $pid) || (get_post_type($pid) != 'gd_list')) { ?>
                  <p class="add-list-error">
                      <?php echo __('You don\'t have permission to edit this list.', GEODIRLISTS_TEXTDOMAIN); ?>
                  </p>

              <?php
              return;
              }
        }
    }
    if(isset($_POST['add_list_submit'])) {
        if(!$_POST['post_title']) {
            $error = __('List title required', GEODIRLISTS_TEXTDOMAIN);
        } else {
            $title = sanitize_text_field(esc_sql($_POST['post_title']));
            $desc = sanitize_text_field(esc_sql($_POST['post_desc']));

            // Create post object
            $post = array(
                'ID'            => $pid,
                'post_title'    => $title,
                'post_content'  => $desc,
                'post_status'   => 'publish',
                'post_author'   => $user_id,
                'post_type' => 'gd_list'
            );

            $post_id = wp_insert_post( $post );
            $permalink = get_permalink( $post_id );
            wp_redirect( $permalink );
            exit;
        }
    }

    if($error) {
        echo '<p class="add-list-error">'.$error.'</p>';
    }

    $title = '';
    $desc = '';
    ?>
    <form name="addlistform" id="propertyform" action="<?php echo get_page_link(get_option('geodir_add_list_page'));?>"
          method="post" enctype="multipart/form-data">
    <h5><?php
        if ($pid) {
            _e('Edit List Details', GEODIRLISTS_TEXTDOMAIN);
            $title = get_the_title($pid);
            $desc = strip_tags(get_post_field('post_content', $pid));
            $submit_btn_text = __('Update', GEODIRLISTS_TEXTDOMAIN);
        } else {
            _e('Enter List Details', GEODIRLISTS_TEXTDOMAIN);
            $submit_btn_text = __('Create', GEODIRLISTS_TEXTDOMAIN);
        }
        ?></h5>
    <div id="geodir_post_title_row" class="required_field geodir_form_row clearfix">
        <label><?php _e('List Title', GEODIRLISTS_TEXTDOMAIN);?><span>*</span> </label>
        <input type="text" name="post_title" id="post_title" class="geodir_textfield"
               value="<?php echo esc_attr(stripslashes($title)); ?>"/>
    </div>

    <?php
    $desc = esc_attr(stripslashes($desc));
    ?>

    <div id="geodir_post_desc_row" class="geodir_form_row clearfix">
        <label><?php _e('List Description', GEODIRLISTS_TEXTDOMAIN);?></label>

            <textarea name="post_desc" id="post_desc" class="geodir_textarea"><?php echo $desc; ?></textarea>

    </div>

    <div id="geodir-add-listing-submit" class="geodir_form_row clear_both" align="center" style="padding:2px;">
        <input name="add_list_submit" type="submit" value="<?php echo $submit_btn_text; ?>"
               class="geodir_button"/>
            <span class="geodir_message_note"
                  style="padding-left:0px;"> <?php _e('Note: You will be able to add items in the next page', GEODIRLISTS_TEXTDOMAIN);?></span>
    </div>

    </form>
    <?php
}

function geodir_list_output_buffer() {
    if(isset($_POST['add_list_submit'])) {
        ob_start();
    }
}
add_action('init', 'geodir_list_output_buffer');

function geodir_enqueue_list_scripts() {
    if( get_post_type() == 'gd_list' ) {
        if (is_single()) {
            wp_enqueue_script('jquery-ui-sortable');
        }
        wp_register_style( 'gd-list-css', gd_list_plugin_url() . '/geodirectory-assets/css/style.css' );
        wp_enqueue_style( 'gd-list-css' );
    }
}
add_action( 'wp_enqueue_scripts', 'geodir_enqueue_list_scripts', 99 );

function gdlist_single_loop_item() {
    global $post;
    ?>
    <li class="gd-list-item-wrap">
            <h3 class="whoop-tab-title">
                <a href="<?php echo esc_url(add_query_arg(array('list_id' => $post->ID), geodir_curPageURL())); ?>">
                    <?php echo get_the_title($post); ?>
                </a>
                <span class="gd-list-item-count">

                </span>
            </h3>
        <p class="gd-list-item-desc">
            <?php echo wp_trim_words(stripslashes(strip_tags(get_the_content($post))), 20); ?>
        </p>
        <ul class="gd-list-item-comments">
            <?php gdlist_all_listed_posts(); ?>
        </ul>
    </li>
    <?php
}

function geodir_get_lists_by_user_id($user_id = 0) {

    if(isset($_GET['list_id'])) {
        $pid = (int) sanitize_text_field(esc_sql($_GET['list_id']));
        $listed_posts = gdlist_get_all_listed_posts($pid);
        $post_ids = array();
        foreach($listed_posts as $key => $lp) {
            $post_ids[] = $key;
        }
        if ($post_ids) {
            geodir_get_reviews_by_user_id($user_id, false, $post_ids);
        } else { ?>
            <div class="whoop-no-events whoop-no-lists">
                <p>
                    <i class="fa fa-list"></i>
                    <?php echo __('Sorry, no list items just yet.', GEODIRLISTS_TEXTDOMAIN); ?>
                </p>

            </div>
        <?php }
    } else {
        $query_args = array(
            'posts_per_page' => 100,
            'post_type' => 'gd_list',
            'author' => $user_id
        );
        $lists = new WP_Query($query_args);
        if ($lists) {
            ?>
            <ul class="whoop-gd-list-content">
                <?php
                while ( $lists->have_posts() ) : $lists->the_post();
                    gdlist_single_loop_item();
                endwhile;
                wp_reset_postdata();
                ?>
            </ul>
        <?php
        } else { ?>
            <div class="whoop-no-events whoop-no-lists">
                <p>
                    <i class="fa fa-list"></i>
                    <?php echo __('Sorry, no lists just yet.', GEODIRLISTS_TEXTDOMAIN); ?>
                </p>

            </div>
        <?php }
    }
}
/*
 * Buddypress
 */
if (class_exists('BuddyPress')) {
    function whoop_bp_user_lists_nav_adder()
    {
        global $bp;
        if (bp_is_user()) {
            $user_id = $bp->displayed_user->id;
        } else {
            $user_id = 0;
        }
        if ($user_id == 0) {
            return;
        }

        bp_core_new_nav_item(
            array(
                'name' => __('Lists', GEODIRLISTS_TEXTDOMAIN),
                'slug' => 'lists',
                'position' => 21,
                'show_for_displayed_user' => true,
                'screen_function' => 'whoop_bp_user_lists',
                'item_css_id' => 'lists',
                'default_subnav_slug' => 'public'
            ));
    }

    add_action('bp_setup_nav', 'whoop_bp_user_lists_nav_adder');

    function whoop_bp_user_lists()
    {
        add_action('bp_template_content', 'whoop_bp_user_lists_content');
        bp_core_load_template(apply_filters('bp_core_template_plugin', 'members/single/plugins'));
    }

    function whoop_bp_user_lists_content()
    {
        global $bp;
        $user_id = $bp->displayed_user->id;
        ?>
        <div class="whoop-bookmark-header">
            <?php
        if(isset($_GET['list_id'])) {
            $pid = (int)sanitize_text_field(esc_sql($_GET['list_id']));
            $title = get_the_title($pid);
            $content = strip_tags(get_post_field('post_content', $pid));
            $user_link = bp_core_get_user_domain($user_id);
            ?>
            <h3 class="whoop-tab-title" style="float: none;">
                <?php echo $title; ?>
                <?php if (current_user_can('edit_post', $pid)) { ?>
                <a style="font-size: 10px;" href="<?php echo get_permalink($pid); ?>">
                    <?php echo __('Edit Items', GEODIRLISTS_TEXTDOMAIN); ?>
                </a>
                <?php } ?>
                <a href="<?php echo $user_link; ?>lists/" class="see-all-lists"><?php echo __('See All Lists', GEODIRLISTS_TEXTDOMAIN); ?></a>
            </h3>
            <?php if ($content) { ?>
                <p class="whoop-list-desc">
                    <?php echo stripcslashes($content); ?>
                </p>
            <?php } ?>
            <?php
        } else {
          ?>
            <h3 class="whoop-tab-title" style="float: none;line-height: 30px;">
                <?php echo __('Lists', GEODIRLISTS_TEXTDOMAIN); ?>
                <?php if ($user_id == get_current_user_id()) { ?>
                <a href="<?php echo home_url('/add-list/'); ?>" class="whoop-btn whoop-btn-small whoop-btn-primary gd-list-view-btn"><?php echo __('Create List', GEODIRLISTS_TEXTDOMAIN); ?></a>
                <?php } ?>
            </h3>
            <?php
        }
        ?>
        </div>
    <?php
        geodir_get_lists_by_user_id($user_id);
    }
}

function gdlist_get_user_reviewed_posts() {
    $user_id = get_current_user_id();
    if ( ! $user_id ) {
        return false;
    }
    global $wpdb, $tablecomments, $tableposts;
    $tablecomments = $wpdb->comments;
    $tableposts = $wpdb->posts;
    $review_table = GEODIR_REVIEW_TABLE;

    $where = $wpdb->prepare("WHERE post_status=%d AND status=%d AND overall_rating>%d AND user_id=%d ", array(1, 1, 0, $user_id));

    $query = "SELECT post_id FROM $review_table $where";

    $items_per_page = 100;

    $page = isset($_GET['rpage']) ? abs((int)strip_tags(esc_sql($_GET['rpage']))) : 1;
    $offset = ($page * $items_per_page) - $items_per_page;
    $results = $wpdb->get_results($query . " GROUP BY post_id LIMIT ${offset}, ${items_per_page}");

    $p_ids = array();
    foreach($results as $result) {
        $p_ids[] = $result->post_id;
    }

    return $p_ids;
}

function gdlist_get_all_listed_posts($post_id = null) {
    if ($post_id) {
        $post = get_post($post_id);
    } else {
        global $post;
    }
    $listed_posts = array();
    $all_postypes = geodir_get_posttypes();
    foreach ($all_postypes as $pt) {
        $connected = new WP_Query( array(
            'connected_type' => $pt.'_to_gd_list',
            'connected_items' => $post,
            'nopaging' => true
        ) );
        while ( $connected->have_posts() ) : $connected->the_post();
            $listed_posts[get_the_ID()] = get_the_title();
        endwhile;
        wp_reset_postdata(); // set $post back to original post
    }
    return $listed_posts;
}

function gdlist_all_listed_posts($post_id = null) {
    if ($post_id) {
        $post = get_post($post_id);
    } else {
        global $post;
    }
    global $bp;
    $user_id = $bp->displayed_user->id;
    $listed_posts = array();
    $all_postypes = geodir_get_posttypes();
    foreach ($all_postypes as $pt) {
        $args = array(
            'connected_type' => $pt.'_to_gd_list',
            'connected_items' => $post,
            //'nopaging' => true,
        );
        $args['posts_per_page'] = 3;
        $connected = new WP_Query($args);
        while ( $connected->have_posts() ) : $connected->the_post();
            ?>
            <li>
                <div class="gd-list-post-thumb">
                    <?php echo get_the_post_thumbnail( $post->ID, array( 20, 20) ); ?>
                </div>
                <a href="<?php echo get_the_permalink(); ?>">
                    <?php echo get_the_title(); ?>
                </a>
                <br/>
                <em>
            <?php
            $args = array(
                'status' => 'approve',
                'number' => 1,
                'parent' => 0,
                'post_id' => $post->ID, // use post_id, not post_ID
                'user_id' => $user_id
            );
            $comments = get_comments($args);
            if ($comments) {
                ?>
                    <?php
                    foreach($comments as $comment) {
                        ?>
                        <?php echo wp_trim_words(stripslashes(strip_tags($comment->comment_content)), 20); ?>
                    <?php
                    }
                    ?>
            <?php } ?>
                </em>
            </li>
            <?php
        endwhile;
        wp_reset_postdata(); // set $post back to original post
    }
    return $listed_posts;
}

function gdlist_get_all_reviewed_posts() {
    $p_ids = gdlist_get_user_reviewed_posts();
    $post_types = geodir_get_posttypes();
    $all_posts = array();
    if ($p_ids) {
        $query_args = array(
            'post_type' => $post_types,
            'posts_per_page' => 100
        );
        $query_args['post__in'] = $p_ids;
        $listings = new WP_Query($query_args);

        if ($listings) {
            while ( $listings->have_posts() ) : $listings->the_post();
                $all_posts[get_the_ID()] = get_the_title();
            endwhile;
        }
        wp_reset_postdata();
    }
    return $all_posts;
}

function gdlist_create_connection_for_each_post($cur_post_id, $post_ids) {
    $listed_posts = gdlist_get_all_listed_posts($cur_post_id);

    $listed_post_ids = array();
    foreach($listed_posts as $key => $title) {
        $listed_post_ids[] = (string) $key;
    }

    $removed_ids = array_diff($listed_post_ids, $post_ids);
    $added_ids = array_diff($post_ids, $listed_post_ids);

    if ( current_user_can('edit_post', $cur_post_id) ) {
        foreach($added_ids as $pid) {
            $con_type = get_post_type( $pid ).'_to_gd_list';
            $args = array(
                'from' => $pid,
                'to' => $cur_post_id
            );

            //$exists = p2p_connection_exists($con_type, $args);
            //if(!$exists) {
                p2p_create_connection($con_type, $args);
            //}
        }

        foreach($removed_ids as $pid) {
            $con_type = get_post_type( $pid ).'_to_gd_list';
            $args = array(
                'from' => $pid,
                'to' => $cur_post_id
            );

            //$exists = p2p_connection_exists($con_type, $args);
            //if($exists) {
                p2p_delete_connections($con_type, $args);
            //}
        }
    }
}

function gdlist_create_connection() {
    check_ajax_referer('gdlist-connection-nonce', 'gdlist_connection_nonce');
    //set variables
    $ids = strip_tags(esc_sql($_POST['ids']));
    $cur_post_id = strip_tags(esc_sql($_POST['cur_post_id']));
    if(empty($ids)) {
        $post_ids = array();
    } else {
        parse_str($ids);
        $post_ids = $post;
    }

    gdlist_create_connection_for_each_post($cur_post_id, $post_ids);
    wp_die();
}
add_action('wp_ajax_gdlist_create_connection', 'gdlist_create_connection');

//Javascript

add_action('wp_footer', 'gdlist_create_connection_js');
function gdlist_create_connection_js() {
    if( is_single() && get_post_type() == 'gd_list' ) {
        $ajax_nonce = wp_create_nonce("gdlist-connection-nonce");
        ?>
        <script type="text/javascript">
            jQuery(document).ready(function () {
                jQuery("#whoop-listSortable-left, #whoop-listSortable-right").sortable({
                    connectWith: ".whoop-listSortable"
                }).disableSelection();

                jQuery('form.gd_list_form').submit(function () {
                    jQuery("#gd_list_submit_btn").html('Saving...').prop('disabled', true);
                    var ids = jQuery("#whoop-listSortable-right").sortable("serialize");
                    var cur_post_id = jQuery('#cur_post_id').val();
                    var data = {
                        'action': 'gdlist_create_connection',
                        'gdlist_connection_nonce': '<?php echo $ajax_nonce; ?>',
                        'ids': ids,
                        'cur_post_id': cur_post_id
                    };
                    jQuery.post('<?php echo admin_url('admin-ajax.php'); ?>', data, function (response) {
                        jQuery("#gd_list_submit_btn").html('Saved').prop('disabled', false);
                    });
                    return false;
                });
            });
        </script>
    <?php
    }
}

//widgets
include_once(gd_list_plugin_path() . '/geodirectory-widgets/fresh-lists.php');


// include upgrade script
if ( is_admin() ){
    require_once('gd_upgrade.php');
}