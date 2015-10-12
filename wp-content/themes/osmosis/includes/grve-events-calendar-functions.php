<?php

/*
*	Events Calendar helper functions and configuration
*
* 	@version	1.0
* 	@author		Greatives Team
* 	@URI		http://greatives.eu
*/

/**
 * Helper function to check if Events Calendar is enabled
 */
function grve_events_calendar_enabled() {
	if ( class_exists( 'TribeEvents' ) ) {
		return true;
	}
	return false;
}

function grve_events_calendar_pro_enabled() {
	if ( class_exists( 'TribeEventsPro' ) ) {
		return true;
	}
	return false;
}

/**
 * Helper function to check if is Events Calendar Overview Page
 */
function grve_events_calendar_is_overview() {
	if ( grve_events_calendar_enabled() ) {
		if ( tribe_is_list_view() || tribe_is_day() || tribe_is_month() ) {
			return true;
		}
	}
	if ( grve_events_calendar_pro_enabled() ) {
		if ( tribe_is_week() || tribe_is_map() || tribe_is_photo() ) {
			return true;
		}
	}
	return false;
}

//If Events Calendar plugin is not enabled return
if ( !grve_events_calendar_enabled() ) {
	return false;
}

/**
 * Prints Header navigation for single events
 */
function grve_print_header_item_event_navigation( $element_class = "grve-nav-wrapper") {
	echo '<div class="' . $element_class . '">';
	echo '  <ul class="grve-post-nav">';
		?>
			<li class="grve-nav-right"><?php echo tribe_the_next_event_link('<span class="grve-icon-nav-right"></span>'); ?></li>
			<li><a href="<?php echo tribe_get_events_link(); ?>" class="grve-icon-th-large grve-backlink"></a></li>
			<li class="grve-nav-left"><?php echo tribe_the_prev_event_link('<span class="grve-icon-nav-left"></span>'); ?></li>

		<?php
	echo '  </ul>';
	echo '</div>';
}

/**
 * Prints Simple Title for single events
 */
function grve_print_event_simple_title( $event_id ) {
	$event_style = grve_option( 'event_style', 'default' );
	if ( 'simple' == $event_style ) {
?>
		<div class="grve-event-title-wrapper">
			<h1 class="grve-post-simple-title"><span><?php the_title(); ?></span></h1>
			<a class="grve-events-backlink" href="<?php echo tribe_get_events_link(); ?>"><i class="grve-icon-th-large"></i><?php _e( 'All Events', GRVE_THEME_TRANSLATE ) ?></a>
		</div>
		<h5 id="grve-meta-event-simple-style">
			<?php echo tribe_events_event_schedule_details( $event_id, '', '' ); ?>
<?php
		if ( tribe_get_cost() ) {
?>
			<span class="grve-events-divider">|</span>
			<span class="grve-event-cost grve-color-primary-1"><?php echo tribe_get_cost( null, true ); ?></span>
<?php
		}
?>
		</h5>
<?php
	}
}

/**
 * Prints Simple Title for single organizer
 */
function grve_print_event_organizer_simple_title() {
	$event_style = grve_option( 'event_style', 'default' );
	if ( 'simple' == $event_style ) {
?>
		<div class="grve-event-title-wrapper">
			<h1 class="grve-post-simple-title"><span><?php the_title(); ?></span></h1>
			<a class="grve-events-backlink" href="<?php echo tribe_get_events_link(); ?>"><i class="grve-icon-th-large"></i><?php _e( 'All Events', GRVE_THEME_TRANSLATE ) ?></a>
		</div>
		<h5 id="grve-meta-event-simple-style">
			<?php echo grve_event_organizer_title_meta(); ?>
		</h5>
<?php
	}
}
/**
 * Prints Simple Title for single venue
 */
function grve_print_event_venue_simple_title() {
	$event_style = grve_option( 'event_style', 'default' );
	if ( 'simple' == $event_style ) {
?>
		<div class="grve-event-title-wrapper">
			<h1 class="grve-post-simple-title"><span><?php the_title(); ?></span></h1>
			<a class="grve-events-backlink" href="<?php echo tribe_get_events_link(); ?>"><i class="grve-icon-th-large"></i><?php _e( 'All Events', GRVE_THEME_TRANSLATE ) ?></a>
		</div>
<?php
	}
}


