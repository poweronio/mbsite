<?php
/*
 * Plugin Name: Facebook Like User Activity Stream For BuddyPress
 * Author: Brajesh Singh
 * Author URI: http://buddydev.com/members/sbrajesh/
 * Plugin URI: http://buddydev.com/plugins/facebook-user-like-activity-stream-for-bddypress/
 * Version: 1.1.5
 * Description: It shows relevant social stream of a user(includes friends stream as well as users/user groups)
 * License: GPL
 * Last Updated: January 25, 2015
 *  
 * 
*/
if( ! defined( 'MYSTREAM_ACTIVITY_SLUG' ) )
    define ( 'MYSTREAM_ACTIVITY_SLUG', 'my-stream-activity' );

/**
 * Helper class
 * 
 */
class Devb_Fblike_Activity_Helper {
    
    private static $instance;
    
    private function __construct() {
        
        add_action( 'bp_activity_setup_nav', array( $this, 'add_activity_nav' ) );
      
 
        
        //fix delete link on mystream activity
        add_filter( 'bp_activity_delete_link', array( $this, 'fix_delete_link' ), 10, 2 );
        
        //show post form on the my stream page
        add_action( 'bp_before_member_activity_post_form', array( $this, 'show_post_form' ) );
        //redirect users to stream page when viewing profile(home page) by default
        //add_action('init',array(&$this,'fix_nav'));
        //load textdomain
        add_action( 'bp_loaded', array( $this, 'load_textdomain' ), 2 );
        add_action( 'bp_setup_nav', array( $this, 'nav_default' ), 5 );
    }
    
    public static function get_instance() {
        
        if( ! isset( self::$instance ) )
            self::$instance = new self();
        
        return self::$instance;
    }
    //load textdomain
    public function load_textdomain() {
        
		$locale = apply_filters( 'bpfblike_activity_stream_textdomain_get_locale', get_locale() );
        
      
        // if load .mo file
        if ( ! empty( $locale ) ) {
            $mofile_default = sprintf( '%slanguages/%s.mo', plugin_dir_path( __FILE__ ), $locale );

            $mofile = apply_filters( 'bpfblike_activity_stream_textdomain_mofile', $mofile_default );

            if ( file_exists( $mofile ) ) {
                        // make sure file exists, and load it
                load_textdomain( 'fb-like-activity-stream', $mofile );
            }
        }
    }
    //add Your Stream to activity nav for logged in users
    public function add_activity_nav() {
		
        $bp = buddypress();
   
        if( ! bp_is_user() || !is_user_logged_in() || !bp_is_my_profile() )
            return;
   
        $activity_link = bp_core_get_user_domain( bp_loggedin_user_id() ) . $bp->activity->slug . '/';
        //add to user activity subnav if it is logged in users profile
        bp_core_new_subnav_item( array( 'name' => __( 'News Feed', 'fb-like-activity-stream' ), 'slug' => MYSTREAM_ACTIVITY_SLUG, 'parent_url' => $activity_link, 'parent_slug' => $bp->activity->slug, 'screen_function' => array($this,'activity_screen'), 'position' => 2,'user_has_access'=>  bp_is_my_profile() ) );
      
         //bp_core_new_nav_default(array('parent_slug'=>$bp->activity->slug,'subnav_slug'=>MYSTREAM_ACTIVITY_SLUG,'screen_function'=>array($this,'activity_screen')));
     
        bp_core_remove_subnav_item( 'activity', 'just-me' );
        
        $sub_nav = array(
                'name'            => __( 'My Posts', 'buddypress' ),
                'slug'            => 'personal',//did you note this?
                'parent_url'      => $activity_link,
                'parent_slug'     => $bp->activity->slug,
                'screen_function' => 'bp_activity_screen_my_activity',
                'position'        => 10
            );
        bp_core_new_subnav_item( $sub_nav );
	
    }

    //loading the activity stream on your stream tab
    //just load the home page and It will do the rest(since we are using subnav of activity, the activity template will be loaded)
    public function activity_screen(){

            do_action( 'bp_mystream_activity_screen' );
            bp_core_load_template( apply_filters( 'bp_activity_template_mystream_activity', 'members/single/home' ) ); 
    }
    
    //make your stream the default nav
    public function nav_default() {
        if( !bp_is_my_profile() )
              return;
             
        global $bp;
       
        bp_core_new_nav_default( array( 'parent_slug' => $bp->activity->slug, 'subnav_slug' => MYSTREAM_ACTIVITY_SLUG, 'screen_function' => array( $this, 'activity_screen' ) ) );
     }
    //show post form on the stream page
    public function show_post_form(){

        if ( is_user_logged_in() && self::is_stream() )  : 
            bp_locate_template( array( 'activity/post-form.php'), true ) ;
        endif; 
    }

