<?php

/**
 * WPFEPP Form is the class responsible for generating HTML for forms and handling submissions.
 *
 * This class is used by WPFEPP_Shortcode_Manager to display a form on the WordPress frontend.
 * It is also responsible for taking user input, performing checks and creating posts.
 *
 * @since 1.0.0
 * @package WPFEPP
 **/

class WPFEPP_Form
{
	/**
	 * Plugin version.
	 *
	 * @access private
	 * @var string
	 **/
	private $version;
	
	/**
	 * An instance of our table class for making database calls
	 * 
	 * @access private
	 * @var WPFEPP_DB_Table
	 **/
	private $db;

	/**
	 * Id of the form from the database table
	 *
	 * @access private
	 * @var integer
	 **/
	private $id;

	/**
	 * Name of the form from the database table
	 *
	 * @access private
	 * @var string
	 **/
	private $name;

	/**
	 * A short description of this form from the database table
	 *
	 * @access private
	 * @var string
	 **/
	private $description;

	/**
	 * The post type for which the current form will work
	 *
	 * @access private
	 * @var string
	 **/
	private $post_type;

	/**
	 * An array containing all the form fields and their restrictions. The array is stored in the Database as a serialized string.
	 *
	 * @access private
	 * @var string
	 **/
	private $fields;

	/**
	 * An array containing all the form settings. The array is stored in the Database as a serialized string.
	 *
	 * @access private
	 * @var string
	 **/
	private $settings;

	/**
	 * A boolean flag that keeps track of whether the form exists in the database table or not.
	 *
	 * @access private
	 * @var boolean
	 **/
	private $valid;

	/**
	 * Fetches the form data from the database table and initializes all the class attributes.
	 * 
	 * @param int $form_id The row ID of this form from the database table.
	 **/
	public function __construct($version, $form_id = -1)
	{
		$this->load_dependencies();

		$this->version 		= $version;

		if($form_id < 0){
			$this->valid = false;
			return;
		}

		require_once plugin_dir_path( dirname(__FILE__) ) . 'includes/class-wpfepp-db-table.php';
		$this->id 	= $form_id;
		$this->db 	= WPFEPP_DB_Table::get_instance();
		$row 		= $this->db->get($form_id);

		$this->captcha = new WPFEPP_Captcha($this->version);

		if($row)
			$this->valid = true;
		else
			$this->valid = false;

		if($this->valid){
			$this->name 		= $row['name'];
			$this->description 	= $row['description'];
			$this->post_type 	= $row['post_type'];
			$this->fields 		= $row['fields'];
			$this->settings 	= $row['settings'];
			$this->emails 		= $row['emails'];

			//Necessary because we need to check if the post type is public before showing any links to the end user.
			$this->post_type_obj = get_post_type_object($this->post_type);
		}
	}

	private function load_dependencies(){
		require_once plugin_dir_path( __FILE__ ) . 'class-wpfepp-copyscape.php';
		require_once plugin_dir_path( __FILE__ ) . 'class-wpfepp-captcha.php';
		require_once plugin_dir_path( __FILE__ ) . 'class-wpfepp-post-previews.php';
	}

	/**
	 * The main function of this class. It is responsible for calling other functions for outputting the form and handling submissions.
	 * 
	 * @param  int $post_id The ID of the post that the form should be populated with on page load. It is used only when the form is created for editing an existing post.
	 **/
	public function display( $post_id = -1 ){
		//Make sure the form exists in the database. If it does not, display a friendly error message.
		if(!$this->valid){
			_e("No form with the specified ID was found", 'wpfepp-plugin');
			return;
		}

		if(!is_user_logged_in()){
			printf(__('You need to %s first.', 'wpfepp-plugin'), sprintf('<a href="%s">%s</a>', wp_login_url(), __('log in', 'wpfepp-plugin')));
			return;
		}

		$current = false;
		$result = false;

		//If a post id was passed to this function then load its content
		if($post_id != -1){
			$current = $this->get_post($post_id);
		}

		//If the form has been submitted, handle the submission and populate the $current variable with either the inserted post or the $_POST array
		if(isset($_POST['wpfepp-form-'.$this->id.'-submit'])){
			$result  = $this->handle_submission($_POST, 'html');
			$current = ($result['success']) ? ($this->get_post($result['post_id'])) : array_map(array($this, 'stripslashes'), $_POST);
		}

		if(isset($_POST['wpfepp-form-'.$this->id.'-save'])){
			$result  = $this->save_draft($_POST, 'html');
			$current = ($result['success']) ? ($this->get_post($result['post_id'])) : array_map(array($this, 'stripslashes'), $_POST);
		}

		//Finally print the form
		$this->print_form($current, $result);
	}

