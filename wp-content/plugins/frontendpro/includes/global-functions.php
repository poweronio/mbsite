<?php

/**
 * Creates an associative array of roles in which each element has role slug as the key and role name as value e.g. 'administrator' => 'Administrator'
 *
 * @return array Array of roles.
 **/
function wpfepp_get_roles(){
	global $wp_roles;
	$roles = $wp_roles->roles;
	$rtn_arr 	= array();
	foreach ($roles as $key => $role) {
		$rtn_arr[$key] = $role['name'];
	}
	return $rtn_arr;
}

/**
 * Checks if the current user has a role for which the value of in the passed array is 1.
 *
 * @var array $roles An array in which the value corresponsponding each role is either 1 or 0.
 * @return bool A boolean variable indicating whether the current user has a role for which the value is 1.
 **/
function wpfepp_current_user_has( $roles ){
	global $current_user;
	get_currentuserinfo();

	foreach ( $current_user->roles as $role ) {
		if( isset($roles[$role]) && $roles[$role] )
			return true;
	}
	return false;
}

/**
 * Prints a list of roles in our special checkbox format.
 *
 * @var string $name Value for the name attribute of the checkboxes.
 * @var array $checked An array indicating which roles to check.
 **/
function wpfepp_print_roles_checkboxes($name, $checked){
	$roles = wpfepp_get_roles();
	?>
		<?php foreach ($roles as $key => $role): ?>
			<input type="hidden" name="<?php echo $name; ?>[<?php echo $key; ?>]" value="0" />
			<input type="checkbox" id="<?php echo $name; ?>[<?php echo $key; ?>]" name="<?php echo $name; ?>[<?php echo $key; ?>]" value="1" <?php if( isset($checked[$key]) ) checked( $checked[$key] ); ?> />
			<label for="<?php echo $name; ?>[<?php echo $key; ?>]"><?php echo $role; ?></label>
			<br/>
		<?php endforeach; ?>
	<?php
}

/**
 * Fetches an array of roles from wpfepp_get_roles() and convert it into a settings array.
 *
 * @return array An array of settings e.g. array( 'administrator' => '1', editor => '0' ... )
 **/
function wpfepp_prepare_default_role_settings(){
	$rtn_arr = array();
	global $wp_roles;
	$roles = $wp_roles->roles;
	foreach ($roles as $key => $role) {
		$rtn_arr[$key] = false;
	}
	return $rtn_arr;
}

/**
 * A recursive function that checks an array for missing keys. If any are found, inserts default values from the second array.
 *
 * @var array $current The array to be checked.
 * @var array $default The array from which we can get the missing values.
 * @return array The patched array.
 **/
function wpfepp_update_array($current, $default) {
	$current = ($current && is_array($current)) ? $current : array();
	foreach ($default as $key => $value) {
		if( !array_key_exists($key, $current) ){
			$current[$key] = $value;
		}
		elseif( is_array( $value ) ){
			$current[$key] = wpfepp_update_array( $current[$key], $value );
		}
	}
	return $current;
}

function wpfepp_update_form_fields($current, $default, $default_custom){

	$current = wpfepp_update_array($current, $default);
	foreach ($current as $key => $field) {
		if($field['type'] == 'custom_field')
			$current[$key] = wpfepp_update_array($field, $default_custom);
	}
	return $current;
}

/**
 * Checks to see if a field is supported by the current post type and theme.
 *
 * @var string $post_type The post type of the current form.
 * @var string $field_type The type of field we want to check.
 * @return bool A boolean variable indicating whether or not the field is supported.
 **/
function wpfepp_is_field_supported($field_type, $post_type){
	if($field_type == 'thumbnail' ) {
		return ( post_type_supports($post_type, 'thumbnail') && get_theme_support('post-thumbnails') );
	}
	elseif($field_type == 'post_format') {
		$formats = get_theme_support('post-formats');
		return ( post_type_supports($post_type, 'post-formats') && is_array($formats) && count($formats) && is_array($formats[0]) && count($formats[0]) );
	}
	elseif($field_type == 'content'){
		return post_type_supports($post_type, 'editor');
	}
	elseif($field_type == 'title' || $field_type == 'excerpt') {
		return post_type_supports($post_type, $field_type);
	}
	return true;
}

function wpfepp_choices($str){
	$choices = array();

	if(empty($str))
		return $choices;

	$lines = explode("\n", $str);
	$count = 0;
	foreach ($lines as $line) {
		if(!empty($line)){
			$line_val = explode("|", $line);
			if(count($line_val) > 1){
				$choices[$count]['key'] = $line_val[0];
				$choices[$count]['val'] = $line_val[1];
			}
			else{
				$choices[$count]['key'] = $line_val[0];
				$choices[$count]['val'] = $line_val[0];
			}
			$count++;
		}
	}
	return $choices;
}

/**
 * Output the html of a form and includes the necessary scripts and stylesheets.
 *
 * @var int $form_id Form ID.
 * @author 
 **/
function wpfepp_submission_form($form_id) {
	echo do_shortcode( sprintf('[wpfepp_submission_form form="%s"]', $form_id) );
}

/**
 * Output the html of a post table and includes the necessary scripts and stylesheets.
 *
 * @var int $form_id Form ID.
 * @author
 **/
function wpfepp_post_table($form_id) {
	echo do_shortcode( sprintf('[wpfepp_post_table form="%s"]', $form_id) );
}

function wpfepp_get_post_types(){
	$types = get_post_types( array('show_ui'=>true), 'names', 'and' );
	unset($types['attachment']);
	return $types;
}

function wpfepp_get_post_type_settings(){
	$settings = array();
	$types = wpfepp_get_post_types();
	foreach ($types as $key => $type) {
		$settings[$type] = false;
	}
	return $settings;
}

if( !function_exists('ot_get_media_post_ID') ) {
	function ot_get_media_post_ID() {
		return -1;
	}
}

?>