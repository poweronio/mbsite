<?php
/**
 *  Dynamic css style for Events Calendar
 * 	@author		Greatives Team
 * 	@URI		http://greatives.eu
 */

$css = "";

/**
 * Event Taxonomy Title
 * ----------------------------------------------------------------------------
 */
$css .= "

#grve-page-title.grve-event-tax-title {
	background-color: " . grve_option( 'event_tax_title_background_color' ) . ";
}

";

/**
 * Single Event Title
 * ----------------------------------------------------------------------------
 */
$css .= "

#grve-page-title.grve-event-title {
	background-color: " . grve_option( 'event_title_background_color' ) . ";
}

";

/**
 * Event Bar Settings
 * ----------------------------------------------------------------------------
 */


$css .= "

#grve-event-bar {
	background-color: " . grve_option( 'event_bar_background_color' ) . ";
}

#grve-event-bar ul li a,
#grve-event-bar #grve-social-share ul li .grve-like-counter {
	color: " . grve_option( 'event_bar_text_color' ) . ";
	background-color: transparent;
}

#grve-event-bar ul li a:hover {
	color: " . grve_option( 'event_bar_text_hover_color' ) . ";
	background-color: " . grve_option( 'event_bar_background_hover_color' ) . ";
}


#grve-event-bar ul li,
#grve-event-bar #grve-social-share ul li a {
	border-color: " . grve_option( 'event_bar_border_color' ) . ";
}

";

/**
* Header Colors
* ----------------------------------------------------------------------------
*/

$css .= "
.grve-tribe-events-meta-group ul li span,
#tribe-events-content .tribe-events-calendar div[id*=tribe-events-event-] h3.tribe-events-month-event-title a {
	color: " . grve_option( 'body_heading_color' ) . ";
}

";


/**
* Borders
* ----------------------------------------------------------------------------
*/
$css .= "

.grve-tribe-events-meta-group ul li,
.grve-list-separator:after,
.grve-post-content .grve-tribe-events-venue-details,
#tribe-events-content .tribe-events-calendar td,
.tribe-grid-allday .type-tribe_events>div,
.tribe-grid-allday .type-tribe_events>div:hover,
.tribe-grid-body .type-tribe_events .tribe-events-week-hourly-single,
.tribe-grid-body .type-tribe_events .tribe-events-week-hourly-single:hover {
	border-color: " . grve_option( 'body_border_color' ) . ";
}

";

/**
* Primary Text
* ----------------------------------------------------------------------------
*/

$css .= "

#tribe-events-content .tribe-events-calendar div[id*=tribe-events-event-] h3.tribe-events-month-event-title a:hover,
#tribe-events-content .tribe-events-tooltip h4,
#tribe_events_filters_wrapper .tribe_events_slider_val,
.single-tribe_events a.tribe-events-gcal,
.single-tribe_events a.tribe-events-ical {
	color: " . grve_option( 'body_primary_1_color' ) . ";
}

";

/**
* Primary Bg
* ----------------------------------------------------------------------------
*/

$css .= "

#tribe-bar-form .tribe-bar-submit input[type=submit],
#tribe-events .tribe-events-button,
#tribe-events .tribe-events-button:hover,
#tribe_events_filters_wrapper input[type=submit],
.tribe-events-button,
.tribe-events-button.tribe-active:hover,
.tribe-events-button.tribe-inactive,
.tribe-events-button:hover, .tribe-events-calendar td.tribe-events-present div[id*=tribe-events-daynum-],
.tribe-events-calendar td.tribe-events-present div[id*=tribe-events-daynum-]>a,
.tribe-grid-allday .type-tribe_events>div,
.tribe-grid-allday .type-tribe_events>div:hover,
.tribe-grid-body .type-tribe_events .tribe-events-week-hourly-single,
.tribe-grid-body .type-tribe_events .tribe-events-week-hourly-single:hover {
	background-color: " . grve_option( 'body_primary_1_color' ) . ";
	color: #ffffff;
}