	/**
	 * A simple wrapper for PHP's own stripslashes() function. It makes sure that the original function is applied only on strings.
	 *
	 * @access private
	 *
	 * @param  string $str The string on which stripslashes() needs to be called.
	 * @return string The input string with slashed stripped out.
	 **/
	private function stripslashes($str){
		if(!is_string($str))
			return $str;
		return stripslashes(trim($str));
	}

	/**
	 * Prints the form populated with existing values ($current) and displays errors if any exist.
	 *
	 * @access private
	 *
	 * @param  array $current An array containing the current field values. These values are either fetched from the DB using $this->get_post() or from the $_POST array.
	 * @param  array $result The array obtained from handle_submission(). It contains a success flag, a list of errors/messages and id of the newly generated post.
	 * @return string The input string with slashed stripped out.
	 **/
	private function print_form($current = false, $result = false){
		include('partials/form.php');
	}

	/**
	 * Saves a post as draft.
	 * 
	 * @param  array $post_data containing all the data from the form.
	 * @param  string $error_format Dictates the format of the returned errors. Set to HTML by default.
	 * @return array An array consisting of a boolean flag that tells whether post insertion was successful, all the form errors and in case of successful post insertion, the post id. This structure has been used so that this function can be conviniently used with ajax.
	 **/
	public function save_draft($post_data, $error_format = 'html') {
		$return_val = array( 'success' => false, 'errors' => array() );

		do{
			if(!$this->settings['enable_drafts']){
				$return_val['errors']['form'][] = __('Drafts are not allowed!', 'wpfepp-plugin');
				break;
			}

			$captcha_enabled = $this->settings['captcha_enabled'];
			if($captcha_enabled && $this->captcha->keys_available() && $this->post_status($post_data['post_id']) == 'new'){
				$captcha_check = $this->captcha->check_response($post_data['g-recaptcha-response']);
				if(!$captcha_check){
					$return_val['errors']['form'][] = __('Captcha response incorrect', 'wpfepp-plugin');
					break;
				}
			}

			$post_data['post_status'] = 'draft';
			$result = $this->insert_post($post_data);
			if(is_wp_error($result)){
				$return_val['errors']['form'][] = $result->get_error_message();
				break;
			}

			$return_val['success'] 	= true;
			$return_val['post_id'] 	= $result;

			$preview_link = sprintf('<br/><a target="_blank" href="%s">%s</a>', WPFEPP_Post_Previews::make_preview_link($result), __('Preview', 'wpfepp-plugin'));
			$preview_link = ($this->post_type_obj->public) ? $preview_link : '';

			$return_val['errors']['form'][] = sprintf(
					__('The post has been saved successfully! %s', 'wpfepp-plugin'),
					$preview_link
				);

		} while(0);

		if($error_format == 'html')
			$return_val['errors'] = $this->format_errors($return_val['errors']);

		return $return_val;
	}

	private function post_status($post_id){
		if($post_id < 1)
			return 'new';
		return get_post_status( $post_id );
	}

