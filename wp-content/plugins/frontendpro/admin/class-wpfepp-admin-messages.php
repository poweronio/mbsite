<?php

/**
 * This class is responsible for displaying messages in the admin area after an update.
 *
 * @since 2.0.0
 * @package WPFEPP
 **/
class WPFEPP_Admin_Messages
{
	/**
	 * Plugin version. This is used in wp_enqueue_style and wp_enqueue_script to make sure that the end user doesn't get outdated scripts and styles because of browser caching.
	 *
	 * @access private
	 * @var string
	 **/
	private $version;
	
	/**
	 * Class constructor. Initializes the class attributes.
	 **/
	public function __construct($version)
	{
		$this->version 		= $version;
	}

	/**
	 * Adds the actions of this class. The WPFEPP_Loader class registers this function with WordPress.
	 **/
	public function add_actions(){
		add_action( 'admin_notices', array($this, 'admin_notices') );
		add_action( 'wp_ajax_wpfepp_dismiss_nag', array($this, 'dismiss_nag') );
		add_action( 'admin_enqueue_scripts', array($this, 'enqueue') );
	}

	public function enqueue($hook){
		wp_enqueue_script("wpfepp-admin-script", plugins_url( "static/js/admin.js" , dirname(__FILE__) ), array('jquery'), $this->version);
	}

	public function admin_notices() {
		$last_nag_dismissed = get_option('wpfepp_nag_dismissed', 0);

		if($last_nag_dismissed >= $this->version || !current_user_can('manage_options'))
			return;

		?>
		    <div class="updated">
		        <p>
		        	<?php _e('Thank you for installing the latest version of Frontend Publishing Pro! Please review your settings to make sure everything is in order', 'wpfepp-plugin'); ?>.<br/>
		        	<a target="_blank" href="http://codecanyon.net/item/frontend-publishing-pro/8517990#item-description__change-log"><?php _e("What's New", 'wpfepp-plugin'); ?></a> | 
		        	<a target="_blank" href="http://codecanyon.net/downloads"><?php _e('Leave a Review', 'wpfepp-plugin'); ?></a> | 
		        	<a href="<?php echo admin_url('admin.php?page=wpfepp_form_manager'); ?>"><?php _e('Forms', 'wpfepp-plugin'); ?></a> | 
		        	<a href="<?php echo admin_url('admin.php?page=wpfepp_settings') ?>"><?php _e('Settings', 'wpfepp-plugin'); ?></a>
		        	<a style="float:right;" id="wpfepp-dismiss-nag" href="#"><?php _e('Dismiss This Message', 'wpfepp-plugin'); ?></a>
		        </p>
		    </div>
		<?php
	}

	/**
	 * Dismisses the message.
	 **/
	public function dismiss_nag()
	{
		update_option('wpfepp_nag_dismissed', $this->version);
	}
}