<?php

function geodir_claim_listing_activation(){

	global $wpdb,$plugin_prefix;
	 
	if (get_option('geodir_installed')) {
		
		geodir_claim_activation_script();
	
		geodir_update_options(geodir_claim_notifications(), true);
		geodir_update_options(geodir_claim_default_options(), true);
		
		add_option('geodir_claim_listing_activation_redirect', 1);
		
	}
	
}

function geodir_claim_listing_uninstall(){
	
	if ( ! isset($_REQUEST['verify-delete-adon']) ) 
	{
		$plugins = isset( $_REQUEST['checked'] ) ? (array) $_REQUEST['checked'] : array();
			//$_POST = from the plugin form; $_GET = from the FTP details screen.
			
			wp_enqueue_script('jquery');
					require_once(ABSPATH . 'wp-admin/admin-header.php');
					printf( '<h2>%s</h2>' ,__( 'Warning!!' , GEODIRCLAIM_TEXTDOMAIN) );
					printf( '%s<br/><strong>%s</strong><br /><br />%s <a href="http://wpgeodirectory.com">%s</a>.' , __('You are about to delete a Geodirectory Adon which has important option and custom data associated to it.' ,GEODIRCLAIM_TEXTDOMAIN) ,__('Deleting this and activating another version, will be treated as a new installation of plugin, so all the data will be lost.', GEODIRCLAIM_TEXTDOMAIN), __('If you have any problem in upgrading the plugin please contact Geodirectroy', GEODIRCLAIM_TEXTDOMAIN) , __('support' ,GEODIRCLAIM_TEXTDOMAIN) ) ;
					
	?><br /><br />
		<form method="post" action="<?php echo esc_url($_SERVER['REQUEST_URI']); ?>" style="display:inline;">
						<input type="hidden" name="verify-delete" value="1" />
						<input type="hidden" name="action" value="delete-selected" />
						<input type="hidden" name="verify-delete-adon" value="1" />
						<?php
							foreach ( (array) $plugins as $plugin )
								echo '<input type="hidden" name="checked[]" value="' . esc_attr($plugin) . '" />';
						?>
						<?php wp_nonce_field('bulk-plugins') ?>
						<?php submit_button(  __( 'Delete plugin files only' , GEODIRCLAIM_TEXTDOMAIN ), 'button', 'submit', false ); ?>
					</form>
					<form method="post" action="<?php echo esc_url($_SERVER['REQUEST_URI']); ?>" style="display:inline;">
						<input type="hidden" name="verify-delete" value="1" />
						<input type="hidden" name="action" value="delete-selected" />
                        <input type="hidden" name="verify-delete-adon" value="1" />
						<input type="hidden" name="verify-delete-adon-data" value="1" />
						<?php
							foreach ( (array) $plugins as $plugin )
								echo '<input type="hidden" name="checked[]" value="' . esc_attr($plugin) . '" />';
						?>
						<?php wp_nonce_field('bulk-plugins') ?>
						<?php submit_button(  __( 'Delete both plugin files and data' , GEODIRCLAIM_TEXTDOMAIN) , 'button', 'submit', false ); ?>
					</form>
					
	<?php
		require_once(ABSPATH . 'wp-admin/admin-footer.php');
		exit;
	}
	
	
	if ( isset($_REQUEST['verify-delete-adon-data']) ) 
	{
	
		global $wpdb,$plugin_prefix;
		
		$post_types = geodir_get_posttypes();
		
		if(!empty($post_types)){
		
			foreach($post_types as $post_type){
				
				$table = $plugin_prefix.$post_type.'_detail';
				
				$wpdb->query("UPDATE ".$table." SET claimed=''");
				
			}	
			
		}
		
		$wpdb->query("DROP TABLE ".$plugin_prefix.'claim');
		
		
		/* --- delete notification options --- */
		
		$notifications = geodir_claim_notifications();
		
		if(!empty($notifications)){
			foreach($notifications as $value){
				if(isset($value['id']) && $value['id'] != '')
					delete_option($value['id'], '');
			}
		}
		
		/* --- delete default options --- */
		
		$default_options = geodir_claim_default_options();
		
		if(!empty($default_options)){
			foreach($default_options as $value){
				if(isset($value['id']) && $value['id'] != '')
					delete_option($value['id'], '');
			}
		}
	}
}


function geodir_claim_activation_script() {
	global $wpdb,$plugin_prefix;

	/**
	 * Include any functions needed for upgrades.
	 *
	 * @since 1.1.4
	 */
	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
	
	$wpdb->hide_errors();
	
	// rename tables if we need to
	if($wpdb->query("SHOW TABLES LIKE 'geodir_claim'")>0){$wpdb->query("RENAME TABLE geodir_claim TO ".$wpdb->prefix."geodir_claim");}
	
	
	$collate = '';
	if($wpdb->has_cap( 'collation' ) ) {
		if(!empty($wpdb->charset)) $collate = "DEFAULT CHARACTER SET $wpdb->charset";
		if(!empty($wpdb->collate)) $collate .= " COLLATE $wpdb->collate";
	}
	
	if($wpdb->get_var("SHOW TABLES LIKE '".GEODIR_CLAIM_TABLE."'") != GEODIR_CLAIM_TABLE){
	
		$claim_table = "CREATE TABLE IF NOT EXISTS `".GEODIR_CLAIM_TABLE."` (
			`pid` int(11) NOT NULL AUTO_INCREMENT,
			`list_id` varchar(255) NOT NULL,
			`list_title` varchar(255) NOT NULL,
			`user_id` varchar(255) NOT NULL,
			`user_name` varchar(255) NOT NULL,
			`user_email` varchar(255) NOT NULL,
			`user_fullname` varchar(255) NOT NULL,
			`user_number` varchar(255) NOT NULL,
			`user_position` varchar(255) NOT NULL,
			`user_comments` longtext NOT NULL,
			`admin_comments` longtext NOT NULL,
			`claim_date` varchar(255) NOT NULL,
			`org_author` varchar(255) NOT NULL,
			`org_authorid` varchar(255) NOT NULL,
			`rand_string` varchar(255) NOT NULL,
			`status` varchar(255) NOT NULL,
			`user_ip` varchar(255) NOT NULL,
			`upgrade_pkg_id` INT( 11 ) NOT NULL,
			`upgrade_pkg_data` TINYTEXT NOT NULL,
			PRIMARY KEY (`pid`)) $collate";
		
		$claim_table = apply_filters('geodir_claim_listing_table' , $claim_table);	
		
		// rename tables if we need to
		if ($wpdb->query("SHOW TABLES LIKE 'geodir_claim'") > 0) {
			$wpdb->query("RENAME TABLE geodir_claim TO ".$wpdb->prefix."geodir_claim");
		} else {
			dbDelta($claim_table);
		}
		
		do_action('geodir_claim_listing_table_created' ,$claim_table ) ;
		
		update_option( 'geodir_claim_fields_upgrade', '1' );		
	}	
}

