<?php
/**
 * List View Single Event
 * This file contains one event in the list view
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
} ?>

<?php

// Setup an array of venue details for use later in the template
$venue_details = array();

if ( $venue_name = tribe_get_meta( 'tribe_event_venue_name' ) ) {
	$venue_details[] = $venue_name;
}

if ( $venue_address = tribe_get_meta( 'tribe_event_venue_address' ) ) {
	$venue_details[] = $venue_address;
}
// Venue microformats
$has_venue_address = ( $venue_address ) ? ' location' : '';

// Organizer
$organizer = tribe_get_organizer();

?>

<?php
if ( has_post_thumbnail() ) {
?>
<!-- Event Image -->
<div class="grve-media grve-image-hover">
	<a class="url" href="<?php echo tribe_get_event_link() ?>" title="<?php the_title() ?>" rel="bookmark">
		<?php the_post_thumbnail( 'grve-image-small-rect-horizontal' ); ?>
	</a>
</div>
<?php
}
?>

<div class="grve-post-content">

	<!-- Event Title -->
	<?php do_action( 'tribe_events_before_the_event_title' ) ?>
	<a class="url" href="<?php echo tribe_get_event_link() ?>" title="<?php the_title() ?>" rel="bookmark">
		<h4 class="grve-post-title"><?php the_title() ?></h4>
	</a>
	<?php do_action( 'tribe_events_after_the_event_title' ) ?>

	<!-- Event Meta -->
	<?php do_action( 'tribe_events_before_the_meta' ) ?>
	<div class="grve-post-meta">
		<div class="author <?php echo $has_venue_address; ?>">
			<!-- Schedule & Recurrence Details -->
			<div class="grve-post-date">
				<?php echo tribe_events_event_schedule_details() ?>
				<!-- Event Cost -->
				<?php if ( tribe_get_cost() ) : ?>
					<div class="grve-tribe-events-event-cost">
						<span class="grve-bg-primary-1"><?php echo tribe_get_cost( null, true ); ?></span>
					</div>
				<?php endif; ?>
			</div>
		</div>
	</div>
	<?php do_action( 'tribe_events_after_the_meta' ) ?>

	<!-- Event Content -->
	<?php do_action( 'tribe_events_before_the_content' ) ?>
	<?php the_excerpt() ?>
	<?php if ( $venue_details ) : ?>
		<!-- Venue Display Info -->
		<div class="grve-tribe-events-venue-details">
			<?php echo implode( ', ', $venue_details ); ?>
		</div> <!-- .tribe-events-venue-details -->
	<?php endif; ?>

	<a class="grve-read-more" href="<?php echo tribe_get_event_link() ?>"><span><?php _e( 'read more', GRVE_THEME_TRANSLATE ); ?></span></a>
	<?php do_action( 'tribe_events_after_the_content' ) ?>

</div>