/**
 * Prints title organizer meta
 */
function grve_event_organizer_title_meta() {
	$phone = tribe_get_organizer_phone();
	$email = tribe_get_organizer_email();
	$website = tribe_get_organizer_website_link();
	ob_start();
?>
<?php if ( ! empty( $phone ) || ! empty( $email ) || ! empty( $website ) ): ?>
	<div class="grve-event-organizer-title-meta">
	<?php if ( ! empty( $phone ) ): ?>
			<span class="tel"> <?php echo $phone ?> </span>
	<?php endif ?>

	<?php if ( ! empty( $email ) ): ?>
			<span class="email"> <?php echo $email ?> </span>
	<?php endif ?>

	<?php if ( ! empty( $website ) ): ?>
			<span class="url"> <?php echo $website ?> </span>
	<?php endif ?>
	</div>
<?php endif ?>
<?php
	return ob_get_clean();
}

/**
 * Prints links for single events
 */
function grve_single_event_links() {

	// don't show on password protected posts
	if ( is_single() && post_password_required() ) {
		return;
	}

	echo '<div class="grve-tribe-events-cal-links">';
	echo '<a class="grve-btn grve-btn-extrasmall grve-square grve-bg-primary-1" href="' . tribe_get_gcal_link() . '" title="' . __( 'Add to Google Calendar', GRVE_THEME_TRANSLATE ) . '"><span>' . __( 'Google Calendar', GRVE_THEME_TRANSLATE ) . '</span></a>';
	echo '<a class="grve-btn grve-btn-extrasmall grve-square grve-grey-color" href="' . tribe_get_single_ical_link() . '" title="' . __( 'Download .ics file', GRVE_THEME_TRANSLATE ) . '" ><span>' . __( 'iCal Export', GRVE_THEME_TRANSLATE ) . '</span></a>';
	echo '</div>';
}


function grve_tribe_meta_event_tags( $label = null, $separator = ', ', $echo = true ) {
	$list = get_the_term_list( get_the_ID(), 'post_tag', '<li><span>' . $label . '</span><div class="grve-tribe-event-tags">', $separator, '</div></li>' );
	return $list;
}

//add_filter( 'tribe_meta_event_tags', 'grve_tribe_meta_event_tags' );

function grve_events_maybe_add_link(  ) {

	global $wp_query;
	$show_ical = apply_filters( 'tribe_events_list_show_ical_link', true );

	if ( ! $show_ical ) {
		return;
	}
	if ( tribe_is_month() && ! tribe_events_month_has_events() ) {
		return;
	}
	if ( is_single() || ! have_posts() ) {
		return;
	}

	$tec = TribeEvents::instance();

	$view = $tec->displaying;
	if ( defined( 'DOING_AJAX' ) && DOING_AJAX && isset( $wp_query->query_vars['eventDisplay'] ) ) {
		$view = $wp_query->query_vars['eventDisplay'];
	}

	switch ( strtolower( $view ) ) {
		case 'month':
			$modifier = __( "Month's Events", GRVE_THEME_TRANSLATE );
			break;
		case 'week':
			$modifier = __( "Week's Events", GRVE_THEME_TRANSLATE );
			break;
		case 'day':
			$modifier = __( "Day's Events", GRVE_THEME_TRANSLATE );
			break;
		default:
			$modifier = __( "Listed Events", GRVE_THEME_TRANSLATE );
			break;
	}

	$text  = apply_filters( 'tribe_events_ical_export_text', __( 'Export', GRVE_THEME_TRANSLATE ) . ' ' . $modifier );
	$title = __( 'Use this to share calendar data with Google Calendar, Apple iCal and other compatible apps', GRVE_THEME_TRANSLATE );
	$ical  = '<div class="grve-align-right"><a class="grve-btn grve-btn-extrasmall grve-square grve-bg-primary-1" title="' . $title . '" href="' . tribe_get_ical_link() . '">' . $text . '</a></div>';

	echo $ical;
}
remove_filter( 'tribe_events_after_footer', array( 'TribeiCal', 'maybe_add_link' ) );
add_action( 'tribe_events_after_footer', 'grve_events_maybe_add_link', 10, 2);

/**
 * Add Meta fields To Events
 */
require_once( get_template_directory() . '/includes/admin/grve-event-meta.php' );


?>