	/**
	 * Runs a number of checks on user-submitted data and attempts to insert it into the database using helper functions.
	 *
	 * @param  array $post_data containing all the data from the form.
	 * @param  string $error_format Dictates the format of the returned errors. Set to HTML by default.
	 * @return array An array consisting of a boolean flag that tells whether post insertion was successful, all the form errors and in case of successful post insertion, the post id. This structure has been used so that this function can be conviniently used with ajax.
	 **/
	public function handle_submission($post_data, $error_format = 'html'){
		$return_val = array( 'success' => false, 'errors' => array() );
		$user_defined_errors = get_option('wpfepp_errors');

		$old_status = $this->post_status($post_data['post_id']);

		if( wpfepp_current_user_has($this->settings['instantly_publish']) )
			$post_data['post_status'] = 'publish';
		else
			$post_data['post_status'] = 'pending';

		do {
			if(!$this->valid){
				$return_val['errors']['form'][] = __('This form no longer exists.', 'wpfepp-plugin');
				break;
			}

			if( !wp_verify_nonce($post_data['_wpnonce'], 'wpfepp-form-'.$post_data['form_id'].'-nonce') ){
				$return_val['errors']['form'][] = __('You failed the security check.', 'wpfepp-plugin');
				break;
			}
			if( $post_data['post_id'] != -1 && !$this->current_user_can_edit($post_data['post_id']) ){
				$return_val['errors']['form'][] = __('You do not have permission to modify this post.', 'wpfepp-plugin');
				break;
			}
			$restriction_errors = $this->check_restrictions($post_data);
			if(count($restriction_errors)){
				$return_val['errors'] = $restriction_errors;
				$return_val['errors']['form'][] = $user_defined_errors['form'];
				break;
			}

			$captcha_enabled = $this->settings['captcha_enabled'];
			if($captcha_enabled && $this->captcha->keys_available() && $this->post_status($post_data['post_id']) == 'new'){
				$captcha_check = $this->captcha->check_response($post_data['g-recaptcha-response']);
				if(!$captcha_check){
					$return_val['errors']['form'][] = __('Captcha response incorrect', 'wpfepp-plugin');
					break;
				}
			}

			$copyscape 			= new WPFEPP_CopyScape($this->version);
			$copyscape_enabled 	= $this->settings['copyscape_enabled'];
			$copyscape_block 	= $copyscape->option('block');
			$column_msg 		= '';
			if($copyscape_enabled){
				$passed 		= $copyscape->passed($post_data);
				
				if(is_wp_error($passed)){
					$column_msg = __('ERROR: ', 'wpfepp-plugin') . $passed->get_error_message();
					$passed 	= true;
				}
				else {
					$column_msg 	= ($passed) ? __('passed', 'wpfepp-plugin') : __('failed', 'wpfepp-plugin');
				}

				$this->fields[WPFEPP_CopyScape::$meta_key] 	= array('type' => 'custom_field');
				$post_data[WPFEPP_CopyScape::$meta_key] 	= $column_msg;

				if(!$passed){
					if($copyscape_block){
						$return_val['errors']['form'][] 	= $user_defined_errors['copyscape'];
						break;
					}
					else {
						$post_data['post_status'] = 'pending';
					}
				}
			}
			else {
				$this->fields[WPFEPP_CopyScape::$meta_key] 	= array('type' => 'custom_field');
				$post_data[WPFEPP_CopyScape::$meta_key] 	= '';
			}

			$result = $this->insert_post($post_data);
			if(is_wp_error($result)){
				$return_val['errors']['form'][] = $result->get_error_message();
				break;
			}

			$return_val['success'] 	= true;
			$return_val['post_id'] 	= $result;
			$return_val['redirect_url'] = ($this->settings['redirect_url']) ? $this->settings['redirect_url'] : false;
			$action 				= ($old_status == 'new' || $old_status == 'draft')?'created':'updated';

			$preview_link 			= sprintf('<a target="_blank" href="%s">%s</a>', WPFEPP_Post_Previews::make_preview_link($result), __('Preview', 'wpfepp-plugin'));
			$permalink 				= sprintf('<a target="_blank" href="%s">%s</a>', get_post_permalink($result), __('View', 'wpfepp-plugin'));
			$final_link 			= ($post_data['post_status'] == 'publish') ? $permalink : $preview_link;
			$final_link 			= ($this->post_type_obj->public) ? $final_link : '';

			$return_val['errors']['form'][] = sprintf(
				__("The post has been %s successfully. %s %s", 'wpfepp-plugin'),
				$action,
				sprintf('<br/><a class="wpfepp-continue-editing" href="#">%s</a>', __('Continue Editing', 'wpfepp-plugin')),
				$final_link
			);

			$this->user_defined_actions(array_merge($post_data, array( 'post_id'=> $result, 'action' => $action )));

		} while (0);

		if($error_format == 'html')
			$return_val['errors'] = $this->format_errors($return_val['errors']);

		return $return_val;
	}

