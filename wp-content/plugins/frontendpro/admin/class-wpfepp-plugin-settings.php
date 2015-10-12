<?php

/**
 * This class is responsible for creating the settings page in the backend area.
 *
 * @since 1.0.0
 * @package WPFEPP
 **/
class WPFEPP_Plugin_Settings
{
	/**
	 * Plugin version. This is used in wp_enqueue_style and wp_enqueue_script to make sure that the end user doesn't get outdated scripts and styles because of browser caching.
	 *
	 * @access private
	 * @var string
	 **/
	private $version;
	
	/**
	 * The hook of our admin page. It is used to make sure that the stylesheets and scripts are only enqueued where they are relevant.
	 *
	 * @access private
	 * @var string
	 **/
	private $page_hook;
	
	/**
	 * The page slug.
	 *
	 * @access private
	 * @var string
	 **/
	private $page;

	/**
	 * The page slug of parent.
	 *
	 * @access private
	 * @var string
	 **/
	private $parent_page;

	/**
	 * Class constructor. Initializes the class attributes.
	 **/
	public function __construct($version, $parent_page)
	{
		$this->load_dependencies();

		$this->version 		= $version;
		$this->parent_page 	= $parent_page;
		$this->page 		= 'wpfepp_settings';

		$this->tabs 		= new WPFEPP_Tab_Collection();
		$media_tab 			= new WPFEPP_Tab_Settings_Media($version, 'media', __('Media', 'wpfepp-plugin'));
		$list_tab 			= new WPFEPP_Tab_Settings_List($version, 'post_list', __('Post List', 'wpfepp-plugin'));
		$data_tab 			= new WPFEPP_Tab_Settings_Data($version, 'data', __('Data', 'wpfepp-plugin'));
		$errors_tab 		= new WPFEPP_Tab_Settings_Errors($version, 'errors', __('Errors', 'wpfepp-plugin'));
		$email_tab 			= new WPFEPP_Tab_Settings_Email($version, 'email', __('Email', 'wpfepp-plugin'));
		$copyscape_tab 		= new WPFEPP_Tab_Settings_CopyScape($version, 'copyscape', __('CopyScape', 'wpfepp-plugin'));
		$recaptcha_tab 		= new WPFEPP_Tab_Settings_ReCaptcha($version, 'recaptcha', __('ReCaptcha', 'wpfepp-plugin'));

		$this->tabs->add($media_tab);
		$this->tabs->add($list_tab);
		$this->tabs->add($data_tab);
		$this->tabs->add($errors_tab);
		$this->tabs->add($email_tab);
		$this->tabs->add($copyscape_tab);
		$this->tabs->add($recaptcha_tab);
	}

	private function load_dependencies() {
		require_once plugin_dir_path( __FILE__ ) . 'class-wpfepp-tab-collection.php';
		require_once plugin_dir_path( __FILE__ ) . 'class-wpfepp-tab-settings-data.php';
		require_once plugin_dir_path( __FILE__ ) . 'class-wpfepp-tab-settings-email.php';
		require_once plugin_dir_path( __FILE__ ) . 'class-wpfepp-tab-settings-errors.php';
		require_once plugin_dir_path( __FILE__ ) . 'class-wpfepp-tab-settings-list.php';
		require_once plugin_dir_path( __FILE__ ) . 'class-wpfepp-tab-settings-media.php';
		require_once plugin_dir_path( __FILE__ ) . 'class-wpfepp-tab-settings-copyscape.php';
		require_once plugin_dir_path( __FILE__ ) . 'class-wpfepp-tab-settings-recaptcha.php';
	}

	/**
	 * Adds the actions of this class. The WPFEPP_Loader class registers this function with WordPress.
	 **/
	public function add_actions(){
		add_action( 'admin_menu', array($this, 'add_menu_item') );

		$this->tabs->add_actions();
	}

	/**
	 * Adds our options panel to the admin menu. This method is registered with WordPress by the add_actions() function above.
	 **/
	public function add_menu_item(){
		$this->page_hook = add_submenu_page( $this->parent_page, __('Frontend Publishing Settings', 'wpfepp-plugin'), __('Settings', 'wpfepp-plugin'), 'manage_options', $this->page, array($this, 'render_settings_page') );
	}
	
	/**
	 * Callback function for add_submenu_page(). Outputs the HTML for our options panel.
	 **/
	public function render_settings_page(){
		?>
		<div class="wrap">
		<div id="icon-options-general" class="icon32"></div>
		<h2><?php _e('Frontend Publishing Settings', 'wpfepp-plugin'); ?></h2>
			<?php settings_errors(); ?>
			<?php $this->tabs->display(); ?>
		</div>
		<?php
	}

