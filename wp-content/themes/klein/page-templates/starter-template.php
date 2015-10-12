<?php
/**
 * Template Name: Starter Template
 *
 * @package  Klein
 * @version  3.0
 * @since  3.0
 */

get_header(klein_header());

/**
 * Just allow the content 
 * to be place here..
 */

if( have_posts() ){
	while( have_posts() ){
		the_post();
		// using the WordPress loop, we'll display the post content here
		// inorder for page builder to work, you need the page builder's shortcode
		// right into the textarea wherein you compose your blog
		the_content();
	}
}

get_footer();
?>