	/**
	 * Checks if the user-submitted data meets the minimum requirements of the form, set by the site administrator in the options panel.
	 * 
	 * This function goes through each field and makes sure that the submitted data corresponding to that field ($post_data[$key]) meets the requirements set by the admin. Note that the key of each field is used as the name attribute in the form.
	 * 
	 * @access private
	 *
	 * @param array $post_data An array containing all the data from the form.
	 * @return array $errors A multidimmensional array of errors. Each array member contains all the errors for a particular field.
	 **/
	private function check_restrictions($post_data){
		$errors = array();
		$user_defined_errors = get_option('wpfepp_errors');
		if( wpfepp_current_user_has($this->settings['no_restrictions']) )
			return $errors;

		foreach ($this->get_fields() as $key => $field) {

			if( wpfepp_is_field_supported($field['type'], $this->post_type) && isset($field['enabled']) && $field['enabled'] && isset($field['required']) && $field['required'] ){
				$stripped_value = isset($post_data[$key]) ? $post_data[$key] : "";
				if(is_string($stripped_value))
					$stripped_value = strip_tags(trim($stripped_value));

				if( empty($stripped_value) || $stripped_value == -1 || (is_array($stripped_value) && !count($stripped_value)) )
					$errors[$key][] = $user_defined_errors['required'];
				if( isset($field['min_words']) && is_numeric($field['min_words']) && $this->word_count($stripped_value) < $field['min_words'] )
					$errors[$key][] = sprintf(str_replace('{0}', '%s', $user_defined_errors['min_words']), $field['min_words']);
				if( isset($field['max_words']) && is_numeric($field['max_words']) && $this->word_count($stripped_value) > $field['max_words'] )
					$errors[$key][] = sprintf(str_replace('{0}', '%s', $user_defined_errors['max_words']), $field['max_words']);
				if( isset($field['min_count']) && is_numeric($field['min_count']) && $this->segment_count($stripped_value) < $field['min_count'] )
					$errors[$key][] = sprintf(str_replace('{0}', '%s', $user_defined_errors['min_segments']), $field['min_count']);
				if( isset($field['max_count']) && is_numeric($field['max_count']) && $this->segment_count($stripped_value) > $field['max_count'] )
					$errors[$key][] = sprintf(str_replace('{0}', '%s', $user_defined_errors['max_segments']), $field['max_count']);
				if( isset($field['max_links']) && is_numeric($field['max_links']) && $this->count_links($post_data[$key]) > $field['max_links'] )
					$errors[$key][] = sprintf(str_replace('{0}', '%s', $user_defined_errors['max_links']), $field['max_links']);
			}
		}
		return $errors;
	}

	/**
	 * Counts links (anchor tags) in a string with the help of a simple regular expression.
	 *
	 * @access private
	 *
	 * @param string $str An HTML string.
	 * @return integer Number of links in the input HTML string.
	 **/
	private function count_links($str){
		return preg_match_all('/<\s*\ba\b.*?href/', $str, $matches);
	}

