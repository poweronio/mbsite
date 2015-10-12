<?php
/*
Plugin Name: Poweron
Plugin URI: http://poweron.io/
Description: Custom Email
Author: Omar Maghrabi   
Author URI: http://poweron.io
*/

add_action( 'bp_setup_nav', 'add_videos_subnav_tab', 100 );

function add_videos_subnav_tab() {
	global $bp;

	bp_core_new_subnav_item( array(
		'name' => 'Posts',
		'slug' => 'posts',
		'parent_url' => trailingslashit( bp_loggedin_user_domain() . 'favorites' ),
		'parent_slug' => 'favorites',
		'screen_function' => 'profile_screen_posts',
		'position' => 10
		)
	);
    bp_core_new_subnav_item( array(
		'name' => 'Articles',
		'slug' => 'articles',
		'parent_url' => trailingslashit( bp_loggedin_user_domain() . 'favorites' ),
		'parent_slug' => 'favorites',
		'screen_function' => 'profile_screen_articles',
		'position' => 20
		)
	);
    $bp->bp_nav['profile']['name'] = 'About Me';
    $bp->bp_nav['buddyblog']['name'] = 'Published Articles';
    $bp->bp_nav['friends']['name'] = 'Soul Family';
    $bp->bp_nav['friends']['position'] = 20;
}

// redirect to videos page when 'Videos' tab is clicked
// assumes that the slug for your Videos page is 'videos' 


function fav_title() {
    echo 'Favorite Posts';
    
}
function fav_content() {
    ?>
<a href="http://mannabliss.com/" class="fav-add-more">Add Posts</a>
	<div id="groups-dir-list" class="groups dir-list"> 
        
		[activity-stream title='Favorite Posts' display_comments=threaded per_page=100 pagination=0  scope=favorites]
	</div><!-- #groups-dir-list -->
    <?php
}

function profile_screen_posts() {
	add_action( 'bp_template_title', 'fav_title' );
	add_action( 'bp_template_content', 'fav_content' );
	bp_core_load_template( apply_filters( 'bp_core_template_plugin', 'members/single/plugins'));
}

function articles_fav_title() {
    echo 'Favorite Articles';
}
function articles_fav_content() {
   ?>
<a href="http://mannabliss.com/news-2/" class="fav-add-more">Add Articles</a>
	<div id="wpfp-d">
        
		[wp-favorite-posts]
	</div><!-- #groups-dir-list -->
    <?php
		
	
   
}

function profile_screen_articles() {
	add_action( 'bp_template_title', 'articles_fav_title' );
	add_action( 'bp_template_content', 'articles_fav_content' );
	bp_core_load_template( apply_filters( 'bp_core_template_plugin', 'members/single/plugins'));
}




/*function my_all_conversations_title() {
	echo 'Articles Published';
}
 
function my_all_conversations_content() {
	?>
	<div id="groups-dir-list" class="groups dir-list">
		<?php locate_template( array( 'groups/groups-loop.php' ), true ) ?>
	</div><!-- #groups-dir-list -->
    <?php
}
 
function my_all_conversations_link () {
	add_action( 'bp_template_title', 'my_all_conversations_title' );
	add_action( 'bp_template_content', 'my_all_conversations_content' );
	bp_core_load_template( apply_filters( 'bp_core_template_plugin', 'members/single/plugins' ) );
}*/

add_action( 'bp_profile_header_meta', 'display_user_color_pref' );
function display_user_color_pref() {
    echo '<div id="profile-header-about">My Bio: ';
    $args = array(
        'field'   => 2, // Integers do not need to be enclosed in quotes.
        );
    bp_profile_field_data( $args );
    echo '</div>';
    
    echo '<div id="profile-header-about">My commitment: ';
    $args = array(
        'field'   => 3, // Integers do not need to be enclosed in quotes.
        );
    bp_profile_field_data( $args );
    echo '</div>';
    
    echo '<div id="profile-header-about">I\'m interested in: ';
    $args = array(
        'field'   => 9, // Integers do not need to be enclosed in quotes.
        );
    bp_profile_field_data( $args );
    echo '</div>';
    
    echo '<div id="profile-header-about">I teach yoga at: ';
    $args = array(
        'field'   => 255, // Integers do not need to be enclosed in quotes.
        );
    bp_profile_field_data( $args );
    echo '</div>';
}

?>
