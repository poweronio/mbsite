<?php
/**
 * Default Events Template
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

get_header(); ?>

<?php
	$grve_event_area_class = '';
	$event_style = grve_option( 'event_style', 'default' );

	if ( 'simple' == $event_style && is_singular() ) {
		$grve_event_area_class = 'grve-simple-style';
	}

?>
	<div id="grve-main-content" class="<?php echo esc_attr( $grve_event_area_class ); ?>">

		<?php
			if ( grve_events_calendar_is_overview() ) {
				grve_print_header_title( 'event-tax' );
			} else {
				if ( 'default' == $event_style ) {
					grve_print_header_title( 'event' );
					if ( is_singular( 'tribe_events' ) ) {
					?>
					<div id="grve-event-bar" class="grve-fields-bar">
						<?php grve_print_header_item_event_navigation(); ?>
					</div>
					<?php
					}
				} else {
					if ( is_singular( 'tribe_events' ) ) {
						grve_print_header_item_event_navigation('grve-nav-wrapper-classic');
					}
				}

			}

		?>
		<div class="grve-container <?php echo grve_sidebar_class( 'event' ); ?>">
			<div id="grve-content-area">
				<?php tribe_events_before_html(); ?>
				<?php tribe_get_view(); ?>
				<?php tribe_events_after_html(); ?>
			</div>
			<?php grve_set_current_view( 'event' ); ?>
			<?php get_sidebar(); ?>
		</div>

	</div>
<?php get_footer(); ?>