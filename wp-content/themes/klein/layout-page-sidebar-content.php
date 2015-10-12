<?php
/**
 * Page Sidebar Content Template
 *
 * @package klein
 */
get_header(klein_header()); ?>

	<?php get_template_part( 'content','header'); ?>
	<div class="row">
		<div id="primary" class="content-area col-sm-9 col-md-push-3 col-sm-push-3">
			<div id="content" class="site-content" role="main">
	
				<?php while ( have_posts() ) : the_post(); ?>
	
					<?php get_template_part( 'content', 'page' ); ?>
	
					<?php
						// If comments are open or we have at least one comment, load up the comment template
						if ( comments_open() || '0' != get_comments_number() )
							comments_template();
					?>
	
				<?php endwhile; // end of the loop. ?>
	
			</div><!-- #content -->
		</div><!-- #primary -->
	
		<div id="secondary" class="widget-area col-md-3 col-sm-4 col-md-pull-9 col-sm-pull-9" role="complementary">
			<?php get_sidebar('left'); ?>
		</div>
	</div>
<?php get_footer(); ?>