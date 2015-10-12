<?php
/**
 * BuddyBlog Posts Screen Functions
 *
 * Screen functions are the controllers of BuddyPress. They will execute when their
 * specific URL is caught. They will first save or manipulate data using business
 * functions, then pass on the user to a template file.
 *
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class BuddyBlog_Screens {
    
    private static $instance = null;
    
    private function __construct() {
      
    }

    public static function get_instance() {
        
        if( is_null( self::$instance ) ) {
        
			self::$instance=new self();
		}
		
        return self::$instance;
    }
    
    /**
     * Handles My Posts screen with the single post/edit post view
     */
    public function my_posts() {
         
        if( buddyblog_is_single_post() ) {
          
			add_action( 'bp_template_content', array( $this, 'get_single_post_data' ) );
		
		} else { //list all posts by user
         
			add_action( 'bp_template_content', array( $this, 'get_posts_list_data' ) );
		}
		
        bp_core_load_template( array( 'members/single/plugins' ) );
    }
   /**
    * New  post form
    */ 
    
    public function new_post() {
        //the new post form
        add_action( 'bp_template_content', array( $this, 'get_edit_post_data' ) );
		
        bp_core_load_template( array( 'members/single/plugins' ) );
    }
   
    /**
     * Handle the edit view
     */
    public function edit_post() {
		
        add_action( 'bp_template_content', array( $this, 'get_edit_post_data' ) );
         
        bp_core_load_template( array( 'members/single/plugins' ) );
    }
    
   
   
  /*
   * The single Post screen data
   */
    public function get_single_post_data() {
		
        ob_start();
		
        buddyblog_load_template( 'single.php' );
        
		$content = ob_get_clean();
        
		echo $content;
    }
    
    /**
     * List of Posts data
     */
    public function get_posts_list_data() {
		
        ob_start();
        
		buddyblog_load_template( 'posts.php' );
        
		$content = ob_get_clean();
		
        echo $content;
    }
	
    /**
     * Edit Post data 
     */
    public function get_edit_post_data() {
		
        ob_start();
        
		buddyblog_load_template( 'edit.php' );
        
		$content = ob_get_clean();
        
		echo $content;
    }

}   


BuddyBlog_Screens::get_instance();
