<?php
/**
 * Single Event Meta (Venue) Template
 *
 */

if ( ! tribe_get_venue_id() ) {
	return;
}
$phone = tribe_get_phone();
$website = tribe_get_venue_website_link();
?>

<div class="grve-tribe-events-meta-group grve-tribe-events-meta-group-venue">
	<?php if ( is_single('tribe_events') ) { ?>
	<h5 class="grve-title"> <?php _e( tribe_get_venue_label_singular(), GRVE_THEME_TRANSLATE ) ?> </h5>
	<?php } else { ?>
	<h5 class="grve-title"> <?php echo tribe_get_venue() ?> </h5>
	<?php } ?>

	<ul>
		<?php do_action( 'tribe_events_single_meta_venue_section_start' ) ?>
		<?php if ( is_single('tribe_events') ) { ?>
		<li>
			<span class="author fn org"> <?php echo tribe_get_venue() ?> </span>
		</li>
		<?php } ?>

		<?php
		// Do we have an address?
		$address = tribe_address_exists() ? '<address class="grve-tribe-events-address">' . tribe_get_full_address() . '</address>' : '';

		// Do we have a Google Map link to display?
		$gmap_link = tribe_show_google_map_link() ? tribe_get_map_link_html() : '';
		$gmap_link = apply_filters( 'tribe_event_meta_venue_address_gmap', $gmap_link );

		// Display if appropriate
		if ( ! empty( $address ) ) {
			echo '<li>' . "$address" . '</li>';
		}
		if ( ! empty( $gmap_link ) ) {
			echo '<li>' . "$gmap_link" . '</li>';
		}
		?>

		<?php if ( ! empty( $phone ) ): ?>
			<li>
				<span> <?php _e( 'Phone:', GRVE_THEME_TRANSLATE ) ?> </span>
				<div class="tel"> <?php echo $phone ?> </div>
			</li>
		<?php endif ?>

		<?php if ( ! empty( $website ) ): ?>
			<li>
				<span> <?php _e( 'Website:', GRVE_THEME_TRANSLATE ) ?> </span>
				<div class="url"> <?php echo $website ?> </div>
			</li>
		<?php endif ?>

		<?php do_action( 'tribe_events_single_meta_venue_section_end' ) ?>
	</ul>
</div>