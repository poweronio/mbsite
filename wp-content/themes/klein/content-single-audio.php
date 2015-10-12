<?php
/**
 * Content Single Audio
 *
 * @package klein.
 */ 
?>
<div class="content-area">
	<div id="content" <?php echo post_class(); ?> role="main">
		<?php while ( have_posts() ) : the_post(); ?>
		<div class="clearfix">
			<div class="blog-content">
				<div class="entry-title sr-only">
					<h3>
						<a title="<?php echo esc_attr(the_title()); ?>" href="<?php echo esc_url(the_permalink()); ?>">
							<?php the_title(); ?>
						</a>
					</h3>
				</div>
				
				<?php if( has_post_thumbnail() ){ ?>
					<div class="row">
						<div class="col-sm-2 no-pd-right">
							<div class="entry-content-thumbnail">
								<?php the_post_thumbnail( 'thumbnail', array( 'class' => 'scsale-with-grid' ) ); ?>
							</div>
						</div>
						<div class="col-sm-10">
							<?php klein_the_audio(); ?>
						</div>
					</div>
				<?php } else { ?>
					<?php klein_the_audio(); ?>
				<?php } ?>
				
				<div class="entry-content">
					<?php the_content(); ?>
				</div>

				<?php the_content_sharer(); ?>
				
				<div class="blog-content-meta">
					<?php klein_entry_meta(); ?>
				</div>

				<div class="blog-author entry-content-author">
					<?php klein_author(); ?>
				</div>

				<div class="single-navigation">
					<?php klein_content_nav( 'nav-below' ); ?>
				</div>
			</div>

			<div class="clear"></div>

		</div>

		<div class="clearfix">
			<?php 
			// If comments are open or we have at least one comment, load up the comment template
			if ( comments_open() || '0' != get_comments_number() )
				comments_template();
			?>
		</div>
	<?php endwhile; // end of the loop. ?>
</div><!-- #content -->

</div><!-- #primary -->