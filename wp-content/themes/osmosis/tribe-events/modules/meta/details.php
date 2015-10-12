<?php
/**
 * Single Event Meta (Details) Template
 *
 */
?>

<div class="grve-tribe-events-meta-group grve-tribe-events-meta-group-details">
	<h5 class="grve-title"> <?php _e( 'Details', GRVE_THEME_TRANSLATE ) ?> </h5>
	<ul>

		<?php
		do_action( 'tribe_events_single_meta_details_section_start' );

		$time_format = get_option( 'time_format', TribeDateUtils::TIMEFORMAT );
		$time_range_separator = tribe_get_option( 'timeRangeSeparator', ' - ' );

		$start_datetime = tribe_get_start_date();
		$start_date = tribe_get_start_date( null, false );
		$start_time = tribe_get_start_date( null, false, $time_format );
		$start_ts = tribe_get_start_date( null, false, TribeDateUtils::DBDATEFORMAT );

		$end_datetime = tribe_get_end_date();
		$end_date = tribe_get_end_date( null, false );
		$end_time = tribe_get_end_date( null, false, $time_format );
		$end_ts = tribe_get_end_date( null, false, TribeDateUtils::DBDATEFORMAT );

		// All day (multiday) events
		if ( tribe_event_is_all_day() && tribe_event_is_multiday() ) :
		?>
			<li>
				<span> <?php _e( 'Start:', GRVE_THEME_TRANSLATE ) ?> </span>
				<abbr class="grve-tribe-events-abbr updated published" title="<?php esc_attr_e( $start_ts ) ?>"> <?php esc_html_e( $start_date ) ?> </abbr>
			</li>
			<li>
				<span> <?php _e( 'End:', GRVE_THEME_TRANSLATE ) ?> </span>
				<abbr class="grve-tribe-events-abbr" title="<?php esc_attr_e( $end_ts ) ?>"> <?php esc_html_e( $end_date ) ?> </abbr>
			</li>
		<?php
		// All day (single day) events
		elseif ( tribe_event_is_all_day() ):
		?>
			<li>
				<span> <?php _e( 'Date:', GRVE_THEME_TRANSLATE ) ?> </span>
				<abbr class="grve-tribe-events-abbr updated published" title="<?php esc_attr_e( $start_ts ) ?>"> <?php esc_html_e( $start_date ) ?> </abbr>
			</li>
		<?php
		// Multiday events
		elseif ( tribe_event_is_multiday() ) :
		?>
			<li>
				<span> <?php _e( 'Start:', GRVE_THEME_TRANSLATE ) ?> </span>
				<abbr class="grve-tribe-events-abbr updated published" title="<?php esc_attr_e( $start_ts ) ?>"> <?php esc_html_e( $start_datetime ) ?> </abbr>
			</li>
			<li>
				<span> <?php _e( 'End:', GRVE_THEME_TRANSLATE ) ?> </span>
				<abbr class="grve-tribe-events-abbr" title="<?php esc_attr_e( $end_ts ) ?>"> <?php esc_html_e( $end_datetime ) ?> </abbr>
			</li>
		<?php
		// Single day events
		else :
		?>
			<li>
				<span> <?php _e( 'Date:', GRVE_THEME_TRANSLATE ) ?> </span>
				<abbr class="grve-tribe-events-abbr updated published" title="<?php esc_attr_e( $start_ts ) ?>"> <?php esc_html_e( $start_date ) ?> </abbr>
			</li>
			<li>
				<span> <?php _e( 'Time:', GRVE_THEME_TRANSLATE ) ?> </span>
				<abbr class="grve-tribe-events-abbr updated published" title="<?php esc_attr_e( $end_ts ) ?>">
				<?php if ( $start_time == $end_time ) {
					esc_html_e( $start_time );
				} else {
					esc_html_e( $start_time . $time_range_separator . $end_time );
				} ?>
				</abbr>
			</li>
		<?php endif ?>

		<?php
		$cost = tribe_get_formatted_cost();
		if ( ! empty( $cost ) ):
		?>
			<li>
				<span> <?php _e( 'Cost:', GRVE_THEME_TRANSLATE ) ?> </span>
				<div class="grve-tribe-events-event-cost"> <?php esc_html_e( tribe_get_formatted_cost() ) ?> </div>
			</li>
		<?php endif ?>

		<?php
		echo tribe_get_event_categories(
			get_the_id(), array(
				'before'       => '',
				'sep'          => ', ',
				'after'        => '',
				'label'        => null, // An appropriate plural/singular label will be provided
				'label_before' => '<li><span>',
				'label_after'  => '</span>',
				'wrap_before'  => '<div class="grve-tribe-events-event-categories">',
				'wrap_after'   => '</div></li>'
			)
		);
		?>

		<?php echo grve_tribe_meta_event_tags( __( 'Event Tags:', GRVE_THEME_TRANSLATE ), ', ', false ) ?>

		<?php
		$website = tribe_get_event_website_link();
		if ( ! empty( $website ) ):
			?>
			<li>
				<span> <?php _e( 'Website:', GRVE_THEME_TRANSLATE ) ?> </span>
				<div class="grve-tribe-events-event-url"> <?php echo $website ?> </div>
			</li>
		<?php endif ?>

		<?php do_action( 'tribe_events_single_meta_details_section_end' ) ?>
	</ul>
</div>