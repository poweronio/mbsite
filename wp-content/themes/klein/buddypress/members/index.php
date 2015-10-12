<?php do_action( 'bp_before_directory_members_page' ); ?>

<div id="buddypress">

	<?php do_action( 'bp_before_directory_members' ); ?>

	<?php do_action( 'bp_before_directory_members_content' ); ?>

	<div id="members-dir-search" class="dir-search width-100" role="search">
		<?php bp_directory_members_search_form(); ?>
	</div><!-- #members-dir-search -->

	<?php do_action( 'bp_before_directory_members_tabs' ); ?>

	<form action="" method="post" id="members-directory-form" class="dir-form">
		
		<div class="row">
			<div class="col-sm-6">
				<div class="item-list-tabs" role="navigation">
					<ul>
						<li class="selected" id="members-all"><a href="<?php bp_members_directory_permalink(); ?>"><?php printf( __( 'All Members <span>%s</span>', 'klein' ), bp_get_total_member_count() ); ?></a></li>
						<?php if ( is_user_logged_in() && bp_is_active( 'friends' ) && bp_get_total_friend_count( bp_loggedin_user_id() ) ) : ?>
							<li id="members-personal"><a href="<?php echo bp_loggedin_user_domain() . bp_get_friends_slug() . '/my-friends/'; ?>"><?php printf( __( 'My Friends <span>%s</span>', 'klein' ), bp_get_total_friend_count( bp_loggedin_user_id() ) ); ?></a></li>
						<?php endif; ?>
						<?php do_action( 'bp_members_directory_member_types' ); ?>
					</ul>
				</div><!-- .item-list-tabs -->
			</div>
			<div class="col-sm-6">
				<div class="item-list-tabs" id="subnav" role="navigation">
					<ul>
						<?php do_action( 'bp_members_directory_member_sub_types' ); ?>
						<li id="members-order-select" class="last filter">
							<div class="mg-bottom-10 clearfix"></div>
							<select id="members-order-by" class="pull-right">
								<option value="active"><?php _e( 'Last Active', 'klein' ); ?></option>
								<option value="newest"><?php _e( 'Newest Registered', 'klein' ); ?></option>

								<?php if ( bp_is_active( 'xprofile' ) ) : ?>
									<option value="alphabetical"><?php _e( 'Alphabetical', 'klein' ); ?></option>
								<?php endif; ?>

								<?php do_action( 'bp_members_directory_order_options' ); ?>
							</select>

							<i class="fg-asbestos fa fa-reorder pull-right mg-right-10"></i>

						</li>
					</ul>
				</div>
			</div>
		</div>
		
		<div id="members-dir-list" class="members dir-list">
			<?php bp_get_template_part( 'members/members-loop' ); ?>
		</div><!-- #members-dir-list -->

		<?php do_action( 'bp_directory_members_content' ); ?>

		<?php wp_nonce_field( 'directory_members', '_wpnonce-member-filter' ); ?>

		<?php do_action( 'bp_after_directory_members_content' ); ?>

	</form><!-- #members-directory-form -->

	<?php do_action( 'bp_after_directory_members' ); ?>

</div><!-- #buddypress -->

<?php do_action( 'bp_after_directory_members_page' ); ?>