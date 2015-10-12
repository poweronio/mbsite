<?php
/**
 * Single Event Template
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

$event_id = get_the_ID();

?>
<div id="grve-event-area">

	<!-- Notices -->
	<?php tribe_events_the_notices() ?>

	<?php while ( have_posts() ) :  the_post(); ?>
		<div id="post-<?php the_ID(); ?>" <?php post_class('grve-single-post'); ?>>
			<div id="grve-single-media">
				<!-- Event featured image, but exclude link -->
				<?php echo tribe_event_featured_image( $event_id, 'grve-image-fullscreen', false ); ?>
			</div>

			<div id="grve-post-content">
				<?php grve_print_event_simple_title( $event_id ); ?>
				<!-- Event content -->
				<?php do_action( 'tribe_events_single_event_before_the_content' ) ?>
				<?php the_content(); ?>

				<?php grve_single_event_links(); ?>

				<!-- Event meta -->
				<?php do_action( 'tribe_events_single_event_before_the_meta' ) ?>
				<?php echo tribe_events_single_event_meta(); ?>
				<?php do_action( 'tribe_events_single_event_after_the_meta' ) ?>
			</div>
		</div>
		<?php grve_print_header_item_event_navigation('grve-nav-wrapper-default'); ?>
		<?php if ( get_post_type() == TribeEvents::POSTTYPE && tribe_get_option( 'showComments', false ) ) comments_template() ?>
	<?php endwhile; ?>

</div>