<?php
/**
 * The template for displaying full content pages
 *
 * @package klein
 */

get_header(klein_header()); ?>

	<?php get_template_part( 'content','header'); ?>
	<div class="row">
		<div id="primary" class="content-area col-xs-10 col-xs-push-1 full-content">
			<div id="content" class="site-content eow" role="main">
	
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
	</div>

<?php get_footer(); ?>
