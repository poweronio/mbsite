<?php
/**
 * Related Events Template
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

$posts = tribe_get_related_posts();

?>
<?php
if ( is_array( $posts ) && ! empty( $posts ) ) {

	echo '<div class="grve-related-post">';
	echo '<h5 class="grve-related-title">' . __( 'Related Events', GRVE_THEME_TRANSLATE ) . '</h5>';
	echo '<ul>';
	foreach ( $posts as $post ) {
		echo '<li>';

		$thumb = ( has_post_thumbnail( $post->ID ) ) ? get_the_post_thumbnail( $post->ID, 'grve-image-small-rect-horizontal' ) : '<img src="' . get_template_directory_uri() . '/images/empty/tribe-related-events-placeholder.png" alt="' . get_the_title( $post->ID ) . '" />';;
		echo '<div class="grve-media grve-image-hover">';
		echo '<a href="' . tribe_get_event_link( $post ) . '" class="url" rel="bookmark">' . $thumb . '</a>';
		echo '</div>';

		echo '<div class="grve-content">';

		echo '<a href="' . tribe_get_event_link( $post ) . '" class="url" rel="bookmark"><h6 class="grve-title">' . get_the_title( $post->ID ) . '</h6></a>';

		if ( $post->post_type == TribeEvents::POSTTYPE ) {
			echo '<div class="grve-caption">' . tribe_events_event_schedule_details( $post ) . '</div>';
		}
		echo '</div>';
		echo '</li>';
	}
	echo '</ul>';
	echo '</div>';
}
?>