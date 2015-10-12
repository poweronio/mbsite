<?php

/**
 * BuddyPress - Users Header
 *
 * @package BuddyPress
 * @subpackage bp-legacy
 */

?>
<?php $cover_photo = klein_get_cover_photo_src(); ?>

<?php do_action( 'bp_before_member_header' ); ?>

<?php $gears_version = klein_get_gears_version(); ?>

<?php if ($gears_version >= 3.3) { ?>

<div class="buddypress-head">

<?php } else { ?>

<div class="buddypress-head deprecated" <?php do_action('bcp_coverphoto_style'); ?>>

<?php } ?> 

	<?php // only allow this functionality for gears version 3.3 and above ?>
	<?php if ($gears_version >= 3.3) { ?>
		<?php if (!empty($cover_photo)) { ?>
			<div class="cover-photo-parallax-container hidden-xs">
				<img id="cover-photo" src="<?php echo $cover_photo; ?>" alt="<?php echo __('Cover Photo', 'klein'); ?>" />
			</div>
		<?php } ?>
	<?php } ?>
	
	<div class="container">
		<div class="content row">
			<div class="col-sm-7">
				<div class="row">
					<div class="col-sm-4">
						<div id="item-header-avatar">
							<a href="<?php bp_displayed_user_link(); ?>">
								<?php bp_displayed_user_avatar( 'type=full' ); ?>
							</a>
						</div><!-- #item-header-avatar -->
					</div>
					<div class="col-sm-8">
						<div id="item-header-content">
						<h1 class="fg-white"><?php echo bp_displayed_user_fullname() ;?></h1>
						<div class="fg-cloud">
						<?php if ( bp_is_active( 'activity' ) && bp_activity_do_mentions() ) : ?>
							<span class="user-nicename">
								<em>
									@<?php bp_displayed_user_mentionname(); ?>
								</em>
							</span>
						<?php endif; ?>
							<em>
								<i class="fa fa-clock-o"></i> <?php bp_last_activity( bp_displayed_user_id() ); ?>
							</em>
						</div>
						<?php do_action( 'bp_before_member_header_meta' ); ?>
						<div id="item-meta">
							<?php if ( bp_is_active( 'activity' ) ) : ?>
								<div class="fg-cloud" id="latest-update">
									<?php bp_activity_latest_update( bp_displayed_user_id() ); ?>
								</div>
							<?php endif; ?>
						
							<?php
							/***
							 * If you'd like to show specific profile fields here use:
							 * bp_member_profile_data( 'field=About Me' ); -- Pass the name of the field
							 */
							 do_action( 'bp_profile_header_meta' );

							 ?>
						</div><!-- #item-meta -->

					</div><!-- #item-header-content -->
					</div>
				</div>
			
			</div>
			<div class="col-sm-5">
				<div id="item-buttons">
					<?php do_action( 'bp_member_header_actions' ); ?>
				</div><!-- #item-buttons -->
			</div>
		</div>
	</div><!--.end container-->
	<div class="clearfix"></div>
</div>

<?php do_action( 'bp_after_member_header' ); ?>