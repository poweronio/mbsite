<?php
/**
 * Pie Chart Shortcode
 */

if( !function_exists( 'grve_pie_chart_shortcode' ) ) {

	function grve_pie_chart_shortcode( $atts, $content ) {

		$output = $link_start = $link_end = $retina_data = $data = $el_class = '';

		extract(
			shortcode_atts(
				array(
					'pie_chart_val' => '50',
					'pie_chart_prefix' => '',
					'pie_chart_suffix' => '',
					'pie_chart_line_size' => '6',
					'pie_chart_color' => '',
					'pie_active_color' => '',
					'pie_line_style' => 'square',
					'title' => '',
					'pie_chart_text' => '',
					'animation' => '',
					'animation_delay' => '200',
					'margin_bottom' => '',
					'el_class' => '',
				),
				$atts
			)
		);

		$pie_chart_classes = array( 'grve-element' );

		array_push( $pie_chart_classes, 'grve-pie-chart' );

		if ( !empty( $animation ) ) {
			array_push( $pie_chart_classes, 'grve-animated-item' );
			array_push( $pie_chart_classes, $animation);
			$data = ' data-delay="' . esc_attr( $animation_delay ) . '"';
		}

		if ( !empty ( $el_class ) ) {
			array_push( $pie_chart_classes, $el_class);
		}

		$pie_chart_class_string = implode( ' ', $pie_chart_classes );


		$style = grve_osmosis_vce_build_margin_bottom_style( $margin_bottom );


		$output .= '<div class="' . esc_attr( $pie_chart_class_string ) . '" style="' . $style . '"' . $data . '>';
		$output .= '  <div class="grve-chart-number" data-percent="' . esc_attr( $pie_chart_val ) . '" data-pie-active-color="' . esc_attr( $pie_active_color ) . '" data-pie-color="' . esc_attr( $pie_chart_color ) . '" data-pie-line-cap="' . esc_attr( $pie_line_style ) . '" data-pie-line-size="' . esc_attr( $pie_chart_line_size ) . '">';
		$output .= '    <span class="grve-counter">' . $pie_chart_prefix . $pie_chart_val . $pie_chart_suffix . '</span>';
		$output .= '  </div>';
			if ( !empty( $title ) ) {
				$output .= '      <h5 class="grve-chart-title">' . $title. '</h5>';
			}
			if ( !empty( $pie_chart_text ) ) {
				$output .= '      <p>' . $pie_chart_text. '</p>';
			}
		$output .= '</div>';

		return $output;
	}
	add_shortcode( 'grve_pie_chart', 'grve_pie_chart_shortcode' );

}

/**
 * Add shortcode to Visual Composer
 */

vc_map( array(
	"name" => __( "Pie Chart", "grve-osmosis-vc-extension" ),
	"description" => __( "Add a counter with icon and title", "grve-osmosis-vc-extension" ),
	"base" => "grve_pie_chart",
	"class" => "",
	"icon"      => "icon-wpb-grve-pie-chart",
	"category" => __( "Content", "js_composer" ),
	"params" => array(
		array(
			"type" => "textfield",
			"heading" => __( "Pie Chart Value", "grve-osmosis-vc-extension" ),
			"param_name" => "pie_chart_val",
			"value" => "50",
			"description" => __( "Enter the pie chart value number.", "grve-osmosis-vc-extension" ),
			"admin_label" => true,
		),
		array(
			"type" => "textfield",
			"heading" => __( "Value Prefix", "grve-osmosis-vc-extension" ),
			"param_name" => "pie_chart_prefix",
			"value" => "",
			"description" => __( "Enter value prefix.", "grve-osmosis-vc-extension" ),
		),
		array(
			"type" => "textfield",
			"heading" => __( "Value Suffix", "grve-osmosis-vc-extension" ),
			"param_name" => "pie_chart_suffix",
			"value" => "",
			"description" => __( "Enter value suffix.", "grve-osmosis-vc-extension" ),
		),
		array(
			"type" => "textfield",
			"heading" => __( "Pie Chart Line Size", "grve-osmosis-vc-extension" ),
			"param_name" => "pie_chart_line_size",
			"value" => "6",
			"description" => __( "Enter pie chart line size.", "grve-osmosis-vc-extension" ),
		),
		array(
			"type" => "dropdown",
			"heading" => __( "Pie Chart Style", "grve-osmosis-vc-extension" ),
			"param_name" => "pie_line_style",
			"value" => array(
				__( "Square", "grve-osmosis-vc-extension" ) => 'square',
				__( "Round", "grve-osmosis-vc-extension" ) => 'round',
			),
			"description" => __( "Set the pie chart shape style", "grve-osmosis-vc-extension" ),
		),
		array(
			'type' => 'colorpicker',
			'heading' => __( "Pie Chart Active Color", "grve-osmosis-vc-extension" ),
			'param_name' => 'pie_active_color',
			'description' => __( "Select the active color for your pie", "grve-osmosis-vc-extension" ),
			'dependency'  => array(
				'element' => 'color',
				'value'   => array( 'custom' )
			),
		),
		array(
			'type' => 'colorpicker',
			'heading' => __( "Pie Chart Base Color", "grve-osmosis-vc-extension" ),
			'param_name' => 'pie_chart_color',
			'description' => __( "Select the base color for your pie", "grve-osmosis-vc-extension" ),
			'dependency'  => array(
				'element' => 'color',
				'value'   => array( 'custom' )
			),
		),
		array(
			"type" => "textfield",
			"heading" => __( "Title", "grve-osmosis-vc-extension" ),
			"param_name" => "title",
			"value" => "",
			"description" => __( "Enter pie chart title", "grve-osmosis-vc-extension" ),
			"admin_label" => true,
		),
		array(
			"type" => "textarea",
			"heading" => __( "Text", "grve-osmosis-vc-extension" ),
			"param_name" => "pie_chart_text",
			"value" => "",
			"description" => __( "Type your text", "grve-osmosis-vc-extension" ),
			"admin_label" => true,
		),
		$grve_vce_add_animation,
		$grve_vce_add_animation_delay,
		$grve_vce_add_margin_bottom,
		$grve_vce_add_el_class,
	)
) );

?>