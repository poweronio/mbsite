<?php
/**
 * Custom functions that act independently of the theme templates
 *
 * Eventually, some of the functionality here could be replaced by core features
 *
 * @package klein
 */

/**
 * Get our wp_nav_menu() fallback, wp_page_menu(), to show a home link.
 */
function klein_page_menu_args( $args ) {
	$args['show_home'] = true;
	return $args;
}
add_filter( 'wp_page_menu_args', 'klein_page_menu_args' );

/**
 * Adds custom classes to the array of body classes.
 */
function klein_body_classes( $classes ) {
	// Adds a class of group-blog to blogs with more than 1 published author
	if ( is_multi_author() ) {
		$classes[] = 'group-blog';
	}
	
	$classes[] = 'klein';
	
	return $classes;
}
add_filter( 'body_class', 'klein_body_classes' );

/**
 * Filter in a link to a content ID attribute for the next/previous image links on image attachment pages
 */
function klein_enhanced_image_navigation( $url, $id ) {
	if ( ! is_attachment() && ! wp_attachment_is_image( $id ) )
		return $url;

	$image = get_post( $id );
	if ( ! empty( $image->post_parent ) && $image->post_parent != $id )
		$url .= '#main';

	return $url;
}
add_filter( 'attachment_link', 'klein_enhanced_image_navigation', 10, 2 );

/**
 * Adds an extra class to our post nav
 */
if( !function_exists( 'klein_nav_attr' )){

	add_filter('next_posts_link_attributes', 'klein_post_nav_prev_attr');
	add_filter('previous_posts_link_attributes', 'klein_post_nav_attr');

	function klein_post_nav_attr(){
		 return 'class="btn btn-primary"';
	}
	
	function klein_post_nav_prev_attr() {
		return 'class="btn btn-default"';
	}
}

/**
 * Add action to get_header function
 * to attach user selected header style
 */

function klein_header() {
	
	$header_style = ot_get_option('header_style', 'style-3');

	return $header_style;
}

/**
 * Filter Social Links
 * that are displayed 
 * inside the option tree
 */
add_filter('ot_type_social_links_defaults', 'klein_social_links');

function klein_social_links() {
	return  array(
		     	array(
		      		'name'    => __( 'Facebook', 'klein' ),
		          	'title'   => 'fa-facebook',
		          	'href'    => 'http://facebook.com'
		      	),
		      	array(
		      		'name'    => __( 'Twitter', 'klein' ),
		          	'title'   => 'fa-twitter',
		          	'href'    => 'http://twitter.com'
		      	),
		      	array(
		      		'name'    => __( 'Google+', 'klein' ),
		          	'title'   => 'fa-google-plus',
		          	'href'    => 'http://plus.google.com'
		      	),
		      	array(
		      		'name'    => __( 'LinkedIn', 'klein' ),
		          	'title'   => 'fa-linkedin',
		          	'href'    => 'http://linkedin.com'
		      	),
		      	array(
		      		'name'    => __( 'Dribbble', 'klein' ),
		          	'title'   => 'fa-dribbble',
		          	'href'    => 'http://dribbble.com'
		      	),
		      	array(
		      		'name'    => __( 'Github', 'klein' ),
		          	'title'   => 'fa-github',
		          	'href'    => 'http://github.com'
		      	),
		      	array(
		      		'name'    => __( 'Twitch', 'klein' ),
		          	'title'   => 'fa-twitch',
		          	'href'    => 'http://twitch.tv'
		      	)
	     	);
}

/**
 * Add stylesheet rule
 * to logo adjustments settings
 */
add_action('before', 'logo_adjustment_settings');

function logo_adjustment_settings() {
	
	$adjust_top  = ot_get_option('logo_adjust_top', 0);
	$adjust_left = ot_get_option('logo_adjust_left', 0);

	$style = sprintf('<style>#bp-klein-top-bar #site-name {margin-top:%dpx; margin-left: %dpx;}</style>', $adjust_top, $adjust_left);

	echo $style;

	return;
}