    //fix delete link for the activity items no belonging to current user, bp does not honour it by default
    public function fix_delete_link( $del_link, $activity ){
        global $bp;
        
        if( bp_is_my_profile() && bp_is_activity_component() && bp_is_current_action( MYSTREAM_ACTIVITY_SLUG ) ){
            //let us apply our mod
            if ( ( is_user_logged_in() && $activity->user_id == bp_loggedin_user_id() ) || is_super_admin() )
                return $del_link;
            return '';
        }
        return $del_link;
    }
 //static method
 //is it my stream page   
    public function is_stream(){
     
       global $bp;
       if( bp_is_my_profile() && bp_is_activity_component() && bp_is_current_action( MYSTREAM_ACTIVITY_SLUG ) )
               return true;
       return false;
    }

}

Devb_Fblike_Activity_helper::get_instance();

//implemented as a singleton pattern
class BPDevMyActivityStream{
    
    private static $instance;
    
    private function __construct(){
      
      
        //filter activity to be shown
        add_filter( 'bp_activity_get', array( $this, 'filter_activity' ), 20, 2 );
        

    }
    
    public static function get_instance() {
        
        if( !isset( self::$instance ) )
            self::$instance = new self();
        
        return self::$instance;
    }
    



    //if this is my stream page, filter the activity
    public function filter_activity( $activity, $args ){
        $bp = buddypress();
     
       if( bp_is_my_profile() && bp_is_activity_component() && bp_is_current_action( MYSTREAM_ACTIVITY_SLUG ) ){
        
            $logged_user_id = bp_loggedin_user_id();
         
            $my_friends = (array)self::get_friend_user_ids( $logged_user_id );
            // $my_friends[]=$bp->loggedin_user->id;
            //if group component is active
         
            if( function_exists('groups_get_user_groups') ){
            
                $groups = groups_get_user_groups( $logged_user_id );
                $object = $bp->groups->id;
                $primary_id = implode( ',', (array)$groups['groups'] );
                $args['filter']['object'] = $object;
                $args['filter']['primary_id'] = $primary_id;
            
            }
        
            $args['filter']['user_ids'] = join( ',', $my_friends );
        
            extract( $args, EXTR_SKIP );
        
            $activity = self::get( $max, $page, $per_page, $sort, $search_terms, $filter, $display_comments, $show_hidden );
        
 
        }
      return $activity;
    }
    
    /**
     * Get friends/Following user ids
     * @param type $logged_user_id
     * @return type
     */
    public static function get_friend_user_ids( $logged_user_id ){

		$friend_ids = array();
        if( function_exists( 'friends_get_friend_user_ids' ) )
            $friend_ids =  friends_get_friend_user_ids( $logged_user_id );
        elseif( function_exists('bp_follow_get_following' ) )
            $friend_ids = bp_follow_get_following ( array('user_id'=> $logged_user_id ) );
		
		return apply_filters( 'fblike_activity_get_friend_ids', $friend_ids, $logged_user_id );

    }


// a copy of BP_Activity_Activity:get method, heavily modded for our use. My sincere thanks to @buddypress dev team
//helper

//get my public/private activities+friends public activities+groups/other relevant activities
	public function get( $max = false, $page = 1, $per_page = 25, $sort = 'DESC', $search_terms = false, $filter = false, $display_comments = false, $show_hidden = false ) {
		global $wpdb, $bp;

		/* Select conditions */
		$select_sql = "SELECT a.*, u.user_email, u.user_nicename, u.user_login, u.display_name";
                
                //from conditions
		$from_sql = " FROM {$bp->activity->table_name} a LEFT JOIN {$wpdb->users} u ON a.user_id = u.ID";
                 
		/* Sorting */
		if ( $sort != 'ASC' && $sort != 'DESC' )
			$sort = 'DESC';

		               
        //get or sql, to support your component, please filter on fb_like_activity_or_sql
        $or_sql=apply_filters("fb_like_activity_or_sql",self::get_filter_sql( $filter ),$filter);

        //get and sql
        $and_sql=apply_filters("fb_like_activity_or_sql",self::get_action_sql($filter),$filter);

                
        if(!empty($or_sql))
            $where_sql="( ".$or_sql." )";
        
        if(!empty ($and_sql))
            $where_sql= $where_sql." AND ".$and_sql;
                   
        $activity_sql="{$select_sql} {$from_sql}  WHERE {$where_sql}  ORDER BY date_recorded {$sort}";
                
                       
		if ( $per_page && $page ) {
			$pag_sql = $wpdb->prepare( "LIMIT %d, %d", intval( ( $page - 1 ) * $per_page ), intval( $per_page ) );
			$activities = $wpdb->get_results(  "{$activity_sql} {$pag_sql}" );
		} else{
                    
			$activities = $wpdb->get_results(  "{$activity_sql} {$pag_sql}"  );
        } 
                 // echo //needs mod
		$total_activities = $wpdb->get_var(  "SELECT count(a.id) FROM {$bp->activity->table_name} a WHERE {$where_sql} ORDER BY a.date_recorded {$sort}"  );

		/* Get the fullnames of users so we don't have to query in the loop */
		if ( function_exists( 'xprofile_install' ) && $activities ) {
			foreach ( (array)$activities as $activity ) {
				if ( (int)$activity->user_id )
					$activity_user_ids[] = $activity->user_id;
			}

			$activity_user_ids = implode( ',', array_unique( (array)$activity_user_ids ) );
            
			if ( !empty( $activity_user_ids ) ) {
                
				if ( $names = $wpdb->get_results(  "SELECT user_id, value AS user_fullname FROM {$bp->profile->table_name_data} WHERE field_id = 1 AND user_id IN ({$activity_user_ids})"  ) ) {
					
                    foreach ( (array)$names as $name )
						$tmp_names[$name->user_id] = $name->user_fullname;

					foreach ( (array)$activities as $i => $activity ) {
						if ( !empty( $tmp_names[$activity->user_id] ) )
							$activities[$i]->user_fullname = $tmp_names[$activity->user_id];
					}

					unset( $names );
					unset( $tmp_names );
				}
			}
		}

		if ( $activities && $display_comments )
			$activities = BP_Activity_Activity::append_comments( $activities );

		/* If $max is set, only return up to the max results */
		if ( !empty( $max ) ) {
			if ( (int)$total_activities > (int)$max )
				$total_activities = $max;
		}

		return array( 'activities' => $activities, 'total' => (int)$total_activities );
	}
 // a copy of BP_Activity_Activity::get_filter_sql method modded for our use, it takes into account the passed user ids list       
    
        
    public function get_filter_sql( $filter_array ) {
		global $wpdb,$bp;
                
                //single user is
                
        if ( !empty( $filter_array['user_id'] ) ) {
			$user_filter = explode( ',', $filter_array['user_id'] );
			$user_sql = ' ( a.user_id IN ( ' . $filter_array['user_id'] . ' ) )';
			$filter_sql[] = $user_sql;
		}
                
                //multiple user ids
		if ( !empty( $filter_array['user_ids'] ) ) {
			$user_filter = explode( ',', $filter_array['user_ids'] );
			$user_sql = ' ( a.user_id IN ( ' . $filter_array['user_ids'] . ' ) AND a.hide_sitewide=0 )';
			$filter_sql[] = $user_sql;
		}
                
                //specific objects are passed
                
                
        if(!empty( $filter_array['object'] )&&!empty( $filter_array['primary_id'] ))
                    $filter_sql[]=self::get_object_sql($filter_array);
		//echo self::get_object_sql($filter_array)."<br/>";

	

		if ( !empty( $filter_array['secondary_id'] ) ) {
			$sid_filter = explode( ',', $filter_array['secondary_id'] );
			$sid_sql = ' ( ';

			$counter = 1;
			foreach( (array) $sid_filter as $sid ) {
				$sid_sql .= $wpdb->prepare( "a.secondary_item_id = %s", trim( $sid ) );

				if ( $counter != count( $sid_filter ) )
					$sid_sql .= ' || ';

				$counter++;
			}

			$sid_sql .= ' )';
			$filter_sql[] = $sid_sql;
		}

		if ( empty($filter_sql) )
			return false;

		return join( ' OR ', $filter_sql );
	}
        
