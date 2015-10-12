<?php
/*
Plugin Name: BP Profile as Homepage Fork
Description: Sets the user BP Profile as homepage on the site for logged in users. This emulates Facebook.
Author: Mort3n
Version: 1.1.3
License: GPL2
*/

/*  Copyright 2013  Mort3n

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/**
 * Redirect logged-in user from site Homepage to Profile
 * @since 1.1.1
 */
 function bp_profile_as_homepage_fork(){

	$selected_role = get_option( 'bpahpf_role_choice' );
	if( is_user_logged_in() && is_front_page() ){
		//redirection to profile applied to all roles
		if( '' == $selected_role ){
			wp_redirect( bp_core_get_user_domain( bp_loggedin_user_id() ) );
			exit;
		}
		//redirection to profile applied to all roles EXCEPT selected_role
		else{
			if( !bp_profile_as_homepage_fork_check_user_role( $selected_role, get_current_user_id() ) ){
				wp_redirect( bp_core_get_user_domain( bp_loggedin_user_id() ) );
				exit;
			}
		}
	}
}
 
 /**
  * Redirect a user who logs in to the profile
  * @since 1.1
  */
function bp_profile_as_homepage_fork_login( $redirect_to, $request, $user ){

	$selected_role = get_option( 'bpahpf_role_choice' );
	
	//default target URL
	$redirect_to = home_url();
	
	//redirection to profile applied to all roles
	if( '' == $selected_role ){
		$redirect_to =  bp_core_get_user_domain( $user->ID); 
	}
	//redirection to profile applied to all roles EXCEPT selected_role
	else{
		if( !bp_profile_as_homepage_fork_check_user_role( $selected_role, $user->ID ) ){
			$redirect_to = bp_core_get_user_domain( $user->ID);
		}
	}
	return $redirect_to;
}

/**
 * Redirect after logout
 * @since 1.1 
 */
function bp_profile_as_homepage_fork_logout_redirection(){
	wp_redirect( home_url() );
	exit;
}

/**
 * Checks if a particular user has a role. 
 * Returns true if a match was found.
 * Code for this function modified from http://docs.appthemes.com/tutorials/wordpress-check-user-role-function/
 *
 * @param string $role Role name.
 * @param int $user_id (Optional) The ID of a user. Defaults to the current user.
 * @return bool
 */
function bp_profile_as_homepage_fork_check_user_role( $role, $user_id = null ) {

    if ( is_numeric( $user_id ) )
		$my_user = get_userdata( $user_id );
    else
        $my_user = get_current_user();
 
    if ( empty( $my_user ) )
		return false;
	
	return in_array( $role, (array) $my_user->roles );
}

/**
 * Add the admin page to the dashboard menu
 * @since 1.0 
 */
function bp_profile_as_homepage_fork_menu(){

	load_plugin_textdomain( 'bpahpf-menu', false, basename( dirname( __FILE__ ) ) . '/languages' );
	add_options_page( __('BP Profile as Homepage Fork Settings', 'bpahpf-menu' ), __( 'BP Profile as Homepage Fork Settings','bpahpf-menu'), 'manage_options', 'bpahpfmenu', 'bpahpf_settings_page');
}

/**
 * Outputs the settings screen and saves data when submitted
 * @since 1.0 
 */
function bpahpf_settings_page(){

	//check for capability to manage options
	if ( !current_user_can( 'manage_options' ) ){
	
      wp_die( __('You do not have sufficient permissions to access this page.', 'bpahpf-menu' ) );
	  
    }
	
	$opt_name = 'bpahpf_role_choice';
	$data_field_name = 'bpahpf_role_choice';
	
	$opt_val = get_option( $opt_name );
	
	//if nonce checks out then save submitted data
	$nonce=$_REQUEST['_wpnonce'];
	if( wp_verify_nonce( $nonce, 'bpahpf' ) )
	{
		$opt_val = $_POST[ $data_field_name ];
		update_option( $opt_name, $opt_val );
		?>
		<div class="updated"><p><strong><?php _e( 'Settings saved.', 'bpahpf-menu' ); ?></strong></p></div>
		<?php

    }
	
    echo '<div class="wrap">';
	echo "<h2>" . __( 'BP Profile as Homepage Fork Settings', 'bpahpf-menu' ) . "</h2>";
	?>
	<p>
	<?php _e( 'Disable Profile as Homepage for a particular user role.', 'bpahpf-menu' ); ?>
	</p>
	<form name="bpahpf-settings-form" method="post" action="">
	<?php wp_nonce_field( 'bpahpf' ); ?>
	<p><b>
	<?php _e( 'You have selected:', 'bpahpf-menu' ); ?>
	</b> 
	<?php 
		if ( '' == $opt_val )
			_e( 'No One', 'bpahpf-menu' );
		else
			echo $opt_val; 
	?> 
		<hr />
	<?php _e( 'Disable redirect to Profile for this role :', 'bpahpf-menu' ); ?> 
		<select name="<?php echo $data_field_name; ?>">
			<option value="">
				<?php _e( 'No One', 'bpahpf-menu' ); ?>
			</option>
			<?php wp_dropdown_roles( );?>
		</select>
	</p>
<p class="submit">
<input type="submit" name="Submit" class="button-primary" value="<?php esc_attr_e( 'Save Changes', 'bpahpf-menu' ) ?>" />
</p>
</form>
</div>
<?php
}	

add_action( 'admin_menu','bp_profile_as_homepage_fork_menu' );
add_filter( 'login_redirect', 'bp_profile_as_homepage_fork_login', 10, 3 );
add_action( 'wp', 'bp_profile_as_homepage_fork' );
add_action( 'wp_logout','bp_profile_as_homepage_fork_logout_redirection' );
?>