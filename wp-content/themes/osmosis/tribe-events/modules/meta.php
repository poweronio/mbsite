<?php
/**
 * Single Event Meta Template
 *
 */

do_action( 'tribe_events_single_meta_before' );
?>

	<div class="grve-tribe-events-single-section grve-tribe-events-event-meta">

<?php
		//Primary Section Map
		if ( tribe_embed_google_map() ) {
?>
			<div class="grve-row">
				<div class="grve-column-1">
					<?php tribe_get_template_part( 'modules/meta/map' ); ?>
				</div>
			</div>
<?php
		}
		//Secondary ( Details/Venue/Organizer )		
?>
		<div class="grve-row">
			<div class="grve-column-1-3">
				<?php tribe_get_template_part( 'modules/meta/details' ); ?>
			</div>
			<div class="grve-column-1-3">
				<?php tribe_get_template_part( 'modules/meta/venue' ); ?>
			</div>
<?php
		if ( tribe_has_organizer() ) {
?>
			<div class="grve-column-1-3">
				<?php tribe_get_template_part( 'modules/meta/organizer' ); ?>
			</div>
<?php
		}
?>
		</div>
<?php
		do_action( 'tribe_events_single_event_meta_primary_section_start' );
		do_action( 'tribe_events_single_event_meta_primary_section_end' );
		
		do_action( 'tribe_events_single_event_meta_secondary_section_start' );
		do_action( 'tribe_events_single_event_meta_secondary_section_end' );
?>
	</div>

<?php

do_action( 'tribe_events_single_meta_after' );

?>