	/**
	 * Adds settings that are not currently in the database. Used when the plugin is updated.
	 *
	 **/
	public function update_settings(){
		$current_media_settings = get_option('wpfepp_media_settings', array());
		$default_media_settings = array(
			'max_upload_size' 		=> 500,
			'own_media_only' 		=> '1',
			'allowed_media_types' 	=> array('image' => '1', 'video' => '0', 'text' => '0', 'audio'=> '0', 'office' => '0', 'open_office' => '0', 'wordperfect' => '0', 'iwork' => '0', 'misc'=>'0'),
			'exempt_roles' 			=> wpfepp_prepare_default_role_settings(),
			'force_allow_uploads' 	=> '0'
		);
		$final_media_settings = wpfepp_update_array($current_media_settings, $default_media_settings);
		update_option('wpfepp_media_settings', $final_media_settings);

		$current_post_list_settings = get_option('wpfepp_post_list_settings', array());
		$default_post_list_settings = array(
			'post_list_page_len' 	=> '10',
			'post_list_cols' 		=> array('link' => '1', 'edit' => '1', 'delete' => '1'),
			'post_list_tabs'		=> array('live' => '1', 'pending' => '1', 'draft' => '1')
		);
		$final_post_list_settings = wpfepp_update_array($current_post_list_settings, $default_post_list_settings);
		update_option('wpfepp_post_list_settings', $final_post_list_settings);

		$current_data_settings = get_option('wpfepp_data_settings', array());
		$default_data_settings = array(
			'delete_on_uninstall' => '0'
		);
		$final_data_settings = wpfepp_update_array($current_data_settings, $default_data_settings);
		update_option('wpfepp_data_settings', $final_data_settings);

		$current_errors =  get_option('wpfepp_errors', array());
		$default_errors = array(
				'form' 			=> __("There are errors in your submission. Please try again.", 'wpfepp-plugin'),
				'required' 		=> __("This field is required.", 'wpfepp-plugin'),
				'min_words' 	=> __("Please enter atleast {0} words.", 'wpfepp-plugin'),
				'max_words' 	=> __("You can't enter more than {0} words.", 'wpfepp-plugin'),
				'max_links' 	=> __("You can't enter more than {0} links.", 'wpfepp-plugin'),
				'min_segments' 	=> __("Please enter atleast {0}.", 'wpfepp-plugin'),
				'max_segments' 	=> __("You can't enter more than {0}.", 'wpfepp-plugin'),
				'invalid_email'	=> __("Please enter a valid email address.", 'wpfepp-plugin'),
				'invalid_url'	=> __("Please enter a valid URL.", 'wpfepp-plugin'),
				'copyscape'		=> __("The content you have entered is not original.", 'wpfepp-plugin')
			);
		$final_errors = wpfepp_update_array($current_errors, $default_errors);
		update_option('wpfepp_errors', $final_errors);

		$current_email_settings =  get_option('wpfepp_email_settings', array());
		$default_email_settings = array(
				'sender_address'=> "",
				'sender_name' 	=> "",
				'email_format' 	=> 'plain'
			);
		$final_email_settings = wpfepp_update_array($current_email_settings, $default_email_settings);
		update_option('wpfepp_email_settings', $final_email_settings);

		$current_copyscape_settings = get_option('wpfepp_copyscape_settings', array());
		$default_copyscape_settings = array(
				'username'		=> "",
				'api_key' 		=> "",
				'block' 		=> false,
				'column_types' 	=> wpfepp_get_post_type_settings()
			);
		$final_copyscape_settings = wpfepp_update_array($current_copyscape_settings, $default_copyscape_settings);
		update_option('wpfepp_copyscape_settings', $final_copyscape_settings);

		$current_recaptcha_settings = get_option('wpfepp_recaptcha_settings', array());
		$default_recaptcha_settings = array(
				'site_key'		=> "",
				'secret' 		=> "",
				'theme' 		=> 'light'
			);
		$final_recaptcha_settings = wpfepp_update_array($current_recaptcha_settings, $default_recaptcha_settings);
		update_option('wpfepp_recaptcha_settings', $final_recaptcha_settings);
	}

	/**
	 * Removes plugin settings from the wp_options table in the database.
	 **/
	public function remove_settings(){
		delete_option('wpfepp_media_settings');
		delete_option('wpfepp_post_list_settings');
		delete_option('wpfepp_data_settings');
		delete_option('wpfepp_errors');
		delete_option('wpfepp_email_settings');
		delete_option('wpfepp_copyscape_settings');
		delete_option('wpfepp_recaptcha_settings');
		delete_option('wpfepp_version');
		delete_option('wpfepp_db_table_version');
	}
}