	/**
	 * Inserts user submitted data into DB with WordPress' own wp_insert_post(). If insertion is successful, sets terms, saves meta and creates the thumbnail.
	 * 
	 * It traverses through the form fields and by checking their types puts the corresponding data items ($post_data[$key]) into either $post, $custom_fields, $hierarchical_taxonomies or $non_hierarchical_taxonomies. The first one is inserted using wp_insert_post(). It should be noted that the key of each field is used as the name attribute in the form. This is why we are able to access the right piece of information using $post_data[$key].
	 * 
	 * @access private
	 *
	 * @param array $post_data An array containing all the data from the form. It is actually $_POST.
	 * @return int or WP_Error Either the ID of the new post is returned or a WP_Error object.
	 **/
	private function insert_post($post_data){
		$post 			= array('post_type' => $this->post_type, 'post_status' => $post_data['post_status']);
		$custom_fields 	= array();
		$hierarchical_taxonomies = array();
		$non_hierarchical_taxonomies = array();
		$post_format = 0;
		$thumbnail = 0;

		foreach ($this->get_fields() as $key => $field) {
			switch ($field['type']) {
				case 'title':
					if(!empty($post_data[$key])) $post['post_title'] = $this->sanitize($post_data[$key], $field);
					break;
				case 'content':
					if(!empty($post_data[$key])) $post['post_content'] = $this->sanitize($post_data[$key], $field);
					break;
				case 'excerpt':
					if(!empty($post_data[$key])) $post['post_excerpt'] = $this->sanitize($post_data[$key], $field);
					break;
				case 'thumbnail':
					if(!empty($post_data[$key]) && $post_data[$key] != -1) $thumbnail = $post_data[$key];
					break;
				case 'hierarchical_taxonomy':
					if(!empty($post_data[$key])){
						if(is_array($post_data[$key]) && count($post_data[$key])){
							$hierarchical_taxonomies[$key] = $post_data[$key];
						}
						elseif(is_string($post_data[$key])){
							//This is necessary because the fallback taxonomy terms are added as commasperated IDs
							$term_ids = explode(',', $post_data[$key]);
							$term_ids = array_map('trim', $term_ids);
							$hierarchical_taxonomies[$key] = $term_ids;
						}
					} 
					break;
				case 'post_format':
					if(!empty($post_data[$key]) && $post_data[$key]) $post_format = $post_data[$key];
					break;
				case 'non_hierarchical_taxonomy':
					if(!empty($post_data[$key])) $non_hierarchical_taxonomies[$key] = $post_data[$key];
					break;
				case 'custom_field':
					if(!empty($post_data[$key])) $custom_fields[$key] = $this->sanitize($post_data[$key], $field);
					break;
				default:
					break;
			}
		}
		if( $post_data['post_id'] != -1 ) {
			$post['ID'] = $post_data['post_id'];
			$post['comment_status'] = get_post_field('comment_status', $post_data['post_id']);
		}

		$post_id = wp_insert_post($post, true);
		if( !is_wp_error($post_id) ){
			foreach ($hierarchical_taxonomies as $tax => $tax_terms) {
				wp_set_post_terms( $post_id, $tax_terms, $tax, false);
			}
			foreach ($non_hierarchical_taxonomies as $tax => $tax_terms) {
				wp_set_post_terms( $post_id, $tax_terms, $tax, false);
			}
			foreach ($custom_fields as $meta_key => $value) {
				update_post_meta( $post_id, $meta_key, $value );
			}
			
			if($thumbnail)
				set_post_thumbnail( $post_id, $thumbnail );
			else
				delete_post_thumbnail( $post_id );

			set_post_format( $post_id, $post_format );
		}
		return $post_id;
	}

	/**
	 * Takes a string and removes potentially harmful HTML and PHP tags from it. This function is run right before post insertion and the writer of the post is not shown any errors.
	 *
	 * @access private
	 *
	 * @param string $value The string from which harmful tags are to be stripped.
	 * @param array $field The settings array for this field.
	 * @return string The stripped string
	 **/
	private function sanitize($value, $field){

		if( isset($field['strip_tags']) && $field['strip_tags'] == 'all' )
			$value = wp_strip_all_tags($value);
		
		if( isset($field['strip_tags']) && $field['strip_tags'] == 'unsafe' )
			$value = wp_kses($value, $this->get_whitelist());
		
		if( isset($field['nofollow']) && $field['nofollow'] )
			$value = stripslashes(wp_rel_nofollow($value));

		return $value;
	}

	/**
	 * Fetches a post using the function get_post() and prepares it for display within our form.
	 *
	 * @access private
	 *
	 * @param integer $post_id The id of the WordPress post to be fetched.
	 * @return array An array containing the post data in the formal that can directly be used by our form.
	 **/
	private function get_post($post_id){
		$form_post = array();
		$post_obj = get_post( $post_id );
		foreach ($this->get_fields() as $key => $field) {
			switch ($field['type']) {
				case 'title':
					$form_post[$key] = $post_obj->post_title;
					break;
				case 'content':
					$form_post[$key] = $post_obj->post_content;
					break;
				case 'excerpt':
					$form_post[$key] = $post_obj->post_excerpt;
					break;
				case 'thumbnail':
					$form_post[$key] = get_post_thumbnail_id( $post_id );
					break;
				case 'hierarchical_taxonomy':
					$form_post[$key] = wp_get_post_terms($post_id, $key, array("fields" => "ids"));
					break;
				case 'post_format':
					$form_post[$key] = get_post_format($post_id);
					break;
				case 'non_hierarchical_taxonomy':
					$term_names = wp_get_post_terms($post_id, $key, array("fields" => "names"));
					$form_post[$key] = implode(', ', $term_names);
					break;
				case 'custom_field':
					$post_meta = get_post_meta( $post_id, $key, true );
					$form_post[$key] = ($post_meta)?$post_meta:"";
					break;
				default:
					break;
			}
		}
		$form_post['post_id'] = $post_id;
		return $form_post;
	}

