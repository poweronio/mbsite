<?php

/**
 * Creates a table of posts along with a form for editing posts. Also responsible for handling post deletion requests.
 *
 * @since 1.0.0
 * @package WPFEPP
 **/
class WPFEPP_Post_List
{
	/**
	 * Plugin version.
	 *
	 * @access private
	 * @var string
	 **/
	private $version;
	
	/**
	 * An instance of the WPFEPP_Form class for editing posts.
	 *
	 * @access private
	 * @var WPFEPP_Form
	 **/
	private $form;

	/**
	 * A boolean flag that keeps track of whether the form exists in the database table or not.
	 *
	 * @access private
	 * @var boolean
	 **/
	private $valid;

	/**
	 * The post type for which we want to display posts
	 *
	 * @access private
	 * @var string
	 **/
	private $post_type;

	/**
	 * Class constructor. Initializes the attributes of the object.
	 **/
	public function __construct($version, $form_id = -1)
	{
		$this->version 		= $version;

		if($form_id < 0){
			$this->valid = false;
			return;
		}

		require_once plugin_dir_path( dirname(__FILE__) ) . 'includes/class-wpfepp-form.php';
		$this->form = new WPFEPP_Form($this->version, $form_id);

		if(!$this->form->valid()){
			$this->valid = false;
			return;
		}

		$this->valid = true;
		$this->post_type = $this->form->post_type();
	}

	/**
	 * Adds the actions of the class. The WPFEPP_Loader class registers this function with WordPress.
	 **/
	public function add_actions(){
		add_action('wp', array($this, 'handle_deletion_request'));
		add_action( 'wp_ajax_wpfepp_delete_post', array($this, 'delete_post_ajax') );
	}

	/**
	 * Outputs the HTML of the post list or the form (depending on $_GET variables).
	 **/
	public function display(){

		if(!$this->valid){
			_e("No form with the specified ID was found", 'wpfepp-plugin');
			return;
		}

		if(!is_user_logged_in()){
			printf(__('You need to %s first.', 'wpfepp-plugin'), sprintf('<a href="%s">%s</a>', wp_login_url(), __('log in', 'wpfepp-plugin')));
			return;
		}

		if( isset($_GET['wpfepp_post']) && isset($_GET['wpfepp_action']) && is_numeric($_GET['wpfepp_post']) && $_GET['wpfepp_action'] == 'edit' ){
			$this->form->display($_GET['wpfepp_post']);
		}
		else
			$this->print_list();
	}

	/**
	 * Takes care of post deletion and redirects the user back to the post list table with a new query variable 'wpfepp_deleted'.
	 **/
	public function handle_deletion_request(){
		if( isset($_GET['wpfepp_post']) && isset($_GET['wpfepp_action']) && is_numeric($_GET['wpfepp_post']) && isset($_GET['_wpnonce']) && $_GET['wpfepp_action'] == 'delete' ){
			$blog_page 		= isset($_GET['p']) ? array('p', $_GET['p']) : array();
			$result 		= $this->delete_post($_GET['wpfepp_post'], $_GET['_wpnonce']);
			$success_vars 	= ($result['success']) ? array( 'wpfepp_deleted' => 1 ) : array();
			$sendback 		= esc_url_raw( add_query_arg( array_merge($blog_page, $success_vars), '' ) );
			wp_redirect( $sendback );
		}
	}

	/**
	 * Deletes posts after checking nonce and making sure that the current user has permission to perform the deletion. Uses WordPress' own wp_delete_post().
	 *
	 * @access private
	 *
	 * @param int $post_id The id of the post that we want to delete.
	 * @param string $delete_nonce A nonce string that ensures that the request is coming from the right person.
	 * @return array An associative array containing a status flag and a message to display to the user.
	 **/
	private function delete_post($post_id, $delete_nonce){
		$data = array('success' => false, 'message' => '');
		do{
			if(!wp_verify_nonce($delete_nonce, 'wpfepp-delete-post-'.$post_id.'-nonce')){
				$data['message'] = __('Sorry! You failed the security check', 'wpfepp-plugin');
				break;
			}	

			if(!$this->current_user_can_delete($post_id)){
				$data['message'] = __("You don't have permission to delete this post", 'wpfepp-plugin');
				break;
			}

			$result = wp_delete_post( $post_id, false );
			if(!$result){
				$data['message'] = __("The article could not be deleted", 'wpfepp-plugin');
				break;
			}

			$data['success'] = true;
			$data['message'] = __('The article has been deleted successfully!', 'wpfepp-plugin');
		}
		while (0);

		return $data;
	}

	/**
	 * Ajax function that processes the deletion request and prints out the appropriate response as a json encoded string.
	 *
	 * @param array The $_POST array.
	 * @return string A json encoded string.
	 **/
	public function delete_post_ajax(){
		die(json_encode($this->delete_post($_POST['post_id'], $_POST['delete_nonce'])));
	}

	/**
	 * Outputs HTML of the post list table.
	 *
	 * @access private
	 **/
	private function print_list(){
		include('partials/post-list.php');
	}

	/**
	 * By default WordPress does not allow subscribers and contributors to delete their own posts. This function aims rectifies this problem.
	 *
	 * @access private
	 *
	 * @param string $action The action to check.
	 * @param int Post id.
	 * @return boolean Whether or not the current user can perform the specified action.
	 **/
	private function current_user_can_delete($post_id){
		$post_author_id = get_post_field( 'post_author', $post_id );
		global $current_user;
		get_currentuserinfo();

		return ( $post_author_id == $current_user->ID || current_user_can('delete_post', $post_id) );
	}
}

?>