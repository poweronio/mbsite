<?php
/**
 * Single Event Meta (Map) Template
 *
 */

$map = apply_filters( 'tribe_event_meta_venue_map', tribe_get_embedded_map() );
if ( empty( $map ) ) {
	return;
}
?>

<div class="grve-tribe-events-venue-map">
	<?php
	do_action( 'tribe_events_single_meta_map_section_start' );
	echo $map;
	do_action( 'tribe_events_single_meta_map_section_end' );
	?>
</div>