#tribe-bar-form .tribe-bar-submit input[type=submit]:hover {
	background-color: " . grve_option( 'body_primary_1_hover_color' ) . ";
	border-color: " . grve_option( 'body_primary_1_hover_color' ) . ";
	color: #ffffff;
}


";

/**
* Widgets
* ----------------------------------------------------------------------------
*/

$css .= "

#grve-main-content .grve-widget .entry-title a,
#grve-main-content .widget .tribe-countdown-text a {
	color: " . grve_option( 'body_heading_color' ) . ";
}

#grve-main-content .widget .tribe-mini-calendar .tribe-events-has-events a,
#grve-main-content .widget .tribe-countdown-number,
#grve-main-content .widget .tribe-mini-calendar-no-event {
	color: " . grve_option( 'body_text_color' ) . ";
}

#grve-main-content .grve-widget .entry-title a:hover,
.widget .tribe-countdown-text a:hover,
.widget .tribe-mini-calendar-event .list-date .list-dayname,
.widget .tribe-countdown-under,
.widget .tribe-mini-calendar td.tribe-events-has-events a {
	color: " . grve_option( 'body_primary_1_color' ) . ";
}

#grve-main-content .tribe-mini-calendar-event {
	border-color: " . grve_option( 'body_border_color' ) . ";
}

.widget .tribe-mini-calendar-nav td,
.widget .tribe-mini-calendar td.tribe-events-has-events.tribe-events-present,
.widget .tribe-mini-calendar td.tribe-events-has-events.tribe-events-present a:hover,
.widget .tribe-mini-calendar td.tribe-events-has-events a:hover,
.widget .tribe-mini-calendar td.tribe-events-has-events.tribe-mini-calendar-today {
	background-color: " . grve_option( 'body_primary_1_color' ) . ";
	color: #ffffff;
}

";

/* Footer */
$css .= "

#grve-footer .grve-widget .entry-title a,
#grve-footer .widget .tribe-countdown-text a {
	color: " . grve_option( 'footer_widgets_headings_color' ) . ";
}

#grve-footer .widget .tribe-countdown-number,
#grve-footer .widget .tribe-mini-calendar-no-event {
	color: " . grve_option( 'footer_widgets_font_color' ) . ";
}

#grve-footer .widget .tribe-mini-calendar-event,
#grve-footer table,
#grve-footer td,
#grve-footer th {
	border-color: " . grve_option( 'footer_widgets_border_color' ) . ";
}

#grve-footer .widget .tribe-mini-calendar-event .list-date,
#grve-footer .widget .tribe-mini-calendar th {
	background-color: " . grve_option( 'footer_widgets_border_color' ) . ";
}

";

/**
* Typography
* ----------------------------------------------------------------------------
*/

$css .= "

.widget .tribe-mini-calendar-event .list-info {
	font-size: " . grve_option( 'body_font', '14px', 'font-size'  ) . ";
	font-family: " . grve_option( 'body_font', 'Arial, Helvetica, sans-serif', 'font-family'  ) . ";
	font-weight: " . grve_option( 'body_font', 'normal', 'font-weight'  ) . ";
}

#tribe-bar-form .tribe-bar-submit input[type=submit],
.grve-widget .entry-title,
.widget .tribe-mini-calendar-nav td,
.widget .tribe-countdown-text,
#tribe-events-content .tribe-events-calendar div[id*=tribe-events-event-] h3.tribe-events-month-event-title {
	font-family: " . grve_option( 'link_text', 'Arial, Helvetica, sans-serif', 'font-family'  ) . ";
	font-weight: " . grve_option( 'link_text', 'normal', 'font-weight'  ) . ";
	font-style: " . grve_option( 'link_text', 'normal', 'font-style'  ) . ";
	font-size: " . grve_option( 'link_text', '11px', 'font-size'  ) . " !important;
	text-transform: " . grve_option( 'link_text', 'uppercase', 'text-transform'  ) . ";
}

";

/* Trim css for speed */
$css_trim =  preg_replace( '/\s+/', ' ', $css );

/* Add stylesheet Tag */
$css_out = "<!-- Dynamic css -->\n<style type=\"text/css\">\n" . $css_trim . "\n</style>";

echo $css_out;

?>
