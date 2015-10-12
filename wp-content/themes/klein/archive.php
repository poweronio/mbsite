<?php
/**
 * The template for displaying Archive pages.
 *
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package klein 
 */
get_header(klein_header()); ?>
<div class="content-heading ">
	<div class="row">
		<div class="col-md-12" id="content-header">
			<div class="row">
				<div class="col-md-12">
					
					<?php $bread_mg_class = ''; ?>
					<?php if(!function_exists('bcn_display')) { ?>
						<?php $bread_mg_class = 'no-mg-bottom'; ?>
					<?php } ?>
					<?php
						the_archive_title( '<h1 class="entry-title '.$bread_mg_class.'">', '</h1>' );
					?>
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

<?php the_archive_description( '<div class="taxonomy-description">', '</div>' ); ?>

	<div class="row">
		<section id="primary" class="content-area col-md-9 col-sm-9">
			<div id="content" class="site-content" role="main">
	
			<?php if ( have_posts() ) : ?>
	
	
				<?php /* Start the Loop */ ?>
				<?php while ( have_posts() ) : the_post(); ?>
					<div class="clearfix klein-blog-article-list">
					<?php
						/* Include the Post-Format-specific template for the content.
						* If you want to overload this in a child theme then include a file
						* called content-___.php (where ___ is the Post Format name) and that will be used instead.
						*/
						get_template_part( 'content', get_post_format() );
					?>
					</div>
				<?php endwhile; ?>
	
				<?php klein_content_nav( 'nav-below' ); ?>
	
			<?php else : ?>
	
				<?php get_template_part( 'no-results', 'archive' ); ?>
	
			<?php endif; ?>
	
			</div><!-- #content -->
		</section><!-- #primary -->
	
		<div class="col-md-3 col-sm-3">
			<?php get_sidebar(); ?>
		</div>
		
	</div><!--.row-->	
<?php get_footer(); ?>