<?php
/**
 *  Dynamic css style for bbPress Forum
 * 	@author		Greatives Team
 * 	@URI		http://greatives.eu
 */

$css = "";

/**
 * Forum Title
 * ----------------------------------------------------------------------------
 */
$css .= "

#grve-page-title.grve-forum-title {
	background-color: " . grve_option( 'forum_title_background_color' ) . ";
}

";


/**
 * Text Colors
 * ----------------------------------------------------------------------------
 */
$css .= "

#bbpress-forums #bbp-single-user-details #bbp-user-navigation a,
#bbpress-forums .status-closed, #bbpress-forums .status-closed a {
	color: " . grve_option( 'body_text_color' ) . ";
}

";

/**
 * Headings Colors
 * ----------------------------------------------------------------------------
 */
$css .= "

#grve-main-content .grve-widget.widget_display_topics li div,
#grve-main-content .grve-widget.widget_display_replies li div {
	color: " . grve_option( 'body_heading_color' ) . ";
}


#grve-footer-area .grve-widget.widget_display_topics li div,
#grve-footer-area .grve-widget.widget_display_replies li div {
	color: " . grve_option( 'footer_widgets_headings_color' ) . ";
}

";

/**
 * Primary #1 Colors
 * ----------------------------------------------------------------------------
 */
$css .= "

#bbpress-forums #bbp-single-user-details #bbp-user-navigation a:hover,
#bbpress-forums #bbp-single-user-details #bbp-user-navigation .current a {
	color: " . grve_option( 'body_primary_1_color' ) . ";
}

";

/**
 * Main Border Colors
 * ----------------------------------------------------------------------------
 */
$css .= "

#grve-main-content #bbpress-forums #bbp-single-user-details,
#grve-main-content #bbpress-forums #bbp-your-profile fieldset span.description,
#bbpress-forums li.bbp-body ul.forum,
#bbpress-forums li.bbp-body ul.topic,
#bbpress-forums ul.bbp-lead-topic,
#bbpress-forums ul.bbp-topics,
#bbpress-forums ul.bbp-forums,
#bbpress-forums ul.bbp-replies,
#bbpress-forums ul.bbp-search-results,
#bbpress-forums .bbp-forums-list,
#bbpress-forums div.bbp-the-content-wrapper textarea.bbp-the-content,
.bbp-pagination-links a,
.bbp-pagination-links span.current,
#bbpress-forums div.bbp-forum-header,
#bbpress-forums div.bbp-topic-header,
#bbpress-forums div.bbp-reply-header,
#grve-main-content .grve-widget.widget_display_stats dd,
#grve-main-content .bbp_widget_login fieldset {
	border-color: " . grve_option( 'body_border_color' ) . ";
}

#grve-footer-area .grve-widget.widget_display_stats dd,
#grve-footer-area .bbp_widget_login fieldset {
	border-color: " . grve_option( 'footer_widgets_border_color' ) . ";
}

";

/**
 * Typography
 * ----------------------------------------------------------------------------
 */
$css .= "
.grve-widget.widget_display_topics li div,
.grve-widget.widget_display_replies li div {
	font-family: " . grve_option( 'small_text', 'Arial, Helvetica, sans-serif', 'font-family'  ) . ";
	font-weight: " . grve_option( 'small_text', 'normal', 'font-weight'  ) . ";
	font-style: " . grve_option( 'small_text', 'normal', 'font-style'  ) . ";
	font-size: " . grve_option( 'small_text', '10px', 'font-size'  ) . " !important;
	text-transform: " . grve_option( 'small_text', 'uppercase', 'text-transform'  ) . ";
	" . grve_css_option( 'small_text', '', 'letter-spacing'  ) . "
}

";

/* Trim css for speed */
$css_trim =  preg_replace( '/\s+/', ' ', $css );

/* Add stylesheet Tag */
$css_out = "<!-- Dynamic css -->\n<style type=\"text/css\">\n" . $css_trim . "\n</style>";

echo $css_out;

?>