    public function get_action_sql( $filter_array ){
      global $wpdb,$bp;
                
      if ( !empty( $filter_array['action'] ) ) {
			$action_filter = explode( ',', $filter_array['action'] );
			$action_sql = ' ( ';

			$counter = 1;
			foreach( (array) $action_filter as $action ) {
				$action_sql .= $wpdb->prepare( "a.type = %s", trim( $action ) );

				if ( $counter != count( $action_filter ) )
					$action_sql .= ' || ';

				$counter++;
			}

			$action_sql .= ' )';
			$filter_sql[] = $action_sql;
		}else {
                  
            $action_sql="( a.type != 'activity_comment' AND a.type!='last_activity')";
        }       
             
        if( $action_sql )
            return $action_sql;
  }      
        
    //return sql for fetching a component specific , pass an array of object=>component_name, primary_id=array of component ids    
  //it may be  
  
  
    public function get_object_sql($filter_array){
      global $wpdb,$bp;
                
       $filter_sql=array();
       if ( !empty( $filter_array['object'] )&&!empty( $filter_array['primary_id'] ) ) {
			$object_filter = explode( ',', $filter_array['object'] );
			$object_sql = ' ( ';

			$counter = 1;
			foreach( (array) $object_filter as $object ) {
				$object_sql .= $wpdb->prepare( "a.component = %s", trim( $object ) );

				if ( $counter != count( $object_filter ) )
					$object_sql .= ' || ';

				$counter++;
			}

			$object_sql .= ' )';
			$filter_sql[] = $object_sql;
		}
         //when ever object is passed, some primary ids muct be passed
         
       if ( !empty( $filter_array['primary_id'] ) ) {
			$pid_filter = explode( ',', $filter_array['primary_id'] );
			$pid_sql = ' ( ';

			$counter = 1;
			foreach( (array) $pid_filter as $pid ) {
				$pid_sql .= $wpdb->prepare( "a.item_id = %s", trim( $pid ) );

				if ( $counter != count( $pid_filter ) )
					$pid_sql .= ' || ';

				$counter++;
			}

			$pid_sql .= ' )';
			$filter_sql[] = $pid_sql;
		}
          if(!empty($filter_sql))      
          return "( ". join(" AND ", $filter_sql)." )";//get object sql     
                
   }     
}
//instantiate
BPDevMyActivityStream::get_instance();
