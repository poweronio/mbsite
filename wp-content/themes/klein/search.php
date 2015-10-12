<?php
/**
 * The template for displaying Search Results pages.
 *
 * @package klein
 */

get_header(klein_header()); ?>

<div class="content-heading ">
	<div class="row">
		<div class="col-md-12" id="content-header">
			<div class="row">
				<div class="col-md-12">
					<h1 class="entry-title" id="bp-klein-page-title">
						<?php _e('Search Results','klein'); ?>
					</h1>
					<!--breadcrumbs-->
					<?php if(function_exists('bcn_display')){ ?>
						<div class="klein-breadcrumbs">
							<?php bcn_display(); ?>
						</div>
					<?php } ?>
				</div>
				
			</div>
		</div>
	</div>
</div>
<div class="alert alert-info">
	<?php printf( __( 'Displaying search results for "%s"', 'klein' ), '<span>' . get_search_query() . '</span>' ); ?>
</div>	
	<div class="row clearfix">
		<div id="primary" class="content-area col-md-9 col-sm-9">
			<div id="content" class="site-content" role="main">
	
			<?php if ( have_posts() ) : ?>
	
				<?php /* Start the Loop */ ?>
				<?php while ( have_posts() ) : the_post(); ?>
	
					<?php
						/* Include the Post-Format-specific template for the content.
						* If you want to overload this in a child theme then include a file
						* called content-___.php (where ___ is the Post Format name) and that will be used instead.
						*/
						get_template_part( 'content', get_post_format() );
					?>
					
					
				<?php endwhile; ?>
	
				<?php klein_content_nav( 'nav-below' ); ?>
	
			<?php else : ?>
	
				<?php get_template_part( 'no-results', 'index' ); ?>
	
			<?php endif; ?>
	
			</div><!-- #content -->
		</div><!-- #primary -->
		
		<div class="col-md-3 col-sm-3">
			<?php get_sidebar(); ?>
		</div>
	</div><!--.row.clearfix-->
<?php get_footer(); ?>