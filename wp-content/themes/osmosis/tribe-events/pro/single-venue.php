<?php
/**
 * Single Venue Template
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

$venue_id = get_the_ID();

?>
<div id="grve-events-venue-area">
	<?php while ( have_posts() ) : the_post(); ?>
	<div id="post-<?php the_ID(); ?>" <?php post_class('grve-single-post'); ?>>
		<div id="grve-single-media">
			<!-- Event featured image, but exclude link -->
			<?php echo tribe_event_featured_image( $venue_id, 'grve-image-fullscreen', false ); ?>
		</div>

		<div id="grve-post-content">
			<!-- Venue Title -->
			<?php do_action('tribe_events_single_venue_before_title') ?>
			<?php grve_print_event_venue_simple_title(); ?>
			<?php do_action('tribe_events_single_venue_after_title') ?>
			<!-- Organizer Content -->
			<?php the_content(); ?>

			<!-- Organizer Meta -->
			<div class="grve-row">
				<div class="grve-column-1-3">
					<?php tribe_get_template_part( 'modules/meta/venue' ); ?>
				</div>
				<div class="grve-column-2-3">
				<?php if ( tribe_embed_google_map() && tribe_address_exists() ) : ?>
					<!-- Venue Map -->
					<div class="grve-tribe-events-map-wrap">
						<?php echo tribe_get_embedded_map( $venue_id, '100%', '250px' ); ?>
					</div>
				<?php endif; ?>
				</div>
			</div>
		</div>

		<!-- Upcoming event list -->
		<?php do_action('tribe_events_single_venue_before_upcoming_events') ?>
		<?php
		// @todo rewrite + use tribe_venue_upcoming_events()
		echo tribe_include_view_list( array(
			'venue'          => $venue_id,
			'eventDisplay'   => 'list',
			'posts_per_page' => apply_filters( 'tribe_events_single_venue_posts_per_page', 100 )
		) ) ?>
		<?php do_action('tribe_events_single_venue_after_upcoming_events') ?>

	</div><!-- .tribe-events-venue -->
	<?php endwhile; ?>
</div>