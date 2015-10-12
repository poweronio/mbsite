<?php do_action( 'bp_before_directory_groups_page' ); ?>
<?php global $bp; ?>

<div id="buddypress">

	<?php do_action( 'bp_before_directory_groups' ); ?>

	<?php do_action( 'bp_before_directory_groups_content' ); ?>

	<div id="group-dir-search" class="dir-search" role="search">
		<?php bp_directory_groups_search_form(); ?>
	</div><!-- #group-dir-search -->

	<form action="" method="post" id="groups-directory-form" class="dir-form">

		<?php do_action( 'template_notices' ); ?>

		<div class="row">
			<div class="col-sm-7">
				<div class="item-list-tabs" role="navigation">
					<ul>
						<li class="selected" id="groups-all"><a href="<?php bp_groups_directory_permalink(); ?>"><?php printf( __( 'All Groups <span>%s</span>', 'klein' ), bp_get_total_group_count() ); ?></a></li>

						<?php if ( is_user_logged_in() && bp_get_total_group_count_for_user( bp_loggedin_user_id() ) ) : ?>
							<li id="groups-personal"><a href="<?php echo bp_loggedin_user_domain() . bp_get_groups_slug() . '/my-groups/'; ?>"><?php printf( __( 'My Groups <span>%s</span>', 'klein' ), bp_get_total_group_count_for_user( bp_loggedin_user_id() ) ); ?></a></li>
						<?php endif; ?>

						<?php do_action( 'bp_groups_directory_group_filter' ); ?>

					</ul>
				</div><!-- .item-list-tabs -->
			</div>
			<div class="col-sm-5">
				<div class="item-list-tabs" id="subnav" role="navigation">
					<ul>
						<?php do_action( 'bp_groups_directory_group_types' ); ?>

						<li id="groups-order-select" class="last filter">

							<div class="spacer mg-bottom-10"></div>
							
							<select id="groups-order-by" class="pull-right">
								<option value="active"><?php _e( 'Last Active', 'klein' ); ?></option>
								<option value="popular"><?php _e( 'Most Members', 'klein' ); ?></option>
								<option value="newest"><?php _e( 'Newly Created', 'klein' ); ?></option>
								<option value="alphabetical"><?php _e( 'Alphabetical', 'klein' ); ?></option>

								<?php do_action( 'bp_groups_directory_order_options' ); ?>
							</select>

							<i class="fa fg-asbestos fa-reorder pull-right"></i>
						</li>
					</ul>
				</div>
			</div>
		</div><!--.row-->
		
		<div class="mg-bottom-35">	

			<?php 
				  // get global buddypress slug config
				  $groups_slug = $bp->groups->slug; ?>
			
			<?php $bp_groups_page_slug = ''; ?>

			<?php 
				  // incase the user selected a different page for groups
				  // get the page that is assigned to groups and catch the slug
				  $bp_pages = get_option('bp-pages'); ?>

			<?php if (!empty($bp_pages)) { ?>
				<?php $bp_groups_page = get_post($bp_pages['groups']); ?>
				<?php $bp_groups_page_slug = $bp_groups_page->post_name; ?>
			<?php } ?>
			<?php if (!empty($bp_groups_page_slug)) { ?>
				<?php $groups_slug = $bp_groups_page_slug; ?>
			<?php } ?>

			<?php $groups_create_url = sprintf('%s/%s/create', get_home_url(), $groups_slug); ?>
				<?php if (!is_user_logged_in()) { ?>
					<?php $groups_create_url = wp_login_url($groups_create_url); ?>
				<?php } ?>

			<a href="<?php echo esc_url($groups_create_url); ?>" class="btn btn-danger pull-right">
				<span class="fa fa-users"></span> 
				<?php _e('Create Group', 'klein'); ?>
			</a>
			
			<div class="clearfix"></div>
		</div>
		
		
		<div class="clearfix"></div>

		<div id="groups-dir-list" class="groups dir-list">
			<?php bp_get_template_part( 'groups/groups-loop' ); ?>
		</div><!-- #groups-dir-list -->

		<?php do_action( 'bp_directory_groups_content' ); ?>

		<?php wp_nonce_field( 'directory_groups', '_wpnonce-groups-filter' ); ?>

		<?php do_action( 'bp_after_directory_groups_content' ); ?>

	</form><!-- #groups-directory-form -->

	<?php do_action( 'bp_after_directory_groups' ); ?>

</div><!-- #buddypress -->

<?php do_action( 'bp_after_directory_groups_page' ); ?>