<?php

/**
 * A class that handles the ajax calls made by the submission form.
 * 
 * @since 1.0.0
 * @package WPFEPP
 **/
class WPFEPP_Form_Ajax
{
	/**
	 * Class constructor. Loads the file of WPFEPP_Form class.
	 **/
	public function __construct( $version ){
		$this->version 		= $version;
		require_once plugin_dir_path( dirname(__FILE__) ) . 'includes/class-wpfepp-form.php';
	}

	/**
	 * Adds ajax actions using wp_ajax_* hooks. The WPFEPP_Loader class registers this function with WordPress.
	 **/
	public function add_actions(){
		add_action( 'wp_ajax_wpfepp_handle_submission_ajax', array($this, 'handle_submission_ajax') );
		add_action( 'wp_ajax_wpfepp_get_thumbnail', array($this, 'get_thumbnail') );
	}

	/**
	 * Ajax function that processes data submitted by users and prints out the appropriate response as a json encoded string.
	 *
	 * @param array The $_POST array.
	 * @return string A json encoded string.
	 **/
	public function handle_submission_ajax(){
		$form_id 	= $_POST['form_id'];
		$form 		= new WPFEPP_Form($this->version, $form_id);
		
		if( $_POST['req_type'] == 'submit' ){
			$result 	= $form->handle_submission($_POST);
		}
		elseif( $_POST['req_type'] == 'save' ){
			$result 	= $form->save_draft($_POST);
		}
		die(json_encode($result));
	}

	/**
	 * Ajax function that prints out the HTML of a thumbnail. Used when the user selects a featured image.
	 *
	 * @param array The $_POST array.
	 * @return string HTML source of a thumbnail.
	 **/
	public function get_thumbnail(){
		$image_id = $_POST['id'];
		ob_start();
		echo wp_get_attachment_image( $image_id, array(200,200) );
		$image = ob_get_clean();
		$return_val = array('success'=>true, 'image'=>$image);
		die(json_encode($return_val));
	}

}

?>