<?php
/**
 * The main template file.
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package klein
 */

get_header(klein_header()); ?>

<?php if( klein_is_blog() ){ ?>
<div class="content-heading ">
	<div class="row">
		<div class="col-md-12" id="content-header">
			<div class="row">
				<div class="col-md-12">
					<?php $bread_mg_class = ''; ?>
					<?php if(!function_exists('bcn_display')) { ?>
						<?php $bread_mg_class = 'no-mg-bottom'; ?>
					<?php } ?>
					<h1 class="entry-title <?php echo $bread_mg_class;?>" id="bp-klein-page-title">
						<?php _e('Blog', 'klein'); ?>
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
<?php } ?>
	<div class="row">
		<div id="primary" class="content-area content-area col-md-9 col-sm-9">
			<div id="content" class="site-content" role="main">
	
			<?php if ( have_posts() ) : ?>
	
				<?php /* Start the Loop */ ?>
				<?php while ( have_posts() ) : the_post(); ?>
				
					<?php
						/* Include the Post-Format-specific template for the content.
						* If you want to overload this in a child theme then include a file
						* called content-___.php (where ___ is the Post Format name) and that will be used instead.
						*/
					?>
					<div class="clearfix klein-blog-article-list">
						<?php get_template_part( 'content', get_post_format() ); ?>
					</div>
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
	</div>
<?php get_footer(); ?>