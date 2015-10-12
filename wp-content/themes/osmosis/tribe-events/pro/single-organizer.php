<?php
/**
 * Single Organizer Template
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

$organizer_id = get_the_ID();

?>
<div id="grve-events-organizer-area">
<?php while( have_posts() ) : the_post(); ?>
	<div id="post-<?php the_ID(); ?>" <?php post_class('grve-single-post'); ?>>
		<div id="grve-single-media">
			<!-- Event featured image, but exclude link -->
			<?php echo tribe_event_featured_image( $organizer_id, 'grve-image-fullscreen', false ); ?>
		</div>

		<div id="grve-post-content">
			<!-- Organizer Title -->
			<?php do_action('tribe_events_single_organizer_before_title') ?>
			<?php grve_print_event_organizer_simple_title(); ?>
			<?php do_action('tribe_events_single_organizer_after_title') ?>
			<!-- Organizer Content -->
			<?php the_content(); ?>
		</div>


		<!-- Upcoming event list -->
		<?php do_action('tribe_events_single_organizer_before_upcoming_events') ?>
			<?php echo tribe_include_view_list( array( 'organizer'    => get_the_ID(),
													   'eventDisplay' => 'list',
					apply_filters( 'tribe_events_single_organizer_posts_per_page', 100 )
				) ) ?>
		<?php do_action('tribe_events_single_organizer_after_upcoming_events') ?>

	</div><!-- .tribe-events-organizer -->
	<?php do_action( 'tribe_events_single_organizer_after_template' ) ?>
<?php endwhile; ?>
</div>