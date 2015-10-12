<?php
/* * **************************************
 * Main.php
 *
 * The main template file, that loads the header, footer and sidebar
 * apart from loading the appropriate rtMedia template
 * *************************************** */
// by default it is not an ajax request
global $rt_ajax_request ;
$rt_ajax_request = false ;

// check if it is an ajax request
if ( ! empty ( $_SERVER[ 'HTTP_X_REQUESTED_WITH' ] ) &&
    strtolower( $_SERVER[ 'HTTP_X_REQUESTED_WITH' ] ) == 'xmlhttprequest'
) {
    $rt_ajax_request = true ;
}
?>
<div id="buddypress">

<?php
//if it's not an ajax request, load headers
if ( ! $rt_ajax_request ) {
    // if this is a BuddyPress page, set template type to
    // buddypress to load appropriate headers
    if ( class_exists( 'BuddyPress' ) && ! bp_is_blog_page () && apply_filters( 'rtm_main_template_buddypress_enable', true ) ) {
        $template_type = 'buddypress' ;
    }
    else {
        $template_type = '' ;
    }
    //get_header( $template_type );

    if ( $template_type == 'buddypress' ) {

        //load buddypress markup
        if ( bp_displayed_user_id () ) {
            get_template_part('rtmedia/klein/user');
        }else if ( bp_is_group () ) {
            // load buddypress rt media profile here
            get_template_part('rtmedia/klein/group');
        }else { // if normal BuddyPress template
            // nothing ...
        }
    } // end $template_type == 'buddypress'

} // if !ajax

if ($rt_ajax_request){
    // include the right rtMedia template
    rtmedia_load_template () ;
}

if ( ! $rt_ajax_request ) {
    if ( function_exists( "bp_displayed_user_id" ) && $template_type == 'buddypress' && (bp_displayed_user_id () || bp_is_group ()) ) {

        if ( bp_is_group () ) {
            do_action ( 'bp_after_group_media' ) ;
            do_action ( 'bp_after_group_body' ) ;
        }
        if ( bp_displayed_user_id () ) {
            do_action ( 'bp_after_member_media' ) ;
            do_action ( 'bp_after_member_body' ) ;
        }
    }
} // EXIF !$rt_ajax_request
?>

</div><!--#buddypress-->
        