	/**
	 * Prints all the restrictions for a field (for instance outputs 'required=""' on required fields). Used while printing form elements.
	 *
	 * @access private
	 *
	 * @param array $field An array containing field data.
	 * @return string A string containing all the restrictions for the field, ready to be inserted in the form element.
	 **/
	private function print_restrictions($field){

		$restriction_array = array();

		if(isset($field['multiple']) && $field['multiple'])
			$restriction_array[] = 'multiple';

		if( wpfepp_current_user_has($this->settings['no_restrictions']) ){
			$restriction_string = implode(' ', $restriction_array);
			return $restriction_string;
		}

		if(isset($field['required']) && $field['required']){
			if($field['type'] == 'thumbnail')
				$restriction_array[] = 'hiddenrequired="1"';
			else
				$restriction_array[] = 'required';
		}
		if(isset($field['min_words']) && $field['min_words'] && is_numeric($field['min_words']))
			$restriction_array[] = sprintf('minwords="%d"', $field['min_words']);
		if(isset($field['max_words']) && $field['max_words'] && is_numeric($field['max_words']))
			$restriction_array[] = sprintf('maxwords="%d"', $field['max_words']);
		if(isset($field['min_links']) && $field['min_links'] && is_numeric($field['min_links']))
			$restriction_array[] = sprintf('minlinks="%d"', $field['min_links']);
		if(isset($field['max_links']) && $field['max_links'] && is_numeric($field['max_links']))
			$restriction_array[] = sprintf('maxlinks="%d"', $field['max_links']);
		if(isset($field['min_count']) && $field['min_count'] && is_numeric($field['min_count']))
			$restriction_array[] = sprintf('minsegments="%d"', $field['min_count']);
		if(isset($field['max_count']) && $field['max_count'] && is_numeric($field['max_count']))
			$restriction_array[] = sprintf('maxsegments="%d"', $field['max_count']);

		$restriction_string = implode(' ', $restriction_array);
		return $restriction_string;
	}

	/**
	 * Takes a multidimensional array and converts every second level array (the errors for an individual field) into an HTML string for output.
	 *
	 * @access private
	 *
	 * @param array $errors A 2D array of errors.
	 * @return array A 1D array in which each element is an HTML string containing
	 **/
	private function format_errors($form_errors){
		$errors_formatted = array();
		foreach ($form_errors as $key => $field_errors) {
			$errors_formatted[$key] = '<ul><li>'.implode('</li><li>', $field_errors).'</li></ul>';
		}
		return $errors_formatted;
	}

	/**
	 * Counts comma seperated segments in a string. Used for counting the terms of non-hierarichal taxonomies.
	 *
	 * @access private
	 *
	 * @param string $str The string of terms
	 * @return integer Number of comma-seperated terms
	 **/
	private function segment_count($str){
		if(!trim($str))
			return 0;

		$segments = explode(',', trim($str));
		return count($segments);
	}

	/**
	 * Prints out the image source of a thumbnail
	 *
	 * @access private
	 *
	 * @param integer $image_id The thumbnail ID.
	 **/
	private function output_thumbnail($image_id){
		if(empty($image_id) || $image_id == -1)
			return;
		echo wp_get_attachment_image( $image_id, array(200,200) );
	}

