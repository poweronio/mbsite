<?php
/**
 * BuddyPress Content Sidebar
 *
 * @package klein
 */ 
?>

<?php if (bp_is_user()) { ?>	
	<?php klein_bp_member_head(); ?>
<?php } ?>
<?php if (bp_is_group_single()) { ?>
	<?php klein_bp_group_head(); ?>
<?php } ?>

<div class="container">
	<div class="row buddypress-wrap">
		<div id="primary" class="bp-content-area col-md-9 col-sm-9">
			<div id="content" class="site-content" role="main">
				<?php while ( have_posts() ) : the_post(); ?>
					<?php the_content(); ?>
				<?php endwhile; // end of the loop. ?>
			</div><!-- #content -->
		</div><!-- #primary --> 
		<div id="secondary" class="col-md-3 col-sm-3">
			<?php get_sidebar( 'buddypress' ); ?>
		</div>  
	</div>
</div>