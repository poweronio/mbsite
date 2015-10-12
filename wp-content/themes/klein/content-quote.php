<?php
/**
 * Content Quote
 *
 * @package Klein
 */ 
?>
<article id="post-<?php the_ID(); ?>" <?php post_class( array( 'blog-list', 'no-border-bottom' ) ); ?>>
	
	<?php if (has_post_thumbnail()){ ?>
		<h3 class="sr-only">
			<a class="primary" title="<?php echo esc_attr(the_title()); ?>" href="<?php echo esc_url(the_permalink()); ?>">
				<?php the_title(); ?>
			</a>
		</h3>
		<div class="row archive-excerpt">
			<div class="col-sm-8">
				<?php the_excerpt(); ?>
			</div>
			<div class="col-sm-4">
				<?php echo the_post_thumbnail('klein-thumbnail'); ?>
			</div>
		</div>
	<?php } else { ?>
		<h3 class="sr-only">
			<a class="primary" title="<?php echo esc_attr(the_title()); ?>" href="<?php echo esc_url(the_permalink()); ?>">
				<?php the_title(); ?>
			</a>
		</h3>
		<div class="post-format-quote-icon">
			<i class="fa fa-quote-left"></i>
		</div>
		<div class="archive-quote-content">
			<?php
				$source_name = get_post_meta(get_the_ID(), '_format_quote_source_name', true);
				$source_url = get_post_meta(get_the_ID(), '_format_quote_source_url', true);;
				$source_title = get_post_meta(get_the_ID(), '_format_quote_source_title', true);;
				$source_date = get_post_meta(get_the_ID(), '_format_quote_source_date', true);;
			?>

			<blockquote class="no-mg-top">
				<?php the_content(); ?>

				<cite>

					<?php echo esc_attr($source_name); ?>
					
						<?php if (!empty($source_title)) { ?>
						, 
							<?php if (!empty($source_url)) { ?>
								<a href="<?php echo esc_url($source_url); ?>" title="<?php echo esc_attr($source_title); ?>">
							<?php } ?>
									<?php echo esc_attr($source_title); ?>
							<?php if (!empty($source_url)) { ?>
								</a>
							<?php } ?>	
						<?php } ?>
						
						<?php if (!empty($source_title)) { ?>
							, <?php echo esc_attr($source_date);?>
						<?php } ?>

						<a class="quote-permalink" href="<?php echo esc_url(the_permalink()); ?>" title="<?php echo _e('Permalink', 'klein'); ?>">
							<i class="fa fa-link"></i>
						</a>
				</cite>
			</blockquote>

		</div>
	<?php } ?>
	<div class="entry-meta sr-only">
		<?php klein_entry_meta(); ?>
	</div>
	<div class="clearfix"></div>
	<!--author end-->
</article><!-- #post-## -->