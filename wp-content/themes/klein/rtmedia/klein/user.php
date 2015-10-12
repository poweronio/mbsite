<?php
/**
 * RT Media User Template
 */

?>
<div class="clear"></div>

<div id="bp-content-container" class="bp-user-profile">
    <div class="row">
        <div class="col-xs-12">
            <?php do_action ( 'bp_before_member_body' ) ; ?>
            <?php do_action ( 'bp_before_member_media' ) ; ?>
            <div class="item-list-tabs no-ajax" id="subnav">
                <ul>
                    <?php rtmedia_sub_nav () ; ?>
                    <?php do_action ( 'rtmedia_sub_nav' ) ; ?>
                </ul>
            </div><!-- .item-list-tabs -->
            <div id="item-body" class="bp-widget">
                <?php
                    // non ajax render
                     rtmedia_load_template();
                ?>
            </div><!--.bp-widget-->
        </div><!--.col-md-8 col-sm-8 col-xs-8-->
        <!--sidebar-->
        <!--//sidebar-->
    </div>
</div><!--#bp-content-container-->
