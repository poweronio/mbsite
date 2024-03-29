<?php
/**
 * Progress Bar Shortcode
 */

if( !function_exists( 'grve_progress_bar_shortcode' ) ) {

	function grve_progress_bar_shortcode( $atts, $content ) {

		$output = $style = $bar_height_style = $el_class = '';

		extract(
			shortcode_atts(
				array(
					'bar_style' => 'style-1',
					'values' => '',
					'bar_line_style' => 'square',
					'bar_height' => '35',
					'color' => 'primary',
					'margin_bottom' => '',
					'el_class' => '',
				),
				$atts
			)
		);

		$bars_classes = array( 'grve-element', 'grve-progress-bars' );
		array_push( $bars_classes, 'grve-' . $bar_style );
		array_push( $bars_classes, 'grve-line-' . $bar_line_style );

		$bars_class_string = implode( ' ', $bars_classes );

		if( !empty( $bar_height ) && 'style-2' == $bar_style ) {
			$bar_height_style .= 'height: '.(preg_match('/(px|em|\%|pt|cm)$/', $bar_height) ? $bar_height : $bar_height.'px').';';
		}

		$style .= grve_osmosis_vce_build_margin_bottom_style( $margin_bottom );

		$graph_lines = explode(",", $values);

		$graph_lines_data = array();
		foreach ($graph_lines as $line) {
			$new_line = array();
			$data = explode("|", $line);
			$new_line['value'] = isset( $data[0] ) && !empty( $data[0] ) ? $data[0] : 0;
			$new_line['percentage_value'] = isset( $data[1] ) && !empty( $data[1] ) ? $data[1] : '';
			$new_line['color'] = isset( $data[2] ) && !empty( $data[2] ) ? $data[2] : $color;

			if( (float)$new_line['value'] < 0 ) {
				$new_line['value'] = 0;
			} else if ( (float)$new_line['value'] > 100 ) {
				$new_line['value'] = 100;
			}

			$new_line['label'] = $new_line['percentage_value'];

			$graph_lines_data[] = $new_line;
		}

		$output .= '<div class="' . esc_attr( $bars_class_string ) . '" style="' . $style . '">';

		foreach($graph_lines_data as $line) {

			$color_class = 'grve-primary';
			if ( 'primary' != $line['color'] ) {
				$color_class = 'grve-bg-' . $line['color'];
			}

			$output .= '<div class="grve-element grve-progress-bar grve-margin-10" data-value="' .  esc_attr( $line['value'] ) . '">';
			$output .= '  <div class="grve-bar-title">' .  $line['label'] . '</div>';
			$output .= '  <div class="grve-bar">';
			$output .= '    <div class="grve-bar-line ' .  esc_attr( $color_class ) . '" " style="' . $bar_height_style . '"></div>';
			$output .= '  </div>';
			$output .= '</div>';

		}

		$output .= '</div>';

		return $output;
	}
	add_shortcode( 'grve_progress_bar', 'grve_progress_bar_shortcode' );

}

/**
 * Add shortcode to Visual Composer
 */

vc_map( array(
	"name" => __( "Progress Bar", "grve-osmosis-vc-extension" ),
	"description" => __( "Create horizontal progress bar", "grve-osmosis-vc-extension" ),
	"base" => "grve_progress_bar",
	"class" => "",
	"icon"      => "icon-wpb-grve-progress-bar",
	"category" => __( "Content", "js_composer" ),
	"params" => array(
		array(
			"type" => "dropdown",
			"heading" => __( "Progress Bar Style", "grve-osmosis-vc-extension" ),
			"param_name" => "bar_style",
			"value" => array(
				__( "Style 1", "grve-osmosis-vc-extension" ) => 'style-1',
				__( "Style 2", "grve-osmosis-vc-extension" ) => 'style-2',
			),
			"description" => __( "Style of the bar line.", "grve-osmosis-vc-extension" ),
		),
		array(
			"type" => "exploded_textarea",
			"heading" => __("Bar values", "grve-osmosis-vc-extension"),
			"param_name" => "values",
			"description" => __( "Input bar values here. Divide values with linebreaks (Enter). Example: 90|Development|black.", "grve-osmosis-vc-extension" ) . '<br/>' .
							__( "Available colors: primary-1, green, orange, red, blue, aqua, purple, black, white.", "grve-osmosis-vc-extension" ),
			"value" => "90|Development,80|Design,70|Marketing",
			"admin_label" => true,
		),
		array(
			"type" => "dropdown",
			"heading" => __( "Bars Color", "grve-osmosis-vc-extension" ),
			"param_name" => "color",
			"value" => array(
				__( "Primary 1", "grve-osmosis-vc-extension" ) => 'primary-1',
				__( "Primary 2", "grve-osmosis-vc-extension" ) => 'primary-2',
				__( "Primary 3", "grve-osmosis-vc-extension" ) => 'primary-3',
				__( "Primary 4", "grve-osmosis-vc-extension" ) => 'primary-4',
				__( "Primary 5", "grve-osmosis-vc-extension" ) => 'primary-5',
				__( "Green", "grve-osmosis-vc-extension" ) => 'green',
				__( "Orange", "grve-osmosis-vc-extension" ) => 'orange',
				__( "Red", "grve-osmosis-vc-extension" ) => 'red',
				__( "Blue", "grve-osmosis-vc-extension" ) => 'blue',
				__( "Aqua", "grve-osmosis-vc-extension" ) => 'aqua',
				__( "Purple", "grve-osmosis-vc-extension" ) => 'purple',
				__( "Black", "grve-osmosis-vc-extension" ) => 'black',
				__( "Grey", "grve-osmosis-vc-extension" ) => 'grey',
			),
			"description" => __( "Use single color for all bars ( If not specified in Bar values )", "grve-osmosis-vc-extension" ),
		),
		array(
			"type" => "textfield",
			"heading" => __( "Progress Bar Height", "grve-osmosis-vc-extension" ),
			"param_name" => "bar_height",
			"value" => "35",
			"description" => __( "Enter progress bar height.", "grve-osmosis-vc-extension" ),
			"admin_label" => true,
			"dependency" => Array( 'element' => "bar_style", 'value' => array( 'style-2' ) ),
		),
		array(
			"type" => "dropdown",
			"heading" => __( "Bars Line Style", "grve-osmosis-vc-extension" ),
			"param_name" => "bar_line_style",
			"value" => array(
				__( "Square", "grve-osmosis-vc-extension" ) => 'square',
				__( "Round", "grve-osmosis-vc-extension" ) => 'round',
			),
			"description" => __( "Style of the bar line.", "grve-osmosis-vc-extension" ),
		),
		$grve_vce_add_margin_bottom,
		$grve_vce_add_el_class,
	),
) );
?>