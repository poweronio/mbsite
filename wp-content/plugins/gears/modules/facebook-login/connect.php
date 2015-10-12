<?php
/**
 * The handler for our Facebook Connect API
 *
 * @package  klein
 * @subpackage gears
 * @version  2
 */

defined( 'ABSPATH' ) || exit;

session_start();

require GEARS_APP_PATH . 'modules/facebook-login/autoload.php';

use Facebook\FacebookSession;
use Facebook\FacebookRedirectLoginHelper;
use Facebook\GraphUser;
use Facebook\FacebookRequest;

FacebookSession::setDefaultApplication($this->appID, $this->appSecret);
$helper = new FacebookRedirectLoginHelper($this->redirectUrl);

try {
	$session = $helper->getSessionFromRedirect();
} catch( FacebookRequestException $ex ) {
	# echo $ex->getMessage();
	wp_safe_redirect(wp_login_url().'?error=true&type=fb_error');
	return;
} catch( Exception $ex ) {
	wp_safe_redirect(wp_login_url().'?error=true&type=app_not_live');
	return;
}

// see if we have a session
if ( isset( $session ) ) {
	
    // graph api request for user data
	$request = new FacebookRequest( $session, 'GET', '/me' );
	$response = $request->execute();

    // get response
	$graphObject = $response->getGraphObject()->asArray();

    // print data
	// echo '<pre>' . print_r( $graphObject, 1 ) . '</pre>';

	$userEmail = $graphObject['email'];
	$userFirstName = $graphObject['first_name'];
	$userLastName = $graphObject['last_name'];
	$userCompleteName = $graphObject['name'];

	// no email
	if (empty($userEmail)) {

		session_destroy();
		wp_safe_redirect(wp_login_url().'?error=true&type=fb_invalid_email');

		return;
	}

	// user must have atleast firstname and lastname
	if (!empty($userLastName) || !empty($userFirstName)) {

		$proposedUserName = sanitize_title(sprintf('%s-%s', $userFirstName, $userLastName));

		$userIDByEmail = email_exists($userEmail);

		// if user email exists, log the user
		if ($userIDByEmail) {
			
			$user = get_user_by('id', $userIDByEmail);

			if ($user) {
				wp_set_auth_cookie ($user->ID);
				// if buddypress is enabled redirect to its profile
				if (function_exists('bp_loggedin_user_domain')) {			
					wp_safe_redirect(bp_core_get_user_domain($user->ID));
				} else {
				// else just redirect to homepage
				wp_safe_redirect(get_bloginfo('url'));
				}
			} else {
				wp_safe_redirect(home_url());
			}
		} else {
		// if user email does not exists, create the user
			$password = wp_generate_password();
			// find available user name
			$username = $this->sanitizeUserName($username = $proposedUserName, $index = 1, $copy = $proposedUserName);
			// create the user
			$userID = wp_create_user( $username, $password, $userEmail );

			if (is_numeric($userID)) {

				//email the user his credentials
				wp_new_user_notification($userID, $password );
				wp_set_auth_cookie ( $userID );
				wp_update_user(
					array(
						'ID' => $userID,
						'display_name' => $userCompleteName,
						)
					);
						// update buddypress profile
				if (function_exists('xprofile_set_field_data')) {
					xprofile_set_field_data('Name', $userID, $userCompleteName);
				}

				if( function_exists( 'bp_loggedin_user_domain' ) ){
					wp_safe_redirect( bp_core_get_user_domain( $userID ) );
				}else{
					//else just redirect to back to homepage
					wp_safe_redirect( get_bloginfo( 'url' ) );
				}
			}else{
				session_destroy();
				wp_safe_redirect( wp_login_url() . '?error=true&type=gears_username_or_email_exists');
				return;
			}
		}

	} else {
		
		// Invalid Facebook User found
		session_destroy();
		wp_safe_redirect(wp_login_url().'?error=true&type=fb_error');
		
		return;
	}

	session_destroy();

} else {
  // show login url
  	if (isset($_GET['error'])) {
  		session_destroy();
  		wp_safe_redirect(home_url());
  	} else {
  		header('location:' . $helper->getLoginUrl(array('email')));
  	}
}