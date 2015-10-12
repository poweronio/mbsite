<?php
/*
*	Greatives Visual Composer Shortcode helper functions
*
* 	@author		Greatives Team
* 	@URI		http://greatives.eu
*/


function grve_vc_social_elements_visibility() {
	$visibility = apply_filters( 'grve_vc_social_elements_visibility', false );
	return $visibility;
}
function grve_vc_wp_elements_visibility() {
	$visibility = apply_filters( 'grve_vc_wp_elements_visibility', false );
	return $visibility;
}
function grve_vc_grid_visibility() {
	$visibility = apply_filters( 'grve_vc_grid_visibility', false );
	return $visibility;
}
function grve_vc_other_elements_visibility() {
	$visibility = apply_filters( 'grve_vc_other_elements_visibility', false );
	return $visibility;
}

function grve_build_margin_bottom_style( $margin_bottom ) {
	$style = '';
	if( $margin_bottom != '' ) {
		$style .= 'margin-bottom: '.(preg_match('/(px|em|\%|pt|cm)$/', $margin_bottom) ? $margin_bottom : $margin_bottom.'px').';';
	}
	return $style;
}

function grve_build_shortcode_img_style( $bg_image = '' , $bg_image_type = '' ) {

	$has_image = false;
	$style = '';

	if((int)$bg_image > 0 && ($attachment_src = wp_get_attachment_image_src( $bg_image, 'grve-image-fullscreen' )) !== false) {

		$image_url = $attachment_src[0];

		$has_image = true;
		$style .= "background-image: url(".$image_url.");";
		return ' style="'.$style.'"';
	}

}

function grve_vc_get_css_color( $prefix, $color ) {
	$rgb_color = preg_match( '/rgba/', $color ) ? preg_replace( array( '/\s+/', '/^rgba\((\d+)\,(\d+)\,(\d+)\,([\d\.]+)\)$/' ), array( '', 'rgb($1,$2,$3)' ), $color ) : $color;
	$string = $prefix . ':' . $rgb_color . ';';
	if ( $rgb_color !== $color ) $string .= $prefix . ':' . $color . ';';
	return $string;
}

function grve_vc_shortcode_custom_css_class( $param_value, $prefix = '' ) {
	$css_class = preg_match( '/\s*\.([^\{]+)\s*\{\s*([^\}]+)\s*\}\s*/', $param_value ) ? $prefix . preg_replace( '/\s*\.([^\{]+)\s*\{\s*([^\}]+)\s*\}\s*/', '$1', $param_value ) : '';
	return $css_class;
}

function grve_build_shortcode_style( $bg_color = '', $font_color = '', $padding_top = '', $padding_bottom = '', $margin_bottom = '') {

	$style = '';

	if(!empty($bg_color)) {
		$style .= grve_vc_get_css_color( 'background-color', $bg_color );
	}

	if( !empty($font_color) ) {
		$style .= grve_vc_get_css_color( 'color', $font_color );
	}
	if( $padding_top != '' ) {
		$style .= 'padding-top: '.(preg_match('/(px|em|\%|pt|cm)$/', $padding_top) ? $padding_top : $padding_top.'px').';';
	}
	if( $padding_bottom != '' ) {
		$style .= 'padding-bottom: '.(preg_match('/(px|em|\%|pt|cm)$/', $padding_bottom) ? $padding_bottom : $padding_bottom.'px').';';
	}
	if( $margin_bottom != '' ) {
		$style .= 'margin-bottom: '.(preg_match('/(px|em|\%|pt|cm)$/', $margin_bottom) ? $margin_bottom : $margin_bottom.'px').';';
	}
	return empty($style) ? $style : ' style="'.$style.'"';
}



if ( !grve_vc_grid_visibility() ) {

	//Remove Builder Grid Menu
	function grve_remove_vc_menu_items( ){
		remove_menu_page( 'edit.php?post_type=vc_grid_item' );
		remove_submenu_page( 'vc-general', 'edit.php?post_type=vc_grid_item' );
	}
	add_filter( 'admin_menu', 'grve_remove_vc_menu_items' );

	//Remove grid element shortcodes
	function grve_vc_remove_shortcodes_from_vc_grid_element( $shortcodes ) {
		unset( $shortcodes['vc_icon'] );
		unset( $shortcodes['vc_button2'] );
		unset( $shortcodes['vc_btn'] );
		unset( $shortcodes['vc_custom_heading'] );
		unset( $shortcodes['vc_single_image'] );
		unset( $shortcodes['vc_empty_space'] );
		unset( $shortcodes['vc_separator'] );
		unset( $shortcodes['vc_text_separator'] );
		unset( $shortcodes['vc_gitem_post_title'] );
		unset( $shortcodes['vc_gitem_post_excerpt'] );
		unset( $shortcodes['vc_gitem_post_date'] );
		unset( $shortcodes['vc_gitem_image'] );
		unset( $shortcodes['vc_gitem_post_meta'] );

	  return $shortcodes;
	}
	add_filter( 'vc_grid_item_shortcodes', 'grve_vc_remove_shortcodes_from_vc_grid_element', 100 );
}
//Remove all default templates.
//add_filter( 'vc_load_default_templates', 'grve_remove_custom_template_modify_array' );
function grve_remove_custom_template_modify_array( $data ) {
	return array();
}
?>