	/**
	 * Builds and returns a whitelist array of safe HTML tags and attributes to be used with wp_kses
	 *
	 * @access private
	 *
	 * @return array An array of safe HTML tags and their attributes.
	 **/
	private function get_whitelist(){
		$allowed_attrs = array(
								'class' => array(),
								'id' 	=> array(),
								'style' => array(),
								'title' => array()
							);
		$allowed_html = array(
						    'a' 	=> array_merge( $allowed_attrs, array( 'href' => array() ) ),
						    'img' 	=> array_merge( 
						    				$allowed_attrs,
						    				array(
										    	'src' => array(),
										    	'alt' => array(),
										    	'width' => array(),
										    	'height' => array()
								    		)
						    			),
						    'ins' 	=> array_merge($allowed_attrs, array('datetime'=>array())),
						    'del' 	=> array_merge($allowed_attrs, array('datetime'=>array())),
						    'p' 	=> $allowed_attrs,
						    'br' 	=> $allowed_attrs,
						    'em' 	=> $allowed_attrs,
						    'b' 	=> $allowed_attrs,
						    'ol'	=> $allowed_attrs,
						    'ul'	=> $allowed_attrs,
						    'li'	=> $allowed_attrs,
						    'table' => $allowed_attrs,
						    'tbody' => $allowed_attrs,
						    'tr' 	=> $allowed_attrs,
						    'td'	=> $allowed_attrs,
						    'div' 	=> $allowed_attrs,
						    'code' 	=> $allowed_attrs,
						    'pre' 	=> $allowed_attrs,
						    'sub' 	=> $allowed_attrs,
						    'sup' 	=> $allowed_attrs,
						    'span' 	=> $allowed_attrs,
						    'q' 	=> $allowed_attrs,
						    'code' 	=> $allowed_attrs,
						    'h1' 	=> $allowed_attrs,
						    'h2' 	=> $allowed_attrs,
						    'h3' 	=> $allowed_attrs,
						    'h4' 	=> $allowed_attrs,
						    'h5' 	=> $allowed_attrs,
						    'h6' 	=> $allowed_attrs,
						    'abbr'	=> $allowed_attrs,
						    'strong'		=> $allowed_attrs,
						    'blockquote' 	=> $allowed_attrs,
						    'address' 		=> $allowed_attrs,
						);
		$allowed_html = apply_filters('wpfepp_form_'.$this->id.'_safe_tags', $allowed_html);
		return $allowed_html;
	}

	/**
	 * A simple getter function for the fields attribute.
	 *
	 * @return array An array containing all field data.
	 **/
	public function get_fields(){
		return $this->fields;
	}

	/**
	 * A simple getter function for the settings attribute.
	 *
	 * @return array An array containing all field data.
	 **/
	public function get_settings(){
		return $this->settings;
	}

	/**
	 * A simple getter function for the emails attribute.
	 *
	 * @return array An array containing email data.
	 **/
	public function get_emails(){
		return $this->emails;
	}

	/**
	 * Simple getter function for checking form validity
	 *
	 * @return boolean
	 **/
	public function valid()
	{
		return $this->valid;
	}

	/**
	 * Simple getter function for post type
	 *
	 * @return string Post type of the form.
	 **/
	public function post_type()
	{
		return $this->post_type;
	}

	/**
	 * Prints out user defined form fields with the help of do_action() function provided by WordPress.
	 *
	 * @access private
	 **/
	private function user_defined_fields($current_values){
		do_action( 'wpfepp_form_'.$this->id.'_fields', $current_values );
		do_action( 'wpfepp_form_fields', $current_values, $this );
	}

	/**
	 * Gives users the ability to perform custom operations on the post data after a post has been successfully added/updated.
	 *
	 * @access private
	 **/
	private function user_defined_actions($post_data){
		do_action( 'wpfepp_form_'.$this->id.'_actions', $post_data );
		do_action( 'wpfepp_form_actions', $post_data, $this );
	}

	/**
	 * By default WordPress does not allow subscribers and contributors to edit their own posts. This function aims rectifies this problem.
	 *
	 * @access private
	 *
	 * @param string $action The action to check.
	 * @param int Post id.
	 * @return boolean Whether or not the current user can perform the specified action.
	 **/
	private function current_user_can_edit($post_id){
		$post_author_id = get_post_field( 'post_author', $post_id );
		global $current_user;
		get_currentuserinfo();

		return ( $post_author_id == $current_user->ID || current_user_can('edit_post', $post_id) );
	}

	private function word_count($str){
		$str = preg_replace('/\s+/', ' ', strip_tags($str));
		return ( substr_count($str, ' ') + 1 );
	}

	private function print_spaces($times){
		for ($i=0; $i < $times; $i++) { 
			echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
		}
	}
	
	private function hierarchical_taxonomy_options($taxonomy, $args, $current, $level = -1){
		$level++;
		$terms = get_terms( $taxonomy, $args );
		if(!count($terms))
			return;
		?>
			<?php foreach($terms as $term_key => $term): ?>
				<option value="<?php echo $term->term_id; ?>" <?php if( is_array($current) && in_array($term->term_id, $current) ) echo 'selected="selected"'; ?> >
					<?php $this->print_spaces($level); ?><?php echo $term->name; ?>
				</option>
				<?php $this->hierarchical_taxonomy_options($taxonomy, array_merge( $args, array('parent' => $term->term_id) ), $current, $level); ?>
			<?php endforeach; ?>
		<?php
	}

}

?>