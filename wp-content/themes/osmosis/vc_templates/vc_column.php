<?php
	$tablet_class = $el_class = '';

	extract(
		shortcode_atts(
			array(
				'width' => '1/1',
				'font_color' => '',
				'desktop_hide' => '',
				'tablet_width' => '',
				'tablet_sm_width' => '',
				'mobile_width' => '',
				'el_class' => '',
				'offset' => '',
				'css' => '',
			),
			$atts
		)
	);

	switch( $width ) {
		case '1/12':
			$shortcode_column = '1-12';
			break;
		case '1/6':
			$shortcode_column = '1-6';
			break;
		case '1/4':
			$shortcode_column = '1-4';
			break;
		case '1/3':
			$shortcode_column = '1-3';
			break;
		case '5/12':
			$shortcode_column = '5-12';
			break;
		case '1/2':
			$shortcode_column = '1-2';
			break;
		case '7/12':
			$shortcode_column = '7-12';
			break;
		case '2/3':
		case '4/6':
			$shortcode_column = '2-3';
			break;
		case '3/4':
			$shortcode_column = '3-4';
			break;
		case '5/6':
			$shortcode_column = '5-6';
			break;
		case '11/12':
			$shortcode_column = '11-12';
			break;
		case '1/1':
		default :
			$shortcode_column = '1';
			break;
	}

	$css_custom = grve_vc_shortcode_custom_css_class( $css, '' );
	$style = grve_build_shortcode_style( '', $font_color );

	$column_classes = array( 'wpb_column' );

	array_push( $column_classes, 'grve-column-' . $shortcode_column );

	if ( !empty( $css_custom ) ) {
		array_push( $column_classes, $css_custom );
	}

	if( vc_settings()->get( 'not_responsive_css' ) != '1') {

		if ( !empty( $desktop_hide ) ) {
			array_push( $column_classes, 'grve-desktop-column-' . $desktop_hide );
		}
		if ( !empty( $tablet_width ) ) {
			array_push( $column_classes, 'grve-tablet-column-' . $tablet_width );
		}
		if ( !empty( $tablet_sm_width ) ) {
			if ( !empty( $tablet_width ) ) {
				array_push( $column_classes, 'grve-tablet-sm-column-' . $tablet_sm_width );
			} else {
				array_push( $column_classes, 'grve-tablet-sm-column-' . $tablet_width );
			}
		}
		if ( !empty( $mobile_width ) ) {
			array_push( $column_classes, 'grve-mobile-column-' . $mobile_width );
		}
	}

	if ( !empty ( $el_class ) ) {
		array_push( $column_classes, $el_class);
	}
	if ( !empty ( $responsive_class ) ) {
		array_push( $column_classes, $responsive_class);
	}

	$column_string = implode( ' ', $column_classes );

	echo '
		<div class="' . $column_string . '"' . $style . '>
			' . do_shortcode( $content ) . '
		</div>
	';
?>