/**
 * Adds search icon 
 * and search functionality
 * to the menu (after the last item)
 */

add_filter( 'wp_nav_menu_items', 'klein_menu_item', 10, 2 );

function klein_menu_item ($items, $args) {

	$enabled_menu_search = ot_get_option('menu_search', 'on');

	if ('on' === $enabled_menu_search) {

	    if ( $args->theme_location == 'primary') {
	        $items .= '<li id="main-menu-search"><a href="#" title="'.__('Search', 'klein').'" id="klein-search-btn"><i class="fa fa-search"></i></a>';
	        	$items .= '<div id="klein-search-container">';
	        		$items .= '<form method="get" action="'.home_url().'">';
	        			$items .= '<input type="search" name="s" placeholder="'.__('Search anything here ...', 'klein').'" />';
	        		$items .= '</form>';
	        	$items .= '</div>';
	        $items .= '</li>';
	    }
	    
	}

	return $items;
}

/**
 * Adds JS config set from
 * Theme Options 
 */
add_action('after', 'klein_js_themeoptions');

function klein_js_themeoptions()
{
	$isStickyMenu = ot_get_option('sticky_menu', 'on');
	$backToTop = ot_get_option('back_to_top', 'on');
	?>
	<script> var kleinConfig = { isStickyMenu: '<?php echo $isStickyMenu; ?>', hasBackToTop: '<?php echo $backToTop; ?>' }; </script>
	<?php
	return;
}

/**
 * Calculates the amount of columns
 * that are needed for header menu
 * and user action bar
 * 
 * @return array 'menu' for menu and 'actions' for user actions
 */
function klein_the_menu_columns() {

	$cols = array(
			'menu' => 7,
			'actions' => 3,
		);
	
	$login_enabled = ot_get_option('enable_login', 'yes');
	$register_enabled = ot_get_option('enable_register', 'yes');
 
	if ('off' === $login_enabled) { 
		$cols['menu']++; 
		$cols['actions']--; 
	}
	if ('off' === $register_enabled) { $cols['menu']++; $cols['actions']--; }

	return $cols;
}

/**
 * Change the default option tree version title
 */
add_filter('ot_header_version_text', 'klein_ot_version_title');

function klein_ot_version_title() {
	return 'Klein Version ' . KLEIN_VERSION;
}

if (version_compare( $GLOBALS['wp_version'], '4.1', '<' )) {
	/**
	 * Filters wp_title to print a neat <title> tag based on what is being viewed.
	 *
	 * @param string $title Default title text for current view.
	 * @param string $sep Optional separator.
	 * @return string The filtered title.
	 */
	function klein_wp_title($title, $sep) {
		
		if (is_feed()) {
			return $title;
		}

		global $page, $paged;
		// Add the blog name
		$title .= get_bloginfo('name', 'display');
		// Add the blog description for the home/front page.
		$site_description = get_bloginfo('description', 'display');
		
		if ($site_description && ( is_home() || is_front_page() )) {
			$title .= " $sep $site_description";
		}
		// Add a page number if necessary:
		if ( ( $paged >= 2 || $page >= 2 ) && ! is_404() ) {
			$title .= " $sep " . sprintf( __( 'Page %s', '_s' ), max( $paged, $page ) );
		}
		return $title;
	}
	add_filter( 'wp_title', 'klein_wp_title', 10, 2 );
	/**
	 * Title shim for sites older than WordPress 4.1.
	 *
	 * @link https://make.wordpress.org/core/2014/10/29/title-tags-in-4-1/
	 * @todo Remove this function when WordPress 4.3 is released.
	 */
	function klein_render_title() {
		?>
		<title><?php wp_title( '|', true, 'right' ); ?></title>
		<?php
	}
	add_action( 'wp_head', 'klein_render_title' );
}

/**
 * Get the current version of gears plugin that is installed on the system
 *
 * @since 3.3
 * @return integer the current version of gears installed on the system
 */
function klein_get_gears_version() 
{

	if (defined('GEARS_APP_VERSION')) {
		return GEARS_APP_VERSION;
	}
	
	return 0;
}