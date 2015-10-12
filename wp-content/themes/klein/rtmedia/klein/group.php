<?php
/**
 * Buddypress Single Group Template for RT Media
 */
?>
<div id="buddypress">

    <?php if ( bp_has_groups() ) : while ( bp_groups() ) : bp_the_group(); ?>
 
    <?php do_action( 'bp_before_group_home_content' ); ?>

        <div id="bp-content-container">
            <div class="row">
                
                <div class="col-xs-12">
                    <div id="bp-groups-main-content">
                        <?php do_action ( 'bp_before_member_body' ) ; ?>
                        <?php do_action ( 'bp_before_member_media' ) ; ?>
                        
                        <div class="item-list-tabs no-ajax" id="subnav">
                            <ul>
                                <?php rtmedia_sub_nav () ; ?>
                                <?php do_action ( 'rtmedia_sub_nav' ) ; ?>
                            </ul>
                        </div><!-- .item-list-tabs -->
                        
                        <div id="item-body" class="bp-widget">
                            <?php rtmedia_load_template(); ?>
                        </div><!--.bp-widget-->
                    </div>
                </div> 
                
                <?php endwhile; endif; // terminate the groups loop before the sidebar to avoid bugs?>
                
                
            </div>
        </div>
</div>