function geodirclaimlisting_activation_redirect() {
	
	if (get_option('geodir_claim_listing_activation_redirect', false)) {
		delete_option('geodir_claim_listing_activation_redirect');
		wp_redirect(admin_url('admin.php?page=geodirectory&tab=claimlisting_fields&subtab=geodir_claim_options')); 
	}
	
}


function geodir_delete_claim_listing_info($deleted_postid, $force = false){
	
	global $wpdb;
	
	$post_type = get_post_type( $deleted_postid );
	
	$all_postypes = geodir_get_posttypes();
	
	if(!in_array($post_type, $all_postypes))
		return false;
	
	$wpdb->query($wpdb->prepare("DELETE FROM ".GEODIR_CLAIM_TABLE." WHERE list_id=%s", array($deleted_postid)));
	
}


function geodir_unactioned_claims(){
	
	global $wpdb, $plugin_prefix;
	
	$geodir_unaction_claim = $wpdb->get_var("SELECT COUNT(pid) 
						FROM ".GEODIR_CLAIM_TABLE."
						WHERE status = ''");
	return $geodir_unaction_claim;
}


function geodir_claims_change_unread_to_read(){
	
	global $wpdb, $plugin_prefix;
	
	if(isset($_REQUEST['subtab']) && $_REQUEST['subtab'] == 'manage_geodir_claim_listing'):
	
		$wpdb->query("update ".GEODIR_CLAIM_TABLE." set status='0' where status = ''");
		
	endif;
}


function geodir_claim_post_type_setting(){

	$post_type_arr = array();
	
	$post_types = geodir_get_posttypes('object');
	
	foreach($post_types as $key => $post_types_obj)
	{
		$post_type_arr[$key] = $post_types_obj->labels->singular_name;
	}
	return 	$post_type_arr;
}


function geodir_get_claim_default_options_form($current_tab){
	
	$current_tab = $_REQUEST['subtab'];
	geodir_claim_default_option_form($current_tab);
	
}


function geodir_claim_default_options($arr=array()){

	$arr[] = array( 'name' => __( 'Options', GEODIRCLAIM_TEXTDOMAIN ), 'type' => 'no_tabs', 'desc' => '', 'id' => 'claim_options' );
	
	$arr[] = array( 'name' => CLAIM_LISTING_OPTIONS, 'type' => 'sectionstart', 'id' => 'claim_default_options');
	
	$arr[] = array(  
		'name' => CLAIM_ENABLE_LISTING,
		'desc' 		=> __( 'Select \'yes\' to enable claim listing.', GEODIRCLAIM_TEXTDOMAIN ),
		'tip' 		=> '',
		'id' 			=> 'geodir_claim_enable',
		'css' 		=> 'min-width:300px;',
		'std' 		=> '',
		'type' 		=> 'select',
		'class'		=> 'chosen_select',
		'options' => array_unique( array( 
			'' => __( 'Select', GEODIRCLAIM_TEXTDOMAIN ),
			'yes' => __( 'Yes', GEODIRCLAIM_TEXTDOMAIN ),
			'no' => __( 'No', GEODIRCLAIM_TEXTDOMAIN ),
			))
	);
	
	$arr[] = array(  
		'name' => CLAIM_AUTO_APPROVE,
		'desc' 		=> __( 'Select \'yes\' to auto approve claim listing.', GEODIRCLAIM_TEXTDOMAIN ),
		'tip' 		=> '',
		'id' 			=> 'geodir_claim_auto_approve',
		'css' 		=> 'min-width:300px;',
		'std' 		=> '',
		'type' 		=> 'select',
		'class'		=> 'chosen_select',
		'options' => array_unique( array( 
			'' => __( 'Select', GEODIRCLAIM_TEXTDOMAIN ),
			'yes' => __( 'Yes', GEODIRCLAIM_TEXTDOMAIN ),
			'no' => __( 'No', GEODIRCLAIM_TEXTDOMAIN ),
			))
	);
	
	$arr[] = array(  
		'name' => CLAIM_OWNER_VERIFIED_LISTING,
		'desc' 		=> __( 'Select \'yes\' to show owner verified text on listings.', GEODIRCLAIM_TEXTDOMAIN ),
		'tip' 		=> '',
		'id' 			=> 'geodir_claim_show_owner_varified',
		'css' 		=> 'min-width:300px;',
		'std' 		=> '',
		'type' 		=> 'select',
		'class'		=> 'chosen_select',
		'options' => array_unique( array( 
			'' => __( 'Select', GEODIRCLAIM_TEXTDOMAIN ),
			'yes' => __( 'Yes', GEODIRCLAIM_TEXTDOMAIN ),
			'no' => __( 'No', GEODIRCLAIM_TEXTDOMAIN ),
			))
	);
	
	$arr[] = array(  
		'name' => CLAIM_AUTHOR_PAGE_ON_LISTING,
		'desc' 		=> __( 'Select \'yes\' to show link to author page on listings.', GEODIRCLAIM_TEXTDOMAIN ),
		'tip' 		=> '',
		'id' 			=> 'geodir_claim_show_author_link',
		'css' 		=> 'min-width:300px;',
		'std' 		=> '',
		'type' 		=> 'select',
		'class'		=> 'chosen_select',
		'options' => array_unique( array( 
			'' => __( 'Select', GEODIRCLAIM_TEXTDOMAIN ),
			'yes' => __( 'Yes', GEODIRCLAIM_TEXTDOMAIN ),
			'no' => __( 'No', GEODIRCLAIM_TEXTDOMAIN ),
			))
	);
	
	$arr[] = array(  
		'name' => CLAIM_SHOW_POSTTYPES,
		'desc' 		=> '',
		'tip' 		=> '',
		'id' 		=> 'geodir_post_types_claim_listing',
		'css' 		=> 'min-width:300px;',
		'std' 		=> array(),
		'type' 		=> 'multiselect',
		'placeholder_text' => __( 'Select post types', GEODIRCLAIM_TEXTDOMAIN ) ,
		'class'		=> 'chosen_select',
		'options' => array_unique( geodir_claim_post_type_setting())
	);
	
	if ( is_plugin_active( 'geodir_payment_manager/geodir_payment_manager.php' ) ) {
		// Force to payment settings
		$arr[] = array(  
			'name' => __( 'Force an upgrade to complete the claim listing procedure', GEODIRCLAIM_TEXTDOMAIN ),
			'desc' 		=> __( 'Select \'yes\' to force an upgrade to complete the claim listing procedure.', GEODIRCLAIM_TEXTDOMAIN ),
			'tip' 		=> '',
			'id' 			=> 'geodir_claim_force_upgrade',
			'css' 		=> 'min-width:300px;',
			'std' 		=> '',
			'type' 		=> 'select',
			'class'		=> 'chosen_select',
			'options' => array_unique( array( 
				'' => __( 'Select', GEODIRCLAIM_TEXTDOMAIN ),
				'yes' => __( 'Yes', GEODIRCLAIM_TEXTDOMAIN ),
				'no' => __( 'No', GEODIRCLAIM_TEXTDOMAIN ),
				))
		);
	}
	
	$arr[] = array( 'type' => 'sectionend', 'id' => 'claim_default_options');
	
	$arr = apply_filters('geodir_claim_default_options' ,$arr );
	
	return $arr;
}


function geodir_claim_notifications($arr=array()){

	$arr[] = array( 'name' => __( 'Notifications', GEODIRCLAIM_TEXTDOMAIN ), 'type' => 'no_tabs', 'desc' => '', 'id' => 'claim_notifications' );
	
	$arr[] = array( 'name' => CLAIM_GEODIRECTORY_NOTIFICATION, 'type' => 'sectionstart', 'id' => 'claim_notifications');
	
	$arr[] = array(  
		'name' => CLAIM_LISTING_REQUEST_NOTICE,
		'desc' 		=> '',
		'id' 		=> 'geodir_claim_email_subject_admin',
		'type' 		=> 'text',
		'css' 		=> 'min-width:300px;',
		'std' 		=> __('Claim Listing Requested', GEODIRCLAIM_TEXTDOMAIN)
		);
	$arr[] = array(  
		'name' => '',
		'desc' 		=> '',
		'id' 		=> 'geodir_claim_email_content_admin',
		'css' 		=> 'width:500px; height: 150px;',
		'type' 		=> 'textarea',
		'std' 		=>  __("<p>Dear Admin,<p><p>A user has requested to become the owner of the below lisitng.</p><p>[#listing_link#]</p><p>You may wish to login and check the claim details.</p><p>Thank you,<br /><br />[#site_name#].</p>", GEODIRCLAIM_TEXTDOMAIN)
		);
		
		$arr[] = array(  
		'name' => CLAIM_LISTING_REQUEST,
		'desc' 		=> '',
		'id' 		=> 'geodir_claim_email_subject',
		'type' 		=> 'text',
		'css' 		=> 'min-width:300px;',
		'std' 		=> __('Claim Listing Requested', GEODIRCLAIM_TEXTDOMAIN)
		);
	$arr[] = array(  
		'name' => '',
		'desc' 		=> '',
		'id' 		=> 'geodir_claim_email_content',
		'css' 		=> 'width:500px; height: 150px;',
		'type' 		=> 'textarea',
		'std' 		=>  __("<p>Dear [#client_name#],<p><p>You have requested to become the owner of the below listing.</p><p>[#listing_link#]</p><p>We may contact you to confirm your request is genuine.</p><p>You will recive a email once your request has been verified</p><p>Thank you,<br /><br />[#site_name#].</p>", GEODIRCLAIM_TEXTDOMAIN)
		);
		
		$arr[] = array(  
		'name' => CLAIM_LISTING_APPROVAL,
		'desc' 		=> '',
		'id' 		=> 'geodir_claim_approved_email_subject',
		'type' 		=> 'text',
		'css' 		=> 'min-width:300px;',
		'std' 		=> __('Claim Listing Approved', GEODIRCLAIM_TEXTDOMAIN)
		);
	$arr[] = array(  
		'name' => '',
		'desc' 		=> '',
		'id' 		=> 'geodir_claim_approved_email_content',
		'css' 		=> 'width:500px; height: 150px;',
		'type' 		=> 'textarea',
		'std' 		=>  __("<p>Dear [#client_name#],<p><p>Your request to become the owner of the below listing has been APPROVED.</p><p>[#listing_link#]</p><p>You may now login and edit your listing.</p><p>Thank you,<br /><br />[#site_name_url#].</p>", GEODIRCLAIM_TEXTDOMAIN)
		);
		
		$arr[] = array(  
		'name' => CLAIM_LISTING_REJECTED,
		'desc' 		=> '',
		'id' 		=> 'geodir_claim_rejected_email_subject',
		'type' 		=> 'text',
		'css' 		=> 'min-width:300px;',
		'std' 		=> __('Claim Listing Rejected', GEODIRCLAIM_TEXTDOMAIN)
		);
	$arr[] = array(  
		'name' => '',
		'desc' 		=> '',
		'id' 		=> 'geodir_claim_rejected_email_content',
		'css' 		=> 'width:500px; height: 150px;',
		'type' 		=> 'textarea',
		'std' 		=>  __("<p>Dear [#client_name#],<p><p>Your request to become the owner of the below listing has been REJECTED.</p><p>[#listing_link#]</p><p>If you feel this is a wrong decision please reply to this email with your reasons.</p><p>Thank you,<br /><br />[#site_name#].</p>", GEODIRCLAIM_TEXTDOMAIN)
		);
		
		$arr[] = array(  
		'name' => CLAIM_LISTING_VERIFY_REQUIRE,
		'desc' 		=> '',
		'id' 		=> 'geodir_claim_auto_approve_email_subject',
		'type' 		=> 'text',
		'css' 		=> 'min-width:300px;',
		'std' 		=> __('Claim Listing Verification Required', GEODIRCLAIM_TEXTDOMAIN)
		);
	$arr[] = array(  
		'name' => '',
		'desc' 		=> '',
		'id' 		=> 'geodir_claim_auto_approve_email_content',
		'css' 		=> 'width:500px; height: 150px;',
		'type' 		=> 'textarea',
		'std' 		=>  __("<p>Dear [#client_name#],<p><p>Your request to become the owner of the below listing needs to be verified.</p><p>[#listing_link#]</p><p><b>By clicking the VERIFY link below you are stating you are legally associated with this business and have the owners consent to edit the listing.</b></p><p><b>If you are not associated with this business and edit the listing with malicious intent you will be solely liable for any legal action or claims for damages.</b></p><p>[#approve_listing_link#]</p><p>Thank you,<br /><br />[#site_name_url#].</p>", GEODIRCLAIM_TEXTDOMAIN)
		);
	
	
	$arr[] = array( 'type' => 'sectionend', 'id' => 'claim_notifications');
	
	$arr = apply_filters('geodir_claim_notifications' ,$arr );
	
	return $arr;
}



function geodir_enable_editor_on_claim_notifications($notification){
	
	if(!empty($notification) && get_option('geodir_tiny_editor')=='1'){
		
		foreach($notification as $key => $value){
			if($value['type'] == 'textarea')
				$notification[$key]['type'] = 'editor';
		}
		
	}
	
	return $notification;
}


function geodir_get_admin_claim_listing_option_form(){
	
	global $wpdb;
	
	if(isset($_REQUEST['subtab']) && $_REQUEST['subtab'] == 'geodir_claim_options' )
	{
		add_action('geodir_admin_option_form', 'geodir_get_claim_default_options_form');
	}
	
	if(isset($_REQUEST['subtab']) && $_REQUEST['subtab'] == 'manage_geodir_claim_listing')
	{
			if(isset($_REQUEST['pagetype']) && $_REQUEST['pagetype']=='addedit')
			{
				geodir_admin_claim_frm();
					
			}else
			{
				geodir_manage_claim_listing();
			}
	}
	
	if(isset($_REQUEST['subtab']) && $_REQUEST['subtab'] == 'geodir_claim_notification')
	{
		add_action('geodir_admin_option_form', 'geodir_get_claim_default_options_form');
	}
	
}


function geodir_claim_manager_ajax()
{

	if(isset($_POST['geodir_sendact']) && $_POST['geodir_sendact'] == 'add_claim')
	{	
		geodir_user_add_claim();
	}
	
	if(isset($_REQUEST['claimact']) && $_REQUEST['claimact'] == 'addclaim')
	{
		geodir_claim_add_comment();
	}
	
	if(isset($_REQUEST['subtab']) && $_REQUEST['subtab'] == 'geodir_claim_options')
	{
		
		geodir_update_options(geodir_claim_default_options());
		
		$msg = CLAIM_LISTING_OPTIONS_SAVE;
		
		$msg = urlencode($msg);
		
		$location = admin_url()."admin.php?page=geodirectory&tab=claimlisting_fields&subtab=geodir_claim_options&claim_success=".$msg;
		
		wp_redirect($location);
		exit;
		
	}
	
	if(isset($_REQUEST['manage_action']) && $_REQUEST['manage_action']=='true')
	{
		geodir_manage_claim_listing_actions();
	}
	
	if(isset($_REQUEST['subtab']) && $_REQUEST['subtab'] == 'geodir_claim_notification')
	{
		
		geodir_update_options(geodir_claim_notifications());
		
		$msg = CLAIM_NOTIFY_SAVE_SUCCESS;
		
		$msg = urlencode($msg);
		
		$location = admin_url()."admin.php?page=geodirectory&tab=claimlisting_fields&subtab=geodir_claim_notification&claim_success=".$msg;
	
		wp_redirect($location);exit;
		
	}
	
	if(isset($_REQUEST['popuptype']) && $_REQUEST['popuptype'] != '' && isset($_REQUEST['post_id']) && $_REQUEST['post_id'] != ''){
		
		if($_REQUEST['popuptype'] == 'geodir_claim_enable')
			geodir_claim_popup_form($_REQUEST['post_id']);
		
		exit;
	}
	
}


function geodir_claim_add_comment()
{
	global $wpdb, $plugin_prefix;
	
	if(isset($_REQUEST['claim_addcomment_nonce']) && isset($_REQUEST['id']) && current_user_can('manage_options')){
	
		if ( !wp_verify_nonce( $_REQUEST['claim_addcomment_nonce'], 'claim_addcomment_nonce' ) )
		return;
		
		if(isset($_REQUEST['claimact']) && $_REQUEST['claimact'] == 'addclaim')
		{
			$id = $_REQUEST['id'];
			$admin_com = $_REQUEST['admin_com'];
			
			if($id)
			{
				$wpdb->query($wpdb->prepare("update ".GEODIR_CLAIM_TABLE." set admin_comments=%s", array($admin_com)));
			}
			
			$msg = CLAIM_COMMENT_ADD_SUCCESS;
			$msg = urlencode($msg);
			
			$location = admin_url('admin.php?page=geodirectory&tab=claimlisting_fields&subtab=manage_geodir_claim_listing&claim_success='.$msg);
			wp_redirect($location);exit;
	
		}
	
	}else{		
		wp_redirect(home_url().'/?geodir_signup=true');
		exit();
	}
}


function geodir_display_post_claim_link(){
	
	global $post, $preview;
	
	$geodir_post_type = array();
	if(get_option('geodir_post_types_claim_listing'))
		$geodir_post_type =	get_option('geodir_post_types_claim_listing');
	
	$post_id = $post->ID;
	$posttype = (isset($post->post_type)) ? $post->post_type : '';
	
	/*if(isset($_REQUEST['pid']) && $_REQUEST['pid'] != ''){
		$post_id = $_REQUEST['pid'];
		$posttype = get_post_type( $post_id );
	}*/
	
	if(in_array($posttype, $geodir_post_type) && !$preview)
	{
		
		$post_info = get_post($post_id);
		
		$author_id = $post_info->post_author;
		
		$post_type = $post_info->post_type;
		
		$user = new WP_User( $author_id );
		
		$author_role = !empty( $user->roles ) && isset($user->roles[0]) ? $user->roles[0] : '';
		
		$is_owned = geodir_get_post_meta($post_id,'claimed',true);
		
		if(get_option('geodir_claim_show_owner_varified')=='yes')
		{ 
			//if ($author_role =='author' && $is_owned != '0' )
			if ( $is_owned != '0' )
			{
				echo '<p class="sucess_msg"><i class="fa fa-check-circle"></i> '.CLAIM_OWNER_VERIFIED_PLACE.'</p>';
			}
		}
		
		/*if(get_option('geodir_claim_enable')=='yes' && ($author_role =='administrator' || $is_owned=='0'))*/
		if(get_option('geodir_claim_enable')=='yes' && $is_owned=='0')
		{
			
			if ( is_user_logged_in())
			{
				echo '<input type="hidden" name="geodir_claim_popup_post_id" value="'.$post_id.'" /><div class="geodir_display_claim_popup_forms"></div><p class="edit-link"><i class="fa fa-question-circle"></i> <a href="javascript:void(0);" class="geodir_claim_enable">'.CLAIM_BUSINESS_OWNER.'</a></p>';
				
			}
			else
			{
				
				$site_login_url = get_option('siteurl').'?geodir_signup=true';
				echo '<p class="edit-link"><a href="'.$site_login_url.'" ><i class="fa fa-question-circle"></i> '.CLAIM_BUSINESS_OWNER.'</a></p>';
				
			}
			
			if(isset($_REQUEST['geodir_claim_request']) && $_REQUEST['geodir_claim_request']=='success')
			{
				echo '<p class="sucess_msg">'.CLAIM_LISTING_SUCCESS.'</p>';
			}	
		}
		
		if ($is_owned=='1' && get_option('geodir_claim_show_author_link')=='yes' && !$preview )
		{
			$author_link = get_author_posts_url( $author_id );
			$author_link = geodir_getlink($author_link,array('geodir_dashbord'=>'true','stype'=>$post_type ),false);
			
			// hook for author link
			$author_link = apply_filters( 'geodir_dashboard_author_link', $author_link, $author_id, $post_type );
			
			echo '<p class="geodir-author-link"><i class="fa fa-user"></i> ';
			echo CLAIM_AUTHOR_TEXT;
			echo '<a href="'.$author_link.'">';
			the_author();
			echo '</a></p>';
			
		}
	}
}


function geodir_user_add_claim(){

	global $wp_query, $post, $General, $wpdb, $plugin_prefix, $current_user;
	
	if(isset($_REQUEST['add_claim_nonce_field']) && isset($_REQUEST['geodir_pid']) && is_user_logged_in()){
		
		if ( !wp_verify_nonce( $_REQUEST['add_claim_nonce_field'], 'add_claim_nonce'.$_REQUEST['geodir_pid'] ) )
				return;
			
		$list_id = $pid = $_POST['geodir_pid'];
			
		$claim_post = get_post($pid);
		
		if(isset($_POST['geodir_sendact']) && $_POST['geodir_sendact'] == 'add_claim')
		{
			
			$uid = $claim_post->post_author;
			
			$list_title = $claim_post->post_title;
			
			$user_id = $current_user->ID;
			
			$user_name = $current_user->user_login;
			
			$user_email = $current_user->user_email;
			
			$user_fullname = $_POST['geodir_full_name'];
			
			$user_number = $_POST['geodir_user_number'];
			
			$user_position = $_POST['geodir_user_position'];
			
			$user_comments = $_POST['geodir_user_comments'];
			
			$claim_date = date("F j, Y, g:i a");
			
			$org_author = get_the_author_meta( 'login', $uid );
			
			$org_authorid = $claim_post->post_author;
			
			$rand_string = createRandomString();
			
			$user_ip = getenv("REMOTE_ADDR") ;
			
			// Force to upgrade to complete claim listing
			$force_upgrade = geodir_claim_force_upgrade();
			$package_list = geodir_claim_payment_package_list( $claim_post->post_type );
			
			if ( $force_upgrade && !empty( $package_list ) ) {
				$geodir_upgrade_pkg = isset($_POST['geodir_claim_pkg']) ? $_POST['geodir_claim_pkg'] : '';
				$package_info = geodir_get_package_info_by_id( $geodir_upgrade_pkg );
				
				if ( empty( $package_info ) || !$list_id ) {
					return;
				}
			}
		
			if($_REQUEST['geodir_pid']){
				
				$claimsql = $wpdb->prepare("INSERT INTO ".GEODIR_CLAIM_TABLE." (list_id, list_title, user_id, user_name, user_email, user_fullname, user_number, user_position, user_comments, claim_date, org_author, org_authorid, rand_string, user_ip ) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s,%s, %s )",
				array($list_id,$list_title,$user_id,$user_name,$user_email,$user_fullname,$user_number,$user_position,$user_comments,$claim_date,$org_author,$org_authorid,$rand_string,$user_ip)
				);	
			
				$claim = $wpdb->query($claimsql);
				
				// Force to upgrade to complete claim listing
				if ( $force_upgrade && !empty( $package_list ) && $claim && $wpdb->insert_id ) {
					if ( !(float)$package_info->amount > 0 ) { // Free price package plan
						$upgrade_pkg_data = array();
						$upgrade_pkg_data['post_id'] = $list_id;
						$upgrade_pkg_data['package_id'] = $geodir_upgrade_pkg;
						$upgrade_pkg_data['pid'] = $wpdb->insert_id;
						$upgrade_pkg_data['date'] = date_i18n( 'Y-m-d H:i:s', time() );
						$upgrade_pkg_data['amount'] = $package_info->amount;
						$upgrade_pkg_data['user_id'] = $user_id;
						$upgrade_pkg_data['author_id'] = $org_authorid;
						
						$upgrade_pkg_data = maybe_serialize( $upgrade_pkg_data );
						
						$sql = $wpdb->prepare( "UPDATE " . GEODIR_CLAIM_TABLE . " SET `upgrade_pkg_id`=%d, `upgrade_pkg_data`=%s WHERE `pid`=%d", array( $geodir_upgrade_pkg, $upgrade_pkg_data, $wpdb->insert_id ) );
						$wpdb->query( $sql );
					}
				}
				
				geodir_adminEmail($list_id,$user_id,'claim_requested'); /* email to admin*/
				geodir_clientEmail($list_id,$user_id,'claim_requested'); /* email to client*/
				
			}
			
			if(get_option('geodir_claim_auto_approve')=='yes'){
					geodir_clientEmail($list_id,$user_id,'auto_claim',$rand_string);/* email to client*/
			}	
			
			$postlink = get_permalink($claim_post->ID);
			$url = geodir_getlink($postlink,array('geodir_claim_request'=>'success'),false);
			wp_redirect($url);
			
		}
		
	}else{		
		wp_redirect(home_url().'/?geodir_signup=true');
		exit();
	}
	
}


function geodir_manage_claim_listing_actions()
{
	global $wpdb, $plugin_prefix;
	
	if(isset($_REQUEST['_wpnonce']) && isset($_REQUEST['id']) && $_REQUEST['id'] != '' && current_user_can( 'manage_options' )){
		
		if ( !wp_verify_nonce( $_REQUEST['_wpnonce'], 'claim_action_'.$_REQUEST['id'] ) )
				return;
		
		if(isset($_REQUEST['pagetype']) && $_REQUEST['pagetype'] == 'delete')
		{
			$pid = $_REQUEST['id'];
			
			$approvesql = $wpdb->prepare("select * from ".GEODIR_CLAIM_TABLE." where pid=%d", array($pid));
			
			$approveinfo = $wpdb->get_results($approvesql);
			
			$post_id = $approveinfo[0]->list_id;
			
			$author_id = $approveinfo[0]->user_id;
			
			$post_id = $approveinfo[0]->list_id;
			
			$wpdb->query($wpdb->prepare("delete from ".GEODIR_CLAIM_TABLE." where pid=%d", array($pid)));
			
			$change_clamed = $wpdb->get_row($wpdb->prepare("select pid from ".GEODIR_CLAIM_TABLE." where list_id=%s and status='1'", array($post_id)));
			
			if(!$change_clamed)
			{
				geodir_save_post_meta($post_id, 'claimed','0');
			}
			
			$msg = CLAIM_DELETED_SUCCESS;
			
			$msg = urlencode($msg);
			
			$location = admin_url('admin.php?page=geodirectory&tab=claimlisting_fields&subtab=manage_geodir_claim_listing&claim_success='.$msg);
			wp_redirect($location);exit;
		}
		
		if(isset($_REQUEST['pagetype']) && $_REQUEST['pagetype'] == 'approve')
		{
			$pid = $_REQUEST['id'];
			
			$approvesql = $wpdb->prepare("select * from ".GEODIR_CLAIM_TABLE." where pid=%d", array($pid));
			
			$approveinfo = $wpdb->get_results($approvesql);
			
			$post_id = $approveinfo[0]->list_id;
			
			$author_id = $approveinfo[0]->user_id;
			
			$wpdb->query($wpdb->prepare("update ".GEODIR_CLAIM_TABLE." set status='2' where list_id=%s and user_id!=%s and status='1'", array($post_id,$author_id)));
			
			geodir_save_post_meta($post_id, 'claimed','1');
			
			$wpdb->query($wpdb->prepare("update $wpdb->posts set post_author=%d where ID=%d", array($author_id,$post_id))); 		
			$wpdb->query($wpdb->prepare("update ".GEODIR_CLAIM_TABLE." set status='1' where pid=%d", array($pid)));
			
			// Force to upgrade to complete claim listing
			$force_upgrade = geodir_claim_force_upgrade();
			$package_list = geodir_claim_payment_package_list( get_post_type( $post_id ) );
			
			if ( $force_upgrade && !empty( $package_list ) && !empty( $approveinfo ) && isset( $approveinfo[0]->upgrade_pkg_id ) ) {
				$geodir_upgrade_pkg = $approveinfo[0]->upgrade_pkg_id;
				$package_info = geodir_get_package_info_by_id( $geodir_upgrade_pkg );
				
				if ( !empty( $package_info ) ) {
					$claim_post_info = array();
					$claim_post_info['package_id'] = $geodir_upgrade_pkg;
				
					geodir_save_listing_payment( $post_id, $claim_post_info );
					
					$wpdb->query( $wpdb->prepare( "UPDATE `" . GEODIR_CLAIM_TABLE . "` SET `upgrade_pkg_id`='' WHERE `pid`=%d", array( $pid ) ) );
				}
			}
				 
			geodir_clientEmail($post_id,$author_id,'claim_approved'); /* email to client*/
			
			$msg = CLAIM_APPROVE_SUCCESS;
			
			$msg = urlencode($msg);
			
			$location = admin_url('admin.php?page=geodirectory&tab=claimlisting_fields&subtab=manage_geodir_claim_listing&claim_success='.$msg);
			
			wp_redirect($location);exit;
			
		}
		
		if(isset($_REQUEST['pagetype']) && $_REQUEST['pagetype'] == 'reject')
		{
			$pid = $_REQUEST['id'];
			
			$wpdb->query($wpdb->prepare("update ".GEODIR_CLAIM_TABLE." set status='2' where pid=%d", array($pid)));
			
			$approvesql = $wpdb->prepare("select * from ".GEODIR_CLAIM_TABLE." where pid=%d", array($pid));
			
			$approveinfo = $wpdb->get_results($approvesql);
			
			$post_id = $approveinfo[0]->list_id;
			
			$author_id = $approveinfo[0]->user_id;
			
			geodir_clientEmail($post_id,$author_id,'claim_rejected'); /* email to client*/
			
			$msg = CLAIM_REJECT_SUCCESS;
			
			$msg = urlencode($msg);
			
			$location = admin_url('admin.php?page=geodirectory&tab=claimlisting_fields&subtab=manage_geodir_claim_listing&claim_success='.$msg);
			
			wp_redirect($location);exit;
			
		}
		
		if(isset($_REQUEST['pagetype']) && $_REQUEST['pagetype'] == 'undo')
		{
			$pid = $_REQUEST['id'];
			
			$approvesql = $wpdb->prepare("select * from ".GEODIR_CLAIM_TABLE." where pid=%d", array($pid));
			
			$approveinfo = $wpdb->get_results($approvesql);
			
			$post_id = $approveinfo[0]->list_id;
			
			$author_id = $approveinfo[0]->org_authorid;
			
			$wpdb->query($wpdb->prepare("update $wpdb->posts set post_author=%d where ID=%d", array($author_id,$post_id)));
			
			$wpdb->query($wpdb->prepare("update ".GEODIR_CLAIM_TABLE." set status='2' where pid=%d", array($pid)));
			
			$change_clamed = $wpdb->get_row($wpdb->prepare("select pid from ".GEODIR_CLAIM_TABLE." where list_id=%s and status='1'", array($post_id)));
			
			if(!$change_clamed)
			{
				geodir_save_post_meta($post_id, 'claimed','0'); /*update claimed post data.*/
			}
			
			$location = admin_url('admin.php?page=geodirectory&tab=claimlisting_fields&subtab=manage_geodir_claim_listing&msg=reject');
			
			wp_redirect($location);exit;	
		
		}
	
	}else{		
		wp_redirect(home_url().'/?geodir_signup=true');
		exit();
	}

}


function geodir_display_claim_messages(){

	if(isset($_REQUEST['claim_success']) && $_REQUEST['claim_success'] != '')
	{
			echo '<div id="message" class="updated fade"><p><strong>' . $_REQUEST['claim_success'] . '</strong></p></div>';			
					
	}
	
	if(isset($_REQUEST['claim_error']) && $_REQUEST['claim_error'] != '')
	{
			echo '<div id="claim_message_error" class="updated fade"><p><strong>' . $_REQUEST['claim_error'] . '</strong></p></div>';			
				
	}
}


function geodir_adminEmail($page_id,$user_id,$message_type,$custom_1=''){	
	if($message_type=='claim_approved')
	{
		$subject = get_option('geodir_claim_approved_email_subject');
		$client_message = get_option('geodir_claim_approved_email_content');
	}
	elseif($message_type=='claim_rejected')
	{
		$subject = get_option('geodir_claim_rejected_email_subject');
		$client_message = get_option('geodir_claim_rejected_email_content');
	}
	elseif($message_type=='claim_requested')
	{
		$subject = get_option('geodir_claim_email_subject_admin'); 
		$client_message = get_option('geodir_claim_email_content_admin');
	}
	elseif($message_type=='auto_claim')
	{
		$subject = get_option('geodir_claim_auto_approve_email_subject');
		$client_message = get_option('geodir_claim_auto_approve_email_content');
	}
	
	$transaction_details = $custom_1;
		
	$approve_listing_link = '<a href="'.home_url().'/?geodir_ptype=verify&rs='.$custom_1.'">'.CLAIM_VERIFY_TEXT.'</a>';	
	
	$fromEmail = get_option('site_email');
	
	$fromEmailName = get_site_emailName();
	
	if(function_exists('get_property_price_info_listing')){
		$pkg_limit = get_property_price_info_listing($page_id);
	
		$alivedays = $pkg_limit['days'];
	}else{
		$alivedays = 'unlimited';
	}	
	
	$productlink = get_permalink($page_id);
	
	$post_info = get_post($page_id);
	
	$post_date =  date('dS F,Y',strtotime($post_info->post_date));
	
	$listingLink ='<a href="'.$productlink.'"><b>'.$post_info->post_title.'</b></a>';
	
	$site_login_url = get_option('siteurl').'?geodir_signup=true';
	
	$loginurl_link = '<a href="'.$site_login_url.'">login</a>';
	
	$siteurl = home_url();
	
	$siteurl_link = '<a href="'.$siteurl.'">'.$fromEmailName.'</a>';
	
	$user_info = get_userdata($user_id);
	
	$user_email = $user_info->user_email;
	
	$display_name = $user_info->first_name;
	
	$user_login = $user_info->user_login;
	
	$number_of_grace_days = get_option('ptthemes_listing_preexpiry_notice_days');
	
	if($number_of_grace_days==''){$number_of_grace_days=1;}
	
	if($post_info->post_type == 'event'){$post_type='event';}else{$post_type='listing';}
	
	$renew_link = '<a href="'.$siteurl.'?ptype=post_'.$post_type.'&renew=1&pid='.$page_id.'">'.CLAIM_RENEW_LINK.'</a>';
	
	$search_array = array('[#client_name#]','[#listing_link#]','[#posted_date#]','[#number_of_days#]','[#number_of_grace_days#]','[#login_url#]','[#username#]','[#user_email#]','[#site_name_url#]','[#renew_link#]','[#post_id#]','[#site_name#]','[#approve_listing_link#]','[#transaction_details#]');
	
	$replace_array = array($display_name,$listingLink,$post_date,$alivedays,$number_of_grace_days,$loginurl_link,$user_login,$user_email,$siteurl_link,$renew_link,$page_id,$fromEmailName,$approve_listing_link,$transaction_details);
	
	$client_message = str_replace($search_array,$replace_array,$client_message);	
	
	$subject = str_replace($search_array,$replace_array,$subject);	
	
	$headers  = 'MIME-Version: 1.0' . "\r\n";
	
	$headers .= 'Content-type: text/html; charset=UTF-8' . "\r\n";
	
	$headers .= 'To: <'.$fromEmail.'>' . "\r\n";
	
	$headers .= 'From: '.$fromEmailName.' <'.$fromEmail.'>' . "\r\n";
	
	// strip slashes from subject & message text
	$subject = stripslashes_deep( $subject );
	$client_message = stripslashes_deep( $client_message );
	
	@wp_mail($fromEmail,$subject,$client_message,$headers);/*To client email*/
}


function geodir_clientEmail($page_id,$user_id,$message_type,$custom_1=''){

	if($message_type=='claim_approved')
	{
		$subject = get_option('geodir_claim_approved_email_subject');
		$client_message = get_option('geodir_claim_approved_email_content');
	}
	elseif($message_type=='claim_rejected')
	{
		$subject = get_option('geodir_claim_rejected_email_subject');
		$client_message = get_option('geodir_claim_rejected_email_content');
	}
	elseif($message_type=='claim_requested')
	{	
		$subject = get_option('geodir_claim_email_subject');
		$client_message = get_option('geodir_claim_email_content');
	}
	elseif($message_type=='auto_claim')
	{
		$subject = get_option('geodir_claim_auto_approve_email_subject');
		$client_message = get_option('geodir_claim_auto_approve_email_content');
	}
	
	$transaction_details = $custom_1;
	
	$approve_listing_link = '<a href="'.home_url().'/?geodir_ptype=verify&rs='.$custom_1.'">'.CLAIM_VERIFY_TEXT.'</a>';	
	
	$fromEmail = get_option('site_email');
	
	$fromEmailName = get_site_emailName();
	
	if(function_exists('get_property_price_info_listing')){
		$pkg_limit = get_property_price_info_listing($page_id);
	
		$alivedays = $pkg_limit['days'];
	}else{
		$alivedays = 'unlimited';
	}	
	
	$productlink = get_permalink($page_id);
	
	$post_info = get_post($page_id);
	
	$post_date =  date('dS F,Y',strtotime($post_info->post_date));
	
	$listingLink ='<a href="'.$productlink.'"><b>'.$post_info->post_title.'</b></a>';
	
	
	$site_login_url = get_option('siteurl').'?geodir_signup=true';
	
	$loginurl_link = '<a href="'.$site_login_url.'">login</a>';
	
	$siteurl = home_url();
	
	$siteurl_link = '<a href="'.$siteurl.'">'.$fromEmailName.'</a>';
	
	$user_info = get_userdata($user_id);
	
	$user_email = $user_info->user_email;
	
	$display_name = $user_info->first_name;
	
	if(!$display_name)
		$display_name = get_the_author_meta( 'display_name', $user_id );
	
	$user_login = $user_info->user_login;
	
	$number_of_grace_days = get_option('ptthemes_listing_preexpiry_notice_days');


	if($number_of_grace_days==''){$number_of_grace_days=1;}

	if($post_info->post_type == 'event'){$post_type='event';}else{$post_type='listing';}
	
	$renew_link = '<a href="'.$siteurl.'?ptype=post_'.$post_type.'&renew=1&pid='.$page_id.'">'.CLAIM_RENEW_LINK.'</a>';
	
	$search_array = array('[#client_name#]','[#listing_link#]','[#posted_date#]','[#number_of_days#]','[#number_of_grace_days#]','[#login_url#]','[#username#]','[#user_email#]','[#site_name_url#]','[#renew_link#]','[#post_id#]','[#site_name#]','[#approve_listing_link#]','[#transaction_details#]');

	$replace_array = array($display_name,$listingLink,$post_date,$alivedays,$number_of_grace_days,$loginurl_link,$user_login,$user_email,$siteurl_link,$renew_link,$page_id,$fromEmailName,$approve_listing_link,$transaction_details);
	
	$client_message = str_replace($search_array,$replace_array,$client_message);
	
	$subject = str_replace($search_array,$replace_array,$subject);
	
	$headers  = 'MIME-Version: 1.0' . "\r\n";
	
	$headers .= 'Content-type: text/html; charset=UTF-8' . "\r\n";
	
	$headers .= 'To: '.$display_name.' <'.$user_email.'>' . "\r\n";
	
	$headers .= 'From: '.$fromEmailName.' <'.$fromEmail.'>' . "\r\n";
	
	// strip slashes from subject & message text
	$subject = stripslashes_deep( $subject );
	$client_message = stripslashes_deep( $client_message );
	
	@wp_mail($user_email,$subject,$client_message,$headers);/*To client email*/
}

function geodir_claim_add_field_in_table(){
	if ( !get_option( 'geodir_claim_fields_upgrade' ) ) {
		geodir_add_column_if_not_exist( GEODIR_CLAIM_TABLE, 'upgrade_pkg_id', 'INT( 11 ) NOT NULL' );
		geodir_add_column_if_not_exist( GEODIR_CLAIM_TABLE, 'upgrade_pkg_data', 'TINYTEXT NOT NULL' );
		
		update_option( 'geodir_claim_fields_upgrade', '1' );
	}
}

/**
 * Check to see enabled force an upgrade to complete the claim listing procedure.
 *
 * This will check for payment addon active & option "Force an upgrade to complete 
 *      the claim listing procedure." enabled in claim listing settings. 
 *
 * @since 1.1.4
 *
 * @return bool True if enabled, else false.
 */
function geodir_claim_force_upgrade() {
	if ( get_option( 'geodir_claim_force_upgrade' ) == 'yes' && is_plugin_active( 'geodir_payment_manager/geodir_payment_manager.php' ) ) {
		return true;
	}
	
	return false;
}

/**
 * Display price package list in claim lisitng form.
 *
 * @since 1.1.4
 *
 * @param  string $field Claim listing field name.
 * @return string Display price package list.
 */
function geodir_claim_after_claim_form_field( $field = '' ) {
	if ( $field == 'geodir_user_comments' && geodir_claim_force_upgrade() ) {
		$gd_post_types = geodir_get_posttypes();
		
		$post_id = isset( $_REQUEST['post_id'] ) ? $_REQUEST['post_id'] : '';
		$post_type = get_post_type( $post_id );
		
		if ( $post_type != '' && in_array( $post_type, $gd_post_types ) ) {
			$package_list = geodir_claim_payment_package_list( $post_type );
			$payment_methods = geodir_claim_payment_methods();
			
			if ( empty( $package_list ) || empty( $payment_methods ) ) {
				return;
			}
			
			$package_id = geodir_get_post_meta( 'package_id', true );
			
			if ( empty( $package_id ) ) {
				$default_package = geodir_get_default_package( $post_type );
				$package_id = $default_package->pid;
			}
			
			$js_script = '';
			?>
			<div id="gd_claim_pkgs" class="row clearfix gd-claim-pkgs gd-chosen-outer">
				<label><?php _e( 'Select Package', GEODIRCLAIM_TEXTDOMAIN );?> : <span>*</span></label>
				<select name="geodir_claim_pkg" id="geodir_claim_pkg" field_type="select" class="is_required chosen_select">
				<?php foreach ( $package_list as $package ) { ?>
				<option value="<?php echo $package->pid; ?>"><?php echo stripslashes_deep( $package->title_desc ); ?></option>
				<?php } ?>
				</select>
				<span class="message_error2" id="geodir_claim_pkgInfo"></span>
			</div>
			<div id="gd_claim_payments" class="row clearfix gd-claim-payments gd-chosen-outer">
				<label><?php _e( 'Select Payment Method', GEODIRCLAIM_TEXTDOMAIN );?> : <span>*</span></label>
				<select name="geodir_claim_payment" id="geodir_claim_payment" field_type="select" class="is_required chosen_select">
				<?php 
					$file_content = '';
					$keys = array();
					foreach ( $payment_methods as $payment_method ) { 
						$keys[] = $payment_method['key'];
						if ( file_exists( GEODIR_PAYMENT_MANAGER_PATH . $payment_method['key'] . '/' . $payment_method['key'] . '.php' ) ) {
							ob_start();
							include_once( GEODIR_PAYMENT_MANAGER_PATH . $payment_method['key'] . '/' . $payment_method['key'] . '.php' );
							$file_content .= ob_get_clean();
						}
				?>
				<option value="<?php echo $payment_method['key']; ?>"><?php echo stripslashes_deep( $payment_method['name'] ); ?></option>
				<?php 
					}
					
					$gd_claim_pmkeys = "['" . implode( "', '", $keys ) . "']";
					$js_script .= 'jQuery("#geodir_claim_payment").attr("onchange", "javascript:gd_claim_change_pmethod(this, ' . $gd_claim_pmkeys . ');"); jQuery("#geodir_claim_payment").trigger("change");';
				?>
				</select>
				<span class="message_error2" id="geodir_claim_paymentInfo"></span>
				<?php echo $file_content;?>
				<?php 
				$js_script .= '';
				?>
			</div>
			<script type="text/javascript"><?php echo $js_script;?> jQuery("#geodir_claim_form").addClass('gd-claimfrm-upgrade');jQuery(".chosen_select", "#geodir_claim_form").chosen({"disable_search":true});</script>
			<?php
		}
	}
}

function geodir_claim_payment_methods() {
	global $wpdb;
	
	$query = $wpdb->prepare( "SELECT * FROM $wpdb->options WHERE option_name LIKE %s", array( 'payment_method_%' ) );
	$results = $wpdb->get_results( $query );
	
	$return = array();
	
	if ( !empty( $results ) ) {
		foreach ( $results as $row ) {
			$option_name = $row->option_name;			
			$option_info = maybe_unserialize( $row->option_value );
			
			if ( !empty( $option_info ) && isset( $option_info['isactive'] ) && $option_info['isactive'] ) {
				$return[$option_info['display_order']][] = $option_info;
			}
		}
		
		if ( !empty( $return ) ) {
			ksort( $return );
			
			$rows = $return;
			
			$return = array();
			foreach ( $rows as $row ) {
				$return = array_merge( $return, $row );
			}
		}
	}
	
	return $return;
}

function geodir_claim_payment_package_list( $post_type, $exclude_free = true ) {
	if ( !function_exists( 'geodir_package_list_info' ) ) {
		return NULL;
	}
	
	$package_list = array();
	
	$packages = geodir_package_list_info( $post_type );
	if ( !$exclude_free ) {
		return $package_list;
	}	
	
	if ( !empty( $packages ) ) {
		foreach ( $packages as $package ) {
			if ( (float)$package->amount > 0 ) {
				$package_list[] = $package;
			}
		}
	}
	
	return $package_list;
}